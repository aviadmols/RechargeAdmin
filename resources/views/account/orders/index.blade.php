@extends('layouts.account')

@section('title', 'Orders')

@section('content')
<div class="mb-6">
    <a href="{{ route('account.dashboard') }}#orders" class="mills-primary text-sm font-medium hover:underline">&larr; Back to my account</a>
</div>
<h1 class="text-2xl font-bold mills-primary mb-2">Order history</h1>
<p class="text-slate-600 text-sm mb-6">View and filter your orders.</p>

<form method="GET" action="{{ route('account.orders.index') }}" class="flex flex-wrap gap-3 mb-6">
    <input type="text" name="status" value="{{ request('status') }}" placeholder="Status" class="rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
    <input type="date" name="created_at_min" value="{{ request('created_at_min') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
    <input type="date" name="created_at_max" value="{{ request('created_at_max') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
    <button type="submit" class="rounded-xl mills-primary-bg text-white px-4 py-2 text-sm font-medium hover:opacity-90">Filter</button>
</form>

@if(empty($orders))
    <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center text-slate-600">No orders yet.</div>
@else
    <div class="space-y-4">
        @foreach($orders as $order)
            <a href="{{ route('account.orders.show', $order['id']) }}" class="block bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold text-slate-800">Order #{{ $order['id'] ?? $order['external_order_id']['order_id'] ?? '—' }}</p>
                        <p class="text-sm text-slate-500">
                            {{ isset($order['processed_at']) ? \Carbon\Carbon::parse($order['processed_at'])->format('M j, Y') : (isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('M j, Y') : '—') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-badge :variant="($order['status'] ?? '') === 'success' ? 'success' : (($order['status'] ?? '') === 'error' ? 'error' : 'default')">
                            {{ $order['status'] ?? '—' }}
                        </x-badge>
                        <span class="font-medium text-slate-800">{{ $order['total_price'] ?? '—' }} {{ $order['currency'] ?? 'USD' }}</span>
                        <span class="mills-primary text-sm font-medium">View →</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    @if(isset($nextCursor) || isset($prevCursor))
        <div class="mt-6 flex justify-center gap-4">
            @if(!empty($prevCursor))
                <a href="{{ route('account.orders.index', ['cursor' => $prevCursor] + request()->only('status', 'created_at_min', 'created_at_max')) }}" class="mills-primary font-medium hover:underline">Previous</a>
            @endif
            @if(!empty($nextCursor))
                <a href="{{ route('account.orders.index', ['cursor' => $nextCursor] + request()->only('status', 'created_at_min', 'created_at_max')) }}" class="mills-primary font-medium hover:underline">Next</a>
            @endif
        </div>
    @endif
@endif
@endsection
