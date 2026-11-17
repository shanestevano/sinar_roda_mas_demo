<x-app-layout title="Pengaturan Toko & Struk (Admin)">
    <div class="max-w-3xl mx-auto space-y-6">
        
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <h1 class="text-xl font-black text-white tracking-tight">Pengaturan Toko & Informasi Struk</h1>
            <p class="text-xs text-slate-400 mt-1">
                Kelola identitas bisnis, alamat toko, kontak resmi, serta catatan kaki pada struk belanja kasir.
            </p>
        </div>

        <form method="POST" action="{{ route('settings.update') }}" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                        Nama Toko / Bengkel *
                    </label>
                    <input type="text" name="store_name" value="{{ $settings['store_name']->value ?? 'Sinar Roda Mas' }}" required
                           class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs font-bold focus:ring-1 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                        Slogan / Tagline Toko
                    </label>
                    <input type="text" name="store_tagline" value="{{ $settings['store_tagline']->value ?? 'Pusat Penjualan Ban & Servis Otomotif' }}"
                           class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                        Alamat Lengkap Toko
                    </label>
                    <textarea name="store_address" rows="2"
                              class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">{{ $settings['store_address']->value ?? 'Jl. Raya Otomotif No. 88, Jakarta Barat' }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                        Nomor Telepon & WhatsApp
                    </label>
                    <input type="text" name="store_phone" value="{{ $settings['store_phone']->value ?? '021-5558989 / 0812-9988-7766' }}"
                           class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                </div>

                <div class="pt-4 border-t border-slate-800">
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                        Catatan Kaki Struk Kasir (Receipt Footer)
                    </label>
                    <textarea name="receipt_footer" rows="3"
                              class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">{{ $settings['receipt_footer']->value ?? "Terima kasih atas kunjungan Anda!\nBan bergaransi pabrik resmi." }}</textarea>
                    <span class="text-[10px] text-slate-400">Teks ini akan tercetak di bagian paling bawah struk thermal kasir</span>
                </div>

                <div class="pt-4 border-t border-slate-800">
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                        PIN Otorisasi Void Default
                    </label>
                    <input type="password" name="default_void_pin" value="{{ $settings['default_void_pin']->value ?? '123456' }}" maxlength="10"
                           class="w-48 px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-center tracking-widest text-sm focus:ring-1 focus:ring-amber-500">
                    <span class="text-[10px] text-slate-400 block mt-1">PIN universal untuk pembatalan transaksi kasir (Default: 123456)</span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <button type="submit"
                        class="px-6 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 transition">
                    Simpan Perubahan Pengaturan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
