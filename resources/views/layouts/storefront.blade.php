<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Katalog Alat Kesehatan Terpercaya' }} — {{ config('app.name', 'Medify') }}</title>
        <meta name="description" content="Medify adalah marketplace alat kesehatan multi-vendor terpercaya di Indonesia. Dapatkan tensimeter, kursi roda, oximeter, nebulizer berstandar resmi dengan gratis ongkir.">

        <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-surface-canvas text-slate-800 selection:bg-primary-500 selection:text-white flex flex-col min-h-screen">

        <!-- Top Announcement Bar -->
        <div class="bg-slate-900 text-slate-200 text-xs py-2 border-b border-slate-800">
            <x-container class="flex flex-col sm:flex-row items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-primary-400 font-semibold uppercase tracking-wider text-[11px] px-2 py-0.5 rounded bg-primary-950/60 border border-primary-800/50">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Standar Kemenkes
                    </span>
                    <span class="hidden sm:inline text-slate-400">•</span>
                    <span>Multi-Vendor Resmi Alat Kesehatan Berizin Edar</span>
                </div>
                <div class="flex items-center gap-4 text-slate-300">
                    <span class="font-medium text-emerald-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Ongkir Rp0 (Gratis Ongkir)
                    </span>
                    <span class="text-slate-600">|</span>
                    <a href="#bantuan" class="hover:text-white transition-colors">Pusat Bantuan</a>
                </div>
            </x-container>
        </div>

        <!-- Sticky Header & Navbar -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-border-subtle shadow-card">
            <x-container class="flex items-center justify-between h-20 gap-4 lg:gap-8">
                    
                    <!-- Left: Brand Logo -->
                    <div class="flex-shrink-0 flex items-center gap-3">
                        <a href="{{ route('catalog.index') }}" class="flex items-center gap-2.5 group">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white shadow-md shadow-primary-500/20 group-hover:scale-105 transition-transform duration-200">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-2xl font-extrabold tracking-tight text-slate-900 font-sans leading-none">
                                    Medi<span class="text-primary-600">fy</span>
                                </span>
                                <span class="text-[10px] text-slate-500 font-medium tracking-wide uppercase mt-0.5">Alat Kesehatan</span>
                            </div>
                        </a>
                    </div>

                    <!-- Middle: Live Search Bar (Alpine.js with auto-complete) -->
                    <div class="flex-1 max-w-2xl hidden md:block" 
                         x-data="{
                             query: '{{ request('q') }}',
                             suggestions: [],
                             loading: false,
                             showDropdown: false,
                             fetchSuggestions() {
                                 if (this.query.trim().length < 2) {
                                     this.suggestions = [];
                                     this.showDropdown = false;
                                     return;
                                 }
                                 this.loading = true;
                                 fetch(`{{ route('ajax.products.suggest') }}?q=${encodeURIComponent(this.query)}`)
                                     .then(res => res.json())
                                     .then(data => {
                                         this.suggestions = data.data || [];
                                         this.showDropdown = this.suggestions.length > 0;
                                     })
                                     .catch(() => { this.suggestions = []; })
                                     .finally(() => { this.loading = false; });
                             }
                         }"
                         @click.outside="showDropdown = false">
                        <form action="{{ route('catalog.index') }}" method="GET" class="relative">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            @if(request('store'))
                                <input type="hidden" name="store" value="{{ request('store') }}">
                            @endif
                            <div class="relative flex items-center">
                                <input type="text"
                                       name="q"
                                       x-model="query"
                                       @input.debounce.300ms="fetchSuggestions()"
                                       @focus="if (suggestions.length > 0) showDropdown = true"
                                       placeholder="Cari alat tensimeter, kursi roda, nebulizer, atau kode produk..."
                                       autocomplete="off"
                                       class="w-full pl-11 pr-24 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm placeholder:text-slate-400 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 transition-all shadow-sm">
                                <div class="absolute left-3.5 text-slate-400 pointer-events-none">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <button type="submit"
                                        class="absolute right-1.5 px-4 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                                    Cari
                                </button>
                            </div>

                            <!-- Live Search Autocomplete Dropdown -->
                            <div x-show="showDropdown"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-1"
                                 class="absolute left-0 right-0 top-full mt-2 bg-white rounded-xl shadow-dropdown border border-border-subtle overflow-hidden z-50 divide-y divide-slate-100"
                                 style="display: none;">
                                <div class="p-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider bg-slate-50/70">
                                    Saran Produk Sesuai Pencarian
                                </div>
                                <template x-for="item in suggestions" :key="item.id">
                                    <a :href="item.url" class="p-3 hover:bg-slate-50 flex items-center gap-3 transition-colors">
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex-shrink-0 flex items-center justify-center text-slate-400 overflow-hidden">
                                            <template x-if="item.image_url">
                                                <img :src="item.image_url" :alt="item.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!item.image_url">
                                                <svg class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-semibold text-slate-900 truncate" x-text="item.name"></div>
                                            <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                                <span class="font-mono text-slate-400" x-text="item.code"></span>
                                                <span>•</span>
                                                <span x-text="item.store_name"></span>
                                                <span class="text-slate-300">(<span x-text="item.store_city"></span>)</span>
                                            </div>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <div class="text-sm font-bold text-primary-700" x-text="item.formatted_price"></div>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </form>
                    </div>

                    <!-- Right Navigation / Auth Actions -->
                    <div class="flex items-center gap-3">

                        <!-- Cart Indicator -->
                        <a href="{{ url('/cart') }}"
                           class="relative p-2.5 rounded-xl border border-slate-200 text-slate-700 hover:text-primary-600 hover:border-primary-200 hover:bg-primary-50/50 transition-colors flex items-center justify-center group"
                           title="Keranjang Belanja">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <!-- Cart count badge -->
                            <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-rose-500 text-white text-[11px] font-bold flex items-center justify-center shadow-sm">
                                0
                            </span>
                        </a>

                        @auth
                            <!-- User Dropdown with Role Badge -->
                            <x-dropdown align="right" width="56">
                                <x-slot name="trigger">
                                    <button class="flex items-center gap-2.5 p-1.5 pl-3 rounded-xl border border-slate-200 hover:border-slate-300 bg-white transition-all">
                                        <div class="flex flex-col text-left">
                                            <span class="text-xs font-bold text-slate-900 max-w-[120px] truncate leading-tight">
                                                {{ Auth::user()->name }}
                                            </span>
                                            <span class="text-[10px] font-semibold uppercase tracking-wider text-primary-600">
                                                {{ Auth::user()->role->label() }}
                                            </span>
                                        </div>
                                        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-700 font-bold text-sm flex items-center justify-center">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="px-4 py-2 border-b border-slate-100 text-xs">
                                        <div class="font-medium text-slate-500">Masuk sebagai:</div>
                                        <div class="font-bold text-slate-900 truncate">{{ Auth::user()->email }}</div>
                                    </div>

                                    @if(Auth::user()->isAdmin())
                                        <x-dropdown-link :href="url('/admin/dashboard')">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                </svg>
                                                Dashboard Admin
                                            </span>
                                        </x-dropdown-link>
                                    @elseif(Auth::user()->isVendor())
                                        <x-dropdown-link :href="url('/vendor/dashboard')">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                Portal Toko Vendor
                                            </span>
                                        </x-dropdown-link>
                                    @endif

                                    <x-dropdown-link :href="url('/orders')">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            Pesanan Saya
                                        </span>
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('profile.edit')">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z" />
                                            </svg>
                                            Pengaturan Akun
                                        </span>
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();"
                                                class="text-rose-600 hover:text-rose-700">
                                            <span class="flex items-center gap-2 font-medium">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                                Keluar (Logout)
                                            </span>
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        @else
                            <!-- Guest Visitor Actions -->
                            <div class="flex items-center gap-2">
                                <a href="{{ route('login') }}" 
                                   class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-primary-600 transition-colors">
                                    Masuk
                                </a>
                                <a href="{{ route('register') }}" 
                                   class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md hover:scale-[1.02] active:scale-[0.98]">
                                    Daftar
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Mobile Search Bar (Visible on mobile only) -->
                <div class="pb-3 md:hidden">
                    <form action="{{ route('catalog.index') }}" method="GET" class="relative">
                        <input type="text"
                               name="q"
                               value="{{ request('q') }}"
                               placeholder="Cari alat kesehatan..."
                               class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm placeholder:text-slate-400">
                        <div class="absolute left-3 top-2.5 text-slate-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </form>
                </div>
            </x-container>
        </header>

        <!-- Main Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-border-subtle mt-16 pt-12 pb-8">
            <x-container>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-slate-100">
                    <!-- Brand info -->
                    <div class="md:col-span-1 space-y-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-primary-600 text-white flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <span class="text-xl font-bold tracking-tight text-slate-900">
                                Medi<span class="text-primary-600">fy</span>
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Marketplace resmi alat kesehatan multi-vendor di Indonesia. Menyediakan perlengkapan medis, diagnostik, terapi, dan alat bantu mobilitas berstandar Kemenkes RI.
                        </p>
                        <div class="flex items-center gap-2 text-xs font-semibold text-emerald-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Terverifikasi Multi-Vendor Resmi</span>
                        </div>
                    </div>

                    <!-- Kategori Cepat -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Kategori Produk</h4>
                        <ul class="space-y-2 text-xs text-slate-600">
                            <li><a href="{{ route('catalog.index', ['category' => 'diagnostik-monitoring']) }}" class="hover:text-primary-600 transition-colors">Diagnostik & Monitoring</a></li>
                            <li><a href="{{ route('catalog.index', ['category' => 'alat-bantu-mobilitas']) }}" class="hover:text-primary-600 transition-colors">Alat Bantu Mobilitas</a></li>
                            <li><a href="{{ route('catalog.index', ['category' => 'terapi-pernapasan']) }}" class="hover:text-primary-600 transition-colors">Terapi & Pernapasan</a></li>
                            <li><a href="{{ route('catalog.index', ['category' => 'sterilisasi-sanitasi-medis']) }}" class="hover:text-primary-600 transition-colors">Sterilisasi & Sanitasi</a></li>
                            <li><a href="{{ route('catalog.index', ['category' => 'pertolongan-pertama-p3k']) }}" class="hover:text-primary-600 transition-colors">Pertolongan Pertama (P3K)</a></li>
                        </ul>
                    </div>

                    <!-- Informasi Belanja & Vendor -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Layanan & Vendor</h4>
                        <ul class="space-y-2 text-xs text-slate-600">
                            <li><a href="{{ route('catalog.index') }}" class="hover:text-primary-600 transition-colors">Jelajah Katalog Belanja</a></li>
                            <li><span class="text-slate-400">Gratis Ongkos Kirim Rp0</span></li>
                            <li><span class="text-slate-400">Simulasi Pembayaran & COD</span></li>
                            <li><a href="{{ route('register') }}" class="hover:text-primary-600 transition-colors font-medium text-primary-700">Daftar Akun Baru</a></li>
                            <li><a href="{{ route('login') }}" class="hover:text-primary-600 transition-colors">Masuk ke Akun Anda</a></li>
                        </ul>
                    </div>

                    <!-- Informasi Bantuan & Standar -->
                    <div id="bantuan">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Pusat Bantuan & Legalitas</h4>
                        <ul class="space-y-2 text-xs text-slate-600">
                            <li><span class="text-slate-500">Email: support@medify.test</span></li>
                            <li><span class="text-slate-500">Jam Layanan: 08.00 - 17.00 WIB</span></li>
                            <li><span class="text-slate-400">Syarat & Ketentuan Layanan</span></li>
                            <li><span class="text-slate-400">Kebijakan Privasi Data</span></li>
                        </ul>
                    </div>
                </div>

                <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
                    <p>© {{ date('Y') }} Medify — Platform Marketplace Alat Kesehatan Multi-Vendor. Hak Cipta Dilindungi.</p>
                    <p class="font-mono text-[11px]">Sistem Asesmen LSP UPNVJT • Tech Stack: Laravel 12 & PostgreSQL</p>
                </div>
            </x-container>
        </footer>

        <!-- Toast Notifications Component -->
        <x-toast />
    </body>
</html>
