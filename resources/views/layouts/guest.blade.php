<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --bs-primary: {{ setting('color_primary', '#c05a1e') }};
                --bs-primary-rgb: {{ implode(',', sscanf(setting('color_primary', '#c05a1e'), "#%02x%02x%02x")) }};
            }

            .btn-primary, .btn-brand {
                background-color: var(--bs-primary) !important;
                border-color: var(--bs-primary) !important;
                color: #fff !important;
            }
            .btn-primary:hover, .btn-brand:hover {
                background-color: color-mix(in srgb, var(--bs-primary), black 15%) !important;
                border-color: color-mix(in srgb, var(--bs-primary), black 15%) !important;
            }
            .text-primary { color: var(--bs-primary) !important; }
            .bg-primary { background-color: var(--bs-primary) !important; }
            body {
                background-color: #f0f4f8; /* Fondo suave del diseño de referencia */
            }
        </style>
    </head>
    <body class="d-flex align-items-center min-vh-100">
        <main class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                    @yield('header')
                    <div class="p-4 p-md-5 bg-white border-0 rounded-4 shadow-lg mt-4">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
