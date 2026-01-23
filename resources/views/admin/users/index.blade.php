<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-xl text-white leading-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                {{ __('Master Data User / Teknisi') }}
            </h2>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm border border-white/20 text-white text-sm">
                    <span class="opacity-75">Total User:</span> 
                    <span class="font-bold ml-1">{{ $users->total() }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ selected: [], role: 'user' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Toolbar & Search --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden mb-6 p-5">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    {{-- Left: Search --}}
                    <form action="{{ route('admin.users.index') }}" method="GET" class="w-full md:w-96 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 text-sm transition-all bg-gray-50 focus:bg-white" 
                               placeholder="Cari user, email, atau role...">
                    </form>

                    {{-- Right: Actions --}}
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        {{-- Bulk Delete --}}
                        <form action="{{ route('admin.users.bulk-destroy') }}" method="POST" onsubmit="return confirm('Yakin hapus ' + this.selected.length + ' user terpilih?')">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in selected">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" 
                                    x-show="selected.length > 0"
                                    x-transition
                                    class="px-4 py-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-colors flex items-center gap-2 font-medium text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus (<span x-text="selected.length"></span>)
                            </button>
                        </form>

                        <button x-on:click.prevent="$dispatch('open-modal', 'create-user-modal')" 
                                class="w-full md:w-auto px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-xl shadow-lg shadow-teal-500/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2 font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            Tambah User
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden">
                {{-- Mobile Card View --}}
                <div class="block lg:hidden grid grid-cols-1 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($users as $user)
                    <div class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-4">
                            <div class="h-10 w-10 rounded-full bg-teal-100 flex-shrink-0 flex items-center justify-center text-teal-600 font-bold text-sm">
                                 {{ substr($user->name, 0, 2) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-bold text-gray-900 dark:text-white truncate">{{ $user->name }}</h3>
                                    <input type="checkbox" value="{{ $user->id }}" x-model="selected" class="rounded border-gray-300 text-teal-600 shadow-sm w-5 h-5 focus:ring-teal-500">
                                </div>
                                <p class="text-xs text-gray-500 truncate mb-2">{{ $user->email }}</p>
                                
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @php
                                        $roleColors = [
                                            'admin' => 'bg-purple-100 text-purple-800',
                                            'technician' => 'bg-teal-100 text-teal-800',
                                            'gudang' => 'bg-orange-100 text-orange-800',
                                            'pic' => 'bg-blue-100 text-blue-800',
                                            'user' => 'bg-gray-100 text-gray-800',
                                            'hr' => 'bg-green-100 text-green-800',
                                        ];
                                        
                                        $roleNames = [
                                            'admin' => 'Admin',
                                            'technician' => 'Teknisi',
                                            'gudang' => 'Gudang',
                                            'pic' => 'PIC',
                                            'user' => 'User',
                                            'hr' => 'HR / HRD',
                                        ];
                                        $color = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $color }}">
                                        {{ $roleNames[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                    @if($user->specialization)
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                                            {{ $user->specialization }}
                                        </span>
                                    @endif
                                </div>
                
                                @if($user->phone)
                                <div class="flex items-center gap-1 text-xs text-gray-500 mb-3">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    {{ $user->phone }}
                                </div>
                                @endif
                                
                                <div class="flex gap-2">
                                    <button x-on:click.prevent="$dispatch('open-modal', 'edit-user-modal-{{ $user->id }}')" 
                                            class="flex-1 bg-teal-50 text-teal-700 px-3 py-1.5 rounded-lg text-xs font-bold text-center hover:bg-teal-100 transition-colors">
                                        Edit Akses
                                    </button>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="flex-1" onsubmit="return confirm('Hapus user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-50 text-red-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                        <div class="text-center p-6 text-gray-500 italic text-sm">User tidak ditemukan.</div>
                    @endforelse
                </div>
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gradient-to-r from-teal-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700">
                                <th scope="col" class="px-6 py-4 text-left">
                                    <input type="checkbox" 
                                           class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500"
                                           @click="selected = $el.checked ? {{ json_encode($users->pluck('id')) }} : []"
                                           :checked="selected.length === {{ $users->count() }} && {{ $users->count() }} > 0">
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Role & Spesialisasi</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Kontak</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($users as $user)
                            <tr class="hover:bg-teal-50/30 dark:hover:bg-gray-700 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" value="{{ $user->id }}" x-model="selected" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 font-bold text-sm">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $roleColors = [
                                            'admin' => 'bg-purple-100 text-purple-800',
                                            'technician' => 'bg-teal-100 text-teal-800',
                                            'gudang' => 'bg-orange-100 text-orange-800',
                                            'pic' => 'bg-blue-100 text-blue-800',
                                            'user' => 'bg-gray-100 text-gray-800',
                                            'hr' => 'bg-green-100 text-green-800',
                                        ];
                                        
                                        $roleNames = [
                                            'admin' => 'Admin',
                                            'technician' => 'Teknisi',
                                            'gudang' => 'Gudang',
                                            'pic' => 'PIC',
                                            'user' => 'User',
                                            'hr' => 'HR / HRD',
                                        ];
                                        $color = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                        {{ $roleNames[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                    @if($user->specialization)
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            {{ $user->specialization }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $user->phone ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button x-on:click.prevent="$dispatch('open-modal', 'edit-user-modal-{{ $user->id }}')" 
                                            class="text-teal-600 hover:text-teal-900 dark:hover:text-teal-400 mr-3 transition-colors p-1 hover:bg-teal-50 rounded-lg">
                                        <span class="sr-only">Edit</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition-colors p-1 hover:bg-red-50 rounded-lg">
                                            <span class="sr-only">Hapus</span>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <x-modal name="edit-user-modal-{{ $user->id }}" :show="false" focusable maxWidth="4xl">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-0">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-teal-500 to-emerald-600 text-white rounded-t-lg">
                                        <h2 class="text-xl font-bold flex items-center gap-3">
                                            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </div>
                                            Edit User Access
                                        </h2>
                                        <button type="button" x-on:click="$dispatch('close')" class="text-white/70 hover:text-white transition-colors p-1 hover:bg-white/10 rounded-full">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                    <input type="hidden" name="form_type" value="edit_user_{{ $user->id }}">
                                    
                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
                                        {{-- Left Column: User Details --}}
                                        <div class="lg:col-span-4 p-6 bg-gray-50 dark:bg-gray-800/50 border-r border-gray-100 dark:border-gray-700 space-y-6">
                                            <div>
                                                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4 border-b pb-2 border-gray-200">Data Personal</h3>
                                                <div class="space-y-4">
                                                    <div>
                                                        <x-input-label for="name" :value="__('Nama Lengkap')" />
                                                        <x-text-input id="name" class="block mt-1 w-full bg-white dark:bg-gray-900" type="text" name="name" :value="$user->name" required />
                                                    </div>
                                                    <div>
                                                        <x-input-label for="email" :value="__('Alamat Email')" />
                                                        <x-text-input id="email" class="block mt-1 w-full bg-white dark:bg-gray-900" type="email" name="email" :value="$user->email" required />
                                                    </div>
                                                    <div>
                                                        <x-input-label for="phone" :value="__('No. WhatsApp')" />
                                                        <x-text-input id="phone" class="block mt-1 w-full bg-white dark:bg-gray-900" type="text" name="phone" :value="$user->phone" placeholder="628xxx" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4 border-b pb-2 border-gray-200 pt-2">Peran & Keamanan</h3>
                                                <div class="space-y-4" x-data="{ currentRole: '{{ $user->role }}' }">
                                                    <div>
                                                        <x-input-label for="role" :value="__('Role Akun')" class="mb-1" />
                                                        <select id="role" name="role" x-model="currentRole" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 rounded-lg shadow-sm text-sm">
                                                            <option value="user">User Staff</option>
                                                            <option value="technician">Technician</option>
                                                            <option value="pic">PIC Material</option>
                                                            <option value="gudang">Staff Gudang</option>
                                                            <option value="hr">HR / HRD</option>
                                                            <option value="admin">Administrator</option>
                                                        </select>
                                                    </div>

                                                    <div x-show="currentRole === 'technician'" x-transition class="pt-2">
                                                        <x-input-label for="specialization" :value="__('Spesialisasi Teknis')" />
                                                        <select id="specialization" name="specialization" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 rounded-lg shadow-sm text-sm">
                                                            <option value="">-- Pilih --</option>
                                                            <optgroup label="Preparation">
                                                                <option value="Washing" {{ $user->specialization === 'Washing' ? 'selected' : '' }}>Washing</option>
                                                                <option value="Sol Repair" {{ $user->specialization === 'Sol Repair' ? 'selected' : '' }}>Sol Repair</option>
                                                                <option value="Upper Repair" {{ $user->specialization === 'Upper Repair' ? 'selected' : '' }}>Upper Repair</option>
                                                            </optgroup>
                                                            <optgroup label="Repaint & Treatment">
                                                                <option value="Repaint" {{ $user->specialization === 'Repaint' ? 'selected' : '' }}>Repaint</option>
                                                                <option value="Treatment" {{ $user->specialization === 'Treatment' ? 'selected' : '' }}>Treatment</option>
                                                            </optgroup>
                                                            <optgroup label="QC">
                                                                <option value="Jahit" {{ $user->specialization === 'Jahit' ? 'selected' : '' }}>Jahit</option>
                                                                <option value="Clean Up" {{ $user->specialization === 'Clean Up' ? 'selected' : '' }}>Clean Up</option>
                                                                <option value="PIC QC" {{ $user->specialization === 'PIC QC' ? 'selected' : '' }}>PIC QC</option>
                                                            </optgroup>
                                                        </select>
                                                    </div>

                                                    <div class="pt-4 border-t border-dashed border-gray-200 mt-2">
                                                        <x-input-label for="password" :value="__('Ubah Password (Opsional)')" />
                                                        <x-text-input id="password" class="block mt-1 w-full text-sm" type="password" name="password" placeholder="Kosongkan jika tetap" />
                                                        <x-text-input id="password_confirmation" class="block mt-2 w-full text-sm" type="password" name="password_confirmation" placeholder="Konfirmasi Password" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Right Column: Access Rights --}}
                                        <div class="lg:col-span-8 p-6 space-y-6">
                                            <div class="flex items-center justify-between mb-4">
                                                 <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.536 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                                    Hak Akses Modul
                                                </h3>
                                                <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs rounded-full border border-amber-200">
                                                    Pilih modul yang dapat diakses
                                                </span>
                                            </div>

                                            @php
                                                $allDivisions = [
                                                    [
                                                        'title' => 'Analitik & Dashboard',
                                                        'color' => 'blue',
                                                        'modules' => [
                                                            'dashboard' => 'Dashboard Utama',
                                                            'workshop.dashboard' => 'Workshop Analytics',
                                                            'admin.performance' => 'Statistik Performa',
                                                        ]
                                                    ],
                                                    [
                                                        'title' => 'Operasional Workshop',
                                                        'color' => 'teal',
                                                        'modules' => [
                                                            'gudang' => 'Gudang (Reception)',
                                                            'assessment' => 'Assessment / Antrian',
                                                            'preparation' => 'Preparation Station',
                                                            'sortir' => 'Sortir & Material',
                                                            'production' => 'Produksi Station',
                                                            'qc' => 'Quality Control (QC)',
                                                            'finish' => 'Finishing & Pickup',
                                                        ]
                                                    ],
                                                    [
                                                        'title' => 'Customer & Pelayanan',
                                                        'color' => 'amber',
                                                        'modules' => [
                                                            'cs' => 'CS (Lead Management)',
                                                            'cx' => 'CX (Followup & OTO)',
                                                            'admin.complaints' => 'Keluhan Pelanggan',
                                                            'admin.customers' => 'Database Pelanggan',
                                                        ]
                                                    ],
                                                    [
                                                        'title' => 'Finance & Logistik',
                                                        'color' => 'emerald',
                                                        'modules' => [
                                                            'finance' => 'Finance / Pembayaran',
                                                            'admin.purchases' => 'Manajemen Pembelian',
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
                                                            'algorithm.dashboard' => 'Algorithm Management',
                                                        ]
                                                    ],
                                                ];
                                            @endphp

                                            <div class="space-y-4">
                                                @foreach($allDivisions as $division)
                                                <div class="p-4 bg-white border border-gray-100 rounded-xl shadow-sm">
                                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full bg-{{ $division['color'] }}-400"></span> {{ $division['title'] }}
                                                    </h4>
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                                        @foreach($division['modules'] as $key => $label)
                                                            <label class="group relative cursor-pointer">
                                                                <input type="checkbox" name="access_rights[]" value="{{ $key }}" 
                                                                       {{ in_array($key, $user->access_rights ?? []) ? 'checked' : '' }}
                                                                       class="peer sr-only">
                                                                <div class="p-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 transition-all duration-200 peer-checked:border-{{ $division['color'] }}-500 peer-checked:ring-1 peer-checked:ring-{{ $division['color'] }}-500 peer-checked:bg-{{ $division['color'] }}-50/50 dark:peer-checked:bg-{{ $division['color'] }}-900/20">
                                                                    <div class="flex items-center gap-3">
                                                                        <div class="w-5 h-5 rounded-md border border-gray-300 dark:border-gray-600 flex items-center justify-center text-white peer-checked:bg-{{ $division['color'] }}-500 peer-checked:border-{{ $division['color'] }}-500 transition-colors">
                                                                            <svg class="w-3.5 h-3.5 opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                            <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-20 peer-checked:hidden transition-opacity text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                        </div>
                                                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200 peer-checked:text-{{ $division['color'] }}-700 dark:peer-checked:text-{{ $division['color'] }}-400 select-none">{{ $label }}</span>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                            <div class="mt-6 p-4 bg-teal-50 dark:bg-teal-900/20 rounded-xl border border-teal-100 dark:border-teal-800 flex items-start gap-3">
                                                <svg class="w-5 h-5 text-teal-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                <div>
                                                    <h5 class="text-sm font-bold text-teal-800 dark:text-teal-300">Catatan Administrator</h5>
                                                    <p class="text-xs text-teal-600 dark:text-teal-400 mt-1">
                                                        User dengan role <strong>Admin</strong> secara otomatis memiliki akses penuh ke semua modul, terlepas dari pilihan di atas.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-6 bg-gray-50/50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 rounded-b-lg">
                                        <button type="button" x-on:click="$dispatch('close')" 
                                            class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all shadow-sm">
                                            {{ __('Batal') }}
                                        </button>
                                        <button type="submit" 
                                            class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 text-white text-sm font-bold rounded-xl hover:from-teal-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 shadow-md shadow-teal-500/20 transform hover:-translate-y-0.5 transition-all">
                                            {{ __('Simpan Perubahan') }}
                                        </button>
                                    </div>
                                </form>
                            </x-modal>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    User tidak ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if($users->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-100 dark:border-gray-600">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-user-modal" :show="$errors->any() && old('form_type') === 'create_user'" focusable maxWidth="4xl">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <input type="hidden" name="form_type" value="create_user">

            <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-teal-500 to-emerald-600 text-white rounded-t-lg">
                <h2 class="text-xl font-bold flex items-center gap-3">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    Tambah User Baru
                </h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-white/70 hover:text-white transition-colors p-1 hover:bg-white/10 rounded-full">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0" x-data="{ role: 'user' }">
                {{-- Left Column: User Details --}}
                <div class="lg:col-span-4 p-6 bg-gray-50 dark:bg-gray-800/50 border-r border-gray-100 dark:border-gray-700 space-y-6">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4 border-b pb-2 border-gray-200">Data Personal</h3>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="name" :value="__('Nama Lengkap')" />
                                <x-text-input id="name" class="block mt-1 w-full bg-white dark:bg-gray-900" type="text" name="name" :value="old('name')" required />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('Alamat Email')" />
                                <x-text-input id="email" class="block mt-1 w-full bg-white dark:bg-gray-900" type="email" name="email" :value="old('email')" required />
                            </div>
                            <div>
                                <x-input-label for="phone" :value="__('No. WhatsApp')" />
                                <x-text-input id="phone" class="block mt-1 w-full bg-white dark:bg-gray-900" type="text" name="phone" :value="old('phone')" placeholder="628xxx" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4 border-b pb-2 border-gray-200 pt-2">Peran & Keamanan</h3>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="role" :value="__('Role Akun')" class="mb-1" />
                                <select id="role" name="role" x-model="role" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 rounded-lg shadow-sm text-sm">
                                    <option value="user">User Staff</option>
                                    <option value="technician">Technician</option>
                                    <option value="pic">PIC Material</option>
                                    <option value="gudang">Staff Gudang</option>
                                    <option value="hr">HR / HRD</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>

                            <div x-show="role === 'technician'" x-transition class="pt-2">
                                <x-input-label for="specialization" :value="__('Spesialisasi Teknis')" />
                                <select id="specialization" name="specialization" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 rounded-lg shadow-sm text-sm">
                                    <option value="">-- Pilih --</option>
                                    <optgroup label="Preparation">
                                        <option value="Washing">Washing</option>
                                        <option value="Sol Repair">Sol Repair</option>
                                        <option value="Upper Repair">Upper Repair</option>
                                    </optgroup>
                                    <optgroup label="Repaint & Treatment">
                                        <option value="Repaint">Repaint</option>
                                        <option value="Treatment">Treatment</option>
                                    </optgroup>
                                    <optgroup label="QC">
                                        <option value="Jahit">Jahit</option>
                                        <option value="Clean Up">Clean Up</option>
                                        <option value="PIC QC">PIC QC</option>
                                    </optgroup>
                                </select>
                            </div>

                            <div class="pt-4 border-t border-dashed border-gray-200 mt-2">
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" class="block mt-1 w-full text-sm" type="password" name="password" required />
                                <x-text-input id="password_confirmation" class="block mt-2 w-full text-sm" type="password" name="password_confirmation" placeholder="Konfirmasi Password" required />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Access Rights --}}
                <div class="lg:col-span-8 p-6 space-y-6">
                    <div class="flex items-center justify-between mb-4">
                         <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.536 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                            Hak Akses Modul
                        </h3>
                        <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs rounded-full border border-amber-200">
                            Pilih modul yang dapat diakses
                        </span>
                    </div>

                                    @php
                                        if(!isset($allDivisions)) {
                                            $allDivisions = [
                                                [
                                                    'title' => 'Analitik & Dashboard',
                                                    'color' => 'blue',
                                                    'modules' => [
                                                        'dashboard' => 'Dashboard Utama',
                                                        'workshop.dashboard' => 'Workshop Analytics',
                                                        'admin.performance' => 'Statistik Performa',
                                                    ]
                                                ],
                                                [
                                                    'title' => 'Operasional Workshop',
                                                    'color' => 'teal',
                                                    'modules' => [
                                                        'gudang' => 'Gudang (Reception)',
                                                        'assessment' => 'Assessment / Antrian',
                                                        'preparation' => 'Preparation Station',
                                                        'sortir' => 'Sortir & Material',
                                                        'production' => 'Produksi Station',
                                                        'qc' => 'Quality Control (QC)',
                                                        'finish' => 'Finishing & Pickup',
                                                    ]
                                                ],
                                                [
                                                    'title' => 'Customer & Pelayanan',
                                                    'color' => 'amber',
                                                    'modules' => [
                                                        'cs' => 'CS (Lead Management)',
                                                        'cx' => 'CX (Followup & OTO)',
                                                        'admin.complaints' => 'Keluhan Pelanggan',
                                                        'admin.customers' => 'Database Pelanggan',
                                                    ]
                                                ],
                                                [
                                                    'title' => 'Finance & Logistik',
                                                    'color' => 'emerald',
                                                    'modules' => [
                                                        'finance' => 'Finance / Pembayaran',
                                                        'admin.purchases' => 'Manajemen Pembelian',
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
                                                        'algorithm.dashboard' => 'Algorithm Management',
                                                    ]
                                                ],
                                            ];
                                        }
                                    @endphp

                                    <div class="space-y-4">
                                        @foreach($allDivisions as $division)
                                        <div class="p-4 bg-white border border-gray-100 rounded-xl shadow-sm">
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-{{ $division['color'] }}-400"></span> {{ $division['title'] }}
                                            </h4>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                                @foreach($division['modules'] as $key => $label)
                                                    <label class="group relative cursor-pointer">
                                                        <input type="checkbox" name="access_rights[]" value="{{ $key }}" 
                                                               class="peer sr-only">
                                                        <div class="p-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 transition-all duration-200 peer-checked:border-{{ $division['color'] }}-500 peer-checked:ring-1 peer-checked:ring-{{ $division['color'] }}-500 peer-checked:bg-{{ $division['color'] }}-50/50 dark:peer-checked:bg-{{ $division['color'] }}-900/20">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-5 h-5 rounded-md border border-gray-300 dark:border-gray-600 flex items-center justify-center text-white peer-checked:bg-{{ $division['color'] }}-500 peer-checked:border-{{ $division['color'] }}-500 transition-colors">
                                                                    <svg class="w-3.5 h-3.5 opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-20 peer-checked:hidden transition-opacity text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                </div>
                                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200 peer-checked:text-{{ $division['color'] }}-700 dark:peer-checked:text-{{ $division['color'] }}-400 select-none">{{ $label }}</span>
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-6 p-4 bg-teal-50 dark:bg-teal-900/20 rounded-xl border border-teal-100 dark:border-teal-800 flex items-start gap-3">
                                        <svg class="w-5 h-5 text-teal-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <div>
                                            <h5 class="text-sm font-bold text-teal-800 dark:text-teal-300">Catatan Administrator</h5>
                                            <p class="text-xs text-teal-600 dark:text-teal-400 mt-1">
                                                User dengan role <strong>Admin</strong> secara otomatis memiliki akses penuh ke semua modul, terlepas dari pilihan di atas.
                                            </p>
                                        </div>
                                    </div>
                </div>
            </div>

            <div class="p-6 bg-gray-50/50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 rounded-b-lg">
                <button type="button" x-on:click="$dispatch('close')" 
                    class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all shadow-sm">
                    {{ __('Batal') }}
                </button>
                <button type="submit" 
                    class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 text-white text-sm font-bold rounded-xl hover:from-teal-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 shadow-md shadow-teal-500/20 transform hover:-translate-y-0.5 transition-all">
                    {{ __('Simpan User') }}
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
