<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC]">
        {{-- Elite Premium Header --}}
        <div class="bg-white/95 shadow-sm border-b border-gray-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 sm:py-6">
                <div class="flex flex-col gap-5 sm:gap-6">
                    {{-- Top Row: Title + Create Button --}}
                    <div class="flex items-center justify-between">
                        {{-- Left Title --}}
                        <div class="flex items-center gap-3.5 sm:gap-5">
                            <div class="p-2.5 sm:p-3.5 bg-gradient-to-br from-[#1B8A68] to-[#125c46] rounded-2xl shadow-[0_10px_25px_-8px_rgba(27,138,104,0.5)] text-white">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 sm:gap-3 mb-1">
                                    <span class="text-[9px] sm:text-[10px] font-black bg-emerald-50 text-[#1B8A68] px-2 py-0.5 rounded-md uppercase tracking-wider border border-emerald-100/80">FINANCE RADAR</span>
                                    <h1 class="text-xl sm:text-3xl font-black text-gray-900 tracking-tight leading-none uppercase">Sentral Invoice</h1>
                                </div>
                                <p class="text-gray-400 text-[10px] sm:text-xs font-semibold tracking-wide hidden sm:block">Manajemen tagihan gabungan & pelacakan status pembayaran terintegrasi</p>
                            </div>
                        </div>

                        {{-- Create Button --}}
                        <a href="{{ route('finance.invoices.create') }}" class="group inline-flex items-center gap-2.5 sm:gap-3 px-4 sm:px-6 py-2.5 sm:py-3.5 bg-gradient-to-r from-[#FFC232] to-[#ffb310] hover:from-[#ffd05b] hover:to-[#ffbd24] text-gray-900 rounded-2xl font-black text-[11px] sm:text-xs uppercase tracking-wider shadow-lg shadow-amber-200/50 transition-all duration-200 hover:-translate-y-0.5 active:scale-95">
                            <span class="hidden sm:inline">Buat Invoice Baru</span>
                            <span class="sm:hidden">Buat Invoice</span>
                            <div class="w-5 h-5 rounded-full bg-black/10 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                        </a>
                    </div>

                    {{-- Bottom Row: Filters --}}
                    <form action="{{ route('finance.invoices.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3">
                        {{-- Gateway Filter --}}
                        <div class="relative">
                            <select name="gateway" onchange="this.form.submit()" class="w-full sm:w-auto pl-4 pr-9 py-2.5 sm:py-3 bg-gray-50 hover:bg-gray-100/80 border border-gray-200 rounded-xl focus:bg-white focus:border-[#1B8A68] focus:ring-2 focus:ring-[#1B8A68]/20 text-xs sm:text-sm font-bold text-gray-700 transition-all cursor-pointer appearance-none outline-none">
                                <option value="">Semua Gateway</option>
                                @foreach($gateways as $gw)
                                    <option value="{{ $gw }}" {{ request('gateway') === $gw ? 'selected' : '' }}>Gateway: {{ $gw }}</option>
                                @endforeach
                            </select>
                            <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        {{-- Payment Status Filter --}}
                        <div class="relative">
                            <select name="payment_status" onchange="this.form.submit()" class="w-full sm:w-auto pl-4 pr-9 py-2.5 sm:py-3 bg-gray-50 hover:bg-gray-100/80 border border-gray-200 rounded-xl focus:bg-white focus:border-[#1B8A68] focus:ring-2 focus:ring-[#1B8A68]/20 text-xs sm:text-sm font-bold text-gray-700 transition-all cursor-pointer appearance-none outline-none">
                                <option value="">Semua Status Pembayaran</option>
                                <option value="Belum Bayar" {{ request('payment_status') === 'Belum Bayar' ? 'selected' : '' }}>⚪ Belum Bayar</option>
                                <option value="DP/Cicil" {{ request('payment_status') === 'DP/Cicil' ? 'selected' : '' }}>🟡 DP / Cicil</option>
                                <option value="Lunas" {{ request('payment_status') === 'Lunas' ? 'selected' : '' }}>🟢 Lunas</option>
                                <option value="Batal" {{ request('payment_status') === 'Batal' ? 'selected' : '' }}>🔴 Batal</option>
                            </select>
                            <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        {{-- Search Input --}}
                        <div class="relative group/search flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari nomor invoice, nama pelanggan, no telepon, kode unik..." 
                                   class="w-full pl-11 pr-10 py-2.5 sm:py-3 bg-gray-50 hover:bg-gray-100/80 border border-gray-200 rounded-xl focus:bg-white focus:border-[#1B8A68] focus:ring-2 focus:ring-[#1B8A68]/20 text-xs sm:text-sm font-semibold text-gray-800 placeholder-gray-400 transition-all outline-none">
                            <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 group-focus-within/search:text-[#1B8A68] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            @if(request('search') || request('gateway') || request('payment_status'))
                                <a href="{{ route('finance.invoices.index') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 text-xs font-bold px-1.5 py-0.5 rounded transition-colors" title="Reset Filter">
                                    ✕
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Main Table Section Wrapped in Alpine State --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8" 
             x-data="{
                expandedRows: {},
                copiedId: null,
                toggleRow(id) {
                    this.expandedRows[id] = !this.expandedRows[id];
                },
                isExpanded(id) {
                    return !!this.expandedRows[id];
                },
                expandAll(ids) {
                    ids.forEach(id => this.expandedRows[id] = true);
                },
                collapseAll() {
                    this.expandedRows = {};
                },
                copyText(text, label) {
                    if (!text) return;
                    navigator.clipboard.writeText(text).then(() => {
                        this.copiedId = label;
                        setTimeout(() => { if (this.copiedId === label) this.copiedId = null; }, 2000);
                    });
                }
             }">

            {{-- Toolbar: Summary & Expand/Collapse Controls --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 px-1">
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#1B8A68]"></span>
                    <p class="text-xs font-bold text-gray-600">
                        Total <span class="font-extrabold text-gray-900">{{ $invoices->total() }}</span> invoice tercatat
                        @if($invoices->total() > 0)
                            <span class="text-gray-400 font-normal">(menampilkan baris {{ $invoices->firstItem() }} - {{ $invoices->lastItem() }})</span>
                        @endif
                    </p>
                </div>

                {{-- Bulk Expand / Collapse Buttons --}}
                <div class="hidden lg:flex items-center gap-2">
                    <button type="button" 
                            @click="expandAll({{ json_encode($invoices->pluck('id')) }})" 
                            class="px-3 py-1.5 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 hover:border-gray-300">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path></svg>
                        Buka Semua Rincian
                    </button>
                    <button type="button" 
                            @click="collapseAll()" 
                            class="px-3 py-1.5 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 hover:border-gray-300">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path></svg>
                        Tutup Semua
                    </button>
                </div>
            </div>

            {{-- DESKTOP COLLAPSIBLE TABLE VIEW (hidden on mobile/tablet) --}}
            <div class="hidden lg:block bg-white rounded-2xl shadow-xl border border-gray-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/90 border-b border-gray-200 text-[11px] font-black text-gray-500 uppercase tracking-wider">
                                <th class="w-12 px-3 py-4 text-center">#</th>
                                <th class="px-5 py-4">No. Invoice & Tanggal</th>
                                <th class="px-5 py-4">Pelanggan</th>
                                <th class="px-5 py-4">SPK & CS</th>
                                <th class="px-4 py-4 text-center">Status SPK</th>
                                <th class="px-4 py-4 text-center">Status Bayar</th>
                                <th class="px-5 py-4 text-right">Total Tagihan</th>
                                <th class="px-4 py-4 text-center">Target Selesai</th>
                                <th class="px-5 py-4 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($invoices as $invoice)
                                {{-- PARENT ROW --}}
                                <tr class="transition-colors duration-150 group" 
                                    :class="isExpanded({{ $invoice->id }}) ? 'bg-emerald-50/30' : 'hover:bg-slate-50/80'">
                                    
                                    {{-- Col 1: Chevron Expand Toggle --}}
                                    <td class="px-3 py-4 text-center">
                                        <button type="button" 
                                                @click="toggleRow({{ $invoice->id }})" 
                                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200"
                                                :class="isExpanded({{ $invoice->id }}) ? 'bg-[#1B8A68] text-white rotate-180 shadow-md shadow-emerald-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-800'"
                                                title="Klik untuk melihat/menutup rincian">
                                            <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </td>

                                    {{-- Col 2: No Invoice & Tanggal --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors"
                                                 :class="isExpanded({{ $invoice->id }}) ? 'bg-emerald-100 text-[#1B8A68]' : 'bg-gray-100 text-gray-500 group-hover:bg-emerald-50 group-hover:text-[#1B8A68]'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <a href="{{ route('finance.invoices.show', $invoice->id) }}" 
                                                   class="font-extrabold text-sm text-gray-900 hover:text-[#1B8A68] transition-colors font-mono tracking-tight block">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                                <span class="text-[11px] text-gray-400 font-medium flex items-center gap-1">
                                                    {{ $invoice->created_at->format('d M Y') }} • {{ $invoice->created_at->format('H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Col 3: Customer --}}
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-gray-900 text-xs sm:text-sm line-clamp-1">
                                            {{ $invoice->customer?->name ?? 'Data Terhapus' }}
                                        </div>
                                        @if($invoice->customer?->phone)
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $invoice->customer->phone)) }}" 
                                               target="_blank" 
                                               class="text-[11px] text-gray-500 hover:text-[#1B8A68] font-mono transition-colors inline-flex items-center gap-1 mt-0.5">
                                                <span>{{ $invoice->customer->phone }}</span>
                                                <svg class="w-3 h-3 text-emerald-500 opacity-70" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-[11px] text-gray-400 italic">-</span>
                                        @endif
                                    </td>

                                    {{-- Col 4: Rincian SPK & CS Gateway --}}
                                    <td class="px-5 py-4">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-800 rounded-lg text-xs font-bold mb-1">
                                            <span>{{ $invoice->workOrders->count() }} Pasang</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($invoice->workOrders->unique('cs_code') as $order)
                                                @if($order->cs_code)
                                                    <span class="text-[10px] font-black px-1.5 py-0.5 bg-emerald-50 text-[#1B8A68] rounded border border-emerald-100">
                                                        {{ $order->cs_code }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>

                                    {{-- Col 5: Status SPK --}}
                                    <td class="px-4 py-4 text-center">
                                        @php
                                            $spkStyle = match($invoice->spk_status) {
                                                'SELESAI' => 'bg-emerald-50 text-[#1B8A68] border-emerald-200',
                                                'BATAL' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                'BELUM SELESAI' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                default => 'bg-gray-100 text-gray-600 border-gray-200'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black tracking-wide border {{ $spkStyle }}">
                                            {{ $invoice->spk_status }}
                                        </span>
                                    </td>

                                    {{-- Col 6: Status Pembayaran --}}
                                    <td class="px-4 py-4 text-center">
                                        @php
                                            $statusBadge = match($invoice->status) {
                                                'Lunas' => 'bg-emerald-50 text-[#1B8A68] border-emerald-200',
                                                'DP/Cicil' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'Batal', 'BATAL' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                default => 'bg-slate-100 text-slate-600 border-slate-200'
                                            };
                                            $dotColor = match($invoice->status) {
                                                'Lunas' => 'bg-[#1B8A68]',
                                                'DP/Cicil' => 'bg-amber-500 animate-pulse',
                                                'Batal', 'BATAL' => 'bg-rose-500',
                                                default => 'bg-slate-400'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold tracking-wide border {{ $statusBadge }}">
                                            <span class="w-2 h-2 rounded-full {{ $dotColor }}"></span>
                                            {{ $invoice->status }}
                                        </span>
                                    </td>

                                    {{-- Col 7: Total Tagihan --}}
                                    <td class="px-5 py-4 text-right">
                                        <div class="text-sm sm:text-base font-black text-gray-900 tabular-nums">
                                            Rp {{ number_format($invoice->total_amount + $invoice->shipping_cost, 0, ',', '.') }}
                                        </div>
                                        @if($invoice->paid_amount > 0)
                                            <div class="text-[10px] text-[#1B8A68] font-bold mt-0.5">
                                                Terbayar: Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}
                                            </div>
                                        @endif
                                        @if($invoice->status !== 'Lunas' && $invoice->remaining_balance > 0)
                                            <div class="text-[10px] text-rose-600 font-semibold mt-0.5">
                                                Sisa: Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Col 8: Target Estimasi --}}
                                    <td class="px-4 py-4 text-center">
                                        @if($invoice->estimasi_selesai)
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-xs font-black text-gray-800">
                                                    {{ \Carbon\Carbon::parse($invoice->estimasi_selesai)->format('d M Y') }}
                                                </span>
                                                <span class="text-[9px] font-bold text-gray-400">Target</span>
                                            </div>
                                        @else
                                            <span class="text-[11px] font-medium text-gray-400 italic">Belum Set</span>
                                        @endif
                                    </td>

                                    {{-- Col 9: Aksi Cepat --}}
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            {{-- Tombol Cetak Nota --}}
                                            <a href="{{ url('/api/invoice_share_grouped.php?token=' . urlencode($invoice->invoice_number) . '&type=' . ($invoice->status === 'Belum Bayar' ? 'BL' : 'L')) }}" 
                                               target="_blank" 
                                               class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-emerald-50 text-gray-500 hover:text-[#1B8A68] border border-gray-200 hover:border-emerald-200 transition-all flex items-center justify-center" 
                                               title="Cetak Nota Gabungan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>

                                            {{-- Tombol Buka Detail --}}
                                            <a href="{{ route('finance.invoices.show', $invoice->id) }}" 
                                               class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-blue-50 text-gray-500 hover:text-blue-600 border border-gray-200 hover:border-blue-200 transition-all flex items-center justify-center" 
                                               title="Buka Halaman Detail Invoice">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            </a>

                                            {{-- Toggle Rincian --}}
                                            <button type="button" 
                                                    @click="toggleRow({{ $invoice->id }})" 
                                                    class="px-2.5 py-1.5 rounded-lg text-xs font-bold border transition-all flex items-center gap-1"
                                                    :class="isExpanded({{ $invoice->id }}) ? 'bg-[#1B8A68] text-white border-[#1B8A68]' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100'">
                                                <span x-text="isExpanded({{ $invoice->id }}) ? 'Tutup' : 'Rincian'"></span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- COLLAPSIBLE DETAIL ROW --}}
                                <tr x-show="isExpanded({{ $invoice->id }})" 
                                    x-cloak 
                                    x-collapse 
                                    class="bg-slate-50/70 border-b border-gray-200">
                                    <td colspan="9" class="p-4 sm:p-6">
                                        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-200 shadow-sm space-y-6" 
                                             x-data="{ activeTab: 'spk' }">
                                            
                                            {{-- Tab Header Inside Collapsible --}}
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <button type="button" 
                                                            @click="activeTab = 'spk'" 
                                                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2"
                                                            :class="activeTab === 'spk' ? 'bg-[#1B8A68] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                        Daftar SPK / Sepatu ({{ $invoice->workOrders->count() }})
                                                    </button>
                                                    <button type="button" 
                                                            @click="activeTab = 'finance'" 
                                                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2"
                                                            :class="activeTab === 'finance' ? 'bg-[#1B8A68] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Breakdown Finansial & Tagihan
                                                    </button>
                                                </div>

                                                {{-- Quick Direct Link to Full Show --}}
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('finance.invoices.show', $invoice->id) }}" 
                                                       class="text-xs font-bold text-[#1B8A68] hover:text-[#125c46] inline-flex items-center gap-1 group">
                                                        <span>Kelola Pembayaran & Edit Invoice</span>
                                                        <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                                    </a>
                                                </div>
                                            </div>

                                            {{-- TAB 1: DAFTAR SPK & SEPATU --}}
                                            <div x-show="activeTab === 'spk'" class="space-y-3">
                                                <div class="overflow-x-auto rounded-xl border border-gray-100">
                                                    <table class="w-full text-left border-collapse text-xs">
                                                        <thead>
                                                            <tr class="bg-gray-50/80 text-[10px] font-black text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                                                <th class="px-4 py-2.5">No. SPK</th>
                                                                <th class="px-4 py-2.5">CS Gateway</th>
                                                                <th class="px-4 py-2.5">Sepatu & Model</th>
                                                                <th class="px-4 py-2.5">Layanan / Treatment</th>
                                                                <th class="px-4 py-2.5 text-center">Status Pengerjaan</th>
                                                                <th class="px-4 py-2.5 text-right">Nilai SPK</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-50">
                                                            @forelse($invoice->workOrders as $wo)
                                                                <tr class="hover:bg-slate-50/60 transition-colors">
                                                                    <td class="px-4 py-3 font-mono font-bold text-gray-900">
                                                                        <div class="flex items-center gap-2">
                                                                            <a href="{{ route('admin.orders.show', $wo->id) }}" 
                                                                               target="_blank" 
                                                                               class="hover:text-[#1B8A68] hover:underline inline-flex items-center gap-1.5 text-gray-900 group/spk" 
                                                                               title="Buka Detail Order / SPK di Admin (/admin/orders/show)">
                                                                                <span>{{ $wo->spk_number }}</span>
                                                                                <svg class="w-3.5 h-3.5 text-gray-400 group-hover/spk:text-[#1B8A68] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                                            </a>
                                                                            @if(\Illuminate\Support\Facades\Route::has('reception.print-spk'))
                                                                                <a href="{{ route('reception.print-spk', $wo->id) }}" 
                                                                                   target="_blank" 
                                                                                   class="text-gray-400 hover:text-[#1B8A68] p-1 rounded-md hover:bg-emerald-50 transition-colors" 
                                                                                   title="Cetak Lembar SPK">
                                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-4 py-3">
                                                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-[#1B8A68] border border-emerald-100">
                                                                            {{ $wo->cs_code ?? '-' }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-4 py-3">
                                                                        <div class="font-bold text-gray-800">{{ $wo->shoe_brand ?? '-' }}</div>
                                                                        <div class="text-[10px] text-gray-400">{{ $wo->shoe_type ?? '-' }}</div>
                                                                    </td>
                                                                    <td class="px-4 py-3">
                                                                        @if($wo->workOrderServices && $wo->workOrderServices->isNotEmpty())
                                                                            <div class="flex flex-wrap gap-1 max-w-xs">
                                                                                @foreach($wo->workOrderServices as $wos)
                                                                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 font-medium">
                                                                                        {{ $wos->service?->name ?? $wos->custom_service_name ?? 'Layanan' }}
                                                                                    </span>
                                                                                @endforeach
                                                                            </div>
                                                                        @else
                                                                            <span class="text-gray-400 italic text-[11px]">-</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        @php
                                                                            $stColor = match($wo->status->value ?? $wo->status) {
                                                                                'SELESAI' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                                                'BATAL' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                                                default => 'bg-amber-50 text-amber-700 border-amber-200'
                                                                            };
                                                                        @endphp
                                                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-extrabold border {{ $stColor }}">
                                                                            {{ $wo->status->value ?? $wo->status }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-right font-mono font-bold text-gray-800">
                                                                        Rp {{ number_format($wo->total_transaksi ?? 0, 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" class="px-4 py-6 text-center text-gray-400 italic">Tidak ada data SPK terkait</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            {{-- TAB 2: BREAKDOWN FINANSIAL & TAGIHAN --}}
                                            <div x-show="activeTab === 'finance'" class="space-y-4">
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    
                                                    {{-- Card 1: Ringkasan Nilai Tagihan --}}
                                                    <div class="bg-gray-50/80 rounded-xl p-4 border border-gray-200 space-y-2.5">
                                                        <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                                                            <span class="text-[11px] font-black text-gray-500 uppercase tracking-wider">Nilai Tagihan</span>
                                                            <span class="text-xs font-bold text-gray-400">Total SPK: {{ $invoice->workOrders->count() }}</span>
                                                        </div>
                                                        <div class="space-y-1.5 text-xs">
                                                            <div class="flex justify-between text-gray-600">
                                                                <span>Subtotal Jasa:</span>
                                                                <span class="font-bold text-gray-900 font-mono">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                                                            </div>
                                                            <div class="flex justify-between text-gray-600">
                                                                <span>Ongkos Kirim:</span>
                                                                <span class="font-bold text-gray-900 font-mono">Rp {{ number_format($invoice->shipping_cost, 0, ',', '.') }}</span>
                                                            </div>
                                                            @if($invoice->discount > 0)
                                                                <div class="flex justify-between text-rose-600">
                                                                    <span>Potongan Diskon:</span>
                                                                    <span class="font-bold font-mono">- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</span>
                                                                </div>
                                                            @endif
                                                            <div class="pt-2 border-t border-gray-200 flex justify-between font-black text-sm text-gray-900">
                                                                <span>Total Tagihan:</span>
                                                                <span class="text-[#1B8A68] font-mono">Rp {{ number_format($invoice->total_amount + $invoice->shipping_cost - $invoice->discount, 0, ',', '.') }}</span>
                                                            </div>
                                                            <div class="flex justify-between text-[11px] text-emerald-700 font-bold">
                                                                <span>Total Terbayar:</span>
                                                                <span class="font-mono">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                                                            </div>
                                                            <div class="flex justify-between text-[11px] font-bold {{ $invoice->remaining_balance > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                                                                <span>Sisa Piutang:</span>
                                                                <span class="font-mono">Rp {{ number_format(max(0, $invoice->remaining_balance), 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Card 2: Skema Tagihan DP (70%) --}}
                                                    <div class="bg-emerald-50/40 rounded-xl p-4 border border-emerald-100 space-y-2.5">
                                                        <div class="flex items-center justify-between pb-2 border-b border-emerald-100">
                                                            <span class="text-[11px] font-black text-emerald-800 uppercase tracking-wider">Tagihan DP (70%)</span>
                                                            @if($invoice->is_dp_paid || $invoice->status === 'Lunas')
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-600 text-white">LUNAS</span>
                                                            @else
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800">MENUNGGU</span>
                                                            @endif
                                                        </div>
                                                        <div class="space-y-1.5 text-xs">
                                                            <div class="flex justify-between text-gray-600">
                                                                <span>Target DP Dasar:</span>
                                                                <span class="font-mono font-bold text-gray-800">Rp {{ number_format($invoice->target_dp_amount, 0, ',', '.') }}</span>
                                                            </div>
                                                            <div class="flex justify-between text-gray-600">
                                                                <span>Kode Unik DP:</span>
                                                                <span class="font-mono font-bold text-emerald-600">+{{ $invoice->dp_unique_code ?? 0 }}</span>
                                                            </div>
                                                            <div class="pt-2 border-t border-emerald-100 flex justify-between font-black text-sm text-emerald-900">
                                                                <span>Total Ditagih:</span>
                                                                <span class="font-mono">Rp {{ number_format($invoice->total_dp, 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                        @if($invoice->invoice_dp_url || $invoice->invoice_awal_url)
                                                            @php $dpUrl = $invoice->invoice_dp_url ?: $invoice->invoice_awal_url; @endphp
                                                            <div class="pt-2">
                                                                <button type="button" 
                                                                        @click="copyText('{{ $dpUrl }}', 'dp_{{ $invoice->id }}')" 
                                                                        class="w-full py-1.5 px-3 bg-white hover:bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                                    <span x-text="copiedId === 'dp_{{ $invoice->id }}' ? '✓ Link DP Tersalin!' : 'Salin Link Tagihan DP'"></span>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Card 3: Skema Pelunasan / Full Payment --}}
                                                    <div class="bg-rose-50/40 rounded-xl p-4 border border-rose-100 space-y-2.5">
                                                        <div class="flex items-center justify-between pb-2 border-b border-rose-100">
                                                            <span class="text-[11px] font-black text-rose-800 uppercase tracking-wider">Pelunasan / Full</span>
                                                            @if($invoice->status === 'Lunas')
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-600 text-white">LUNAS</span>
                                                            @else
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800">BELUM LUNAS</span>
                                                            @endif
                                                        </div>
                                                        <div class="space-y-1.5 text-xs">
                                                            <div class="flex justify-between text-gray-600">
                                                                <span>Sisa Pokok:</span>
                                                                <span class="font-mono font-bold text-gray-800">Rp {{ number_format(max(0, $invoice->remaining_balance), 0, ',', '.') }}</span>
                                                            </div>
                                                            <div class="flex justify-between text-gray-600">
                                                                <span>Kode Unik Pelunasan:</span>
                                                                <span class="font-mono font-bold text-rose-600">+{{ $invoice->final_unique_code ?? 0 }}</span>
                                                            </div>
                                                            <div class="pt-2 border-t border-rose-100 flex justify-between font-black text-sm text-rose-900">
                                                                <span>Total Ditagih:</span>
                                                                <span class="font-mono">Rp {{ number_format($invoice->total_pelunasan > 0 ? $invoice->total_pelunasan : $invoice->total_full, 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                        @if($invoice->invoice_final_url || $invoice->invoice_full_url || $invoice->invoice_akhir_url)
                                                            @php $fpUrl = $invoice->invoice_final_url ?: ($invoice->invoice_full_url ?: $invoice->invoice_akhir_url); @endphp
                                                            <div class="pt-2">
                                                                <button type="button" 
                                                                        @click="copyText('{{ $fpUrl }}', 'fp_{{ $invoice->id }}')" 
                                                                        class="w-full py-1.5 px-3 bg-white hover:bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                                    <span x-text="copiedId === 'fp_{{ $invoice->id }}' ? '✓ Link Pelunasan Tersalin!' : 'Salin Link Pelunasan'"></span>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Quick Actions Footer --}}
                                            <div class="pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-gray-400 font-bold text-[11px]">Tautan Langsung:</span>
                                                    <a href="{{ url('/api/invoice_share_grouped.php?token=' . urlencode($invoice->invoice_number) . '&type=BL') }}" 
                                                       target="_blank" 
                                                       class="px-2.5 py-1 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition-colors inline-flex items-center gap-1">
                                                        <span>Nota Awal (DP)</span>
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                    </a>
                                                    <a href="{{ url('/api/invoice_share_grouped.php?token=' . urlencode($invoice->invoice_number) . '&type=L') }}" 
                                                       target="_blank" 
                                                       class="px-2.5 py-1 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition-colors inline-flex items-center gap-1">
                                                        <span>Nota Akhir (Lunas)</span>
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                    </a>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('finance.invoices.show', $invoice->id) }}" 
                                                       class="px-4 py-2 bg-gradient-to-r from-[#1B8A68] to-[#125c46] hover:from-[#22a57d] hover:to-[#176a51] text-white rounded-xl font-bold shadow-md shadow-emerald-200 transition-all inline-flex items-center gap-2">
                                                        <span>Kelola Detail Lengkap Invoice</span>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                    </a>
                                                </div>
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-24 text-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-2xl flex items-center justify-center text-4xl mb-4 border border-gray-100 mx-auto">📋</div>
                                        <h3 class="text-xl font-black text-gray-800 mb-1">Belum Ada Data Invoice</h3>
                                        <p class="text-gray-400 text-xs font-semibold">Tidak ada transaksi tagihan yang sesuai dengan filter atau kata kunci pencarian</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Desktop --}}
                @if(isset($invoices) && $invoices->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>

            {{-- MOBILE/TABLET RESPONSIVE CARDS (visible on mobile/tablet, hidden on desktop) --}}
            <div class="lg:hidden space-y-4">
                @forelse($invoices as $invoice)
                    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-4 transition-all space-y-4"
                         x-data="{ mobileExpanded: false }">
                        
                        {{-- Top Header: No Invoice + Status Badge --}}
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#1B8A68] flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <a href="{{ route('finance.invoices.show', $invoice->id) }}" class="font-extrabold text-sm text-gray-900 font-mono block hover:text-[#1B8A68]">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                    <span class="text-[11px] text-gray-400 font-medium">
                                        {{ $invoice->created_at->format('d M Y • H:i') }}
                                    </span>
                                </div>
                            </div>
                            @php
                                $statusBadge = match($invoice->status) {
                                    'Lunas' => 'bg-emerald-50 text-[#1B8A68] border-emerald-200',
                                    'DP/Cicil' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'Batal', 'BATAL' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-100 text-slate-600 border-slate-200'
                                };
                                $spkMobileBadge = match($invoice->spk_status) {
                                    'SELESAI' => 'bg-emerald-50 text-[#1B8A68]',
                                    'BATAL' => 'bg-rose-50 text-rose-700',
                                    default => 'bg-amber-50 text-amber-700'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black border {{ $statusBadge }}">
                                {{ $invoice->status }}
                            </span>
                        </div>

                        {{-- Customer Info + SPK Status --}}
                        <div class="flex items-center justify-between py-2 border-y border-gray-100 text-xs">
                            <div>
                                <div class="font-bold text-gray-900">{{ $invoice->customer?->name ?? 'Data Terhapus' }}</div>
                                <div class="text-gray-400 font-mono text-[11px]">{{ $invoice->customer?->phone ?? '-' }}</div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $spkMobileBadge }}">
                                    {{ $invoice->spk_status }}
                                </span>
                                <div class="text-[10px] text-gray-400 font-semibold mt-0.5">{{ $invoice->workOrders->count() }} Pasang Sepatu</div>
                            </div>
                        </div>

                        {{-- Total Price & Actions --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Total Tagihan</span>
                                <span class="text-base font-black text-gray-900 font-mono">
                                    Rp {{ number_format($invoice->total_amount + $invoice->shipping_cost, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ url('/api/invoice_share_grouped.php?token=' . urlencode($invoice->invoice_number) . '&type=' . ($invoice->status === 'Belum Bayar' ? 'BL' : 'L')) }}" 
                                   target="_blank" 
                                   class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-200 text-gray-600 flex items-center justify-center hover:bg-emerald-50 hover:text-[#1B8A68]" 
                                   title="Cetak">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                <button type="button" 
                                        @click="mobileExpanded = !mobileExpanded" 
                                        class="px-3 py-2 rounded-xl text-xs font-bold border transition-all flex items-center gap-1.5"
                                        :class="mobileExpanded ? 'bg-[#1B8A68] text-white border-[#1B8A68]' : 'bg-gray-50 text-gray-700 border-gray-200'">
                                    <span x-text="mobileExpanded ? 'Tutup Rincian' : 'Buka Rincian'"></span>
                                    <svg class="w-3.5 h-3.5 transition-transform" :class="mobileExpanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Mobile Collapsible Details --}}
                        <div x-show="mobileExpanded" x-cloak x-collapse class="pt-3 border-t border-gray-100 space-y-4 text-xs">
                            {{-- SPK List Mobile --}}
                            <div class="space-y-2">
                                <span class="font-black text-gray-600 uppercase text-[10px] tracking-wider block">Daftar Sepatu (SPK):</span>
                                @foreach($invoice->workOrders as $wo)
                                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-200/80 space-y-1">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1.5">
                                                <a href="{{ route('admin.orders.show', $wo->id) }}" 
                                                   target="_blank" 
                                                   class="font-mono font-bold text-gray-900 hover:text-[#1B8A68] hover:underline inline-flex items-center gap-1"
                                                   title="Buka Detail Order di Admin (/admin/orders/show)">
                                                    <span>{{ $wo->spk_number }}</span>
                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                                @if(\Illuminate\Support\Facades\Route::has('reception.print-spk'))
                                                    <a href="{{ route('reception.print-spk', $wo->id) }}" 
                                                       target="_blank" 
                                                       class="text-gray-400 hover:text-[#1B8A68]" 
                                                       title="Cetak SPK">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                    </a>
                                                @endif
                                            </div>
                                            <span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-emerald-50 text-[#1B8A68]">{{ $wo->cs_code ?? '-' }}</span>
                                        </div>
                                        <div class="font-semibold text-gray-700">{{ $wo->shoe_brand }} - {{ $wo->shoe_type }}</div>
                                        <div class="flex items-center justify-between text-[11px] pt-1">
                                            <span class="text-gray-500">{{ $wo->status->value ?? $wo->status }}</span>
                                            <span class="font-mono font-bold text-gray-900">Rp {{ number_format($wo->total_transaksi ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Payment Breakdown Mobile --}}
                            <div class="bg-emerald-50/50 rounded-xl p-3 border border-emerald-100 space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">DP (70% + Unik):</span>
                                    <span class="font-mono font-bold text-emerald-800">Rp {{ number_format($invoice->total_dp, 0, ',', '.') }} ({{ $invoice->dp_unique_code ?? '-' }})</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Pelunasan (+ Unik):</span>
                                    <span class="font-mono font-bold text-rose-800">Rp {{ number_format($invoice->total_pelunasan > 0 ? $invoice->total_pelunasan : $invoice->total_full, 0, ',', '.') }} ({{ $invoice->final_unique_code ?? '-' }})</span>
                                </div>
                            </div>

                            <a href="{{ route('finance.invoices.show', $invoice->id) }}" class="block w-full py-2 bg-[#1B8A68] text-white text-center rounded-xl font-bold">
                                Buka Halaman Detail Penuh
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-8 text-center border border-gray-200">
                        <p class="text-gray-500 font-bold text-sm">Belum Ada Data Invoice</p>
                    </div>
                @endforelse

                {{-- Pagination Mobile --}}
                @if(isset($invoices) && $invoices->hasPages())
                    <div class="py-4 flex justify-center">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
