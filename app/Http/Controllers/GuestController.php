<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GuestController extends Controller
{
    use AuthorizesRequests;

    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', Guest::class);

        if ($request->ajax() || $request->wantsJson()) {
            $query = Guest::query();
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('document_number', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }
            $total = $query->count();
            $guests = $query->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
            return response()->json(['data' => $guests, 'total' => (int)$total]);
        }

        return view('guests.index');
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

    public function destroy(Guest $guest, \Illuminate\Http\Request $request)
    {
        $this->authorize('delete', $guest);
        $guest->delete();
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Huésped eliminado correctamente.']);
        }
        return redirect()->route('guests.index')->with('success', 'Huésped eliminado correctamente.');
    }
}
