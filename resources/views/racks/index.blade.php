<x-app-layout title="Manajemen Lokasi Rak Gudang">
    <div x-data="rackApp()" class="space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <div>
                <h1 class="text-xl font-black text-white tracking-tight">Manajemen Lokasi Rak Gudang</h1>
                <p class="text-xs text-slate-400 mt-1">
                    Atur tata letak penyimpanan fisik ban di gudang utama dan area buffer (Relasi One-to-Many Rak & Ban).
                </p>
            </div>

            <button type="button" @click="openCreateModal()"
                    class="px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Rak Baru</span>
            </button>
        </div>

        <!-- Racks Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($rackLocations as $rack)
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl hover:border-slate-700 transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-mono text-base font-black text-amber-400 px-3 py-1 rounded-xl bg-slate-950 border border-slate-800">
                                {{ $rack->code }}
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                {{ $rack->zone }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-300 font-medium mb-3">
                            {{ $rack->description ?: 'Lokasi rak penyimpanan ban.' }}
                        </p>

                        <!-- Capacity & Occupancy -->
                        <div class="space-y-1 mb-4">
                            @php
                                $totalTireUnits = $rack->tires->sum('stock');
                                $pct = min(100, round(($totalTireUnits / max(1, $rack->capacity)) * 100));
                            @endphp
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Kapasitas Rak:</span>
                                <span class="font-bold text-white">{{ $totalTireUnits }} / {{ $rack->capacity }} unit ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                                <div style="width: {{ $pct }}%;"
                                     class="h-full rounded-full transition-all duration-300 {{ $pct >= 90 ? 'bg-rose-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-emerald-500') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Tires on this Rack -->
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
                                Ban di Rak Ini ({{ $rack->tires->count() }} SKU):
                            </span>
                            <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                                @forelse($rack->tires as $t)
                                    <div class="p-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] flex justify-between items-center">
                                        <div class="min-w-0">
                                            <div class="font-bold text-white truncate">{{ $t->brand }} {{ $t->pattern }}</div>
                                            <span class="text-[10px] font-mono text-slate-400">{{ $t->size_display }}</span>
                                        </div>
                                        <span class="font-bold text-emerald-400 flex-shrink-0 text-xs">{{ $t->stock }} unit</span>
                                    </div>
                                @empty
                                    <div class="text-[11px] text-slate-500 py-2 text-center">Belum ada ban di rak ini.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 mt-4 border-t border-slate-800">
                        <button type="button"
                                @click="openEditModal({{ $rack->id }}, '{{ $rack->code }}', '{{ $rack->zone }}', '{{ $rack->description }}', {{ $rack->capacity }})"
                                class="text-xs text-amber-400 hover:underline font-semibold">
                            Edit Rak
                        </button>

                        @if($rack->tires->count() === 0)
                            <form method="POST" action="{{ route('racks.destroy', $rack->id) }}" onsubmit="return confirm('Hapus rak {{ $rack->code }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-400 hover:underline">Hapus</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Create / Edit Modal -->
        <div x-show="modalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5"
                 @click.away="modalOpen = false">
                
                <h3 class="text-base font-black text-white" x-text="isEdit ? 'Edit Lokasi Rak' : 'Tambah Lokasi Rak Baru'"></h3>

                <form :action="isEdit ? '/racks/' + form.id : '{{ route('racks.store') }}'" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kode Rak *</label>
                        <input type="text" name="code" x-model="form.code" required placeholder="Contoh: RAK-A1"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono uppercase text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Zona Gudang *</label>
                        <input type="text" name="zone" x-model="form.zone" required placeholder="Contoh: Zona Depan / Zona Truk"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kapasitas Maksimal (Unit Ban) *</label>
                        <input type="number" name="capacity" x-model.number="form.capacity" required min="1"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Keterangan / Deskripsi</label>
                        <input type="text" name="description" x-model="form.description" placeholder="Contoh: Rak khusus ban sedan ring 15"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div class="flex items-center gap-3 pt-3">
                        <button type="submit"
                                class="flex-1 py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition">
                            Simpan Lokasi Rak
                        </button>
                        <button type="button" @click="modalOpen = false"
                                class="px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function rackApp() {
            return {
                modalOpen: false,
                isEdit: false,
                form: {
                    id: null,
                    code: '',
                    zone: '',
                    capacity: 50,
                    description: ''
                },

                openCreateModal() {
                    this.isEdit = false;
                    this.form = { id: null, code: '', zone: '', capacity: 50, description: '' };
                    this.modalOpen = true;
                },

                openEditModal(id, code, zone, desc, capacity) {
                    this.isEdit = true;
                    this.form = { id: id, code: code, zone: zone, capacity: capacity, description: desc };
                    this.modalOpen = true;
                }
            };
        }
    </script>
</x-app-layout>
