<?php

namespace App\Http\Controllers;

use App\Actions\ConfirmReservationAction;
use App\Enums\ReservationStatus;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with(['reservation.confirmedPayments', 'reservation.primaryGuest', 'guest']);

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('reservation', function($rq) use ($search) {
                      $rq->where('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('guest', function($gq) use ($search) {
                      $gq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('document_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $limit = $request->get('limit', 15);
            $offset = $request->get('offset', 0);
            $total = $query->count();
            $payments = $query->orderBy('reservation_id', 'desc')->orderBy('payment_date', 'asc')->offset($offset)->limit($limit)->get();
            
            $payments->each(function($p) {
                if ($p->reservation) {
                    $p->reservation->append('outstanding_balance');
                }
            });

            return response()->json(['data' => $payments, 'total' => (int)$total]);
        }

        $payments = $query->orderBy('reservation_id', 'desc')->orderBy('payment_date', 'asc')->paginate(20)->withQueryString();

        // Agrupar pagos por reserva para renderizado con divisores claros
        $groupedPayments = $payments->getCollection()->groupBy(function($item) {
            return $item->reservation_id ? 'res_' . $item->reservation_id : 'sin_reserva';
        });

        // Contadores KPI
        $stats = [
            'total_amount'     => Payment::where('status', 'confirmed')->whereIn('type', ['payment', 'deposit'])->sum('amount'),
            'confirmed_count'  => Payment::where('status', 'confirmed')->count(),
            'pending_count'    => Payment::where('status', 'pending')->count(),
            'deposits_count'   => Payment::where('type', 'deposit')->count(),
        ];

        return view('payments.index', compact('payments', 'groupedPayments', 'stats'));
    }



    public function create(Request $request)
    {
        $this->authorize('create', Payment::class);
        $reservations = Reservation::with('primaryGuest')->whereNotIn('status', ['cancelled'])->orderBy('id', 'desc')->get();
        $guests = Guest::orderBy('first_name')->get();
        
        $selectedReservation = null;
        $reservationPayments = collect();
        $suggestedType       = $request->get('payment_type', 'payment'); // deposit | payment
        $suggestedAmount     = null;

        if ($request->filled('reservation_id')) {
            $selectedReservation = Reservation::with(['primaryGuest', 'payments.guest'])->find($request->get('reservation_id'));

            if ($selectedReservation) {
                // Pagos ya confirmados de esta reserva
                $reservationPayments = $selectedReservation->payments()
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->orderBy('payment_date')
                    ->get();

                $confirmedPaymentsTotal = $selectedReservation->confirmedPayments()
                    ->whereIn('type', ['payment', 'deposit'])
                    ->sum('amount');

                $hasDeposit = $selectedReservation->confirmedPayments()
                    ->where('type', 'deposit')
                    ->exists();

                // Sugerir tipo según si ya hay depósito o no
                if (!$request->filled('payment_type')) {
                    $suggestedType = $hasDeposit ? 'payment' : 'deposit';
                }

                // Sugerir monto: si no hay depósito, sugerir deposit_required o 50% del total
                if ($suggestedType === 'deposit') {
                    $suggestedAmount = $selectedReservation->deposit_required
                        ?? round($selectedReservation->total_amount * 0.5, 2);
                } else {
                    // Saldo pendiente (outstanding_balance)
                    $suggestedAmount = max(0, $selectedReservation->total_amount - $confirmedPaymentsTotal);
                }
            }
        }

        return view('payments.create', compact(
            'reservations', 'guests', 'selectedReservation',
            'reservationPayments', 'suggestedType', 'suggestedAmount'
        ));
    }

    public function store(StorePaymentRequest $request, ConfirmReservationAction $confirmAction)
    {
        $this->authorize('create', Payment::class);
        
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['code'] = 'PAY-' . strtoupper(uniqid());
        
        if ($data['status'] === 'confirmed') {
            $data['confirmed_at'] = now();
            $data['confirmed_by'] = auth()->id();
        }

        $payment = Payment::create($data);

        // Si el pago se confirma y la reserva está pendiente, confirmar la reserva automáticamente
        if ($payment->reservation_id && $data['status'] === 'confirmed') {
            $reservation = Reservation::find($payment->reservation_id);
            if ($reservation && $reservation->status === ReservationStatus::Pending) {
                try {
                    $confirmAction->execute(
                        $reservation,
                        auth()->id(),
                        'Confirmación automática tras registro de pago ' . $payment->code
                    );
                } catch (\Exception $e) {
                    // Si ya estaba confirmada u otro detalle, continuar
                }
            }
        }

        if ($request->filled('reservation_id')) {
            return redirect()
                ->route('reservations.show', $payment->reservation_id)
                ->with('success', '¡Pago registrado exitosamente! La reserva y su saldo han sido actualizados.');
        }

        return redirect()->route('payments.index')->with('success', 'Pago registrado correctamente.');
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        $payment->load(['reservation.payments.guest', 'guest', 'createdBy', 'confirmedBy']);

        $relatedPayments = collect();
        if ($payment->reservation) {
            $relatedPayments = $payment->reservation->payments()
                ->where('id', '!=', $payment->id)
                ->orderBy('payment_date', 'asc')
                ->get();
        }

        return view('payments.show', compact('payment', 'relatedPayments'));
    }


    public function edit(Payment $payment)
    {
        $this->authorize('update', $payment);
        return view('payments.edit', compact('payment'));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $this->authorize('update', $payment);
        
        $data = $request->validated();
        
        if ($data['status'] === 'confirmed' && $payment->status->value !== 'confirmed') {
            $data['confirmed_at'] = now();
            $data['confirmed_by'] = auth()->id();
        } elseif ($data['status'] === 'rejected' && $payment->status->value !== 'rejected') {
            $data['rejected_at'] = now();
        }

        $payment->update($data);
        return redirect()->route('payments.index')->with('success', 'Pago actualizado correctamente.');
    }

    public function destroy(Payment $payment)
    {
        $this->authorize('delete', $payment);
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Pago eliminado correctamente.');
    }
}
