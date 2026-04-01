<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengiriman') }}
        </h2>
    </x-slot>

    <style>
        [x-cloak] { display: none !important; }
        .sticky-header th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #F9FAFB; /* gray-50 fallback */
        }
        .dark .sticky-header th {
            background: #1F2937; /* gray-800 fallback */
        }
        select, input[type="date"], input[type="text"] {
            transition: all 0.2s ease-in-out;
        }
        select:focus, input:focus {
            box-shadow: 0 0 0 4px rgba(34, 175, 133, 0.15);
        }
    </style>

    <div class="py-10 bg-[#FBFBFB]" x-data="shippingTable()" x-cloak>
        <div class="max-w-[1600px] mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Header Section -->
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Antrian Pengiriman</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola konfirmasi, kategori, dan resi pengiriman kustomer secara real-time.</p>
                </div>
                
                <!-- Advanced Filter Card -->
                <div class="w-full xl:w-auto bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-wrap items-center gap-3">
                    <form method="GET" action="{{ route('shipping.index') }}" class="flex flex-wrap items-center gap-3 w-full">
                        <!-- Search Box -->
                        <div class="relative min-w-[300px] flex-grow xl:flex-grow-0">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="search" name="search" value="{{ request('search') }}" 
                                class="block w-full pl-11 pr-4 py-2.5 text-sm border-gray-200 rounded-xl bg-gray-50/50 hover:bg-white focus:bg-white focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all" 
                                placeholder="Cari Nama, No HP, SPK, Resi...">
                        </div>
                        
                        <div class="h-8 w-px bg-gray-100 dark:bg-gray-700 hidden lg:block"></div>

                        <!-- Status Filter -->
                        <select name="status" class="bg-gray-50/50 border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] block py-2.5 px-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all">
                            <option value="">Semua Verifikasi</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                            <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Belum Diverifikasi</option>
                        </select>
                        
                        <!-- Date Range -->
                        <div class="flex items-center gap-2">
                            <input type="date" name="date_start" value="{{ request('date_start') }}" 
                                class="bg-gray-50/50 border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] block py-2.5 px-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all">
                            <span class="text-gray-400 text-xs font-bold uppercase">Ke</span>
                            <input type="date" name="date_end" value="{{ request('date_end') }}" 
                                class="bg-gray-50/50 border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] block py-2.5 px-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all">
                        </div>

                        <button type="submit" class="bg-[#FFC232] text-gray-900 font-bold px-6 py-2.5 rounded-xl hover:brightness-95 transition-all shadow-md active:scale-95 flex items-center gap-2 border border-[#FFC232]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 1.101a1 1 0 00-.707.293L8 15v5l-2-2V8.707a1 1 0 00-.293-.707L2.293 6.586A1 1 0 012 5.879V4z"/></svg>
                            Filter
                        </button>

                        <button type="button" 
                                @click="showManifestModal = true"
                                class="bg-[#22AF85] text-white font-bold px-6 py-2.5 rounded-xl hover:brightness-95 transition-all shadow-md active:scale-95 flex items-center gap-2 border border-[#22AF85]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Cetak Manifest
                        </button>

                        @if(request()->hasAny(['search', 'status', 'date_start', 'date_end']))
                            <a href="{{ route('shipping.index') }}" class="p-2.5 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-all dark:bg-gray-700 dark:text-gray-300" title="Reset Filters">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Main Data Table Card -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto relative max-h-[750px]">
                    <table class="w-full text-sm text-left">
                        <thead class="sticky-header text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-5 text-center w-16">ID</th>
                                <th class="px-6 py-5">Info Kustomer</th>
                                <th class="px-6 py-5">Nomor SPK</th>
                                <th class="px-4 py-5">Kategori</th>
                                <th class="px-6 py-5 text-center">Verifikasi</th>
                                <th class="px-6 py-5">PIC Gudang</th>
                                <th class="px-6 py-5">Target Kirim</th>
                                <th class="px-6 py-5">Resi Pengiriman</th>
                                <th class="px-6 py-5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @forelse($shippings as $shipping)
                            <tr class="group hover:bg-gray-50/80 dark:hover:bg-gray-700/40 transition-all duration-200">
                                <td class="px-6 py-6 text-center">
                                    <span class="text-xs font-mono font-bold text-gray-400">#{{ $shipping->id }}</span>
                                </td>
                                <td class="px-6 py-6 border-l-4 border-transparent group-hover:border-[#22AF85] transition-all">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 dark:text-white text-base">{{ $shipping->customer_name }}</span>
                                        <span class="text-xs text-[#22AF85] font-semibold mt-0.5 flex items-center gap-1.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            {{ $shipping->customer_phone }}
                                        </span>
                                        @if($shipping->workOrder?->customer_address)
                                            <span class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 flex items-start gap-1.5 leading-relaxed">
                                                <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $shipping->workOrder->customer_address }}
                                            </span>
                                        @endif
                                        <span class="text-[10px] text-gray-400 mt-1.5 uppercase font-bold tracking-tighter">Masuk: {{ $shipping->tanggal_masuk->format('d M Y') }}</span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-6">
                                    <span class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-mono font-black border border-gray-200 dark:border-gray-600 group-hover:bg-white transition-all">
                                        {{ $shipping->spk_number }}
                                    </span>
                                </td>

                                <td class="px-4 py-6">
                                    <form id="form-{{ $shipping->id }}" action="{{ route('shipping.update', $shipping->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <select name="kategori_pengiriman" @change="saveForm({{ $shipping->id }})" 
                                            class="w-[140px] text-[11px] font-bold py-2 border-gray-200 rounded-xl bg-gray-50/50 focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:border-gray-700 dark:text-white transition-all cursor-pointer">
                                            <option value="">- Kategori -</option>
                                            <option value="Ojek Online" {{ $shipping->kategori_pengiriman == 'Ojek Online' ? 'selected' : '' }}>🛵 Ojek Online</option>
                                            <option value="Ambil Sendiri" {{ $shipping->kategori_pengiriman == 'Ambil Sendiri' ? 'selected' : '' }}>🏠 Ambil Sendiri</option>
                                            <option value="Ekspedisi" {{ $shipping->kategori_pengiriman == 'Ekspedisi' ? 'selected' : '' }}>📦 Ekspedisi</option>
                                        </select>
                                </td>

                                <td class="px-6 py-6 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_verified" value="1" {{ $shipping->is_verified ? 'checked' : '' }} class="sr-only peer" @change="saveForm({{ $shipping->id }})">
                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#22AF85]"></div>
                                    </label>
                                </td>

                                <td class="px-6 py-6">
                                    <select name="pic" @change="saveForm({{ $shipping->id }})" 
                                        class="w-[150px] text-[11px] font-bold py-2 border-gray-200 rounded-xl bg-gray-50/50 focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:border-gray-700 dark:text-white transition-all">
                                        <option value="">- Pilih PIC -</option>
                                        @foreach($technicians as $tech)
                                            <option value="{{ $tech->name }}" {{ $shipping->pic == $tech->name ? 'selected' : '' }}>👤 {{ $tech->name }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="px-6 py-6">
                                    <input type="date" name="tanggal_pengiriman" value="{{ $shipping->tanggal_pengiriman?->format('Y-m-d') }}" @change="saveForm({{ $shipping->id }})" 
                                        class="w-[140px] text-[11px] font-bold py-2 border-gray-200 rounded-xl bg-gray-50/50 focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:border-gray-700 dark:text-white transition-all">
                                </td>

                                <td class="px-6 py-6">
                                    <div class="relative w-[160px]">
                                        <input type="text" name="resi_pengiriman" value="{{ $shipping->resi_pengiriman }}" @change="saveForm({{ $shipping->id }})" 
                                            placeholder="Input Resi..." 
                                            class="w-full text-[11px] font-bold py-2.5 border-gray-200 rounded-xl bg-gray-50/30 focus:bg-white focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:border-gray-700 dark:text-white transition-all placeholder:text-gray-300">
                                    </div>
                                </td>

                                <td class="px-6 py-6 text-center">
                                    <div class="flex flex-col items-center justify-center gap-1">
                                        <div id="save-indicator-{{ $shipping->id }}" class="hidden group-indicator select-none">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#22AF85]/10 text-[#22AF85] animate-pulse">
                                                Saved
                                            </span>
                                        </div>
                                        @if($shipping->resi_pengiriman)
                                            <span class="w-2 h-2 rounded-full bg-[#22AF85]"></span>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-gray-200 dark:bg-gray-600"></span>
                                        @endif
                                    </div>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-full">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        </div>
                                        <p class="text-gray-500 dark:text-gray-400 font-medium italic">Belum ada antrean pengiriman yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                @if($shippings->hasPages())
                <div class="px-8 py-6 bg-gray-50 dark:bg-gray-800/10 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-tight">Menampilkan {{ $shippings->firstItem() }}-{{ $shippings->lastItem() }} dari {{ $shippings->total() }} Data</span>
                    <div>
                        {{ $shippings->links() }}
                    </div>
                </div>
                @endif
            </div>

        </div>


    <!-- Manifest Selection Modal -->
    <div x-show="showManifestModal" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm" @click="showManifestModal = false"></div>

            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="p-2 bg-[#22AF85]/10 rounded-lg">📋</span>
                        Cetak Manifest Pengiriman
                    </h3>
                    <button @click="showManifestModal = false" class="text-gray-400 hover:text-gray-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18"/></svg>
                    </button>
                </div>

                <form action="{{ route('shipping.manifest.preview') }}" method="GET" target="_blank" @submit="showManifestModal = false">
                    <div class="space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                                <input type="date" name="date_start" value="{{ date('Y-m-d') }}" 
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all py-3 px-4 text-sm font-bold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Selesai</label>
                                <input type="date" name="date_end" value="{{ date('Y-m-d') }}" 
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all py-3 px-4 text-sm font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Kategori Pengiriman</label>
                            <select name="category" 
                                class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all py-3 px-4 text-sm font-bold">
                                <option value="">Semua Kategori</option>
                                <option value="Ojek Online">🛵 Ojek Online</option>
                                <option value="Ekspedisi">🚚 Ekspedisi</option>
                                <option value="Ambil Sendiri">🏢 Ambil Sendiri</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Petugas Penyiap (Opsional)</label>
                            <input type="text" name="prepared_by" placeholder="Contoh: Budi Santoso" 
                                class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all py-3 px-4 text-sm font-bold">
                        </div>
                        
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800/50 flex gap-3">
                            <div class="text-blue-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                                Sistem akan membuka halaman preview manifest pengiriman berdasarkan kriteria yang Anda pilih di atas.
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button type="button" @click="showManifestModal = false" 
                                class="flex-1 px-4 py-3 text-sm font-bold text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-[2] px-4 py-3 text-sm font-bold text-white bg-[#22AF85] rounded-xl hover:brightness-95 shadow-lg shadow-[#22AF85]/20 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Lihat Manifest
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('shippingTable', () => ({
                showManifestModal: false,
                saveForm(id) {
                    const form = document.getElementById('form-' + id);
                    const indicator = document.getElementById('save-indicator-' + id);
                    const formData = new FormData(form);
                    
                    // Show animation placeholder
                    indicator.classList.remove('hidden');
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // High performance visual feedback
                            setTimeout(() => {
                                indicator.classList.add('hidden');
                            }, 1500);
                        }
                    })
                    .catch(error => {
                        console.error('Error saving:', error);
                        alert('Gagal sinkronisasi data.');
                    });
                }
            }));
        });
    </script>
</x-app-layout>