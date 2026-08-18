<?php

namespace App\Http\Controllers;

use App\Models\CleaningTask;
use App\Models\Accommodation;
use App\Models\Usuarios\Usuario;
use App\Http\Requests\StoreCleaningTaskRequest;
use App\Http\Requests\UpdateCleaningTaskRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CleaningTaskController extends Controller
{
    use AuthorizesRequests;

    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', CleaningTask::class);

        if ($request->ajax() || $request->wantsJson()) {
            $query = CleaningTask::with(['accommodation', 'assignedTo']);
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');
            if ($search) {
                $query->whereHas('accommodation', function($aq) use ($search) {
                    $aq->where('name', 'like', "%{$search}%");
                });
            }
            $total = $query->count();
            $tasks = $query->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
            return response()->json(['data' => $tasks, 'total' => (int)$total]);
        }

        return view('cleaning.index');
    }

    public function create()
    {
        $this->authorize('create', CleaningTask::class);
        $accommodations = Accommodation::all();
        $users = Usuario::all(); // idealmente solo los que tienen rol de limpieza
        return view('cleaning.create', compact('accommodations', 'users'));
    }

    public function store(StoreCleaningTaskRequest $request)
    {
        $this->authorize('create', CleaningTask::class);
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        
        CleaningTask::create($data);
        return redirect()->route('cleaning.index')->with('success', 'Tarea de limpieza registrada.');
    }

    public function show(CleaningTask $cleaning)
    {
        $this->authorize('view', $cleaning);
        return view('cleaning.show', compact('cleaning'));
    }

    public function edit(CleaningTask $cleaning)
    {
        $this->authorize('update', $cleaning);
        $users = Usuario::all();
        return view('cleaning.edit', compact('cleaning', 'users'));
    }

    public function update(UpdateCleaningTaskRequest $request, CleaningTask $cleaning)
    {
        $this->authorize('update', $cleaning);
        $data = $request->validated();

        if ($data['status'] === 'completed' && $cleaning->status->value !== 'completed') {
            $data['completed_at'] = now();
            $data['completed_by'] = auth()->id();
            
            // Si está completado, el alojamiento vuelve a estar disponible (lógica simple)
            $cleaning->accommodation->update(['status' => 'available']);
        } elseif ($data['status'] === 'in_progress' && $cleaning->status->value !== 'in_progress') {
            $data['started_at'] = now();
            $cleaning->accommodation->update(['status' => 'cleaning']);
        }

        $cleaning->update($data);
        return redirect()->route('cleaning.index')->with('success', 'Tarea actualizada.');
    }

    public function destroy(CleaningTask $cleaning, \Illuminate\Http\Request $request)
    {
        $this->authorize('delete', $cleaning);
        $cleaning->delete();
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Tarea eliminada.']);
        }
        return redirect()->route('cleaning.index')->with('success', 'Tarea eliminada.');
    }
}
