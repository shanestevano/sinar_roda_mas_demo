<x-app-layout title="Edit Master Data Ban">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('products.index') }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-1">
                    &larr; Kembali ke Daftar Ban
                </a>
                <h1 class="text-xl font-black text-white tracking-tight">Edit Data Ban: {{ $tire->sku }}</h1>
            </div>
            <a href="{{ route('products.barcode', $tire->id) }}" target="_blank"
               class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 text-xs font-bold border border-slate-700 flex items-center gap-2">
                <span>Cetak Barcode</span>
            </a>
        </div>

        <form method="POST" action="{{ route('products.update', $tire->id) }}" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Identitas -->
            <div>
                <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">
                    1. Identitas Ban & Barcode
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kode SKU Ban *</label>
                        <input type="text" name="sku" value="{{ old('sku', $tire->sku) }}" required
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-mono uppercase focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Barcode Fisik</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $tire->barcode) }}"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-mono focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>
            </div>

            <!-- Section 2: Spesifikasi Teknis -->
            <div>
                <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">
                    2. Spesifikasi Teknis
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Merk / Brand Ban *</label>
                        <input type="text" name="brand" value="{{ old('brand', $tire->brand) }}" list="brandList" required
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                        <datalist id="brandList">
                            @foreach($brands as $b)
                                <option value="{{ $b }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Seri / Tipe Kembangan (Pattern) *</label>
                        <input type="text" name="pattern" value="{{ old('pattern', $tire->pattern) }}" required
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kategori Ban</label>
                        <select name="type" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs">
                            <option value="Tubeless / Passenger Car" {{ $tire->type === 'Tubeless / Passenger Car' ? 'selected' : '' }}>Tubeless / Passenger Car</option>
                            <option value="SUV / All-Terrain" {{ $tire->type === 'SUV / All-Terrain' ? 'selected' : '' }}>SUV / All-Terrain</option>
                            <option value="Commercial / Niaga Truk" {{ $tire->type === 'Commercial / Niaga Truk' ? 'selected' : '' }}>Commercial / Niaga Truk</option>
                            <option value="Performance / Luxury" {{ $tire->type === 'Performance / Luxury' ? 'selected' : '' }}>Performance / Luxury</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Lebar (Width mm)</label>
                        <input type="number" name="width" value="{{ old('width', $tire->width) }}"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Aspek Rasio (%)</label>
                        <input type="number" name="aspect_ratio" value="{{ old('aspect_ratio', $tire->aspect_ratio) }}"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Ukuran Ring (Rim ")</label>
                        <input type="number" name="rim_size" value="{{ old('rim_size', $tire->rim_size) }}"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Format Tampilan Ukuran *</label>
                        <input type="text" name="size_display" value="{{ old('size_display', $tire->size_display) }}" required
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kode DOT Produksi (4 Digit)</label>
                        <input type="text" name="dot_code" value="{{ old('dot_code', $tire->dot_code) }}" maxlength="4"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Tahun Produksi DOT</label>
                        <input type="number" name="dot_year" value="{{ old('dot_year', $tire->dot_year) }}"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-mono">
                    </div>
                </div>
            </div>

            <!-- Section 3: Harga & Lokasi -->
            <div>
                <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">
                    3. Harga & Pengaturan Rak
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Harga Modal / HPP (Rp) *</label>
                        <input type="number" name="cost_price" value="{{ old('cost_price', (float)$tire->cost_price) }}" required min="0"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Harga Jual Toko (Rp) *</label>
                        <input type="number" name="selling_price" value="{{ old('selling_price', (float)$tire->selling_price) }}" required min="0"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Batas Minimum Peringatan</label>
                        <input type="number" name="min_stock" value="{{ old('min_stock', $tire->min_stock) }}" required min="1"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Lokasi Rak Gudang</label>
                        <select name="rack_location_id" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs">
                            <option value="">-- Pilih Lokasi Rak --</option>
                            @foreach($rackLocations as $rack)
                                <option value="{{ $rack->id }}" {{ old('rack_location_id', $tire->rack_location_id) == $rack->id ? 'selected' : '' }}>
                                    {{ $rack->code }} ({{ $rack->zone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Status Keaktifan</label>
                        <select name="is_active" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs">
                            <option value="1" {{ $tire->is_active ? 'selected' : '' }}>Aktif (Tersedia Dijual)</option>
                            <option value="0" {{ !$tire->is_active ? 'selected' : '' }}>Nonaktif / Arsip</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Stok Saat Ini (Sistem)</label>
                        <div class="px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-amber-400 text-xs font-black">
                            {{ $tire->stock }} unit
                            <span class="text-[10px] text-slate-400 font-normal block">* Gunakan menu Stock Opname untuk penyesuaian stok fisik</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Catatan Tambahan</label>
                    <input type="text" name="notes" value="{{ old('notes', $tire->notes) }}"
                           class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('products.index') }}" class="px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
