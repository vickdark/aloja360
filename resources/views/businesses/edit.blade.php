@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fa-solid fa-building text-primary me-2"></i>Editar Información de Empresa</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('businesses.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver al Listado
            </a>
        </div>
    </div>

    <form action="{{ route('businesses.update', $business) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-5">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-id-card text-primary me-2"></i> Identificación Fiscal
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-12 mb-3">
                                <label class="form-label small fw-bold text-muted">Nombre Comercial</label>
                                <input type="text" name="name" value="{{ old('name', $business->name) }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Razón Social</label>
                                <input type="text" name="legal_name" value="{{ old('legal_name', $business->legal_name) }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">NIT / C.C / Identificación</label>
                                <input type="text" name="tax_id" value="{{ old('tax_id', $business->tax_id) }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-5">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-location-dot text-primary me-2"></i> Contacto y Ubicación
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Email Corporativo</label>
                                <input type="email" name="email" value="{{ old('email', $business->email) }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Teléfono</label>
                                <input type="text" name="phone" value="{{ old('phone', $business->phone) }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">WhatsApp</label>
                                <input type="text" name="whatsapp" value="{{ old('whatsapp', $business->whatsapp) }}" class="form-control">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Dirección Física</label>
                                <input type="text" name="address" value="{{ old('address', $business->address) }}" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted">Ciudad</label>
                                <input type="text" name="city" value="{{ old('city', $business->city) }}" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted">País</label>
                                <input type="text" name="country" value="{{ old('country', $business->country) }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 text-white" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold d-flex align-items-center">
                            <i class="fas fa-globe me-2"></i> Configuración Regional
                        </h4>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white-50">Zona Horaria</label>
                            <select name="timezone" class="form-select bg-white bg-opacity-10 border-0 text-white">
                                <option value="America/Bogota" {{ old('timezone', $business->timezone) == 'America/Bogota' ? 'selected' : '' }} style="color:black;">Bogotá, Lima, Quito (GMT-5)</option>
                                <option value="America/New_York" {{ old('timezone', $business->timezone) == 'America/New_York' ? 'selected' : '' }} style="color:black;">New York (GMT-4)</option>
                                <option value="America/Argentina/Buenos_Aires" {{ old('timezone', $business->timezone) == 'America/Argentina/Buenos_Aires' ? 'selected' : '' }} style="color:black;">Buenos Aires, São Paulo (GMT-3)</option>
                                <option value="Europe/Madrid" {{ old('timezone', $business->timezone) == 'Europe/Madrid' ? 'selected' : '' }} style="color:black;">Madrid (GMT+2)</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label small fw-bold text-white-50">Moneda Predeterminada</label>
                            <select name="currency" class="form-select bg-white bg-opacity-10 border-0 text-white">
                                <option value="COP" {{ old('currency', $business->currency) == 'COP' ? 'selected' : '' }} style="color:black;">Peso Colombiano (COP)</option>
                                <option value="USD" {{ old('currency', $business->currency) == 'USD' ? 'selected' : '' }} style="color:black;">Dólar (USD)</option>
                                <option value="EUR" {{ old('currency', $business->currency) == 'EUR' ? 'selected' : '' }} style="color:black;">Euro (EUR)</option>
                                <option value="ARS" {{ old('currency', $business->currency) == 'ARS' ? 'selected' : '' }} style="color:black;">Peso Argentino (ARS)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <label class="form-check-label fw-bold" for="status">
                            <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', $business->status) ? 'checked' : '' }}>
                            <i class="fa-solid fa-circle-check text-success me-2"></i> Empresa Activa en el Sistema
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
