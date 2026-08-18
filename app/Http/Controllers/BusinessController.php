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

        if ($request->ajax() || $request->wantsJson()) {
            $query = Business::query();
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('legal_name', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            $total = $query->count();
            $businesses = $query->orderBy('name')->offset($offset)->limit($limit)->get();
            return response()->json(['data' => $businesses, 'total' => (int)$total]);
        }

        $businesses = Business::orderBy('name')->paginate(10);
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

        Business::create($request->validated());

        return redirect()->route('businesses.index')
            ->with('success', 'Configuración de empresa creada exitosamente.');
    }

    public function show(Business $business)
    {
        $this->authorize('view', $business);
        return view('businesses.show', compact('business'));
    }

    public function edit(Business $business)
    {
        $this->authorize('update', $business);
        return view('businesses.edit', compact('business'));
    }

    public function update(\App\Http\Requests\UpdateBusinessRequest $request, Business $business)
    {
        $this->authorize('update', $business);
        $business->update($request->validated());
        return redirect()->route('businesses.show', $business)->with('success', 'Datos de la empresa actualizados exitosamente.');
    }
}
