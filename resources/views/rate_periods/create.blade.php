@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fa-solid fa-plus text-primary me-2"></i>Nueva Temporada / Regla Tarifaria</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('rate_periods.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('rate_periods.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold"><i class="fa-solid fa-calendar text-primary me-2"></i> Configuración General</h4>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Nombre de la Temporada</label>
                                <input type="text" name="name" id="name" value="{{ old('name', 'Temporada Alta') }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Aplicar a Alojamiento</label>
                                <select name="accommodation_id" id="accommodation_id" class="form-select form-select-lg" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="all" {{ old('accommodation_id') == 'all' ? 'selected' : '' }}>🌐 Todos los alojamientos (crea 1 regla por alojamiento)</option>
                                    @foreach($accommodations as $acc)
                                        <option value="{{ $acc->id }}"
                                            data-base-price="{{ $acc->base_price }}"
                                            data-price-per-person="{{ $acc->price_per_person ?? 0 }}"
                                            data-price-per-child="{{ $acc->price_per_child ?? $acc->price_per_person ?? 0 }}"
                                            data-day-pass-base-price="{{ $acc->day_pass_base_price ?? $acc->base_price }}"
                                            data-day-pass-price-per-person="{{ $acc->day_pass_price_per_person ?? $acc->price_per_person ?? 0 }}"
                                            data-day-pass-price-per-child="{{ $acc->day_pass_price_per_child ?? $acc->price_per_child ?? $acc->price_per_person ?? 0 }}"
                                            data-pricing-type="{{ $acc->pricing_type }}"
                                            {{ old('accommodation_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text" id="all_hint" style="display:none;"><i class="fa-solid fa-circle-info me-1"></i> Se crearán <strong>{{ $accommodations->count() }} reglas</strong> idénticas, una por cada alojamiento.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Prioridad</label>
                                <input type="number" name="priority" value="{{ old('priority', 1) }}" class="form-control form-control-lg" min="0">
                                <div class="form-text">Mayor = Se aplica primero.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Estado</label>
                                <select name="status" id="status" class="form-select form-select-lg">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activa</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactiva</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha de Inicio</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha de Fin</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Notas Internas</label>
                                <textarea name="notes" rows="3" class="form-control" placeholder="Ej: Vacaciones de semana santa, aplicar solo a cabañas...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-1 fw-bold"><i class="fa-solid fa-clock text-primary me-2"></i> ¿Cuándo aplica?</h4>
                        <p class="text-muted small mb-3">Deja sin marcar para aplicar todos los días del rango. Marca para filtrar.</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="is_weekend" class="w-100 mb-0" style="cursor:pointer;">
                                    <input class="d-none" type="checkbox" name="is_weekend" id="is_weekend" value="1" {{ old('is_weekend') ? 'checked' : '' }}>
                                    <div class="p-3 border rounded-4 text-center h-100 transition-all" id="card_weekend">
                                        <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle" style="width:48px;height:48px;background:#fff7ed;">
                                            <i class="fa-solid fa-mug-hot text-warning fs-4"></i>
                                        </div>
                                        <div class="fw-bold">Fines de semana</div>
                                        <small class="text-muted">Solo sábados y domingos</small>
                                        <div class="mt-2"><span class="badge rounded-pill" id="badge_weekend">Inactivo</span></div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label for="is_holiday" class="w-100 mb-0" style="cursor:pointer;">
                                    <input class="d-none" type="checkbox" name="is_holiday" id="is_holiday" value="1" {{ old('is_holiday') ? 'checked' : '' }}>
                                    <div class="p-3 border rounded-4 text-center h-100 transition-all" id="card_holiday">
                                        <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle" style="width:48px;height:48px;background:#fef2f2;">
                                            <i class="fa-solid fa-champagne-glasses text-danger fs-4"></i>
                                        </div>
                                        <div class="fw-bold">Días festivos</div>
                                        <small class="text-muted">Solo festivos oficiales de Colombia</small>
                                        <div class="mt-2"><span class="badge rounded-pill" id="badge_holiday">Inactivo</span></div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <!-- Tarjeta referencia Precios Base (ahora en columna derecha, arriba) -->
                <div class="card border-0 shadow-sm rounded-4 mb-4" id="price_reference_card" style="display:none;">
                    <div class="card-header bg-white border-bottom p-3">
                        <h6 class="mb-0 fw-bold d-flex align-items-center">
                            <i class="fa-solid fa-tags text-primary me-2"></i> Precios Base
                            <span class="badge bg-light text-dark ms-2 border" id="ref_accommodation_name"></span>
                        </h6>
                        <small class="text-muted">Referencia del alojamiento seleccionado</small>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2 small" id="price_reference_content"></div>
                        <div class="alert alert-info border-0 rounded-3 small mt-3 mb-0" id="price_preview_alert" style="display:none;">
                            <div class="fw-bold mb-1"><i class="fa-solid fa-calculator me-1"></i> Vista previa</div>
                            <div id="price_preview_content"></div>
                        </div>
                        <div class="alert alert-warning border-0 rounded-3 small mt-3 mb-0" id="all_reference_alert" style="display:none;">
                            <i class="fa-solid fa-layer-group me-1"></i> Se creará la misma regla para <strong>{{ $accommodations->count() }} alojamientos</strong>.
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3 fw-bold d-flex align-items-center">
                            <i class="fa-solid fa-sliders text-primary me-2"></i> Ajuste Tarifario
                            <span class="badge bg-primary bg-opacity-10 text-primary ms-auto">Por persona / Por alojamiento</span>
                        </h5>
                        <p class="text-muted small mb-3">Se <strong>suma</strong> al precio base del alojamiento, nunca lo reemplaza.<br><span class="small">Por persona: <code>Adultos×(Base Adulto+Ajuste) + Niños×(Base Niño+Ajuste Niño)</code> — ver Precios Base a la derecha. Por alojamiento: Base Alojamiento + Ajuste.</span></p>

                        <div class="p-3 bg-light rounded-4 border mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa-solid fa-house text-primary me-2"></i>
                                <span class="fw-bold small">Alojamiento</span>
                                <span class="badge bg-white border text-muted ms-auto">Tarifa plana</span>
                            </div>
                            <div class="form-text small text-muted mb-2">Ajuste para <strong>Tarifa por Alojamiento completo</strong>. Si vacío usa el de Adulto.</div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Tipo alojamiento</label>
                                    <select name="accommodation_adjustment_type" id="accommodation_adjustment_type" class="form-select">
                                        <option value="" {{ old('accommodation_adjustment_type') === '' ? 'selected' : '' }}>Usar adulto</option>
                                        <option value="amount" {{ old('accommodation_adjustment_type', 'amount') == 'amount' ? 'selected' : '' }}>Monto fijo (+ $)</option>
                                        <option value="percentage" {{ old('accommodation_adjustment_type') == 'percentage' ? 'selected' : '' }}>Porcentaje (+ %)</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Valor alojamiento</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="100" min="0" name="accommodation_adjustment_value" id="accommodation_adjustment_value" value="{{ old('accommodation_adjustment_value') }}" class="form-control" placeholder="Vacío = adulto">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-4 border mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa-solid fa-user text-primary me-2"></i>
                                <span class="fw-bold small">Adulto</span>
                                <span class="badge bg-white border text-muted ms-auto">Ajuste principal</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Tipo</label>
                                    <select name="adjustment_type" id="adjustment_type" class="form-select">
                                        <option value="amount" {{ old('adjustment_type', 'amount') == 'amount' ? 'selected' : '' }}>Monto fijo (+ $)</option>
                                        <option value="percentage" {{ old('adjustment_type') == 'percentage' ? 'selected' : '' }}>Porcentaje (+ %)</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Valor</label>
                                    <div class="input-group" id="adjustment_value_group">
                                        <span class="input-group-text" id="adjustment_prefix">$</span>
                                        <input type="number" step="100" min="0" name="adjustment_value" id="adjustment_value" value="{{ old('adjustment_value', 0) }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-text small" id="adjustment_hint">Ej: 50.000 → base 100.000 + 50.000 = 150.000</div>
                        </div>

                        <div class="p-3 bg-white rounded-4 border mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa-solid fa-child text-success me-2"></i>
                                <span class="fw-bold small">Niño</span>
                                <span class="badge bg-success bg-opacity-10 text-success ms-auto">opcional</span>
                            </div>
                            <div class="form-text small text-muted mb-2">Si se deja vacío, el niño usa el mismo ajuste que el adulto.</div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Tipo niño</label>
                                    <select name="child_adjustment_type" id="child_adjustment_type" class="form-select">
                                        <option value="" {{ old('child_adjustment_type') === '' ? 'selected' : '' }}>Usar adulto</option>
                                        <option value="amount" {{ old('child_adjustment_type', 'amount') == 'amount' ? 'selected' : '' }}>Monto fijo (+ $)</option>
                                        <option value="percentage" {{ old('child_adjustment_type') == 'percentage' ? 'selected' : '' }}>Porcentaje (+ %)</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Valor niño</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="100" min="0" name="child_adjustment_value" id="child_adjustment_value" value="{{ old('child_adjustment_value') }}" class="form-control" placeholder="Vacío = adulto">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-4 border mb-3">
                            <div class="fw-bold small mb-2"><i class="fa-solid fa-users me-1 text-warning"></i> Recargo por exceso <small class="text-muted fw-normal">(solo Tarifa por Alojamiento)</small></div>
                            <p class="form-text small text-muted mb-2">Se cobra <strong>por cada huésped que supere la capacidad base</strong> del alojamiento. <strong>No se usa en Tarifa por Persona</strong> — ahí el total es <code>Adultos×Tarifa Adulto + Niños×Tarifa Niño</code> (arriba).</p>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Adulto extra</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="100" min="0" name="extra_guest_price" value="{{ old('extra_guest_price', 0) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Niño extra</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="100" min="0" name="extra_child_price" value="{{ old('extra_child_price', 0) }}" class="form-control" placeholder="Vacío = adulto">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Mínimo noches</label>
                                <input type="number" min="1" name="min_nights" value="{{ old('min_nights', 1) }}" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Máximo noches</label>
                                <input type="number" min="1" name="max_nights" value="{{ old('max_nights', 30) }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                        <i class="fas fa-save me-2"></i> Guardar Regla
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('adjustment_type');
    const prefix = document.getElementById('adjustment_prefix');
    const hint = document.getElementById('adjustment_hint');
    const accSelect = document.getElementById('accommodation_id');
    const refCard = document.getElementById('price_reference_card');
    const refContent = document.getElementById('price_reference_content');
    const refName = document.getElementById('ref_accommodation_name');
    const previewAlert = document.getElementById('price_preview_alert');
    const previewContent = document.getElementById('price_preview_content');
    const allHint = document.getElementById('all_hint');
    const allAlert = document.getElementById('all_reference_alert');
    const adjValue = document.getElementById('adjustment_value');
    const childType = document.getElementById('child_adjustment_type');
    const childValue = document.getElementById('child_adjustment_value');
    const accType = document.getElementById('accommodation_adjustment_type');
    const accValue = document.getElementById('accommodation_adjustment_value');
    const weekendCheck = document.getElementById('is_weekend');
    const holidayCheck = document.getElementById('is_holiday');
    const cardWeekend = document.getElementById('card_weekend');
    const cardHoliday = document.getElementById('card_holiday');
    const badgeWeekend = document.getElementById('badge_weekend');
    const badgeHoliday = document.getElementById('badge_holiday');

    function fmt(n){ return Number(n).toLocaleString('es-CO'); }

    function refreshAdjustmentUI() {
        if (typeSelect.value === 'percentage') {
            prefix.textContent = '％';
            hint.textContent = 'Ej: 30 → base 100.000 + 30% = 130.000';
        } else {
            prefix.textContent = '$';
            hint.textContent = 'Ej: 50.000 → base 100.000 + 50.000 = 150.000';
        }
        updatePreview();
    }

    function updateReference(){
        const opt = accSelect.options[accSelect.selectedIndex];
        if (!opt || !opt.value) {
            refCard.style.display = 'none';
            if(allHint) allHint.style.display='none';
            if(allAlert) allAlert.style.display='none';
            return;
        }
        if (opt.value === 'all') {
            refCard.style.display = 'block';
            refContent.innerHTML = '<div class="col-12 text-muted">Se creará la misma temporada para todos los alojamientos. Cada uno conserva su precio base.</div>';
            refName.textContent = 'Todos (' + (accSelect.options.length-2) + ')';
            if(previewAlert) previewAlert.style.display='none';
            if(allHint) allHint.style.display='block';
            if(allAlert) allAlert.style.display='block';
            return;
        }
        if(allHint) allHint.style.display='none';
        if(allAlert) allAlert.style.display='none';
        refCard.style.display = 'block';
        const base = parseFloat(opt.getAttribute('data-base-price'))||0;
        const adult = parseFloat(opt.getAttribute('data-price-per-person'))||0;
        let child = adult;
        const rawChild = opt.getAttribute('data-price-per-child');
        if(rawChild!==null && rawChild!==''){ const v=parseFloat(rawChild); if(!isNaN(v)) child=v; }
        const dpBase = parseFloat(opt.getAttribute('data-day-pass-base-price'))||base;
        const dpAdult = (()=>{ const r=opt.getAttribute('data-day-pass-price-per-person'); if(r!==null&&r!==''){const v=parseFloat(r); if(!isNaN(v)) return v;} return adult; })();
        let dpChild = child;
        const rawDpChild = opt.getAttribute('data-day-pass-price-per-child');
        if(rawDpChild!==null && rawDpChild!==''){ const v=parseFloat(rawDpChild); if(!isNaN(v)) dpChild=v; } else dpChild = child;
        refName.textContent = opt.textContent.trim();
        refContent.innerHTML = `
            <div class="col-12"><span class="text-muted">Tarifa alojamiento completo</span><span class="float-end fw-bold">$${fmt(base)} /noche</span></div>
            <div class="col-12"><span class="text-muted">Tarifa adulto</span><span class="float-end fw-bold">$${fmt(adult)} /noche</span></div>
            <div class="col-12"><span class="text-muted">Tarifa niño</span><span class="float-end fw-bold">$${fmt(child)} /noche ${child===adult && adult>0 ? '<small class="text-muted">(igual adulto)</small>': child===0?'<span class="badge bg-success ms-1">Gratis</span>':''}</span></div>
            <div class="col-12 border-top pt-2 mt-1"><span class="text-muted">Pasadía completo</span><span class="float-end fw-bold">$${fmt(dpBase)}</span></div>
            <div class="col-12"><span class="text-muted">Pasadía adulto</span><span class="float-end fw-bold">$${fmt(dpAdult)}</span></div>
            <div class="col-12"><span class="text-muted">Pasadía niño</span><span class="float-end fw-bold">$${fmt(dpChild)}</span></div>
        `;
        updatePreview();
    }

    function calcAdjusted(base, type, val){
        val = parseFloat(val)||0;
        if(!val) return base;
        if(type==='percentage') return Math.round(base*(1+val/100));
        return base+val;
    }

    function updatePreview(){
        const opt = accSelect.options[accSelect.selectedIndex];
        if(!opt || !opt.value || opt.value==='all'){ if(previewAlert) previewAlert.style.display='none'; return; }
        const base = parseFloat(opt.getAttribute('data-base-price'))||0;
        const adult = parseFloat(opt.getAttribute('data-price-per-person'))||0;
        const childRaw = parseFloat(opt.getAttribute('data-price-per-child'));
        const child = isNaN(childRaw)?adult:childRaw;
        const type = typeSelect.value;
        const val = parseFloat(adjValue.value)||0;
        const accTypeRaw = accType ? accType.value : '';
        const accValRaw = accValue ? accValue.value : '';
        const accTypeEff = accTypeRaw ? accTypeRaw : type;
        const accVal = accValRaw!=='' ? parseFloat(accValRaw) : val;
        const cType = childType && childType.value ? childType.value : type;
        const cValRaw = childValue ? childValue.value : '';
        const cVal = cValRaw!=='' ? parseFloat(cValRaw) : val;
        const cTypeEff = cValRaw!=='' ? cType : type;
        if(!val && !cValRaw && !accValRaw){ previewAlert.style.display='none'; return; }
        const adjBase = calcAdjusted(base, accTypeEff, accVal);
        const adjAdult = calcAdjusted(adult, type, val);
        const adjChild = calcAdjusted(child, cTypeEff, cVal);
        previewAlert.style.display='block';
        previewContent.innerHTML = `
            <div>Alojamiento: $${fmt(base)} → <strong class="text-success">$${fmt(adjBase)}</strong> <small class="text-muted">(${accTypeEff==='percentage'?'+'+accVal+'%':'+$'+fmt(accVal)}${accValRaw===''?' (usa adulto)':''})</small></div>
            <div>Adulto: $${fmt(adult)} → <strong class="text-success">$${fmt(adjAdult)}</strong> <small class="text-muted">(${type==='percentage'?'+'+val+'%':'+$'+fmt(val)})</small></div>
            <div>Niño: $${fmt(child)} → <strong class="text-success">$${fmt(adjChild)}</strong> <small class="text-muted">(${cTypeEff==='percentage'?'+'+cVal+'%':'+$'+fmt(cVal)}${cValRaw===''?' (usa adulto)':''})</small></div>
        `;
    }

    function updateWeekendHoliday(){
        if(weekendCheck){
            if(weekendCheck.checked){
                cardWeekend.classList.add('border-primary','bg-primary','bg-opacity-10');
                cardWeekend.classList.remove('border');
                badgeWeekend.textContent='Activo ✓';
                badgeWeekend.className='badge rounded-pill bg-success';
            }else{
                cardWeekend.classList.remove('border-primary','bg-primary','bg-opacity-10');
                cardWeekend.classList.add('border');
                badgeWeekend.textContent='Inactivo';
                badgeWeekend.className='badge rounded-pill bg-secondary';
            }
        }
        if(holidayCheck){
            if(holidayCheck.checked){
                cardHoliday.classList.add('border-primary','bg-primary','bg-opacity-10');
                cardHoliday.classList.remove('border');
                badgeHoliday.textContent='Activo ✓';
                badgeHoliday.className='badge rounded-pill bg-success';
            }else{
                cardHoliday.classList.remove('border-primary','bg-primary','bg-opacity-10');
                cardHoliday.classList.add('border');
                badgeHoliday.textContent='Inactivo';
                badgeHoliday.className='badge rounded-pill bg-secondary';
            }
        }
    }
    if(weekendCheck) weekendCheck.addEventListener('change', updateWeekendHoliday);
    if(holidayCheck) holidayCheck.addEventListener('change', updateWeekendHoliday);
    if(typeSelect) typeSelect.addEventListener('change', refreshAdjustmentUI);
    if(adjValue) adjValue.addEventListener('input', updatePreview);
    if(childType) childType.addEventListener('change', updatePreview);
    if(childValue) childValue.addEventListener('input', updatePreview);
    if(accType) accType.addEventListener('change', updatePreview);
    if(accValue) accValue.addEventListener('input', updatePreview);
    if(accSelect) accSelect.addEventListener('change', updateReference);
    refreshAdjustmentUI();
    updateReference();
    updateWeekendHoliday();
});
</script>
@endpush
@endsection
