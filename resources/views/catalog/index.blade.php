<x-storefront-layout title="Katalog Alat Kesehatan Multi-Vendor">
    
    <!-- Hero / Headline Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-primary-950 to-slate-900 text-white py-12 md:py-16 border-b border-primary-900/40">
        <!-- Ambient decorative blur circles -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-secondary-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <x-container class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="max-w-2xl text-center md:text-left">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary-500/20 text-primary-300 border border-primary-400/30 mb-4">
                    <span class="w-2 h-2 rounded-full bg-primary-400 animate-pulse"></span>
                    Katalog Resmi Multi-Vendor Medify
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight font-sans">
                    Alat Kesehatan Standar Medis, <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 to-secondary-300">Pasti Resmi & Berizin</span>
                </h1>
                <p class="mt-4 text-sm sm:text-base text-slate-300 leading-relaxed">
                    Dapatkan peralatan medis rumah sakit, perlengkapan klinik, tensimeter, hingga alat bantu mobilitas langsung dari distributor terpercaya di seluruh Indonesia.
                </p>

                <!-- Value Proposition Badges -->
                <div class="mt-6 flex flex-wrap items-center justify-center md:justify-start gap-4 text-xs font-medium text-slate-200">
                    <div class="flex items-center gap-1.5 bg-slate-800/80 px-3 py-1.5 rounded-lg border border-slate-700/60 backdrop-blur-sm">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Gratis Ongkir Rp0</span>
                    </div>
                    <div class="flex items-center gap-1.5 bg-slate-800/80 px-3 py-1.5 rounded-lg border border-slate-700/60 backdrop-blur-sm">
                        <svg class="w-4 h-4 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Izin Edar Kemenkes RI</span>
                    </div>
                    <div class="flex items-center gap-1.5 bg-slate-800/80 px-3 py-1.5 rounded-lg border border-slate-700/60 backdrop-blur-sm">
                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>COD / Bayar di Tempat Sekota</span>
                    </div>
                </div>
            </div>

            <!-- Quick Category Pill Selector in Hero -->
            <div class="w-full md:w-auto bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/15 flex flex-col gap-2 max-w-sm">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Pilihan Kategori Cepat</span>
                <div class="flex flex-wrap gap-2 mt-1">
                    @foreach ($categories->take(4) as $cat)
                        <a href="{{ route('catalog.index', ['category' => $cat->slug]) }}" 
                           class="px-2.5 py-1 text-xs rounded-lg transition-colors {{ $currentCategory === $cat->slug ? 'bg-primary-500 text-white font-bold' : 'bg-white/15 text-white hover:bg-white/25' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </x-container>
    </div>

    <!-- Main Content: Sidebar Filter & Product Grid -->
    <x-container class="py-8"
         x-data="{
             quickViewOpen: false,
             loadingQuickView: false,
             activeProduct: null,
             openModal(id) {
                 this.quickViewOpen = true;
                 this.loadingQuickView = true;
                 fetch(`{{ url('/ajax/products') }}/${id}`)
                     .then(res => res.json())
                     .then(data => {
                         this.activeProduct = data;
                     })
                     .catch(() => {
                         this.quickViewOpen = false;
                     })
                     .finally(() => {
                         this.loadingQuickView = false;
                     });
             }
         }"
         @open-quick-view.window="openModal($event.detail.id)">

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
            
            <!-- Left Column: Filter Sidebar (Sticky Desktop) -->
            <aside class="lg:col-span-1 bg-white rounded-xl border border-border-subtle shadow-card p-5 space-y-6 sticky top-24">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter Katalog
                    </h2>
                    @if ($currentCategory || $currentStore || $currentSearch || $minPrice || $maxPrice)
                        <a href="{{ route('catalog.index') }}" class="text-xs text-rose-600 hover:text-rose-700 font-semibold transition-colors">
                            Reset
                        </a>
                    @endif
                </div>

                <form action="{{ route('catalog.index') }}" method="GET" id="filter-form" class="space-y-6">
                    @if ($currentSearch)
                        <input type="hidden" name="q" value="{{ $currentSearch }}">
                    @endif
                    @if ($currentSort !== 'latest')
                        <input type="hidden" name="sort" value="{{ $currentSort }}">
                    @endif

                    <!-- Category Filter -->
                    <div>
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                            Kategori Alat Medis
                        </h3>
                        <div class="space-y-1">
                            <a href="{{ route('catalog.index', array_merge(request()->except(['category', 'page']))) }}"
                               class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors {{ empty($currentCategory) ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                                <span>Semua Kategori</span>
                                <span class="text-slate-400 font-normal">({{ $categories->sum('products_count') }})</span>
                            </a>
                            @foreach ($categories as $cat)
                                <a href="{{ route('catalog.index', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}"
                                   class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors {{ $currentCategory === $cat->slug ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                                    <span class="truncate">{{ $cat->name }}</span>
                                    <span class="text-slate-400 font-normal flex-shrink-0">({{ $cat->products_count }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Store Vendor Filter -->
                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                            Toko Vendor
                        </h3>
                        <div class="space-y-1">
                            <a href="{{ route('catalog.index', array_merge(request()->except(['store', 'page']))) }}"
                               class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors {{ empty($currentStore) ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                                <span>Semua Toko</span>
                            </a>
                            @foreach ($stores as $st)
                                <a href="{{ route('catalog.index', array_merge(request()->except(['store', 'page']), ['store' => $st->slug])) }}"
                                   class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors {{ $currentStore === $st->slug ? 'bg-primary-50 text-primary-800 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="truncate">{{ $st->name }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 px-1.5 py-0.5 rounded bg-slate-100 flex-shrink-0">
                                        {{ $st->city->name }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                            Rentang Harga (Rp)
                        </h3>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <label for="min_price" class="text-[10px] text-slate-400">Minimal</label>
                                <input type="number"
                                       id="min_price"
                                       name="min_price"
                                       value="{{ $minPrice }}"
                                       placeholder="0"
                                       step="10000"
                                       class="w-full text-xs py-1.5 px-2 bg-slate-50 border border-slate-200 rounded-lg">
                            </div>
                            <div>
                                <label for="max_price" class="text-[10px] text-slate-400">Maksimal</label>
                                <input type="number"
                                       id="max_price"
                                       name="max_price"
                                       value="{{ $maxPrice }}"
                                       placeholder="5000000"
                                       step="10000"
                                       class="w-full text-xs py-1.5 px-2 bg-slate-50 border border-slate-200 rounded-lg">
                            </div>
                        </div>
                        <button type="submit" 
                                class="w-full mt-2 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition-colors">
                            Terapkan Harga
                        </button>
                    </div>
                </form>
            </aside>

            <!-- Right Column: Product Results Grid -->
            <div class="lg:col-span-3 space-y-6">
                
                <!-- Catalog Header: Counter, Active Filter Badges & Sort -->
                <div class="bg-white rounded-xl border border-border-subtle shadow-card p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-slate-800">
                            Menampilkan <span class="text-primary-700 font-bold">{{ $products->total() }}</span> produk alat kesehatan
                        </span>

                        <!-- Active filter chips -->
                        @if ($currentSearch)
                            <a href="{{ route('catalog.index', array_merge(request()->except(['q', 'page']))) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-800 border border-primary-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors"
                               title="Hapus pencarian">
                                <span>Pencarian: "{{ $currentSearch }}"</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif

                        @if ($currentCategory)
                            <a href="{{ route('catalog.index', array_merge(request()->except(['category', 'page']))) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-secondary-50 text-secondary-800 border border-secondary-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors"
                               title="Hapus filter kategori">
                                <span>Kategori: {{ $categories->firstWhere('slug', $currentCategory)?->name ?? $currentCategory }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif

                        @if ($currentStore)
                            <a href="{{ route('catalog.index', array_merge(request()->except(['store', 'page']))) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-300 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors"
                               title="Hapus filter toko">
                                <span>Toko: {{ $stores->firstWhere('slug', $currentStore)?->name ?? $currentStore }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>

                    <!-- Sort Select Dropdown -->
                    <div class="flex items-center gap-2 self-end sm:self-auto flex-shrink-0">
                        <label for="sort-select" class="text-xs text-slate-500 font-medium">Urutkan:</label>
                        <select id="sort-select"
                                onchange="location = this.value;"
                                class="text-xs font-semibold py-1.5 pl-3 pr-8 rounded-lg border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-primary-200">
                            <option value="{{ route('catalog.index', array_merge(request()->except(['sort', 'page']), ['sort' => 'latest'])) }}" {{ $currentSort === 'latest' ? 'selected' : '' }}>
                                Terbaru
                            </option>
                            <option value="{{ route('catalog.index', array_merge(request()->except(['sort', 'page']), ['sort' => 'price_asc'])) }}" {{ $currentSort === 'price_asc' ? 'selected' : '' }}>
                                Harga Terendah
                            </option>
                            <option value="{{ route('catalog.index', array_merge(request()->except(['sort', 'page']), ['sort' => 'price_desc'])) }}" {{ $currentSort === 'price_desc' ? 'selected' : '' }}>
                                Harga Tertinggi
                            </option>
                            <option value="{{ route('catalog.index', array_merge(request()->except(['sort', 'page']), ['sort' => 'name_asc'])) }}" {{ $currentSort === 'name_asc' ? 'selected' : '' }}>
                                Nama A-Z
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                @if ($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8 pt-4">
                        {{ $products->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-2xl border border-border-subtle p-12 text-center shadow-card flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1">Produk Tidak Ditemukan</h3>
                        <p class="text-xs text-slate-500 max-w-sm mb-6">
                            Tidak ada produk alat kesehatan yang cocok dengan kriteria pencarian atau filter yang Anda pilih.
                        </p>
                        <a href="{{ route('catalog.index') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all">
                            Lihat Semua Produk
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick View Alpine Modal -->
        <div x-show="quickViewOpen"
             x-transition:enter="ease-out duration-250"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
             style="display: none;"
             @keydown.escape.window="quickViewOpen = false">
            
            <!-- Backdrop with blur -->
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="quickViewOpen = false"></div>

            <!-- Modal Panel -->
            <div class="relative bg-white rounded-2xl max-w-2xl w-full p-6 shadow-modal border border-border-subtle z-10 overflow-hidden"
                 @click.outside="quickViewOpen = false">
                
                <button type="button" 
                        @click="quickViewOpen = false"
                        class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Loading State Skeleton -->
                <div x-show="loadingQuickView" class="animate-pulse space-y-4 py-8">
                    <div class="h-6 bg-slate-200 rounded w-3/4"></div>
                    <div class="h-4 bg-slate-200 rounded w-1/2"></div>
                    <div class="h-24 bg-slate-100 rounded"></div>
                </div>

                <!-- Product Quick View Details -->
                <div x-show="!loadingQuickView && activeProduct" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    
                    <!-- Image -->
                    <div class="aspect-square bg-slate-50 rounded-xl overflow-hidden border border-slate-200 flex items-center justify-center">
                        <template x-if="activeProduct?.primary_image">
                            <img :src="activeProduct.primary_image" :alt="activeProduct.name" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!activeProduct?.primary_image">
                            <svg class="w-16 h-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </template>
                    </div>

                    <!-- Details -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-primary-50 text-primary-800 border border-primary-200"
                                  x-text="activeProduct?.category_name"></span>
                        </div>

                        <h2 class="text-lg font-bold text-slate-900 leading-snug" x-text="activeProduct?.name"></h2>

                        <!-- Store Badge -->
                        <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                            <svg class="w-4 h-4 text-primary-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <div>
                                <span class="font-bold text-slate-900" x-text="activeProduct?.store_name"></span>
                                <span class="text-slate-400">• Kota <span x-text="activeProduct?.store_city"></span></span>
                            </div>
                        </div>

                        <!-- Price & Stock -->
                        <div class="pt-2">
                            <div class="text-xs text-slate-400">Harga Satuan:</div>
                            <div class="text-2xl font-black text-primary-700 tracking-tight" x-text="activeProduct?.formatted_price"></div>
                            <div class="text-xs mt-1 text-slate-600">
                                Status: <span class="font-semibold" :class="activeProduct?.stock > 5 ? 'text-emerald-600' : 'text-amber-600'" x-text="activeProduct?.is_in_stock ? `Tersedia (${activeProduct?.stock} unit)` : 'Stok Habis'"></span>
                            </div>
                        </div>

                        <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed pt-1" x-text="activeProduct?.description"></p>

                        <!-- Actions -->
                        <div class="pt-4 flex items-center gap-3">
                            <a :href="activeProduct?.url" 
                               class="flex-1 py-2 text-center bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all">
                                Buka Halaman Lengkap
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-container>
</x-storefront-layout>
