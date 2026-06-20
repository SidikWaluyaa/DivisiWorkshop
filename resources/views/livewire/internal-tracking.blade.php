<div class="px-4 pb-12 pt-8 sm:px-6 lg:px-8 max-w-7xl mx-auto"
    x-data="{
        lightboxUrl: '',
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
    <style>
        .scrollbar-none::-webkit-scrollbar {
            display: none !important;
        }
        .scrollbar-none {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
    </style>

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
                        @php
                            // 1. Determine Payment/Billing info
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

                            // 2. Determine status code and colors
                            $statusVal = $spk->status->value ?? $spk->status;

                            $statusLabel = str_replace('_', ' ', $spk->status->name ?? $spk->status->value);
                            $statusTheme = match($statusVal) {
                                'SELESAI', 'DIANTAR' => [
                                    'badge' => 'bg-emerald-50 text-emerald-800 border-emerald-200/60',
                                    'dot' => 'bg-emerald-500',
                                ],
                                'PRODUCTION' => [
                                    'badge' => 'bg-blue-50 text-blue-800 border-blue-200/60',
                                    'dot' => 'bg-blue-500',
                                ],
                                'QC' => [
                                    'badge' => 'bg-purple-50 text-purple-800 border-purple-200/60',
                                    'dot' => 'bg-purple-500',
                                ],
                                'PREPARATION', 'SORTIR' => [
                                    'badge' => 'bg-indigo-50 text-indigo-800 border-indigo-200/60',
                                    'dot' => 'bg-indigo-500',
                                ],
                                'ASSESSMENT', 'WAITING_PAYMENT', 'WAITING_VERIFICATION' => [
                                    'badge' => 'bg-amber-50 text-amber-800 border-amber-200/60',
                                    'dot' => 'bg-amber-500',
                                ],
                                default => [
                                    'badge' => 'bg-slate-50 text-slate-800 border-slate-200/60',
                                    'dot' => 'bg-slate-550',
                                ],
                            };
                            
                            // WhatsApp direct link builder
                            $phone = $spk->customer_phone ?? optional($spk->customer)->phone ?? '';
                            $waLink = '#';
                            if ($phone) {
                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                if (str_starts_with($cleanPhone, '08')) {
                                    $cleanPhone = '628' . substr($cleanPhone, 2);
                                }
                                $waLink = 'https://wa.me/' . $cleanPhone;
                            }
                        @endphp

                        <div class="bg-white border border-gray-200/80 hover:border-[#22AF85]/50 hover:shadow-2xl hover:shadow-slate-200/60 transition-all duration-300 transform hover:-translate-y-1.5 rounded-2xl flex flex-col group relative">
                            
                            {{-- Ticket Header (Split Top Panel) --}}
                            <div class="bg-gradient-to-br from-slate-50 to-slate-100/50 p-4.5 rounded-t-2xl flex flex-col gap-2.5 flex-shrink-0 relative overflow-hidden">
                                {{-- Subtle background logo/watermark for premium look --}}
                                <div class="absolute -right-3 -top-3 w-16 h-16 text-slate-200/40 pointer-events-none group-hover:text-[#22AF85]/10 transition-colors duration-300">
                                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                    </svg>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    {{-- Left Column: SPK Tag details --}}
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="text-[8px] font-black tracking-widest text-slate-400 uppercase">SPK NUMBER</span>
                                        <div class="flex items-center gap-1">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50/80 text-emerald-700 font-extrabold text-[10px] font-mono border border-emerald-150 shadow-sm uppercase tracking-wider" title="Nomor SPK">
                                                {{ $spk->spk_number }}
                                            </span>
                                            {{-- Copy SPK --}}
                                            <div x-data="{ copied: false }" class="relative flex items-center">
                                                <button 
                                                    @click="
                                                        navigator.clipboard.writeText('{{ $spk->spk_number }}');
                                                        copied = true;
                                                        setTimeout(() => copied = false, 1500);
                                                    "
                                                    class="p-1 rounded bg-white hover:bg-emerald-50 text-slate-450 hover:text-emerald-700 border border-slate-200 shadow-sm active:scale-95 transition-all cursor-pointer"
                                                    title="Salin Nomor SPK"
                                                >
                                                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                    <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                                <div x-show="copied" x-cloak x-transition class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-1.5 py-0.5 bg-slate-800 text-white text-[8px] font-bold rounded shadow-sm whitespace-nowrap z-30">
                                                    Disalin!
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Right Column: Invoice Tag details --}}
                                    <div class="flex flex-col gap-1 items-end">
                                        <span class="text-[8px] font-black tracking-widest text-slate-400 uppercase">INVOICE ID</span>
                                        <div class="flex items-center gap-1">
                                            @if($spk->invoice)
                                                <a href="{{ route('finance.invoices.show', $spk->invoice->id) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800 transition-all font-extrabold text-[10px] font-mono border border-blue-150 shadow-sm uppercase tracking-wider cursor-pointer mr-1" title="Buka Detail Invoice">
                                                    {{ $spk->invoice->invoice_number }}
                                                </a>
                                                {{-- Copy Invoice --}}
                                                <div x-data="{ copied: false }" class="relative flex items-center">
                                                    <button 
                                                        @click="
                                                            navigator.clipboard.writeText('{{ $spk->invoice->invoice_number }}');
                                                            copied = true;
                                                            setTimeout(() => copied = false, 1500);
                                                        "
                                                        class="p-1 rounded bg-white hover:bg-blue-50 text-slate-450 hover:text-blue-700 border border-slate-200 shadow-sm active:scale-95 transition-all cursor-pointer"
                                                        title="Salin Nomor Invoice"
                                                    >
                                                        <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                        <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </button>
                                                    <div x-show="copied" x-cloak x-transition class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-1.5 py-0.5 bg-slate-800 text-white text-[8px] font-bold rounded shadow-sm whitespace-nowrap z-30">
                                                        Disalin!
                                                    </div>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-400 font-bold text-[10px] font-mono border border-slate-200 uppercase tracking-wider">
                                                    Belum Rilis
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Ticket Separator Line with Left & Right Cutouts --}}
                            <div class="relative flex items-center justify-between h-4 bg-white select-none">
                                <!-- Left Notch Cutout (blends with page background bg-gray-100) -->
                                <div class="absolute -left-[8px] top-1/2 -translate-y-1/2 w-4 h-4 bg-gray-100 border border-gray-200 rounded-full z-20"></div>
                                <!-- Dashed Divider Line -->
                                <div class="w-full border-t-2 border-dashed border-slate-150 mx-3"></div>
                                <!-- Right Notch Cutout (blends with page background bg-gray-100) -->
                                <div class="absolute -right-[8px] top-1/2 -translate-y-1/2 w-4 h-4 bg-gray-100 border border-gray-200 rounded-full z-20"></div>
                            </div>

                            {{-- Ticket Body --}}
                            <div class="p-5 flex flex-col flex-grow bg-white rounded-b-2xl">
                                
                                {{-- Customer Profile (Left Image, Right Txt) --}}
                                <div class="flex items-center gap-4 mb-4 flex-grow-0">
                                    {{-- Left Side: Image with hover effect & magnifier --}}
                                    @if($spk->spk_cover_photo_url)
                                        <button 
                                            @click="lightboxUrl = '{{ $spk->spk_cover_photo_url }}'"
                                            class="w-16 h-16 rounded-2xl border border-slate-100 shadow-md shadow-slate-100 overflow-hidden flex-shrink-0 focus:outline-none focus:ring-2 focus:ring-[#22AF85]/30 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-lg relative group/img"
                                            title="Klik untuk memperbesar"
                                        >
                                            <img src="{{ $spk->spk_cover_photo_url }}" alt="Cover SPK" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover/img:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                            </div>
                                        </button>
                                    @else
                                        <div class="w-16 h-16 rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 flex flex-col items-center justify-center text-slate-400 flex-shrink-0">
                                            <svg class="w-6 h-6 text-slate-300 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-[7.5px] uppercase tracking-wider text-slate-450 font-bold">No Cover</span>
                                        </div>
                                    @endif

                                    {{-- Right Side: Name & Contact --}}
                                    <div class="flex-grow min-w-0">
                                        <h3 class="text-base font-extrabold text-slate-900 leading-tight truncate mb-1" title="{{ optional($spk->customer)->name ?? $spk->customer_name }}">
                                            {{ optional($spk->customer)->name ?? $spk->customer_name }}
                                        </h3>
                                        
                                        @if($phone)
                                            <div class="flex items-center gap-1.5 mb-1">
                                                <a href="{{ $waLink }}" target="_blank" class="text-xs text-slate-500 hover:text-[#22AF85] font-bold flex items-center gap-1 transition-colors duration-200 group/wa" title="Hubungi via WhatsApp">
                                                    <svg class="w-3.5 h-3.5 text-slate-450 group-hover/wa:text-[#22AF85] transition-colors flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.73-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.42 9.864-9.864.002-2.637-1.03-5.114-2.905-6.99C16.486 1.88 14.021.845 12.012.845c-5.437 0-9.866 4.418-9.87 9.862-.001 1.702.461 3.351 1.341 4.771l-.98 3.586 3.673-.963zm10.741-6.937c-.3-.15-1.774-.875-2.046-.974-.273-.1-.472-.15-.671.15-.198.3-.77.974-.944 1.173-.173.2-.347.225-.647.075-.3-.15-1.266-.466-2.41-1.487-.89-.794-1.49-1.774-1.664-2.074-.173-.3-.018-.462.13-.61.135-.13.3-.349.45-.523.15-.174.2-.3.3-.5.1-.2.05-.374-.025-.524-.075-.15-.671-1.62-.92-2.22-.242-.584-.487-.504-.671-.514-.172-.01-.371-.01-.57-.01-.2 0-.526.075-.801.374-.275.3-1.05 1.024-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.224 5.116 4.522.715.31 1.273.495 1.71.635.72.23 1.375.197 1.892.12.576-.087 1.774-.726 2.022-1.43.247-.704.247-1.306.173-1.43-.075-.124-.273-.198-.572-.348z"/>
                                                    </svg>
                                                    <span class="truncate">{{ $phone }}</span>
                                                </a>
                                                {{-- Copy WhatsApp number --}}
                                                <div x-data="{ copied: false }" class="relative flex items-center">
                                                    <button 
                                                        @click="
                                                            navigator.clipboard.writeText('{{ $phone }}');
                                                            copied = true;
                                                            setTimeout(() => copied = false, 1500);
                                                        "
                                                        class="p-0.5 rounded text-slate-450 hover:text-emerald-700 hover:bg-emerald-50/50 active:scale-95 transition-all cursor-pointer"
                                                        title="Salin No WhatsApp"
                                                    >
                                                        <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 022 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                        <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </button>
                                                    <div x-show="copied" x-cloak x-transition class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-1.5 py-0.5 bg-slate-800 text-white text-[8px] font-bold rounded shadow-sm whitespace-nowrap z-30">
                                                        Disalin!
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-xs text-slate-400 font-bold flex items-center gap-1.5 mb-1">
                                                <svg class="w-3.5 h-3.5 text-slate-350" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                                <span>-</span>
                                            </div>
                                        @endif

                                        <div class="text-[10px] text-slate-400 font-semibold flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="truncate">{{ $spk->entry_date ? $spk->entry_date->translatedFormat('d M Y H:i') : '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Brand Info Capsule --}}
                                <div class="px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-xs text-slate-700 font-extrabold flex items-center justify-center gap-2 mb-3.5 hover:bg-slate-100/70 transition-colors duration-200">
                                    <svg class="w-4 h-4 text-slate-450 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    <span class="truncate">{{ $spk->shoe_brand ?? '-' }}</span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                    <span class="truncate text-slate-500 font-medium">{{ $spk->shoe_color ?? '-' }}</span>
                                </div>

                                {{-- Stripe-style Unified Status & Billing Info Panel --}}
                                <div class="mb-4">
                                    @if($isPaid)
                                        <div class="px-4 py-3 bg-gradient-to-r from-emerald-500/10 to-teal-500/5 border border-emerald-500/15 rounded-xl flex items-center justify-between shadow-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500 text-white shadow-sm shadow-emerald-500/20">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </span>
                                                <span class="text-emerald-800 font-extrabold text-xs uppercase tracking-wider">Lunas</span>
                                            </div>
                                            <span class="text-emerald-700 font-extrabold font-mono text-sm">Rp 0</span>
                                        </div>
                                    @else
                                        <div class="px-4 py-3 bg-gradient-to-r from-rose-500/10 to-orange-500/5 border border-rose-500/15 rounded-xl flex items-center justify-between shadow-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-rose-500 text-white shadow-sm shadow-rose-500/20">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                </span>
                                                <span class="text-rose-900 font-extrabold text-xs uppercase tracking-wider">{{ $billingStatus === 'DP/Cicil' ? 'DP / Cicil' : 'Belum Bayar' }}</span>
                                            </div>
                                            <div class="flex flex-col items-end leading-none">
                                                <span class="text-rose-600 font-extrabold font-mono text-sm">Rp {{ number_format($billingAmount, 0, ',', '.') }}</span>
                                                <span class="text-[7.5px] text-slate-450 font-bold uppercase tracking-wider mt-0.5">Sisa Tagihan</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Visual Stepper --}}
                                @php
                                    $flowSteps = [
                                        \App\Enums\WorkOrderStatus::DITERIMA,
                                        \App\Enums\WorkOrderStatus::ASSESSMENT,
                                        \App\Enums\WorkOrderStatus::PREPARATION,
                                        \App\Enums\WorkOrderStatus::PRODUCTION,
                                        \App\Enums\WorkOrderStatus::QC,
                                        \App\Enums\WorkOrderStatus::SELESAI,
                                    ];
                                    $currentIndex = match($statusVal) {
                                        'SPK_PENDING', 'DITERIMA', 'READY_TO_DISPATCH', 'OTW_WORKSHOP' => 0,
                                        'ASSESSMENT', 'WAITING_PAYMENT', 'WAITING_VERIFICATION' => 1,
                                        'PREPARATION', 'SORTIR' => 2,
                                        'PRODUCTION' => 3,
                                        'QC' => 4,
                                        'SELESAI', 'DIANTAR' => 5,
                                        default => -1,
                                    };
                                    $stepTimes = [];
                                    $stepTimes[\App\Enums\WorkOrderStatus::DITERIMA->value] = $spk->entry_date ?? $spk->created_at;
                                    $assessmentLog = $spk->logs->first(function($l) { return in_array($l->step, ['WAITING_PAYMENT', 'READY_TO_DISPATCH', 'PREPARATION']) || $l->action === 'AUTO_PASS_FINANCE' || str_contains(strtolower($l->description), 'assessment selesai'); });
                                    $stepTimes[\App\Enums\WorkOrderStatus::ASSESSMENT->value] = $assessmentLog?->created_at;
                                    $prepCompletedTimes = array_filter([$spk->prep_washing_completed_at, $spk->prep_sol_completed_at, $spk->prep_upper_completed_at]);
                                    if (!empty($prepCompletedTimes)) { $stepTimes[\App\Enums\WorkOrderStatus::PREPARATION->value] = \Carbon\Carbon::parse(max($prepCompletedTimes)); }
                                    else { $prepLog = $spk->logs->first(function($l) { return in_array($l->step, ['SORTIR', 'PRODUCTION']) && str_contains(strtolower($l->description), 'preparation selesai'); }); $stepTimes[\App\Enums\WorkOrderStatus::PREPARATION->value] = $prepLog?->created_at; }
                                    $prodCompletedTimes = array_filter([$spk->prod_sol_completed_at, $spk->prod_upper_completed_at, $spk->prod_cleaning_completed_at]);
                                    if (!empty($prodCompletedTimes)) { $stepTimes[\App\Enums\WorkOrderStatus::PRODUCTION->value] = \Carbon\Carbon::parse(max($prodCompletedTimes)); }
                                    else { $prodLog = $spk->logs->first(function($l) { return $l->step === 'QC' && str_contains(strtolower($l->description), 'menyelesaikan proses prod'); }); $stepTimes[\App\Enums\WorkOrderStatus::PRODUCTION->value] = $prodLog?->created_at; }
                                    $qcCompletedTimes = array_filter([$spk->qc_jahit_completed_at, $spk->qc_cleanup_completed_at, $spk->qc_final_completed_at]);
                                    if (!empty($qcCompletedTimes)) { $stepTimes[\App\Enums\WorkOrderStatus::QC->value] = \Carbon\Carbon::parse(max($qcCompletedTimes)); }
                                    else { $qcLog = $spk->logs->first(function($l) { return $l->step === 'SELESAI' && str_contains(strtolower($l->description), 'menyelesaikan proses qc'); }); $stepTimes[\App\Enums\WorkOrderStatus::QC->value] = $qcLog?->created_at; }
                                    $stepTimes[\App\Enums\WorkOrderStatus::SELESAI->value] = $spk->finished_date;
                                @endphp

                                <div class="mb-4 bg-slate-50/50 p-3 rounded-xl border border-slate-100/85 overflow-x-auto scrollbar-none">
                                    <div class="flex items-center justify-between min-w-[450px]">
                                        @foreach($flowSteps as $index => $step)
                                            @php
                                                $isCompleted = $index < $currentIndex;
                                                $isActive = $currentIndex === $index;
                                                $showCheckmark = $isCompleted || ($isActive && $step->value === 'SELESAI');
                                            @endphp
                                            <div class="flex flex-col items-center relative flex-1 group/step">
                                                @if(!$loop->last)
                                                    <div class="absolute top-2.5 left-1/2 w-full h-[2px] transition-colors duration-500 -z-10 {{ $index < $currentIndex ? 'bg-[#22AF85]' : 'bg-slate-200' }}"></div>
                                                @endif
                                                
                                                <div class="w-4.5 h-4.5 rounded-full flex items-center justify-center text-[7px] font-extrabold border mb-1 z-10 transition-all duration-500
                                                    {{ $isCompleted || ($isActive && $step->value === 'SELESAI') ? 'bg-[#22AF85] border-[#22AF85] text-white shadow-sm shadow-[#22AF85]/30' : '' }}
                                                    {{ $isActive && $step->value !== 'SELESAI' ? 'bg-[#22AF85] border-[#22AF85] text-white shadow-md shadow-[#22AF85]/40 animate-pulse' : '' }}
                                                    {{ !$isCompleted && !$isActive ? 'bg-white border-slate-200 text-slate-400' : '' }}">
                                                    @if($showCheckmark)
                                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4.5" d="M5 13l4 4L19 7"></path></svg>
                                                    @else
                                                        {{ $loop->iteration }}
                                                    @endif
                                                </div>
                                                <span class="text-[7px] font-black uppercase tracking-wider {{ ($isActive || $isCompleted) ? 'text-[#22AF85]' : 'text-slate-400' }}">
                                                    {{ str_replace('_', ' ', $step->value) }}
                                                </span>
                                                
                                                @if(isset($stepTimes[$step->value]) && $stepTimes[$step->value])
                                                    <div class="text-center select-none leading-none mt-1">
                                                        <span class="text-[6.5px] text-slate-500 font-extrabold block">
                                                            {{ \Carbon\Carbon::parse($stepTimes[$step->value])->translatedFormat('d M') }}
                                                        </span>
                                                        <span class="text-[6px] text-slate-400 font-semibold block font-mono">
                                                            {{ \Carbon\Carbon::parse($stepTimes[$step->value])->format('H:i') }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Current Status Pill --}}
                                <div class="flex justify-center mb-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-extrabold border uppercase tracking-widest shadow-sm {{ $statusTheme['badge'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusTheme['dot'] }} mr-1 animate-pulse"></span>
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                {{-- Card Footer Actions --}}
                                <div class="grid grid-cols-2 gap-3 mt-auto">
                                    <a href="{{ $this->getRedirectUrl($spk) }}" class="flex items-center justify-center gap-1.5 py-3 px-4 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-slate-900 rounded-xl text-xs font-black transition-all shadow-md shadow-amber-500/10 hover:shadow-lg hover:shadow-amber-500/20 transform hover:-translate-y-0.5 border border-amber-500/20 tracking-wider uppercase group-hover:border-amber-600/30">
                                        Stasiun 
                                        <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>
                                    <a href="{{ route('admin.orders.show', $spk->id) }}" class="flex items-center justify-center gap-1.5 py-3 px-4 bg-white border border-slate-200/80 hover:border-slate-300 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-black transition-all shadow-sm hover:shadow tracking-wider uppercase" title="Lihat History Keseluruhan">
                                        History
                                    </a>
                                </div>
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

    {{-- Premium Lightbox Modal --}}
    <div 
        x-show="lightboxUrl" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-sm"
        @keydown.escape.window="lightboxUrl = ''"
        style="display: none;"
    >
        <div 
            class="relative max-w-4xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl border border-gray-100"
            @click.away="lightboxUrl = ''"
        >
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-black text-gray-900">Cover SPK Original</h3>
                <button 
                    @click="lightboxUrl = ''"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none p-1.5 hover:bg-gray-100 rounded-xl transition-all"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            {{-- Image container --}}
            <div class="p-6 bg-gray-50 flex items-center justify-center min-h-[300px] max-h-[70vh] overflow-y-auto">
                <img :src="lightboxUrl" alt="Zoomed Cover SPK" class="max-w-full max-h-[60vh] object-contain rounded-2xl shadow-md border border-gray-200">
            </div>
            
            {{-- Footer --}}
            <div class="px-6 py-4 bg-white border-t border-gray-100 flex justify-end">
                <button 
                    @click="lightboxUrl = ''"
                    class="py-2.5 px-5 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-xs font-black transition-all shadow-sm"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
