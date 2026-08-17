<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $primary = setting('color_primary', '#c05a1e');
        $hex = ltrim($primary, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = $g = $b = 0;
        if (strlen($hex) === 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $primaryRgb = "$r, $g, $b";
    @endphp
    <style>
        html :root {
            --bs-primary: {{ $primary }};
            --bs-primary-rgb: {{ $primaryRgb }};
        }

        body {
            --bs-primary: {{ $primary }};
            --bs-primary-rgb: {{ $primaryRgb }};
        }

        .btn-primary {
            --bs-btn-bg: var(--bs-primary);
            --bs-btn-border-color: var(--bs-primary);
            --bs-btn-hover-bg: color-mix(in srgb, var(--bs-primary), black 15%);
            --bs-btn-hover-border-color: color-mix(in srgb, var(--bs-primary), black 15%);
            --bs-btn-active-bg: color-mix(in srgb, var(--bs-primary), black 20%);
        }
    </style>
    @stack('styles')
</head>
<body class="app-shell">
<div class="sidebar-overlay" onclick="document.body.classList.remove('sidebar-open')"></div>
<div class="app-layout">
    @include('partials.aside')
    <main class="app-main">
        @include('partials.navbar')
        <div class="app-content">
