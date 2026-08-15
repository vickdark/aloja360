@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Gestionar Permisos: {{ $role->nombre }}</h1>
            <p class="text-muted small mb-0">Selecciona los accesos específicos para este rol.</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary rounded-pill px-4" id="selectAllGlobal">
                    <i class="fas fa-check-double me-2"></i> Seleccionar Todo
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('roles.update_permissions', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            @php
                $groupedPermissions = $permissions->groupBy(function($item) {
                    return explode('.', $item->slug)[0];
                });
            @endphp

            @foreach($groupedPermissions as $module => $modulePermissions)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-soft rounded-4 overflow-hidden module-card">
                        <div class="card-header bg-primary text-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fs-6 fw-bold text-uppercase letter-spacing-1">
                                <i class="fas fa-folder me-2"></i> {{ ucfirst($module) }}
                            </h5>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input select-module" type="checkbox" data-module="{{ $module }}" id="switch_{{ $module }}">
                                <label class="form-check-label text-white small" for="switch_{{ $module }}">Todo</label>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="list-group list-group-flush">
                                @foreach($modulePermissions as $permission)
                                    <div class="list-group-item border-0 px-0 py-2">
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" 
                                                   type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}" 
                                                   id="perm_{{ $permission->id }}"
                                                   data-module-group="{{ $module }}"
                                                   {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label cursor-pointer w-100" for="perm_{{ $permission->id }}">
                                                <div class="fw-bold text-dark small">{{ $permission->nombre }}</div>
                                                <div class="text-muted x-small">{{ $permission->descripcion }}</div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mt-5 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-soft rounded-4 bg-primary text-white">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">Guardar Configuración</h5>
                            <p class="mb-0 opacity-75 small">Se aplicarán los permisos seleccionados inmediatamente al rol {{ $role->nombre }}.</p>
                        </div>
                        <button type="submit" class="btn btn-light text-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> Actualizar Permisos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .letter-spacing-1 { letter-spacing: 1px; }
    .x-small { font-size: 0.7rem; }
    .shadow-soft { box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03); }
    .module-card { transition: transform 0.2s; }
    .module-card:hover { transform: translateY(-5px); }
    .cursor-pointer { cursor: pointer; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar Todo Global
    const selectAllGlobal = document.getElementById('selectAllGlobal');
    const allCheckboxes = document.querySelectorAll('.permission-checkbox');
    const allSwitches = document.querySelectorAll('.select-module');
    
    let isAllSelected = false;

    selectAllGlobal.addEventListener('click', function() {
        isAllSelected = !isAllSelected;
        allCheckboxes.forEach(cb => cb.checked = isAllSelected);
        allSwitches.forEach(sw => sw.checked = isAllSelected);
        
        this.innerHTML = isAllSelected 
            ? '<i class="fas fa-times-circle me-2"></i> Deseleccionar Todo' 
            : '<i class="fas fa-check-double me-2"></i> Seleccionar Todo';
        
        this.classList.toggle('btn-outline-primary');
        this.classList.toggle('btn-outline-danger');
    });

    // Seleccionar por Módulo
    allSwitches.forEach(sw => {
        sw.addEventListener('change', function() {
            const module = this.dataset.module;
            const moduleCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module-group="${module}"]`);
            moduleCheckboxes.forEach(cb => cb.checked = this.checked);
        });
    });

    // Actualizar Switch de módulo si se desmarca uno individualmente
    allCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const module = this.dataset.moduleGroup;
            const moduleSwitch = document.querySelector(`.select-module[data-module="${module}"]`);
            const moduleCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module-group="${module}"]`);
            const allChecked = Array.from(moduleCheckboxes).every(c => c.checked);
            moduleSwitch.checked = allChecked;
        });
    });

    // Inicializar switches de módulo
    allSwitches.forEach(sw => {
        const module = sw.dataset.module;
        const moduleCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module-group="${module}"]`);
        const allChecked = Array.from(moduleCheckboxes).every(c => c.checked);
        sw.checked = allChecked;
    });
});
</script>
@endsection
