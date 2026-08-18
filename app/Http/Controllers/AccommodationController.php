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

    /**
     * Listado de alojamientos.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\Usuarios\Usuario $user */
        $user = $request->user();

        if (! $user->hasPermission('accommodations.index')) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'No autorizado.'
                ], 403);
            }

            abort(403, 'No autorizado.');
        }

        $search = $request->query('search');
        $status = $request->query('status');
        $type = $request->query('type');

        $query = Accommodation::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('type', $type);
        }

        $accommodations = $query->orderBy('code')->get();

        if ($request->wantsJson()) {
            return response()->json($accommodations);
        }

        $statusCounts = Accommodation::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalCount = Accommodation::count();

        return view('accommodations.index', compact(
            'accommodations',
            'search',
            'status',
            'type',
            'statusCounts',
            'totalCount'
        ));
    }

    /**
     * Formulario para crear alojamiento.
     */
    public function create()
    {
        $this->authorize('create', Accommodation::class);

        $amenities = Amenity::all();

        return view('accommodations.create', compact('amenities'));
    }

    /**
     * Guardar alojamiento.
     */
    public function store(StoreAccommodationRequest $request)
    {
        $this->authorize('create', Accommodation::class);

        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $accommodation = Accommodation::create($data);

        if ($request->filled('amenities')) {
            $syncData = [];

            foreach ($request->input('amenities', []) as $amenityId) {
                $syncData[$amenityId] = [
                    'quantity' => 1
                ];
            }

            $accommodation->amenities()->sync($syncData);
        }

        return redirect()
            ->route('accommodations.index')
            ->with('success', 'Alojamiento creado exitosamente.');
    }

    /**
     * Mostrar alojamiento.
     */
    public function show(Request $request, Accommodation $accommodation)
    {
        $this->authorize('view', $accommodation);

        $accommodation->load([
            'amenities',
            'ratePeriods',
            'blockedPeriods',
            'images',
            'inventoryItems',
        ]);

        if ($request->wantsJson()) {
            return response()->json($accommodation);
        }

        return view('accommodations.show', compact('accommodation'));
    }

    /**
     * Formulario para editar alojamiento.
     */
    public function edit(Accommodation $accommodation)
    {
        $this->authorize('update', $accommodation);

        $amenities = Amenity::all();

        $accommodation->load('amenities');

        return view(
            'accommodations.edit',
            compact('accommodation', 'amenities')
        );
    }

    /**
     * Actualizar alojamiento.
     */
    public function update(
        UpdateAccommodationRequest $request,
        Accommodation $accommodation
    ) {
        $this->authorize('update', $accommodation);

        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $accommodation->update($data);

        if ($request->filled('amenities')) {
            $syncData = [];

            foreach ($request->input('amenities', []) as $amenityId) {
                $pivot = $accommodation
                    ->amenities()
                    ->where('amenities.id', $amenityId)
                    ->first()
                    ?->pivot;

                $syncData[$amenityId] = [
                    'quantity' => $pivot?->quantity ?? 1
                ];
            }

            $accommodation->amenities()->sync($syncData);
        } else {
            $accommodation->amenities()->detach();
        }

        return redirect()
            ->route('accommodations.index')
            ->with('success', 'Alojamiento actualizado exitosamente.');
    }

    /**
     * Eliminar alojamiento.
     */
    public function destroy(Accommodation $accommodation)
    {
        $this->authorize('delete', $accommodation);

        try {
            $accommodation->delete();

            return redirect()
                ->route('accommodations.index')
                ->with(
                    'success',
                    'Alojamiento eliminado exitosamente.'
                );
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('accommodations.index')
                ->with(
                    'error',
                    'No fue posible eliminar el alojamiento.'
                );
        }
    }

    /**
     * Buscar alojamientos disponibles.
     */
    public function available(
        Request $request,
        AvailabilityService $availabilityService
    ): JsonResponse {
        $validated = $request->validate([
            'check_in_date' => [
                'required',
                'date',
            ],
            'check_out_date' => [
                'required',
                'date',
                'after:check_in_date',
            ],
        ]);

        /** @var \App\Models\Usuarios\Usuario $user */
        $user = $request->user();

        if (! $user->hasPermission('accommodations.index')) {
            return response()->json([
                'message' => 'No autorizado.'
            ], 403);
        }

        $availableAccommodations =
            $availabilityService->getAvailableAccommodations(
                $validated['check_in_date'],
                $validated['check_out_date']
            );

        return response()->json($availableAccommodations);
    }
}