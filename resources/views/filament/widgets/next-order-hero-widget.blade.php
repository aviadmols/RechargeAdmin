<x-filament-widgets::widget>
    <x-filament::section class="overflow-hidden">
        <div class="space-y-6">
            {{-- Next order date hero --}}
            <div class="rounded-2xl bg-gradient-to-br from-amber-500/10 via-primary-500/5 to-transparent dark:from-amber-500/20 dark:via-primary-500/10 p-6 md:p-8 border border-amber-200/50 dark:border-amber-500/20">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Next order
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Scheduled delivery date for the upcoming shipment
                        </p>
                    </div>
                    @if($nextOrderDate)
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl md:text-4xl font-bold text-amber-600 dark:text-amber-400 tabular-nums">
                                {{ $nextOrderDate->format('M j, Y') }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                ({{ $nextOrderDate->locale('en')->translatedFormat('l') }})
                            </span>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">
                            No upcoming charge scheduled
                        </p>
                    @endif
                </div>
            </div>

            {{-- Products in the next delivery (large images) --}}
            @if($products->isNotEmpty())
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        Products in the next delivery
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($products as $product)
                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-gray-50 dark:bg-gray-800/50">
                                <div class="aspect-square bg-gray-200 dark:bg-gray-700 relative">
                                    @if($product->image_url)
                                        <img
                                            src="{{ $product->image_url }}"
                                            alt="{{ $product->title }}"
                                            class="absolute inset-0 w-full h-full object-cover"
                                            loading="lazy"
                                        />
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center text-gray-400 dark:text-gray-500 text-sm">
                                            No image
                                        </div>
                                    @endif
                                </div>
                                <div class="p-2 text-center">
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate" title="{{ $product->title }}">
                                        {{ $product->title }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
