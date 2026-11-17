<x-app-layout title="Kasir Point of Sales (POS)">
    <div x-data="posApp()" x-init="init()" class="space-y-6">
        
        <!-- Header Info Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 border border-slate-800 rounded-2xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-black text-white tracking-tight">Kasir Penjualan Ban</h1>
                    <p class="text-xs text-slate-400">
                        Shift Kasir: 
                        <strong class="text-emerald-400">{{ $currentShift ? 'Aktif (' . $currentShift->opened_at->format('H:i') . ')' : 'Belum Dibuka' }}</strong>
                        • Kasir: {{ auth()->user()->name }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('shifts.index') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition">
                    Rekap Shift
                </a>
                <a href="{{ route('pos.history') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition">
                    Riwayat Transaksi
                </a>
            </div>
        </div>

        <!-- Main POS Grid: Left is Search & Product Catalog, Right is Cart & Payment -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left Side: Smart Search & Catalog (Col 7) -->
            <div class="lg:col-span-7 space-y-4">
                
                <!-- Smart Search Input Box (Barcode & Text) -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl space-y-4">
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
                               class="w-full pl-12 pr-28 py-3.5 bg-slate-950 border border-slate-700 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-sm">
                        
                        <div class="absolute inset-y-0 right-2 flex items-center gap-1">
                            <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-1 rounded-lg border border-slate-700 font-mono">
                                ENTER = Pilih
                            </span>
                        </div>
                    </div>

                    <!-- Search Results Dropdown / Grid -->
                    <div x-show="searchResults.length > 0" class="max-h-72 overflow-y-auto space-y-2 pr-1">
                        <template x-for="tire in searchResults" :key="tire.id">
                            <div @click="addToCart(tire)"
                                 class="p-3 rounded-2xl bg-slate-950 hover:bg-slate-800/80 border border-slate-800 hover:border-amber-500/50 cursor-pointer transition flex items-center justify-between gap-3 group">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black text-white group-hover:text-amber-400 transition" x-text="tire.brand + ' ' + tire.pattern"></span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-amber-300 font-mono" x-text="tire.size_display"></span>
                                        <template x-if="tire.dot_code">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-slate-800 text-slate-300" x-text="'DOT: ' + tire.dot_code"></span>
                                        </template>
                                        <template x-if="tire.is_aged">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">DOT Lama</span>
                                        </template>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-3">
                                        <span x-text="'SKU: ' + tire.sku"></span>
                                        <span x-text="'Rak: ' + tire.rack_code"></span>
                                        <span :class="tire.stock <= 5 ? 'text-rose-400 font-bold' : 'text-emerald-400 font-bold'"
                                              x-text="'Stok: ' + tire.stock + ' unit'"></span>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div class="text-xs font-black text-white" x-text="formatRupiah(tire.selling_price)"></div>
                                    <span class="text-[10px] text-amber-400 font-semibold group-hover:underline">+ Tambah</span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="isSearching" class="text-center py-4 text-xs text-slate-400">
                        Mencari database stok ban...
                    </div>
                </div>

                <!-- Popular / Quick Tires Grid -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Katalog Cepat Ban Populer</h2>
                        <span class="text-[11px] text-slate-500">Klik ban untuk langsung masuk keranjang</span>
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
                                    class="p-3 rounded-2xl bg-slate-950 hover:bg-slate-800 border border-slate-800 hover:border-amber-500/50 text-left transition flex flex-col justify-between h-28 group">
                                <div>
                                    <div class="text-xs font-bold text-white group-hover:text-amber-400 truncate">{{ $pTire->brand }} {{ $pTire->pattern }}</div>
                                    <div class="text-[11px] font-mono text-amber-300 font-semibold mt-0.5">{{ $pTire->size_display }}</div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] pt-2 border-t border-slate-800/80">
                                    <span class="text-slate-400">Stok: <strong class="{{ $pTire->stock <= 5 ? 'text-rose-400' : 'text-emerald-400' }}">{{ $pTire->stock }}</strong></span>
                                    <span class="font-bold text-white text-xs">Rp {{ number_format($pTire->selling_price / 1000, 0) }}k</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Side: Cart, Customer Info & Checkout (Col 5) -->
            <div class="lg:col-span-5 bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-2xl space-y-5">
                
                <!-- Cart Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Keranjang Transaksi</h2>
                    </div>
                    <button type="button" @click="clearCart()" x-show="cart.length > 0"
                            class="text-xs text-rose-400 hover:underline font-semibold">
                        Kosongkan
                    </button>
                </div>

                <!-- Customer Details -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Pelanggan</label>
                        <input type="text" x-model="customerName" placeholder="Pelanggan Umum"
                               class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">No Polisi Kendaraan</label>
                        <input type="text" x-model="vehiclePlate" placeholder="Contoh: B 1234 ABC"
                               class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs uppercase focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <!-- Cart Items List -->
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-bold text-white truncate" x-text="item.brand + ' ' + item.pattern"></div>
                                <div class="text-[11px] text-slate-400" x-text="item.size_display + ' (' + formatRupiah(item.unit_price) + ')'"></div>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <button type="button" @click="decrementQty(index)"
                                        class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold flex items-center justify-center text-xs">
                                    -
                                </button>
                                <span class="w-8 text-center text-xs font-bold text-amber-400" x-text="item.quantity"></span>
                                <button type="button" @click="incrementQty(index)"
                                        class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold flex items-center justify-center text-xs">
                                    +
                                </button>
                            </div>

                            <div class="text-right flex-shrink-0 min-w-16">
                                <div class="text-xs font-bold text-white" x-text="formatRupiah(item.quantity * item.unit_price)"></div>
                                <button type="button" @click="removeFromCart(index)" class="text-[10px] text-rose-400 hover:underline">Hapus</button>
                            </div>
                        </div>
                    </template>

                    <div x-show="cart.length === 0" class="text-center py-8 text-slate-500 text-xs">
                        Keranjang masih kosong.<br>Scan barcode atau cari ban di sebelah kiri.
                    </div>
                </div>

                <!-- Calculation Summary -->
                <div class="space-y-2 pt-3 border-t border-slate-800 text-xs text-slate-300">
                    <div class="flex justify-between">
                        <span>Subtotal Barang:</span>
                        <span class="font-bold text-white" x-text="formatRupiah(subtotal)"></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span>Potongan Diskon (Rp):</span>
                        <input type="number" x-model.number="discountAmount" min="0" placeholder="0"
                               class="w-32 px-2.5 py-1 bg-slate-950 border border-slate-800 rounded-lg text-right text-xs text-white">
                    </div>

                    <div class="flex justify-between text-base font-black text-amber-400 pt-2 border-t border-slate-800">
                        <span>Grand Total:</span>
                        <span x-text="formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <!-- Payment Methods Selector -->
                <div class="space-y-2 pt-3 border-t border-slate-800">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Metode Pembayaran</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" @click="paymentMethod = 'cash'"
                                :class="paymentMethod === 'cash' ? 'bg-amber-500 text-slate-950 font-black border-amber-400' : 'bg-slate-950 text-slate-300 border-slate-800'"
                                class="py-2 px-1 rounded-xl border text-xs font-bold transition text-center">
                            Tunai (Cash)
                        </button>
                        <button type="button" @click="paymentMethod = 'transfer'"
                                :class="paymentMethod === 'transfer' ? 'bg-amber-500 text-slate-950 font-black border-amber-400' : 'bg-slate-950 text-slate-300 border-slate-800'"
                                class="py-2 px-1 rounded-xl border text-xs font-bold transition text-center">
                            Transfer
                        </button>
                        <button type="button" @click="paymentMethod = 'qris'"
                                :class="paymentMethod === 'qris' ? 'bg-amber-500 text-slate-950 font-black border-amber-400' : 'bg-slate-950 text-slate-300 border-slate-800'"
                                class="py-2 px-1 rounded-xl border text-xs font-bold transition text-center">
                            QRIS
                        </button>
                        <button type="button" @click="paymentMethod = 'debit'"
                                :class="paymentMethod === 'debit' ? 'bg-amber-500 text-slate-950 font-black border-amber-400' : 'bg-slate-950 text-slate-300 border-slate-800'"
                                class="py-2 px-1 rounded-xl border text-xs font-bold transition text-center">
                            Debit Card
                        </button>
                    </div>
                </div>

                <!-- Cash Payment Inputs & Fast Buttons -->
                <div x-show="paymentMethod === 'cash'" class="space-y-3 pt-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nominal Diterima (Rp)</label>
                        <input type="number" x-model.number="amountPaid"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-bold text-base focus:ring-2 focus:ring-amber-500 text-right">
                    </div>

                    <!-- Quick Cash Buttons -->
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" @click="amountPaid = grandTotal" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-amber-300">
                            Uang Pas
                        </button>
                        <button type="button" @click="amountPaid = 500000" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300">
                            500rb
                        </button>
                        <button type="button" @click="amountPaid = 1000000" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300">
                            1 Juta
                        </button>
                        <button type="button" @click="amountPaid = 2000000" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300">
                            2 Juta
                        </button>
                        <button type="button" @click="amountPaid = 5000000" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300">
                            5 Juta
                        </button>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex justify-between items-center text-xs">
                        <span class="text-slate-400">Kembalian:</span>
                        <span class="text-sm font-black text-emerald-400" x-text="formatRupiah(changeAmount)"></span>
                    </div>
                </div>

                <!-- Submit Checkout Button -->
                <button type="button"
                        @click="processCheckout()"
                        :disabled="cart.length === 0 || isProcessing"
                        class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 disabled:opacity-40 disabled:cursor-not-allowed text-slate-950 font-black text-sm uppercase tracking-wider shadow-lg shadow-emerald-500/20 transition active:scale-[0.99] flex items-center justify-center gap-2">
                    <svg x-show="!isProcessing" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="isProcessing ? 'Memproses Checkout...' : 'Bayar & Selesaikan Transaksi'"></span>
                </button>
            </div>
        </div>

        <!-- Success Checkout Modal with Printable Receipt Button -->
        <div x-show="lastTransaction !== null" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl text-center space-y-4">
                <div class="w-14 h-14 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <div>
                    <h3 class="text-xl font-black text-white">Transaksi Berhasil!</h3>
                    <p class="text-xs text-slate-400 mt-1" x-text="'No Faktur: ' + (lastTransaction?.invoice_number || '')"></p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-left space-y-2 text-xs">
                    <div class="flex justify-between text-slate-400">
                        <span>Total Belanja:</span>
                        <span class="font-bold text-white" x-text="formatRupiah(lastTransaction?.grand_total || 0)"></span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Nominal Dibayar:</span>
                        <span class="font-bold text-white" x-text="formatRupiah(lastTransaction?.amount_paid || 0)"></span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Kembalian:</span>
                        <span class="font-black text-emerald-400" x-text="formatRupiah(lastTransaction?.change_amount || 0)"></span>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <a :href="lastReceiptUrl" target="_blank"
                       class="flex-1 py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak Struk Kasir</span>
                    </a>
                    <button type="button" @click="closeSuccessModal()"
                            class="px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">
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
                    // Load cart from localStorage if exists
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

                    // Auto set amountPaid for convenience if in cash
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
