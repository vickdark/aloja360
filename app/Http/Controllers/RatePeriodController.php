<?php

namespace App\Http\Controllers;

use App\Models\RatePeriod;
use App\Models\Accommodation;
use App\Http\Requests\StoreRatePeriodRequest;
use App\Http\Requests\UpdateRatePeriodRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RatePeriodController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', RatePeriod::class);
        $ratePeriods = RatePeriod::with('accommodation')->latest()->paginate(20);
        return view('rate_periods.index', compact('ratePeriods'));
    }

    public function create()
    {
        $this->authorize('create', RatePeriod::class);
        $accommodations = Accommodation::orderBy('name')->pluck('name', 'id');
        return view('rate_periods.create', compact('accommodations'));
    }

    public function store(StoreRatePeriodRequest $request)
    {
        $this->authorize('create', RatePeriod::class);
        $data = $request->validated();
        $data['is_weekend'] = $request->boolean('is_weekend', false);
        $data['is_holiday'] = $request->boolean('is_holiday', false);
        RatePeriod::create($data);
        return redirect()->route('rate_periods.index')->with('success', 'Temporada creada correctamente.');
    }

    public function show(RatePeriod $ratePeriod)
    {
        $this->authorize('view', $ratePeriod);
        return view('rate_periods.show', compact('ratePeriod'));
    }

    public function edit(RatePeriod $ratePeriod)
    {
        $this->authorize('update', $ratePeriod);
        $accommodations = Accommodation::orderBy('name')->pluck('name', 'id');
        return view('rate_periods.edit', compact('ratePeriod', 'accommodations'));
    }

    public function update(UpdateRatePeriodRequest $request, RatePeriod $ratePeriod)
    {
        $this->authorize('update', $ratePeriod);
        $data = $request->validated();
        $data['is_weekend'] = $request->boolean('is_weekend', false);
        $data['is_holiday'] = $request->boolean('is_holiday', false);
        $ratePeriod->update($data);
        return redirect()->route('rate_periods.index')->with('success', 'Temporada actualizada correctamente.');
    }

    public function destroy(RatePeriod $ratePeriod)
    {
        $this->authorize('delete', $ratePeriod);
        $ratePeriod->delete();
        return redirect()->route('rate_periods.index')->with('success', 'Temporada eliminada correctamente.');
    }
}
