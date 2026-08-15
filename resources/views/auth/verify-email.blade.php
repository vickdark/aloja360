@extends('layouts.guest')

@section('content')
    <div class="mb-4">
        <h1 class="h4 fw-semibold">Verifica tu email</h1>
        <p class="text-secondary mb-0">
            Te enviamos un enlace de verificacion. Revisa tu correo para continuar.
        </p>
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success">
            Se envio un nuevo enlace de verificacion.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="btn btn-brand text-white w-100" type="submit">Reenviar enlace</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button class="btn btn-outline-secondary w-100" type="submit">Cerrar sesion</button>
    </form>
@endsection
