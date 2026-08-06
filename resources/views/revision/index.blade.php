<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Technical Revision Dashboard') }}
        </h2>
    </x-slot>

    {{-- Flatpickr Styles & Scripts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <div class="py-12" x-data="{ tab: '{{ request()->has('page_history') ? 'history' : 'active' }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- DYNAMIC FILTER BAR (PERMANENT & AUTO-SUBMIT) --}}
            <div class="mb-8 bg-white/50 dark:bg-gray-800/50 backdrop-blur-xl rounded-[2.5rem] border border-white dark:border-gray-700 shadow-xl overflow-hidden">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-500 flex items-center justify-center text-white shadow-lg shadow-indigo-200 dark:shadow-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800 dark:text-gray-100 tracking-tight">Filter Dinamis</h3>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Penyaringan data revisi otomatis</p>
                            </div>
                        </div>

                        @if(request()->anyFilled(['q', 'start_date', 'end_date', 'origin', 'pic', 'brand']))
                            <a href="{{ route('revision.index') }}" class="px-6 py-2.5 rounded-full bg-red-50 dark:bg-red-900/30 text-[10px] font-black uppercase tracking-widest text-red-500 hover:bg-red-100 dark:hover:bg-red-900/50 transition-all flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Reset Filter
                            </a>
                        @endif
                    </div>

                    <form x-ref="filterForm" action="{{ route('revision.index') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                            {{-- Search --}}
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Pencarian</label>
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="No. SPK / Nama..." 
                                       onchange="this.form.submit()"
                                       class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all placeholder:text-gray-300">
                            </div>

                            {{-- Flatpickr Range Tanggal --}}
                            <div class="space-y-2" x-data="{
                                startDate: '{{ request('start_date') }}',
                                endDate: '{{ request('end_date') }}',
                                initFlatpickr() {
                                    flatpickr($refs.dateRange, {
                                        mode: 'range',
                                        dateFormat: 'Y-m-d',
                                        defaultDate: this.startDate && this.endDate ? [this.startDate, this.endDate] : (this.startDate ? [this.startDate] : []),
                                        onChange: (selectedDates, dateStr, instance) => {
                                            if (selectedDates.length === 2) {
                                                this.startDate = instance.formatDate(selectedDates[0], 'Y-m-d');
                                                this.endDate = instance.formatDate(selectedDates[1], 'Y-m-d');
                                                this.$nextTick(() => {
                                                    $refs.filterForm.submit();
                                                });
                                            } else if (selectedDates.length === 0) {
                                                this.startDate = '';
                                                this.endDate = '';
                                                this.$nextTick(() => {
                                                    $refs.filterForm.submit();
                                                });
                                            }
                                        }
                                    });
                                }
                            }" x-init="initFlatpickr()">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Rentang Tanggal</label>
                                <input type="hidden" name="start_date" :value="startDate">
                                <input type="hidden" name="end_date" :value="endDate">
                                <input type="text" x-ref="dateRange" placeholder="Pilih Rentang Tanggal..." readonly class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer text-gray-700 dark:text-gray-200">
                            </div>

                            {{-- Sumber Revisi (origin_status) --}}
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Sumber Revisi</label>
                                <select name="origin" onchange="this.form.submit()" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all appearance-none text-gray-600 dark:text-gray-300">
                                    <option value="">Semua Sumber</option>
                                    <option value="QC" {{ request('origin') == 'QC' ? 'selected' : '' }}>Tahap QC</option>
                                    <option value="PRODUCTION" {{ request('origin') == 'PRODUCTION' ? 'selected' : '' }}>Tahap Produksi</option>
                                    <option value="SELESAI" {{ request('origin') == 'SELESAI' ? 'selected' : '' }}>Tahap Finish / Selesai</option>
                                </select>
                            </div>

                            {{-- PIC Pelapor --}}
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">PIC Pelapor</label>
                                <select name="pic" onchange="this.form.submit()" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all appearance-none text-gray-600 dark:text-gray-300">
                                    <option value="">Semua PIC</option>
                                    @foreach($reporters as $user)
                                        <option value="{{ $user->id }}" {{ request('pic') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Brand --}}
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Brand Sepatu</label>
                                <select name="brand" onchange="this.form.submit()" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all appearance-none text-gray-600 dark:text-gray-300">
                                    <option value="">Semua Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SUMMARY METRIC CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1: QC Revisions -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-xl flex items-center gap-4 transition-all hover:scale-[1.02]">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-2xl">
                        🔍
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Revisi QC</p>
                        <p class="text-2xl font-black text-gray-800 dark:text-gray-100 tracking-tight">{{ $qcCount }}</p>
                    </div>
                </div>

                <!-- Card 2: Production Revisions -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-xl flex items-center gap-4 transition-all hover:scale-[1.02]">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center text-2xl">
                        ⚙️
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Revisi Production</p>
                        <p class="text-2xl font-black text-gray-800 dark:text-gray-100 tracking-tight">{{ $prodCount }}</p>
                    </div>
                </div>

                <!-- Card 3: Selesai Revisions -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-xl flex items-center gap-4 transition-all hover:scale-[1.02]">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-2xl">
                        ✅
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Revisi Selesai</p>
                        <p class="text-2xl font-black text-gray-800 dark:text-gray-100 tracking-tight">{{ $selesaiCount }}</p>
                    </div>
                </div>

                <!-- Card 4: Total Revisions -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-xl flex items-center gap-4 transition-all hover:scale-[1.02]">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-2xl">
                        📊
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Total Revisi Aktif</p>
                        <p class="text-2xl font-black text-gray-800 dark:text-gray-100 tracking-tight">{{ $totalActiveCount }}</p>
                    </div>
                </div>
            </div>

            {{-- TAB NAVIGATION --}}
            <div class="flex items-center gap-2 mb-8 bg-gray-100/50 dark:bg-gray-900/50 p-1.5 rounded-2xl w-fit border border-gray-100 dark:border-gray-800 backdrop-blur-sm">
                <button @click="tab = 'active'" 
                        :class="tab === 'active' ? 'bg-white dark:bg-gray-800 text-indigo-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-6 py-2.5 rounded-xl font-black uppercase text-[10px] tracking-widest transition-all flex items-center gap-3">
                    Antrean Aktif
                    <span :class="tab === 'active' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-200 text-gray-400'"
                          class="px-2 py-0.5 rounded-md text-[9px] font-black">
                        {{ $active->total() }}
                    </span>
                </button>
                <button @click="tab = 'history'" 
                        :class="tab === 'history' ? 'bg-white dark:bg-gray-800 text-green-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-6 py-2.5 rounded-xl font-black uppercase text-[10px] tracking-widest transition-all flex items-center gap-3">
                    Riwayat Selesai
                    <span :class="tab === 'history' ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-400'"
                          class="px-2 py-0.5 rounded-md text-[9px] font-black">
                        {{ $history->total() }}
                    </span>
                </button>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-[2rem] overflow-hidden border border-gray-100 dark:border-gray-700">
                
                {{-- TAB 1: ACTIVE REVISIONS --}}
                <div x-show="tab === 'active'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="overflow-x-auto overflow-y-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-50 dark:border-gray-700">
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] w-12">No</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">SPK & Customer</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Unit Details</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Sumber</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Masalah / Revisi</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Pelapor</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Waktu</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            @forelse($active as $rev)
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition-colors group">
                                    {{-- No --}}
                                    <td class="px-8 py-6">
                                        <span class="text-xs font-black text-gray-400 dark:text-gray-600">{{ $loop->iteration }}</span>
                                    </td>

                                    {{-- SPK & Customer --}}
                                    <td class="px-6 py-6">
                                        <div class="flex flex-col">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 border border-red-100 dark:border-red-900/30 w-fit mb-2 uppercase tracking-widest">
                                                {{ $rev->workOrder->spk_number }}
                                            </span>
                                            <span class="text-base font-black text-gray-800 dark:text-gray-200 tracking-tight">
                                                {{ $rev->workOrder->customer_name }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Unit Details --}}
                                    <td class="px-6 py-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/10 rounded-xl flex items-center justify-center text-lg border border-orange-100 dark:border-orange-900/20">
                                                👟
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-700 dark:text-gray-300 line-clamp-1">{{ $rev->workOrder->shoe_brand }}</p>
                                                <p class="text-[10px] font-medium text-gray-400 line-clamp-1">{{ $rev->workOrder->shoe_color }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Sumber --}}
                                    <td class="px-6 py-6">
                                        @php
                                            $origin = strtoupper($rev->origin_status ?: 'SELESAI');
                                            $badgeClass = match($origin) {
                                                'QC' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400 border-amber-100 dark:border-amber-900/30',
                                                'PRODUCTION' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 border-blue-100 dark:border-blue-900/30',
                                                default => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/30'
                                            };
                                            $label = match($origin) {
                                                'QC' => 'QC',
                                                'PRODUCTION' => 'PRODUCTION',
                                                default => 'SELESAI'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black border uppercase tracking-widest {{ $badgeClass }}">
                                            {{ $label }}
                                        </span>
                                    </td>

                                    {{-- Masalah / Revisi --}}
                                    <td class="px-6 py-6 max-w-xs">
                                        <div class="flex flex-col gap-2">
                                            <div class="bg-red-50/50 dark:bg-red-900/5 rounded-xl p-3 border border-red-50 dark:border-red-900/10">
                                                <p class="text-xs text-gray-600 dark:text-gray-400 italic line-clamp-2">"{{ $rev->description }}"</p>
                                            </div>
                                            @if($rev->photo_path)
                                                <a href="{{ $rev->photo_url }}" target="_blank" class="flex items-center gap-1.5 text-[10px] font-black text-indigo-500 uppercase tracking-widest hover:text-indigo-600 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    Lampiran Foto
                                                </a>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Pelapor --}}
                                    <td class="px-6 py-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center font-bold text-indigo-600 text-xs border border-indigo-100 dark:border-indigo-900/30">
                                                {{ substr($rev->creator->name ?? '?', 0, 1) }}
                                            </div>
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-400 tracking-tight">{{ explode(' ', $rev->creator->name ?? 'System')[0] }}</span>
                                        </div>
                                    </td>

                                    {{-- Waktu --}}
                                    <td class="px-6 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 tracking-tight">
                                                {{ $rev->created_at->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                {{ $rev->created_at->format('H:i') }} ({{ $rev->created_at->diffForHumans() }})
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end gap-3 transition-opacity">
                                            <a href="{{ route('revision.show', $rev->id) }}" 
                                               class="p-2 text-gray-400 hover:text-indigo-500 transition-colors" title="Detail Masalah">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            
                                            @if(auth()->user()->isAdmin() || $rev->created_by === auth()->id())
                                                <form action="{{ route('revision.destroy', $rev->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="delete-confirm p-2 text-gray-300 hover:text-red-500 transition-colors"
                                                            data-title="Hapus Ajuan Revisi?"
                                                            data-text="SPK ini akan dikembalikan ke status SELESAI jika revisi dibatalkan."
                                                            title="Batalkan/Hapus Revisi">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('revision.complete', $rev->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-green-100 dark:shadow-none transition-all hover:scale-105">
                                                    Selesai
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-20 text-center">
                                        <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900/50 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl">
                                            🎉
                                        </div>
                                        <h3 class="text-xl font-black text-gray-800 dark:text-gray-100 tracking-tight">Semua Unit Aman!</h3>
                                        <p class="text-gray-400 text-sm">Tidak ada unit yang sedang dalam komplain atau revisi teknik.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($active->hasPages())
                        <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-50 dark:border-gray-700">
                            {{ $active->links() }}
                        </div>
                    @endif
                </div>

                {{-- TAB 2: REVISION HISTORY --}}
                <div x-show="tab === 'history'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="overflow-x-auto overflow-y-hidden" x-cloak>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-50 dark:border-gray-700">
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] w-12">No</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">SPK & Unit</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Sumber</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Masalah</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Waktu Ajuan</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Waktu Selesai</th>
                                <th class="px-6 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Durasi</th>
                                <th class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            @forelse($history as $rev)
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-900/30 transition-colors">
                                    <td class="px-8 py-6">
                                        <span class="text-xs font-black text-gray-400 dark:text-gray-600">{{ $loop->iteration }}</span>
                                    </td>

                                    <td class="px-6 py-6 border-l-4 border-green-500/20">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $rev->workOrder->spk_number }}</p>
                                        <p class="text-sm font-black text-gray-700 dark:text-gray-300 tracking-tight">{{ $rev->workOrder->shoe_brand }}</p>
                                        <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                            <span class="whitespace-nowrap text-[9px] font-bold text-gray-600 bg-gray-100/80 border border-gray-200/60 dark:bg-gray-800 dark:text-gray-400 px-1.5 py-0.5 rounded inline-flex items-center gap-1">
                                                👤 {{ $rev->workOrder->customer_name }}
                                            </span>
                                            <span class="whitespace-nowrap text-[9px] font-mono font-bold text-gray-600 bg-gray-100/80 border border-gray-200/60 dark:bg-gray-800 dark:text-gray-400 px-1.5 py-0.5 rounded inline-flex items-center gap-1">
                                                📞 {{ $rev->workOrder->customer_phone }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Sumber --}}
                                    <td class="px-6 py-6">
                                        @php
                                            $origin = strtoupper($rev->origin_status ?: 'SELESAI');
                                            $badgeClass = match($origin) {
                                                'QC' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400 border-amber-100 dark:border-amber-900/30',
                                                'PRODUCTION' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 border-blue-100 dark:border-blue-900/30',
                                                default => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/30'
                                            };
                                            $label = match($origin) {
                                                'QC' => 'QC',
                                                'PRODUCTION' => 'PRODUCTION',
                                                default => 'SELESAI'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black border uppercase tracking-widest {{ $badgeClass }}">
                                            {{ $label }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-6 max-w-xs">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 italic line-clamp-1">"{{ $rev->description }}"</p>
                                    </td>

                                    <td class="px-6 py-6">
                                         <p class="text-xs font-bold text-gray-600 dark:text-gray-400 tracking-tight">{{ $rev->created_at->format('d M Y') }}</p>
                                         <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $rev->created_at->format('H:i') }}</p>
                                    </td>

                                    <td class="px-6 py-6">
                                         <p class="text-xs font-bold text-green-600 dark:text-green-400 tracking-tight">{{ $rev->finished_at->format('d M Y') }}</p>
                                         <p class="text-[10px] font-black text-green-400 uppercase tracking-widest">{{ $rev->finished_at->format('H:i') }}</p>
                                    </td>

                                    <td class="px-6 py-6">
                                        <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest border border-gray-200 dark:border-gray-600">
                                            {{ $rev->created_at->diffForHumans($rev->finished_at, true) }}
                                        </span>
                                    </td>

                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('revision.show', $rev->id) }}" 
                                               class="inline-flex items-center gap-2 bg-gray-50 dark:bg-gray-900 px-4 py-2 rounded-xl text-[10px] font-black text-gray-400 uppercase tracking-widest border border-gray-100 dark:border-gray-800 hover:text-indigo-500 transition-colors">
                                                Detail
                                            </a>
                                            
                                            @if(auth()->user()->isAdmin())
                                                <form action="{{ route('revision.destroy', $rev->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="delete-confirm p-2 text-gray-300 hover:text-red-500 transition-colors"
                                                            data-title="Hapus Riwayat Revisi?"
                                                            data-text="Data ini akan dihapus permanen dari riwayat."
                                                            title="Hapus Riwayat">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-20 text-center text-gray-400">Belum ada riwayat revisi yang selesai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($history->hasPages())
                        <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-50 dark:border-gray-700">
                            {{ $history->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
