@php
    $primary = config('mills.primary_color', '#002642');
    $bg = config('mills.background_color', '#d7ecff');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Account') – {{ config('app.name') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        .mills-bg { background-color: {{ $bg }}; }
        .mills-primary { color: {{ $primary }}; }
        .mills-primary-bg { background-color: {{ $primary }}; }
    </style>
</head>
<body class="min-h-screen mills-bg text-slate-900">
    <header class="sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-slate-200/60 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('account.dashboard') }}" class="text-xl font-bold mills-primary tracking-tight">{{ config('app.name') }}</a>
                <a href="{{ route('account.dashboard') }}" class="text-sm text-slate-600 hover:text-slate-900">← My account</a>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm font-medium text-slate-600 hover:text-slate-900">Log out</button>
            </form>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
        @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-800 text-sm border border-red-200">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
