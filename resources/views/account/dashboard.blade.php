@php
    $primary = config('mills.primary_color', '#002642');
    $bg = config('mills.background_color', '#d7ecff');
    $productImages = config('mills.product_images', []);
    $defaultProductImage = $productImages['default'] ?? '/images/placeholder-product.svg';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Account – {{ config('app.name') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        .mills-bg { background-color: {{ $bg }}; }
        .mills-primary { color: {{ $primary }}; }
        .mills-primary-bg { background-color: {{ $primary }}; }
        .mills-border { border-color: {{ $primary }}20; }
    </style>
</head>
<body class="min-h-screen mills-bg text-slate-900" x-data="{ toast: null }">
    {{-- Minimal header: logo + logout --}}
    <header class="sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-slate-200/60 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="{{ route('account.dashboard') }}" class="text-xl font-bold mills-primary tracking-tight">{{ config('app.name') }}</a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm font-medium text-slate-600 hover:text-slate-900">Log out</button>
            </form>
        </div>
    </header>

    {{-- In-page navigation --}}
    <nav class="sticky top-[52px] z-10 bg-white/80 backdrop-blur border-b border-slate-200/60">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-2">
            <div class="flex flex-wrap gap-2 sm:gap-4 justify-center">
                <a href="#next-order" class="px-3 py-1.5 rounded-full text-sm font-medium mills-primary hover:bg-[#00264212]">Next order</a>
                <a href="#products" class="px-3 py-1.5 rounded-full text-sm font-medium mills-primary hover:bg-[#00264212]">Products</a>
                <a href="#featured" class="px-3 py-1.5 rounded-full text-sm font-medium mills-primary hover:bg-[#00264212]">Featured</a>
                <a href="#overview" class="px-3 py-1.5 rounded-full text-sm font-medium mills-primary hover:bg-[#00264212]">Overview</a>
                <a href="#orders" class="px-3 py-1.5 rounded-full text-sm font-medium mills-primary hover:bg-[#00264212]">Orders</a>
                <a href="#subscriptions" class="px-3 py-1.5 rounded-full text-sm font-medium mills-primary hover:bg-[#00264212]">Subscriptions</a>
                <a href="#my-details" class="px-3 py-1.5 rounded-full text-sm font-medium mills-primary hover:bg-[#00264212]">My details</a>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8 pb-24">

    
    <img src="//millsdailypacks.com/cdn/shop/t/104/assets/doggies%20blue%201.svg?v=34908997617189530931769941793" alt="Dogs Illustration" style=" margin-left: auto;  margin-right: auto;">
    
    
    @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200" x-data x-init="setTimeout(() => $el.remove(), 4000)">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-800 text-sm border border-red-200">{{ session('error') }}</div>
        @endif

        {{-- Your next order (prominent) --}}
        <section id="next-order" class="scroll-mt-28 mb-10">
            <div class="rounded-2xl bg-white border-2 border-[#002642]/20  overflow-hidden">
                <div class="p-5 sm:p-6 bg-gradient-to-r from-[#002642]/08 to-transparent">
                    @php $displayName = $user ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) : ''; @endphp
                    <h2 class="text-lg font-bold mills-primary">Your next order@if($displayName !== ''), {{ $displayName }}@endif</h2>
                    <p class="text-sm text-slate-600 mt-0.5">Scheduled delivery date</p>
                    @if($nextChargeDate)
                        @php $nextDate = \Carbon\Carbon::parse($nextChargeDate); @endphp
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 mt-2">{{ $nextDate->format('l, F j, Y') }}</p>
                        <p class="text-sm text-slate-500 mt-1">Your next shipment is scheduled for this date.</p>
                    @else
                        <p class="text-lg font-semibold text-slate-600 mt-2">No upcoming order scheduled</p>
                        <p class="text-sm text-slate-500 mt-1">Add a subscription or product to see your next delivery date here.</p>
                    @endif
                </div>
            </div>
        </section>

        {{-- Section: Products we offer (upsell-style cards from Admin → Subscription products) --}}
        @if($subscriptionProducts->isNotEmpty())
        <section id="products" class="scroll-mt-28 mb-12">
            <h2 class="text-xl font-bold mills-primary mb-4">Our products</h2>
            <div class="flex gap-4 overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none;" role="region" aria-label="Product cards">
                @foreach($subscriptionProducts as $product)
                @php
                    $originalPrice = $product->original_price ? (float) $product->original_price : null;
                    $discountPct = $product->discount_percent ?? 0;
                    $discountedPrice = $originalPrice !== null && $discountPct > 0
                        ? round($originalPrice * (1 - $discountPct / 100), 2)
                        : $originalPrice;
                @endphp
                <div class="upsell-product-card flex-shrink-0 w-[220px] max-w-[90%] bg-white rounded-2xl overflow-hidden  hover:shadow-md transition">
                    <div class="upsell-product-card-image-wrapper relative w-full bg-[#E0E0E0]" style="padding-top: 105%;">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="absolute inset-0 w-full h-full object-cover object-top" loading="lazy" onerror="this.style.display='none'">
                        @endif
                        @if($product->badge_1 || $product->badge_2)
                            <div class="absolute top-3 right-3 flex flex-col gap-2 z-10">
                                @if($product->badge_1)<span class="bg-[#002642] text-white text-xs font-bold px-3 py-1.5 rounded">{{ $product->badge_1 }}</span>@endif
                                @if($product->badge_2)<span class="bg-[#002642] text-white text-xs font-bold px-3 py-1.5 rounded">{{ $product->badge_2 }}</span>@endif
                            </div>
                        @endif
                    </div>
                    <div class="upsell-product-content p-3 text-left">
                        <div class="flex flex-wrap items-baseline justify-between gap-1 mb-0.5">
                            <h3 class="text-lg font-semibold text-[#002642] leading-tight">{{ $product->title }}</h3>
                            <span class="flex items-center gap-1 flex-shrink-0">
                                @if($originalPrice !== null && $discountPct > 0 && $discountedPrice != $originalPrice)
                                    <span class="text-sm text-[#999] line-through">${{ number_format($originalPrice, 2) }}</span>
                                    <span class="text-sm font-semibold text-[#00ad67]">${{ number_format($discountedPrice, 2) }}</span>
                                @elseif($originalPrice !== null)
                                    <span class="text-sm font-semibold text-[#002642]">${{ number_format($originalPrice, 2) }}</span>
                                @else
                                    <span class="text-sm text-slate-500">Every {{ $product->order_interval_frequency }} {{ $product->order_interval_unit }}(s)</span>
                                @endif
                            </span>
                        </div>
                        @if($product->subtitle)
                            <p class="text-sm font-bold text-slate-700 mb-1">{{ $product->subtitle }}</p>
                        @endif
                        @if($product->description)
                            <p class="text-sm text-[#666] leading-snug line-clamp-2 mb-3">{{ Str::limit(strip_tags($product->description), 100) }}</p>
                        @endif
                        <div class="mt-2 pt-2 border-t border-slate-200 flex flex-col gap-2" x-data="{ submitting: false }">
                            <form action="{{ route('account.products.add') }}" method="POST" @submit="submitting = true">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="w-full rounded-full mills-primary-bg text-white text-sm font-semibold py-2.5 px-4 hover:opacity-90 disabled:opacity-70 transition" :disabled="submitting">
                                    <span x-show="!submitting">Add to my box</span>
                                    <span x-show="submitting" x-cloak>Adding…</span>
                                </button>
                            </form>
                            <form action="{{ route('account.products.buy-once') }}" method="POST" @submit="submitting = true">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="w-full rounded-full border-2 border-[#002642] text-[#002642] text-sm font-semibold py-2 px-4 hover:bg-[#002642]/5 disabled:opacity-70 transition" :disabled="submitting">
                                    <span x-show="!submitting">Buy once (no subscription)</span>
                                    <span x-show="submitting" x-cloak>Adding…</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Section: Featured --}}
        @if(!empty($promotedProducts))
        <section id="featured" class="scroll-mt-28 mb-12">
            <h2 class="text-xl font-bold mills-primary mb-4">What's on the menu</h2>
            <div class="flex gap-4 overflow-x-auto pb-2" style="scrollbar-width: none;">
                @foreach($promotedProducts as $promo)
                <a href="{{ $promo['link'] ?? '#' }}" class="flex-shrink-0 w-[260px] sm:w-[280px] rounded-2xl bg-white border border-slate-200/80 p-5  hover:shadow-md transition block">
                    <h3 class="font-semibold mills-primary mb-1">{{ $promo['title'] }}</h3>
                    <p class="text-sm text-slate-600 mb-4">{{ $promo['description'] ?? '' }}</p>
                    <span class="inline-flex items-center rounded-xl mills-primary-bg text-white text-sm font-medium px-4 py-2 hover:opacity-90">{{ $promo['cta'] ?? 'Learn more' }}</span>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Section: Overview --}}
        <section id="overview" class="scroll-mt-28 mb-12">
            <h2 class="text-xl font-bold mills-primary mb-4">Your overview</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5">
                    <p class="text-slate-500 text-sm">Active subscriptions</p>
                    <p class="text-2xl font-bold mills-primary mt-1">{{ $activeSubscriptionsCount }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 ">
                    <p class="text-slate-500 text-sm">Next charge</p>
                    <p class="text-lg font-bold text-slate-800 mt-1">{{ $nextChargeDate ? \Carbon\Carbon::parse($nextChargeDate)->format('M j, Y') : '—' }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                    <p class="text-slate-500 text-sm">Orders</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $ordersCount }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                    <p class="text-slate-500 text-sm">Last order</p>
                    <p class="text-lg font-semibold text-slate-800 mt-1">
                        @if($lastOrder)
                            {{ $lastOrder['status'] ?? '—' }} · {{ isset($lastOrder['processed_at']) ? \Carbon\Carbon::parse($lastOrder['processed_at'])->format('M j') : '—' }}
                        @else — @endif
                    </p>
                </div>
            </div>
        </section>

        {{-- Section: Orders (with product images) --}}
        <section id="orders" class="scroll-mt-28 mb-12">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold mills-primary">Orders</h2>
                <a href="{{ route('account.orders.index') }}" class="text-sm font-medium mills-primary hover:underline">View all</a>
            </div>
            @if(empty($orders))
                <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center text-slate-600">No orders yet.</div>
            @else
                <div class="space-y-4">
                    @foreach($orders as $order)
                        @php
                            $orderId = $order['id'] ?? $order['external_order_id']['order_id'] ?? '—';
                        @endphp
                        <a href="{{ route('account.orders.show', $order['id']) }}" class="block bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm hover:shadow-md transition">
                            <div class="flex flex-wrap gap-4 items-start">
                                {{-- Product images from first line items --}}
                                <div class="flex gap-2 flex-shrink-0" style=" min-width: 100%;">
                                    @foreach(array_slice($order['line_items'] ?? [], 0, 3) as $item)
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
                                        <div class="w-14 h-14 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0">
                                            <img src="{{ $img }}" alt="" class="w-full h-full object-cover" onerror="this.src='{{ $defaultProductImage }}'; this.onerror=null;">
                                        </div>
                                    @endforeach
                                    @if(empty($order['line_items']))
                                        <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center">
                                            <img src="{{ $defaultProductImage }}" alt="" class="w-10 h-10 object-contain opacity-60">
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-slate-800">Order #{{ $orderId }}</p>
                                    <p class="text-sm text-slate-500">
                                        {{ isset($order['processed_at']) ? \Carbon\Carbon::parse($order['processed_at'])->format('M j, Y') : (isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('M j, Y') : '') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-badge :variant="($order['status'] ?? '') === 'success' ? 'success' : (($order['status'] ?? '') === 'error' ? 'error' : 'default')">{{ $order['status'] ?? '—' }}</x-badge>
                                    <span class="font-medium text-slate-800">{{ $order['total_price'] ?? '—' }} {{ $order['currency'] ?? 'USD' }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Section: Subscriptions --}}
        <section id="subscriptions" class="scroll-mt-28 mb-12">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold mills-primary">Subscriptions</h2>
                <a href="{{ route('account.subscriptions.index') }}" class="text-sm font-medium mills-primary hover:underline">View all</a>
            </div>
            @if(empty($subscriptions))
                <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center text-slate-600">No subscriptions.</div>
            @else
                <div class="space-y-4">
                    @foreach(array_slice($subscriptions, 0, 5) as $sub)
                        <a href="{{ route('account.subscriptions.show', $sub['id']) }}" class="block bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $sub['product_title'] ?? 'Subscription' }}</p>
                                    <p class="text-sm text-slate-500">{{ $sub['variant_title'] ?? '' }} · Next: @if(isset($sub['next_charge_scheduled_at'])){{ \Carbon\Carbon::parse($sub['next_charge_scheduled_at'])->format('M j, Y') }}@else—@endif</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-badge :variant="($sub['status'] ?? '') === 'active' ? 'success' : (($sub['status'] ?? '') === 'cancelled' ? 'error' : 'default')">{{ $sub['status'] ?? '—' }}</x-badge>
                                    <span class="font-medium text-slate-800">{{ $sub['price'] ?? '—' }} {{ $sub['currency'] ?? 'USD' }}</span>
                                    <span class="text-sm font-medium mills-primary">Manage →</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Section: My details (profile + address) --}}
        <section id="my-details" class="scroll-mt-28 mb-12">
            <h2 class="text-xl font-bold mills-primary mb-4">My details</h2>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm mb-4">
                <h3 class="font-semibold mills-primary mb-3">Personal information</h3>
                <dl class="grid sm:grid-cols-2 gap-2 text-sm">
                    <dt class="text-slate-500">Name</dt>
                    <dd class="text-slate-800">{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</dd>
                    <dt class="text-slate-500">Email</dt>
                    <dd class="text-slate-800">{{ $user->email }}</dd>
                </dl>
            </div>

            @if($addressId && $enableAddressUpdate)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm" x-data="addressForm({{ json_encode($address ?? []) }}, '{{ $addressId }}')">
                <h3 class="font-semibold mills-primary mb-3">Shipping address</h3>
                <form @submit.prevent="save()" class="space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">First name</label>
                            <input type="text" x-model="form.first_name" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-900 focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Last name</label>
                            <input type="text" x-model="form.last_name" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-900 focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                        <input type="text" x-model="form.address1" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-900 focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Address 2 (optional)</label>
                        <input type="text" x-model="form.address2" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-900 focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">City</label>
                            <input type="text" x-model="form.city" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-900 focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">State / Province</label>
                            <input type="text" x-model="form.province" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-900 focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">ZIP / Postal code</label>
                            <input type="text" x-model="form.zip" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-900 focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Country</label>
                            <input type="text" x-model="form.country_code" maxlength="2" placeholder="US" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-900 focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Phone (optional)</label>
                        <input type="text" x-model="form.phone" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-900 focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
                    </div>
                    <button type="submit" class="rounded-xl mills-primary-bg text-white font-medium px-5 py-2.5 hover:opacity-90" :disabled="saving">
                        <span x-show="!saving">Save address</span>
                        <span x-show="saving" x-cloak>Saving...</span>
                    </button>
                </form>
            </div>
            @elseif($address)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h3 class="font-semibold mills-primary mb-3">Shipping address</h3>
                <p class="text-sm text-slate-700">
                    {{ $address['first_name'] ?? '' }} {{ $address['last_name'] ?? '' }}<br>
                    {{ $address['address1'] ?? '' }}<br>
                    @if(!empty($address['address2'])){{ $address['address2'] }}<br>@endif
                    {{ $address['city'] ?? '' }}, {{ $address['province'] ?? '' }} {{ $address['zip'] ?? '' }}<br>
                    {{ $address['country_code'] ?? '' }}
                </p>
            </div>
            @endif
        </section>
    </main>

    <script>
    function addressForm(initial, addressId) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        return {
            form: {
                first_name: initial?.first_name ?? '',
                last_name: initial?.last_name ?? '',
                address1: initial?.address1 ?? '',
                address2: initial?.address2 ?? '',
                city: initial?.city ?? '',
                province: initial?.province ?? '',
                zip: initial?.zip ?? '',
                country_code: initial?.country_code ?? '',
                phone: initial?.phone ?? '',
            },
            addressId,
            saving: false,
            async save() {
                this.saving = true;
                try {
                    const res = await fetch('/api/addresses/' + this.addressId, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify(this.form),
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.message || 'Failed to save');
                    if (document.body.__x?.$data?.toast !== undefined) document.body.__x.$data.toast = 'Address saved.';
                    setTimeout(() => location.reload(), 600);
                } catch (e) {
                    alert(e.message);
                } finally {
                    this.saving = false;
                }
            },
        };
    }
    </script>
    <div x-show="toast" x-cloak class="fixed bottom-4 right-4 px-4 py-2 rounded-xl shadow-lg bg-slate-800 text-white text-sm" x-transition x-text="toast"></div>
</body>
</html>
