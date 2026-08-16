@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Reportes Generales</h1>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Reservas</h5>
                    <p class="card-text fs-2">{{ $totalReservations }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Ingresos Totales</h5>
                    <p class="card-text fs-2">${{ number_format($totalIncome, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Gastos Totales</h5>
                    <p class="card-text fs-2">${{ number_format($totalExpenses, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Balance Neto</h5>
                    <p class="card-text fs-2">${{ number_format($balance, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted">Próximamente: Gráficos y filtros avanzados por fecha, categoría y estado.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection