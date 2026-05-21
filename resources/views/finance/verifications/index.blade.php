<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC]">
        {{-- Premium Header --}}
        <div class="bg-white/90 shadow-2xl border-b border-gray-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex items-center gap-6">
                    <div class="p-4 bg-purple-600 rounded-[1.5rem] shadow-[0_10px_30px_-10px_rgba(147,51,234,0.5)] transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-[10px] font-black bg-purple-50 text-purple-600 px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-purple-100">REKONSILIASI</span>
                            <h1 class="text-4xl font-black text-gray-900 tracking-tighter leading-none italic uppercase">Verifikasi Mutasi</h1>
                        </div>
                        <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-70">Cocokkan Pembayaran Manual Dengan Mutasi Bank</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="max-w-7xl mx-auto px-6 pt-6">
            <div class="bg-emerald-50 border-2 border-emerald-200 text-[#1B8A68] px-6 py-4 rounded-2xl font-black text-sm italic flex items-center gap-3 shadow-lg shadow-emerald-50">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        </div>
        @endif
        @if(session('error'))
        <div class="max-w-7xl mx-auto px-6 pt-6">
            <div class="bg-red-50 border-2 border-red-200 text-red-700 px-6 py-4 rounded-2xl font-black text-sm italic flex items-center gap-3 shadow-lg shadow-red-50">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        </div>
        @endif

        {{-- Legend --}}
        <div class="max-w-7xl mx-auto px-6 pt-8">
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                    <span class="text-[10px] font-black text-gray-600 uppercase tracking-wider italic">Exact Match (Kode Unik)</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span class="text-[10px] font-black text-gray-600 uppercase tracking-wider italic">Rekomendasi Invoice Terdekat — Pilih Manual</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <span class="w-3 h-3 rounded-full bg-gray-300"></span>
                    <span class="text-[10px] font-black text-gray-600 uppercase tracking-wider italic">Tidak Ada Invoice Cocok</span>
                </div>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="max-w-7xl mx-auto px-6 pt-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 bg-white p-2 rounded-[2rem] shadow-sm border border-gray-100">
                {{-- Tabs --}}
                <div class="flex items-center gap-1 p-1 bg-gray-50 rounded-[1.8rem] w-full md:w-auto">
                    <a href="{{ route('finance.verifications.index', ['tab' => 'candidates']) }}" class="flex-1 md:flex-none px-8 py-3 rounded-[1.5rem] text-[11px] font-black uppercase tracking-widest italic transition-all {{ $tab === 'candidates' ? 'bg-white text-purple-600 shadow-sm border border-gray-100' : 'text-gray-400 hover:text-gray-600' }}">
                        Menunggu ({{ $candidatesCount ?? 0 }})
                    </a>
                    <a href="{{ route('finance.verifications.index', ['tab' => 'history']) }}" class="flex-1 md:flex-none px-8 py-3 rounded-[1.5rem] text-[11px] font-black uppercase tracking-widest italic transition-all {{ $tab === 'history' ? 'bg-white text-purple-600 shadow-sm border border-gray-100' : 'text-gray-400 hover:text-gray-600' }}">
                        Riwayat
                    </a>
                </div>

                <form action="{{ route('finance.verifications.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto pr-4">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    
                    @if($tab === 'candidates')
                    <select name="match_type" onchange="this.form.submit()" class="w-full md:w-auto px-5 py-3 bg-transparent border-none text-[11px] font-black uppercase tracking-wider italic text-gray-600 focus:ring-0 cursor-pointer">
                        <option value="">Semua Tipe Match</option>
                        <option value="exact" {{ request('match_type') === 'exact' ? 'selected' : '' }}>🟣 Exact Match</option>
                        <option value="partial" {{ request('match_type') === 'partial' ? 'selected' : '' }}>🟡 Partial Match</option>
                        <option value="none" {{ request('match_type') === 'none' ? 'selected' : '' }}>⚪ Tidak Ada Match</option>
                    </select>
                    @endif

                    <div class="relative w-full md:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Invoice..." class="w-full pl-10 pr-4 py-3 bg-gray-50 border-none rounded-xl text-sm font-bold italic text-gray-700 focus:ring-0 placeholder-gray-400">
                        <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </form>
            </div>
        </div>

        {{-- Verification Content --}}
        <div class="max-w-7xl mx-auto px-6 py-6 pb-20">
            @if($tab === 'candidates')
                {{-- CANDIDATES TAB --}}
                <div class="space-y-6">
                    @forelse($candidates as $candidate)
                        @php
                            $mutation = $candidate['mutation'];
                            $matchType = $candidate['match_type'];
                            $invoice = $candidate['invoice'];
                            $targetType = $candidate['target_type'];
                            $recommendations = $candidate['recommendations'];

                            $borderColor = match($matchType) {
                                'exact' => 'border-l-purple-500 bg-purple-50/10',
                                'partial' => 'border-l-amber-500 bg-amber-50/10',
                                default => 'border-l-gray-300',
                            };
                        @endphp
                        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 border-l-4 {{ $borderColor }} shadow-2xl relative group hover:-translate-y-1 transition-all duration-500">
                            <div class="flex flex-col lg:flex-row gap-8">
                                {{-- LEFT: Mutation Info --}}
                                <div class="flex-1">
                                    <div class="flex items-center gap-4 mb-5">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-inner {{ $matchType === 'exact' ? 'bg-purple-100 text-purple-600' : ($matchType === 'partial' ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-400') }}">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-gray-900 italic uppercase tracking-tighter">{{ $mutation->bank_code ?: 'BANK' }}</div>
                                            <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic opacity-60">MUTASI REKENING</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                        <div>
                                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider italic block mb-1">Nominal Mutasi</span>
                                            <span class="text-lg font-black text-[#1B8A68] italic tabular-nums tracking-tighter">+ Rp {{ number_format($mutation->amount, 0, ',', '.') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider italic block mb-1">Tanggal Transaksi</span>
                                            <span class="text-sm font-black text-gray-700 italic">{{ $mutation->transaction_date ? $mutation->transaction_date->format('d M Y') : '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 px-5 py-3 bg-purple-50/30 rounded-xl border border-purple-100/50">
                                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-wider italic block">Keterangan Transfer</span>
                                        <p class="text-xs font-bold text-gray-600 italic mt-0.5 leading-relaxed">{{ $mutation->description ?: '-' }}</p>
                                    </div>
                                </div>

                                {{-- DIVIDER --}}
                                <div class="hidden lg:flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                    </div>
                                </div>

                                {{-- RIGHT: Invoice Target Match --}}
                                <div class="flex-1">
                                    @if($matchType === 'exact')
                                        {{-- EXACT INVOICE MATCH --}}
                                        <div class="p-6 bg-purple-50/80 rounded-2xl border-2 border-purple-200 mb-4">
                                            <div class="flex items-center gap-2 mb-4">
                                                <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                                                <span class="text-[10px] font-black text-purple-700 uppercase tracking-[0.2em] italic">Exact Match Terdeteksi!</span>
                                            </div>
                                            <div class="space-y-3">
                                                <div class="flex justify-between items-baseline"><span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">No. Invoice</span><span class="text-sm font-black text-purple-800 italic">{{ $invoice->invoice_number }}</span></div>
                                                <div class="flex justify-between items-baseline"><span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">No. SPK</span><span class="text-xs font-black text-purple-700 italic">{{ $invoice->workOrders->pluck('spk_number')->implode(', ') ?: '-' }}</span></div>
                                                <div class="flex justify-between items-baseline"><span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Pelanggan</span><span class="text-xs font-black text-gray-800 italic">{{ $invoice->customer->name ?? '-' }}</span></div>
                                                <div class="flex justify-between items-baseline">
                                                    <span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">No. HP</span>
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-xs font-black text-gray-800 italic">{{ $invoice->customer->phone ?? '-' }}</span>
                                                        @if($invoice->customer && $invoice->customer->phone)
                                                            <button type="button" onclick="copyTextToClipboard('{{ $invoice->customer->phone }}')" class="p-1 text-purple-600 hover:bg-purple-100 rounded-md transition" title="Salin Nomor HP">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex justify-between items-baseline"><span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Jenis Tagihan</span><span class="text-xs font-black text-purple-700 italic font-black uppercase tracking-wider">{{ $targetType }} (Target Cerdas)</span></div>
                                                <div class="flex justify-between items-baseline"><span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Target Nominal</span><span class="text-sm font-black text-purple-700 italic tabular-nums">Rp {{ number_format($targetType === 'DP' ? $invoice->total_dp_with_code : $invoice->total_pelunasan_with_code, 0, ',', '.') }}</span></div>
                                            </div>
                                        </div>
                                        {{-- AUTO-MATCH COCOK BANNER --}}
                                        <div class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-purple-50 text-purple-700 rounded-2xl border-2 border-dashed border-purple-200 font-black text-[11px] uppercase tracking-[0.1em] italic">
                                            <svg class="w-5 h-5 animate-spin text-purple-600" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Akan Diverifikasi Otomatis Besok Hari
                                        </div>

                                    @elseif($matchType === 'partial')
                                        {{-- PARTIAL MATCH — Nearest Active Invoices dropdown --}}
                                        <div class="p-6 bg-amber-50 rounded-2xl border-2 border-amber-200 mb-4">
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span class="text-[10px] font-black text-amber-700 uppercase tracking-[0.2em] italic">Rekomendasi Invoice Terdekat</span>
                                            </div>
                                            <p class="text-[10px] text-amber-800/70 italic font-bold leading-relaxed">Sistem mendeteksi invoice dengan nominal terdekat. Silakan pilih invoice yang sesuai untuk mengikat pembayaran ini.</p>
                                        </div>

                                        <form id="verify-form-{{ $mutation->id }}" action="{{ route('finance.verifications.verify', $mutation->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-4 relative font-sans">
                                                {{-- Trigger Button --}}
                                                <button type="button" id="dropdown-trigger-{{ $mutation->id }}" class="w-full px-5 py-4 bg-white border-2 border-amber-200 rounded-2xl text-xs font-black italic tracking-tight text-gray-700 focus:border-amber-400 focus:ring-4 focus:ring-amber-500/5 outline-none flex items-center justify-between cursor-pointer select-none">
                                                    <span id="selected-text-{{ $mutation->id }}">
                                                        <span class="text-gray-400 font-bold italic">— Pilih invoice aktif terdekat —</span>
                                                    </span>
                                                    <svg class="w-4 h-4 text-amber-500 transition-transform duration-300 transform" id="dropdown-arrow-{{ $mutation->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                                </button>

                                                {{-- Dropdown Menu Options Panel --}}
                                                <div id="dropdown-menu-{{ $mutation->id }}" class="hidden absolute left-0 right-0 top-full mt-2 bg-white rounded-[2rem] border border-gray-100 shadow-2xl p-3 z-50 max-h-[320px] overflow-y-auto space-y-1.5 scrollbar-thin">
                                                    @foreach($recommendations as $rec)
                                                        @php 
                                                            $recInvoice = $rec['invoice'];
                                                            $recType = $rec['target_type'];
                                                            $recTarget = $rec['target_nominal'];
                                                            $recDiff = $rec['diff'];
                                                            $spks = $recInvoice->workOrders->pluck('spk_number')->implode(', ');
                                                            $custName = $recInvoice->customer->name ?? 'Pelanggan';
                                                        @endphp
                                                        <div class="dropdown-option-{{ $mutation->id }} p-4 rounded-xl hover:bg-amber-50/50 border border-transparent hover:border-amber-100 cursor-pointer transition-all duration-300 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                                             data-value="{{ $recInvoice->id }}:{{ $recType }}"
                                                             data-invoice="{{ $recInvoice->invoice_number }}"
                                                             data-spk="{{ $spks }}"
                                                             data-customer="{{ $custName }}"
                                                             data-target-type="{{ $recType }}"
                                                             data-target-nominal="Rp {{ number_format($recTarget, 0, ',', '.') }}"
                                                             data-diff="Rp {{ number_format($recDiff, 0, ',', '.') }}"
                                                             data-phone="{{ $recInvoice->customer->phone ?? '-' }}">
                                                            
                                                            {{-- Info Left --}}
                                                            <div class="flex-1 min-w-0">
                                                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                                                    <span class="text-xs font-black text-gray-900 italic tracking-tight">{{ $recInvoice->invoice_number }}</span>
                                                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[9px] font-black uppercase tracking-wider rounded-md italic">{{ $recType }}</span>
                                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[9px] font-bold rounded-md max-w-[150px] truncate italic">{{ $custName }}</span>
                                                                </div>
                                                                <div class="text-[10px] text-gray-400 font-bold flex items-center gap-3 flex-wrap mt-0.5 italic">
                                                                    <span>SPK: <span class="text-gray-600">{{ $spks ?: '-' }}</span></span>
                                                                    <span class="inline-flex items-center gap-1">
                                                                        HP: <span class="text-gray-600">{{ $recInvoice->customer->phone ?? '-' }}</span>
                                                                        @if($recInvoice->customer && $recInvoice->customer->phone)
                                                                            <button type="button" onclick="event.stopPropagation(); copyTextToClipboard('{{ $recInvoice->customer->phone }}')" class="p-0.5 text-amber-600 hover:bg-amber-100 rounded transition" title="Salin Nomor HP">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                                            </button>
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            {{-- Nominal & Diff Right --}}
                                                            <div class="text-right flex sm:flex-col items-baseline sm:items-end justify-between sm:justify-center gap-2 sm:gap-0.5 flex-shrink-0">
                                                                <div class="text-[11px] font-black text-gray-900 italic">Target: Rp {{ number_format($recTarget, 0, ',', '.') }}</div>
                                                                <div class="text-[9px] font-black text-[#1B8A68] bg-[#E8F5E9] px-2 py-0.5 rounded-full italic">Selisih: Rp {{ number_format($recDiff, 0, ',', '.') }}</div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <input type="hidden" name="invoice_id" value="">
                                                <input type="hidden" name="target_type" value="">
                                            </div>
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.15em] italic shadow-xl shadow-amber-100 transition-all hover:-translate-y-0.5 active:scale-95">
                                                Verify Manual
                                            </button>
                                        </form>

                                        {{-- Custom Dropdown JS Script --}}
                                        <script>
                                            (function() {
                                                const trigger = document.getElementById('dropdown-trigger-{{ $mutation->id }}');
                                                const menu = document.getElementById('dropdown-menu-{{ $mutation->id }}');
                                                const arrow = document.getElementById('dropdown-arrow-{{ $mutation->id }}');
                                                const form = document.getElementById('verify-form-{{ $mutation->id }}');
                                                const invoiceInput = form.querySelector('input[name="invoice_id"]');
                                                const targetTypeInput = form.querySelector('input[name="target_type"]');
                                                const selectedText = document.getElementById('selected-text-{{ $mutation->id }}');
                                                const options = document.querySelectorAll('.dropdown-option-{{ $mutation->id }}');

                                                // Toggle Dropdown Menu
                                                trigger.addEventListener('click', function(e) {
                                                    e.stopPropagation();
                                                    // Close other open dropdowns
                                                    document.querySelectorAll('[id^="dropdown-menu-"]').forEach(m => {
                                                        if (m !== menu) m.classList.add('hidden');
                                                    });
                                                    document.querySelectorAll('[id^="dropdown-arrow-"]').forEach(a => {
                                                        if (a !== arrow) a.classList.remove('rotate-180');
                                                    });
                                                    
                                                    const isHidden = menu.classList.contains('hidden');
                                                    if (isHidden) {
                                                        menu.classList.remove('hidden');
                                                        arrow.classList.add('rotate-180');
                                                    } else {
                                                        menu.classList.add('hidden');
                                                        arrow.classList.remove('rotate-180');
                                                    }
                                                });

                                                // Option Selection
                                                options.forEach(opt => {
                                                    opt.addEventListener('click', function(e) {
                                                        e.stopPropagation();
                                                        const val = this.getAttribute('data-value');
                                                        const parts = val.split(':');
                                                        invoiceInput.value = parts[0];
                                                        targetTypeInput.value = parts[1];

                                                        const invoiceNum = this.getAttribute('data-invoice');
                                                        const targetType = this.getAttribute('data-target-type');
                                                        const targetNominal = this.getAttribute('data-target-nominal');
                                                        const diff = this.getAttribute('data-diff');
                                                        const cust = this.getAttribute('data-customer');
                                                         const phone = this.getAttribute('data-phone');

                                                        // Set trigger button content to be ultra premium
                                                        selectedText.innerHTML = `
                                                            <div class="flex items-center gap-2 flex-wrap text-left">
                                                                <span class="font-black text-gray-900">${invoiceNum}</span>
                                                                <span class="px-1.5 py-0.5 bg-amber-100 text-amber-800 text-[8px] font-black rounded">${targetType}</span>
                                                                <span class="text-gray-400 font-medium text-[10px] truncate max-w-[240px]">(${cust} • HP: ${phone})</span>
                                                                <span class="text-[#1B8A68] font-bold text-[10px] ml-1">Target: ${targetNominal} (Selisih: ${diff})</span>
                                                            </div>
                                                        `;

                                                        menu.classList.add('hidden');
                                                        arrow.classList.remove('rotate-180');
                                                    });
                                                });

                                                // Close on clicking outside
                                                document.addEventListener('click', function() {
                                                    menu.classList.add('hidden');
                                                    arrow.classList.remove('rotate-180');
                                                });

                                                // Form Submit Validation
                                                form.addEventListener('submit', function(e) {
                                                    if (!invoiceInput.value || !targetTypeInput.value) {
                                                        e.preventDefault();
                                                        alert('Silakan pilih invoice aktif terdekat terlebih dahulu!');
                                                        return false;
                                                    }
                                                    return confirm('Verifikasi mutasi bank untuk invoice terpilih?');
                                                });
                                            })();
                                        </script>

                                    @else
                                        {{-- NO MATCH --}}
                                        <div class="p-6 bg-gray-50 rounded-2xl border-2 border-gray-100 h-full flex flex-col items-center justify-center text-center">
                                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl mb-4 shadow-sm">🔍</div>
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic block mb-2">No Match</span>
                                            <p class="text-[10px] text-gray-400 italic font-bold leading-relaxed max-w-[250px]">Tidak ada invoice aktif yang nominal targetnya mendekati nominal mutasi bank ini (selisih di bawah Rp 10.000).</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden px-10 py-40 text-center">
                            <div class="w-32 h-32 bg-[#F8FAFC] rounded-[2.5rem] flex items-center justify-center text-6xl mb-8 shadow-inner border border-gray-100 mx-auto filter grayscale opacity-20">🛡️</div>
                            <h3 class="text-3xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">
                                {{ $tab === 'auto' ? 'Tidak Ada Cocok Otomatis' : 'Semua Terverifikasi' }}
                            </h3>
                            <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-60">
                                {{ $tab === 'auto' ? 'Belum ada mutasi bank hari ini yang cocok otomatis dengan kode unik' : 'Tidak ada mutasi bank masuk yang menunggu verifikasi' }}
                            </p>
                        </div>
                    @endforelse
                </div>
            @else
                {{-- HISTORY TAB --}}
                <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden relative">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#F8FAFC] border-b border-gray-100">
                                    <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Invoice & Pelanggan</th>
                                    <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Nominal Verifikasi</th>
                                    <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Data Mutasi</th>
                                    <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Diverifikasi Oleh</th>
                                    <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($history as $v)
                                    <tr class="hover:bg-[#F8FAFC] transition-all duration-300">
                                        <td class="px-10 py-6">
                                            <div class="text-sm font-black text-gray-900 italic uppercase tracking-tighter">{{ $v->payment->invoice->invoice_number ?? '-' }}</div>
                                            <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic opacity-60">{{ $v->payment->invoice->customer->name ?? '-' }}</div>
                                        </td>
                                        <td class="px-10 py-6">
                                            <div class="text-lg font-black text-[#1B8A68] italic tabular-nums tracking-tighter">Rp {{ number_format($v->payment->amount, 0, ',', '.') }}</div>
                                            @if($v->notes)
                                                <div class="text-[9px] text-amber-600 font-bold italic mt-1">{{ $v->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="px-10 py-6">
                                            <div class="text-xs font-black text-gray-700 italic">{{ $v->mutation->bank_code }} • {{ $v->mutation->transaction_date->format('d/m/Y') }}</div>
                                            <div class="text-[9px] text-gray-400 font-bold italic mt-1 truncate max-w-[150px]">{{ $v->mutation->description }}</div>
                                        </td>
                                        <td class="px-10 py-6">
                                            <div class="text-xs font-black text-gray-700 italic">{{ $v->verifier->name ?? '-' }}</div>
                                            <div class="text-[9px] text-gray-400 font-bold italic mt-1">{{ $v->verified_at->format('d/m/Y H:i') }}</div>
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <form action="{{ route('finance.verifications.unverify', $v->id) }}" method="POST" onsubmit="return confirm('PERHATIAN: Membatalkan verifikasi akan membebaskan mutasi bank dan mengubah status invoice kembali menjadi belum lunas. Lanjutkan?')">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl font-black text-[10px] uppercase tracking-widest italic transition-all border border-red-100">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Batalkan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-10 py-20 text-center">
                                            <div class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-60">Belum ada riwayat verifikasi</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($history->hasPages())
                        <div class="px-10 py-10 border-t border-gray-50 bg-[#F8FAFC]/50">
                            {{ $history->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    {{-- Robust Clipboard Copy script with fallback --}}
    <script>
        function copyTextToClipboard(text) {
            if (!text || text === '-') {
                alert('Tidak ada nomor HP yang bisa disalin.');
                return;
            }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Nomor HP berhasil disalin: ' + text);
                }).catch(err => {
                    fallbackCopyToClipboard(text);
                });
            } else {
                fallbackCopyToClipboard(text);
            }
        }

        function fallbackCopyToClipboard(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    alert('Nomor HP berhasil disalin: ' + text);
                } else {
                    alert('Gagal menyalin nomor HP.');
                }
            } catch (err) {
                alert('Gagal menyalin nomor HP: ' + err);
            }
            document.body.removeChild(textArea);
        }
    </script>
</x-app-layout>
