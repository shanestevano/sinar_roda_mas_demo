<x-app-layout title="Tambah Master Data Ban">
    <div x-data="tireForm()" class="max-w-4xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('products.index') }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-1">
                    &larr; Kembali ke Daftar Ban
                </a>
                <h1 class="text-xl font-black text-white tracking-tight">Tambah Master Data Ban Baru</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('products.store') }}" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            @csrf

            <!-- Section 1: Identitas & Barcode -->
            <div>
                <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">
                    1. Identitas Ban & Kode Barcode
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kode SKU Ban *</label>
                        <input type="text" name="sku" x-model="sku" required
                               placeholder="Contoh: TIR-BS-185-65-R15"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-mono uppercase focus:ring-1 focus:ring-amber-500">
                        <span class="text-[10px] text-slate-400">Kode unik identifikasi stok toko</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Barcode Fisik</label>
                        <input type="text" name="barcode" value="{{ old('barcode') }}"
                               placeholder="Scan atau kosongkan (otomatis di-generate)"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-mono focus:ring-1 focus:ring-amber-500">
                        <span class="text-[10px] text-slate-400">Barcode stiker pada fisik ban untuk scanner kasir</span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Spesifikasi Teknis -->
            <div>
                <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">
                    2. Spesifikasi Teknis & Dimensi
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Merk / Brand Ban *</label>
                        <input type="text" name="brand" x-model="brand" @input="updateSku()" list="brandList" required
                               placeholder="Bridgestone, Michelin, Dunlop..."
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                        <datalist id="brandList">
                            @foreach($brands as $b)
                                <option value="{{ $b }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Seri / Tipe Kembangan (Pattern) *</label>
                        <input type="text" name="pattern" value="{{ old('pattern') }}" required
                               placeholder="Contoh: Ecopia EP150, Primacy 4..."
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kategori Ban</label>
                        <select name="type" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs">
                            <option value="Tubeless / Passenger Car">Tubeless / Passenger Car (Sedan/MPV/City Car)</option>
                            <option value="SUV / All-Terrain">SUV / All-Terrain (A/T, H/T, M/T)</option>
                            <option value="Commercial / Niaga Truk">Commercial / Niaga Truk Ringan (8PR/10PR)</option>
                            <option value="Performance / Luxury">Performance / Luxury Ultra High</option>
                        </select>
                    </div>
                </div>

                <!-- Dimensi Ukuran -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Lebar (Width mm)</label>
                        <input type="number" name="width" x-model.number="width" @input="updateSizeAndSku()" placeholder="185"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Aspek Rasio (%)</label>
                        <input type="number" name="aspect_ratio" x-model.number="aspectRatio" @input="updateSizeAndSku()" placeholder="65"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Ukuran Ring (Rim ")</label>
                        <input type="number" name="rim_size" x-model.number="rimSize" @input="updateSizeAndSku()" placeholder="15"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Format Tampilan Ukuran *</label>
                        <input type="text" name="size_display" x-model="sizeDisplay" required placeholder="185/65 R15"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-mono font-bold focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <!-- Kode DOT (PRD 4.3) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kode DOT Produksi (4 Digit)</label>
                        <input type="text" name="dot_code" x-model="dotCode" @input="updateDotYear()" maxlength="4" placeholder="Contoh: 3224 (Minggu 32 Thn 24)"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-mono focus:ring-1 focus:ring-amber-500">
                        <span class="text-[10px] text-slate-400">2 digit pertama = minggu, 2 digit terakhir = tahun</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Tahun Produksi DOT</label>
                        <input type="number" name="dot_year" x-model.number="dotYear" placeholder="2024"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-mono focus:ring-1 focus:ring-amber-500">
                        <span class="text-[10px] text-slate-400">Penting untuk deteksi dini dead stock / ban kadaluarsa</span>
                    </div>
                </div>
            </div>

            <!-- Section 3: Harga & Stok Gudang -->
            <div>
                <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">
                    3. Harga & Manajemen Stok Rak
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Harga Modal / HPP (Rp) *</label>
                        <input type="number" name="cost_price" value="{{ old('cost_price', 0) }}" required min="0"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-bold focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Harga Jual Toko (Rp) *</label>
                        <input type="number" name="selling_price" value="{{ old('selling_price', 0) }}" required min="0"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-bold focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Jumlah Stok Awal *</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" required min="0"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-bold focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Batas Minimum Peringatan</label>
                        <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" required min="1"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Lokasi Rak Gudang</label>
                        <select name="rack_location_id" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs">
                            <option value="">-- Pilih Lokasi Rak --</option>
                            @foreach($rackLocations as $rack)
                                <option value="{{ $rack->id }}" {{ old('rack_location_id') == $rack->id ? 'selected' : '' }}>
                                    {{ $rack->code }} ({{ $rack->zone }} - {{ $rack->description }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Catatan Tambahan (Mobil yang cocok)</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Contoh: Cocok untuk Toyota Avanza, Xenia, Mobilio"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('products.index') }}" class="px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20">
                    Simpan Master Data Ban
                </button>
            </div>
        </form>
    </div>

    <script>
        function tireForm() {
            return {
                brand: '',
                width: null,
                aspectRatio: null,
                rimSize: null,
                sizeDisplay: '',
                sku: '',
                dotCode: '',
                dotYear: null,

                updateSizeAndSku() {
                    if (this.width && this.aspectRatio && this.rimSize) {
                        this.sizeDisplay = `${this.width}/${this.aspectRatio} R${this.rimSize}`;
                    }
                    this.updateSku();
                },

                updateSku() {
                    let b = (this.brand || 'TIR').substring(0, 2).toUpperCase();
                    let w = this.width || '000';
                    let a = this.aspectRatio || '00';
                    let r = this.rimSize || '00';
                    if (!this.sku || this.sku.startsWith('TIR-')) {
                        this.sku = `TIR-${b}-${w}-${a}-R${r}`;
                    }
                },

                updateDotYear() {
                    if (this.dotCode && this.dotCode.length === 4) {
                        let yr = parseInt(this.dotCode.substring(2, 4));
                        if (!isNaN(yr)) {
                            this.dotYear = 2000 + yr;
                        }
                    }
                }
            };
        }
    </script>
</x-app-layout>
