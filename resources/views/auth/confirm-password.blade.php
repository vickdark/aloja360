@extends('layouts.guest')

@section('content')
    <div class="mb-4">
        <h1 class="h4 fw-semibold">Confirmar contrasena</h1>
        <p class="text-secondary mb-0">Confirma tu contrasena para continuar.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="password">Contrasena</label>
            <input
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                type="password"
                name="password"
                required
                autofocus
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-brand text-white w-100" type="submit">Confirmar</button>
    </form>
@endsection
