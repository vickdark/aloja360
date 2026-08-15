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

        .btn-primary {
            --bs-btn-bg: var(--bs-primary);
            --bs-btn-border-color: var(--bs-primary);
            --bs-btn-hover-bg: color-mix(in srgb, var(--bs-primary), black 15%);
            --bs-btn-hover-border-color: color-mix(in srgb, var(--bs-primary), black 15%);
            --bs-btn-active-bg: color-mix(in srgb, var(--bs-primary), black 20%);
        }

        .text-primary { color: var(--bs-primary) !important; }
        .bg-primary { background-color: var(--bs-primary) !important; }
    </style>
</head>
<body class="app-shell">
<div class="sidebar-overlay" onclick="document.body.classList.remove('sidebar-open')"></div>
<div class="app-layout">
    @include('partials.aside')
    <main class="app-main">
        @include('partials.navbar')
        <div class="app-content">
