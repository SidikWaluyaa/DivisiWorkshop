<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-12" x-data="{ fullImage: null }">
    {{-- Top Header Section --}}
    <div class="bg-white dark:bg-gray-800 p-5 sm:p-7 rounded-3xl shadow-sm border border-slate-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-[#22AF85] text-white flex items-center justify-center font-black text-xs shadow-md shadow-[#22AF85]/20 transform -rotate-3">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-[#22AF85] bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
                        Finance Desk
                    </span>
                </div>
            </div>
            <h1 class="text-xl sm:text-2xl font-black font-poppins text-slate-900 dark:text-white uppercase tracking-tight">
                Verifikasi Bukti Bayar Customer
            </h1>
            <p class="text-xs text-slate-500 dark:text-gray-400 max-w-xl">
                Validasi dan sinkronisasi pembayaran mandiri customer, pilih tipe pembayaran (DP, Pelunasan, dll) untuk update status Invoice otomatis.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ url('/konfirmasi-pembayaran') }}" target="_blank"
               class="w-full sm:w-auto px-4 py-3 bg-[#FFC232] hover:bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider rounded-2xl shadow-md shadow-amber-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Buka Form Customer ↗</span>
            </a>
        </div>
    </div>

    {{-- KPI Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        {{-- Card 1: Pending --}}
        <div wire:click="setTab('pending')" 
             class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-3xl shadow-sm border-2 transition-all cursor-pointer group {{ $activeTab === 'pending' ? 'border-[#FFC232] bg-amber-50/20 shadow-md shadow-amber-500/10' : 'border-slate-100 dark:border-gray-700 hover:border-slate-200' }}">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-gray-400">Menunggu Verifikasi</span>
                <div class="w-7 h-7 rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 flex items-center justify-center font-black text-xs">
                    ⏳
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black font-poppins text-slate-900 dark:text-white">
                    {{ $pendingCount }}
                </span>
                <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase">Struk Pending</span>
            </div>
            <div class="mt-2 text-[10px] text-slate-400 flex items-center gap-1">
                <span class="w-2 h-2 rounded-full {{ $pendingCount > 0 ? 'bg-[#FFC232] animate-ping' : 'bg-slate-300' }}"></span>
                <span>{{ $pendingCount > 0 ? 'Pilih tipe bayar & setujui' : 'Semua antrean beres' }}</span>
            </div>
        </div>

        {{-- Card 2: Verified --}}
        <div wire:click="setTab('verified')" 
             class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-3xl shadow-sm border-2 transition-all cursor-pointer group {{ $activeTab === 'verified' ? 'border-[#22AF85] bg-emerald-50/20 shadow-md shadow-emerald-500/10' : 'border-slate-100 dark:border-gray-700 hover:border-slate-200' }}">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-gray-400">Terverifikasi (Lolos)</span>
                <div class="w-7 h-7 rounded-xl bg-emerald-100 text-[#22AF85] dark:bg-emerald-950 dark:text-emerald-300 flex items-center justify-center font-black text-xs">
                    ✓
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black font-poppins text-[#22AF85]">
                    {{ $verifiedCount }}
                </span>
                <span class="text-[10px] font-bold text-[#22AF85] uppercase">Tercatat</span>
            </div>
            <div class="mt-2 text-[10px] text-slate-400 flex items-center gap-1">
                <span>Saldo Invoice otomatis terpotong</span>
            </div>
        </div>

        {{-- Card 3: Rejected --}}
        <div wire:click="setTab('rejected')" 
             class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-3xl shadow-sm border-2 transition-all cursor-pointer group {{ $activeTab === 'rejected' ? 'border-rose-400 bg-rose-50/20 shadow-md shadow-rose-500/10' : 'border-slate-100 dark:border-gray-700 hover:border-slate-200' }}">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-gray-400">Bukti Ditolak</span>
                <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 flex items-center justify-center font-black text-xs">
                    ✕
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black font-poppins text-rose-600">
                    {{ $rejectedCount }}
                </span>
                <span class="text-[10px] font-bold text-rose-600 uppercase">Ditolak</span>
            </div>
            <div class="mt-2 text-[10px] text-slate-400 flex items-center gap-1">
                <span>Disertai catatan penolakan</span>
            </div>
        </div>
    </div>

    {{-- Filter & Search Toolbar --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-3xl shadow-sm border border-slate-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        {{-- Navigation Tabs --}}
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
            <button type="button" 
                    wire:click="setTab('pending')"
                    class="py-2.5 px-4 rounded-2xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer flex-shrink-0
                           {{ $activeTab === 'pending' ? 'bg-[#FFC232] text-slate-950 shadow-sm' : 'bg-slate-100 text-slate-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-slate-200' }}">
                <span>Menunggu (Pending)</span>
                @if($pendingCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'pending' ? 'bg-slate-950 text-[#FFC232]' : 'bg-amber-500 text-white' }}">
                        {{ $pendingCount }}
                    </span>
                @endif
            </button>

            <button type="button" 
                    wire:click="setTab('verified')"
                    class="py-2.5 px-4 rounded-2xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer flex-shrink-0
                           {{ $activeTab === 'verified' ? 'bg-[#22AF85] text-white shadow-sm' : 'bg-slate-100 text-slate-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-slate-200' }}">
                <span>Terverifikasi</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'verified' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-gray-600 text-slate-700 dark:text-gray-300' }}">
                    {{ $verifiedCount }}
                </span>
            </button>

            <button type="button" 
                    wire:click="setTab('rejected')"
                    class="py-2.5 px-4 rounded-2xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer flex-shrink-0
                           {{ $activeTab === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-slate-200' }}">
                <span>Ditolak</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'rejected' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-gray-600 text-slate-700 dark:text-gray-300' }}">
                    {{ $rejectedCount }}
                </span>
            </button>
        </div>

        {{-- Search Input --}}
        <div class="relative w-full sm:w-80">
            <input type="text" 
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari Invoice, SPK, Customer, No HP..."
                   class="w-full text-xs bg-[#F8FAFC] dark:bg-gray-900 border border-slate-200 dark:border-gray-700 rounded-2xl pl-9 pr-4 py-2.5 text-slate-900 dark:text-gray-200 font-medium focus:ring-2 focus:ring-[#22AF85]/20 focus:border-[#22AF85] outline-none transition-all placeholder:text-slate-400">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- ADAPTIVE CONTENT: CARDS ON MOBILE & TABLE ON DESKTOP     --}}
    {{-- ======================================================== --}}

    {{-- 1. Mobile Cards View (Tampil di Layar < lg) --}}
    <div class="block lg:hidden space-y-4">
        @forelse($payments as $index => $pay)
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-md shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-gray-700 space-y-4 relative overflow-hidden">
                {{-- Status Header Bar --}}
                <div class="flex items-start justify-between gap-2 border-b border-slate-100 dark:border-gray-700 pb-3">
                    <div class="flex items-center gap-3">
                        {{-- Bukti Transfer Thumbnail --}}
                        @if($pay->proof_image)
                            <div class="relative group cursor-pointer w-14 h-14 rounded-2xl overflow-hidden border border-slate-200 dark:border-gray-700 shadow-sm flex-shrink-0"
                                 @click="fullImage = '{{ Storage::url($pay->proof_image) }}'">
                                <img src="{{ Storage::url($pay->proof_image) }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center text-white text-[9px] font-bold">
                                    🔍
                                </div>
                            </div>
                        @else
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-gray-700 flex items-center justify-center text-slate-400 text-[10px] font-bold flex-shrink-0">
                                No Foto
                            </div>
                        @endif

                        <div>
                            @if($pay->invoice)
                                <a href="{{ route('finance.invoices.show', $pay->invoice->id) }}" target="_blank"
                                   class="font-black font-poppins text-slate-900 dark:text-white hover:text-[#22AF85] text-sm uppercase block tracking-tight">
                                    {{ $pay->invoice->invoice_number }} ↗
                                </a>
                            @else
                                <span class="font-black text-slate-900 dark:text-white text-sm uppercase">{{ $pay->spk_number_snapshot }}</span>
                            @endif
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    {{ $pay->customer_name_snapshot ?? ($pay->invoice->customer->name ?? 'N/A') }}
                                </span>
                                @if($phone = ($pay->customer_phone_snapshot ?? ($pay->invoice->customer->phone ?? null)))
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $phone)) }}" target="_blank"
                                       class="text-[10px] text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded font-mono font-bold hover:underline">
                                        WA ↗
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div>
                        @if(str_contains($pay->notes, '[DITOLAK FINANCE'))
                            <span class="px-2.5 py-1 bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 rounded-full text-[9px] font-black uppercase block">
                                Ditolak
                            </span>
                        @elseif($pay->is_verified)
                            <span class="px-2.5 py-1 bg-emerald-100 text-[#22AF85] dark:bg-emerald-950 dark:text-emerald-300 rounded-full text-[9px] font-black uppercase block">
                                ✓ Lolos
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300 rounded-full text-[9px] font-black uppercase animate-pulse block">
                                Pending
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Shoe Items Breakdown --}}
                @if($pay->invoice && $pay->invoice->workOrders->isNotEmpty())
                    <div class="bg-slate-50 dark:bg-gray-750 p-2.5 rounded-2xl border border-slate-200/60 dark:border-gray-700 text-xs space-y-1">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Sepatu &amp; SPK:</span>
                        <div class="space-y-1 max-h-24 overflow-y-auto">
                            @foreach($pay->invoice->workOrders as $wo)
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $wo->shoe_brand }} {{ $wo->shoe_type }}</span>
                                    <span class="font-mono text-[10px] text-slate-500 bg-white dark:bg-gray-800 px-1.5 py-0.5 rounded border">{{ $wo->spk_number }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Financial & Transfer Details --}}
                <div class="grid grid-cols-2 gap-2 bg-[#F8FAFC] dark:bg-gray-900/60 p-3.5 rounded-2xl border border-slate-200/60 dark:border-gray-700 text-xs">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Nominal Ditransfer</span>
                        <span class="font-black text-[#22AF85] font-mono text-sm block">
                            Rp {{ number_format($pay->amount_total, 0, ',', '.') }}
                        </span>
                        <span class="text-[9px] font-bold text-slate-600 dark:text-slate-300 uppercase mt-0.5 inline-block bg-slate-100 px-1.5 py-0.5 rounded">
                            Bank {{ $pay->payment_method }}
                        </span>
                    </div>

                    <div class="text-right">
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Total Tagihan</span>
                        @if($pay->invoice)
                            <span class="font-bold text-slate-800 dark:text-slate-200 font-mono text-xs block">
                                Rp {{ number_format($pay->invoice->total_amount, 0, ',', '.') }}
                            </span>
                            <span class="text-[9px] text-amber-700 dark:text-amber-400 font-bold block mt-0.5">
                                Sisa: Rp {{ number_format(max(0, $pay->invoice->total_amount - $pay->invoice->paid_amount), 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-slate-400 text-xs">-</span>
                        @endif
                    </div>
                </div>

                {{-- Tipe Pembayaran Selector / Badge --}}
                <div class="space-y-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">
                        Tipe Pembayaran
                    </label>
                    @if(!$pay->is_verified && !str_contains($pay->notes, '[DITOLAK FINANCE'))
                        <select wire:model="selectedTypes.{{ $pay->id }}" 
                                class="w-full px-3 py-2 bg-white dark:bg-gray-900 border-2 border-emerald-500/40 rounded-xl text-xs font-black text-slate-800 dark:text-white focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] outline-none">
                            <option value="BEFORE">DP / Pencicilan</option>
                            <option value="AFTER">Pelunasan Pesanan</option>
                            <option value="TAMBAH_JASA">Tambah Jasa</option>
                            <option value="LUNAS_AWAL">Lunas Awal</option>
                            <option value="ONGKIR">Pembayaran Ongkir</option>
                            <option value="OTO">Pembayaran OTO</option>
                        </select>
                    @else
                        @php
                            $typeNames = [
                                'BEFORE'      => 'DP / Pencicilan',
                                'AFTER'       => 'Pelunasan Pesanan',
                                'TAMBAH_JASA' => 'Tambah Jasa',
                                'LUNAS_AWAL'  => 'Lunas Awal',
                                'ONGKIR'      => 'Pembayaran Ongkir',
                                'OTO'         => 'Pembayaran OTO',
                            ];
                        @endphp
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-800 dark:bg-blue-950 dark:text-blue-300 rounded-lg text-xs font-black uppercase inline-block">
                            {{ $typeNames[$pay->type] ?? ($pay->type ?: 'Pembayaran') }}
                        </span>
                    @endif
                </div>

                {{-- Notes / Catatan Customer --}}
                @if($pay->notes)
                    <div class="text-[11px] text-slate-600 dark:text-gray-300 bg-slate-50 dark:bg-gray-750 p-2.5 rounded-xl border border-slate-200/60 dark:border-gray-700 italic">
                        "{{ $pay->notes }}"
                    </div>
                @endif

                {{-- Action Controls (Mobile) --}}
                <div class="pt-1 flex items-center justify-between gap-2">
                    <span class="text-[10px] text-slate-400 font-medium">
                        {{ $pay->paid_at ? $pay->paid_at->format('d M Y, H:i') : '-' }} WIB
                    </span>

                    @if(!$pay->is_verified && !str_contains($pay->notes, '[DITOLAK FINANCE'))
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    wire:click="openRejectModal({{ $pay->id }})"
                                    class="px-3.5 py-2 bg-slate-100 hover:bg-rose-50 text-rose-600 text-xs font-black uppercase rounded-xl transition-all active:scale-95 cursor-pointer">
                                ✕ Tolak
                            </button>
                            <button type="button" 
                                    wire:click="openApproveModal({{ $pay->id }})"
                                    class="px-4 py-2 bg-[#22AF85] hover:bg-emerald-600 text-white text-xs font-black uppercase rounded-xl shadow-md shadow-emerald-500/20 transition-all active:scale-95 flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Terima</span>
                            </button>
                        </div>
                    @else
                        <span class="text-slate-400 text-[10px] italic">
                            PIC: {{ $pay->pic->name ?? 'Sistem' }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-10 text-center text-slate-400 text-xs border border-slate-100">
                Tidak ada data bukti transfer pada tab ini.
            </div>
        @endforelse
    </div>

    {{-- 2. Desktop Table View (Tampil di Layar >= lg) --}}
    <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-slate-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] dark:bg-gray-900/60 border-b border-slate-100 dark:border-gray-700 text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-gray-400">
                        <th class="py-4 px-3 text-center w-10">No</th>
                        <th class="py-4 px-3">Bukti Transfer</th>
                        <th class="py-4 px-4">Invoice &amp; Item SPK</th>
                        <th class="py-4 px-4">Pelanggan</th>
                        <th class="py-4 px-4">Nominal &amp; Tagihan</th>
                        <th class="py-4 px-4">Tipe Pembayaran</th>
                        <th class="py-4 px-3">Rekening &amp; Waktu</th>
                        <th class="py-4 px-4 text-right">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-gray-700/60 text-xs">
                    @forelse($payments as $index => $pay)
                        <tr class="hover:bg-[#F8FAFC]/80 dark:hover:bg-gray-750/50 transition-colors">
                            {{-- No --}}
                            <td class="py-4 px-3 text-center font-bold text-slate-400">
                                {{ $payments->firstItem() + $index }}
                            </td>

                            {{-- Bukti Struk Thumbnail --}}
                            <td class="py-4 px-3">
                                @if($pay->proof_image)
                                    <div class="relative group cursor-pointer w-16 h-16 rounded-2xl overflow-hidden border border-slate-200 dark:border-gray-700 shadow-xs"
                                         @click="fullImage = '{{ Storage::url($pay->proof_image) }}'">
                                        <img src="{{ Storage::url($pay->proof_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-200">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-[10px] font-bold">
                                            🔍 Zoom
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[10px]">Tanpa Foto</span>
                                @endif
                            </td>

                            {{-- Invoice & Item SPK --}}
                            <td class="py-4 px-4 space-y-1 max-w-[200px]">
                                @if($pay->invoice)
                                    <a href="{{ route('finance.invoices.show', $pay->invoice->id) }}" target="_blank"
                                       class="font-black text-[#22AF85] hover:underline uppercase block text-xs tracking-tight">
                                        {{ $pay->invoice->invoice_number }} ↗
                                    </a>
                                    @if($pay->invoice->workOrders->isNotEmpty())
                                        <div class="space-y-0.5">
                                            @foreach($pay->invoice->workOrders->take(2) as $wo)
                                                <div class="text-[10px] text-slate-600 dark:text-slate-300 truncate">
                                                    • <span class="font-bold">{{ $wo->shoe_brand }}</span> ({{ $wo->spk_number }})
                                                </div>
                                            @endforeach
                                            @if($pay->invoice->workOrders->count() > 2)
                                                <span class="text-[9px] text-slate-400 italic">+{{ $pay->invoice->workOrders->count() - 2 }} item lainnya</span>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="font-bold text-slate-800 dark:text-gray-200">{{ $pay->spk_number_snapshot }}</span>
                                @endif
                            </td>

                            {{-- Pelanggan & WA --}}
                            <td class="py-4 px-4 space-y-1">
                                <span class="font-black text-slate-900 dark:text-gray-200 block">
                                    {{ $pay->customer_name_snapshot ?? ($pay->invoice->customer->name ?? '-') }}
                                </span>
                                @if($phone = ($pay->customer_phone_snapshot ?? ($pay->invoice->customer->phone ?? null)))
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $phone)) }}" target="_blank"
                                       class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-bold hover:underline flex items-center gap-1">
                                        <span>💬 {{ $phone }}</span>
                                    </a>
                                @endif
                            </td>

                            {{-- Nominal & Sisa Tagihan --}}
                            <td class="py-4 px-4 space-y-0.5">
                                <span class="font-black text-[#22AF85] text-sm font-mono block">
                                    Rp {{ number_format($pay->amount_total, 0, ',', '.') }}
                                </span>
                                @if($pay->invoice)
                                    <span class="text-[10px] text-slate-400 block font-mono">
                                        Total: Rp {{ number_format($pay->invoice->total_amount, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[9px] font-bold text-amber-700 dark:text-amber-400 block font-mono">
                                        Sisa: Rp {{ number_format(max(0, $pay->invoice->total_amount - $pay->invoice->paid_amount), 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Tipe Pembayaran Dropdown / Badge --}}
                            <td class="py-4 px-4">
                                @if(!$pay->is_verified && !str_contains($pay->notes, '[DITOLAK FINANCE'))
                                    <select wire:model="selectedTypes.{{ $pay->id }}" 
                                            class="w-full px-2.5 py-1.5 bg-white dark:bg-gray-900 border border-slate-300 dark:border-gray-600 rounded-xl text-xs font-black text-slate-800 dark:text-white focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] outline-none">
                                        <option value="BEFORE">DP / Pencicilan</option>
                                        <option value="AFTER">Pelunasan Pesanan</option>
                                        <option value="TAMBAH_JASA">Tambah Jasa</option>
                                        <option value="LUNAS_AWAL">Lunas Awal</option>
                                        <option value="ONGKIR">Pembayaran Ongkir</option>
                                        <option value="OTO">Pembayaran OTO</option>
                                    </select>
                                @else
                                    @php
                                        $typeNames = [
                                            'BEFORE'      => 'DP / Pencicilan',
                                            'AFTER'       => 'Pelunasan Pesanan',
                                            'TAMBAH_JASA' => 'Tambah Jasa',
                                            'LUNAS_AWAL'  => 'Lunas Awal',
                                            'ONGKIR'      => 'Pembayaran Ongkir',
                                            'OTO'         => 'Pembayaran OTO',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-800 dark:bg-blue-950 dark:text-blue-300 rounded-lg text-xs font-black uppercase inline-block">
                                        {{ $typeNames[$pay->type] ?? ($pay->type ?: 'Pembayaran') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Rekening & Waktu --}}
                            <td class="py-4 px-3 space-y-1">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase block w-fit
                                      {{ $pay->payment_method === 'BCA' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                    {{ $pay->payment_method }}
                                </span>
                                <span class="text-[10px] text-slate-400 block font-mono">
                                    {{ $pay->paid_at ? $pay->paid_at->format('d/m/Y H:i') : '-' }} WIB
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="py-4 px-4 text-right">
                                @if(!$pay->is_verified && !str_contains($pay->notes, '[DITOLAK FINANCE'))
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" 
                                                wire:click="openApproveModal({{ $pay->id }})"
                                                class="px-3.5 py-2 bg-[#22AF85] hover:bg-emerald-600 text-white font-black text-xs uppercase rounded-xl shadow-md shadow-emerald-500/20 transition-all active:scale-95 flex items-center gap-1 cursor-pointer">
                                            ✓ Terima
                                        </button>
                                        <button type="button" 
                                                wire:click="openRejectModal({{ $pay->id }})"
                                                class="px-3 py-2 bg-slate-100 hover:bg-rose-50 text-rose-600 font-bold text-xs uppercase rounded-xl transition-all active:scale-95 cursor-pointer">
                                            ✕ Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[10px] italic">
                                        {{ $pay->pic->name ?? 'Sistem' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-14 text-center text-slate-400 text-xs">
                                Tidak ada data bukti pembayaran pada tab ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-gray-700">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    {{-- Mobile Pagination fallback --}}
    <div class="block lg:hidden">
        @if($payments->hasPages())
            <div class="bg-white dark:bg-gray-800 p-4 rounded-3xl border border-slate-100 dark:border-gray-700">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    {{-- Fullscreen Image Preview Lightbox --}}
    <div x-show="fullImage" 
         x-cloak 
         class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4"
         @click.self="fullImage = null"
         @keydown.escape.window="fullImage = null">
        <div class="relative max-w-3xl max-h-[90vh] bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-700">
            <button type="button" 
                    @click="fullImage = null"
                    class="absolute top-3 right-3 w-8 h-8 bg-black/70 text-white rounded-full hover:bg-black transition-colors z-10 flex items-center justify-center font-bold text-sm">
                ✕
            </button>
            <img :src="fullImage" class="max-w-full max-h-[85vh] object-contain mx-auto">
        </div>
    </div>

    {{-- Approve Modal (Review & Confirm Type) --}}
    @if($approveModalOpen && $approvingPayment)
        <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl border border-slate-100 dark:border-gray-700 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-xl bg-emerald-100 text-[#22AF85] flex items-center justify-center font-bold text-xs">✓</div>
                        <h3 class="font-black text-sm uppercase tracking-wider text-slate-900 dark:text-white">Konfirmasi Terima Pembayaran</h3>
                    </div>
                    <button type="button" wire:click="closeApproveModal" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                {{-- Summary Box --}}
                <div class="bg-[#F8FAFC] dark:bg-gray-900/60 p-4 rounded-2xl border border-slate-200/60 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nomor Invoice:</span>
                        <span class="font-black text-slate-900 dark:text-white font-mono">{{ $approvingPayment->invoice->invoice_number ?? $approvingPayment->spk_number_snapshot }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nama Pelanggan:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $approvingPayment->customer_name_snapshot ?? ($approvingPayment->invoice->customer->name ?? '-') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nominal Transfer:</span>
                        <span class="font-black text-[#22AF85] text-sm font-mono">Rp {{ number_format($approvingPayment->amount_total, 0, ',', '.') }}</span>
                    </div>
                    @if($approvingPayment->invoice)
                        <div class="flex justify-between border-t pt-1.5 border-slate-200/60">
                            <span class="text-slate-500">Total Tagihan:</span>
                            <span class="font-bold font-mono">Rp {{ number_format($approvingPayment->invoice->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Sisa Tagihan Setelah Ini:</span>
                            <span class="font-black text-[#B45309] font-mono">
                                Rp {{ number_format(max(0, $approvingPayment->invoice->total_amount - ($approvingPayment->invoice->paid_amount + $approvingPayment->amount_total)), 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Tipe Pembayaran Selector --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">
                        Pilih Tipe Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="approvePaymentType" 
                            class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-2 border-emerald-500/50 rounded-2xl text-xs font-black text-slate-800 dark:text-white focus:ring-2 focus:ring-[#22AF85] outline-none">
                        <option value="BEFORE">DP / Pencicilan</option>
                        <option value="AFTER">Pelunasan Pesanan</option>
                        <option value="TAMBAH_JASA">Tambah Jasa</option>
                        <option value="LUNAS_AWAL">Lunas Awal</option>
                        <option value="ONGKIR">Pembayaran Ongkir</option>
                        <option value="OTO">Pembayaran OTO</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeApproveModal" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-bold transition-all">
                        Batal
                    </button>
                    <button type="button" wire:click="confirmApproveFromModal" class="px-5 py-2.5 bg-[#22AF85] hover:bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase tracking-wider shadow-md shadow-emerald-500/20 transition-all active:scale-95 cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Konfirmasi Terima</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Reject Reason Modal --}}
    @if($rejectModalOpen)
        <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-100 dark:border-gray-700 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-700 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xs">✕</div>
                        <h3 class="font-black text-sm uppercase tracking-wider text-rose-600">Tolak Bukti Pembayaran</h3>
                    </div>
                    <button type="button" wire:click="closeRejectModal" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-gray-300">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model.defer="rejectReason" 
                              rows="3" 
                              placeholder="Misal: Nominal tidak sesuai dengan mutasi bank, gambar struk buram/tidak terbaca..."
                              class="w-full text-xs p-3.5 border border-slate-300 dark:border-gray-600 rounded-2xl bg-[#F8FAFC] dark:bg-gray-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition-all placeholder:text-slate-400"></textarea>
                    @error('rejectReason') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeRejectModal" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-bold transition-all">
                        Batal
                    </button>
                    <button type="button" wire:click="confirmRejectPayment" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-xs font-black uppercase tracking-wider shadow-md transition-all active:scale-95 cursor-pointer">
                        Konfirmasi Tolak
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
