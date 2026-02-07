@extends('layouts.account')

@section('title', 'Order #' . ($order['id'] ?? ''))

@section('content')
<div class="mb-6">
    <a href="{{ route('account.orders.index') }}" class="text-violet-600 text-sm font-medium hover:underline">&larr; Back to orders</a>
</div>

<x-card class="p-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-4">Order #{{ $order['id'] ?? $order['external_order_id']['order_id'] ?? '—' }}</h1>
    <p class="text-slate-600 text-sm mb-6">
        {{ isset($order['processed_at']) ? \Carbon\Carbon::parse($order['processed_at'])->format('F j, Y') : '' }}
        · <x-badge :variant="($order['status'] ?? '') === 'success' ? 'success' : 'default'">{{ $order['status'] ?? '—' }}</x-badge>
    </p>

    <div class="border-t border-slate-200 pt-4">
        <h2 class="font-semibold text-slate-800 mb-2">Line items</h2>
        <ul class="space-y-2">
            @foreach($order['line_items'] ?? [] as $item)
                <li class="flex justify-between text-sm">
                    <span>{{ $item['title'] ?? 'Item' }} × {{ $item['quantity'] ?? 1 }}</span>
                    <span>{{ $item['price'] ?? '—' }} {{ $order['currency'] ?? 'USD' }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="border-t border-slate-200 mt-4 pt-4 flex justify-between font-semibold">
        <span>Total</span>
        <span>{{ $order['total_price'] ?? '—' }} {{ $order['currency'] ?? 'USD' }}</span>
    </div>

    @if(!empty($order['shipping_address']))
        <div class="border-t border-slate-200 mt-4 pt-4">
            <h2 class="font-semibold text-slate-800 mb-2">Shipping</h2>
            <p class="text-sm text-slate-600">
                {{ $order['shipping_address']['address1'] ?? '' }}<br>
                {{ $order['shipping_address']['city'] ?? '' }}, {{ $order['shipping_address']['province'] ?? '' }} {{ $order['shipping_address']['zip'] ?? '' }}<br>
                {{ $order['shipping_address']['country_code'] ?? '' }}
            </p>
        </div>
    @endif
</x-card>
@endsection
