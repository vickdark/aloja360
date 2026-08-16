@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-receipt text-primary me-2"></i> Gestión de Gastos
        </h1>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Registrar Gasto
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Gastos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Concepto</th>
                            <th>Alojamiento</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $expense->title }}</div>
                                <div class="small text-muted">{{ $expense->expenseCategory->name ?? $expense->category->label() }}</div>
                            </td>
                            <td>{{ $expense->accommodation->name ?? 'Gasto General' }}</td>
                            <td class="fw-bold text-danger">-${{ number_format($expense->amount, 2) }} {{ $expense->currency }}</td>
                            <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                            <td>{{ $expense->supplier ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este gasto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-receipt fa-2x mb-3"></i>
                                <p>No hay gastos registrados.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
