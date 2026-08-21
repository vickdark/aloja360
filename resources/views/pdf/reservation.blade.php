<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva {{ $reservation->code }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 2px;
        }
        .company-details {
            font-size: 10px;
            color: #666666;
            line-height: 1.3;
        }
        .doc-title-box {
            background-color: #f4f6f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            text-align: right;
        }
        .doc-title {
            font-size: 16px;
            font-weight: bold;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .doc-code {
            font-size: 13px;
            font-weight: bold;
            color: #2d3748;
        }
        .doc-meta {
            font-size: 10px;
            color: #718096;
            margin-top: 4px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #2d3748;
            text-transform: uppercase;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 4px;
            margin-top: 15px;
            margin-bottom: 8px;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 8px;
        }
        .info-card td {
            vertical-align: top;
            padding: 2px 4px;
            font-size: 10.5px;
        }
        .label {
            font-weight: bold;
            color: #4a5568;
            width: 32%;
        }
        .val {
            color: #1a202c;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 12px;
            text-transform: uppercase;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }
        .badge-primary {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .items-table {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 12px;
        }
        .items-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 8px;
            text-align: left;
        }
        .items-table th.text-end, .items-table td.text-end {
            text-align: right;
        }
        .items-table th.text-center, .items-table td.text-center {
            text-align: center;
        }
        .items-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10.5px;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .totals-table {
            width: 100%;
            margin-top: 5px;
        }
        .totals-table td {
            padding: 3px 8px;
            font-size: 10.5px;
        }
        .totals-table .total-label {
            text-align: right;
            color: #4a5568;
            font-weight: 500;
        }
        .totals-table .total-value {
            text-align: right;
            font-weight: bold;
            color: #1a202c;
            width: 120px;
        }
        .grand-total-row td {
            background-color: #0284c7;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            padding: 6px 10px;
        }
        .grand-total-row .total-label {
            color: #ffffff;
            text-transform: uppercase;
        }
        .grand-total-row .total-value {
            color: #ffffff;
        }
        .balance-due-row td {
            background-color: #fef2f2;
            color: #991b1b;
            font-size: 12px;
            font-weight: bold;
            padding: 6px 10px;
            border: 1px solid #fecaca;
        }
        .balance-paid-row td {
            background-color: #f0fdf4;
            color: #166534;
            font-size: 12px;
            font-weight: bold;
            padding: 6px 10px;
            border: 1px solid #bbf7d0;
        }
        .box-note {
            background-color: #fefce8;
            border-left: 3px solid #eab308;
            padding: 8px 12px;
            font-size: 10px;
            color: #713f12;
            margin-top: 10px;
            border-radius: 0 4px 4px 0;
        }
        .box-bank {
            background-color: #f0fdf4;
            border-left: 3px solid #22c55e;
            padding: 8px 12px;
            font-size: 10px;
            color: #14532d;
            margin-top: 10px;
            border-radius: 0 4px 4px 0;
        }
        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px dashed #cbd5e1;
            text-align: center;
            font-size: 9.5px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-name">{{ setting('empresa_nombre', config('app.name', 'Aloja360')) }}</div>
                <div class="company-details">
                    @if(setting('empresa_id_fiscal'))
                        <strong>NIT/RUT:</strong> {{ setting('empresa_id_fiscal') }}<br>
                    @endif
                    @if(setting('empresa_direccion'))
                        {{ setting('empresa_direccion') }}@if(setting('empresa_ciudad')), {{ setting('empresa_ciudad') }}@endif<br>
                    @endif
                    @if(setting('empresa_telefono'))
                        <strong>Tel:</strong> {{ setting('empresa_telefono') }}
                    @endif
                    @if(setting('empresa_email'))
                        | <strong>Email:</strong> {{ setting('empresa_email') }}<br>
                    @endif
                    @if(setting('empresa_web'))
                        <strong>Web:</strong> {{ setting('empresa_web') }}<br>
                    @endif
                    @if(setting('empresa_regimen'))
                        <small style="color:#888;">{{ setting('empresa_regimen') }}</small>
                    @endif
                </div>
            </td>
            <td style="width: 45%;">
                <div class="doc-title-box">
                    <div class="doc-title">Comprobante de Reserva</div>
                    <div class="doc-code">#{{ $reservation->code }}</div>
                    <div class="doc-meta">
                        <strong>Fecha Emisión:</strong> {{ $reservation->created_at?->format('d/m/Y') ?? date('d/m/Y') }}<br>
                        <strong>Estado:</strong> {{ $reservation->status->label() }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Cliente y Alojamiento -->
    <table style="margin-top: 12px;">
        <tr>
            <!-- Datos del Cliente -->
            <td style="width: 49%; vertical-align: top;">
                <div class="section-title">Huésped Principal</div>
                <div class="info-card">
                    <table>
                        <tr>
                            <td class="label">Nombre:</td>
                            <td class="val"><strong>{{ $reservation->primaryGuest?->full_name ?? 'Huésped General' }}</strong></td>
                        </tr>
                        @if($reservation->primaryGuest?->document_number)
                        <tr>
                            <td class="label">Documento:</td>
                            <td class="val">{{ $reservation->primaryGuest?->document_type }} {{ $reservation->primaryGuest?->document_number }}</td>
                        </tr>
                        @endif
                        @if($reservation->primaryGuest?->phone)
                        <tr>
                            <td class="label">Teléfono:</td>
                            <td class="val">{{ $reservation->primaryGuest?->phone }}</td>
                        </tr>
                        @endif
                        @if($reservation->primaryGuest?->email)
                        <tr>
                            <td class="label">Email:</td>
                            <td class="val">{{ $reservation->primaryGuest?->email }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
            <td style="width: 2%;"></td>
            <!-- Datos de la Estancia -->
            <td style="width: 49%; vertical-align: top;">
                <div class="section-title">Detalles de la Reserva</div>
                <div class="info-card">
                    <table>
                        <tr>
                            <td class="label">Alojamiento:</td>
                            <td class="val"><strong>{{ $reservation->accommodation?->name ?? 'N/A' }}</strong> ({{ $reservation->accommodation?->type?->label() ?? '' }})</td>
                        </tr>
                        <tr>
                            <td class="label">Modalidad:</td>
                            <td class="val">
                                @if($reservation->is_day_pass)
                                    <span class="badge badge-warning">Pasadía (Uso Diurno)</span>
                                @else
                                    <span class="badge badge-primary">Hospedaje (Con Noches)</span>
                                @endif
                            </td>
                        </tr>
                        @if($reservation->is_day_pass)
                        <tr>
                            <td class="label">Fecha:</td>
                            <td class="val"><strong>{{ $reservation->check_in_date?->format('d/m/Y') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label">Horario:</td>
                            <td class="val">{{ $reservation->check_in_time ?? $reservation->accommodation?->day_pass_check_in_time ?? '08:00' }} - {{ $reservation->check_out_time ?? $reservation->accommodation?->day_pass_check_out_time ?? '17:00' }}</td>
                        </tr>
                        @else
                        <tr>
                            <td class="label">Fechas:</td>
                            <td class="val">{{ $reservation->check_in_date?->format('d/m/Y') }} al {{ $reservation->check_out_date?->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Check-In / Out:</td>
                            <td class="val">{{ $reservation->check_in_time ?? '15:00' }} / {{ $reservation->check_out_time ?? '11:00' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Noches:</td>
                            <td class="val"><strong>{{ $reservation->nights_count }} noche(s)</strong></td>
                        </tr>
                        @endif
                        <tr>
                            <td class="label">Huéspedes:</td>
                            <td class="val">
                                <strong>{{ $reservation->guests_count }} personas</strong> ({{ $reservation->adults_count }} adultos @if($reservation->children_count > 0), {{ $reservation->children_count }} niños @endif)
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Forma Cobro:</td>
                            <td class="val">{{ $reservation->pricing_type?->label() ?? 'Por Alojamiento' }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabla de Liquidación -->
    <div class="section-title">Detalle y Conceptos Facturados</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 45%;">Concepto / Descripción</th>
                <th class="text-center" style="width: 15%;">Cantidad / Pax</th>
                <th class="text-end" style="width: 20%;">Tarifa Unitaria</th>
                <th class="text-end" style="width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rateSnapshot = is_array($reservation->rate_snapshot) ? $reservation->rate_snapshot : [];
                $snapshotNights = $rateSnapshot['nights'] ?? [];
                $hasNightBreakdown = is_array($snapshotNights) && count($snapshotNights) > 0;
            @endphp

            @if($reservation->is_day_pass && !empty($rateSnapshot) && isset($rateSnapshot['day_pass_date']))
                @php
                    $dpTotal = (float) ($rateSnapshot['applied_price'] ?? $reservation->nightly_subtotal);
                    $dpUnit = $reservation->pricing_type?->value === 'per_person'
                        ? $dpTotal / max(1, $reservation->guests_count)
                        : $dpTotal;
                @endphp
                <tr>
                    <td>
                        <strong>Pasadía en {{ $reservation->accommodation?->name }}</strong>
                        <br>
                        <small style="color: #64748b;">{{ $reservation->check_in_date?->format('d/m/Y') }} · {{ $reservation->pricing_type?->label() }}</small>
                    </td>
                    <td class="text-center">
                        @if($reservation->pricing_type?->value === 'per_person')
                            {{ $reservation->guests_count }} pax
                        @else
                            1 día
                        @endif
                    </td>
                    <td class="text-end">${{ number_format($dpUnit, 2) }}</td>
                    <td class="text-end fw-bold">${{ number_format($reservation->nightly_subtotal, 2) }}</td>
                </tr>
            @elseif($hasNightBreakdown)
                @foreach($snapshotNights as $nightDate => $night)
                    @php
                        $d = \Illuminate\Support\Carbon::parse($nightDate);
                        $nightTotal = (float) ($night['applied_price'] ?? 0);
                        $nightUnit = $reservation->pricing_type?->value === 'per_person'
                            ? $nightTotal / max(1, $reservation->guests_count)
                            : $nightTotal;
                    @endphp
                    <tr>
                        <td>
                            <strong>Noche del {{ $d->format('d/m/Y') }}</strong>
                            <br>
                            <small style="color: #64748b;">{{ $reservation->accommodation?->name }} · {{ $reservation->pricing_type?->label() }}</small>
                        </td>
                        <td class="text-center">
                            @if($reservation->pricing_type?->value === 'per_person')
                                {{ $reservation->guests_count }} pax
                            @else
                                1 noche
                            @endif
                        </td>
                        <td class="text-end">${{ number_format($nightUnit, 2) }}</td>
                        <td class="text-end fw-bold">${{ number_format($nightTotal, 2) }}</td>
                    </tr>
                @endforeach
            @else
            <tr>
                <td>
                    <strong>
                        @if($reservation->is_day_pass)
                            Pasadía en {{ $reservation->accommodation?->name }}
                        @else
                            Estancia en {{ $reservation->accommodation?->name }}
                        @endif
                    </strong>
                    <br>
                    <small style="color: #64748b;">
                        @if($reservation->is_day_pass)
                            Modalidad Pasadía ({{ $reservation->pricing_type?->label() }})
                        @else
                            {{ $reservation->nights_count }} noche(s) · {{ $reservation->pricing_type?->label() }}
                        @endif
                    </small>
                </td>
                <td class="text-center">
                    @if($reservation->pricing_type?->value === 'per_person')
                        {{ $reservation->guests_count }} pax
                    @else
                        {{ $reservation->is_day_pass ? '1 día' : $reservation->nights_count . ' noche(s)' }}
                    @endif
                </td>
                <td class="text-end">
                    @php
                        $unitPrice = 0;
                        if ($reservation->pricing_type?->value === 'per_person') {
                            $factor = $reservation->is_day_pass ? $reservation->guests_count : max(1, $reservation->nights_count * $reservation->guests_count);
                            $unitPrice = $factor > 0 ? ($reservation->nightly_subtotal / $factor) : $reservation->nightly_subtotal;
                        } else {
                            $factor = $reservation->is_day_pass ? 1 : max(1, $reservation->nights_count);
                            $unitPrice = $factor > 0 ? ($reservation->nightly_subtotal / $factor) : $reservation->nightly_subtotal;
                        }
                    @endphp
                    ${{ number_format($unitPrice, 2) }}
                </td>
                <td class="text-end fw-bold">
                    ${{ number_format($reservation->nightly_subtotal, 2) }}
                </td>
            </tr>
            @endif
            @if($reservation->services_total > 0)
            <tr>
                <td>Servicios Adicionales</td>
                <td class="text-center">-</td>
                <td class="text-end">${{ number_format($reservation->services_total, 2) }}</td>
                <td class="text-end">${{ number_format($reservation->services_total, 2) }}</td>
            </tr>
            @endif
            @if($reservation->security_deposit > 0)
            <tr>
                <td>Depósito de Seguridad Reembolsable</td>
                <td class="text-center">1</td>
                <td class="text-end">${{ number_format($reservation->security_deposit, 2) }}</td>
                <td class="text-end">${{ number_format($reservation->security_deposit, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Totales y Pagos -->
    <table>
        <tr>
            <td style="width: 55%; vertical-align: top;">
                @if($reservation->confirmedPayments->count() > 0)
                    <div style="font-weight: bold; font-size: 10px; color: #166534; text-transform: uppercase; margin-bottom: 4px;">
                        Pagos Confirmados Recibidos:
                    </div>
                    <table style="width: 100%; border: 1px solid #dcfce7; font-size: 9.5px; background: #f0fdf4; border-radius: 4px; margin-bottom: 8px;">
                        @foreach($reservation->confirmedPayments as $payment)
                            <tr>
                                <td style="padding: 3px 6px; border-bottom: 1px solid #dcfce7;">{{ $payment->created_at->format('d/m/Y') }}</td>
                                <td style="padding: 3px 6px; border-bottom: 1px solid #dcfce7;">{{ $payment->payment_method?->label() ?? 'Pago' }}</td>
                                <td style="padding: 3px 6px; border-bottom: 1px solid #dcfce7; color: #666;">Ref: {{ $payment->reference ?? '-' }}</td>
                                <td style="padding: 3px 6px; border-bottom: 1px solid #dcfce7; text-align: right; font-weight: bold;">${{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif

                @if($reservation->guest_notes)
                    <div class="box-note">
                        <strong>Notas / Solicitudes:</strong><br>
                        {{ $reservation->guest_notes }}
                    </div>
                @endif

                @if(setting('empresa_banco_info') && $reservation->outstanding_balance > 0)
                    <div class="box-bank">
                        <strong>Información para Pagos / Saldo Pendiente:</strong><br>
                        {!! nl2br(e(setting('empresa_banco_info'))) !!}
                    </div>
                @endif
            </td>
            <td style="width: 5%;"></td>
            <td style="width: 40%; vertical-align: top;">
                <table class="totals-table">
                    <tr>
                        <td class="total-label">Subtotal:</td>
                        <td class="total-value">${{ number_format($reservation->nightly_subtotal + $reservation->services_total, 2) }}</td>
                    </tr>
                    @if($reservation->discount_total > 0)
                    <tr>
                        <td class="total-label" style="color: #15803d;">Descuento:</td>
                        <td class="total-value" style="color: #15803d;">-${{ number_format($reservation->discount_total, 2) }}</td>
                    </tr>
                    @endif
                    @if($reservation->tax_total > 0)
                    <tr>
                        <td class="total-label">Impuestos (IVA):</td>
                        <td class="total-value">${{ number_format($reservation->tax_total, 2) }}</td>
                    </tr>
                    @endif
                    @if($reservation->security_deposit > 0)
                    <tr>
                        <td class="total-label">Depósito Garantía:</td>
                        <td class="total-value">${{ number_format($reservation->security_deposit, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td class="total-label">TOTAL ESTANCIA:</td>
                        <td class="total-value">${{ number_format($reservation->total_amount, 2) }}</td>
                    </tr>
                    @php
                        $paidAmount = $reservation->confirmedPayments()->sum('amount');
                    @endphp
                    <tr>
                        <td class="total-label" style="color: #166534;">Abonos / Pagos:</td>
                        <td class="total-value" style="color: #166534;">-${{ number_format($paidAmount, 2) }}</td>
                    </tr>
                    @if($reservation->outstanding_balance > 0)
                        <tr class="balance-due-row">
                            <td class="total-label">SALDO PENDIENTE:</td>
                            <td class="total-value">${{ number_format($reservation->outstanding_balance, 2) }}</td>
                        </tr>
                    @else
                        <tr class="balance-paid-row">
                            <td class="total-label">ESTADO DE CUENTA:</td>
                            <td class="total-value">PAGADO TOTAL</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        @if(setting('empresa_pie_pagina'))
            <div><strong>{{ setting('empresa_pie_pagina') }}</strong></div>
        @else
            <div>¡Gracias por su estadía! Es un placer atenderle.</div>
        @endif
        <div style="margin-top: 4px; color: #94a3b8; font-size: 8.5px;">
            Generado por {{ config('app.name', 'Aloja360') }} el {{ date('d/m/Y H:i') }}
        </div>
    </div>

</body>
</html>
