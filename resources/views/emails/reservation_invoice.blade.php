<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Reserva #{{ $reservation->code }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333333; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: #0284c7; color: #ffffff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: bold; }
        .content { padding: 24px; }
        .badge { display: inline-block; background: #e0f2fe; color: #0369a1; font-weight: bold; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .card-info { background: #f8fafc; border: 1px solid #edf2f7; border-radius: 6px; padding: 16px; margin: 16px 0; }
        .card-info table { width: 100%; border-collapse: collapse; }
        .card-info td { padding: 4px 0; font-size: 13px; }
        .total-box { background: #1e293b; color: #ffffff; padding: 14px 20px; border-radius: 6px; text-align: center; margin: 20px 0; }
        .total-box .amount { font-size: 24px; font-weight: bold; color: #38bdf8; }
        .balance-info { display: flex; justify-content: space-between; font-size: 13px; margin-top: 8px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 8px; }
        .custom-msg { background: #fefce8; border-left: 4px solid #eab308; padding: 12px 16px; margin: 16px 0; font-size: 13px; color: #713f12; }
        .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ setting('empresa_nombre', config('app.name', 'Aloja360')) }}</h1>
            <p style="margin: 4px 0 0; opacity: 0.9; font-size: 14px;">Comprobante de Reserva #{{ $reservation->code }}</p>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $reservation->primaryGuest?->full_name ?? 'Huésped' }}</strong>,</p>
            <p>Te enviamos el comprobante y detalle de tu reserva en nuestras instalaciones:</p>

            @if($customMessage)
                <div class="custom-msg">
                    <strong>Mensaje:</strong><br>
                    {!! nl2br(e($customMessage)) !!}
                </div>
            @endif

            <div class="card-info">
                <table>
                    <tr>
                        <td style="color: #64748b; width: 40%;">Alojamiento:</td>
                        <td><strong>{{ $reservation->accommodation?->name }}</strong></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Estado Reserva:</td>
                        <td><span class="badge">{{ $reservation->status->label() }}</span></td>
                    </tr>
                    @if($reservation->is_day_pass)
                    <tr>
                        <td style="color: #64748b;">Fecha Pasadía:</td>
                        <td><strong>{{ $reservation->check_in_date?->format('d/m/Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Horario:</td>
                        <td>{{ $reservation->check_in_time ?? '08:00' }} - {{ $reservation->check_out_time ?? '17:00' }}</td>
                    </tr>
                    @else
                    <tr>
                        <td style="color: #64748b;">Fechas:</td>
                        <td>{{ $reservation->check_in_date?->format('d/m/Y') }} al {{ $reservation->check_out_date?->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Duración:</td>
                        <td>{{ $reservation->nights_count }} noche(s)</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="color: #64748b;">Huéspedes:</td>
                        <td>{{ $reservation->guests_count }} personas</td>
                    </tr>
                </table>
            </div>

            <div class="total-box">
                <div style="font-size: 12px; opacity: 0.8; text-transform: uppercase;">Total Estancia</div>
                <div class="amount">${{ number_format($reservation->total_amount, 0) }}</div>
                @if($reservation->outstanding_balance > 0)
                    <div style="font-size: 13px; color: #fca5a5; margin-top: 6px;">
                        Saldo Pendiente: ${{ number_format($reservation->outstanding_balance, 0) }}
                    </div>
                @else
                    <div style="font-size: 13px; color: #86efac; margin-top: 6px;">
                        ✓ Totalmente Pagado
                    </div>
                @endif
            </div>

            <p style="font-size: 13px; color: #475569;">
                📎 Adjunto encontrarás el comprobante de reserva oficial en formato <strong>PDF</strong> con la liquidación y registro de pagos.
            </p>

            @if(setting('empresa_telefono') || setting('empresa_email'))
                <p style="font-size: 12px; color: #64748b; margin-top: 20px;">
                    Para cualquier inquietud o solicitud especial, contáctanos al 
                    <strong>{{ setting('empresa_telefono') }}</strong> o responde a este correo.
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
