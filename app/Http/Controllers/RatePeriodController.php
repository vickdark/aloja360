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

    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', RatePeriod::class);

        if ($request->ajax() || $request->wantsJson()) {
            $query = RatePeriod::with('accommodation');
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('accommodation', function($aq) use ($search) {
                          $aq->where('name', 'like', "%{$search}%");
                      });
                });
            }
            $total = $query->count();
            $items = $query->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
            return response()->json(['data' => $items, 'total' => (int)$total]);
        }

        $active_count = RatePeriod::where('status', 'active')->count();
        $avg_nightly_price_active = (float)RatePeriod::where('status', 'active')->avg('price_per_night');
        $total_count = RatePeriod::count();
        return view('rate_periods.index', compact('active_count', 'avg_nightly_price_active', 'total_count'));
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

    public function destroy(RatePeriod $ratePeriod, \Illuminate\Http\Request $request)
    {
        $this->authorize('delete', $ratePeriod);
        $ratePeriod->delete();
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Temporada eliminada correctamente.']);
        }
        return redirect()->route('rate_periods.index')->with('success', 'Temporada eliminada correctamente.');
    }
}
