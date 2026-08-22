@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-receipt text-primary me-2"></i> Gestión de Gastos
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('expense_categories.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-tags me-1"></i> Categorías
            </a>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Registrar Gasto
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
    window.initExpensesIndex({
        routes: {
            index: '{{ route('expenses.index') }}',
            edit: '{{ route('expenses.edit', ':id') }}',
            destroy: '{{ route('expenses.destroy', ':id') }}'
        },
        tokens: {
            csrf: '{{ csrf_token() }}'
        }
    });
});
</script>
@endpush
