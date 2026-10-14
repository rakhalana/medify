<x-storefront-layout :title="$product->name">
    
    <x-container class="py-8"
         x-data="{
             selectedImage: '{{ $product->primaryImage ? asset($product->primaryImage->path) : '' }}',
             qty: 1,
             maxStock: {{ $product->stock }},
             addingToCart: false,
             addToCart(andRedirect = false) {
                 if (this.addingToCart) return;
                 this.addingToCart = true;

                 fetch(`{{ route('ajax.cart.items.add') }}`, {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                         'Accept': 'application/json',
                     },
                     body: JSON.stringify({
                         product_id: {{ $product->id }},
                         quantity: this.qty
                     })
                 })
                 .then(res => res.json())
                 .then(res => {
                     if (res.code === 'UNAUTHENTICATED' || res.redirect_url) {
                         window.location.href = res.redirect_url || '{{ route('login') }}';
                         return;
                     }

                     if (res.data) {
                         window.dispatchEvent(new CustomEvent('cart-updated', {
                             detail: { count: res.data.total_items_count }
                         }));
                         window.dispatchEvent(new CustomEvent('toast', {
                             detail: { message: res.message, type: 'success' }
                         }));
                         if (andRedirect) {
                             window.location.href = '{{ route('cart.index') }}';
                         }
                     } else if (res.message) {
                         window.dispatchEvent(new CustomEvent('toast', {
                             detail: { message: res.message, type: 'danger' }
                         }));
                     }
                 })
                 .catch(() => {
                     window.dispatchEvent(new CustomEvent('toast', {
                         detail: { message: 'Gagal menambahkan produk ke keranjang.', type: 'danger' }
                     }));
                 })
                 .finally(() => {
                     this.addingToCart = false;
                 });
             }
         }">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
            <a href="{{ route('catalog.index') }}" class="hover:text-primary-600 transition-colors">Katalog</a>
            <span>/</span>
            <a href="{{ route('catalog.index', ['category' => $product->category->slug]) }}" class="hover:text-primary-600 transition-colors">
                {{ $product->category->name }}
            </a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $product->name }}</span>
        </nav>

        <!-- Product Overview Main Card -->
        <div class="bg-white rounded-2xl border border-border-subtle shadow-card p-6 md:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                
                <!-- Left: Product Image Gallery (5 Cols) -->
                <div class="lg:col-span-5 space-y-4">
                    <!-- Main Big Image (Aspect 1:1) -->
                    <div class="aspect-square bg-slate-50 rounded-2xl overflow-hidden border border-slate-200 flex items-center justify-center relative shadow-sm">
                        <template x-if="selectedImage">
                            <img :src="selectedImage" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover">
                        </template>
                        <template x-if="!selectedImage">
                            <div class="flex flex-col items-center justify-center text-slate-300 p-8">
                                <svg class="w-24 h-24 stroke-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                <span class="text-xs font-semibold text-slate-400 mt-2">Medify Alkes</span>
                            </div>
                        </template>

                        <!-- Category Pill -->
                        <div class="absolute top-3 left-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-white/90 backdrop-blur-md text-slate-700 border border-slate-200 shadow-sm">
                                {{ $product->category->name }}
                            </span>
                        </div>
                    </div>

                    <!-- Gallery Thumbnails (if multiple images) -->
                    @if ($product->images->count() > 1)
                        <div class="flex items-center gap-2 overflow-x-auto pb-2">
                            @foreach ($product->images as $img)
                                <button type="button" 
                                        @click="selectedImage = '{{ asset($img->path) }}'"
                                        class="w-16 h-16 rounded-xl overflow-hidden border-2 flex-shrink-0 transition-all"
                                        :class="selectedImage === '{{ asset($img->path) }}' ? 'border-primary-600 ring-2 ring-primary-100' : 'border-slate-200 opacity-70 hover:opacity-100'">
                                    <img src="{{ asset($img->path) }}" alt="Thumbnail" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right: Product Information & Purchase Panel (7 Cols) -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <!-- Category Badge & Title -->
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-primary-50 text-primary-700 border border-primary-200/60 mb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                            <span>{{ $product->category->name }}</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
                            {{ $product->name }}
                        </h1>
                    </div>

                    <!-- Vendor Store Card -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center flex-shrink-0 font-bold">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-900 truncate flex items-center gap-1.5">
                                    <span>{{ $product->store->name }}</span>
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                        <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Resmi
                                    </span>
                                </div>
                                <div class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Kota {{ $product->store->city->name }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('catalog.index', ['store' => $product->store->slug]) }}" 
                           class="text-xs font-semibold text-primary-700 hover:text-primary-800 bg-white border border-slate-200 hover:border-slate-300 px-3 py-1.5 rounded-lg transition-colors flex-shrink-0 shadow-sm">
                            Kunjungi Toko
                        </a>
                    </div>

                    <!-- Price Block -->
                    <div class="py-4 border-y border-slate-100 flex items-baseline justify-between">
                        <div>
                            <span class="text-xs text-slate-400 font-medium">Harga Resmi</span>
                            <div class="text-3xl sm:text-4xl font-extrabold text-primary-700 font-sans tracking-tight">
                                {{ $product->formatted_price }}
                            </div>
                        </div>

                        <!-- Stock Badge -->
                        <div class="text-right">
                            <span class="text-xs text-slate-400 font-medium block">Ketersediaan</span>
                            @if ($product->stock > 5)
                                <span class="inline-flex items-center gap-1 font-semibold text-sm text-emerald-600">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Stok Tersedia ({{ $product->stock }} unit)
                                </span>
                            @elseif ($product->stock > 0)
                                <span class="inline-flex items-center gap-1 font-semibold text-sm text-amber-600">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    Sisa {{ $product->stock }} unit lagi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 font-semibold text-sm text-rose-600">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                    Stok Habis
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Quantity Stepper & Buy Actions -->
                    @if ($product->stock > 0)
                        <div class="space-y-4 pt-2">
                            <div class="flex items-center gap-4">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Jumlah:</label>
                                <div class="inline-flex items-center border border-slate-300 rounded-xl overflow-hidden shadow-sm bg-white">
                                    <button type="button" 
                                            @click="if(qty > 1) qty--" 
                                            class="px-3 py-1.5 text-slate-600 hover:bg-slate-100 transition-colors font-bold select-none">
                                        -
                                    </button>
                                    <input type="number" 
                                           x-model.number="qty" 
                                           min="1" 
                                           :max="maxStock" 
                                           class="w-14 text-center border-none text-sm font-bold focus:ring-0 p-0 text-slate-900">
                                    <button type="button" 
                                            @click="if(qty < maxStock) qty++" 
                                            class="px-3 py-1.5 text-slate-600 hover:bg-slate-100 transition-colors font-bold select-none">
                                        +
                                    </button>
                                </div>
                                <span class="text-xs text-slate-400">Maks. pembelian {{ $product->stock }} unit</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <!-- Direct Action / Add to Cart -->
                                <button type="button"
                                        @click="addToCart(false)"
                                        :disabled="addingToCart"
                                        class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 bg-white border border-primary-600 text-primary-700 hover:bg-primary-50 font-semibold rounded-xl transition-all shadow-sm active:scale-[0.98] disabled:opacity-50">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <span x-text="addingToCart ? 'Menambahkan...' : 'Tambah ke Keranjang'"></span>
                                </button>

                                <button type="button"
                                        @click="addToCart(true)"
                                        :disabled="addingToCart"
                                        class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg shadow-primary-600/20 active:scale-[0.98] disabled:opacity-50">
                                    <span>Beli Sekarang</span>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                            <span class="text-sm font-semibold text-slate-600">Produk ini saat ini sedang habis. Silakan periksa kembali nanti.</span>
                        </div>
                    @endif

                    <!-- Value Assurance Pills -->
                    <div class="pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-slate-600">
                        <div class="flex items-center gap-2 p-2.5 rounded-lg bg-slate-50">
                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Ongkir Rp0 Gratis</span>
                        </div>
                        <div class="flex items-center gap-2 p-2.5 rounded-lg bg-slate-50">
                            <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Standar Kemenkes RI</span>
                        </div>
                        <div class="flex items-center gap-2 p-2.5 rounded-lg bg-slate-50">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Bisa COD Sekota</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Detailed Description & Specifications Section -->
        <div class="mt-8 bg-white rounded-2xl border border-border-subtle shadow-card p-6 md:p-8">
            <h2 class="text-lg font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Deskripsi & Spesifikasi Produk
            </h2>

            <div class="mt-6 prose prose-slate max-w-none text-sm text-slate-700 leading-relaxed space-y-4">
                <p class="whitespace-pre-line">{{ $product->description }}</p>
            </div>
        </div>

        <!-- Related Products Section -->
        @if ($relatedProducts->count() > 0)
            <div class="mt-12">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Produk Terkait Lainnya</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Rekomendasi alat kesehatan dari kategori {{ $product->category->name }}</p>
                    </div>
                    <a href="{{ route('catalog.index', ['category' => $product->category->slug]) }}" 
                       class="text-xs font-semibold text-primary-700 hover:text-primary-800 transition-colors flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($relatedProducts as $related)
                        <x-product-card :product="$related" />
                    @endforeach
                </div>
            </div>
        @endif
    </x-container>
</x-storefront-layout>
