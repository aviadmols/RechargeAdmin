@push('styles')
@include('account.orders.partials.order-summary-styles')
@endpush
@extends('layouts.account')

@section('title', 'Orders')

@section('content')
<div class="order-summary">
    <a href="{{ route('account.dashboard') }}#orders" class="order-summary-back">
        <span>←</span>
        <span>Back to my account</span>
    </a>
    <div class="order-summary-header">
        <h1 class="order-summary-title">Order history</h1>
        <p class="order-summary-subtitle">View and filter your orders.</p>
    </div>

    <form method="GET" action="{{ route('account.orders.index') }}" class="order-filters">
        <input type="text" name="status" value="{{ request('status') }}" placeholder="Status">
        <input type="date" name="created_at_min" value="{{ request('created_at_min') }}">
        <input type="date" name="created_at_max" value="{{ request('created_at_max') }}">
        <button type="submit">Filter</button>
    </form>

    @if(empty($orders))
        <ul class="order-items-list">
            <li class="order-empty">No orders yet.</li>
        </ul>
    @else
        <ul class="order-items-list">
            @foreach($orders as $order)
                @php
                    $orderId = $order['id'] ?? $order['external_order_id']['order_id'] ?? '—';
                    $date = isset($order['processed_at']) ? \Carbon\Carbon::parse($order['processed_at'])->format('M j, Y') : (isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('M j, Y') : '—');
                    $status = $order['status'] ?? '—';
                    $total = $order['total_price'] ?? '—';
                    $currency = $order['currency'] ?? 'USD';
                    $badgeClass = $status === 'success' ? 'order-badge-success' : ($status === 'error' ? 'order-badge-error' : 'order-badge-default');
                @endphp
                <a href="{{ route('account.orders.show', $order['id']) }}" class="order-item">
                    <div class="order-item-checkbox">
                        <x-icon-check />
                    </div>
                    <div class="order-item-content">
                        <div class="order-item-title">
                            Order #{{ $orderId }}
                            <span class="order-badge {{ $badgeClass }}">{{ $status }}</span>
                        </div>
                        <div class="order-item-meta">{{ $date }}</div>
                    </div>
                    <div class="order-item-right">@if(isset($total) && $total !== '' && $total !== null && $total !== '—'){{ $total }} {{ $currency }}@else Free @endif</div>
                </a>
            @endforeach
        </ul>

        @if(isset($nextCursor) || isset($prevCursor))
            <nav class="order-nav">
                @if(!empty($prevCursor))
                    <a href="{{ route('account.orders.index', ['cursor' => $prevCursor] + request()->only('status', 'created_at_min', 'created_at_max')) }}">← Previous</a>
                @endif
                @if(!empty($nextCursor))
                    <a href="{{ route('account.orders.index', ['cursor' => $nextCursor] + request()->only('status', 'created_at_min', 'created_at_max')) }}">Next →</a>
                @endif
            </nav>
        @endif
    @endif
</div>
@endsection
