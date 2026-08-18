<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Guest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        if ($request->ajax() || $request->wantsJson()) {
            $query = Payment::with(['reservation', 'guest']);
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                      ->orWhereHas('reservation', function($rq) use ($search) {
                          $rq->where('code', 'like', "%{$search}%");
                      })
                      ->orWhereHas('guest', function($gq) use ($search) {
                          $gq->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            }
            $total = $query->count();
            $payments = $query->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
            return response()->json(['data' => $payments, 'total' => (int)$total]);
        }

        return view('payments.index');
    }

    public function create()
    {
        $this->authorize('create', Payment::class);
        $reservations = Reservation::whereNotIn('status', ['cancelled'])->get();
        $guests = Guest::all();
        return view('payments.create', compact('reservations', 'guests'));
    }

    public function store(StorePaymentRequest $request)
    {
        $this->authorize('create', Payment::class);
        
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['code'] = 'PAY-' . strtoupper(uniqid());
        
        if ($data['status'] === 'confirmed') {
            $data['confirmed_at'] = now();
            $data['confirmed_by'] = auth()->id();
        }

        Payment::create($data);
        return redirect()->route('payments.index')->with('success', 'Pago registrado correctamente.');
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $this->authorize('update', $payment);
        return view('payments.edit', compact('payment'));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $this->authorize('update', $payment);
        
        $data = $request->validated();
        
        if ($data['status'] === 'confirmed' && $payment->status->value !== 'confirmed') {
            $data['confirmed_at'] = now();
            $data['confirmed_by'] = auth()->id();
        } elseif ($data['status'] === 'rejected' && $payment->status->value !== 'rejected') {
            $data['rejected_at'] = now();
        }

        $payment->update($data);
        return redirect()->route('payments.index')->with('success', 'Pago actualizado correctamente.');
    }

    public function destroy(Payment $payment)
    {
        $this->authorize('delete', $payment);
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Pago eliminado correctamente.');
    }
}
