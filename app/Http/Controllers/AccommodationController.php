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
        $this->authorize('viewAny', Accommodation::class);

        $accommodations = Accommodation::all();

        if ($request->wantsJson()) {
            return response()->json($accommodations);
        }

        return view('accommodations.index', compact('accommodations'));
    }

    public function show(Accommodation $accommodation)
    {
        $this->authorize('view', $accommodation);

        $accommodation->load(['amenities', 'ratePeriods', 'blockedPeriods']);

        if (request()->wantsJson()) {
            return response()->json($accommodation);
        }
        
        return view('accommodations.show', compact('accommodation'));
    }

    /**
     * Endpoint específico para buscar disponibilidad usando el AvailabilityService
     */
    public function available(Request $request, AvailabilityService $availabilityService): JsonResponse
    {
        $request->validate([
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        $availableAccommodations = $availabilityService->getAvailableAccommodations(
            $request->input('check_in_date'),
            $request->input('check_out_date')
        );

        return response()->json($availableAccommodations);
    }
}
