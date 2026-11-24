<x-app-layout title="Data Supplier Ban">
    <div x-data="supplierApp()" class="space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Data Supplier & Distributor Ban</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Kelola kontak distributor pabrikan resmi (Bridgestone, Michelin, GT Radial, dll).
                </p>
            </div>

            <button type="button" @click="openCreateModal()"
                    class="px-5 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-xs text-xs uppercase tracking-wider shadow-lg shadow-blue-500/10 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Supplier</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($suppliers as $supplier)
                <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs hover:border-slate-200 transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-bold text-slate-900">{{ $supplier->name }}</h3>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $supplier->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-500' }}">
                                {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <div class="space-y-1.5 text-xs text-slate-700 mt-3">
                            <div class="flex items-center gap-2">
                                <span class="text-slate-500 w-24">Kontak Person:</span>
                                <span class="font-semibold text-slate-900">{{ $supplier->contact_person ?: '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-500 w-24">No Telepon:</span>
                                <span class="font-mono text-amber-300">{{ $supplier->phone ?: '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-500 w-24">Email:</span>
                                <span class="text-slate-500">{{ $supplier->email ?: '-' }}</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-slate-500 w-24">Alamat:</span>
                                <span class="text-slate-500 flex-1">{{ $supplier->address ?: '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-200">
                                <span class="text-slate-500 w-24">Riwayat Inbound:</span>
                                <span class="font-bold text-blue-400">{{ $supplier->inbounds_count }} pengiriman pasokan</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 mt-4 border-t border-slate-200">
                        <button type="button"
                                @click="openEditModal({{ $supplier->id }}, '{{ $supplier->name }}', '{{ $supplier->contact_person }}', '{{ $supplier->phone }}', '{{ $supplier->email }}', '{{ $supplier->address }}', {{ $supplier->is_active ? 1 : 0 }})"
                                class="text-xs text-blue-600 hover:underline font-semibold">
                            Edit
                        </button>
                        <form method="POST" action="{{ route('suppliers.destroy', $supplier->id) }}" onsubmit="return confirm('Hapus/nonaktifkan supplier {{ $supplier->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-rose-400 hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-4">
            {{ $suppliers->links() }}
        </div>

        <!-- Supplier Modal -->
        <div x-show="modalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/50 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-3xl shadow-2xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5"
                 @click.away="modalOpen = false">
                
                <h3 class="text-base font-bold text-slate-900" x-text="isEdit ? 'Edit Data Supplier' : 'Tambah Supplier Baru'"></h3>

                <form :action="isEdit ? '/suppliers/' + form.id : '{{ route('suppliers.store') }}'" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan Supplier *</label>
                        <input type="text" name="name" x-model="form.name" required placeholder="PT Bridgestone Tire Indonesia..."
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kontak Person</label>
                            <input type="text" name="contact_person" x-model="form.contact_person" placeholder="Bpk. Hendra..."
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon</label>
                            <input type="text" name="phone" x-model="form.phone" placeholder="021-..."
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" x-model="form.email" placeholder="sales@distributor.com"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Kantor / Gudang</label>
                        <textarea name="address" x-model="form.address" rows="2" placeholder="Jl. Raya Kawasan Industri..."
                                  class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <template x-if="isEdit">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status Keaktifan</label>
                            <select name="is_active" x-model="form.is_active" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </template>

                    <div class="flex items-center gap-3 pt-3">
                        <button type="submit"
                                class="flex-1 py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-xs text-xs uppercase tracking-wider transition">
                            Simpan Supplier
                        </button>
                        <button type="button" @click="modalOpen = false"
                                class="px-4 py-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function supplierApp() {
            return {
                modalOpen: false,
                isEdit: false,
                form: {
                    id: null,
                    name: '',
                    contact_person: '',
                    phone: '',
                    email: '',
                    address: '',
                    is_active: 1
                },

                openCreateModal() {
                    this.isEdit = false;
                    this.form = { id: null, name: '', contact_person: '', phone: '', email: '', address: '', is_active: 1 };
                    this.modalOpen = true;
                },

                openEditModal(id, name, cp, phone, email, addr, active) {
                    this.isEdit = true;
                    this.form = { id: id, name: name, contact_person: cp, phone: phone, email: email, address: addr, is_active: active };
                    this.modalOpen = true;
                }
            };
        }
    </script>
</x-app-layout>
