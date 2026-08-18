@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-money-bill-wave text-primary me-2"></i> Gestión de Pagos
        </h1>
        <a href="{{ route('payments.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Registrar Pago
        </a>
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
    window.initPaymentsIndex({
        routes: {
            index: '{{ route('payments.index') }}',
            show: '{{ route('payments.show', ':id') }}',
            edit: '{{ route('payments.edit', ':id') }}',
            show_reservation: '{{ route('reservations.show', ':id') }}'
        },
        tokens: {
            csrf: '{{ csrf_token() }}'
        }
    });
});
</script>
@endpush
