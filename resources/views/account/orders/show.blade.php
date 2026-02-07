@php
    $productImages = config('mills.product_images', []);
    $defaultProductImage = $productImages['default'] ?? '/images/placeholder-product.svg';
@endphp
@extends('layouts.account')

@section('title', 'Order #' . ($order['id'] ?? ''))

@section('content')
<div class="mb-6">
    <a href="{{ route('account.dashboard') }}#orders" class="mills-primary text-sm font-medium hover:underline">&larr; Back to my account</a>
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-6">
        <h1 class="text-2xl font-bold mills-primary mb-4">Order #{{ $order['id'] ?? $order['external_order_id']['order_id'] ?? '—' }}</h1>
        <p class="text-slate-600 text-sm mb-6">
            {{ isset($order['processed_at']) ? \Carbon\Carbon::parse($order['processed_at'])->format('F j, Y') : '' }}
            · <x-badge :variant="($order['status'] ?? '') === 'success' ? 'success' : 'default'">{{ $order['status'] ?? '—' }}</x-badge>
        </p>

        <h2 class="font-semibold mills-primary mb-3">Line items</h2>
        <ul class="space-y-4">
            @foreach($order['line_items'] ?? [] as $item)
                @php
                    $img = $item['images'][0]['src'] ?? $item['image']['src'] ?? null;
                    if (!$img) {
                        $title = $item['title'] ?? '';
                        foreach ($productImages as $key => $url) {
                            if ($key !== 'default' && $title && stripos($title, $key) !== false) { $img = $url; break; }
                        }
                        $img = $img ?? $defaultProductImage;
                    }
                @endphp
                <li class="flex gap-4 items-center py-3 border-b border-slate-100 last:border-0">
                    <div class="w-16 h-16 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0">
                        <img src="{{ $img }}" alt="" class="w-full h-full object-cover" onerror="this.src='{{ $defaultProductImage }}'; this.onerror=null;">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-slate-800">{{ $item['title'] ?? 'Item' }} × {{ $item['quantity'] ?? 1 }}</p>
                        <p class="text-sm text-slate-500">{{ $item['variant_title'] ?? '' }}</p>
                    </div>
                    <span class="font-medium text-slate-800">{{ $item['price'] ?? '—' }} {{ $order['currency'] ?? 'USD' }}</span>
                </li>
            @endforeach
        </ul>

        <div class="border-t border-slate-200 mt-4 pt-4 flex justify-between font-semibold">
            <span>Total</span>
            <span>{{ $order['total_price'] ?? '—' }} {{ $order['currency'] ?? 'USD' }}</span>
        </div>

        @if(!empty($order['shipping_address']))
            <div class="border-t border-slate-200 mt-4 pt-4">
                <h2 class="font-semibold mills-primary mb-2">Shipping</h2>
                <p class="text-sm text-slate-600">
                    {{ $order['shipping_address']['address1'] ?? '' }}<br>
                    {{ $order['shipping_address']['city'] ?? '' }}, {{ $order['shipping_address']['province'] ?? '' }} {{ $order['shipping_address']['zip'] ?? '' }}<br>
                    {{ $order['shipping_address']['country_code'] ?? '' }}
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
