<x-storefront-layout title="Keranjang Belanja Multi-Vendor">

    <x-container class="py-8"
                 x-data="{
                     loading: false,
                     updateQty(itemId, newQty) {
                         if (this.loading || newQty < 1) return;
                         this.loading = true;

                         fetch(`{{ url('/ajax/cart/items') }}/${itemId}`, {
                             method: 'PATCH',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                 'Accept': 'application/json',
                             },
                             body: JSON.stringify({ quantity: newQty })
                         })
                         .then(res => res.json())
                         .then(res => {
                             if (res.data) {
                                 window.location.reload();
                             } else if (res.message) {
                                 window.dispatchEvent(new CustomEvent('toast', {
                                     detail: { message: res.message, type: 'danger' }
                                 }));
                                 this.loading = false;
                             }
                         })
                         .catch(() => {
                             window.dispatchEvent(new CustomEvent('toast', {
                                 detail: { message: 'Gagal memperbarui jumlah produk. Silakan coba lagi.', type: 'danger' }
                             }));
                             this.loading = false;
                         });
                     },
                     removeItem(itemId) {
                         if (this.loading) return;
                         if (!confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang belanja?')) return;
                         this.loading = true;

                         fetch(`{{ url('/ajax/cart/items') }}/${itemId}`, {
                             method: 'DELETE',
                             headers: {
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                 'Accept': 'application/json',
                             }
                         })
                         .then(res => res.json())
                         .then(res => {
                             if (res.data) {
                                 window.location.reload();
                             } else {
                                 this.loading = false;
                             }
                         })
                         .catch(() => {
                             window.dispatchEvent(new CustomEvent('toast', {
                                 detail: { message: 'Gagal menghapus produk.', type: 'danger' }
                             }));
                             this.loading = false;
                         });
                     },
                     clearCart() {
                         if (this.loading) return;
                         if (!confirm('Apakah Anda yakin ingin mengosongkan seluruh isi keranjang belanja?')) return;
                         this.loading = true;

                         fetch(`{{ route('ajax.cart.clear') }}`, {
                             method: 'DELETE',
                             headers: {
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                 'Accept': 'application/json',
                             }
                         })
                         .then(res => res.json())
                         .then(res => {
                             if (res.data) {
                                 window.location.reload();
                             } else {
                                 this.loading = false;
                             }
                         })
                         .catch(() => {
                             window.dispatchEvent(new CustomEvent('toast', {
                                 detail: { message: 'Gagal mengosongkan keranjang.', type: 'danger' }
                             }));
                             this.loading = false;
                         });
                     }
                 }">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
            <a href="{{ route('catalog.index') }}" class="hover:text-primary-600 transition-colors">Katalog</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Keranjang Belanja</span>
        </nav>

        <!-- Main Cart Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-200 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 font-sans flex items-center gap-3">
                    Keranjang Belanja
                    @if (! $cartSummary['is_empty'])
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-primary-100 text-primary-800">
                            {{ $cartSummary['total_items_count'] }} Produk
                        </span>
                    @endif
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Kelola produk alat kesehatan pilihan Anda dari berbagai toko vendor terverifikasi.
                </p>
            </div>

            @if (! $cartSummary['is_empty'])
                <button type="button"
                        @click="clearCart()"
                        :disabled="loading"
                        class="text-xs font-semibold text-rose-600 hover:text-rose-800 flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-rose-50 transition-colors self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Kosongkan Keranjang
                </button>
            @endif
        </div>

        @if ($cartSummary['is_empty'])
            <!-- Empty Cart State -->
            <div class="bg-white rounded-2xl border border-border-subtle shadow-card p-12 text-center max-w-xl mx-auto my-8">
                <div class="w-20 h-20 rounded-2xl bg-primary-50 text-primary-600 mx-auto flex items-center justify-center mb-5">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900 mb-2">Keranjang Belanja Anda Kosong</h2>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-md mx-auto mb-6">
                    Belum ada alat kesehatan yang dimasukkan. Temukan tensimeter, alat bantu jalan, nebulizer, dan perlengkapan medis berizin edar resmi di katalog kami.
                </p>
                <a href="{{ route('catalog.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-primary-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                    </svg>
                    Jelajahi Katalog Produk
                </a>
            </div>
        @else
            <!-- Two Columns Layout When Items Exist -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Items Grouped by Store (8 Cols) -->
                <div class="lg:col-span-8 space-y-6">
                    @foreach ($cartSummary['stores'] as $store)
                        <div class="bg-white rounded-2xl border border-border-subtle shadow-card overflow-hidden">
                            
                            <!-- Store Header -->
                            <div class="bg-slate-50/80 px-5 py-3.5 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-primary-600 text-white flex items-center justify-center text-xs font-bold">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-slate-900">{{ $store['store_name'] }}</span>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-200/70 text-slate-700">
                                            <svg class="w-3 h-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            {{ $store['store_city'] }}
                                        </span>
                                    </div>
                                </div>
                                <span class="text-xs font-medium text-emerald-600 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Gratis Ongkir Rp0
                                </span>
                            </div>

                            <!-- Item Rows -->
                            <div class="divide-y divide-slate-100 p-2 sm:p-4">
                                @foreach ($store['items'] as $item)
                                    <div class="p-3 sm:p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-colors hover:bg-slate-50/50 rounded-xl">
                                        
                                        <!-- Product Info -->
                                        <div class="flex items-start gap-3 sm:gap-4 flex-1 min-w-0">
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-slate-100 border border-slate-200 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                                @if (! empty($item['image_url']))
                                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['product_name'] }}" class="w-full h-full object-cover">
                                                @else
                                                    <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                                    </svg>
                                                @endif
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-sm font-bold text-slate-900 truncate leading-snug">
                                                    <a href="/products/{{ $item['product_slug'] }}" 
                                                       class="hover:text-primary-600 transition-colors">
                                                        {{ $item['product_name'] }}
                                                    </a>
                                                </h3>
                                                <div class="text-xs font-semibold text-primary-700 mt-1">
                                                    {{ $item['formatted_price'] }}
                                                </div>
                                                
                                                <!-- Stock Warning Indicator -->
                                                <div class="mt-1 text-[11px]">
                                                    @if (! $item['is_active'])
                                                        <span class="text-rose-600 font-semibold">Produk sedang tidak aktif</span>
                                                    @elseif ($item['stock'] < 1)
                                                        <span class="text-rose-600 font-semibold">Stok habis</span>
                                                    @elseif ($item['stock'] < 5)
                                                        <span class="text-amber-600 font-semibold">Sisa stok: {{ $item['stock'] }} unit</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quantity Stepper & Price Subtotal -->
                                        <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                            
                                            <!-- Interactive Stepper -->
                                            <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden bg-white shadow-sm">
                                                <button type="button"
                                                        @click="updateQty({{ $item['id'] }}, {{ $item['quantity'] - 1 }})"
                                                        :disabled="loading || {{ $item['quantity'] <= 1 ? 'true' : 'false' }}"
                                                        class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors font-bold text-base select-none">
                                                    -
                                                </button>
                                                <span class="w-10 text-center font-bold text-xs text-slate-900 select-none font-mono">
                                                    {{ $item['quantity'] }}
                                                </span>
                                                <button type="button"
                                                        @click="updateQty({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                                        :disabled="loading || {{ $item['quantity'] >= $item['stock'] ? 'true' : 'false' }}"
                                                        class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors font-bold text-base select-none">
                                                    +
                                                </button>
                                            </div>

                                            <!-- Line Subtotal -->
                                            <div class="text-right min-w-[100px]">
                                                <div class="text-[11px] text-slate-400">Subtotal</div>
                                                <div class="text-sm font-black text-slate-900 font-sans tracking-tight">
                                                    {{ $item['formatted_subtotal'] }}
                                                </div>
                                            </div>

                                            <!-- Trash Action -->
                                            <button type="button"
                                                    @click="removeItem({{ $item['id'] }})"
                                                    :disabled="loading"
                                                    title="Hapus Produk"
                                                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Store Subtotal Bar -->
                            <div class="bg-slate-50/50 px-5 py-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium">Subtotal Toko:</span>
                                <span class="font-extrabold text-slate-900">{{ $store['formatted_store_subtotal'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Right Column: Sticky Summary Card (4 Cols) -->
                <div class="lg:col-span-4 sticky top-24 space-y-4">
                    <div class="bg-white rounded-2xl border border-border-subtle shadow-card p-6">
                        <h2 class="text-base font-bold text-slate-900 pb-4 border-b border-slate-100 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Ringkasan Belanja
                        </h2>

                        <!-- Calculation Breakdown -->
                        <div class="py-4 space-y-3 text-xs border-b border-slate-100">
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Total Harga ({{ $cartSummary['total_items_count'] }} barang)</span>
                                <span class="font-bold text-slate-900">{{ $cartSummary['pricing']['formatted_subtotal'] }}</span>
                            </div>

                            <div class="flex items-center justify-between text-slate-600">
                                <div class="flex items-center gap-1.5">
                                    <span>PPN ({{ $cartSummary['pricing']['tax_rate_percent'] }}%)</span>
                                    <span class="text-[10px] text-slate-400" title="Tarif aktif PPN resmi">• Pajak Resmi</span>
                                </div>
                                <span class="font-bold text-slate-900">{{ $cartSummary['pricing']['formatted_tax_amount'] }}</span>
                            </div>

                            <div class="flex items-center justify-between text-slate-600">
                                <span class="flex items-center gap-1">
                                    Biaya Pengiriman
                                    <span class="text-[10px] text-emerald-600 font-semibold">• Promo</span>
                                </span>
                                <span class="font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                    {{ $cartSummary['pricing']['formatted_shipping_fee'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Grand Total -->
                        <div class="pt-4 pb-2">
                            <div class="flex items-baseline justify-between mb-4">
                                <div class="text-xs font-semibold text-slate-700">Total Pembayaran</div>
                                <div class="text-xl sm:text-2xl font-black text-primary-700 font-sans tracking-tight">
                                    {{ $cartSummary['pricing']['formatted_grand_total'] }}
                                </div>
                            </div>

                            <!-- Postpaid COD Notice -->
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 mb-5 text-[11px] leading-relaxed">
                                <div class="font-bold text-slate-800 flex items-center gap-1.5 mb-1">
                                    @if ($cartSummary['postpaid_eligibility']['eligible'])
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                    Metode Pembayaran
                                </div>
                                <span class="text-slate-600">{{ $cartSummary['postpaid_eligibility']['reason'] }}</span>
                            </div>

                            <!-- Checkout Action Button -->
                            <a href="#checkout"
                               @click.prevent="alert('Alur checkout dan pembayaran akan dihubungkan di TASK-014!')"
                               class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl shadow-md hover:shadow-lg shadow-primary-600/25 transition-all text-sm active:scale-[0.98]">
                                <span>Lanjut ke Pembayaran</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Trust Assurances Card -->
                    <div class="bg-white rounded-2xl border border-border-subtle p-5 space-y-3 text-xs text-slate-600">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-bold text-slate-800">Standar Kemenkes RI</div>
                                <div class="text-[11px] text-slate-400">Semua produk resmi dan berizin edar</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-bold text-slate-800">Simulasi Transaksi Aman</div>
                                <div class="text-[11px] text-slate-400">Uji belanja terisolasi tanpa risiko dana riil</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @endif

    </x-container>

</x-storefront-layout>
