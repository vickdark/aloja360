@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
            <i class="fa-solid fa-boxes-stacked text-primary me-2"></i> Inventario
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('inventory.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nuevo Ítem
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initInventoryIndex({
            routes: {
                index: "{{ route('inventory.index') }}",
                show: "{{ route('inventory.show', ':id') }}",
                edit: "{{ route('inventory.edit', ':id') }}",
                destroy: "{{ route('inventory.destroy', ':id') }}"
            },
            tokens: {
                csrf: "{{ csrf_token() }}"
            }
        });
    });
</script>
@endsection
