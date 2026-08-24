<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            {{ __('Master Data User / Teknisi') }}
        </h2>
    </x-slot>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 font-sans" x-data="{ selected: [], role: 'user' }">
        {{-- Header Banner --}}
        <div class="bg-gradient-to-r from-[#1a3b34] via-[#22AF85] to-[#1a3b34] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-emerald-400/20">
            <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-white/10 rounded-full border border-white/20 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-[#FFC232] shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#FFC232] animate-pulse"></span>
                        <span>USER DIRECTORY &amp; ACCESS CONTROL</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white leading-tight">
                        Master Data <span class="text-[#FFC232]">User &amp; Teknisi</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100/90 font-medium max-w-xl leading-relaxed">
                        Kelola akun pengguna, alokasi peran (Role), spesialisasi teknis, dan hak akses modul workshop secara terpusat.
                    </p>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto justify-start md:justify-end shrink-0">
                    <div class="px-4 py-2.5 bg-slate-900/80 backdrop-blur-md rounded-2xl border border-white/10 text-white text-xs font-bold flex items-center gap-2 shadow-lg">
                        <span class="text-emerald-400">Total Pengguna:</span>
                        <span class="font-black text-sm text-[#FFC232]">{{ $users->total() }}</span>
                    </div>

                    <button x-on:click.prevent="$dispatch('open-modal', 'create-user-modal')" 
                            class="px-5 py-2.5 bg-[#FFC232] hover:bg-amber-400 text-slate-950 rounded-2xl shadow-lg shadow-amber-500/20 transition-all active:scale-95 flex items-center gap-2 font-black text-xs border border-amber-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Tambah User
                    </button>
                </div>
            </div>
        </div>
        @php
            $allDivisions = [
                [
                    'title' => 'Analitik & Dashboard',
                    'color' => 'blue',
                    'modules' => [
                        'dashboard' => 'Dashboard Utama',
                        'workshop.dashboard' => 'Workshop Analytics',
                        'cx.dashboard' => 'CX Analytics',
                        'admin.performance' => 'Statistik Performa',
                    ]
                ],
                [
                    'title' => 'Operasional Workshop',
                    'color' => 'teal',
                    'modules' => [
                        'gudang' => 'Penerimaan (Reception)',
                        'assessment' => 'Assessment / Antrian',
                        'preparation' => 'Preparation Station',
                        'sortir' => 'Sortir & Material',
                        'production' => 'Produksi Station',
                        'qc' => 'Quality Control (QC)',
                        'finish' => 'Finishing & Pickup',
                        'gallery' => 'Gallery Dokumentasi',
                    ]
                ],
                [
                    'title' => 'Marketing & Pelayanan',
                    'color' => 'amber',
                    'modules' => [
                        'cs' => 'CS (Lead Management)',
                        'cs.greeting' => 'Greeting Chat (Import)',
                        'cs.spk' => 'Data SPK CS',
                        'admin.promotions' => 'Manajemen Promo',
                        'cx' => 'CX (Followup)',
                        'admin.customers' => 'Database Pelanggan',
                        'admin.complaints' => 'Keluhan Pelanggan',
                    ]
                ],
                [
                    'title' => 'Finance & Logistik',
                    'color' => 'emerald',
                    'modules' => [
                        'finance' => 'Finance / Pembayaran',
                        'manifest.index' => 'Manifest / Logistik',
                        'admin.purchases' => 'Manajemen Pembelian',
                        'warehouse.storage' => 'Manajemen Rak (Storage)',
                        'admin.materials.request' => 'Material Request (PO)',
                    ]
                ],
                [
                    'title' => 'Master Data',
                    'color' => 'purple',
                    'modules' => [
                        'admin.services' => 'Katalog Layanan',
                        'admin.materials' => 'Katalog Material',
                    ]
                ],
                [
                    'title' => 'Administrasi & Sistem',
                    'color' => 'rose',
                    'modules' => [
                        'admin.reports' => 'Laporan Sistem',
                        'admin.users' => 'Manajemen User',
                        'admin.system' => 'System Tools',
                        'admin.data-integrity' => 'Data Integrity Hub',
                    ]
                ],
            ];
        @endphp

        {{-- Filter & Action Panel --}}
        <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-100 space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-slate-100">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#22AF85]"></span>
                        <h3 class="font-black text-slate-800 text-sm uppercase tracking-wider">Filter &amp; Pencarian User</h3>
                    </div>
                    <p class="text-xs font-bold text-slate-400">Gunakan filter di bawah untuk menyaring daftar akun secara cepat dan akurat.</p>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                    {{-- Bulk Delete --}}
                    <form action="{{ route('admin.users.bulk-destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <template x-for="id in selected">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="button" 
                                x-show="selected.length > 0"
                                x-transition
                                @click="
                                    const isDark = document.documentElement.classList.contains('dark') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                                    Swal.fire({
                                        title: 'Hapus ' + selected.length + ' User Terpilih?',
                                        text: 'Apakah Anda yakin ingin menghapus ' + selected.length + ' user yang dipilih? Tindakan ini akan menghapus data mereka secara permanen.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#EF4444',
                                        cancelButtonColor: '#6B7280',
                                        confirmButtonText: 'Ya, Hapus Semua!',
                                        cancelButtonText: 'Batal',
                                        background: isDark ? '#1f2937' : '#ffffff',
                                        color: isDark ? '#f3f4f6' : '#111827'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $el.closest('form').submit();
                                        }
                                    })
                                "
                                class="px-4 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-2xl transition-all active:scale-95 flex items-center gap-2 font-black text-xs border border-red-200 shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus (<span x-text="selected.length"></span>)
                        </button>
                    </form>
                </div>
            </div>

            {{-- Filter Form --}}
            <form action="{{ route('admin.users.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                    {{-- Keyword Search --}}
                    <div class="relative">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Cari User/Email/Role</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all" 
                                   placeholder="Nama, email, role...">
                        </div>
                    </div>

                    {{-- Filter Peran --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Filter Peran (Role)</label>
                        <select name="role" 
                                class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all cursor-pointer">
                            <option value="">-- Semua Peran --</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner / Direktur</option>
                            <option value="spv" {{ request('role') === 'spv' ? 'selected' : '' }}>Supervisor</option>
                            <option value="technician" {{ request('role') === 'technician' ? 'selected' : '' }}>Teknisi / Workshop</option>
                            <option value="gudang" {{ request('role') === 'gudang' ? 'selected' : '' }}>Staf Gudang</option>
                            <option value="cs" {{ request('role') === 'cs' ? 'selected' : '' }}>Customer Service</option>
                            <option value="finance" {{ request('role') === 'finance' ? 'selected' : '' }}>Finance / Kasir</option>
                            <option value="pic" {{ request('role') === 'pic' ? 'selected' : '' }}>PIC Material</option>
                            <option value="hr" {{ request('role') === 'hr' ? 'selected' : '' }}>HR / HRD</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Staff / User</option>
                        </select>
                    </div>

                    {{-- Filter Status Akun --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Status Akun</label>
                        <select name="is_active" 
                                class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all cursor-pointer">
                            <option value="">-- Semua Status --</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    {{-- Filter Status Online --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Status Keaktifan</label>
                        <select name="online_status" 
                                class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all cursor-pointer">
                            <option value="">-- Semua Keaktifan --</option>
                            <option value="online" {{ request('online_status') === 'online' ? 'selected' : '' }}>Online (Sedang Aktif)</option>
                            <option value="offline" {{ request('online_status') === 'offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>

                    {{-- Filter Spesialisasi --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Spesialisasi</label>
                        <select name="specialization" 
                                class="w-full py-2.5 rounded-xl border border-slate-200 text-xs font-bold bg-slate-50 focus:bg-white focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 transition-all cursor-pointer">
                            <option value="">-- Semua Spesialisasi --</option>
                            @foreach ($specializations as $spec)
                                <option value="{{ $spec }}" {{ request('specialization') === $spec ? 'selected' : '' }}>
                                    {{ $spec }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-black transition-all">
                        Reset Filter
                    </a>
                    <button type="submit" 
                            class="px-5 py-2.5 bg-[#22AF85] hover:bg-emerald-600 text-white rounded-xl text-xs font-black shadow-md shadow-emerald-950/10 transition-all active:scale-95">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Main Table / Card Container --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            {{-- Mobile Card View --}}
            <div class="grid lg:hidden grid-cols-1 divide-y divide-slate-100">
                @forelse ($users as $user)
                <div class="p-5 bg-white hover:bg-slate-50/80 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-[#22AF85] to-[#1a3b34] text-white flex-shrink-0 flex items-center justify-center font-black text-sm shadow-md">
                             {{ substr($user->name, 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <h3 class="font-black text-slate-900 text-sm truncate leading-snug">{{ $user->name }}</h3>
                                <input type="checkbox" value="{{ $user->id }}" x-model="selected" class="rounded border-slate-300 text-[#22AF85] shadow-xs w-5 h-5 focus:ring-[#22AF85]">
                            </div>
                            <p class="text-xs font-medium text-slate-400 truncate mb-2.5">{{ $user->email }}</p>
                            
                            <div class="flex flex-wrap gap-1.5 mb-3">
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
                                <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full border {{ $color }}">
                                    {{ $roleNames[$user->role] ?? ucfirst($user->role) }}
                                </span>
                                @if($user->specialization)
                                    <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                        ✨ {{ $user->specialization }}
                                    </span>
                                @endif
                                @if ($user->is_active)
                                    <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        ● Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                        ○ Nonaktif
                                    </span>
                                @endif
                            </div>

                            <div class="text-[10px] font-bold text-slate-400 mb-3 flex items-center gap-1.5">
                                <span class="uppercase tracking-wider opacity-70">Aktif Terakhir:</span>
                                @if($user->last_active_at)
                                    @if($user->last_active_at->diffInMinutes(now()) < 5)
                                        <span class="inline-flex items-center gap-1 text-emerald-600 font-black">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                                            Online
                                        </span>
                                    @else
                                        <span title="{{ $user->last_active_at->translatedFormat('d M Y H:i:s') }}" class="text-slate-600">
                                            {{ $user->last_active_at->diffForHumans() }}
                                        </span>
                                    @endif
                                @else
                                    <span class="italic text-slate-400">Belum pernah aktif</span>
                                @endif
                            </div>
            
                            <div class="flex gap-2">
                                <button x-on:click.prevent="$dispatch('open-modal', 'edit-user-modal-{{ $user->id }}')" 
                                        class="flex-1 bg-emerald-50 text-[#22AF85] hover:bg-emerald-100 px-3 py-2 rounded-xl text-xs font-black text-center transition-all border border-emerald-200">
                                    Edit Akses
                                </button>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="delete-confirm w-full bg-red-50 text-red-600 hover:bg-red-100 px-3 py-2 rounded-xl text-xs font-black text-center transition-all border border-red-200"
                                            data-title="Hapus Akun User?"
                                            data-text="Apakah Anda yakin ingin menghapus user '{{ $user->name }}'? Tindakan ini tidak dapat dibatalkan."
                                            data-confirm="Ya, Hapus!"
                                            data-cancel="Batal">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="text-center p-8 text-slate-400 italic text-xs font-bold">User tidak ditemukan.</div>
                @endforelse
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white">
                            <th scope="col" class="px-6 py-4 text-left w-12">
                                <input type="checkbox" 
                                       class="rounded border-slate-700 bg-slate-800 text-[#22AF85] shadow-xs focus:ring-[#22AF85]"
                                       @click="selected = $el.checked ? {{ json_encode($users->pluck('id')) }} : []"
                                       :checked="selected.length === {{ $users->count() }} && {{ $users->count() }} > 0">
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-200">Pengguna (User)</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-200">Role &amp; Spesialisasi</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-200">Aktif Terakhir</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-200">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-200">Kontak</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-black uppercase tracking-wider text-slate-200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse ($users as $user)
                        <tr class="hover:bg-emerald-50/40 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" value="{{ $user->id }}" x-model="selected" class="rounded border-slate-300 text-[#22AF85] shadow-xs focus:ring-[#22AF85]">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-2xl bg-gradient-to-br from-[#22AF85] to-[#1a3b34] text-white flex items-center justify-center font-black text-xs shadow-md shrink-0">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div class="ml-3.5">
                                        <div class="text-xs font-black text-slate-900 group-hover:text-[#22AF85] transition-colors">{{ $user->name }}</div>
                                        <div class="text-[11px] font-medium text-slate-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $roleColors = [
                                        'admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'technician' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'gudang' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'pic' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                        'user' => 'bg-slate-100 text-slate-800 border-slate-200',
                                        'hr' => 'bg-green-100 text-green-800 border-green-200',
                                    ];
                                    
                                    $roleNames = [
                                        'admin' => 'Administrator',
                                        'technician' => 'Teknisi / Workshop',
                                        'gudang' => 'Staf Gudang',
                                        'pic' => 'PIC Material',
                                        'user' => 'User',
                                        'hr' => 'HR / HRD',
                                    ];
                                    $color = $roleColors[$user->role] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                                @endphp
                                <span class="px-3 py-1 inline-flex text-[10px] font-black rounded-full border {{ $color }}">
                                    {{ $roleNames[$user->role] ?? ucfirst($user->role) }}
                                </span>
                                @if($user->specialization)
                                    <div class="text-[11px] font-bold text-slate-600 mt-1 flex items-center gap-1">
                                        <span class="text-amber-500">✨</span>
                                        {{ $user->specialization }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->last_active_at)
                                    @if($user->last_active_at->diffInMinutes(now()) < 5)
                                        <div class="flex items-center gap-1.5">
                                            <span class="relative flex h-2 w-2">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                            </span>
                                            <span class="text-[10px] font-black text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">Online</span>
                                        </div>
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
                                @if ($user->is_active)
                                    <span class="px-3 py-1 inline-flex text-[10px] font-black rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        ● Aktif
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-[10px] font-black rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                        ○ Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-600">
                                {{ $user->phone ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-black">
                                <button x-on:click.prevent="$dispatch('open-modal', 'edit-user-modal-{{ $user->id }}')" 
                                        class="text-[#22AF85] hover:text-emerald-700 mr-2 transition-all p-1.5 hover:bg-emerald-50 rounded-xl inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit Akses
                                </button>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="delete-confirm text-rose-500 hover:text-rose-700 transition-all p-1.5 hover:bg-rose-50 rounded-xl inline-flex items-center gap-1"
                                            data-title="Hapus Akun User?"
                                            data-text="Apakah Anda yakin ingin menghapus user '{{ $user->name }}'? Tindakan ini tidak dapat dibatalkan."
                                            data-confirm="Ya, Hapus!"
                                            data-cancel="Batal">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Hapus
                                    </button>
                                </form>
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

            <!-- Pagination -->
            @if($users->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Create Modal -->
    @include('admin.users.partials.create-modal', ['allDivisions' => $allDivisions])

    <!-- Edit Modals -->
    @foreach ($users as $user)
        @include('admin.users.partials.edit-modal', ['user' => $user, 'allDivisions' => $allDivisions])
    @endforeach
</x-app-layout>
