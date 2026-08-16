<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Accommodation;
use App\Models\Usuarios\Usuario;
use App\Http\Requests\StoreMaintenanceRequest;
use App\Http\Requests\UpdateMaintenanceRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MaintenanceRequestController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', MaintenanceRequest::class);
        $requests = MaintenanceRequest::with(['accommodation', 'assignedTo', 'reportedBy'])
            ->latest()
            ->paginate(15);
        return view('maintenance.index', compact('requests'));
    }

    public function create()
    {
        $this->authorize('create', MaintenanceRequest::class);
        $accommodations = Accommodation::all();
        $users = Usuario::all(); // idealmente técnicos
        return view('maintenance.create', compact('accommodations', 'users'));
    }

    public function store(StoreMaintenanceRequest $request)
    {
        $this->authorize('create', MaintenanceRequest::class);
        
        $data = $request->validated();
        $data['reported_by'] = auth()->id();
        $data['reported_at'] = now();
        $data['blocks_accommodation'] = $request->has('blocks_accommodation');

        $maintenance = MaintenanceRequest::create($data);
        
        if ($data['blocks_accommodation']) {
            $maintenance->accommodation->update(['status' => 'maintenance']);
        }

        return redirect()->route('maintenance.index')->with('success', 'Mantenimiento reportado correctamente.');
    }

    public function show(MaintenanceRequest $maintenance)
    {
        $this->authorize('view', $maintenance);
        return view('maintenance.show', compact('maintenance'));
    }

    public function edit(MaintenanceRequest $maintenance)
    {
        $this->authorize('update', $maintenance);
        $users = Usuario::all();
        return view('maintenance.edit', compact('maintenance', 'users'));
    }

    public function update(UpdateMaintenanceRequest $request, MaintenanceRequest $maintenance)
    {
        $this->authorize('update', $maintenance);
        $data = $request->validated();

        if ($data['status'] === 'completed' && $maintenance->status->value !== 'completed') {
            $data['completed_at'] = now();
            $data['completed_by'] = auth()->id();
            
            if ($maintenance->blocks_accommodation) {
                $maintenance->accommodation->update(['status' => 'available']);
            }
        } elseif ($data['status'] === 'in_progress' && $maintenance->status->value !== 'in_progress') {
            $data['started_at'] = now();
        }

        $maintenance->update($data);
        return redirect()->route('maintenance.index')->with('success', 'Mantenimiento actualizado.');
    }

    public function destroy(MaintenanceRequest $maintenance)
    {
        $this->authorize('delete', $maintenance);
        
        if ($maintenance->blocks_accommodation && $maintenance->status->value !== 'completed') {
            $maintenance->accommodation->update(['status' => 'available']);
        }
        
        $maintenance->delete();
        return redirect()->route('maintenance.index')->with('success', 'Reporte eliminado.');
    }
}
