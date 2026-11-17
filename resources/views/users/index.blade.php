<x-app-layout title="Manajemen Pengguna (Admin)">
    <div x-data="userApp()" class="space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <div>
                <h1 class="text-xl font-black text-white tracking-tight">Manajemen Pengguna & Hak Akses (RBAC)</h1>
                <p class="text-xs text-slate-400 mt-1">
                    Buat akun staf baru, atur peran wewenang (Admin, Owner, Gudang, Kasir), blokir akses, dan reset kata sandi.
                </p>
            </div>

            <button type="button" @click="openCreateModal()"
                    class="px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>Tambah Pengguna Baru</span>
            </button>
        </div>

        <!-- Users Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/80 border-b border-slate-800 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                        <tr>
                            <th class="py-4 px-6">Nama Pengguna</th>
                            <th class="py-4 px-6">Email Login</th>
                            <th class="py-4 px-6">Peran (Role)</th>
                            <th class="py-4 px-6">No Telepon</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-center">PIN Void</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-800/40 transition {{ !$user->is_active ? 'opacity-50' : '' }}">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-white text-xs">{{ $user->name }}</div>
                                    @if($user->id === auth()->id())
                                        <span class="text-[9px] text-amber-400 font-bold">(Akun Anda Sendiri)</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-mono text-slate-300">
                                    {{ $user->email }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                        {{ $user->role === 'admin' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : '' }}
                                        {{ $user->role === 'owner' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/40' : '' }}
                                        {{ $user->role === 'gudang' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/40' : '' }}
                                        {{ $user->role === 'kasir' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : '' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-400 font-mono">
                                    {{ $user->phone ?: '-' }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($user->is_active)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                            Diblokir
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center font-mono text-slate-400">
                                    {{ $user->void_pin ? '******' : '-' }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Edit -->
                                        <button type="button"
                                                @click="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->phone }}', '{{ $user->void_pin }}')"
                                                class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition"
                                                title="Edit Pengguna">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <!-- Reset Password -->
                                        <button type="button"
                                                @click="openResetModal({{ $user->id }}, '{{ $user->name }}')"
                                                class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-amber-400 transition"
                                                title="Reset Kata Sandi">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                        </button>

                                        <!-- Toggle Active Status -->
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('users.toggle', $user->id) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition"
                                                        title="{{ $user->is_active ? 'Blokir Pengguna' : 'Aktifkan Pengguna' }}">
                                                    @if($user->is_active)
                                                        <svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    @endif
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-800">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Create / Edit User Modal -->
        <div x-show="userModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5"
                 @click.away="userModalOpen = false">
                
                <h3 class="text-base font-black text-white" x-text="isEdit ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru'"></h3>

                <form :action="isEdit ? '/users/' + form.id : '{{ route('users.store') }}'" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" x-model="form.name" required
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat Email *</label>
                        <input type="email" name="email" x-model="form.email" required
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <template x-if="!isEdit">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Kata Sandi Awal *</label>
                            <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                        </div>
                    </template>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Peran Akses (Role) *</label>
                            <select name="role" x-model="form.role" required
                                    class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                                <option value="admin">Admin IT</option>
                                <option value="owner">Owner</option>
                                <option value="gudang">Staf Gudang</option>
                                <option value="kasir">Kasir POS</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">PIN Void Transaksi</label>
                            <input type="text" name="void_pin" x-model="form.void_pin" maxlength="10" placeholder="123456"
                                   class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-xs focus:ring-1 focus:ring-amber-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Telepon / WA</label>
                        <input type="text" name="phone" x-model="form.phone" placeholder="0812..."
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div class="flex items-center gap-3 pt-3">
                        <button type="submit"
                                class="flex-1 py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition">
                            Simpan Pengguna
                        </button>
                        <button type="button" @click="userModalOpen = false"
                                class="px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reset Password Modal -->
        <div x-show="resetModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 sm:p-8 max-w-sm w-full shadow-2xl space-y-4"
                 @click.away="resetModalOpen = false">
                
                <h3 class="text-base font-black text-white">Reset Kata Sandi</h3>
                <p class="text-xs text-slate-400" x-text="'Untuk pengguna: ' + targetUserName"></p>

                <form :action="'/users/' + targetUserId + '/reset-password'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kata Sandi Baru *</label>
                        <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-amber-500">
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                                class="flex-1 py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition">
                            Perbarui Sandi
                        </button>
                        <button type="button" @click="resetModalOpen = false"
                                class="px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function userApp() {
            return {
                userModalOpen: false,
                resetModalOpen: false,
                isEdit: false,
                targetUserId: null,
                targetUserName: '',
                form: {
                    id: null,
                    name: '',
                    email: '',
                    role: 'kasir',
                    phone: '',
                    void_pin: '123456'
                },

                openCreateModal() {
                    this.isEdit = false;
                    this.form = { id: null, name: '', email: '', role: 'kasir', phone: '', void_pin: '123456' };
                    this.userModalOpen = true;
                },

                openEditModal(id, name, email, role, phone, pin) {
                    this.isEdit = true;
                    this.form = { id: id, name: name, email: email, role: role, phone: phone, void_pin: pin };
                    this.userModalOpen = true;
                },

                openResetModal(id, name) {
                    this.targetUserId = id;
                    this.targetUserName = name;
                    this.resetModalOpen = true;
                }
            };
        }
    </script>
</x-app-layout>
