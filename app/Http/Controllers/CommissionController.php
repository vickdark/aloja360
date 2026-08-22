<?php

namespace App\Http\Controllers;

use App\Enums\CommissionStatus;
use App\Http\Requests\StoreCommissionRequest;
use App\Http\Requests\UpdateCommissionRequest;
use App\Models\Accommodation;
use App\Models\Commission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommissionController extends Controller
{
    use AuthorizesRequests;

    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', Commission::class);

        if ($request->ajax() || $request->wantsJson()) {
            $query = Commission::with(['accommodation']);
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('beneficiary_name', 'like', "%{$search}%")
                        ->orWhereHas('accommodation', function ($aq) use ($search) {
                            $aq->where('name', 'like', "%{$search}%");
                        });
                });
            }
            $total = $query->count();
            $commissions = $query->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

            return response()->json(['data' => $commissions, 'total' => (int) $total]);
        }

        $totals = Commission::query()
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) AS pending_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END), 0) AS paid_total")
            ->first();

        return view('commissions.index', compact('totals'));
    }

    public function create()
    {
        $this->authorize('create', Commission::class);
        $accommodations = Accommodation::all();

        return view('commissions.create', compact('accommodations'));
    }

    public function store(StoreCommissionRequest $request)
    {
        $this->authorize('create', Commission::class);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        if (($data['status'] ?? '') === CommissionStatus::Paid->value && empty($data['paid_date'])) {
            $data['paid_date'] = now()->toDateString();
        }

        Commission::create($data);

        return redirect()->route('commissions.index')->with('success', 'Comisión registrada.');
    }

    public function show(Commission $commission)
    {
        $this->authorize('view', $commission);

        return view('commissions.show', compact('commission'));
    }

    public function edit(Commission $commission)
    {
        $this->authorize('update', $commission);
        $accommodations = Accommodation::all();

        return view('commissions.edit', compact('commission', 'accommodations'));
    }

    public function update(UpdateCommissionRequest $request, Commission $commission)
    {
        $this->authorize('update', $commission);

        $data = $request->validated();

        if (($data['status'] ?? '') === CommissionStatus::Paid->value && empty($data['paid_date'])) {
            $data['paid_date'] = now()->toDateString();
        }
        if (($data['status'] ?? '') !== CommissionStatus::Paid->value) {
            $data['paid_date'] = null;
        }

        $commission->update($data);

        return redirect()->route('commissions.index')->with('success', 'Comisión actualizada.');
    }

    public function markPaid(Commission $commission, \Illuminate\Http\Request $request)
    {
        $this->authorize('update', $commission);

        if ($commission->status === CommissionStatus::Pending) {
            $commission->update([
                'status' => CommissionStatus::Paid,
                'paid_date' => now()->toDateString(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Comisión marcada como pagada.']);
        }

        return redirect()->route('commissions.index')->with('success', 'Comisión marcada como pagada.');
    }

    public function destroy(Commission $commission, \Illuminate\Http\Request $request)
    {
        $this->authorize('delete', $commission);
        $commission->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Comisión eliminada.']);
        }

        return redirect()->route('commissions.index')->with('success', 'Comisión eliminada.');
    }
}
