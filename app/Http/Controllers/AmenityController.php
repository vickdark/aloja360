<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Http\Requests\StoreAmenityRequest;
use App\Http\Requests\UpdateAmenityRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class AmenityController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Amenity::class);
        $amenities = Amenity::orderBy('sort_order')->orderBy('name')->paginate(20);
        $categories = Amenity::select('category')->whereNotNull('category')->distinct()->pluck('category');
        return view('amenities.index', compact('amenities', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Amenity::class);
        return view('amenities.create');
    }

    public function store(StoreAmenityRequest $request)
    {
        $this->authorize('create', Amenity::class);
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_default'] = $request->boolean('is_default', false);
        Amenity::create($data);
        return redirect()->route('amenities.index')->with('success', 'Amenidad creada correctamente.');
    }

    public function show(Amenity $amenity)
    {
        $this->authorize('view', $amenity);
        return view('amenities.show', compact('amenity'));
    }

    public function edit(Amenity $amenity)
    {
        $this->authorize('update', $amenity);
        return view('amenities.edit', compact('amenity'));
    }

    public function update(UpdateAmenityRequest $request, Amenity $amenity)
    {
        $this->authorize('update', $amenity);
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_default'] = $request->boolean('is_default', false);
        $amenity->update($data);
        return redirect()->route('amenities.index')->with('success', 'Amenidad actualizada correctamente.');
    }

    public function destroy(Amenity $amenity)
    {
        $this->authorize('delete', $amenity);
        $amenity->delete();
        return redirect()->route('amenities.index')->with('success', 'Amenidad eliminada correctamente.');
    }
}
