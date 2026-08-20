<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización {{ $quote->code }}</title>
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
            color: #c05a1e;
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
            border-bottom: 2px solid #c05a1e;
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
            background-color: #c05a1e;
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            padding: 8px 10px;
            border-radius: 4px;
        }
        .grand-total-row .total-label {
            color: #ffffff;
            text-transform: uppercase;
        }
        .grand-total-row .total-value {
            color: #ffffff;
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
                    <div class="doc-title">Cotización</div>
                    <div class="doc-code">#{{ $quote->code }}</div>
                    <div class="doc-meta">
                        <strong>Fecha Emisión:</strong> {{ $quote->created_at?->format('d/m/Y') ?? date('d/m/Y') }}<br>
                        @if($quote->expires_at)
                            <strong>Válida Hasta:</strong> {{ $quote->expires_at->format('d/m/Y') }}<br>
                        @endif
                        <strong>Estado:</strong> {{ $quote->status->label() }}
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
                <div class="section-title">Información del Cliente</div>
                <div class="info-card">
                    <table>
                        <tr>
                            <td class="label">Cliente:</td>
                            <td class="val"><strong>{{ $quote->guest?->full_name ?? 'Cliente General' }}</strong></td>
                        </tr>
                        @if($quote->guest?->document_number)
                        <tr>
                            <td class="label">Documento:</td>
                            <td class="val">{{ $quote->guest?->document_type }} {{ $quote->guest?->document_number }}</td>
                        </tr>
                        @endif
                        @if($quote->guest?->phone)
                        <tr>
                            <td class="label">Teléfono:</td>
                            <td class="val">{{ $quote->guest?->phone }}</td>
                        </tr>
                        @endif
                        @if($quote->guest?->email)
                        <tr>
                            <td class="label">Email:</td>
                            <td class="val">{{ $quote->guest?->email }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
            <td style="width: 2%;"></td>
            <!-- Datos de la Estancia -->
            <td style="width: 49%; vertical-align: top;">
                <div class="section-title">Detalles del Alojamiento</div>
                <div class="info-card">
                    <table>
                        <tr>
                            <td class="label">Alojamiento:</td>
                            <td class="val"><strong>{{ $quote->accommodation?->name ?? 'N/A' }}</strong> ({{ $quote->accommodation?->type?->label() ?? '' }})</td>
                        </tr>
                        <tr>
                            <td class="label">Modalidad:</td>
                            <td class="val">
                                @if($quote->is_day_pass)
                                    <span class="badge badge-warning">Pasadía (Uso Diurno)</span>
                                @else
                                    <span class="badge badge-primary">Hospedaje (Con Noches)</span>
                                @endif
                            </td>
                        </tr>
                        @if($quote->is_day_pass)
                        <tr>
                            <td class="label">Fecha:</td>
                            <td class="val"><strong>{{ $quote->check_in_date?->format('d/m/Y') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label">Horario:</td>
                            <td class="val">{{ $quote->accommodation?->day_pass_check_in_time ?? '08:00' }} - {{ $quote->accommodation?->day_pass_check_out_time ?? '17:00' }}</td>
                        </tr>
                        @else
                        <tr>
                            <td class="label">Fechas:</td>
                            <td class="val">{{ $quote->check_in_date?->format('d/m/Y') }} al {{ $quote->check_out_date?->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Noches:</td>
                            <td class="val"><strong>{{ $quote->nights_count }} noche(s)</strong></td>
                        </tr>
                        @endif
                        <tr>
                            <td class="label">Huéspedes:</td>
                            <td class="val">
                                <strong>{{ $quote->guests_count }} personas</strong> ({{ $quote->adults_count }} adultos @if($quote->children_count > 0), {{ $quote->children_count }} niños @endif)
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Forma Cobro:</td>
                            <td class="val">{{ $quote->pricing_type?->label() ?? 'Por Alojamiento' }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabla de Liquidación -->
    <div class="section-title">Detalle y Liquidación de Tarifas</div>
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
                $rateSnapshot = is_array($quote->rate_snapshot) ? $quote->rate_snapshot : [];
                $snapshotNights = $rateSnapshot['nights'] ?? [];
                $hasNightBreakdown = is_array($snapshotNights) && count($snapshotNights) > 0;
            @endphp

            @if($quote->is_day_pass && !empty($rateSnapshot) && isset($rateSnapshot['day_pass_date']))
                @php
                    $dpTotal = (float) ($rateSnapshot['applied_price'] ?? $quote->nightly_subtotal);
                    $dpUnit = $quote->pricing_type?->value === 'per_person'
                        ? $dpTotal / max(1, $quote->guests_count)
                        : $dpTotal;
                    $dpLabels = collect($rateSnapshot['adjustments'] ?? [])
                        ->map(fn ($a) => $a['label'] ?? $a['name'] ?? '')
                        ->filter()
                        ->implode(' · ');
                @endphp
                <tr>
                    <td>
                        <strong>Pasadía en {{ $quote->accommodation?->name }}</strong>
                        <br>
                        <small style="color: #64748b;">{{ $quote->check_in_date?->format('d/m/Y') }} · {{ $quote->pricing_type?->label() }}</small>
                        @if($dpLabels)
                            <br><small style="color: #92400e;">Modificadores: {{ $dpLabels }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($quote->pricing_type?->value === 'per_person')
                            {{ $quote->guests_count }} pax
                        @else
                            1 día
                        @endif
                    </td>
                    <td class="text-end">${{ number_format($dpUnit, 2) }}</td>
                    <td class="text-end fw-bold">${{ number_format($quote->nightly_subtotal, 2) }}</td>
                </tr>
            @elseif($hasNightBreakdown)
                @foreach($snapshotNights as $nightDate => $night)
                    @php
                        $d = \Illuminate\Support\Carbon::parse($nightDate);
                        $nightTotal = (float) ($night['applied_price'] ?? 0);
                        $nightUnit = $quote->pricing_type?->value === 'per_person'
                            ? $nightTotal / max(1, $quote->guests_count)
                            : $nightTotal;
                        $nightLabels = collect($night['adjustments'] ?? [])
                            ->map(fn ($a) => $a['label'] ?? $a['name'] ?? '')
                            ->filter()
                            ->implode(' · ');
                    @endphp
                    <tr>
                        <td>
                            <strong>Noche del {{ $d->format('d/m/Y') }}</strong>
                            <br>
                            <small style="color: #64748b;">{{ $quote->accommodation?->name }} · {{ $quote->pricing_type?->label() }}</small>
                            @if($nightLabels)
                                <br><small style="color: #92400e;">Modificadores: {{ $nightLabels }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($quote->pricing_type?->value === 'per_person')
                                {{ $quote->guests_count }} pax
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
                        @if($quote->is_day_pass)
                            Pasadía en {{ $quote->accommodation?->name }}
                        @else
                            Estancia en {{ $quote->accommodation?->name }}
                        @endif
                    </strong>
                    <br>
                    <small style="color: #64748b;">
                        @if($quote->is_day_pass)
                            Modalidad Pasadía ({{ $quote->pricing_type?->label() }})
                        @else
                            {{ $quote->nights_count }} noche(s) · {{ $quote->pricing_type?->label() }}
                        @endif
                    </small>
                </td>
                <td class="text-center">
                    @if($quote->pricing_type?->value === 'per_person')
                        {{ $quote->guests_count }} pax
                    @else
                        {{ $quote->is_day_pass ? '1 día' : $quote->nights_count . ' noche(s)' }}
                    @endif
                </td>
                <td class="text-end">
                    @php
                        $unitPrice = 0;
                        if ($quote->pricing_type?->value === 'per_person') {
                            $factor = $quote->is_day_pass ? $quote->guests_count : max(1, $quote->nights_count * $quote->guests_count);
                            $unitPrice = $factor > 0 ? ($quote->nightly_subtotal / $factor) : $quote->nightly_subtotal;
                        } else {
                            $factor = $quote->is_day_pass ? 1 : max(1, $quote->nights_count);
                            $unitPrice = $factor > 0 ? ($quote->nightly_subtotal / $factor) : $quote->nightly_subtotal;
                        }
                    @endphp
                    ${{ number_format($unitPrice, 2) }}
                </td>
                <td class="text-end fw-bold">
                    ${{ number_format($quote->nightly_subtotal, 2) }}
                </td>
            </tr>
            @endif
            @if($quote->cleaning_fee > 0)
            <tr>
                <td>Tarifa de Limpieza / Aseo</td>
                <td class="text-center">1</td>
                <td class="text-end">${{ number_format($quote->cleaning_fee, 2) }}</td>
                <td class="text-end">${{ number_format($quote->cleaning_fee, 2) }}</td>
            </tr>
            @endif
            @if($quote->services_total > 0)
            <tr>
                <td>Servicios Adicionales</td>
                <td class="text-center">-</td>
                <td class="text-end">${{ number_format($quote->services_total, 2) }}</td>
                <td class="text-end">${{ number_format($quote->services_total, 2) }}</td>
            </tr>
            @endif
            @if($quote->security_deposit > 0)
            <tr>
                <td>Depósito de Seguridad Reembolsable</td>
                <td class="text-center">1</td>
                <td class="text-end">${{ number_format($quote->security_deposit, 2) }}</td>
                <td class="text-end">${{ number_format($quote->security_deposit, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Totales y Resumen -->
    <table>
        <tr>
            <td style="width: 55%; vertical-align: top;">
                @if($quote->guest_notes)
                    <div class="box-note">
                        <strong>Notas / Observaciones:</strong><br>
                        {{ $quote->guest_notes }}
                    </div>
                @endif

                @if(setting('empresa_banco_info'))
                    <div class="box-bank">
                        <strong>Información para Pagos y Transferencias:</strong><br>
                        {!! nl2br(e(setting('empresa_banco_info'))) !!}
                    </div>
                @endif
            </td>
            <td style="width: 5%;"></td>
            <td style="width: 40%; vertical-align: top;">
                <table class="totals-table">
                    <tr>
                        <td class="total-label">Subtotal:</td>
                        <td class="total-value">${{ number_format($quote->nightly_subtotal + $quote->cleaning_fee + $quote->services_total, 2) }}</td>
                    </tr>
                    @if($quote->discount_total > 0)
                    <tr>
                        <td class="total-label" style="color: #15803d;">Descuento:</td>
                        <td class="total-value" style="color: #15803d;">-${{ number_format($quote->discount_total, 2) }}</td>
                    </tr>
                    @endif
                    @if($quote->tax_total > 0)
                    <tr>
                        <td class="total-label">Impuestos (IVA):</td>
                        <td class="total-value">${{ number_format($quote->tax_total, 2) }}</td>
                    </tr>
                    @endif
                    @if($quote->security_deposit > 0)
                    <tr>
                        <td class="total-label">Depósito Garantía:</td>
                        <td class="total-value">${{ number_format($quote->security_deposit, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td class="total-label">TOTAL COTIZADO:</td>
                        <td class="total-value">${{ number_format($quote->total_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        @if(setting('empresa_pie_pagina'))
            <div><strong>{{ setting('empresa_pie_pagina') }}</strong></div>
        @else
            <div>¡Gracias por cotizar con nosotros! Cotización sujeta a disponibilidad al momento de confirmar.</div>
        @endif
        <div style="margin-top: 4px; color: #94a3b8; font-size: 8.5px;">
            Generado por {{ config('app.name', 'Aloja360') }} el {{ date('d/m/Y H:i') }}
        </div>
    </div>

</body>
</html>
