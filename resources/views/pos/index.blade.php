<x-app-layout title="Kasir Point of Sales (POS)">
    <div x-data="posApp()" x-init="init()" class="space-y-6">
        
        <!-- Header Info Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Kasir Penjualan Ban</h2>
                    <p class="text-xs text-slate-500">
                        Shift Kasir: 
                        <strong class="text-emerald-600 font-semibold">{{ $currentShift ? 'Aktif (' . $currentShift->opened_at->format('H:i') . ')' : 'Belum Dibuka' }}</strong>
                        • Kasir: {{ auth()->user()->name }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('shifts.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200 transition">
                    Rekap Shift
                </a>
                <a href="{{ route('pos.history') }}" class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200 transition">
                    Riwayat Transaksi
                </a>
            </div>
        </div>

        <!-- Main POS Grid: Left is Search & Product Catalog, Right is Cart & Payment -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left Side: Smart Search & Catalog (Col 7) -->
            <div class="lg:col-span-7 space-y-4">
                
                <!-- Smart Search Input Box (Barcode & Text) -->
                <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs space-y-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               x-model="searchQuery"
                               @input.debounce.250ms="performSearch()"
                               @keydown.enter.prevent="handleBarcodeOrEnter()"
                               placeholder="Scan Barcode atau ketik Merk / Ukuran / Ring / Kode DOT (e.g. 185/65, Avanza, Bridgestone)..."
                               class="w-full pl-11 pr-24 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition text-xs">
                        
                        <div class="absolute inset-y-0 right-2 flex items-center">
                            <span class="text-[10px] bg-slate-200 text-slate-600 px-2 py-1 rounded-md font-mono font-semibold">
                                ENTER = Pilih
                            </span>
                        </div>
                    </div>

                    <!-- Search Results Dropdown / Grid -->
                    <div x-show="searchResults.length > 0" class="max-h-72 overflow-y-auto space-y-2 pr-1">
                        <template x-for="tire in searchResults" :key="tire.id">
                            <div @click="addToCart(tire)"
                                 class="p-3 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-200 hover:border-blue-300 cursor-pointer transition flex items-center justify-between gap-3 group">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition" x-text="tire.brand + ' ' + tire.pattern"></span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 font-mono" x-text="tire.size_display"></span>
                                        <template x-if="tire.dot_code">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-slate-100 text-slate-600" x-text="'DOT: ' + tire.dot_code"></span>
                                        </template>
                                        <template x-if="tire.is_aged">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">DOT Lama</span>
                                        </template>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-3">
                                        <span x-text="'SKU: ' + tire.sku"></span>
                                        <span x-text="'Rak: ' + tire.rack_code"></span>
                                        <span :class="tire.stock <= 5 ? 'text-rose-600 font-semibold' : 'text-emerald-600 font-semibold'"
                                              x-text="'Stok: ' + tire.stock + ' unit'"></span>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div class="text-xs font-bold text-slate-900" x-text="formatRupiah(tire.selling_price)"></div>
                                    <span class="text-[10px] text-blue-600 font-semibold group-hover:underline">+ Tambah</span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="isSearching" class="text-center py-4 text-xs text-slate-400">
                        Mencari database stok ban...
                    </div>
                </div>

                <!-- Popular / Quick Tires Grid -->
                <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center justify-between mb-3.5">
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Katalog Cepat Ban Populer</h3>
                        <span class="text-[11px] text-slate-400">Klik ban untuk langsung masuk keranjang</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($popularTires as $pTire)
                            <button type="button"
                                    @click="addToCart({
                                        id: {{ $pTire->id }},
                                        sku: '{{ $pTire->sku }}',
                                        brand: '{{ $pTire->brand }}',
                                        pattern: '{{ $pTire->pattern }}',
                                        size_display: '{{ $pTire->size_display }}',
                                        selling_price: {{ (float) $pTire->selling_price }},
                                        stock: {{ $pTire->stock }},
                                        dot_code: '{{ $pTire->dot_code }}',
                                        rack_code: '{{ $pTire->rackLocation ? $pTire->rackLocation->code : '-' }}'
                                    })"
                                    class="p-3 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-200/80 hover:border-blue-300 text-left transition flex flex-col justify-between h-28 group">
                                <div>
                                    <div class="text-xs font-bold text-slate-900 group-hover:text-blue-600 truncate">{{ $pTire->brand }} {{ $pTire->pattern }}</div>
                                    <div class="text-[11px] font-mono text-slate-600 font-semibold mt-0.5">{{ $pTire->size_display }}</div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] pt-2 border-t border-slate-200">
                                    <span class="text-slate-500">Stok: <strong class="{{ $pTire->stock <= 5 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $pTire->stock }}</strong></span>
                                    <span class="font-bold text-slate-900 text-xs">Rp {{ number_format($pTire->selling_price / 1000, 0) }}k</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Side: Cart, Customer Info & Checkout (Col 5) -->
            <div class="lg:col-span-5 bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs space-y-4">
                
                <!-- Cart Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Keranjang Transaksi</h3>
                    </div>
                    <button type="button" @click="clearCart()" x-show="cart.length > 0"
                            class="text-xs text-rose-600 hover:underline font-semibold">
                        Kosongkan
                    </button>
                </div>

                <!-- Customer Details -->
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Pelanggan</label>
                        <input type="text" x-model="customerName" placeholder="Pelanggan Umum"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:ring-1 focus:ring-blue-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">No Polisi Kendaraan</label>
                        <input type="text" x-model="vehiclePlate" placeholder="Contoh: B 1234 ABC"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs uppercase focus:ring-1 focus:ring-blue-500 focus:bg-white">
                    </div>
                </div>

                <!-- Cart Items List -->
                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-2.5">
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-bold text-slate-900 truncate" x-text="item.brand + ' ' + item.pattern"></div>
                                <div class="text-[11px] text-slate-500" x-text="item.size_display + ' (' + formatRupiah(item.unit_price) + ')'"></div>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button type="button" @click="decrementQty(index)"
                                        class="w-6 h-6 rounded-md bg-white hover:bg-slate-200 border border-slate-300 text-slate-700 font-bold flex items-center justify-center text-xs">
                                    -
                                </button>
                                <span class="w-7 text-center text-xs font-bold text-slate-900" x-text="item.quantity"></span>
                                <button type="button" @click="incrementQty(index)"
                                        class="w-6 h-6 rounded-md bg-white hover:bg-slate-200 border border-slate-300 text-slate-700 font-bold flex items-center justify-center text-xs">
                                    +
                                </button>
                            </div>

                            <div class="text-right flex-shrink-0 min-w-16">
                                <div class="text-xs font-bold text-slate-900" x-text="formatRupiah(item.quantity * item.unit_price)"></div>
                                <button type="button" @click="removeFromCart(index)" class="text-[10px] text-rose-600 hover:underline">Hapus</button>
                            </div>
                        </div>
                    </template>

                    <div x-show="cart.length === 0" class="text-center py-7 text-slate-400 text-xs">
                        Keranjang masih kosong.<br>Scan barcode atau cari ban di sebelah kiri.
                    </div>
                </div>

                <!-- Calculation Summary -->
                <div class="space-y-2 pt-3 border-t border-slate-100 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span>Subtotal Barang:</span>
                        <span class="font-bold text-slate-900" x-text="formatRupiah(subtotal)"></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span>Potongan Diskon (Rp):</span>
                        <input type="number" x-model.number="discountAmount" min="0" placeholder="0"
                               class="w-32 px-2.5 py-1 bg-slate-50 border border-slate-300 rounded-lg text-right text-xs text-slate-900 focus:bg-white">
                    </div>

                    <div class="flex justify-between text-base font-bold text-slate-900 pt-2 border-t border-slate-200">
                        <span>Grand Total:</span>
                        <span class="text-blue-600" x-text="formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <!-- Payment Methods Selector -->
                <div class="space-y-1.5 pt-2 border-t border-slate-100">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Metode Pembayaran</label>
                    <div class="grid grid-cols-4 gap-1.5">
                        <button type="button" @click="paymentMethod = 'cash'"
                                :class="paymentMethod === 'cash' ? 'bg-blue-600 text-white font-bold border-blue-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                class="py-2 px-1 rounded-xl border text-xs font-semibold transition text-center">
                            Tunai
                        </button>
                        <button type="button" @click="paymentMethod = 'transfer'"
                                :class="paymentMethod === 'transfer' ? 'bg-blue-600 text-white font-bold border-blue-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                class="py-2 px-1 rounded-xl border text-xs font-semibold transition text-center">
                            Transfer
                        </button>
                        <button type="button" @click="paymentMethod = 'qris'"
                                :class="paymentMethod === 'qris' ? 'bg-blue-600 text-white font-bold border-blue-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                class="py-2 px-1 rounded-xl border text-xs font-semibold transition text-center">
                            QRIS
                        </button>
                        <button type="button" @click="paymentMethod = 'debit'"
                                :class="paymentMethod === 'debit' ? 'bg-blue-600 text-white font-bold border-blue-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                class="py-2 px-1 rounded-xl border text-xs font-semibold transition text-center">
                            Debit
                        </button>
                    </div>
                </div>

                <!-- Cash Payment Inputs & Fast Buttons -->
                <div x-show="paymentMethod === 'cash'" class="space-y-2.5 pt-1">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nominal Diterima (Rp)</label>
                        <input type="number" x-model.number="amountPaid"
                               class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-bold text-base focus:ring-2 focus:ring-blue-500 focus:bg-white text-right">
                    </div>

                    <!-- Quick Cash Buttons -->
                    <div class="flex flex-wrap gap-1">
                        <button type="button" @click="amountPaid = grandTotal" class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-[11px] font-bold text-blue-700 border border-blue-200">
                            Uang Pas
                        </button>
                        <button type="button" @click="amountPaid = 500000" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-700">
                            500rb
                        </button>
                        <button type="button" @click="amountPaid = 1000000" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-700">
                            1 Juta
                        </button>
                        <button type="button" @click="amountPaid = 2000000" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-700">
                            2 Juta
                        </button>
                        <button type="button" @click="amountPaid = 5000000" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] text-slate-700">
                            5 Juta
                        </button>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex justify-between items-center text-xs">
                        <span class="text-slate-500">Kembalian:</span>
                        <span class="text-sm font-bold text-emerald-600" x-text="formatRupiah(changeAmount)"></span>
                    </div>
                </div>

                <!-- Submit Checkout Button -->
                <button type="button"
                        @click="processCheckout()"
                        :disabled="cart.length === 0 || isProcessing"
                        class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-xs uppercase tracking-wider shadow-sm transition active:scale-[0.99] flex items-center justify-center gap-2">
                    <svg x-show="!isProcessing" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="isProcessing ? 'Memproses Checkout...' : 'Bayar & Selesaikan Transaksi'"></span>
                </button>
            </div>
        </div>

        <!-- Success Checkout Modal with Printable Receipt Button -->
        <div x-show="lastTransaction !== null" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-slate-900">Transaksi Berhasil!</h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="'No Faktur: ' + (lastTransaction?.invoice_number || '')"></p>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-left space-y-2 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>Total Belanja:</span>
                        <span class="font-bold text-slate-900" x-text="formatRupiah(lastTransaction?.grand_total || 0)"></span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Nominal Dibayar:</span>
                        <span class="font-bold text-slate-900" x-text="formatRupiah(lastTransaction?.amount_paid || 0)"></span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Kembalian:</span>
                        <span class="font-bold text-emerald-600" x-text="formatRupiah(lastTransaction?.change_amount || 0)"></span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 pt-1">
                    <a :href="lastReceiptUrl" target="_blank"
                       class="flex-1 py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak Struk Kasir</span>
                    </a>
                    <button type="button" @click="closeSuccessModal()"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs">
                        Transaksi Baru
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine.js POS Logic -->
    <script>
        function posApp() {
            return {
                searchQuery: '',
                searchResults: [],
                isSearching: false,
                cart: [],
                customerName: 'Pelanggan Umum',
                vehiclePlate: '',
                paymentMethod: 'cash',
                discountAmount: 0,
                amountPaid: 0,
                isProcessing: false,
                lastTransaction: null,
                lastReceiptUrl: '#',

                init() {
                    const saved = localStorage.getItem('srm_pos_cart');
                    if (saved) {
                        try {
                            this.cart = JSON.parse(saved);
                        } catch(e) {}
                    }

                    this.$watch('cart', (val) => {
                        localStorage.setItem('srm_pos_cart', JSON.stringify(val));
                    });
                },

                async performSearch() {
                    const q = this.searchQuery.trim();
                    if (q.length === 0) {
                        this.searchResults = [];
                        return;
                    }
                    this.isSearching = true;
                    try {
                        const res = await fetch(`{{ route('pos.search') }}?q=${encodeURIComponent(q)}`);
                        if (res.ok) {
                            this.searchResults = await res.json();
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isSearching = false;
                    }
                },

                handleBarcodeOrEnter() {
                    if (this.searchResults.length > 0) {
                        this.addToCart(this.searchResults[0]);
                        this.searchQuery = '';
                        this.searchResults = [];
                    } else if (this.searchQuery.trim().length > 0) {
                        this.performSearch().then(() => {
                            if (this.searchResults.length > 0) {
                                this.addToCart(this.searchResults[0]);
                                this.searchQuery = '';
                                this.searchResults = [];
                            }
                        });
                    }
                },

                addToCart(tire) {
                    if (tire.stock <= 0) {
                        alert(`Stok ban ${tire.brand} ${tire.pattern} habis!`);
                        return;
                    }

                    const existingIndex = this.cart.findIndex(i => i.id === tire.id);
                    if (existingIndex > -1) {
                        if (this.cart[existingIndex].quantity + 1 > tire.stock) {
                            alert(`Jumlah melebihi stok yang tersedia (${tire.stock} unit).`);
                            return;
                        }
                        this.cart[existingIndex].quantity++;
                    } else {
                        this.cart.push({
                            id: tire.id,
                            sku: tire.sku,
                            brand: tire.brand,
                            pattern: tire.pattern,
                            size_display: tire.size_display,
                            unit_price: parseFloat(tire.selling_price),
                            stock: tire.stock,
                            quantity: 1
                        });
                    }

                    if (this.paymentMethod === 'cash' && (this.amountPaid === 0 || this.amountPaid < this.grandTotal)) {
                        this.amountPaid = this.grandTotal;
                    }
                },

                incrementQty(index) {
                    const item = this.cart[index];
                    if (item.quantity + 1 > item.stock) {
                        alert(`Stok hanya tersedia ${item.stock} unit.`);
                        return;
                    }
                    item.quantity++;
                },

                decrementQty(index) {
                    if (this.cart[index].quantity > 1) {
                        this.cart[index].quantity--;
                    } else {
                        this.removeFromCart(index);
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                clearCart() {
                    if (confirm('Kosongkan semua barang dari keranjang?')) {
                        this.cart = [];
                        localStorage.removeItem('srm_pos_cart');
                    }
                },

                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
                },

                get grandTotal() {
                    return Math.max(0, this.subtotal - (this.discountAmount || 0));
                },

                get changeAmount() {
                    if (this.paymentMethod !== 'cash') return 0;
                    return Math.max(0, (this.amountPaid || 0) - this.grandTotal);
                },

                formatRupiah(num) {
                    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
                },

                async processCheckout() {
                    if (this.cart.length === 0) return;

                    if (this.paymentMethod === 'cash' && (this.amountPaid || 0) < this.grandTotal) {
                        alert('Nominal uang tunai yang diterima kurang dari total belanja!');
                        return;
                    }

                    this.isProcessing = true;
                    try {
                        const payload = {
                            customer_name: this.customerName,
                            vehicle_plate: this.vehiclePlate,
                            payment_method: this.paymentMethod,
                            amount_paid: this.paymentMethod === 'cash' ? this.amountPaid : this.grandTotal,
                            discount_amount: this.discountAmount || 0,
                            items: this.cart.map(i => ({
                                tire_id: i.id,
                                quantity: i.quantity,
                                unit_price: i.unit_price
                            }))
                        };

                        const response = await fetch(`{{ route('pos.checkout') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();
                        if (response.ok && data.success) {
                            this.lastTransaction = data.transaction;
                            this.lastReceiptUrl = data.receipt_url;
                            this.cart = [];
                            localStorage.removeItem('srm_pos_cart');
                            this.searchQuery = '';
                            this.searchResults = [];
                            this.vehiclePlate = '';
                            this.customerName = 'Pelanggan Umum';
                            this.discountAmount = 0;
                            this.amountPaid = 0;
                        } else {
                            alert(data.message || 'Gagal memproses transaksi.');
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan jaringan atau server: ' + e.message);
                    } finally {
                        this.isProcessing = false;
                    }
                },

                closeSuccessModal() {
                    this.lastTransaction = null;
                }
            };
        }
    </script>
</x-app-layout>