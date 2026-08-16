@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Roles del Sistema</h1>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                @if(auth()->user()->hasPermission('permissions.sync'))
                    <button type="button" id="btnSyncPermissions" class="btn btn-outline-info rounded-pill px-4">
                        <i class="fas fa-sync me-2"></i> Sincronizar Permisos
                    </button>
                @endif
                @if(auth()->user()->hasPermission('roles.create'))
                    <a href="{{ route('roles.create') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-plus me-2"></i> Nuevo Rol
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Identificador (Slug)</th>
                            <th>Usuarios</th>
                            <th>Descripción</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark">{{ $role->nombre }}</span>
                            </td>
                            <td>
                                <code class="small text-primary bg-primary-subtle px-2 py-1 rounded">{{ $role->slug }}</code>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border rounded-pill px-3">
                                    <i class="fas fa-users me-1 text-muted"></i> {{ $role->users_count }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ Str::limit($role->descripcion ?: 'Sin descripción', 50) }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    @if(auth()->user()->hasPermission('roles.edit'))
                                        <a href="{{ route('roles.edit_permissions', $role) }}" class="btn btn-sm btn-outline-info rounded-pill px-3" title="Gestionar Permisos">
                                            <i class="fas fa-key me-1"></i> Permisos
                                        </a>
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-edit me-1"></i> Editar
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->hasPermission('roles.destroy'))
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este rol?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" {{ $role->slug === 'admin' ? 'disabled' : '' }}>
                                            <i class="fas fa-trash-alt me-1"></i> Eliminar
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Progreso de Sincronización -->
<div class="modal fade" id="syncProgressModal" tabindex="-1" aria-labelledby="syncProgressModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-info-600 to-indigo-600 text-white border-0" style="background: linear-gradient(135deg, #0dcaf0, #4f46e5);">
                <h5 class="modal-title fw-bold" id="syncProgressModalLabel">
                    <i class="fas fa-sync-alt fa-spin me-2"></i>
                    <span id="syncModalTitle">Sincronizando Permisos</span>
                </h5>
                <button type="button" class="btn-close btn-close-white d-none" id="syncModalClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-gray-700" id="syncStepLabel">Preparando...</span>
                        <span class="fw-bold text-primary fs-5" id="syncPercentLabel">0%</span>
                    </div>
                    <div class="progress" style="height: 1.25rem; border-radius: 9999px; background: #eef2ff;">
                        <div id="syncProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-gradient-to-r" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #0dcaf0, #4f46e5);" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small text-muted">
                        <span id="syncStatsLabel">0 / 0 rutas procesadas</span>
                        <span id="syncTimeLabel">Tiempo transcurrido: 0s</span>
                    </div>
                </div>

                <div class="card bg-dark text-light border-0 rounded-3">
                    <div class="card-header bg-black bg-opacity-25 border-0 d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="small fw-semibold"><i class="fas fa-terminal me-1"></i> Registro de eventos</span>
                        <span class="badge bg-info bg-opacity-50 small" id="syncLogCount">0 líneas</span>
                    </div>
                    <div class="card-body p-3" id="syncLogContainer" style="height: 240px; overflow-y: auto; font-family: 'JetBrains Mono', 'Fira Code', Consolas, monospace; font-size: 0.8rem; line-height: 1.5;">
                        <div class="text-info opacity-75">Esperando inicio del proceso...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-gray-50 border-0 d-flex justify-content-between px-4 py-3">
                <div id="syncResultBadge"></div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 d-none" id="syncCloseBtn" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cerrar
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 d-none" id="syncReloadBtn">
                        <i class="fas fa-sync me-2"></i> Actualizar Página
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnSync = document.getElementById('btnSyncPermissions');
    if (!btnSync) return;

    function bootstrapReady() { return typeof bootstrap !== 'undefined'; }

    function withBootstrap(fn) {
        if (bootstrapReady()) { fn(); return; }
        let tries = 0;
        const t = setInterval(() => {
            tries++;
            if (bootstrapReady()) { clearInterval(t); fn(); return; }
            if (tries > 50) { clearInterval(t); alert('Bootstrap no está cargado. Intenta refrescar la página.'); }
        }, 100);
    }

    btnSync.addEventListener('click', function () {
        withBootstrap(startSync);
    });

    function startSync() {
        const syncModal = new bootstrap.Modal(document.getElementById('syncProgressModal'));

        const progressBar = document.getElementById('syncProgressBar');
        const percentLabel = document.getElementById('syncPercentLabel');
        const stepLabel = document.getElementById('syncStepLabel');
        const statsLabel = document.getElementById('syncStatsLabel');
        const timeLabel = document.getElementById('syncTimeLabel');
        const logContainer = document.getElementById('syncLogContainer');
        const logCountLabel = document.getElementById('syncLogCount');
        const modalTitle = document.getElementById('syncModalTitle');
        const closeBtn = document.getElementById('syncCloseBtn');
        const syncCloseBtn = document.getElementById('syncCloseBtn');
        const syncReloadBtn = document.getElementById('syncReloadBtn');
        const resultBadge = document.getElementById('syncResultBadge');
        const modalCloseX = document.getElementById('syncModalClose');

        if (syncReloadBtn) {
            syncReloadBtn.addEventListener('click', function () {
                window.location.reload();
            });
        }

        let logLines = 0;
        let startTime = 0;
        let timeInterval = null;
        let aborted = false;

        aborted = false;
        logLines = 0;
        startTime = Date.now();
        updateTime();

        progressBar.style.width = '0%';
        progressBar.setAttribute('aria-valuenow', 0);
        percentLabel.textContent = '0%';
        stepLabel.textContent = 'Iniciando proceso...';
        statsLabel.textContent = '0 / 0 rutas procesadas';
        logContainer.innerHTML = '';
        logCountLabel.textContent = '0 líneas';
        resultBadge.innerHTML = '';
        closeBtn.classList.add('d-none');
        syncCloseBtn.classList.add('d-none');
        syncReloadBtn.classList.add('d-none');
        modalCloseX.classList.add('d-none');
        modalTitle.textContent = 'Sincronizando Permisos';
        progressBar.classList.add('progress-bar-animated');

        syncModal.show();

        if (timeInterval) clearInterval(timeInterval);
        timeInterval = setInterval(updateTime, 1000);

        runStream();

        function updateTime() {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const m = Math.floor(elapsed / 60);
            const s = elapsed % 60;
            timeLabel.textContent = `Tiempo transcurrido: ${m > 0 ? m + 'm ' : ''}${s}s`;
        }

        function addLog(message, type = 'info') {
            const div = document.createElement('div');
            const colors = {
                info:    'text-info',
                line:    'text-white',
                warn:    'text-warning',
                success: 'text-success fw-bold',
                error:   'text-danger fw-bold',
                start:   'text-info opacity-75',
                complete:'text-success fw-bold',
            };
            const icons = {
                info:    '',
                line:    '',
                warn:    '⚠ ',
                success: '✔ ',
                error:   '✖ ',
                start:   '→ ',
                complete:'✅ ',
            };
            div.className = colors[type] || 'text-white';
            const time = new Date().toLocaleTimeString();
            div.innerHTML = `<span class="opacity-50">[${time}]</span> ${icons[type] || ''}${escapeHtml(message)}`;
            logContainer.appendChild(div);
            logLines++;
            logCountLabel.textContent = `${logLines} líneas`;
            logContainer.scrollTop = logContainer.scrollHeight;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function updateProgress(percent, step, total, message) {
            const safePercent = Math.max(0, Math.min(100, percent));
            progressBar.style.width = safePercent + '%';
            progressBar.setAttribute('aria-valuenow', safePercent);
            percentLabel.textContent = safePercent + '%';

            if (message) {
                stepLabel.textContent = message;
            }
            if (typeof step === 'number' && typeof total === 'number') {
                statsLabel.textContent = `${step} / ${total} rutas procesadas`;
            }
        }

        function finishSync(success, finalMessage, stats) {
            if (timeInterval) clearInterval(timeInterval);
            updateTime();

            progressBar.classList.remove('progress-bar-animated');

            if (success) {
                progressBar.style.background = 'linear-gradient(90deg, #10b981, #059669)';
                resultBadge.innerHTML = `<span class="badge bg-success rounded-pill px-4 py-2"><i class="fas fa-check-circle me-1"></i> Proceso completado</span>`;
                modalTitle.textContent = '¡Sincronización completada!';
            } else {
                progressBar.style.background = 'linear-gradient(90deg, #ef4444, #dc2626)';
                resultBadge.innerHTML = `<span class="badge bg-danger rounded-pill px-4 py-2"><i class="fas fa-times-circle me-1"></i> Error en el proceso</span>`;
                modalTitle.textContent = 'Error durante la sincronización';
            }

            if (stats) {
                addLog(`Resumen: ${stats.processed || 0} procesados · ${stats.created || 0} nuevos · ${stats.deleted || 0} eliminados`, success ? 'success' : 'error');
            }

            closeBtn.classList.remove('d-none');
            syncCloseBtn.classList.remove('d-none');
            modalCloseX.classList.remove('d-none');
            if (success) {
                syncReloadBtn.classList.remove('d-none');
            }
        }

        async function runStream() {
            const endpoint = @json(route('permissions.sync_stream'));

            try {
                addLog('Conectando con el servidor...', 'start');

                const response = await fetch(endpoint, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'text/event-stream, application/json',
                        'Cache-Control': 'no-cache',
                    },
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const reader = response.body.getReader();
                const decoder = new TextDecoder('utf-8');
                let buffer = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done || aborted) break;

                    buffer += decoder.decode(value, { stream: true });
                    const lines = buffer.split(/\r?\n/);
                    buffer = lines.pop() || '';

                    for (const rawLine of lines) {
                        const line = rawLine.trim();
                        if (!line) continue;

                        let event;
                        try {
                            event = JSON.parse(line);
                        } catch (e) {
                            addLog('Evento desconocido: ' + line, 'warn');
                            continue;
                        }

                        handleEvent(event);
                    }
                }

                if (buffer.trim()) {
                    try {
                        handleEvent(JSON.parse(buffer.trim()));
                    } catch (_) {}
                }
            } catch (err) {
                addLog('Fallo de conexión: ' + err.message, 'error');
                finishSync(false, err.message);
            }
        }

        function handleEvent(event) {
            const type = event.type || 'info';
            const msg = event.message || '';
            const percent = event.percent ?? null;

            if (percent !== null) {
                updateProgress(percent, event.step, event.total, msg);
            }

            switch (type) {
                case 'start':
                    addLog(msg, 'start');
                    break;
                case 'complete':
                    addLog(msg, 'complete');
                    finishSync(true, msg, event.stats);
                    break;
                case 'error':
                    addLog(msg, 'error');
                    finishSync(false, msg);
                    break;
                case 'warn':
                    addLog(msg, 'warn');
                    break;
                case 'success':
                    addLog(msg, 'success');
                    break;
                case 'line':
                    addLog(msg, 'line');
                    break;
                case 'info':
                default:
                    addLog(msg, 'info');
                    break;
            }
        }
    }
});
</script>
@endpush
