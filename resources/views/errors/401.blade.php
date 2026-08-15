@extends('layouts.guest')

@section('content')
    <div class="text-center">
        <h1 class="display-1 font-weight-bold text-danger">401</h1>
        <p class="h3 mb-3">No Autorizado</p>
        <p class="mb-4">Lo sentimos, no estás autorizado para acceder a esta página.</p>
        <a href="/" class="btn btn-primary">Volver al inicio</a>
    </div>
@endsection