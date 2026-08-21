<?php

namespace App\Http\Controllers;

use App\Actions\CancelReservationAction;
use App\Actions\CheckInReservationAction;
use App\Actions\CheckOutReservationAction;
use App\Actions\ConfirmReservationAction;
use App\Actions\CreateReservationAction;
use App\Actions\DeleteReservationAction;
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

        $reservations = $query->orderBy('check_in_date', 'desc')->paginate(10)->withQueryString();

        return view('reservations.index', compact(
            'reservations',
            'total_count',
            'pending_count',
            'confirmed_count',
            'checked_in_count',
            'checked_out_count'
        ));
    }

    /**
     * Muestra la vista de calendario interactivo de reservas.
     */
    public function calendar(Request $request)
    {
        $this->authorize('viewAny', Reservation::class);
        $accommodations = Accommodation::all();
        return view('reservations.calendar', compact('accommodations'));
    }

    /**
     * Retorna los datos de eventos para el calendario en formato JSON.
     */
    public function calendarData(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Reservation::class);

        $query = Reservation::with(['accommodation', 'primaryGuest']);

        if ($request->filled('accommodation_id')) {
            $query->where('accommodation_id', $request->get('accommodation_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('start') && $request->filled('end')) {
            $start = Carbon::parse($request->get('start'))->startOfDay();
            $end = Carbon::parse($request->get('end'))->endOfDay();
            $query->where(function($q) use ($start, $end) {
                $q->where('check_in_date', '<=', $end)
                  ->where('check_out_date', '>=', $start);
            });
        }

        $reservations = $query->get();

        $statusColors = [
            'pending' => ['bg' => '#f59e0b', 'border' => '#d97706', 'text' => '#ffffff'],
            'confirmed' => ['bg' => '#3b82f6', 'border' => '#2563eb', 'text' => '#ffffff'],
            'checked_in' => ['bg' => '#10b981', 'border' => '#059669', 'text' => '#ffffff'],
            'checked_out' => ['bg' => '#06b6d4', 'border' => '#0891b2', 'text' => '#ffffff'],
            'cancelled' => ['bg' => '#ef4444', 'border' => '#dc2626', 'text' => '#ffffff'],
            'no_show' => ['bg' => '#6b7280', 'border' => '#4b5563', 'text' => '#ffffff'],
        ];

        $events = $reservations->map(function ($r) use ($statusColors) {
            $st = $r->status->value;
            $colors = $statusColors[$st] ?? ['bg' => '#6b7280', 'border' => '#4b5563', 'text' => '#ffffff'];
            $guestName = $r->primaryGuest ? "{$r->primaryGuest->first_name} {$r->primaryGuest->last_name}" : 'Sin Huésped';
            $accName = $r->accommodation ? $r->accommodation->name : 'Alojamiento';

            // Para pasadía (mismo día), end = check_in_date
            // Para reservas con noches, check_out_date es el día de salida
            return [
                'id' => $r->id,
                'title' => "#{$r->code} - {$accName} ({$guestName})",
                'code' => $r->code,
                'guest' => $guestName,
                'guest_phone' => $r->primaryGuest?->phone ?? '',
                'guest_email' => $r->primaryGuest?->email ?? '',
                'accommodation' => $accName,
                'start' => $r->check_in_date->format('Y-m-d'),
                'end' => $r->is_day_pass ? $r->check_in_date->format('Y-m-d') : $r->check_out_date->format('Y-m-d'),
                'check_in_formatted' => $r->check_in_date->format('d/m/Y'),
                'check_out_formatted' => $r->check_out_date->format('d/m/Y'),
                'nights_count' => $r->nights_count,
                'guests_count' => $r->guests_count,
                'adults_count' => $r->adults_count,
                'children_count' => $r->children_count,
                'is_day_pass' => (bool)$r->is_day_pass,
                'status' => $st,
                'status_label' => $r->status->label(),
                'total_amount' => number_format((float)$r->total_amount, 2),
                'outstanding_balance' => number_format((float)$r->outstanding_balance, 2),
                'source' => $r->source,
                'notes' => $r->notes,
                'show_url' => route('reservations.show', $r->id),
                'edit_url' => route('reservations.edit', $r->id),
                'backgroundColor' => $colors['bg'],
                'borderColor' => $colors['border'],
                'textColor' => $colors['text'],
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $events,
        ]);
    }

    /**
     * Genera un estimado de tarifas con el desglose real por noche (temporadas/modificadores),
     * usando el mismo PricingService que salva la reserva. No persiste nada.
     */
    public function estimate(Request $request, PricingService $pricing): JsonResponse
    {
        $this->authorize('create', Reservation::class);

        $data = $request->validate([
            'accommodation_id' => 'required|exists:accommodations,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
            'pricing_type' => 'nullable|string',
            'guests_count' => 'nullable|integer|min:1',
            'adults_count' => 'nullable|integer|min:1',
            'children_count' => 'nullable|integer|min:0',
            'is_day_pass' => 'nullable|boolean',
        ]);

        $accommodation = Accommodation::findOrFail($data['accommodation_id']);
        $isDayPass = $request->boolean('is_day_pass') || ($data['check_in_date'] === $data['check_out_date']);
        $adults = $data['adults_count'] ?? null;
        $children = $data['children_count'] ?? null;
        $guests = $data['guests_count'] ?? (($adults ?? 1) + ($children ?? 0));
        if ($adults === null && $children === null) {
            $adults = $guests;
            $children = 0;
        } elseif ($adults === null) {
            $adults = max($guests - ($children ?? 0), 1);
        } elseif ($children === null) {
            $children = max($guests - $adults, 0);
        }

        $prices = $pricing->calculateStayTotal(
            $accommodation,
            $data['check_in_date'],
            $data['check_out_date'],
            $guests,
            $data['pricing_type'] ?? null,
            $isDayPass,
            $adults,
            $children
        );

        return response()->json([
            'subtotal' => $prices['subtotal'],
            'nights' => $prices['nights'],
            'is_day_pass' => $isDayPass,
            'pricing_type' => $prices['pricing_type'],
            'snapshot' => $prices['snapshot'],
        ]);
    }

    public function store(StoreReservationRequest $request, CreateReservationAction $action, PricingService $pricing): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        try {
            $data = $request->validated();
            $data['created_by'] = $request->user()->id;

            $isDayPass = $request->boolean('is_day_pass') || ($data['check_in_date'] === $data['check_out_date']);
            $data['is_day_pass'] = $isDayPass;

            // Si no se proveen valores financieros manualmente, calcularlos automaticamente
            if (!isset($data['nightly_subtotal']) || !isset($data['total_amount'])) {
                $accommodation = Accommodation::find($data['accommodation_id']);
                $adults = $data['adults_count'] ?? null;
                $children = $data['children_count'] ?? 0;
                $guests = $data['guests_count'] ?? (($adults ?? 2) + ($children ?? 0));
                if (!isset($data['guests_count'])) {
                    $data['guests_count'] = $guests;
                }
                // Normalizar para pricing: si no hay desglose, tratar todo como adultos (compat)
                if ($adults === null) {
                    $adults = max($guests - $children, 1);
                }
                $prices = $pricing->calculateStayTotal(
                    $accommodation,
                    $data['check_in_date'],
                    $data['check_out_date'],
                    $guests,
                    $data['pricing_type'] ?? null,
                    $isDayPass,
                    $adults,
                    $children
                );

                $data['pricing_type'] = $prices['pricing_type'];

                $data['nights_count'] = $prices['nights'];
                $data['nightly_subtotal'] = $prices['subtotal'];
                $data['services_total'] = $data['services_total'] ?? 0;
                $data['discount_total'] = $data['discount_total'] ?? 0;
                $data['tax_total'] = $data['tax_total'] ?? 0;
                $data['cleaning_fee'] = 0;
                $data['security_deposit'] = $data['security_deposit'] ?? $accommodation->security_deposit ?? 0;
                $data['rate_snapshot'] = $prices['snapshot'];
                
                $data['total_amount'] = $data['nightly_subtotal'] 
                    + $data['services_total'] 
                    - $data['discount_total'] 
                    + $data['tax_total'];
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

            $isDayPass = $request->boolean('is_day_pass') || ($data['check_in_date'] === $data['check_out_date']);
            $data['is_day_pass'] = $isDayPass;

            // Si las fechas, alojamiento o desglose adultos/niños cambiaron, recalcular precios
            $adults = $data['adults_count'] ?? null;
            $children = $data['children_count'] ?? 0;
            $guests = $data['guests_count'] ?? (($adults ?? $reservation->adults_count) + ($children ?? $reservation->children_count));
            if (!isset($data['guests_count'])) {
                $data['guests_count'] = $guests;
            }
            if ($adults === null) {
                $adults = max($guests - $children, 1);
            }
            $datesChanged = $reservation->check_in_date->format('Y-m-d') !== $request->input('check_in_date')
                || $reservation->check_out_date->format('Y-m-d') !== $request->input('check_out_date')
                || $reservation->accommodation_id !== $request->input('accommodation_id')
                || ((bool) $reservation->is_day_pass !== $isDayPass)
                || ($request->filled('pricing_type') && ((string) $reservation->pricing_type?->value !== (string) $request->input('pricing_type')));
            $guestBreakdownChanged = ($reservation->adults_count !== $adults) || ($reservation->children_count !== $children);

            if ($datesChanged || $guestBreakdownChanged) {
                $accommodation = Accommodation::find($data['accommodation_id']);
                $prices = $pricing->calculateStayTotal(
                    $accommodation,
                    $data['check_in_date'],
                    $data['check_out_date'],
                    $guests,
                    $data['pricing_type'] ?? null,
                    $isDayPass,
                    $adults,
                    $children
                );

                $data['pricing_type'] = $prices['pricing_type'];

                $data['nights_count'] = $prices['nights'];
                $data['nightly_subtotal'] = $prices['subtotal'];
                $data['rate_snapshot'] = $prices['snapshot'];
                $data['total_amount'] = $data['nightly_subtotal'] 
                    + ($data['services_total'] ?? 0) 
                    - ($data['discount_total'] ?? 0) 
                    + ($data['tax_total'] ?? 0);
            }

            $oldStatus = $reservation->status;
            $newStatus = $data['status'] ?? $reservation->status;
            
            $reservation->update($data);

            // Si cambió el estado manualmente, guardar en histórico
            $oldStatusVal = is_object($oldStatus) && property_exists($oldStatus, 'value') ? $oldStatus->value : (string) $oldStatus;
            $newStatusVal = is_object($newStatus) && property_exists($newStatus, 'value') ? $newStatus->value : (string) $newStatus;

            if ($oldStatusVal !== $newStatusVal) {
                \App\Models\ReservationStatusHistory::create([
                    'reservation_id' => $reservation->id,
                    'status' => $newStatusVal,
                    'old_status' => $oldStatusVal,
                    'new_status' => $newStatusVal,
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

            // Si el error es por falta de pago, redirigir directamente al registro de pago
            if (str_contains($e->getMessage(), 'depósito o pago confirmado')) {
                return redirect()
                    ->route('payments.create', [
                        'reservation_id' => $reservation->id,
                        'guest_id'       => $reservation->primary_guest_id,
                        'payment_type'   => 'deposit',
                    ])
                    ->with('warning', 'Para confirmar la reserva <b>#' . $reservation->code . '</b> primero debes registrar un depósito o pago.');
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

    public function destroy(Reservation $reservation, Request $request, DeleteReservationAction $action): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $reservation);

        try {
            $action->execute($reservation);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Reserva eliminada exitosamente']);
            }

            return redirect()
                ->route('reservations.index')
                ->with('success', '¡Reserva eliminada exitosamente!');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function pdf(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        $reservation->load(['accommodation', 'primaryGuest', 'payments', 'createdBy']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reservation', compact('reservation'));

        return $pdf->stream("Reserva-{$reservation->code}.pdf");
    }

    public function sendEmail(Request $request, Reservation $reservation)
    {
        $this->authorize('view', $reservation);

        $request->validate([
            'email_recipient_type' => 'required|in:registered,custom',
            'custom_email'         => 'required_if:email_recipient_type,custom|nullable|email',
            'custom_message'       => 'nullable|string|max:1000',
        ]);

        $recipientEmail = $request->email_recipient_type === 'custom'
            ? $request->custom_email
            : $reservation->primaryGuest?->email;

        if (!$recipientEmail) {
            return back()->with('error', 'El huésped no tiene un correo registrado. Por favor especifica un correo válido.');
        }

        try {
            $reservation->load(['accommodation', 'primaryGuest', 'payments', 'createdBy']);
            \Illuminate\Support\Facades\Mail::to($recipientEmail)->send(
                new \App\Mail\ReservationInvoiceMail($reservation, $request->custom_message)
            );

            return back()->with('success', "Comprobante enviado exitosamente a <b>{$recipientEmail}</b> con el PDF adjunto.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar el correo: ' . $e->getMessage());
        }
    }
}
