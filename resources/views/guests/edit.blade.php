@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-user-edit text-primary me-2"></i> Editar Huésped
        </h1>
        <a href="{{ route('guests.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información del Huésped</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('guests.update', $guest) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">Nombre(s) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $guest->first_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $guest->last_name) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="document_type" class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                        <select class="form-select" id="document_type" name="document_type" required>
                            @foreach(\App\Enums\DocumentType::cases() as $type)
                                <option value="{{ $type->value }}" {{ old('document_type', $guest->document_type->value) == $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="document_number" class="form-label">Número de Documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="document_number" name="document_number" value="{{ old('document_number', $guest->document_number) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $guest->email) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $guest->phone) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="whatsapp" class="form-label">WhatsApp</label>
                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $guest->whatsapp) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date', optional($guest->birth_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="nationality" class="form-label">Nacionalidad</label>
                        <input type="text" class="form-control" id="nationality" name="nationality" value="{{ old('nationality', $guest->nationality) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="country" class="form-label">País (ISO 2)</label>
                        <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $guest->country) }}" maxlength="2">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="city" class="form-label">Ciudad</label>
                        <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $guest->city) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="address" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $guest->address) }}">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notas Adicionales</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $guest->notes) }}</textarea>
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="marketing_consent" name="marketing_consent" value="1" {{ old('marketing_consent', $guest->marketing_consent) ? 'checked' : '' }}>
                        <label class="form-check-label" for="marketing_consent">Consiente recibir información de marketing</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Actualizar Huésped
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
