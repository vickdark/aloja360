<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatePeriodRequest;
use App\Http\Requests\UpdateRatePeriodRequest;
use App\Models\Accommodation;
use App\Models\RatePeriod;
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
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('accommodation', function ($aq) use ($search) {
                            $aq->where('name', 'like', "%{$search}%");
                        });
                });
            }
            $total = $query->count();
            $items = $query->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

            return response()->json(['data' => $items, 'total' => (int) $total]);
        }

        $active_count = RatePeriod::where('status', 'active')->count();
        $avg_nightly_price_active = (float) RatePeriod::where('status', 'active')->avg('price_per_night');
        $total_count = RatePeriod::count();

        return view('rate_periods.index', compact('active_count', 'avg_nightly_price_active', 'total_count'));
    }

    public function create()
    {
        $this->authorize('create', RatePeriod::class);
        $accommodations = Accommodation::orderBy('name')->get(['id','name','base_price','price_per_person','price_per_child','day_pass_base_price','day_pass_price_per_person','day_pass_price_per_child']);

        return view('rate_periods.create', compact('accommodations'));
    }

    public function store(StoreRatePeriodRequest $request)
    {
        $this->authorize('create', RatePeriod::class);
        $data = $request->validated();
        // Soporte "Todos los alojamientos": el Request permite valor "all"
        $applyToAll = ($request->input('accommodation_id') === 'all') || $request->boolean('apply_to_all');
        $data['is_weekend'] = $request->boolean('is_weekend', false);
        $data['is_holiday'] = $request->boolean('is_holiday', false);
        $data['adjustment_type'] = $data['adjustment_type'] ?? 'amount';
        // Normalizar child/accommodation adjustment: si no viene, dejar null para fallback al adulto
        if (empty($data['child_adjustment_type'])) {
            $data['child_adjustment_type'] = null;
        }
        if (!isset($data['child_adjustment_value']) || $data['child_adjustment_value'] === '' || $data['child_adjustment_value'] === null) {
            $data['child_adjustment_value'] = null;
        }
        if (empty($data['accommodation_adjustment_type'])) {
            $data['accommodation_adjustment_type'] = null;
        }
        if (!isset($data['accommodation_adjustment_value']) || $data['accommodation_adjustment_value'] === '' || $data['accommodation_adjustment_value'] === null) {
            $data['accommodation_adjustment_value'] = null;
        }

        if ($applyToAll) {
            $ids = Accommodation::pluck('id');
            $created = 0;
            foreach ($ids as $aid) {
                $payload = $data;
                $payload['accommodation_id'] = $aid;
                // Evitar colisión de validación: quitar apply_to_all
                unset($payload['apply_to_all']);
                RatePeriod::create($payload);
                $created++;
            }
            return redirect()->route('rate_periods.index')->with('success', "Se crearon {$created} reglas (una por alojamiento) correctamente.");
        }

        unset($data['apply_to_all']);
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
        $accommodations = Accommodation::orderBy('name')->get(['id','name','base_price','price_per_person','price_per_child','day_pass_base_price','day_pass_price_per_person','day_pass_price_per_child']);

        return view('rate_periods.edit', compact('ratePeriod', 'accommodations'));
    }

    public function update(UpdateRatePeriodRequest $request, RatePeriod $ratePeriod)
    {
        $this->authorize('update', $ratePeriod);
        $data = $request->validated();
        $data['is_weekend'] = $request->boolean('is_weekend', false);
        $data['is_holiday'] = $request->boolean('is_holiday', false);
        $data['adjustment_type'] = $data['adjustment_type'] ?? $ratePeriod->adjustment_type ?? 'amount';
        if (empty($data['child_adjustment_type'])) $data['child_adjustment_type'] = null;
        if (!isset($data['child_adjustment_value']) || $data['child_adjustment_value'] === '' ) $data['child_adjustment_value'] = null;
        if (empty($data['accommodation_adjustment_type'])) $data['accommodation_adjustment_type'] = null;
        if (!isset($data['accommodation_adjustment_value']) || $data['accommodation_adjustment_value'] === '' ) $data['accommodation_adjustment_value'] = null;
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
