@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0">
            @if($ratePeriod->status == 'active')
                <span class="badge text-bg-success rounded-pill fs-6 me-2">ACTIVA</span>
            @else
                <span class="badge text-bg-secondary rounded-pill fs-6 me-2">INACTIVA</span>
            @endif
            {{ $ratePeriod->name }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('rate_periods.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('rate_periods.edit', $ratePeriod) }}" class="btn btn-warning rounded-pill px-4 shadow-sm">
                <i class="fas fa-pen-to-square me-2"></i> Modificar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            @php
                $acc = $ratePeriod->accommodation;
                $baseAloj = $acc->base_price ?? 0;
                $baseAdult = $acc->price_per_person ?? 0;
                $baseChild = $acc->price_per_child ?? $baseAdult;
                $dpBase = $acc->day_pass_base_price ?? $baseAloj;
                $dpAdult = $acc->day_pass_price_per_person ?? $baseAdult;
                $dpChild = $acc->day_pass_price_per_child ?? $baseChild;
                // Ajustes
                $hasAcc = $ratePeriod->hasAccommodationAdjustment();
                $hasChild = $ratePeriod->hasChildAdjustment();
                $accAdjVal = $ratePeriod->accommodationEffectiveValue();
                $isAccPct = $ratePeriod->isAccommodationPercentage();
                $adultVal = $ratePeriod->effectiveValue();
                $isAdultPct = $ratePeriod->isPercentage();
                $childVal = $ratePeriod->childEffectiveValue();
                $isChildPct = $ratePeriod->isChildPercentage();
                $calc = function($base,$isPct,$val){ if(!$val) return $base; return $isPct ? round($base*(1+$val/100)) : $base+$val; };
                $adjAloj = $calc($baseAloj, $isAccPct, $accAdjVal);
                $adjAdult = $calc($baseAdult, $isAdultPct, $adultVal);
                $adjChild = $hasChild ? $calc($baseChild, $isChildPct, $childVal) : $calc($baseChild, $isAdultPct, $adultVal);
                if(!$hasAcc){ $adjAloj = $calc($baseAloj, $isAdultPct, $adultVal); }
            @endphp
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="text-uppercase small fw-bold opacity-75 mb-0">Alojamiento</h6>
                        <span class="badge bg-white bg-opacity-20 text-white border-0">{{ $acc->code ?? '' }}</span>
                    </div>
                    <h5 class="fw-bold mb-1"><i class="fa-solid fa-house me-2"></i>{{ $acc->name ?? 'N/A' }}</h5>
                    <div class="small opacity-75">{{ $acc->type?->label() ?? '' }} • {{ $acc->pricing_type?->label() ?? '' }}</div>
                    <div class="mt-3 p-3 bg-white bg-opacity-10 rounded-3">
                        <div class="small fw-bold opacity-75 mb-2">Ajustes de esta temporada</div>
                        <div class="d-flex justify-content-between small mb-1"><span class="opacity-75"><i class="fa-solid fa-house me-1"></i> Alojamiento</span><span class="fw-bold">{{ $ratePeriod->accommodationAdjustmentLabel() }} @if(!$hasAcc)<small class="opacity-75">(usa adulto)</small>@endif</span></div>
                        <div class="d-flex justify-content-between small mb-1"><span class="opacity-75"><i class="fa-solid fa-user me-1"></i> Adulto</span><span class="fw-bold">{{ $ratePeriod->adjustmentLabel() }}</span></div>
                        <div class="d-flex justify-content-between small"><span class="opacity-75"><i class="fa-solid fa-child me-1"></i> Niño</span><span class="fw-bold">{{ $ratePeriod->childAdjustmentLabel() }} @if(!$hasChild)<small class="opacity-75">(usa adulto)</small>@endif</span></div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <h6 class="fw-bold small text-muted mb-3"><i class="fa-solid fa-tags me-1"></i> Precios base → con ajuste</h6>
                    <div class="small">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Alojamiento <small class="d-block text-muted">Tarifa plana /noche</small></span>
                            <span class="text-end"><span class="text-muted text-decoration-line-through">${{ number_format($baseAloj,0) }}</span><br><span class="fw-bold text-success">${{ number_format($adjAloj,0) }}</span></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Adulto <small class="d-block text-muted">Por persona /noche</small></span>
                            <span class="text-end"><span class="text-muted text-decoration-line-through">${{ number_format($baseAdult,0) }}</span><br><span class="fw-bold text-success">${{ number_format($adjAdult,0) }}</span></span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Niño <small class="d-block text-muted">@if($baseChild==0) Gratis @elseif($baseChild==$baseAdult) Igual adulto @else Por persona /noche @endif</small></span>
                            <span class="text-end"><span class="text-muted text-decoration-line-through">${{ number_format($baseChild,0) }}</span><br><span class="fw-bold text-success">${{ number_format($adjChild,0) }}</span> @if($baseChild==0)<span class="badge bg-success ms-1">Gratis base</span>@endif</span>
                        </div>
                    </div>
                    <div class="alert alert-light border small mt-3 mb-0">
                        <div class="fw-bold small mb-1">Fórmula</div>
                        <div class="small text-muted">Por persona: <code>Adultos×(Base Adulto+Ajuste) + Niños×(Base Niño+Ajuste Niño)</code><br>Por alojamiento: <code>Base Alojamiento + Ajuste Alojamiento</code></div>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted fw-bold">EXTRA ADULTO <small class="d-block fw-normal">Supera aforo (solo por alojamiento)</small></span>
                        <span class="fw-bold {{ $ratePeriod->extra_guest_price ? 'text-warning' : 'text-muted' }}">+${{ number_format($ratePeriod->extra_guest_price ?? 0,0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-3">
                        <span class="text-muted fw-bold">EXTRA NIÑO</span>
                        <span class="fw-bold {{ $ratePeriod->extra_child_price ? 'text-warning' : 'text-muted' }}">+${{ number_format($ratePeriod->extra_child_price ?? 0,0) }} @if(!$ratePeriod->extra_child_price)<small class="text-muted">(usa adulto)</small>@endif</span>
                    </div>
                    <div class="row g-2 small text-center">
                        <div class="col-4"><div class="p-2 bg-light rounded-3"><div class="text-muted small">Prioridad</div><div class="fw-bold">#{{ $ratePeriod->priority ?? 0 }}</div></div></div>
                        <div class="col-4"><div class="p-2 bg-light rounded-3"><div class="text-muted small">Mín noches</div><div class="fw-bold">{{ $ratePeriod->min_nights ?? 1 }}</div></div></div>
                        <div class="col-4"><div class="p-2 bg-light rounded-3"><div class="text-muted small">Máx noches</div><div class="fw-bold">{{ $ratePeriod->max_nights ?? '∞' }}</div></div></div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3">
                    <h6 class="fw-bold small mb-2"><i class="fa-solid fa-circle-info text-primary me-1"></i> Detalle alojamiento</h6>
                    <div class="small">
                        <div class="d-flex justify-content-between py-1"><span class="text-muted">Capacidad</span><span class="fw-bold">{{ $acc->max_guests ?? '-' }} huéspedes</span></div>
                        <div class="d-flex justify-content-between py-1"><span class="text-muted">Pasadía</span><span class="fw-bold">@if($acc->allows_day_pass) Sí ({{ $acc->day_pass_max_guests ?? $acc->max_guests }} pax) @else No @endif</span></div>
                        <div class="d-flex justify-content-between py-1"><span class="text-muted">Check-in/out</span><span class="fw-bold">{{ $acc->check_in_time ?? '15:00' }} / {{ $acc->check_out_time ?? '11:00' }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase small fw-bold text-muted mb-3"><i class="fa-solid fa-calendar-day me-2"></i> Rango de Fechas</h6>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success-subtle rounded-3 p-3 text-success">
                                    <i class="fa-solid fa-play display-6"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-bold">INICIO</div>
                                    <div class="fw-bold fs-4">{{ $ratePeriod->start_date?->format('d / M / Y') }}</div>
                                </div>
                            </div>
                            <hr class="my-3 opacity-25">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger-subtle rounded-3 p-3 text-danger">
                                    <i class="fa-solid fa-stop display-6"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-bold">FINAL</div>
                                    <div class="fw-bold fs-4">{{ $ratePeriod->end_date?->format('d / M / Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase small fw-bold text-muted mb-3"><i class="fa-solid fa-sliders me-2"></i> Reglas de Aplicación</h6>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="fa-solid fa-mug-hot me-2 text-warning"></i> Fin de Semana</span>
                                    @if($ratePeriod->is_weekend) <i class="fa-solid fa-check text-success"></i> @else <i class="fa-solid fa-x text-muted opacity-25"></i> @endif
                                </div>
                                <hr class="opacity-25">
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="fa-solid fa-champagne-glasses me-2 text-danger"></i> Días Festivos</span>
                                    @if($ratePeriod->is_holiday) <i class="fa-solid fa-check text-success"></i> @else <i class="fa-solid fa-x text-muted opacity-25"></i> @endif
                                </div>
                                <hr class="opacity-25">
                            </div>
                            <div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="fa-solid fa-calendar-week me-2 text-primary"></i> Días Específicos</span>
                                    @if($ratePeriod->days_of_week)
                                        <span class="text-primary fw-bold">{{ count($ratePeriod->days_of_week) }} días</span>
                                    @else
                                        <i class="fa-solid fa-x text-muted opacity-25"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($ratePeriod->notes)
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-warning-subtle border-start border-warning border-5">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-warning d-flex align-items-center mb-2">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> Notas Internas
                    </h6>
                    <p class="mb-0 fw-medium">{{ $ratePeriod->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
