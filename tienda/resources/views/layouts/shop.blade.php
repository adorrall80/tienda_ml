<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? '' }}">
    <meta name="app-url" content="{{ url('/') }}">
    @vite(['resources/css/shop.css', 'resources/js/shop.js'])
    {{ $head ?? '' }}
</head>
<body>

<x-layout.mobile-nav />

<x-layout.header />

<x-layout.nav-categorias />

<main>
    {{ $slot }}
</main>

<x-layout.footer />

<div class="toast-container"></div>

</body>
</html>
