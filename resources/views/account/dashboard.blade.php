@extends('layouts.account')

@section('title', 'Dashboard')

@section('content')
{{-- Welcome headline – user friendly --}}
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1">Let's make a perfect match</h1>
    <p class="text-slate-600">Welcome back. Here’s your account at a glance.</p>
</div>

{{-- Promoted products – horizontal scroll cards (like image + Mills) --}}
@if(!empty($promotedProducts))
<section class="mb-10">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-slate-800">Featured</h2>
        <span class="text-sm text-slate-500">View all</span>
    </div>
    <div class="flex gap-4 overflow-x-auto pb-2 -mx-1" style="scrollbar-width: none; -ms-overflow-style: none;">
        @foreach($promotedProducts as $promo)
        @php
            $bg = match($promo['accent'] ?? 'violet') {
                'pink' => 'bg-[#fce7f3]',
                'emerald' => 'bg-[#d1fae5]',
                default => 'bg-violet-50',
            };
            $btn = match($promo['accent'] ?? 'violet') {
                'pink' => 'bg-pink-500 hover:bg-pink-600',
                'emerald' => 'bg-emerald-500 hover:bg-emerald-600',
                default => 'bg-violet-600 hover:bg-violet-700',
            };
        @endphp
        <a href="{{ $promo['link'] ?? '#' }}" class="flex-shrink-0 w-[280px] sm:w-[300px] rounded-2xl {{ $bg }} border border-white/60 p-5 shadow-sm hover:shadow-md transition block">
            <h3 class="font-semibold text-slate-800 mb-1">{{ $promo['title'] }}</h3>
            <p class="text-sm text-slate-600 mb-4">{{ $promo['description'] ?? '' }}</p>
            <span class="inline-flex items-center rounded-xl {{ $btn }} text-white text-sm font-medium px-4 py-2">{{ $promo['cta'] ?? 'Learn more' }}</span>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- Your overview – stat cards (clean, rounded) --}}
<h2 class="text-lg font-semibold text-slate-800 mb-4">Your overview</h2>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-violet-100/80 p-5 shadow-sm">
        <p class="text-slate-500 text-sm">Active subscriptions</p>
        <p class="text-2xl font-bold text-violet-800 mt-1">{{ $activeSubscriptionsCount }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-violet-100/80 p-5 shadow-sm">
        <p class="text-slate-500 text-sm">Next charge</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ $nextChargeDate ? \Carbon\Carbon::parse($nextChargeDate)->format('M j, Y') : '—' }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-violet-100/80 p-5 shadow-sm">
        <p class="text-slate-500 text-sm">Orders</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">{{ $ordersCount }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-violet-100/80 p-5 shadow-sm">
        <p class="text-slate-500 text-sm">Last order</p>
        <p class="text-lg font-semibold text-slate-800 mt-1">
            @if($lastOrder)
                {{ $lastOrder['status'] ?? '—' }} · {{ isset($lastOrder['processed_at']) ? \Carbon\Carbon::parse($lastOrder['processed_at'])->format('M j') : '—' }}
            @else
                —
            @endif
        </p>
    </div>
</div>

{{-- Quick actions – primary CTA style (Mills) --}}
<div class="flex flex-wrap gap-3">
    <a href="{{ route('account.orders.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 text-white font-medium px-5 py-2.5 hover:bg-violet-700 transition shadow-sm">View orders</a>
    <a href="{{ route('account.subscriptions.index') }}" class="inline-flex items-center gap-2 rounded-xl border-2 border-violet-200 text-violet-700 font-medium px-5 py-2.5 hover:bg-violet-50 transition">View subscriptions</a>
</div>
@endsection
