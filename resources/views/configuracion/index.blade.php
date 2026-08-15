@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Configuración del Sistema</h1>
            <p class="text-muted">Personaliza la apariencia y los datos de tu empresa.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('configuracion.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- App Settings -->
            <div class="col-lg-6">
                <div class="card shadow-sm rounded-4 border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fa-solid fa-window-maximize text-primary"></i>
                        </div>
                        <h6 class="m-0 font-weight-bold text-primary">Apariencia del Sistema</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Nombre de la Aplicación</label>
                            <input type="text" name="app_name" class="form-control rounded-3" value="{{ setting('app_name') }}" placeholder="Ej: Mi Sistema">
                            <div class="form-text">Se muestra en la barra lateral y títulos.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Subtítulo</label>
                            <input type="text" name="app_subtitle" class="form-control rounded-3" value="{{ setting('app_subtitle') }}" placeholder="Ej: Gestión Integral">
                            <div class="form-text">Texto pequeño debajo del nombre en el sidebar.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Logo del Sistema (Imagen)</label>
                            <input type="file" name="app_logo_image" class="form-control rounded-3" accept="image/*">
                            <div class="form-text">Sube una imagen para el logo. Si no se sube ninguna, se usará el icono por defecto.</div>
                        </div>

                        @if(setting('app_logo_image'))
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted d-block">Logo Actual</label>
                                <div class="bg-light p-2 rounded-3 d-inline-block">
                                    <img src="{{ asset('storage/' . setting('app_logo_image')) }}" alt="Logo" style="height: 40px;">
                                </div>
                            </div>
                        @endif

                        <hr class="my-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Color Primario</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="color_primary" class="form-control form-control-color border-0 p-0" value="{{ setting('color_primary', '#c05a1e') }}" title="Elige el color primario">
                                <span class="text-muted small">{{ setting('color_primary', '#c05a1e') }}</span>
                            </div>
                            <div class="form-text">Color usado en botones, iconos y elementos resaltados.</div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Company Settings -->
            <div class="col-lg-6">
                <div class="card shadow-sm rounded-4 border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fa-solid fa-building text-success"></i>
                        </div>
                        <h6 class="m-0 font-weight-bold text-success">Datos de la Empresa</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Nombre Legal / Razón Social</label>
                                <input type="text" name="empresa_nombre" class="form-control rounded-3" value="{{ setting('empresa_nombre') }}">
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">ID Fiscal (NIT/RUT)</label>
                                <input type="text" name="empresa_id_fiscal" class="form-control rounded-3" value="{{ setting('empresa_id_fiscal') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Dirección Física</label>
                            <input type="text" name="empresa_direccion" class="form-control rounded-3" value="{{ setting('empresa_direccion') }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Teléfono de Contacto</label>
                                <input type="text" name="empresa_telefono" class="form-control rounded-3" value="{{ setting('empresa_telefono') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Email Corporativo</label>
                                <input type="email" name="empresa_email" class="form-control rounded-3" value="{{ setting('empresa_email') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Sitio Web</label>
                            <input type="text" name="empresa_web" class="form-control rounded-3" value="{{ setting('empresa_web') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 text-end mb-5">
                <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
