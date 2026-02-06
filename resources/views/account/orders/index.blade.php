@extends('layouts.account')

@section('title', 'Orders')

@section('content')
<h1 class="text-2xl font-semibold text-slate-800 mb-6">Order history</h1>

<form method="GET" action="{{ route('account.orders.index') }}" class="flex flex-wrap gap-4 mb-6">
    <input type="text" name="status" value="{{ request('status') }}" placeholder="Status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
    <input type="date" name="created_at_min" value="{{ request('created_at_min') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
    <input type="date" name="created_at_max" value="{{ request('created_at_max') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
    <button type="submit" class="rounded-lg bg-slate-800 text-white px-4 py-2 text-sm font-medium">Filter</button>
</form>

@if(empty($orders))
    <x-card>
        <x-empty-state title="No orders yet" />
    </x-card>
@else
    <div class="space-y-4">
        @foreach($orders as $order)
            <x-card class="p-5">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="font-medium text-slate-800">Order #{{ $order['id'] ?? $order['external_order_id']['order_id'] ?? '—' }}</p>
                        <p class="text-sm text-slate-500">
                            {{ isset($order['processed_at']) ? \Carbon\Carbon::parse($order['processed_at'])->format('M j, Y') : (isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('M j, Y') : '—') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-badge :variant="($order['status'] ?? '') === 'success' ? 'success' : (($order['status'] ?? '') === 'error' ? 'error' : 'default')">
                            {{ $order['status'] ?? '—' }}
                        </x-badge>
                        <span class="font-medium text-slate-800">{{ $order['total_price'] ?? '—' }} {{ $order['currency'] ?? 'USD' }}</span>
                        <a href="{{ route('account.orders.show', $order['id']) }}" class="text-indigo-600 text-sm font-medium hover:underline">View</a>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>

    @if($nextCursor || $prevCursor)
        <div class="mt-6 flex justify-center gap-4">
            @if($prevCursor)
                <a href="{{ route('account.orders.index', ['cursor' => $prevCursor] + request()->only('status', 'created_at_min', 'created_at_max')) }}" class="text-indigo-600 hover:underline">Previous</a>
            @endif
            @if($nextCursor)
                <a href="{{ route('account.orders.index', ['cursor' => $nextCursor] + request()->only('status', 'created_at_min', 'created_at_max')) }}" class="text-indigo-600 hover:underline">Next</a>
            @endif
        </div>
    @endif
@endif
@endsection
