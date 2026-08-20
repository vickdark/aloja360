{{--
    Desglose por noche de la tarifa guardada en rate_snapshot.
    Muestra los modificadores (temporadas/fin de semana/festivos) que aplicaron
    a cada noche, tal como fueron calculados por PricingService.
    Props: snapshot (array, como se guardó en Quote/Reservation->rate_snapshot)
--}}
@props(['snapshot' => null])

@php
    $snapshot = is_array($snapshot) ? $snapshot : null;
    $weekdays = [
        'monday' => 'Lunes',
        'tuesday' => 'Martes',
        'wednesday' => 'Miércoles',
        'thursday' => 'Jueves',
        'friday' => 'Viernes',
        'saturday' => 'Sábado',
        'sunday' => 'Domingo',
    ];
@endphp

@if($snapshot)
    @if(($snapshot['is_day_pass'] ?? false) || !is_array($snapshot['nights'] ?? null))
        @php
            $applied = (float) ($snapshot['applied_price'] ?? 0);
            $labels = collect($snapshot['adjustments'] ?? [])
                ->map(fn ($a) => $a['label'] ?? $a['name'] ?? '')
                ->filter()
                ->implode(' · ');
        @endphp
        @if($applied > 0)
            <div class="d-flex justify-content-between gap-2 py-1 small">
                <span class="text-muted">Pasadía</span>
                <span class="fw-semibold">${{ number_format($applied, 2) }}</span>
            </div>
            @if($labels)
                <div class="text-warning small fst-italic py-1">{{ $labels }}</div>
            @endif
        @endif
    @else
        @foreach($snapshot['nights'] as $date => $night)
            @php
                $d = \Illuminate\Support\Carbon::parse($date);
                $labels = collect($night['adjustments'] ?? [])
                    ->map(fn ($a) => $a['label'] ?? $a['name'] ?? '')
                    ->filter()
                    ->implode(' · ');
                $price = (float) ($night['applied_price'] ?? 0);
                $base  = (float) ($night['base_price_applied'] ?? 0);
            @endphp
            <div class="d-flex justify-content-between align-items-center gap-2 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div>
                    <div class="fw-semibold small">
                        {{ $weekdays[$night['weekday'] ?? ''] ?? '' }} {{ $d->format('d/m/Y') }}
                    </div>
                    @if($labels)
                        <div class="text-warning small fst-italic">{{ $labels }}</div>
                    @endif
                </div>
                <div class="text-end small text-nowrap">
                    <div class="fw-semibold">${{ number_format($price, 2) }}</div>
                    <div class="text-muted" style="font-size: .72rem;">base ${{ number_format($base, 2) }}</div>
                </div>
            </div>
        @endforeach
    @endif
@endif