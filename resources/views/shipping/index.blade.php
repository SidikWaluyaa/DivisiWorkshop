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
            background: #F9FAFB;
        }
        .dark .sticky-header th {
            background: #1F2937;
        }
        select, input[type="date"], input[type="text"] {
            transition: all 0.2s ease-in-out;
        }
        select:focus, input:focus {
            box-shadow: 0 0 0 4px rgba(34, 175, 133, 0.15);
        }
    </style>

    <div class="py-8 bg-[#FBFBFB] min-h-screen" x-data="shippingTable()" x-cloak>
        <div class="max-w-[1650px] mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header & Filter Section -->
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-3 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-100">
                            Shipping Logistics Hub
                        </span>
                        <span class="px-3 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest border border-blue-100">
                            Gudang & Ekspedisi
                        </span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Antrian Pengiriman</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Kelola verifikasi, kategori, ekspedisi, tanggal pengiriman, dan resi kustomer secara real-time.</p>
                </div>
                
                <!-- Advanced Filter Controls -->
                <div class="w-full xl:w-auto">
                    <form method="GET" action="{{ route('shipping.index') }}" class="flex flex-wrap items-center gap-2.5">
                        <!-- Search Box -->
                        <div class="relative flex-1 sm:flex-initial sm:w-64">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="search" name="search" value="{{ request('search') }}" 
                                class="block w-full pl-10 pr-3 py-2 text-xs border-gray-200 rounded-xl bg-gray-50/50 hover:bg-white focus:bg-white focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-700 dark:border-gray-600 dark:text-white font-bold transition-all" 
                                placeholder="Cari Nama, HP, SPK, Resi...">
                        </div>

                        <!-- Status Filter -->
                        <select name="status" class="bg-gray-50/50 border-gray-200 text-gray-700 text-xs font-bold rounded-xl focus:ring-[#22AF85] focus:border-[#22AF85] py-2 px-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-all cursor-pointer">
                            <option value="">Semua Verifikasi</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>🟢 Terverifikasi</option>
                            <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>📦 Belum Diverifikasi</option>
                        </select>
                        
                        <!-- Date Range -->
                        <div class="flex items-center gap-1.5 bg-gray-50/50 dark:bg-gray-700 p-1 rounded-xl border border-gray-200 dark:border-gray-600">
                            <input type="date" name="date_start" value="{{ request('date_start') }}" 
                                class="bg-transparent border-none text-gray-700 dark:text-white text-xs font-bold py-1 px-2 focus:ring-0">
                            <span class="text-gray-400 text-[10px] font-black uppercase">s/d</span>
                            <input type="date" name="date_end" value="{{ request('date_end') }}" 
                                class="bg-transparent border-none text-gray-700 dark:text-white text-xs font-bold py-1 px-2 focus:ring-0">
                        </div>

                        <button type="submit" class="bg-[#FFC232] hover:bg-[#f5b827] text-gray-900 font-extrabold text-xs px-4 py-2 rounded-xl transition-all shadow-sm flex items-center gap-1.5">
                            🔍 Filter
                        </button>

                        <button type="button" @click="showManifestModal = true"
                            class="bg-[#22AF85] hover:bg-[#1fa17a] text-white font-extrabold text-xs px-4 py-2 rounded-xl transition-all shadow-sm flex items-center gap-1.5">
                            📋 Cetak Manifest
                        </button>

                        <a href="{{ route('admin.custom-label.index') }}" 
                            class="bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs px-4 py-2 rounded-xl transition-all shadow-sm flex items-center gap-1.5">
                            🏷️ Label Custom
                        </a>

                        @if(request()->hasAny(['search', 'status', 'date_start', 'date_end']))
                            <a href="{{ route('shipping.index') }}" class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition-all font-bold text-xs" title="Reset Filters">
                                🔄 Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Main Data Table Card (No Horizontal Scrollbar, Clean Valid HTML5) -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="w-full">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead class="sticky-header text-[10px] font-black text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                            <tr class="bg-slate-50/75">
                                <th class="py-4 px-3 text-center w-10">No</th>
                                <th class="py-4 px-4 min-w-[220px]">Info Kustomer & SPK</th>
                                <th class="py-4 px-3 w-[125px]">Kategori</th>
                                <th class="py-4 px-3 w-[125px]">Ekspedisi</th>
                                <th class="py-4 px-3 text-center w-[95px]">Verifikasi</th>
                                <th class="py-4 px-3 w-[125px]">PIC Gudang</th>
                                <th class="py-4 px-3 w-[135px]">Tanggal Pengiriman</th>
                                <th class="py-4 px-3 w-[140px]">Resi Pengiriman</th>
                                <th class="py-4 px-3 text-center w-[150px]">Status Pengiriman</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse($shippings as $shipping)
                            <tr id="tr-shipping-{{ $shipping->id }}" class="group hover:bg-slate-50/50 dark:hover:bg-gray-700/40 transition-colors" 
                                x-data="{ 
                                    kategori: '{{ $shipping->kategori_pengiriman }}',
                                    isVerified: {{ $shipping->is_verified ? 'true' : 'false' }},
                                    resi: '{{ $shipping->resi_pengiriman }}',
                                    tglKirim: '{{ $shipping->tanggal_pengiriman ? $shipping->tanggal_pengiriman->format('Y-m-d') : '' }}',
                                    tglKirimFormatted: '{{ $shipping->tanggal_pengiriman ? $shipping->tanggal_pengiriman->format('d M Y') : '' }}',
                                    isShipped() {
                                        return this.isVerified || (this.resi && this.resi.trim() !== '');
                                    }
                                }">
                                
                                {{-- ID / Index --}}
                                <td class="py-4 px-3 text-center font-bold text-gray-400 font-mono text-[11px]">
                                    #{{ $shipping->id }}
                                </td>

                                {{-- Combined Info Kustomer & SPK Badge --}}
                                <td class="py-4 px-4 border-l-2 border-transparent group-hover:border-[#22AF85] transition-all">
                                    <div class="flex flex-col space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-black text-gray-900 dark:text-white text-sm tracking-tight">{{ $shipping->customer_name }}</span>
                                            <span class="text-[11px] font-bold font-mono text-[#22AF85] bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded border border-emerald-100 dark:border-emerald-900/50">
                                                {{ $shipping->customer_phone }}
                                            </span>
                                        </div>

                                        {{-- Single-line SPK Badge (Whitespace-Nowrap) --}}
                                        <div class="flex items-center gap-2 pt-0.5">
                                            <span class="px-2.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-lg text-[11px] font-mono font-black border border-slate-200 dark:border-slate-600 whitespace-nowrap shadow-xs">
                                                {{ $shipping->spk_number }}
                                            </span>
                                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">
                                                Masuk: {{ $shipping->tanggal_masuk->format('d M Y') }}
                                            </span>
                                        </div>

                                        @if($shipping->workOrder?->customer_address)
                                            <div class="text-[10px] text-gray-500 dark:text-gray-400 font-medium line-clamp-1 leading-snug pt-0.5" title="{{ $shipping->workOrder->customer_address }}">
                                                📍 {{ $shipping->workOrder->customer_address }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Kategori --}}
                                <td class="py-4 px-3">
                                    <select name="kategori_pengiriman" x-model="kategori" @change="saveRow({{ $shipping->id }})" 
                                        class="w-full text-[11px] font-extrabold py-1.5 px-2 border-gray-200 rounded-xl bg-gray-50/50 focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:border-gray-700 dark:text-white transition-all cursor-pointer">
                                        <option value="">- Kategori -</option>
                                        <option value="Ojek Online" {{ $shipping->kategori_pengiriman == 'Ojek Online' ? 'selected' : '' }}>🛵 Ojek Online</option>
                                        <option value="Ambil Sendiri" {{ $shipping->kategori_pengiriman == 'Ambil Sendiri' ? 'selected' : '' }}>🏠 Ambil Sendiri</option>
                                        <option value="Ekspedisi" {{ $shipping->kategori_pengiriman == 'Ekspedisi' ? 'selected' : '' }}>📦 Ekspedisi</option>
                                    </select>
                                </td>

                                {{-- Ekspedisi --}}
                                <td class="py-4 px-3">
                                    <select name="ekspedisi" @change="saveRow({{ $shipping->id }})"
                                        x-bind:disabled="kategori !== 'Ekspedisi'"
                                        class="w-full text-[11px] font-extrabold py-1.5 px-2 border-gray-200 rounded-xl bg-gray-50/50 focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:border-gray-700 dark:text-white transition-all cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                                        <option value="">- Ekspedisi -</option>
                                        <option value="JNE" {{ $shipping->ekspedisi == 'JNE' ? 'selected' : '' }}>JNE</option>
                                        <option value="PCP Express" {{ $shipping->ekspedisi == 'PCP Express' ? 'selected' : '' }}>PCP Express</option>
                                        <option value="J&T Express" {{ $shipping->ekspedisi == 'J&T Express' ? 'selected' : '' }}>J&T Express</option>
                                        <option value="Sicepat" {{ $shipping->ekspedisi == 'Sicepat' ? 'selected' : '' }}>Sicepat</option>
                                        <option value="Anteraja" {{ $shipping->ekspedisi == 'Anteraja' ? 'selected' : '' }}>Anteraja</option>
                                        <option value="TIKI" {{ $shipping->ekspedisi == 'TIKI' ? 'selected' : '' }}>TIKI</option>
                                        <option value="Pos Indonesia" {{ $shipping->ekspedisi == 'Pos Indonesia' ? 'selected' : '' }}>Pos Indonesia</option>
                                        <option value="Ninja Express" {{ $shipping->ekspedisi == 'Ninja Express' ? 'selected' : '' }}>Ninja Express</option>
                                        <option value="Wahana" {{ $shipping->ekspedisi == 'Wahana' ? 'selected' : '' }}>Wahana</option>
                                        <option value="Lainnya" {{ $shipping->ekspedisi == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </td>

                                {{-- Verifikasi Toggle Switch --}}
                                <td class="py-4 px-3 text-center">
                                    <div class="flex items-center justify-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_verified" value="1" 
                                                x-model="isVerified"
                                                @change="saveRow({{ $shipping->id }})" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#22AF85]"></div>
                                        </label>
                                    </div>
                                </td>

                                {{-- PIC Gudang --}}
                                <td class="py-4 px-3">
                                    <select name="pic" @change="saveRow({{ $shipping->id }})" 
                                        class="w-full text-[11px] font-extrabold py-1.5 px-2 border-gray-200 rounded-xl bg-gray-50/50 focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:border-gray-700 dark:text-white transition-all">
                                        <option value="">- PIC -</option>
                                        @foreach($technicians as $tech)
                                            <option value="{{ $tech->name }}" {{ $shipping->pic == $tech->name ? 'selected' : '' }}>👤 {{ $tech->name }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                {{-- Tanggal Pengiriman (Kolom Tanggal Pengiriman) --}}
                                <td class="py-4 px-3">
                                    <input type="date" name="tanggal_pengiriman" 
                                        x-model="tglKirim"
                                        @change="saveRow({{ $shipping->id }})" 
                                        class="w-full text-[11px] font-extrabold py-1.5 px-2 border-gray-200 rounded-xl bg-gray-50/50 focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:border-gray-700 dark:text-white transition-all">
                                </td>

                                {{-- Resi Pengiriman --}}
                                <td class="py-4 px-3">
                                    <input type="text" name="resi_pengiriman" 
                                        x-model="resi"
                                        @change="saveRow({{ $shipping->id }})" 
                                        placeholder="Input Resi..." 
                                        class="w-full text-[11px] font-extrabold py-1.5 px-2 border-gray-200 rounded-xl bg-gray-50/30 focus:bg-white focus:ring-[#22AF85] focus:border-[#22AF85] dark:bg-gray-800 dark:border-gray-700 dark:text-white transition-all placeholder:text-gray-300 font-mono">
                                </td>

                                {{-- Status Pengiriman (Live Dynamic Marker) --}}
                                <td class="py-4 px-3 text-center">
                                    <div class="flex flex-col items-center justify-center gap-1">
                                        <div id="save-indicator-{{ $shipping->id }}" class="hidden group-indicator select-none">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-[#22AF85]/10 text-[#22AF85] animate-pulse">
                                                ✓ Saved
                                            </span>
                                        </div>

                                        <template x-if="isShipped()">
                                            <div class="flex flex-col items-center gap-0.5">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[9px] font-black uppercase tracking-wider border border-emerald-200 shadow-2xs">
                                                    🟢 SUDAH DIKIRIM
                                                </span>
                                                <span class="text-[8.5px] font-mono font-bold text-gray-500 truncate max-w-[130px]" x-text="tglKirimFormatted ? 'Tgl: ' + tglKirimFormatted : (resi ? 'Resi: ' + resi : 'Terverifikasi')"></span>
                                            </div>
                                        </template>

                                        <template x-if="!isShipped()">
                                            <div class="flex flex-col items-center gap-0.5">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[9px] font-black uppercase tracking-wider border border-blue-200 shadow-2xs">
                                                    📦 SEDANG DISIAPKAN
                                                </span>
                                                <span class="text-[8.5px] text-blue-600 font-bold">Antrian Gudang</span>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="py-16 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-full text-2xl">
                                            📦
                                        </div>
                                        <p class="text-gray-500 dark:text-gray-400 font-bold text-xs">Belum ada antrean pengiriman yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                @if($shippings->hasPages())
                <div class="px-6 py-4 bg-slate-50/50 dark:bg-gray-800/10 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-tight">Menampilkan {{ $shippings->firstItem() }}-{{ $shippings->lastItem() }} dari {{ $shippings->total() }} Data</span>
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
                saveRow(id) {
                    const tr = document.getElementById('tr-shipping-' + id);
                    const indicator = document.getElementById('save-indicator-' + id);
                    
                    if (indicator) indicator.classList.remove('hidden');

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'PUT');

                    // Gather all inputs & selects inside this table row
                    const inputs = tr.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        if (input.type === 'checkbox') {
                            if (input.checked) {
                                formData.append(input.name, input.value);
                            }
                        } else if (input.name) {
                            formData.append(input.name, input.value);
                        }
                    });

                    fetch('{{ url("shipping") }}/' + id, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const alpineData = tr._x_dataStack ? tr._x_dataStack[0] : null;
                            if (alpineData) {
                                if (data.tanggal_pengiriman) {
                                    alpineData.tglKirim = data.tanggal_pengiriman;
                                }
                                if (data.tanggal_pengiriman_formatted) {
                                    alpineData.tglKirimFormatted = data.tanggal_pengiriman_formatted;
                                }
                                alpineData.isVerified = data.is_verified;
                                if (data.resi_pengiriman !== undefined) {
                                    alpineData.resi = data.resi_pengiriman;
                                }
                            }
                            
                            setTimeout(() => {
                                if (indicator) indicator.classList.add('hidden');
                            }, 1200);
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