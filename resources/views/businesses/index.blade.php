@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-building text-primary me-2"></i> Gestión de Negocios
        </h1>
        @can('create', App\Models\Business::class)
        <a href="{{ route('businesses.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Crear Negocio
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
    window.initBusinessesIndex({
        routes: {
            index: '{{ route('businesses.index') }}',
            show: '{{ route('businesses.show', ':id') }}',
            edit: '{{ route('businesses.edit', ':id') }}'
        },
        tokens: {
            csrf: '{{ csrf_token() }}'
        }
    });
});
</script>
@endpush
