<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessRequest;
use App\Models\Business;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Business::class);

        /** @var \App\Models\Usuarios\Usuario $user */
        $user = $request->user();

        // Si es super admin ve todos, si no, solo a los que pertenece
        if ($user->hasRole('Super Administrador')) {
            $businesses = Business::orderBy('name')->paginate(10);
        } else {
            $businesses = $user->businesses()->orderBy('name')->paginate(10);
        }

        return view('businesses.index', compact('businesses'));
    }

    public function create()
    {
        $this->authorize('create', Business::class);

        return view('businesses.create');
    }

    public function store(StoreBusinessRequest $request)
    {
        $this->authorize('create', Business::class);

        DB::transaction(function () use ($request) {
            $business = Business::create($request->validated());

            // Asignar el usuario actual como propietario (owner) del nuevo negocio
            // Asumiendo que el rol 'owner' existe en la BD
            $role = \App\Models\Roles\Role::where('slug', 'owner')->first();
            
            if ($role) {
                $request->user()->businesses()->attach($business->id, ['role_id' => $role->id]);
            }
        });

        return redirect()->route('businesses.index')
            ->with('success', 'Negocio creado exitosamente.');
    }
}
