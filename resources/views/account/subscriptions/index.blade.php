@extends('layouts.account')

@section('title', 'Subscriptions')

@section('content')
<h1 class="text-2xl font-bold text-slate-800 mb-2">Subscriptions</h1>
<p class="text-slate-600 text-sm mb-6">Manage your active subscriptions and delivery schedule.</p>

@if(empty($subscriptions))
    <x-card class="p-8">
        <x-empty-state title="No subscriptions" />
    </x-card>
@else
    <div class="space-y-4">
        @foreach($subscriptions as $sub)
            <x-card class="p-5 hover:shadow-md transition">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold text-slate-800">{{ $sub['product_title'] ?? 'Subscription' }}</p>
                        <p class="text-sm text-slate-500">{{ $sub['variant_title'] ?? '' }} · Qty {{ $sub['quantity'] ?? 1 }}</p>
                        <p class="text-sm text-slate-500 mt-1">
                            Next charge: @if(isset($sub['next_charge_scheduled_at'])){{ \Carbon\Carbon::parse($sub['next_charge_scheduled_at'])->format('M j, Y') }}@else—@endif
                            · {{ $sub['order_interval_frequency'] ?? '?' }} {{ $sub['order_interval_unit'] ?? 'month' }}(s)
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-badge :variant="($sub['status'] ?? '') === 'active' ? 'success' : (($sub['status'] ?? '') === 'cancelled' ? 'error' : 'default')">
                            {{ $sub['status'] ?? '—' }}
                        </x-badge>
                        <span class="font-medium text-slate-800">{{ $sub['price'] ?? '—' }} {{ $sub['currency'] ?? 'USD' }}</span>
                        <a href="{{ route('account.subscriptions.show', $sub['id']) }}" class="text-violet-600 text-sm font-medium hover:underline">Manage</a>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
@endif
@endsection
