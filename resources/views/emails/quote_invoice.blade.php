<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización #{{ $quote->code }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333333; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: #c05a1e; color: #ffffff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: bold; }
        .content { padding: 24px; }
        .badge { display: inline-block; background: #fff3ed; color: #c05a1e; font-weight: bold; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .card-info { background: #f8fafc; border: 1px solid #edf2f7; border-radius: 6px; padding: 16px; margin: 16px 0; }
        .card-info table { width: 100%; border-collapse: collapse; }
        .card-info td { padding: 4px 0; font-size: 13px; }
        .total-box { background: #1e293b; color: #ffffff; padding: 14px 20px; border-radius: 6px; text-align: center; margin: 20px 0; }
        .total-box .amount { font-size: 24px; font-weight: bold; color: #38bdf8; }
        .custom-msg { background: #fefce8; border-left: 4px solid #eab308; padding: 12px 16px; margin: 16px 0; font-size: 13px; color: #713f12; }
        .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ setting('empresa_nombre', config('app.name', 'Aloja360')) }}</h1>
            <p style="margin: 4px 0 0; opacity: 0.9; font-size: 14px;">Cotización de Servicio #{{ $quote->code }}</p>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $quote->guest?->full_name ?? 'Cliente' }}</strong>,</p>
            <p>Esperamos que te encuentres muy bien. Te compartimos la cotización solicitada para tu estadía:</p>

            @if($customMessage)
                <div class="custom-msg">
                    <strong>Mensaje del equipo:</strong><br>
                    {!! nl2br(e($customMessage)) !!}
                </div>
            @endif

            <div class="card-info">
                <table>
                    <tr>
                        <td style="color: #64748b; width: 40%;">Alojamiento:</td>
                        <td><strong>{{ $quote->accommodation?->name }}</strong></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Modalidad:</td>
                        <td>
                            @if($quote->is_day_pass)
                                <span class="badge">Pasadía (Uso Diurno)</span>
                            @else
                                <span class="badge">Hospedaje ({{ $quote->nights_count }} Noches)</span>
                            @endif
                        </td>
                    </tr>
                    @if($quote->is_day_pass)
                    <tr>
                        <td style="color: #64748b;">Fecha Pasadía:</td>
                        <td><strong>{{ $quote->check_in_date?->format('d/m/Y') }}</strong></td>
                    </tr>
                    @else
                    <tr>
                        <td style="color: #64748b;">Fechas de Estancia:</td>
                        <td>{{ $quote->check_in_date?->format('d/m/Y') }} al {{ $quote->check_out_date?->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="color: #64748b;">Huéspedes:</td>
                        <td>{{ $quote->guests_count }} personas</td>
                    </tr>
                </table>
            </div>

            <div class="total-box">
                <div style="font-size: 12px; opacity: 0.8; text-transform: uppercase;">Total Cotizado</div>
                <div class="amount">${{ number_format($quote->total_amount, 0) }}</div>
            </div>

            <p style="font-size: 13px; color: #475569;">
                📎 Hemos adjuntado el documento oficial en <strong>PDF</strong> con el detalle tarifario completo y nuestras cuentas para pago.
            </p>

            @if(setting('empresa_telefono') || setting('empresa_email'))
                <p style="font-size: 12px; color: #64748b; margin-top: 20px;">
                    Si tienes dudas o deseas confirmar tu reserva, comunícate con nosotros al 
                    <strong>{{ setting('empresa_telefono') }}</strong> o respondiendo a este correo.
                </p>
            @endif
        </div>
        <div class="footer">
            {{ setting('empresa_nombre', config('app.name', 'Aloja360')) }} · {{ setting('empresa_direccion') }}<br>
            {{ setting('empresa_pie_pagina') }}
        </div>
    </div>
</body>
</html>
