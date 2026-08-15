<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccommodationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $businessId = $request->query('business_id', $request->user()->businesses()->first()?->id);
        
        if (!$businessId) {
            if ($request->wantsJson()) return response()->json(['message' => 'El business_id es requerido'], 400);
            return redirect()->route('dashboard')->with('error', 'Debe seleccionar un negocio.');
        }

        /** @var \App\Models\Usuarios\Usuario $user */
        $user = $request->user();

        if (!$user->belongsToBusiness($businessId)) {
            if ($request->wantsJson()) return response()->json(['message' => 'No autorizado para este negocio'], 403);
            return abort(403, 'No autorizado para este negocio.');
        }

        $accommodations = Accommodation::where('business_id', $businessId)->get();

        if ($request->wantsJson()) {
            return response()->json($accommodations);
        }

        return view('accommodations.index', compact('accommodations', 'businessId'));
    }

    public function show(Accommodation $accommodation): JsonResponse
    {
        $this->authorize('view', $accommodation);

        $accommodation->load(['amenities', 'ratePeriods', 'blockedPeriods']);

        return response()->json($accommodation);
    }

    /**
     * Endpoint específico para buscar disponibilidad usando el AvailabilityService
     */
    public function available(Request $request, AvailabilityService $availabilityService): JsonResponse
    {
        $request->validate([
            'business_id' => 'required|integer',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        $businessId = $request->input('business_id');

        /** @var \App\Models\Usuarios\Usuario $user */
        $user = $request->user();

        if (!$user->belongsToBusiness($businessId)) {
            return response()->json(['message' => 'No autorizado para este negocio'], 403);
        }

        $availableAccommodations = $availabilityService->getAvailableAccommodations(
            $businessId,
            $request->input('check_in_date'),
            $request->input('check_out_date')
        );

        return response()->json($availableAccommodations);
    }
}
