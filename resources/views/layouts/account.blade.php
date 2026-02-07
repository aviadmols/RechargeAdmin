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
<body class="min-h-screen bg-[#f5f1f8] text-slate-900" x-data="{ toast: null }">
    {{-- Header: Mills style – clean, minimal --}}
    <header class="sticky top-0 z-20 bg-white/95 backdrop-blur border-b border-violet-100/60 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="{{ route('account.dashboard') }}" class="text-xl font-bold text-violet-800 tracking-tight">{{ config('app.name') }}</a>
            <nav class="flex items-center gap-1 sm:gap-4">
                <a href="{{ route('account.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-violet-700 hover:bg-violet-50 transition">Dashboard</a>
                <a href="{{ route('account.orders.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-violet-700 hover:bg-violet-50 transition">Orders</a>
                <a href="{{ route('account.subscriptions.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-violet-700 hover:bg-violet-50 transition">Subscriptions</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition">Log out</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200" x-data x-init="setTimeout(() => $el.remove(), 4000)">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-800 text-sm border border-red-200">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    {{-- Mobile bottom nav (optional – same links as header) --}}
    <nav class="fixed bottom-0 left-0 right-0 z-10 sm:hidden bg-white border-t border-violet-100/60 safe-area-pb">
        <div class="flex justify-around items-center h-14">
            <a href="{{ route('account.dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-violet-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-xs mt-0.5">Home</span>
            </a>
            <a href="{{ route('account.orders.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-slate-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-xs mt-0.5">Orders</span>
            </a>
            <a href="{{ route('account.subscriptions.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-slate-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span class="text-xs mt-0.5">Subs</span>
            </a>
        </div>
    </nav>

    <div class="h-16 sm:hidden" aria-hidden="true"></div>
    <div x-show="toast" x-cloak class="fixed bottom-20 sm:bottom-4 right-4 px-4 py-2 rounded-xl shadow-lg bg-slate-800 text-white text-sm" x-transition></div>
</body>
</html>
