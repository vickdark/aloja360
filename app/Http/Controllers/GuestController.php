<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GuestController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Guest::class);
        $guests = Guest::latest()->paginate(15);
        return view('guests.index', compact('guests'));
    }

    public function create()
    {
        $this->authorize('create', Guest::class);
        return view('guests.create');
    }

    public function store(StoreGuestRequest $request)
    {
        $this->authorize('create', Guest::class);
        Guest::create($request->validated());
        return redirect()->route('guests.index')->with('success', 'Huésped creado correctamente.');
    }

    public function show(Guest $guest)
    {
        $this->authorize('view', $guest);
        return view('guests.show', compact('guest'));
    }

    public function edit(Guest $guest)
    {
        $this->authorize('update', $guest);
        return view('guests.edit', compact('guest'));
    }

    public function update(UpdateGuestRequest $request, Guest $guest)
    {
        $this->authorize('update', $guest);
        $guest->update($request->validated());
        return redirect()->route('guests.index')->with('success', 'Huésped actualizado correctamente.');
    }

    public function destroy(Guest $guest)
    {
        $this->authorize('delete', $guest);
        $guest->delete();
        return redirect()->route('guests.index')->with('success', 'Huésped eliminado correctamente.');
    }
}
