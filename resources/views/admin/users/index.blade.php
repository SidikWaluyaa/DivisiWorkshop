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
                <div class="grid lg:hidden grid-cols-1 divide-y divide-gray-100 dark:divide-gray-700">
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
                                            'owner' => 'bg-indigo-100 text-indigo-800',
                                            'technician' => 'bg-teal-100 text-teal-800',
                                            'gudang' => 'bg-orange-100 text-orange-800',
                                            'pic' => 'bg-cyan-100 text-cyan-800',
                                            'cs' => 'bg-pink-100 text-pink-800',
                                            'finance' => 'bg-emerald-100 text-emerald-800',
                                            'spv' => 'bg-amber-100 text-amber-800',
                                            'hr' => 'bg-green-100 text-green-800',
                                            'user' => 'bg-gray-100 text-gray-800',
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
                                    @if ($user->is_active)
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-800">
                                            Nonaktif
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
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="delete-confirm w-full bg-red-50 text-red-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors"
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
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-teal-800 dark:text-teal-400 uppercase tracking-wider">Status</th>
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->is_active)
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Nonaktif
                                        </span>
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
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="delete-confirm text-red-400 hover:text-red-600 transition-colors p-1 hover:bg-red-50 rounded-lg"
                                                data-title="Hapus Akun User?"
                                                data-text="Apakah Anda yakin ingin menghapus user '{{ $user->name }}'? Tindakan ini tidak dapat dibatalkan."
                                                data-confirm="Ya, Hapus!"
                                                data-cancel="Batal">
                                            <span class="sr-only">Hapus</span>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>

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
    @include('admin.users.partials.create-modal', ['allDivisions' => $allDivisions])

    <!-- Edit Modals -->
    @foreach ($users as $user)
        @include('admin.users.partials.edit-modal', ['user' => $user, 'allDivisions' => $allDivisions])
    @endforeach
</x-app-layout>
