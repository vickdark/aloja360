<?php

namespace App\Http\Controllers;

use App\Enums\QuoteStatus;
use App\Enums\ReservationStatus;
use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Accommodation;
use App\Models\Guest;
use App\Models\Quote;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Quote::class);

        $query = Quote::with(['accommodation', 'guest']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->whereHas('guest', function($gq) use ($s) {
                    $gq->where('first_name', 'LIKE', "%$s%")->orWhere('last_name', 'LIKE', "%$s%")->orWhere('document_number', 'LIKE', "%$s%");
                })->orWhere('code', 'LIKE', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $total = $query->count();
            $items = $query->latest('created_at')->offset($offset)->limit($limit)->get();
            return response()->json(['data' => $items, 'total' => (int)$total]);
        }

        $quotes = $query->latest('created_at')->paginate(15)->withQueryString();
        $statusCounts = Quote::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status');

        return view('quotes.index', compact('quotes', 'statusCounts'));
    }

    public function create()
    {
        $this->authorize('create', Quote::class);
        $accommodations = Accommodation::where('status', '!=', 'maintenance')->orderBy('name')->get();
        $guests = Guest::all()->sortBy('first_name')->mapWithKeys(function ($g) {
            $doc = $g->document_number ? ' ('.$g->document_number.')' : '';
            $phone = $g->phone ? ' - '.$g->phone : '';
            return [$g->id => trim("{$g->first_name} {$g->last_name}{$doc}{$phone}")];
        })->prepend('Seleccionar huésped', '');

        return view('quotes.create', compact('accommodations', 'guests'));
    }

    public function store(StoreQuoteRequest $request, PricingService $pricingService)
    {
        $this->authorize('create', Quote::class);

        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['created_by'] = auth()->id();
            $data['code'] = $data['code'] ?? ('COT-' . strtoupper(Str::random(6)));
            $data['status'] = QuoteStatus::Draft;
            $data['guests_count'] = ($data['adults_count'] ?? 1) + ($data['children_count'] ?? 0);

            $checkIn = now()->parse($data['check_in_date']);
            $checkOut = now()->parse($data['check_out_date']);
            
            $isDayPass = $request->boolean('is_day_pass') || ($data['check_in_date'] === $data['check_out_date']);
            $data['is_day_pass'] = $isDayPass;
            $data['nights_count'] = $isDayPass ? 0 : $checkIn->diffInDays($checkOut);

            // Calcular Precios Base
            $accommodation = Accommodation::findOrFail($data['accommodation_id']);
            $prices = $pricingService->calculateStayTotal(
                $accommodation,
                $checkIn,
                $checkOut,
                $data['guests_count'],
                $data['pricing_type'] ?? null,
                $isDayPass
            );

            $data['nightly_subtotal'] = $prices['subtotal'];
            $data['rate_snapshot'] = $prices['snapshot'];
            $data['pricing_type'] = $prices['pricing_type'];
            $data['cleaning_fee'] = $data['cleaning_fee'] ?? $accommodation->cleaning_fee ?? 0;
            $data['security_deposit'] = $data['security_deposit'] ?? $accommodation->security_deposit ?? 0;
            $data['discount_total'] = $data['discount_total'] ?? 0;
            $data['tax_total'] = $data['tax_total'] ?? 0;
            
            $total = $prices['subtotal'] + $data['cleaning_fee'] + $data['security_deposit'] - $data['discount_total'] + $data['tax_total'];
            $data['total_amount'] = $total;

            $quote = Quote::create($data);

            DB::commit();
            return redirect()->route('quotes.show', $quote)->with('success', 'Cotización creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al crear cotización: ' . $e->getMessage());
        }
    }

    public function show(Quote $quote)
    {
        $this->authorize('view', $quote);
        $quote->load(['accommodation', 'guest', 'createdBy', 'reservation']);
        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $this->authorize('update', $quote);
        
        if ($quote->status === QuoteStatus::Converted || $quote->reservation_id) {
            return redirect()->route('quotes.show', $quote)->with('warning', 'No se puede editar una cotización ya convertida.');
        }

        $accommodations = Accommodation::orderBy('name')->get();
        $guests = Guest::all()->sortBy('first_name')->mapWithKeys(function ($g) {
            $doc = $g->document_number ? ' ('.$g->document_number.')' : '';
            $phone = $g->phone ? ' - '.$g->phone : '';
            return [$g->id => trim("{$g->first_name} {$g->last_name}{$doc}{$phone}")];
        })->prepend('Seleccionar huésped', '');

        return view('quotes.edit', compact('quote', 'accommodations', 'guests'));
    }

    public function update(UpdateQuoteRequest $request, Quote $quote, PricingService $pricingService)
    {
        $this->authorize('update', $quote);

        if ($quote->status === QuoteStatus::Converted || $quote->reservation_id) {
            return redirect()->route('quotes.show', $quote)->with('warning', 'No se puede editar una cotización ya convertida.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['guests_count'] = ($data['adults_count'] ?? 1) + ($data['children_count'] ?? 0);
            
            $isDayPass = $request->boolean('is_day_pass') || ($data['check_in_date'] === $data['check_out_date']);
            $data['is_day_pass'] = $isDayPass;

            $checkIn = now()->parse($data['check_in_date']);
            $checkOut = now()->parse($data['check_out_date']);
            $data['nights_count'] = $isDayPass ? 0 : $checkIn->diffInDays($checkOut);

            // Recalcular Precios
            $accommodation = Accommodation::findOrFail($data['accommodation_id']);
            $prices = $pricingService->calculateStayTotal(
                $accommodation,
                $checkIn,
                $checkOut,
                $data['guests_count'],
                $data['pricing_type'] ?? null,
                $isDayPass
            );

            $data['nightly_subtotal'] = $prices['subtotal'];
            $data['rate_snapshot'] = $prices['snapshot'];
            $data['pricing_type'] = $prices['pricing_type'];
            $data['cleaning_fee'] = $data['cleaning_fee'] ?? $accommodation->cleaning_fee ?? 0;
            $data['security_deposit'] = $data['security_deposit'] ?? $accommodation->security_deposit ?? 0;
            $data['discount_total'] = $data['discount_total'] ?? 0;
            $data['tax_total'] = $data['tax_total'] ?? 0;
            $total = $prices['subtotal'] + $data['cleaning_fee'] + $data['security_deposit'] - $data['discount_total'] + $data['tax_total'];
            $data['total_amount'] = $total;

            $quote->update($data);

            DB::commit();
            return redirect()->route('quotes.show', $quote)->with('success', 'Cotización actualizada.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Quote $quote, Request $request)
    {
        $this->authorize('delete', $quote);
        $quote->delete();
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Cotización eliminada.']);
        }
        return redirect()->route('quotes.index')->with('success', 'Cotización eliminada.');
    }

    /**
     * Convertir una cotización en una Venta (Reserva)
     * Re-Chequea disponibilidad y preserva todos los montos de la cotización
     */
    public function convertToReservation(Request $request, Quote $quote, AvailabilityService $availabilityService)
    {
        $this->authorize('update', $quote);

        if ($quote->status === QuoteStatus::Converted || $quote->reservation_id) {
            return redirect()->route('quotes.show', $quote)->with('error', 'Esta cotización ya fue convertida.');
        }

        try {
            DB::beginTransaction();

            // 1. Verificar disponibilidad ACTUAL (el usuario pudo haber tomado la cotización hace días)
            $isAvailable = $availabilityService->isAccommodationAvailable(
                $quote->accommodation_id,
                $quote->check_in_date,
                $quote->check_out_date
            );

            if (!$isAvailable) {
                throw new \Exception('El alojamiento ya no se encuentra disponible para las fechas de la cotización.');
            }

            // 2. Crear la Reserva (Venta) copiando TODOS los snapshot financieros
            $reservationCode = 'RES-' . strtoupper(substr(uniqid(rand(), true), -6));
            
            $reservationData = [
                'code' => $reservationCode,
                'quote_id' => $quote->id,
                'accommodation_id' => $quote->accommodation_id,
                'pricing_type' => $quote->pricing_type,
                'primary_guest_id' => $quote->guest_id,
                'check_in_date' => $quote->check_in_date,
                'check_out_date' => $quote->check_out_date,
                'nights_count' => $quote->nights_count,
                'status' => ReservationStatus::Pending,
                'guests_count' => $quote->guests_count,
                'adults_count' => $quote->adults_count,
                'children_count' => $quote->children_count,
                'nightly_subtotal' => $quote->nightly_subtotal,
                'services_total' => $quote->services_total ?? 0,
                'discount_total' => $quote->discount_total ?? 0,
                'tax_total' => $quote->tax_total ?? 0,
                'cleaning_fee' => $quote->cleaning_fee ?? 0,
                'security_deposit' => $quote->security_deposit ?? 0,
                'total_amount' => $quote->total_amount,
                'rate_snapshot' => $quote->rate_snapshot,
                'guest_notes' => $quote->guest_notes,
                'internal_notes' => (is_string($quote->internal_notes) ? $quote->internal_notes : '') . "\n\n[System: Converted from Quote #{$quote->code} on " . now() . "]",
                'created_by' => auth()->id(),
                'source' => 'quote',
            ];
            
            // Merge conditional: solo agregar services_snapshot si el modelo lo soporta (para evitar MassAssignmentException)
            if (in_array('services_snapshot', (new Reservation())->getFillable())) {
                $reservationData['services_snapshot'] = $quote->services_snapshot;
            }

            $reservation = Reservation::create($reservationData);

            // 3. Actualizar la Cotización como Convertida
            $quote->update([
                'status' => QuoteStatus::Converted,
                'reservation_id' => $reservation->id,
            ]);

            // 4. Crear Status History
            if (class_exists(\App\Models\ReservationStatusHistory::class)) {
                \App\Models\ReservationStatusHistory::create([
                    'reservation_id' => $reservation->id,
                    'status' => ReservationStatus::Pending->value, // Columna 'status'
                    'old_status' => null, // Recién creado
                    'new_status' => ReservationStatus::Pending->value, // Columna requerida por la BD
                    'changed_by' => auth()->id(),
                    'notes' => 'Creado desde Cotización ' . $quote->code,
                ]);
            }

            DB::commit();
            
            // Flujo exitoso: Redirigir a Registrar Depósito para confirmar la reserva
            return redirect()
                ->route('payments.create', [
                    'reservation_id' => $reservation->id,
                    'guest_id'       => $reservation->primary_guest_id,
                    'payment_type'   => 'deposit',
                ])
                ->with('success', '¡Cotización convertida en Reserva <b>#' . $reservation->code . '</b>! Registra el depósito inicial para confirmarla.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'No se pudo convertir: ' . $e->getMessage());
        }
    }

    public function pdf(Quote $quote)
    {
        $this->authorize('view', $quote);
        $quote->load(['accommodation', 'guest', 'createdBy']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.quote', compact('quote'));
        
        return $pdf->stream("Cotizacion-{$quote->code}.pdf");
    }

    public function sendEmail(Request $request, Quote $quote)
    {
        $this->authorize('view', $quote);

        $request->validate([
            'email_recipient_type' => 'required|in:registered,custom',
            'custom_email' => 'required_if:email_recipient_type,custom|nullable|email',
            'custom_message' => 'nullable|string|max:1000',
        ]);

        $recipientEmail = $request->email_recipient_type === 'custom'
            ? $request->custom_email
            : $quote->guest?->email;

        if (!$recipientEmail) {
            return back()->with('error', 'El cliente no tiene un correo registrado. Por favor especifica un correo válido.');
        }

        try {
            $quote->load(['accommodation', 'guest', 'createdBy']);
            \Illuminate\Support\Facades\Mail::to($recipientEmail)->send(
                new \App\Mail\QuoteInvoiceMail($quote, $request->custom_message)
            );

            return back()->with('success', "Cotización enviada exitosamente a <b>{$recipientEmail}</b> con el PDF adjunto.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar el correo: ' . $e->getMessage());
        }
    }
}
