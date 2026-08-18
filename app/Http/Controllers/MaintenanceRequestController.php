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

    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', MaintenanceRequest::class);

        if ($request->ajax() || $request->wantsJson()) {
            $query = MaintenanceRequest::with(['accommodation', 'assignedTo', 'reportedBy']);
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%")
                      ->orWhereHas('accommodation', function($aq) use ($search) {
                          $aq->where('name', 'like', "%{$search}%");
                      });
                });
            }
            $total = $query->count();
            $items = $query->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
            return response()->json(['data' => $items, 'total' => (int)$total]);
        }

        return view('maintenance.index');
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

    public function destroy(MaintenanceRequest $maintenance, \Illuminate\Http\Request $request)
    {
        $this->authorize('delete', $maintenance);
        
        if ($maintenance->blocks_accommodation && $maintenance->status->value !== 'completed') {
            $maintenance->accommodation->update(['status' => 'available']);
        }
        
        $maintenance->delete();
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Reporte eliminado.']);
        }
        return redirect()->route('maintenance.index')->with('success', 'Reporte eliminado.');
    }
}
