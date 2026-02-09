@php
    $primary = config('mills.primary_color', '#002642');
    $bg = config('mills.background_color', '#d7ecff');
    $originalPrice = $product->original_price ? (float) $product->original_price : null;
    $discountPct = $product->discount_percent ?? 0;
    $discountedPrice = $originalPrice !== null && $discountPct > 0
        ? round($originalPrice * (1 - $discountPct / 100), 2)
        : $originalPrice;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->title }} – {{ config('app.name') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        .mills-bg { background-color: #f9f6f1; }
        .mills-primary { color: {{ $primary }}; }
        .mills-primary-bg { background-color: {{ $primary }}; }
        .mills-border { border-color: {{ $primary }}20; }
        .product-banner {
            align-items: center;
            BACKGROUND: #FFF;
            display: flex;
        }
        .product-banner__media { position: relative; width: 50%; overflow: hidden; }
        @media (min-width: 750px) { 
      
       }
        .product-banner__media img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
        .product-banner__content { position: relative; z-index: 2; padding: 2rem 1.5rem; max-width: 42rem; }
        @media (min-width: 750px) { .product-banner__content { padding: 3rem 4rem; } }
        .content-container { color: #002642; }
        .multicolumn-card__info { background-color: #efd3bf; padding: 1rem 1.5rem 1.25rem; }

        @media (max-width: 750px) {
        .product-banner__media {
    position: relative;
    width: 100%;
            }
            .product-banner {
                display: grid;
            }
        }
        @media (min-width: 750px) { .multicolumn-card__info { padding-inline: 1.5rem; padding-top: 1rem; } }
        .h5-custom { font-size: 1.25rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.001rem; }
        .faq h2 { margin-bottom: 0.2rem; }
        .faq-details { border-bottom: 1px solid rgba(0,38,66,0.15); }
        .faq-details summary { padding: 1rem 0; cursor: pointer; font-weight: 600; color: #002642; list-style: none; }
        .faq-details summary::-webkit-details-marker { display: none; }
        .faq-details[open] summary { margin-bottom: 0.5rem; }
        .faq-details .rte { padding-bottom: 1rem; color: #334155; font-size: 0.9375rem; line-height: 1.5; }
        .rte ul, .rte ol { padding-left: 1.25rem; margin-top: 0.5rem; margin-bottom: 0.5rem; }
        .rte li { margin-bottom: 0.35rem; }
        .rte h6 { font-size: 1.125rem; text-decoration: underline; margin-top: 0.75rem; margin-bottom: 0.25rem; color: #002642; }
    </style>
</head>
<body class="min-h-screen mills-bg text-slate-900" x-data="{ submitting: false }">
    <header class="sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-slate-200/60 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('account.dashboard') }}" class="text-xl font-bold mills-primary tracking-tight">{{ config('app.name') }}</a>
                <a href="{{ route('account.dashboard') }}#products" class="text-sm font-medium text-slate-600 hover:text-slate-900">← All products</a>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm font-medium text-slate-600 hover:text-slate-900">Log out</button>
            </form>
        </div>
    </header>

    <main class="max-w-4xl mx-auto">
        @if (session('success'))
            <div class="mx-4 mt-6 p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mx-4 mt-6 p-4 rounded-2xl bg-red-50 text-red-800 text-sm border border-red-200">{{ session('error') }}</div>
        @endif

        {{-- Image banner --}}
        <section class="product-banner relative">
            <div class="product-banner__media">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->title }}" loading="eager">
                @else
                    <div class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-500">No image</div>
                @endif
            </div>
            <div class="product-banner__content">
                <div class="content-container">
                    <h1 class="text-2xl sm:text-3xl font-bold mills-primary mb-2">{{ $product->title }}</h1>
                    <p class="text-slate-700 mb-4">{{ $product->subtitle ?: 'Because treats should be as healthy as mealtime.' }}</p>
                    <div class="flex flex-wrap gap-3">
                        <form action="{{ route('account.products.add') }}" method="POST" class="inline" @submit="submitting = true">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="rounded-full mills-primary-bg text-white font-semibold py-2.5 px-5 hover:opacity-90 disabled:opacity-70 transition" :disabled="submitting">
                                <span x-show="!submitting">Add to my box</span>
                                <span x-show="submitting" x-cloak>Adding…</span>
                            </button>
                        </form>
                        <form action="{{ route('account.products.buy-once') }}" method="POST" class="inline" @submit="submitting = true">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="rounded-full border-2 border-[#002642] text-[#002642] font-semibold py-2 px-5 hover:bg-[#002642]/5 disabled:opacity-70 transition" :disabled="submitting">
                                <span x-show="!submitting">Buy once</span>
                                <span x-show="submitting" x-cloak>Adding…</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        {{-- Rich text: 100% freeze dried meat --}}
        <section class="py-4 text-center padding-top-50">
            <h2 class="h5-custom mills-primary">{{ $product->subtitle ?: '100% freeze dried meat' }}</h2>
        </section>

    
        {{-- Rich text: Smart Snacking --}}
        <section class="px-4 sm:px-6  text-center">
            <h2 class="text-xl font-bold mills-primary mb-3">Smart Snacking That Fits With Daily Packs</h2>
            <p class="text-slate-700 max-w-2xl mx-auto">
                Treats should account for up to <strong>10% of your dog's daily calories</strong>. Because Mills Daily Packs are perfectly-portioned for your dog, adding freeze dried, single-protein treats is a great way to bond with your dog, keeping them happy and healthy.
            </p>
        </section>

        {{-- Smart Packaging, Smaller Footprint --}}
        <section class="px-4 sm:px-6 py-8 bg-stone-50/80">
            <h2 class="text-xl font-bold text-[#002642] text-center mb-6">Smart Packaging, Smaller Footprint</h2>
            <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 items-center">
                <div class="rounded-2xl overflow-hidden bg-white border border-slate-200/80">
                    <img src="https://millsdailypacks.com/cdn/shop/files/Untitled-28.webp?v=1753134960&width=750" alt="Mills Daily Packs packaging - recyclable box and pouch" class="w-full h-auto object-cover">
                </div>
                <div class="space-y-5 text-slate-700">
                    <div>
                        <h3 class="font-bold text-[#002642] mb-2">Recyclable & Responsible Materials</h3>
                        <p class="text-sm leading-relaxed">Our Daily Packs are made with recyclable outer packaging and BPA-free plastic construction, making them safe for your pet and easier on the environment in communities with recycling programs.</p>
                    </div>
                    <div>
                        <h3 class="font-bold text-[#002642] mb-2">Lighter Weight, Lower Impact</h3>
                        <p class="text-sm leading-relaxed">Our 28-day supply of Daily Packs weighs significantly less than traditional large dog food sacks, reducing transportation emissions and packaging waste. Unlike conventional dog food bags that often contain aluminum and non-recyclable materials, our packs are designed with end-of-life in mind.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- FAQ --}}
        <section class="px-4 sm:px-6 pb-12 faq padding-bottom-50">
            <h2 class="text-xl font-bold mills-primary text-center mb-6">FAQ</h2>
            <div class="max-w-2xl mx-auto space-y-0">
                <details class="faq-details">
                    <summary>Are treats healthy?</summary>
                    <div class="rte">
                        <p>Our Single-Ingredient Treats are super healthy, made of a single protein, without grains or fillers. While treats should not replace meals, our treats are a perfect complement to Mills Daily Packs.</p>
                    </div>
                </details>
                <details class="faq-details">
                    <summary>Are these treats good for dogs with sensitivities?</summary>
                    <div class="rte">
                        <p>Yes. With one protein per flavor and no fillers or additives, they're an excellent option for sensitive stomachs and ingredient-conscious pet parents.</p>
                    </div>
                </details>
                <details class="faq-details">
                    <summary>Are they safe for puppies and senior dogs?</summary>
                    <div class="rte">
                        <p>Absolutely. The freeze-dried texture makes the treats easy to break into small pieces for puppies, seniors, or small dogs.</p>
                    </div>
                </details>
                <details class="faq-details">
                    <summary>How should I store them?</summary>
                    <div class="rte">
                        <p>Keep the treats sealed and stored in a cool, dry place. Since they're freeze-dried, they stay fresh without added preservatives.</p>
                    </div>
                </details>
            </div>
        </section>
    </main>

    {{-- Sticky add to subscription bar --}}
    <div class="fixed bottom-0 left-0 right-0 z-20 bg-white/95 backdrop-blur border-t border-slate-200/60 shadow-[0_-4px_20px_rgba(0,0,0,0.08)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex justify-center">
            <form action="{{ route('account.products.add') }}" method="POST" class="w-full max-w-md" @submit="submitting = true">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="w-full rounded-full mills-primary-bg text-white font-bold py-3.5 px-6 text-base hover:opacity-90 disabled:opacity-70 transition shadow-lg" :disabled="submitting">
                    <span x-show="!submitting">Get {{ $product->title }} Pack</span>
                    <span x-show="submitting" x-cloak>Adding…</span>
                </button>
            </form>
        </div>
    </div>
    <div class="h-20" aria-hidden="true"></div>
</body>
</html>
