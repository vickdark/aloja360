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

        if ($request->ajax() || $request->wantsJson()) {
            $query = Payment::with(['reservation', 'guest']);
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                      ->orWhereHas('reservation', function($rq) use ($search) {
                          $rq->where('code', 'like', "%{$search}%");
                      })
                      ->orWhereHas('guest', function($gq) use ($search) {
                          $gq->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            }
            $total = $query->count();
            $payments = $query->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
            return response()->json(['data' => $payments, 'total' => (int)$total]);
        }

        return view('payments.index');
    }

    public function create(Request $request)
    {
        $this->authorize('create', Payment::class);
        $reservations = Reservation::with('primaryGuest')->whereNotIn('status', ['cancelled'])->orderBy('id', 'desc')->get();
        $guests = Guest::orderBy('first_name')->get();
        
        $selectedReservation = null;
        if ($request->filled('reservation_id')) {
            $selectedReservation = Reservation::with('primaryGuest')->find($request->get('reservation_id'));
        }

        return view('payments.create', compact('reservations', 'guests', 'selectedReservation'));
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
        return view('payments.show', compact('payment'));
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
