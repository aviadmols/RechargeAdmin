@extends('layouts.account')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Dashboard</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <x-card class="p-5">
        <p class="text-slate-500 text-sm">Active subscriptions</p>
        <p class="text-2xl font-semibold text-slate-800 mt-1">{{ $activeSubscriptionsCount }}</p>
    </x-card>
    <x-card class="p-5">
        <p class="text-slate-500 text-sm">Next charge</p>
        <p class="text-2xl font-semibold text-slate-800 mt-1">{{ $nextChargeDate ? \Carbon\Carbon::parse($nextChargeDate)->format('M j, Y') : '—' }}</p>
    </x-card>
    <x-card class="p-5">
        <p class="text-slate-500 text-sm">Orders</p>
        <p class="text-2xl font-semibold text-slate-800 mt-1">{{ $ordersCount }}</p>
    </x-card>
    <x-card class="p-5">
        <p class="text-slate-500 text-sm">Last order</p>
        <p class="text-lg font-semibold text-slate-800 mt-1">
            @if($lastOrder)
                {{ $lastOrder['status'] ?? '—' }} · {{ isset($lastOrder['processed_at']) ? \Carbon\Carbon::parse($lastOrder['processed_at'])->format('M j') : '—' }}
            @else
                —
            @endif
        </p>
    </x-card>
</div>

<div class="flex flex-wrap gap-4">
    <a href="{{ route('account.orders.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700">View orders</a>
    <a href="{{ route('account.subscriptions.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-300 text-slate-700 font-medium hover:bg-slate-50">View subscriptions</a>
</div>
@endsection
