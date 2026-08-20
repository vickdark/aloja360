@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
            <i class="fa-solid fa-wand-magic-sparkles text-primary me-2"></i> Gestión de Amenidades
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('accommodations.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm" title="Volver a Alojamientos">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver a Alojamientos
            </a>
            <a href="{{ route('amenities.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nueva Amenidad
            </a>
        </div>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.initAmenitiesIndex) {
            window.initAmenitiesIndex({
                routes: {
                    index: "{{ route('amenities.index') }}",
                    show: "{{ route('amenities.show', ':id') }}",
                    edit: "{{ route('amenities.edit', ':id') }}",
                    destroy: "{{ route('amenities.destroy', ':id') }}"
                },
                tokens: {
                    csrf: "{{ csrf_token() }}"
                }
            });
        }
    });
</script>
@endpush

