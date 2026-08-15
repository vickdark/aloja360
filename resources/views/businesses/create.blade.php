@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-building-circle-check text-primary me-2"></i> Crear Nuevo Negocio
        </h1>
        <a href="{{ route('businesses.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información del Negocio</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('businesses.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nombre Comercial <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="legal_name" class="form-label">Razón Social</label>
                        <input type="text" class="form-control" id="legal_name" name="legal_name" value="{{ old('legal_name') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="tax_id" class="form-label">Identificación Tributaria (NIT/RUT)</label>
                        <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ old('tax_id') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="whatsapp" class="form-label">WhatsApp</label>
                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}">
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="font-weight-bold text-primary mb-3">Ubicación y Localización</h6>

                <div class="row mb-3">
                    <div class="col-md-12 mb-3">
                        <label for="address" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="city" class="form-label">Ciudad</label>
                        <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="country" class="form-label">País</label>
                        <input type="text" class="form-control" id="country" name="country" value="{{ old('country') }}">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="timezone" class="form-label">Zona Horaria <span class="text-danger">*</span></label>
                        <select class="form-select" id="timezone" name="timezone" required>
                            <option value="America/Bogota" {{ old('timezone') == 'America/Bogota' ? 'selected' : '' }}>America/Bogota (UTC-5)</option>
                            <option value="America/Mexico_City" {{ old('timezone') == 'America/Mexico_City' ? 'selected' : '' }}>America/Mexico_City (UTC-6)</option>
                            <option value="America/Lima" {{ old('timezone') == 'America/Lima' ? 'selected' : '' }}>America/Lima (UTC-5)</option>
                            <option value="America/Argentina/Lima" {{ old('timezone') == 'America/Argentina/Lima' ? 'selected' : '' }}>America/Argentina/Lima (UTC-3)</option>
                            <option value="America/Santiago" {{ old('timezone') == 'America/Santiago' ? 'selected' : '' }}>America/Santiago (UTC-3)</option>
                            <option value="Europe/Madrid" {{ old('timezone') == 'Europe/Madrid' ? 'selected' : '' }}>Europe/Madrid (UTC+1)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="currency" class="form-label">Moneda (ISO 3) <span class="text-danger">*</span></label>
                        <select class="form-select" id="currency" name="currency" required>
                            <option value="COP" {{ old('currency') == 'COP' ? 'selected' : '' }}>COP - Peso Colombiano</option>
                            <option value="MXN" {{ old('currency') == 'MXN' ? 'selected' : '' }}>MXN - Peso Mexicano</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - Dólar Estadounidense</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Estado Inicial <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Guardar Negocio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
