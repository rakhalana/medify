@props(['product'])

@php
    $primaryImg = $product->primaryImage;
    $imgUrl = $primaryImg ? asset($primaryImg->path) : null;
    $isLowStock = $product->stock > 0 && $product->stock <= 5;
    $isOutOfStock = $product->stock <= 0;
@endphp

<div class="group bg-white rounded-xl border border-border-subtle shadow-card hover:shadow-card-hover hover:-translate-y-1 transition-all duration-200 overflow-hidden flex flex-col h-full">
    
    <!-- Product Image (Aspect Ratio 1:1) -->
    <div class="relative w-full pb-[100%] bg-slate-50 border-b border-slate-100 overflow-hidden">
        @if ($imgUrl)
            <img src="{{ $imgUrl }}"
                 alt="{{ $product->name }}"
                 loading="lazy"
                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <!-- Placeholder Grafis Medify -->
            <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-300 p-4 bg-gradient-to-br from-slate-50 to-slate-100">
                <svg class="w-16 h-16 stroke-1 text-slate-300 group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                <span class="text-[11px] font-semibold text-slate-400 mt-2">Medify Alkes</span>
            </div>
        @endif

        <!-- Category Badge (Top Left Pill) -->
        <div class="absolute top-2.5 left-2.5 z-10">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide bg-white/90 backdrop-blur-md text-slate-700 border border-slate-200/80 shadow-sm">
                {{ $product->category->name }}
            </span>
        </div>

        <!-- Stock Indicator Badge (Top Right) -->
        <div class="absolute top-2.5 right-2.5 z-10">
            @if ($isOutOfStock)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-sm">
                    Habis
                </span>
            @elseif ($isLowStock)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-sm">
                    Sisa {{ $product->stock }}
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                    Stok {{ $product->stock }}
                </span>
            @endif
        </div>
    </div>

    <!-- Product Card Body -->
    <div class="p-4 flex-1 flex flex-col justify-between">
        <div>
            <!-- Store Name & Store City Badge -->
            <div class="flex items-center justify-between text-xs text-slate-500 mb-1.5 gap-2">
                <a href="{{ route('catalog.index', ['store' => $product->store->slug]) }}" 
                   class="font-semibold text-slate-700 hover:text-primary-600 truncate flex items-center gap-1 transition-colors"
                   title="{{ $product->store->name }}">
                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="truncate">{{ $product->store->name }}</span>
                </a>
                
                <!-- City Pill (Kriteria sekota Postpaid COD) -->
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 flex-shrink-0">
                    <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $product->store->city->name }}
                </span>
            </div>

            <!-- Product Code (JetBrains Mono) -->
            <div class="font-mono text-[11px] text-slate-400 tracking-wider uppercase mb-1">
                {{ $product->code }}
            </div>

            <!-- Product Title -->
            <h3 class="font-semibold text-sm text-slate-900 group-hover:text-primary-700 transition-colors line-clamp-2 leading-snug">
                <a href="{{ route('catalog.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h3>
        </div>

        <!-- Price & Action Buttons -->
        <div class="mt-4 pt-3 border-t border-slate-100">
            <div class="flex items-baseline justify-between mb-3">
                <span class="text-xs text-slate-400">Harga Satuan</span>
                <span class="text-base sm:text-lg font-bold text-primary-700 font-sans tracking-tight">
                    {{ $product->formatted_price }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <!-- Quick View Modal Trigger -->
                <button type="button"
                        @click="$dispatch('open-quick-view', { id: {{ $product->id }} })"
                        class="w-full inline-flex items-center justify-center px-2.5 py-1.5 border border-slate-200 hover:border-slate-300 text-xs font-semibold rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                    Detail
                </button>

                <!-- Buy Action Button -->
                <a href="{{ route('catalog.show', $product->slug) }}"
                   class="w-full inline-flex items-center justify-center gap-1.5 px-2.5 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all hover:shadow active:scale-[0.98]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Beli
                </a>
            </div>
        </div>
    </div>
</div>
