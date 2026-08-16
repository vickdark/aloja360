<?php

namespace App\Http\Controllers;

use App\Models\BlockedPeriod;
use App\Models\Accommodation;
use App\Http\Requests\StoreBlockedPeriodRequest;
use App\Http\Requests\UpdateBlockedPeriodRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class BlockedPeriodController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', BlockedPeriod::class);
        $blockedPeriods = BlockedPeriod::with('accommodation', 'createdBy')->latest()->paginate(20);
        return view('blocked_periods.index', compact('blockedPeriods'));
    }

    public function create()
    {
        $this->authorize('create', BlockedPeriod::class);
        $accommodations = Accommodation::orderBy('name')->pluck('name', 'id');
        return view('blocked_periods.create', compact('accommodations'));
    }

    public function store(StoreBlockedPeriodRequest $request)
    {
        $this->authorize('create', BlockedPeriod::class);
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $data['is_active'] = $request->boolean('is_active', true);
        BlockedPeriod::create($data);
        return redirect()->route('blocked_periods.index')->with('success', 'Bloqueo creado correctamente.');
    }

    public function show(BlockedPeriod $blockedPeriod)
    {
        $this->authorize('view', $blockedPeriod);
        return view('blocked_periods.show', compact('blockedPeriod'));
    }

    public function edit(BlockedPeriod $blockedPeriod)
    {
        $this->authorize('update', $blockedPeriod);
        $accommodations = Accommodation::orderBy('name')->pluck('name', 'id');
        return view('blocked_periods.edit', compact('blockedPeriod', 'accommodations'));
    }

    public function update(UpdateBlockedPeriodRequest $request, BlockedPeriod $blockedPeriod)
    {
        $this->authorize('update', $blockedPeriod);
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $blockedPeriod->update($data);
        return redirect()->route('blocked_periods.index')->with('success', 'Bloqueo actualizado correctamente.');
    }

    public function destroy(BlockedPeriod $blockedPeriod)
    {
        $this->authorize('delete', $blockedPeriod);
        $blockedPeriod->delete();
        return redirect()->route('blocked_periods.index')->with('success', 'Bloqueo eliminado correctamente.');
    }
}
