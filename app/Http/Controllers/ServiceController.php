<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Service::class);
        $services = Service::orderBy('sort_order')->orderBy('name')->paginate(20);
        return view('services.index', compact('services'));
    }

    public function create()
    {
        $this->authorize('create', Service::class);
        return view('services.create');
    }

    public function store(StoreServiceRequest $request)
    {
        $this->authorize('create', Service::class);
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_taxable'] = $request->boolean('is_taxable', false);
        $data['is_active'] = $request->boolean('is_active', true);
        Service::create($data);
        return redirect()->route('services.index')->with('success', 'Servicio creado correctamente.');
    }

    public function show(Service $service)
    {
        $this->authorize('view', $service);
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $this->authorize('update', $service);
        return view('services.edit', compact('service'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $this->authorize('update', $service);
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_taxable'] = $request->boolean('is_taxable', false);
        $data['is_active'] = $request->boolean('is_active', true);
        $service->update($data);
        return redirect()->route('services.index')->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Servicio eliminado correctamente.');
    }
}
