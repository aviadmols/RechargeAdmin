@php
    $productImages = config('mills.product_images', []);
    $defaultProductImage = $productImages['default'] ?? '/images/placeholder-product.svg';
@endphp
@push('styles')
@include('account.orders.partials.order-summary-styles')
@endpush
@extends('layouts.account')

@section('title', 'Order #' . ($order['id'] ?? ''))

@section('content')
<div class="order-summary">
    <a href="{{ route('account.orders.index') }}" class="order-summary-back">
        <span>←</span>
        <span>Back to order history</span>
    </a>
    <div class="order-summary-header">
        <h1 class="order-summary-title">Order #{{ $order['id'] ?? $order['external_order_id']['order_id'] ?? '—' }}</h1>
        <p class="order-summary-subtitle">
            {{ isset($order['processed_at']) ? \Carbon\Carbon::parse($order['processed_at'])->format('F j, Y') : '' }}
            · <span class="order-badge {{ ($order['status'] ?? '') === 'success' ? 'order-badge-success' : 'order-badge-default' }}">{{ $order['status'] ?? '—' }}</span>
        </p>
    </div>

    <ul class="order-items-list">
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
                $title = $item['title'] ?? 'Item';
                $qty = $item['quantity'] ?? 1;
                $variant = $item['variant_title'] ?? '';
                $price = $item['price'] ?? '—';
                $currency = $order['currency'] ?? 'USD';
            @endphp
            <li class="order-item" style="cursor: default;">
                <div class="order-item-checkbox">
                    <x-icon-check />
                </div>
                <img src="{{ $img }}" alt="" class="order-item-image" onerror="this.src='{{ $defaultProductImage }}'; this.onerror=null;">
                <div class="order-item-content">
                    <div class="order-item-title">{{ $title }} × {{ $qty }}</div>
                    @if($variant)
                        <div class="order-item-meta">{{ $variant }}</div>
                    @endif
                </div>
                <div class="order-item-right">@if(isset($price) && $price !== '' && $price !== null && $price !== '—'){{ $price }} {{ $currency }}@else Free @endif</div>
            </li>
        @endforeach
    </ul>

    <div class="order-summary-price-section">
        <div class="order-summary-total">
            <span class="order-summary-total-label">Your total</span>
            <span class="order-summary-total-current">{{ $order['total_price'] ?? '—' }} {{ $order['currency'] ?? 'USD' }}</span>
        </div>
    </div>

    @if(!empty($order['shipping_address']))
        <div class="order-summary-shipping">
            <h3>Shipping</h3>
            <p>
                {{ $order['shipping_address']['address1'] ?? '' }}<br>
                {{ $order['shipping_address']['city'] ?? '' }}, {{ $order['shipping_address']['province'] ?? '' }} {{ $order['shipping_address']['zip'] ?? '' }}<br>
                {{ $order['shipping_address']['country_code'] ?? '' }}
            </p>
        </div>
    @endif
</div>
@endsection
