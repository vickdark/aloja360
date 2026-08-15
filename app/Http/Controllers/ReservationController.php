<?php

namespace App\Http\Controllers;

use App\Actions\CancelReservationAction;
use App\Actions\CheckInReservationAction;
use App\Actions\CheckOutReservationAction;
use App\Actions\ConfirmReservationAction;
use App\Actions\CreateReservationAction;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $businessId = $request->query('business_id', $request->user()->current_business_id ?? $request->user()->businesses()->first()?->id);

        if (! $businessId) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'El business_id es requerido'], 400);
            }

            return redirect()->route('dashboard')->with('error', 'Debe seleccionar un negocio.');
        }

        /** @var \App\Models\Usuarios\Usuario $user */
        $user = $request->user();

        if (! $user->hasPermission('reservations.index')) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'No autorizado para este negocio'], 403);
            }

            return abort(403, 'No autorizado para este negocio.');
        }

        $reservations = Reservation::with(['accommodation', 'primaryGuest'])
            ->where('business_id', $businessId)
            ->orderBy('check_in_date', 'asc')
            ->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($reservations);
        }

        return view('reservations.index', compact('reservations', 'businessId'));
    }

    public function store(StoreReservationRequest $request, CreateReservationAction $action): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['created_by'] = $request->user()->id;

            $reservation = $action->execute($data);

            return response()->json([
                'message' => 'Reserva creada exitosamente',
                'data' => $reservation->load(['accommodation', 'primaryGuest']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(Reservation $reservation): JsonResponse
    {
        $this->authorize('view', $reservation);

        $reservation->load([
            'accommodation',
            'primaryGuest',
            'services',
            'payments',
            'statusHistories.changedBy',
        ]);

        return response()->json($reservation);
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation): JsonResponse
    {
        $reservation->update($request->validated());

        return response()->json([
            'message' => 'Reserva actualizada exitosamente',
            'data' => $reservation,
        ]);
    }

    public function confirm(Reservation $reservation, Request $request, ConfirmReservationAction $action): JsonResponse
    {
        $this->authorize('update', $reservation);

        try {
            $notes = $request->input('notes', 'Confirmación manual');
            $action->execute($reservation, $request->user()->id, $notes);

            return response()->json(['message' => 'Reserva confirmada exitosamente', 'data' => $reservation]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function checkIn(Reservation $reservation, Request $request, CheckInReservationAction $action): JsonResponse
    {
        $this->authorize('update', $reservation);

        try {
            $notes = $request->input('notes', '');
            $action->execute($reservation, $request->user()->id, $notes);

            return response()->json(['message' => 'Check-in realizado exitosamente', 'data' => $reservation]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function checkOut(Reservation $reservation, Request $request, CheckOutReservationAction $action): JsonResponse
    {
        $this->authorize('update', $reservation);

        try {
            $notes = $request->input('notes', '');
            $action->execute($reservation, $request->user()->id, $notes);

            return response()->json(['message' => 'Check-out realizado exitosamente', 'data' => $reservation]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function cancel(Reservation $reservation, Request $request, CancelReservationAction $action): JsonResponse
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

            return response()->json(['message' => 'Reserva cancelada exitosamente', 'data' => $reservation]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
