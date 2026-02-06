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
</head>
<body class="min-h-screen bg-slate-50 text-slate-900" x-data="{ toast: null }">
    <nav class="sticky top-0 z-10 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('account.dashboard') }}" class="font-semibold text-slate-800">{{ config('app.name') }}</a>
            <div class="flex items-center gap-4">
                <a href="{{ route('account.dashboard') }}" class="text-sm text-slate-600 hover:text-slate-900">Dashboard</a>
                <a href="{{ route('account.orders.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Orders</a>
                <a href="{{ route('account.subscriptions.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Subscriptions</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-slate-600 hover:text-slate-900">Log out</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="max-w-5xl mx-auto px-4 py-8">
        @if (session('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 text-sm" x-data x-init="setTimeout(() => $el.remove(), 4000)">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
    <div x-show="toast" x-cloak class="fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow-lg bg-slate-800 text-white text-sm" x-transition></div>
</body>
</html>
