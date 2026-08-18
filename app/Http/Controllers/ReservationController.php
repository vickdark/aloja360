<?php

namespace App\Http\Controllers;

use App\Actions\CancelReservationAction;
use App\Actions\CheckInReservationAction;
use App\Actions\CheckOutReservationAction;
use App\Actions\ConfirmReservationAction;
use App\Actions\CreateReservationAction;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Accommodation;
use App\Models\Guest;
use App\Models\Reservation;
use App\Services\PricingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Reservation::class);

        $query = Reservation::with(['accommodation', 'primaryGuest']);

        // Filtro de Búsqueda (Implementación requerida por la nueva UI)
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhereHas('primaryGuest', function($gq) use ($search) {
                      $gq->where('first_name', 'LIKE', "%{$search}%")
                         ->orWhere('last_name', 'LIKE', "%{$search}%")
                         ->orWhere('phone', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('accommodation', function($aq) use ($search) {
                      $aq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filtro por Estado (KPI Cards)
        if ($status = $request->get('status')) {
            // Mapeo seguro por si el valor no pertenece al Enum
            try {
                $statusEnum = \App\Enums\ReservationStatus::from($status);
                $query->where('status', $statusEnum);
            } catch (\ValueError $e) {
                // Estado inválido, ignorar filtro
            }
        }

        // Conteos para KPI (Cálculo directo en BD para performance)
        $pending_count = Reservation::where('status', \App\Enums\ReservationStatus::Pending)->count();
        $confirmed_count = Reservation::where('status', \App\Enums\ReservationStatus::Confirmed)->count();
        $checked_in_count = Reservation::where('status', \App\Enums\ReservationStatus::CheckedIn)->count();
        $checked_out_count = Reservation::where('status', \App\Enums\ReservationStatus::CheckedOut)->count();

        $total_count = $query->count();

        if ($request->ajax() || $request->wantsJson()) {
            if ($request->has('limit')) {
                $limit = $request->get('limit', 10);
                $offset = $request->get('offset', 0);
                $items = $query->orderBy('check_in_date', 'desc')->offset($offset)->limit($limit)->get();
                $items->load(['accommodation', 'primaryGuest']);
                return response()->json(['data' => $items, 'total' => (int)$total_count, 'status' => 'success']);
            }
            $reservations = $query->orderBy('check_in_date', 'desc')->paginate(10);
            return response()->json($reservations);
        }

        return view('reservations.index', compact(
            'total_count',
            'pending_count',
            'confirmed_count',
            'checked_in_count',
            'checked_out_count'
        ));
    }

    public function store(StoreReservationRequest $request, CreateReservationAction $action, PricingService $pricing): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        try {
            $data = $request->validated();
            $data['created_by'] = $request->user()->id;

            // Si no se proveen valores financieros manualmente, calcularlos automaticamente
            if (!isset($data['nightly_subtotal']) || !isset($data['total_amount'])) {
                $guests = $data['guests_count'] ?? ($data['adults_count'] ?? 2) + ($data['children_count'] ?? 0);
                $prices = $pricing->calculateStayTotal(
                    $data['accommodation_id'],
                    $data['check_in_date'],
                    $data['check_out_date'],
                    $guests,
                    $data['pricing_type'] ?? null
                );

                $data['pricing_type'] = $prices['pricing_type'];

                $data['nights_count'] = $prices['nights'];
                $data['nightly_subtotal'] = $prices['subtotal'];
                $data['services_total'] = $data['services_total'] ?? 0;
                $data['discount_total'] = $data['discount_total'] ?? 0;
                $data['tax_total'] = $data['tax_total'] ?? 0;
                $data['cleaning_fee'] = $data['cleaning_fee'] ?? Accommodation::find($data['accommodation_id'])?->cleaning_fee ?? 0;
                $data['security_deposit'] = $data['security_deposit'] ?? Accommodation::find($data['accommodation_id'])?->security_deposit ?? 0;
                $data['rate_snapshot'] = $prices['snapshot'];
                
                $data['total_amount'] = $data['nightly_subtotal'] 
                    + $data['services_total'] 
                    - $data['discount_total'] 
                    + $data['tax_total'] 
                    + $data['cleaning_fee'];
            }

            $reservation = $action->execute($data);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Reserva creada exitosamente',
                    'data' => $reservation->load(['accommodation', 'primaryGuest'])
                ], 201);
            }

            return redirect()
                ->route('reservations.show', $reservation)
                ->with('success', '¡Reserva creada exitosamente! Código generado: ' . $reservation->code);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function create()
    {
        $this->authorize('create', Reservation::class);

        $accommodations = Accommodation::orderBy('name')->get();
        $guests = Guest::orderBy('first_name')->get();
        
        // Valores por defecto del formulario
        $defaults = [
            'check_in_date' => Carbon::now()->addDay()->format('Y-m-d'),
            'check_out_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'guests_count' => 2,
            'adults_count' => 2,
            'children_count' => 0,
            'status' => \App\Enums\ReservationStatus::Pending->value,
            'source' => 'manual',
        ];

        return view('reservations.create', compact('accommodations', 'guests', 'defaults'));
    }

    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        $accommodations = Accommodation::orderBy('name')->get();
        $guests = Guest::orderBy('first_name')->get();

        return view('reservations.edit', compact('reservation', 'accommodations', 'guests'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation, PricingService $pricing): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        try {
            $data = $request->validated();

            // Si las fechas o alojamiento cambiaron, recalcular precios
            $datesChanged = $reservation->check_in_date->format('Y-m-d') !== $request->input('check_in_date')
                || $reservation->check_out_date->format('Y-m-d') !== $request->input('check_out_date')
                || $reservation->accommodation_id !== $request->input('accommodation_id')
                || ($request->filled('pricing_type') && ((string) $reservation->pricing_type?->value !== (string) $request->input('pricing_type')));

            if ($datesChanged) {
                $guests = $data['guests_count'] ?? ($data['adults_count'] ?? $reservation->guests_count);
                $prices = $pricing->calculateStayTotal(
                    $data['accommodation_id'],
                    $data['check_in_date'],
                    $data['check_out_date'],
                    $guests,
                    $data['pricing_type'] ?? null
                );

                $data['pricing_type'] = $prices['pricing_type'];

                $data['nights_count'] = $prices['nights'];
                $data['nightly_subtotal'] = $prices['subtotal'];
                $data['rate_snapshot'] = $prices['snapshot'];
                $data['total_amount'] = $data['nightly_subtotal'] 
                    + ($data['services_total'] ?? 0) 
                    - ($data['discount_total'] ?? 0) 
                    + ($data['tax_total'] ?? 0) 
                    + ($data['cleaning_fee'] ?? 0);
            }

            $oldStatus = $reservation->status;
            $newStatus = $data['status'] ?? $reservation->status;
            
            $reservation->update($data);

            // Si cambió el estado manualmente, guardar en histórico
            if ($oldStatus !== $newStatus) {
                \App\Models\ReservationStatusHistory::create([
                    'reservation_id' => $reservation->id,
                    'status' => $newStatus->value,
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatus->value,
                    'changed_by' => auth()->id(),
                    'notes' => 'Cambio de estado manual desde edición de reserva.',
                ]);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Reserva actualizada exitosamente',
                    'data' => $reservation
                ]);
            }

            return redirect()
                ->route('reservations.show', $reservation)
                ->with('success', '¡Reserva actualizada exitosamente!');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Reservation $reservation): \Illuminate\Http\Response|\Illuminate\View\View|JsonResponse
    {
        $this->authorize('view', $reservation);

        $reservation->load([
            'accommodation', 
            'primaryGuest', 
            'services', 
            'payments',
            'createdBy',
            'statusHistories.changedBy'
        ]);

        // Custom attribute calculation
        $reservation->loadMissing('payments');
        $confirmedPayments = $reservation->payments->filter(function($pay) {
            return in_array($pay->status->value, ['confirmed', 'completed']) 
                && in_array($pay->type->value, ['payment', 'deposit']);
        })->sum('amount');
        
        $refunds = $reservation->payments->filter(function($pay) {
            return in_array($pay->status->value, ['confirmed', 'completed'])
                && in_array($pay->type->value, ['refund', 'deposit_return']);
        })->sum('amount');

        $reservation->setAttribute('confirmed_payments_sum', $confirmedPayments);
        $reservation->setAttribute('outstanding_balance', max(0, $reservation->total_amount - $confirmedPayments + $refunds));

        if (request()->wantsJson()) {
            return response()->json($reservation);
        }

        return view('reservations.show', [
            'reservation' => $reservation,
        ]);
    }

    public function confirm(Reservation $reservation, Request $request, ConfirmReservationAction $action): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        $this->authorize('update', $reservation);

        try {
            $notes = $request->input('notes', 'Confirmación manual desde interfaz');
            $action->execute($reservation, $request->user()->id, $notes);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Reserva confirmada exitosamente', 'data' => $reservation]);
            }
            
            return back()->with('success', '¡Reserva Confirmada! La disponibilidad ha sido bloqueada.');
            
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function checkIn(Reservation $reservation, Request $request, CheckInReservationAction $action): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        $this->authorize('update', $reservation);

        try {
            $notes = $request->input('notes', '');
            $action->execute($reservation, $request->user()->id, $notes);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Check-in realizado exitosamente', 'data' => $reservation]);
            }
            
            return back()->with('success', '¡Check-In Realizado! El huésped ha sido registrado.');
            
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function checkOut(Reservation $reservation, Request $request, CheckOutReservationAction $action): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        $this->authorize('update', $reservation);

        try {
            $notes = $request->input('notes', '');
            $action->execute($reservation, $request->user()->id, $notes);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Check-out realizado exitosamente', 'data' => $reservation]);
            }
            
            return back()->with('success', '¡Check-Out Realizado! Se generó la tarea de limpieza automáticamente.');
            
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function cancel(Reservation $reservation, Request $request, CancelReservationAction $action): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        $this->authorize('update', $reservation);

        $request->validate(['reason' => 'required|string|max:255']);

        try {
            $action->execute(
                $reservation, 
                $request->input('reason'), 
                $request->user()->id, 
                $request->input('notes', '')
            );

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Reserva cancelada exitosamente', 'data' => $reservation]);
            }
            
            return back()->with('success', '¡Reserva Cancelada! El alojamiento está disponible nuevamente.');
            
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
