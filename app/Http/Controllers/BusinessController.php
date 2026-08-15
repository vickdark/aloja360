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
}
