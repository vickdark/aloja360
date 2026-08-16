@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Detalles del Gasto #{{ $expense->id }}</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="w-25">Título</th>
                                <td>{{ $expense->title }}</td>
                            </tr>
                            <tr>
                                <th>Monto</th>
                                <td>{{ number_format($expense->amount, 2) }} {{ $expense->currency }}</td>
                            </tr>
                            <tr>
                                <th>Categoría</th>
                                <td>
                                    @if($expense->expenseCategory)
                                        <span class="badge bg-info">{{ $expense->expenseCategory->name }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Alojamiento</th>
                                <td>
                                    @if($expense->accommodation)
                                        {{ $expense->accommodation->name }}
                                    @else
                                        <span class="text-muted">No asignado</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Fecha</th>
                                <td>{{ $expense->expense_date ? $expense->expense_date->format('d/m/Y') : 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="w-25">Método de Pago</th>
                                <td>{{ $expense->payment_method ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Nº Factura</th>
                                <td>{{ $expense->invoice_number ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Proveedor</th>
                                <td>{{ $expense->supplier ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Registrado por</th>
                                <td>
                                    @if($expense->creator)
                                        {{ $expense->creator->nombre }}
                                    @else
                                        <span class="text-muted">Desconocido</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Creado en</th>
                                <td>{{ $expense->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            @if($expense->description)
                <div class="mt-4">
                    <h5>Descripción</h5>
                    <div class="p-3 bg-light border rounded">
                        {{ $expense->description }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection