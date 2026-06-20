<div class="px-4 pb-12 pt-8 sm:px-6 lg:px-8 max-w-7xl mx-auto"
    x-data="{
        init() {
            // Hotkey '/' or 'Ctrl+K'
            document.addEventListener('keydown', (e) => {
                if ((e.key === '/' || (e.key === 'k' && e.ctrlKey)) && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    this.$refs.searchInput.focus();
                }
            });

            // Global Barcode Scanner
            let barcode = '';
            let barcodeTimeout;
            document.addEventListener('keypress', (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
                barcode += e.key;
                clearTimeout(barcodeTimeout);
                barcodeTimeout = setTimeout(() => {
                    if (barcode.length >= 5) {
                        @this.set('searchKeyword', barcode);
                        this.$refs.searchInput.focus();
                    }
                    barcode = '';
                }, 50);
            });
        },
        playBeep(type) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                if(ctx.state === 'suspended') ctx.resume();
                const osc = ctx.createOscillator();
                const gainNode = ctx.createGain();
                osc.connect(gainNode);
                gainNode.connect(ctx.destination);
                
                if (type === 'success') {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(800, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.1);
                    gainNode.gain.setValueAtTime(0.1, ctx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.1);
                } else {
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(300, ctx.currentTime);
                    gainNode.gain.setValueAtTime(0.1, ctx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.3);
                }
            } catch(e) {}
        }
    }"
>
    
    {{-- Search Header (Centered, Premium) --}}
    <div class="mb-10 text-center max-w-3xl mx-auto">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#22AF85]/10 border border-[#22AF85]/20 shadow-sm text-[#22AF85] mb-5">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <h1 class="text-3xl md:text-5xl font-black text-gray-900 tracking-tight">Internal Tracking</h1>
        <p class="mt-4 text-sm md:text-base text-gray-500 font-medium tracking-wide">Pencarian kilat. Langsung Scan Barcode (tanpa klik) atau Ketik Nama Customer.</p>
    </div>

    {{-- Main Search Box (Huge, Spotlight style) --}}
    <div class="max-w-4xl mx-auto mb-12 relative z-10 group">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-6 md:pl-8 flex items-center pointer-events-none">
                <svg class="h-6 w-6 md:h-8 md:w-8 text-[#22AF85] transition-transform duration-300 group-focus-within:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input 
                x-ref="searchInput"
                wire:model.live.debounce.300ms="searchKeyword" 
                type="text" 
                autofocus
                class="block w-full pl-16 md:pl-20 pr-16 py-5 md:py-6 text-xl md:text-2xl font-bold bg-white border-2 border-gray-200 rounded-3xl text-gray-900 placeholder-gray-400 focus:ring-4 focus:ring-[#22AF85]/20 focus:border-[#22AF85] shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:shadow-gray-200/60 transition-all duration-300 outline-none" 
                placeholder="Scan / Ketik SPK atau Nama..."
                autocomplete="off"
            >
            <div wire:loading class="absolute right-6 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-6 w-6 md:h-8 md:w-8 text-[#22AF85]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <div class="absolute top-1/2 -translate-y-1/2 right-6 hidden sm:flex items-center gap-1 border border-gray-200 bg-gray-50 rounded-lg px-2.5 py-1.5 text-xs text-gray-500 font-bold tracking-wide shadow-sm" wire:loading.remove border-gray-200>
                <span class="text-gray-400 text-[10px] uppercase font-bold mr-1">Hotkeys</span> /
            </div>
        </div>
    </div>

    {{-- Results Area --}}
    <div class="relative z-0">
        @if(strlen(trim($searchKeyword)) > 0)
            @if($results->count() > 0)
                <div x-init="if(window.lastBeep !== '{{ $searchKeyword }}-success') { playBeep('success'); window.lastBeep = '{{ $searchKeyword }}-success'; }"></div>
                
                {{-- Match Indicator --}}
                <div class="mb-5 text-sm font-bold text-gray-500 text-center">
                    Menemukan <span class="text-[#22AF85] font-black text-lg mx-1">{{ $results->count() }}</span> data relevan
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($results as $spk)
                        <div class="bg-white border border-gray-200/80 hover:border-[#22AF85]/60 hover:shadow-2xl hover:shadow-[#22AF85]/5 transition-all duration-300 transform hover:-translate-y-1 rounded-2xl p-5 flex flex-col group relative overflow-hidden">
                            
                            {{-- Decorative top bar --}}
                            <div class="absolute top-0 left-0 w-full h-1 bg-gray-100 group-hover:bg-[#22AF85] transition-colors duration-300"></div>

                            {{-- Card Header --}}
                            <div class="flex justify-between items-start gap-4 mb-3 flex-grow-0 pt-1">
                                <div class="flex-grow min-w-0">
                                    {{-- SPK & Invoice Badges --}}
                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-[#22AF85]/10 text-[#22AF85] font-extrabold text-[9px] font-mono border border-[#22AF85]/20 uppercase tracking-wide">
                                            SPK: {{ $spk->spk_number }}
                                        </span>
                                        @if($spk->invoice)
                                            <a href="{{ route('finance.invoices.show', $spk->invoice->id) }}" class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-850 transition-colors font-extrabold text-[9px] font-mono border border-blue-150 uppercase tracking-wide cursor-pointer shadow-sm" title="Buka Detail Invoice">
                                                INV: {{ $spk->invoice->invoice_number }}
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-50 text-gray-400 font-bold text-[9px] font-mono border border-gray-200 uppercase tracking-wide">
                                                No Invoice
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Customer Name --}}
                                    <h3 class="text-base font-black text-gray-900 leading-snug truncate" title="{{ optional($spk->customer)->name ?? $spk->customer_name }}">
                                        {{ optional($spk->customer)->name ?? $spk->customer_name }}
                                    </h3>
                                    
                                    {{-- Created Date --}}
                                    <div class="text-[10px] text-gray-400 font-bold mt-0.5 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $spk->entry_date ? $spk->entry_date->translatedFormat('d M Y H:i') : '-' }}
                                    </div>
                                </div>

                                {{-- SPK Cover Image / QR Code --}}
                                <div class="flex-shrink-0 relative">
                                    @if($spk->spk_cover_photo_url)
                                        <img src="{{ $spk->spk_cover_photo_url }}" alt="Cover SPK" class="w-16 h-16 object-cover rounded-xl border border-gray-150 shadow-inner group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-16 h-16 rounded-xl border border-dashed border-gray-200 bg-gray-50/50 flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6 text-gray-300 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-[7px] uppercase tracking-wider text-gray-400 font-bold">No Cover</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Status Badge --}}
                            <div class="mb-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-50 text-amber-800 font-extrabold text-[9px] border border-amber-200/60 font-mono tracking-wide uppercase shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1 animate-pulse"></span>
                                    {{ str_replace('_', ' ', $spk->status->name ?? $spk->status->value) }}
                                </span>
                            </div>

                            {{-- Card Body --}}
                            <div class="space-y-2 mt-1 flex-grow">
                                {{-- Brand Detail --}}
                                <div class="flex items-center gap-2 p-2 bg-gray-50/50 rounded-xl border border-gray-100 text-xs font-semibold text-gray-700">
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    <span class="truncate">{{ $spk->shoe_brand ?? '-' }} ({{ $spk->shoe_color ?? '-' }})</span>
                                </div>

                                {{-- Billing Info Panel (Apple/Stripe Style) --}}
                                @php
                                    $isPaid = false;
                                    $billingStatus = '';
                                    $billingAmount = 0;

                                    if ($spk->invoice) {
                                        $billingStatus = $spk->invoice->status;
                                        $billingAmount = $spk->invoice->remaining_balance;
                                        $isPaid = $billingStatus === 'Lunas';
                                    } else {
                                        $isPaid = $spk->status_pembayaran === 'L';
                                        $billingStatus = $spk->status_pembayaran === 'DP/Cicil' ? 'DP/Cicil' : ($isPaid ? 'Lunas' : 'Belum Bayar');
                                        $billingAmount = $spk->sisa_tagihan;
                                    }
                                @endphp

                                @if($isPaid)
                                    <div class="p-3 bg-emerald-50/60 border border-emerald-100 rounded-xl flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="text-emerald-800 font-extrabold">Lunas (Invoice)</span>
                                        </div>
                                        <span class="text-emerald-600 font-bold font-mono">Rp 0</span>
                                    </div>
                                @else
                                    <div class="p-3 bg-rose-50/40 border border-rose-100 rounded-xl flex flex-col justify-between text-xs">
                                        <div class="flex items-center justify-between">
                                            <span class="text-rose-900 font-extrabold flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                {{ $billingStatus === 'DP/Cicil' ? 'DP / Cicil' : 'Belum Bayar' }}
                                            </span>
                                            <span class="text-rose-600 font-black font-mono text-sm">Rp {{ number_format($billingAmount, 0, ',', '.') }}</span>
                                        </div>
                                        <span class="text-[9px] text-gray-400 font-bold mt-0.5 uppercase tracking-wider">Sisa Tagihan</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Visual Stepper (Identical to show.blade.php but compact and scrollbar-free) --}}
                            @php
                                $flowSteps = [
                                    \App\Enums\WorkOrderStatus::DITERIMA,
                                    \App\Enums\WorkOrderStatus::ASSESSMENT,
                                    \App\Enums\WorkOrderStatus::PREPARATION,
                                    \App\Enums\WorkOrderStatus::PRODUCTION,
                                    \App\Enums\WorkOrderStatus::QC,
                                    \App\Enums\WorkOrderStatus::SELESAI,
                                ];

                                // Map order's actual status to the index of flowSteps
                                $statusVal = $spk->status->value ?? $spk->status;
                                $currentIndex = match($statusVal) {
                                    'SPK_PENDING', 'DITERIMA', 'READY_TO_DISPATCH', 'OTW_WORKSHOP' => 0,
                                    'ASSESSMENT', 'WAITING_PAYMENT', 'WAITING_VERIFICATION' => 1,
                                    'PREPARATION', 'SORTIR' => 2,
                                    'PRODUCTION' => 3,
                                    'QC' => 4,
                                    'SELESAI', 'DIANTAR' => 5,
                                    default => -1,
                                };

                                // Get times for each step
                                $stepTimes = [];

                                // 1. DITERIMA Time
                                $stepTimes[\App\Enums\WorkOrderStatus::DITERIMA->value] = $spk->entry_date ?? $spk->created_at;

                                // 2. ASSESSMENT Time
                                $assessmentLog = $spk->logs->first(function($l) {
                                    return in_array($l->step, ['WAITING_PAYMENT', 'READY_TO_DISPATCH', 'PREPARATION']) 
                                        || $l->action === 'AUTO_PASS_FINANCE'
                                        || str_contains(strtolower($l->description), 'assessment selesai');
                                });
                                $stepTimes[\App\Enums\WorkOrderStatus::ASSESSMENT->value] = $assessmentLog?->created_at;

                                // 3. PREPARATION Time
                                $prepCompletedTimes = array_filter([
                                    $spk->prep_washing_completed_at,
                                    $spk->prep_sol_completed_at,
                                    $spk->prep_upper_completed_at
                                ]);
                                if (!empty($prepCompletedTimes)) {
                                    $stepTimes[\App\Enums\WorkOrderStatus::PREPARATION->value] = \Carbon\Carbon::parse(max($prepCompletedTimes));
                                } else {
                                    $prepLog = $spk->logs->first(function($l) {
                                        return in_array($l->step, ['SORTIR', 'PRODUCTION']) && str_contains(strtolower($l->description), 'preparation selesai');
                                    });
                                    $stepTimes[\App\Enums\WorkOrderStatus::PREPARATION->value] = $prepLog?->created_at;
                                }

                                // 4. PRODUCTION Time
                                $prodCompletedTimes = array_filter([
                                    $spk->prod_sol_completed_at,
                                    $spk->prod_upper_completed_at,
                                    $spk->prod_cleaning_completed_at
                                ]);
                                if (!empty($prodCompletedTimes)) {
                                    $stepTimes[\App\Enums\WorkOrderStatus::PRODUCTION->value] = \Carbon\Carbon::parse(max($prodCompletedTimes));
                                } else {
                                    $prodLog = $spk->logs->first(function($l) {
                                        return $l->step === 'QC' && str_contains(strtolower($l->description), 'menyelesaikan proses prod');
                                    });
                                    $stepTimes[\App\Enums\WorkOrderStatus::PRODUCTION->value] = $prodLog?->created_at;
                                }

                                // 5. QC Time
                                $qcCompletedTimes = array_filter([
                                    $spk->qc_jahit_completed_at,
                                    $spk->qc_cleanup_completed_at,
                                    $spk->qc_final_completed_at
                                ]);
                                if (!empty($qcCompletedTimes)) {
                                    $stepTimes[\App\Enums\WorkOrderStatus::QC->value] = \Carbon\Carbon::parse(max($qcCompletedTimes));
                                } else {
                                    $qcLog = $spk->logs->first(function($l) {
                                        return $l->step === 'SELESAI' && str_contains(strtolower($l->description), 'menyelesaikan proses qc');
                                    });
                                    $stepTimes[\App\Enums\WorkOrderStatus::QC->value] = $qcLog?->created_at;
                                }

                                // 6. SELESAI Time
                                $stepTimes[\App\Enums\WorkOrderStatus::SELESAI->value] = $spk->finished_date;
                            @endphp
                            
                            {{-- Hide default scrollbar styling --}}
                            <style>
                                .scrollbar-none::-webkit-scrollbar {
                                    display: none !important;
                                }
                                .scrollbar-none {
                                    -ms-overflow-style: none !important;
                                    scrollbar-width: none !important;
                                }
                            </style>

                            <div class="mt-5 mb-5 bg-gray-50/30 p-3 rounded-xl border border-gray-100/80 overflow-x-auto scrollbar-none">
                                <div class="flex items-center justify-between min-w-[450px]">
                                    @foreach($flowSteps as $index => $step)
                                        @php
                                            $isCompleted = $index < $currentIndex;
                                            $isActive = $currentIndex === $index;
                                            
                                            // Specific check for showing checkmark on SELESAI step
                                            $showCheckmark = $isCompleted || ($isActive && $step->value === 'SELESAI');
                                        @endphp
                                        <div class="flex flex-col items-center relative flex-1 group">
                                            {{-- Connecting Line --}}
                                            @if(!$loop->last)
                                                <div class="absolute top-2.5 left-1/2 w-full h-[1.5px] transition-colors duration-500 -z-10
                                                    {{ $index < $currentIndex ? 'bg-[#22AF85]' : 'bg-gray-200' }}"></div>
                                            @endif
                                            
                                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[8px] font-bold border-2 mb-1 z-10 transition-all duration-500
                                                {{ $isCompleted || ($isActive && $step->value === 'SELESAI') ? 'bg-[#22AF85] border-[#22AF85] text-white shadow-sm' : '' }}
                                                {{ $isActive && $step->value !== 'SELESAI' ? 'bg-[#22AF85] border-[#22AF85] text-white shadow animate-pulse' : '' }}
                                                {{ !$isCompleted && !$isActive ? 'bg-white border-gray-200 text-gray-400' : '' }}">
                                                @if($showCheckmark)
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    {{ $loop->iteration }}
                                                @endif
                                            </div>
                                            <span class="text-[7px] font-bold uppercase tracking-wider {{ ($isActive || $isCompleted) ? 'text-[#22AF85]' : 'text-gray-400' }}">
                                                {{ str_replace('_', ' ', $step->value) }}
                                            </span>
                                            
                                            {{-- Timestamps --}}
                                            @if(isset($stepTimes[$step->value]) && $stepTimes[$step->value])
                                                <div class="text-center mt-1 select-none leading-none">
                                                    <span class="text-[7px] text-gray-500 font-bold block">
                                                        {{ \Carbon\Carbon::parse($stepTimes[$step->value])->translatedFormat('d M Y') }}
                                                    </span>
                                                    <span class="text-[6.5px] text-gray-400 font-medium block font-mono mt-0.5">
                                                        {{ \Carbon\Carbon::parse($stepTimes[$step->value])->format('H:i') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Card Footer Actions --}}
                            <div class="grid grid-cols-2 gap-3 w-full mt-auto">
                                <a href="{{ $this->getRedirectUrl($spk) }}" class="flex items-center justify-center gap-1.5 py-2.5 px-3 bg-[#FFC232] hover:bg-[#eeb121] text-gray-900 rounded-xl text-xs font-black transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5 truncate border border-[#eeb121]/20">
                                    Stasiun 
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                                <a href="{{ route('admin.orders.show', $spk->id) }}" class="flex items-center justify-center gap-1.5 py-2.5 px-3 bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-600 rounded-xl text-xs font-bold transition-all shadow-sm hover:shadow truncate focus:outline-none focus:ring-4 focus:ring-gray-100" title="Lihat History Keseluruhan">
                                    History
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div x-init="if(window.lastBeep !== '{{ $searchKeyword }}-fail') { playBeep('fail'); window.lastBeep = '{{ $searchKeyword }}-fail'; }"></div>
                <div class="text-center py-20 bg-white border-2 border-gray-200 border-dashed rounded-3xl shadow-sm max-w-3xl mx-auto">
                    <div class="w-24 h-24 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-6 border border-gray-100">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900">SPK Tidak Ditemukan</h3>
                    <p class="mt-3 text-gray-500 font-medium max-w-md mx-auto leading-relaxed">Kami tidak dapat menemukan pencarian <br><span class="text-[#22AF85] font-mono font-bold bg-[#22AF85]/10 px-3 py-1 rounded inline-block mt-2">"{{ $searchKeyword }}"</span></p>
                </div>
            @endif
        @else
            <div class="text-center py-20 opacity-80 flex flex-col items-center justify-center min-h-[40vh] bg-white rounded-3xl border border-gray-100 shadow-sm border-dashed max-w-4xl mx-auto group">
                <div class="w-28 h-28 bg-[#22AF85]/5 rounded-full flex items-center justify-center mb-8 border border-[#22AF85]/10 group-hover:bg-[#22AF85]/10 group-hover:scale-110 transition-all duration-500">
                    <svg class="w-14 h-14 text-[#22AF85]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-400 tracking-widest uppercase">Siap Menerima Scan Barcode</h3>
                <p class="mt-4 text-gray-400 font-medium">Bisa juga ketik manual dengan tekan tombol <kbd class="bg-gray-100 border border-gray-200 px-3 py-1.5 rounded-lg text-gray-600 font-black shadow-sm mx-1 text-sm">/</kbd> di keyboard Anda.</p>
            </div>
        @endif
    </div>
</div>
