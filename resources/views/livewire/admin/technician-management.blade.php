<div class="min-h-screen bg-slate-50 text-slate-800 p-4 md:p-6 space-y-6">
    {{-- Header Banner & Stats Container --}}
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="p-4 rounded-2xl bg-emerald-50 border border-[#22AF85]/40 text-emerald-900 flex items-center justify-between shadow-md backdrop-blur-md">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-[#22AF85] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-700 hover:text-emerald-950 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        {{-- Action Bar & Page Title (Clean Light Card) --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-[#FFC232] text-slate-950 flex items-center justify-center font-black shadow-md shadow-amber-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-black tracking-tight text-slate-900">Manajemen Data Teknisi</h1>
                        <p class="text-xs text-slate-500 font-medium">Kelola profil, role, stasiun utama, spesialisasi keahlian, dan pool workshop teknisi.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button wire:click="openCreateModal" 
                        class="px-5 py-2.5 rounded-2xl bg-[#FFC232] hover:bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider flex items-center gap-2 shadow-md shadow-amber-500/20 transition-all transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Tambah Teknisi Baru</span>
                </button>
            </div>
        </div>

        {{-- Key Metrics Cards (Bright White Cards with Accent Border-Left for ALL STATIONS) --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            <div class="bg-white border border-slate-200/80 border-l-4 border-l-[#22AF85] p-3.5 rounded-2xl shadow-sm">
                <p class="text-[9px] font-black uppercase text-slate-500 tracking-wider">Total Teknisi</p>
                <p class="text-xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white border border-slate-200/80 border-l-4 border-l-emerald-500 p-3.5 rounded-2xl shadow-sm">
                <p class="text-[9px] font-black uppercase text-[#22AF85] tracking-wider">Akun Aktif</p>
                <p class="text-xl font-black text-[#22AF85] mt-1">{{ $stats['active'] }}</p>
            </div>
            <div class="bg-white border border-slate-200/80 border-l-4 border-l-teal-500 p-3.5 rounded-2xl shadow-sm">
                <p class="text-[9px] font-black uppercase text-teal-700 tracking-wider">Stasiun Cuci</p>
                <p class="text-xl font-black text-teal-800 mt-1">{{ $stats['cuci'] }}</p>
            </div>
            <div class="bg-white border border-slate-200/80 border-l-4 border-l-[#FFC232] p-3.5 rounded-2xl shadow-sm">
                <p class="text-[9px] font-black uppercase text-amber-700 tracking-wider">Stasiun Soling</p>
                <p class="text-xl font-black text-amber-800 mt-1">{{ $stats['soling'] }}</p>
            </div>
            <div class="bg-white border border-slate-200/80 border-l-4 border-l-sky-500 p-3.5 rounded-2xl shadow-sm">
                <p class="text-[9px] font-black uppercase text-sky-700 tracking-wider">Stasiun Upper</p>
                <p class="text-xl font-black text-sky-800 mt-1">{{ $stats['upper'] }}</p>
            </div>
            <div class="bg-white border border-slate-200/80 border-l-4 border-l-purple-500 p-3.5 rounded-2xl shadow-sm">
                <p class="text-[9px] font-black uppercase text-purple-700 tracking-wider">Stasiun Treatment</p>
                <p class="text-xl font-black text-purple-800 mt-1">{{ $stats['treatment'] }}</p>
            </div>
            <div class="bg-white border border-slate-200/80 border-l-4 border-l-emerald-600 p-3.5 rounded-2xl shadow-sm">
                <p class="text-[9px] font-black uppercase text-emerald-700 tracking-wider">Stasiun QC</p>
                <p class="text-xl font-black text-emerald-800 mt-1">{{ $stats['qc'] }}</p>
            </div>
            <div class="bg-white border border-slate-200/80 border-l-4 border-l-slate-400 p-3.5 rounded-2xl shadow-sm">
                <p class="text-[9px] font-black uppercase text-slate-500 tracking-wider">Akun Nonaktif</p>
                <p class="text-xl font-black text-slate-600 mt-1">{{ $stats['inactive'] }}</p>
            </div>
        </div>

        {{-- Filter & Search Toolbar --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center gap-3 justify-between">
            <div class="relative w-full md:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Cari nama, email, spesialisasi, pool..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end">
                {{-- Station Filter --}}
                <select wire:model.live="filterStation" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 cursor-pointer">
                    <option value="ALL">Semua Stasiun</option>
                    <option value="PREPARATION">PREPARATION (Cuci)</option>
                    <option value="SORTIR">SORTIR & Penilaian</option>
                    <option value="SOLING">SOLING (Sol Bawah)</option>
                    <option value="UPPER">UPPER (Jahit Atas)</option>
                    <option value="TREATMENT">TREATMENT & Painting</option>
                    <option value="QC">QC & Finishing</option>
                </select>

                {{-- Pool Filter --}}
                <select wire:model.live="filterPool" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 cursor-pointer">
                    <option value="ALL">Semua Pool Workshop</option>
                    <option value="HIJAU">🟢 Workshop Hijau (Prep, Sortir & QC)</option>
                    <option value="ABU">⚪ Workshop Abu (Production)</option>
                </select>

                {{-- Status Filter --}}
                <select wire:model.live="filterStatus" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 cursor-pointer">
                    <option value="ALL">Semua Status</option>
                    <option value="ACTIVE">Aktif (User Active)</option>
                    <option value="INACTIVE">Nonaktif (User Inactive)</option>
                </select>
            </div>
        </div>

        {{-- Technicians Grid Cards (Clean White Cards) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($technicians as $tech)
                @php
                    $isProductionStation = in_array($tech->station, ['SOLING', 'UPPER', 'TREATMENT']);
                    $poolDisplayName = $isProductionStation 
                        ? '⚪ Workshop Abu (Production)' 
                        : '🟢 Workshop Hijau (Prep/Sortir/QC)';
                @endphp
                <div class="bg-white border border-slate-200/80 hover:border-[#22AF85]/50 rounded-3xl p-5 space-y-4 transition-all duration-200 hover:-translate-y-1 shadow-sm hover:shadow-md flex flex-col justify-between relative group">
                    <div>
                        {{-- Top Badge Row --}}
                        <div class="flex items-center justify-between gap-2">
                            @php
                                $stationBadgeClass = match($tech->station) {
                                    'SOLING' => 'bg-amber-50 text-amber-800 border border-amber-200',
                                    'UPPER' => 'bg-sky-50 text-sky-800 border border-sky-200',
                                    'PREPARATION' => 'bg-teal-50 text-teal-800 border border-teal-200',
                                    'QC' => 'bg-emerald-50 text-[#22AF85] border border-emerald-200',
                                    default => 'bg-slate-100 text-slate-700 border border-slate-200',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $stationBadgeClass }}">
                                📍 {{ $tech->station ?? 'SOLING' }}
                            </span>

                            <div class="flex items-center gap-1.5">
                                @if(!$tech->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[9px] border border-slate-200">NONAKTIF</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#22AF85] font-bold text-[9px] border border-emerald-200">AKTIF</span>
                                @endif
                            </div>
                        </div>

                        {{-- Tech Identity --}}
                        <div class="flex items-start gap-3.5 mt-4">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#22AF85] to-[#0F5A47] text-white font-black text-lg flex items-center justify-center flex-shrink-0 shadow-md">
                                {{ strtoupper(substr($tech->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-extrabold text-base text-slate-900 truncate">{{ $tech->name }}</h3>
                                <p class="text-xs text-slate-500 truncate">{{ $tech->email }}</p>
                                <p class="text-[11px] text-[#22AF85] font-semibold mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span>{{ $tech->phone ?: 'Tidak ada No HP' }}</span>
                                </p>
                            </div>
                        </div>

                        {{-- Details List --}}
                        <div class="mt-4 pt-3 border-t border-slate-100 space-y-1.5 text-xs text-slate-600">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 font-semibold">Spesialisasi:</span>
                                <span class="font-bold text-amber-800 truncate max-w-[160px]">{{ $tech->specialization ?: 'Reparasi Sol' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 font-semibold">Pool Workshop:</span>
                                <span class="font-bold text-slate-800 truncate max-w-[160px]">{{ $poolDisplayName }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 font-semibold">Role Pengguna:</span>
                                <span class="font-bold text-[#22AF85] uppercase text-[10px]">{{ $tech->role }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Row --}}
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2 mt-4">
                        <button wire:click="openEditModal({{ $tech->id }})" 
                                class="flex-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit Profil & Pool</span>
                        </button>

                        <button wire:click="toggleActiveStatus({{ $tech->id }})" 
                                title="{{ $tech->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                class="p-2 rounded-xl border {{ $tech->is_active ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-[#22AF85] hover:bg-emerald-50' }} transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </button>

                        <a href="{{ route('admin.technician-skills') }}" 
                           title="Atur Skill Matrix Jasa"
                           class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-amber-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-12 rounded-3xl text-center border border-slate-200 shadow-sm space-y-3">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Tidak Ada Data Teknisi</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Tidak ditemukan teknisi dengan kriteria filter atau kata kunci pencarian tersebut.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="pt-4">
            {{ $technicians->links() }}
        </div>
    </div>

    {{-- Centered Dynamic Light Emerald Glassmorphic Modal --}}
    @if($showModal)
        <div x-data="{ selectedRole: @entangle('role') }" class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-10 flex items-center justify-center">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" wire:click="closeModal"></div>

            {{-- Modal Card Container --}}
            <div class="relative w-full max-w-2xl bg-white rounded-3xl overflow-hidden shadow-2xl border border-emerald-500/20 text-slate-800 z-10 my-auto animate-in fade-in zoom-in-95 duration-200">
                
                {{-- Emerald Header Banner --}}
                <div class="bg-gradient-to-r from-[#0F5A47] via-[#14745C] to-[#22AF85] p-5 text-white relative">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-[#FFC232] flex items-center justify-center font-black shadow-inner">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-400/20 border border-emerald-300/30 text-emerald-100 font-bold text-[9px] uppercase tracking-widest inline-block mb-0.5">
                                    ACCESS CONTROL ENGINE
                                </span>
                                <h2 class="text-base font-black text-white tracking-tight">
                                    {{ $isEditMode ? 'Edit Akses User: ' . $name : 'Tambah Akun & Profil Teknisi Baru' }}
                                </h2>
                            </div>
                        </div>
                        <button wire:click="closeModal" class="w-8 h-8 rounded-full bg-black/20 hover:bg-black/40 text-white/80 hover:text-white flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Tab Pill Header --}}
                    <div class="flex items-center gap-2 mt-4 pt-2.5 border-t border-white/10">
                        <span class="px-3 py-1 rounded-xl bg-white text-emerald-900 font-extrabold text-[11px] flex items-center gap-1.5 shadow-sm">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Profil & Keamanan</span>
                        </span>
                        <span class="px-3 py-1 rounded-xl bg-white/10 text-emerald-100 font-bold text-[11px] flex items-center gap-1.5">
                            <span>Hak Akses & Operasional</span>
                            <span class="w-3.5 h-3.5 rounded-full bg-emerald-400/30 text-emerald-200 text-[9px] font-black flex items-center justify-center">✓</span>
                        </span>
                    </div>
                </div>

                {{-- Form Content Body --}}
                <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto bg-slate-50">
                    
                    {{-- 1. SECTION DATA PERSONAL --}}
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                        <h4 class="text-xs font-black text-emerald-700 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#22AF85]"></span>
                            DATA PERSONAL
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap *</label>
                                <input type="text" wire:model="name" placeholder="Nama Lengkap Teknisi" 
                                       class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all">
                                @error('name') <span class="text-rose-500 text-[10px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email / ID Login *</label>
                                <input type="email" wire:model="email" placeholder="nama@workshop.com" 
                                       class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all">
                                @error('email') <span class="text-rose-500 text-[10px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP</label>
                            <input type="text" wire:model="phone" placeholder="628123456789" 
                                   class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all">
                            @error('phone') <span class="text-rose-500 text-[10px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- 2. SECTION OPERASIONAL WORKSHOP (DYNAMIC) --}}
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                        <h4 class="text-xs font-black text-emerald-700 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#22AF85]"></span>
                            PENGATURAN OPERASIONAL & ROLE
                        </h4>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Role Pengguna *</label>
                            <select wire:model="role" x-model="selectedRole" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 cursor-pointer">
                                <option value="technician">Technician (Teknisi Utama)</option>
                                <option value="technician_assistant">Technician Assistant (Asisten)</option>
                                <option value="qc">QC Specialist</option>
                                <option value="admin">Admin Workshop</option>
                            </select>
                            @error('role') <span class="text-rose-500 text-[10px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        {{-- Technical Fields (Shown Dynamically for Technicians & QC) --}}
                        <div x-show="selectedRole === 'technician' || selectedRole === 'technician_assistant' || selectedRole === 'qc'" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="space-y-3 pt-1 border-t border-slate-100">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                {{-- Station --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Stasiun Pengerjaan Utama *</label>
                                    <select wire:model.live="station" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 cursor-pointer">
                                        <option value="PREPARATION">PREPARATION (Persiapan & Cuci)</option>
                                        <option value="SORTIR">SORTIR (Sortir & Penilaian)</option>
                                        <option value="SOLING">SOLING (Reparasi Sol Bawah)</option>
                                        <option value="UPPER">UPPER (Reparasi Jahit Atas)</option>
                                        <option value="TREATMENT">TREATMENT (Painting & Care)</option>
                                        <option value="QC">QC (Quality Control & Finishing)</option>
                                    </select>
                                    @error('station') <span class="text-rose-500 text-[10px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                                </div>

                                {{-- Workshop Pool (Exact 2 Workshop Pools as per user directive) --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Pool Workshop / Unit Pengerjaan *</label>
                                    <select wire:model="workshop_pool" class="w-full px-3.5 py-2 rounded-xl bg-white border border-emerald-500/40 text-xs font-bold text-slate-800 focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 cursor-pointer shadow-sm">
                                        <option value="Workshop Hijau (Prep, Sortir & QC)">🟢 Workshop Hijau (Prep, Sortir & QC)</option>
                                        <option value="Workshop Abu (Production)">⚪ Workshop Abu (Production)</option>
                                    </select>
                                    @error('workshop_pool') <span class="text-rose-500 text-[10px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Grouped Specialization Dropdown --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Spesialisasi Teknis *</label>
                                <select wire:model="specialization" class="w-full px-3.5 py-2 rounded-xl bg-white border border-emerald-500/40 text-xs font-bold text-slate-800 focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 cursor-pointer shadow-sm">
                                    <option value="">-- Pilih Spesialisasi --</option>
                                    <optgroup label="1. Preparation (Persiapan - Workshop Hijau)">
                                        <option value="Washing">Washing (Cuci)</option>
                                        <option value="Bongkar Sol">Bongkar Sol</option>
                                        <option value="Bongkar Upper">Bongkar Upper</option>
                                    </optgroup>
                                    <optgroup label="2. Production (Produksi / Reparasi - Workshop Abu)">
                                        <option value="Reparasi Sol">Reparasi Sol</option>
                                        <option value="Reparasi Upper">Reparasi Upper</option>
                                        <option value="Reparasi Treatment">Reparasi Treatment / Cleaning</option>
                                    </optgroup>
                                    <optgroup label="3. Quality Control (QC - Workshop Hijau)">
                                        <option value="QC Jahit">QC Jahit</option>
                                        <option value="QC Cleanup">QC Cleanup</option>
                                        <option value="QC Final">QC Final</option>
                                    </optgroup>
                                </select>
                                @error('specialization') <span class="text-rose-500 text-[10px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Toggle Status Aktif --}}
                        <div class="pt-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-12 h-7 bg-slate-200 rounded-full peer-checked:bg-[#22AF85] transition-colors duration-300 shadow-inner relative">
                                    <div class="absolute left-1 top-1 bg-white w-5 h-5 rounded-full transition-transform duration-300 peer-checked:translate-x-5 shadow-md flex items-center justify-center">
                                        @if($is_active)
                                            <svg class="w-3 h-3 text-[#22AF85]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        @else
                                            <svg class="w-3 h-3 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        @endif
                                    </div>
                                </div>
                                <span class="ml-3 text-xs font-black {{ $is_active ? 'text-[#22AF85]' : 'text-rose-600' }}">
                                    {{ $is_active ? '● Status Akun: Aktif' : '○ Status Akun: Nonaktif' }}
                                </span>
                            </label>
                            <p class="text-[11px] mt-0.5 font-medium {{ $is_active ? 'text-slate-500' : 'text-rose-500' }}">
                                {{ $is_active ? 'Pengguna teknisi ini dapat login dan mengakses PWA Workshop.' : 'Pengguna akan langsung ter-logout dan tidak bisa login.' }}
                            </p>
                        </div>
                    </div>

                    {{-- 3. SECTION GANTI PASSWORD (OPSIONAL) --}}
                    <div class="bg-amber-50/60 p-4 rounded-2xl border border-amber-200/80 space-y-2.5">
                        <h4 class="text-xs font-black text-amber-800 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            GANTI PASSWORD (OPSIONAL)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Password Baru</label>
                                <input type="password" wire:model="password" placeholder="Kosongkan jika tidak diganti" 
                                       class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all">
                                @error('password') <span class="text-rose-500 text-[10px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Password</label>
                                <input type="password" wire:model="password_confirmation" placeholder="Ketik ulang password baru" 
                                       class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all">
                                @error('password_confirmation') <span class="text-rose-500 text-[10px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="p-4 bg-slate-100 border-t border-slate-200 flex items-center justify-end gap-3">
                    <button wire:click="closeModal" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button wire:click="saveTechnician" class="px-5 py-2 rounded-xl bg-[#FFC232] hover:bg-amber-400 text-slate-950 font-black text-xs transition-all shadow-md shadow-amber-500/20 cursor-pointer flex items-center gap-2">
                        <span>{{ $isEditMode ? 'Simpan Perubahan' : 'Tambah User Teknisi' }}</span>
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
