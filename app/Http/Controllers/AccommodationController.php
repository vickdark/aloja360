<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\AccommodationImage;
use App\Models\Amenity;
use App\Services\AvailabilityService;
use App\Http\Requests\StoreAccommodationRequest;
use App\Http\Requests\UpdateAccommodationRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AccommodationController extends Controller
{
    use AuthorizesRequests;

    private const MAX_IMAGES = 10;

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

        $accommodations = $query->with('images')->orderBy('code')->get();

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

        unset($data['images']);

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

        $this->handleImageUploads($request, $accommodation);

        return redirect()
            ->route('accommodations.show', $accommodation)
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
            'images' => fn ($q) => $q->orderBy('sort_order'),
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

        $accommodation->load([
            'amenities',
            'images' => fn ($q) => $q->orderBy('sort_order'),
        ]);

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

        unset($data['images']);

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

        $this->handleImageUploads($request, $accommodation);

        return redirect()
            ->route('accommodations.show', $accommodation)
            ->with('success', 'Alojamiento actualizado exitosamente.');
    }

    /**
     * Eliminar imagen del alojamiento.
     */
    public function destroyImage(Accommodation $accommodation, AccommodationImage $image)
    {
        $this->authorize('update', $accommodation);

        abort_unless($image->accommodation_id === $accommodation->id, 404);

        Storage::disk($image->disk)->delete($image->path);

        $image->delete();

        return redirect()
            ->route('accommodations.edit', $accommodation)
            ->with('success', 'Imagen eliminada.');
    }

    /**
     * Eliminar alojamiento.
     */
    public function destroy(Accommodation $accommodation)
    {
        $this->authorize('delete', $accommodation);

        try {
            foreach ($accommodation->images as $image) {
                Storage::disk($image->disk)->delete($image->path);
            }

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

    /**
     * Procesar subida de imágenes para un alojamiento.
     */
    private function handleImageUploads(
        Request $request,
        Accommodation $accommodation
    ): void {
        if (! $request->hasFile('images')) {
            return;
        }

        $existingCount = $accommodation->images()->count();
        $files = $request->file('images');
        $captions = $request->input('image_captions', []);
        $maxNew = self::MAX_IMAGES - $existingCount;

        foreach (array_slice($files, 0, $maxNew) as $index => $file) {
            $path = $file->store('accommodations/' . $accommodation->id, 'public');

            $accommodation->images()->create([
                'path' => $path,
                'disk' => 'public',
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'caption' => $captions[$index] ?? null,
                'is_primary' => $accommodation->images()->count() === 0 && $index === 0,
                'sort_order' => $existingCount + $index,
            ]);
        }
    }
}