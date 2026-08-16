<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Amenity;
use App\Services\AvailabilityService;
use App\Http\Requests\StoreAccommodationRequest;
use App\Http\Requests\UpdateAccommodationRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccommodationController extends Controller
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

        if (! $user->hasPermission('accommodations.index')) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'No autorizado para este negocio'], 403);
            }

            return abort(403, 'No autorizado para este negocio.');
        }

        $accommodations = Accommodation::where('business_id', $businessId)->get();

        if ($request->wantsJson()) {
            return response()->json($accommodations);
        }

        return view('accommodations.index', compact('accommodations'));
    }

    public function create()
    {
        $this->authorize('create', Accommodation::class);
        $amenities = Amenity::all();
        return view('accommodations.create', compact('amenities'));
    }

    public function store(StoreAccommodationRequest $request)
    {
        $this->authorize('create', Accommodation::class);

        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $accommodation = Accommodation::create($data);

        if ($request->has('amenities')) {
            $syncData = [];
            foreach ($request->amenities as $amenityId) {
                $syncData[$amenityId] = ['quantity' => 1];
            }
            $accommodation->amenities()->sync($syncData);
        }

        return redirect()->route('accommodations.index')->with('success', 'Alojamiento creado exitosamente.');
    }

    public function show(Accommodation $accommodation)
    {
        $this->authorize('view', $accommodation);

        $accommodation->load(['amenities', 'ratePeriods', 'blockedPeriods', 'images', 'inventoryItems']);

        if (request()->wantsJson()) {
            return response()->json($accommodation);
        }
        
        return view('accommodations.show', compact('accommodation'));
    }

    public function edit(Accommodation $accommodation)
    {
        $this->authorize('update', $accommodation);
        $amenities = Amenity::all();
        $accommodation->load('amenities');
        return view('accommodations.edit', compact('accommodation', 'amenities'));
    }

    public function update(UpdateAccommodationRequest $request, Accommodation $accommodation)
    {
        $this->authorize('update', $accommodation);

        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $accommodation->update($data);

        if ($request->has('amenities')) {
            $syncData = [];
            foreach ($request->amenities as $amenityId) {
                $currentPivot = $accommodation->amenities()->where('amenity_id', $amenityId)->first()?->pivot;
                $syncData[$amenityId] = ['quantity' => $currentPivot?->quantity ?? 1];
            }
            $accommodation->amenities()->sync($syncData);
        } else {
            $accommodation->amenities()->detach();
        }

        return redirect()->route('accommodations.index')->with('success', 'Alojamiento actualizado exitosamente.');
    }

    public function destroy(Accommodation $accommodation)
    {
        $this->authorize('delete', $accommodation);

        try {
            $accommodation->delete();
            return redirect()->route('accommodations.index')->with('success', 'Alojamiento eliminado (Soft Delete).');
        } catch (\Exception $e) {
            return redirect()->route('accommodations.index')->with('error', 'Error al eliminar alojamiento: ' . $e->getMessage());
        }
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

        $businessId = $request->input('business_id');

        /** @var \App\Models\Usuarios\Usuario $user */
        $user = $request->user();

        if (! $user->hasPermission('accommodations.index')) {
            return response()->json(['message' => 'No autorizado para este negocio'], 403);
        }

        $availableAccommodations = $availabilityService->getAvailableAccommodations(
            $request->input('check_in_date'),
            $request->input('check_out_date')
        );

        return response()->json($availableAccommodations);
    }
}
