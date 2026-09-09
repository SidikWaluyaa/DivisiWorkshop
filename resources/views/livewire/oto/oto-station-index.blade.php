<div>
    {{-- Full-Screen Branded Logo Loader Overlay --}}
    <x-branded-loader text="Memuat Data Stasiun OTO..." :fullscreen="true" />

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <span>Workshop</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 dark:text-white font-bold">Stasiun Khusus OTO</span>
                </nav>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold text-xs rounded-full border border-amber-500/20">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Modul Mandiri OTO</span>
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto pb-12 px-4 sm:px-6 lg:px-8 pt-6">

        {{-- Hero Header Card Glassmorphism --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 p-6 md:p-8 text-white shadow-xl shadow-amber-900/15">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -top-10 w-48 h-48 rounded-full bg-amber-400/20 blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[11px] font-black uppercase tracking-wider text-amber-100 border border-white/20">
                        <span>🔥 Dedicated Station</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                        <span>One Time Offer Hub</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white flex items-center gap-2.5">
                        <span>Stasiun Pengerjaan OTO</span>
                    </h1>
                    <p class="text-sm text-amber-100 max-w-2xl leading-relaxed">
                        Kelola pengerjaan fisik paket layanan tambahan (One Time Offer) yang disetujui pelanggan secara mandiri dan terpisah dari antrean produksi reguler.
                    </p>
                </div>

                {{-- Fast Stats Pill in Hero --}}
                <div class="flex items-center gap-3 bg-black/20 backdrop-blur-md p-3 rounded-2xl border border-white/15">
                    <div class="text-center px-3 border-r border-white/15">
                        <span class="block text-[10px] uppercase tracking-wider text-amber-200 font-bold">Total Aktif</span>
                        <span class="text-xl font-black text-white">{{ $counts['total_active'] }}</span>
                    </div>
                    <div class="text-center px-3">
                        <span class="block text-[10px] uppercase tracking-wider text-amber-200 font-bold">Potensi Omset</span>
                        <span class="text-sm font-black text-emerald-300">Rp {{ number_format($counts['potential_revenue'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4 Metric KPI Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- 1. Antrean OTO --}}
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-150 dark:border-gray-700 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 block mb-1">Antrean Belum Mulai</span>
                    <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $counts['antrean'] }}</span>
                    <span class="text-[10px] text-gray-400 block mt-0.5">Menunggu pengerjaan</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-xl text-amber-600 border border-amber-200 dark:border-amber-800">
                    ⏳
                </div>
            </div>

            {{-- 2. Sedang Dikerjakan --}}
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-150 dark:border-gray-700 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 block mb-1">Sedang Dikerjakan</span>
                    <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ $counts['in_progress'] }}</span>
                    <span class="text-[10px] text-blue-500 font-semibold block mt-0.5">Teknisi aktif</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center text-xl text-blue-600 border border-blue-200 dark:border-blue-800">
                    🏃
                </div>
            </div>

            {{-- 3. Selesai Hari Ini --}}
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-150 dark:border-gray-700 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 block mb-1">Selesai Hari Ini</span>
                    <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $counts['completed_today'] }}</span>
                    <span class="text-[10px] text-gray-400 block mt-0.5">Total selesai: {{ $counts['completed'] }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-xl text-emerald-600 border border-emerald-200 dark:border-emerald-800">
                    ✅
                </div>
            </div>

            {{-- 4. Status Integrasi --}}
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-150 dark:border-gray-700 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 block mb-1">Integrasi Sistem</span>
                    <span class="text-sm font-black text-purple-600 dark:text-purple-400">Terisolasi</span>
                    <span class="text-[10px] text-gray-400 block mt-0.5">Bebas bentrok Produksi</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center text-xl text-purple-600 border border-purple-200 dark:border-purple-800">
                    🛡️
                </div>
            </div>
        </div>

        {{-- Tab Navigation & Controls --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-gray-700 pb-3">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                {{-- Tab 1: Antrean Belum Mulai --}}
                <button wire:click="$set('activeTab', 'antrean')" 
                        class="px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition-all cursor-pointer {{ $activeTab === 'antrean' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                    <span>⏳ Antrean Pengerjaan</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'antrean' ? 'bg-white text-amber-700' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        {{ $counts['antrean'] }}
                    </span>
                </button>

                {{-- Tab 2: Sedang Dikerjakan --}}
                <button wire:click="$set('activeTab', 'in_progress')" 
                        class="px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition-all cursor-pointer {{ $activeTab === 'in_progress' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                    <span>🏃 Sedang Dikerjakan</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'in_progress' ? 'bg-white text-blue-700' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        {{ $counts['in_progress'] }}
                    </span>
                </button>

                {{-- Tab 3: Riwayat Selesai --}}
                <button wire:click="$set('activeTab', 'completed')" 
                        class="px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition-all cursor-pointer {{ $activeTab === 'completed' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                    <span>✅ Riwayat Selesai</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'completed' ? 'bg-white text-emerald-700' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        {{ $counts['completed'] }}
                    </span>
                </button>
            </div>

            {{-- Bulk Actions --}}
            @if(count($selectedItems) > 0 && $activeTab !== 'completed')
                <div class="flex items-center gap-2">
                    <button wire:click="bulkComplete" 
                            wire:confirm="Selesaikan {{ count($selectedItems) }} paket OTO terpilih sekaligus?"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-1.5 cursor-pointer">
                        <span>✨</span>
                        <span>Selesaikan Terpilih ({{ count($selectedItems) }})</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- Filters & Search Bar --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs flex flex-col md:flex-row items-center gap-3">
            {{-- Search Bar --}}
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       class="block w-full pl-10 pr-4 py-2.5 bg-gray-50/50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-semibold text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-amber-500 transition-all placeholder:text-gray-400" 
                       placeholder="Cari No SPK, Nama Pelanggan, Merk Sepatu, atau Layanan OTO...">
            </div>

            {{-- Filter Petugas Teknisi --}}
            <div class="w-full md:w-52">
                <select wire:model.live="technicianFilter" class="w-full py-2.5 px-3 bg-gray-50/50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-amber-500 cursor-pointer">
                    <option value="all">👤 Semua Petugas</option>
                    @foreach($techs['all'] ?? [] as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sort Order --}}
            <div class="w-full md:w-40">
                <select wire:model.live="sort" class="w-full py-2.5 px-3 bg-gray-50/50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-amber-500 cursor-pointer">
                    <option value="asc">📅 Terlama</option>
                    <option value="desc">🆕 Terbaru</option>
                </select>
            </div>

            {{-- Reset Button --}}
            <button wire:click="$set('search', ''); $set('technicianFilter', 'all'); $set('serviceFilter', 'all'); $set('sort', 'asc');" 
                    class="p-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-600 dark:text-gray-300 rounded-xl transition-all active:scale-95 flex items-center justify-center cursor-pointer"
                    title="Reset Filter">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>

        {{-- Table / List Section --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                    <thead class="bg-gray-50/80 dark:bg-gray-700 text-gray-500 dark:text-gray-300 uppercase text-xs font-black tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5 w-20">
                                <div class="flex items-center gap-2">
                                    @if($activeTab !== 'completed')
                                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-amber-600 rounded focus:ring-amber-500 cursor-pointer">
                                    @endif
                                    <span>No</span>
                                </div>
                            </th>
                            <th class="px-6 py-3.5">SPK & Prioritas</th>
                            <th class="px-6 py-3.5">Pelanggan & Sepatu</th>
                            <th class="px-6 py-3.5">Paket OTO & Nilai</th>
                            <th class="px-6 py-3.5">Progress Stasiun & Teknisi</th>
                            <th class="px-6 py-3.5 text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    
                    @forelse($otos as $oto)
                        <x-oto-card 
                            wire:key="card-oto-{{ $oto->id }}"
                            :oto="$oto"
                            :techs="$techs"
                            :loopIteration="($otos->currentPage() - 1) * $otos->perPage() + $loop->iteration"
                            :showCheckbox="true"
                            :isCompletedTab="$activeTab === 'completed'"
                        />
                    @empty
                        <tbody class="divide-y divide-gray-150 dark:divide-gray-750">
                            <tr>
                                <td colspan="6" class="p-16 text-center text-gray-400 dark:text-gray-500">
                                    <span class="text-4xl block mb-2">✨</span>
                                    <p class="font-bold text-sm">Tidak ada pengerjaan OTO pada tab ini saat ini.</p>
                                    <p class="text-xs text-gray-400 mt-1">Semua penawaran OTO yang disetujui pelanggan akan otomatis muncul di sini.</p>
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
                </table>
            </div>

            @if($otos instanceof \Illuminate\Pagination\LengthAwarePaginator && $otos->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                    {{ $otos->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
