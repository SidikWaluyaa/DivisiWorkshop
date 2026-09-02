<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 font-sans">
    {{-- Header Banner --}}
    <div class="bg-gradient-to-r from-[#1a3b34] via-[#22AF85] to-[#1a3b34] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-emerald-400/20">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-white/10 rounded-full border border-white/20 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-[#FFC232] shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-[#FFC232] animate-pulse"></span>
                    <span>LIVE USER DIRECTORY &amp; ACCESS CONTROL</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white leading-tight">
                    Master Data <span class="text-[#FFC232]">User &amp; Teknisi</span>
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100/90 font-medium max-w-xl leading-relaxed">
                    Kelola akun pengguna, alokasi peran (Role), status keaktifan real-time, spesialisasi teknis, dan matriks hak akses secara interaktif tanpa reload.
                </p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto justify-start md:justify-end shrink-0">
                <button wire:click="openCreateModal" 
                        class="px-5 py-2.5 bg-[#FFC232] hover:bg-amber-400 text-slate-950 rounded-2xl shadow-lg shadow-amber-500/20 transition-all active:scale-95 flex items-center gap-2 font-black text-xs border border-amber-300 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Tambah User Baru
                </button>
            </div>
        </div>
    </div>

    {{-- KPI Metric Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Total --}}
        <div wire:click="$set('filterStatus', 'ALL')" class="p-5 rounded-2xl bg-white border border-slate-100 shadow-xs hover:border-[#22AF85]/40 transition-all cursor-pointer {{ $filterStatus === 'ALL' ? 'ring-2 ring-[#22AF85]' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Total User</span>
                <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
            </div>
            <div class="mt-2 text-2xl font-black text-slate-900">{{ $counts['total'] }}</div>
            <div class="text-[10px] font-bold text-slate-400 mt-0.5">Semua Akun Terdaftar</div>
        </div>

        {{-- Aktif --}}
        <div wire:click="$set('filterStatus', 'ACTIVE')" class="p-5 rounded-2xl bg-white border border-emerald-100 shadow-xs hover:border-emerald-400 transition-all cursor-pointer {{ $filterStatus === 'ACTIVE' ? 'ring-2 ring-emerald-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-black uppercase tracking-wider text-emerald-600">User Aktif</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            </div>
            <div class="mt-2 text-2xl font-black text-emerald-700">{{ $counts['active'] }}</div>
            <div class="text-[10px] font-bold text-emerald-600/70 mt-0.5">Dapat Login &amp; Bertugas</div>
        </div>

        {{-- Nonaktif --}}
        <div wire:click="$set('filterStatus', 'INACTIVE')" class="p-5 rounded-2xl bg-white border border-rose-100 shadow-xs hover:border-rose-400 transition-all cursor-pointer {{ $filterStatus === 'INACTIVE' ? 'ring-2 ring-rose-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-black uppercase tracking-wider text-rose-600">Nonaktif</span>
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
            </div>
            <div class="mt-2 text-2xl font-black text-rose-700">{{ $counts['inactive'] }}</div>
            <div class="text-[10px] font-bold text-rose-600/70 mt-0.5">Akses Login Ditangguhkan</div>
        </div>

        {{-- Online --}}
        <div wire:click="$set('filterOnline', 'ONLINE')" class="p-5 rounded-2xl bg-white border border-teal-100 shadow-xs hover:border-teal-400 transition-all cursor-pointer {{ $filterOnline === 'ONLINE' ? 'ring-2 ring-teal-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-black uppercase tracking-wider text-teal-600">Sedang Online</span>
                <span class="w-2.5 h-2.5 rounded-full bg-teal-500 animate-pulse"></span>
            </div>
            <div class="mt-2 text-2xl font-black text-teal-700">{{ $counts['online'] }}</div>
            <div class="text-[10px] font-bold text-teal-600/70 mt-0.5">Aktif 5 Menit Terakhir</div>
        </div>

        {{-- Trashed / Terhapus --}}
        <div wire:click="$set('filterStatus', 'TRASHED')" class="p-5 rounded-2xl bg-white border border-amber-100 shadow-xs hover:border-amber-400 transition-all cursor-pointer col-span-2 lg:col-span-1 {{ $filterStatus === 'TRASHED' ? 'ring-2 ring-amber-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-black uppercase tracking-wider text-amber-600">Arsip / Terhapus</span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            </div>
            <div class="mt-2 text-2xl font-black text-amber-700">{{ $counts['trashed'] }}</div>
            <div class="text-[10px] font-bold text-amber-600/70 mt-0.5">Dapat Dipulihkan</div>
        </div>
    </div>

    {{-- Filter Panel (Instant Livewire) --}}
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-100 space-y-5">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-slate-100">
            <div class="space-y-0.5">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#22AF85]"></span>
                    <h3 class="font-black text-slate-800 text-sm uppercase tracking-wider">Filter &amp; Pencarian Real-Time</h3>
                </div>
                <p class="text-xs font-bold text-slate-400">Pencarian langsung tanpa reload halaman saat mengetik.</p>
            </div>

            @if(count($selected) > 0)
                <div class="flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-2xl shadow-md animate-fadeIn">
                    <span class="text-xs font-black text-[#FFC232]">{{ count($selected) }} Terpilih</span>
                    <button type="button" wire:click="bulkDelete" wire:confirm="Apakah Anda yakin ingin menghapus user terpilih?" class="text-xs font-black text-rose-400 hover:text-rose-300 ml-2 cursor-pointer">
                        🗑️ Hapus Massal
                    </button>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
            {{-- Keyword Search --}}
            <div class="relative">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Cari User / Email / Telepon</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all" 
                           placeholder="Ketik nama, email, role...">
                </div>
            </div>

            {{-- Role Filter --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Filter Peran (Role)</label>
                <select wire:model.live="filterRole" 
                        class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all cursor-pointer">
                    <option value="ALL">-- Semua Peran --</option>
                    <option value="admin">Administrator</option>
                    <option value="owner">Owner / Direktur</option>
                    <option value="spv">Supervisor</option>
                    <option value="technician">Teknisi / Workshop</option>
                    <option value="gudang">Staf Gudang</option>
                    <option value="cs">Customer Service</option>
                    <option value="finance">Finance / Kasir</option>
                    <option value="pic">PIC Material</option>
                    <option value="hr">HR / HRD</option>
                    <option value="user">Staff / User</option>
                </select>
            </div>

            {{-- Status Filter --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Status Akun</label>
                <select wire:model.live="filterStatus" 
                        class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all cursor-pointer">
                    <option value="ALL">-- Semua Status --</option>
                    <option value="ACTIVE">● Aktif</option>
                    <option value="INACTIVE">○ Nonaktif</option>
                    <option value="TRASHED">🗑️ Terhapus / Arsip</option>
                </select>
            </div>

            {{-- Online Filter --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Status Keaktifan</label>
                <select wire:model.live="filterOnline" 
                        class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all cursor-pointer">
                    <option value="ALL">-- Semua Keaktifan --</option>
                    <option value="ONLINE">🟢 Online (Aktif)</option>
                    <option value="OFFLINE">⚪ Offline</option>
                </select>
            </div>

            {{-- Specialization Filter --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Spesialisasi</label>
                <select wire:model.live="filterSpecialization" 
                        class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all cursor-pointer">
                    <option value="ALL">-- Semua Spesialisasi --</option>
                    @foreach ($specializations as $spec)
                        <option value="{{ $spec }}">{{ $spec }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
            <button wire:click="resetFilters" 
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-black transition-all cursor-pointer">
                ↺ Reset Filter
            </button>
        </div>
    </div>

    {{-- Table Container --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        {{-- Desktop View --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white">
                        <th scope="col" class="px-6 py-4 text-left w-12">
                            <input type="checkbox" wire:model.live="selectAll" 
                                   class="rounded border-slate-700 bg-slate-800 text-[#22AF85] shadow-xs focus:ring-[#22AF85]">
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-200">Pengguna (User)</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-200">Role &amp; Spesialisasi</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-200">Keaktifan</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-200">Status Akun</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-200">Kontak</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-200">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse ($users as $user)
                    <tr class="hover:bg-emerald-50/40 transition-colors group {{ $user->trashed() ? 'bg-amber-50/30 opacity-80' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" value="{{ $user->id }}" wire:model.live="selected" 
                                   class="rounded border-slate-300 text-[#22AF85] shadow-xs focus:ring-[#22AF85]">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-2xl bg-gradient-to-br from-[#22AF85] to-[#1a3b34] text-white flex items-center justify-center font-black text-xs shadow-md shrink-0">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <div class="ml-3.5">
                                    <div class="text-xs font-black text-slate-900 group-hover:text-[#22AF85] transition-colors flex items-center gap-1.5">
                                        {{ $user->name }}
                                        @if($user->trashed())
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-rose-100 text-rose-700 border border-rose-200">TERHAPUS</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] font-medium text-slate-400">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $roleColors = [
                                    'admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'owner' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                    'technician' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'gudang' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'pic' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                    'cs' => 'bg-pink-100 text-pink-800 border-pink-200',
                                    'finance' => 'bg-teal-100 text-teal-800 border-teal-200',
                                    'spv' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'hr' => 'bg-green-100 text-green-800 border-green-200',
                                    'user' => 'bg-slate-100 text-slate-800 border-slate-200',
                                ];
                                
                                $roleNames = [
                                    'admin' => 'Administrator',
                                    'owner' => 'Owner / Direktur',
                                    'technician' => 'Teknisi / Workshop',
                                    'gudang' => 'Staf Gudang',
                                    'pic' => 'PIC Material',
                                    'cs' => 'Customer Service',
                                    'finance' => 'Finance / Kasir',
                                    'spv' => 'Supervisor',
                                    'hr' => 'HR / HRD',
                                    'user' => 'Staff / User',
                                ];
                                $color = $roleColors[$user->role] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                            @endphp
                            <div class="flex flex-col items-start gap-1">
                                <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full border {{ $color }}">
                                    {{ $roleNames[$user->role] ?? ucfirst($user->role) }}
                                </span>
                                @if($user->specialization)
                                    <span class="text-[10px] font-bold text-slate-500">
                                        ✨ {{ $user->specialization }}
                                    </span>
                                @endif
                                @if($user->station)
                                    <span class="text-[9px] font-extrabold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                                        📍 Stasiun: {{ $user->station }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->last_active_at)
                                @if($user->last_active_at->diffInMinutes(now()) < 5)
                                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-black">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                                        Online
                                    </span>
                                @else
                                    <span class="text-xs text-slate-500 font-bold" title="{{ $user->last_active_at->translatedFormat('d M Y H:i:s') }}">
                                        {{ $user->last_active_at->diffForHumans() }}
                                    </span>
                                @endif
                            @else
                                <span class="text-xs text-slate-400 italic font-bold">Belum pernah aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(!$user->trashed())
                                <button type="button" 
                                        wire:click="toggleStatus({{ $user->id }})" 
                                        class="px-3 py-1 inline-flex items-center gap-1.5 text-[10px] font-black rounded-full border transition-all cursor-pointer {{ $user->is_active ? 'bg-emerald-100 text-emerald-800 border-emerald-200 hover:bg-emerald-200' : 'bg-rose-100 text-rose-800 border-rose-200 hover:bg-rose-200' }}">
                                    <span>{{ $user->is_active ? '● Aktif' : '○ Nonaktif' }}</span>
                                    <span class="text-[8px] opacity-60">(Klik ubah)</span>
                                </button>
                            @else
                                <span class="px-3 py-1 inline-flex text-[10px] font-black rounded-full bg-slate-100 text-slate-500 border border-slate-200">
                                    🗑️ Di Arsip
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-600">
                            {{ $user->phone ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-black">
                            @if(!$user->trashed())
                                <button type="button" 
                                        wire:click="openEditModal({{ $user->id }})" 
                                        class="text-[#22AF85] hover:text-emerald-700 mr-2 transition-all p-1.5 hover:bg-emerald-50 rounded-xl inline-flex items-center gap-1 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit Akses
                                </button>
                                <button type="button" 
                                        wire:click="deleteUser({{ $user->id }})" 
                                        wire:confirm="Apakah Anda yakin ingin menghapus akun user '{{ $user->name }}'?" 
                                        class="text-rose-500 hover:text-rose-700 transition-all p-1.5 hover:bg-rose-50 rounded-xl inline-flex items-center gap-1 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Hapus
                                </button>
                            @else
                                <button type="button" 
                                        wire:click="restoreUser({{ $user->id }})" 
                                        class="text-emerald-600 hover:text-emerald-800 mr-2 transition-all p-1.5 hover:bg-emerald-50 rounded-xl inline-flex items-center gap-1 cursor-pointer">
                                    ♻️ Pulihkan
                                </button>
                                <button type="button" 
                                        wire:click="forceDeleteUser({{ $user->id }})" 
                                        wire:confirm="PERINGATAN: Akun '{{ $user->name }}' akan dihapus permanen dan tidak bisa dikembalikan. Lanjutkan?" 
                                        class="text-red-700 hover:text-red-900 transition-all p-1.5 hover:bg-red-50 rounded-xl inline-flex items-center gap-1 cursor-pointer">
                                    💥 Hapus Permanen
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic text-xs font-bold">
                            User tidak ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Livewire Modal for Create & Edit User --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6 bg-slate-950/70 backdrop-blur-xs animate-fadeIn">
        <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100 font-sans max-w-4xl w-full">
            {{-- Modal Header --}}
            <div class="p-6 bg-gradient-to-r from-[#1a3b34] via-[#22AF85] to-[#1a3b34] text-white flex justify-between items-center relative overflow-hidden border-b border-emerald-400/20">
                <div class="relative z-10 flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-md flex items-center justify-center text-[#FFC232] shadow-sm shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-white/10 rounded-full text-[9px] font-black uppercase tracking-widest text-[#FFC232]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232] animate-pulse"></span>
                            {{ $isEditMode ? 'EDIT USER &amp; ACCESS MATRIX' : 'USER CREATION ENGINE' }}
                        </div>
                        <h2 class="text-xl font-black text-white tracking-tight leading-tight">
                            {{ $isEditMode ? 'Edit Profil & Hak Akses: ' . $name : 'Tambah Akun User Baru' }}
                        </h2>
                    </div>
                </div>

                <button type="button" wire:click="closeModal" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                    ✕
                </button>
            </div>

            {{-- Tabs --}}
            <div class="flex border-b border-slate-100 bg-slate-50/80 px-6 pt-3 gap-2">
                <button type="button" wire:click="$set('modalTab', 'personal')" 
                        class="px-4 py-2.5 rounded-t-xl text-xs font-black transition-all cursor-pointer {{ $modalTab === 'personal' ? 'bg-white text-[#22AF85] border-t-2 border-[#22AF85] shadow-xs' : 'text-slate-400 hover:text-slate-600' }}">
                    👤 1. Informasi Profil
                </button>
                <button type="button" wire:click="$set('modalTab', 'access')" 
                        class="px-4 py-2.5 rounded-t-xl text-xs font-black transition-all cursor-pointer {{ $modalTab === 'access' ? 'bg-white text-[#22AF85] border-t-2 border-[#22AF85] shadow-xs' : 'text-slate-400 hover:text-slate-600' }}">
                    🔐 2. Hak Akses Modul (Matrix)
                </button>
            </div>

            {{-- Tab 1: Personal Info --}}
            @if($modalTab === 'personal')
            <div class="p-6 sm:p-7 space-y-5 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20" placeholder="Nama lengkap...">
                        @error('name') <span class="text-[11px] font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" wire:model="email" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20" placeholder="email@workshop.com">
                        @error('email') <span class="text-[11px] font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1.5">Nomor WhatsApp / HP</label>
                        <input type="text" wire:model="phone" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20" placeholder="08xxxxxxxxxx">
                        @error('phone') <span class="text-[11px] font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1.5">Peran Utama (Role) <span class="text-rose-500">*</span></label>
                        <select wire:model.live="role" class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 cursor-pointer">
                            <option value="user">Staff / User</option>
                            <option value="technician">Teknisi / Workshop</option>
                            <option value="gudang">Staf Gudang</option>
                            <option value="cs">Customer Service</option>
                            <option value="finance">Finance / Kasir</option>
                            <option value="pic">PIC Material</option>
                            <option value="spv">Supervisor</option>
                            <option value="hr">HR / HRD</option>
                            <option value="admin">Administrator</option>
                            <option value="owner">Owner / Direktur</option>
                        </select>
                        @error('role') <span class="text-[11px] font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- CS Code (If CS) --}}
                    @if($role === 'cs')
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1.5">Kode CS (2 Huruf Unik SPK)</label>
                        <input type="text" wire:model="cs_code" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 uppercase" placeholder="Contoh: FI, OL, RQ">
                    </div>
                    @endif

                    {{-- Station / Specialization (If Technician / PIC) --}}
                    @if(in_array($role, ['technician', 'pic']))
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1.5">Spesialisasi Pengerjaan</label>
                        <select wire:model="specialization" class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 cursor-pointer">
                            <option value="">-- Pilih Spesialisasi --</option>
                            <option value="Washing">Washing (Cuci Sepatu)</option>
                            <option value="Bongkar Sol">Bongkar Sol</option>
                            <option value="Bongkar Upper">Bongkar Upper</option>
                            <option value="Reparasi Sol">Reparasi Sol (Soling)</option>
                            <option value="Reparasi Upper">Reparasi Upper / Jahit</option>
                            <option value="Repaint">Repaint / Treatment</option>
                            <option value="QC Jahit">QC Jahit</option>
                            <option value="QC Cleanup">QC Cleanup</option>
                            <option value="QC Final">QC Final</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1.5">Lokasi Pool Workshop</label>
                        <select wire:model="workshop_pool" class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 cursor-pointer">
                            <option value="">-- Pilih Pool --</option>
                            <option value="Workshop Hijau (Prep, Sortir & QC)">Workshop Hijau (Prep, Sortir &amp; QC)</option>
                            <option value="Workshop Abu (Production)">Workshop Abu (Production)</option>
                        </select>
                    </div>
                    @endif

                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1.5">
                            {{ $isEditMode ? 'Password Baru (Kosongkan jika tidak diubah)' : 'Kata Sandi (Password) *' }}
                        </label>
                        <input type="password" wire:model="password" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50" placeholder="Minimal 6 karakter...">
                        @error('password') <span class="text-[11px] font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1.5">Konfirmasi Password</label>
                        <input type="password" wire:model="password_confirmation" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50" placeholder="Ketik ulang password...">
                    </div>
                </div>

                {{-- Status Active Toggle --}}
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-black text-slate-800 block">Status Keaktifan Akun</span>
                        <span class="text-[11px] font-medium text-slate-400">Jika dinonaktifkan, user tidak dapat login ke sistem.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#22AF85]"></div>
                    </label>
                </div>
            </div>
            @endif

            {{-- Tab 2: Access Rights Matrix --}}
            @if($modalTab === 'access')
            <div class="p-6 sm:p-7 space-y-6 max-h-[70vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Matriks Izin Akses Modul</h4>
                        <p class="text-[11px] font-medium text-slate-400">Pilih modul apa saja yang boleh diakses oleh pengguna ini.</p>
                    </div>
                    <button type="button" wire:click="applyPresetForRole('{{ $role }}')" class="px-3 py-1.5 bg-emerald-50 text-[#22AF85] hover:bg-emerald-100 rounded-xl text-[10px] font-black transition-all cursor-pointer">
                        ⚡ Terapkan Preset {{ ucfirst($role) }}
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($allDivisions as $div)
                    <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                            <span class="text-xs font-black text-slate-800">{{ $div['title'] }}</span>
                        </div>
                        <div class="space-y-2">
                            @foreach($div['modules'] as $moduleKey => $moduleName)
                            <label class="flex items-center gap-2.5 cursor-pointer text-xs font-bold text-slate-700 hover:text-[#22AF85]">
                                <input type="checkbox" value="{{ $moduleKey }}" wire:model="access_rights" class="rounded border-slate-300 text-[#22AF85] focus:ring-[#22AF85]">
                                <span>{{ $moduleName }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Modal Footer --}}
            <div class="p-5 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" wire:click="closeModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-black transition-all cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="saveUser" class="px-6 py-2 bg-[#22AF85] hover:bg-emerald-600 text-white rounded-xl text-xs font-black shadow-md shadow-emerald-950/10 transition-all active:scale-95 cursor-pointer">
                    💾 Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
