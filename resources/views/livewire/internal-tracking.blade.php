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
                                    'text' => 'text-emerald-800',
                                ],
                                'PRODUCTION' => [
                                    'badge' => 'bg-blue-50 text-blue-800 border-blue-200/60',
                                    'dot' => 'bg-blue-500',
                                    'text' => 'text-blue-800',
                                ],
                                'QC' => [
                                    'badge' => 'bg-purple-50 text-purple-800 border-purple-200/60',
                                    'dot' => 'bg-purple-500',
                                    'text' => 'text-purple-800',
                                ],
                                'PREPARATION', 'SORTIR' => [
                                    'badge' => 'bg-indigo-50 text-indigo-800 border-indigo-200/60',
                                    'dot' => 'bg-indigo-500',
                                    'text' => 'text-indigo-800',
                                ],
                                'ASSESSMENT', 'WAITING_PAYMENT', 'WAITING_VERIFICATION' => [
                                    'badge' => 'bg-amber-50 text-amber-800 border-amber-200/60',
                                    'dot' => 'bg-amber-500',
                                    'text' => 'text-amber-800',
                                ],
                                default => [
                                    'badge' => 'bg-slate-50 text-slate-800 border-slate-200/60',
                                    'dot' => 'bg-slate-500',
                                    'text' => 'text-slate-900',
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

                        <div wire:key="spk-{{ $spk->id }}" class="bg-white border border-slate-200 hover:border-[#22AF85]/50 hover:shadow-xl transition-all duration-500 rounded-2xl flex flex-col group relative overflow-hidden transform hover:-translate-y-1">
                            
                            {{-- Top Colored Accent Line --}}
                            <div class="absolute top-0 left-0 w-full h-1 bg-slate-100 group-hover:bg-[#22AF85] transition-colors duration-300 z-10"></div>

                            {{-- SPK Cover Photo Header (Full Width h-32) --}}
                            <div class="relative h-32 w-full overflow-hidden bg-slate-50 border-b border-slate-100 flex-shrink-0">
                                @if($spk->spk_cover_photo_url)
                                    <button 
                                        @click="lightboxUrl = '{{ $spk->spk_cover_photo_url }}'"
                                        class="w-full h-full focus:outline-none cursor-pointer group/img relative block"
                                        title="Klik untuk memperbesar"
                                    >
                                        <img src="{{ $spk->spk_cover_photo_url }}" alt="Cover SPK" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute inset-0 bg-black/10 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </div>
                                    </button>
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-350 bg-slate-50">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Card Contents Wrapper --}}
                            <div class="p-5 flex flex-col flex-grow">
                                {{-- Card Header: SPK & Invoice Badges --}}
                                <div class="flex flex-col items-start gap-2 mb-4 select-none">
                                    {{-- Row 1: SPK Badge --}}
                                    <div x-data="{ 
                                        copied: false,
                                        copyText() {
                                            const text = '{{ $spk->spk_number }}';
                                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                                navigator.clipboard.writeText(text);
                                            } else {
                                                const el = document.createElement('textarea');
                                                el.value = text;
                                                document.body.appendChild(el);
                                                el.select();
                                                document.execCommand('copy');
                                                document.body.removeChild(el);
                                            }
                                            this.copied = true;
                                            setTimeout(() => this.copied = false, 1500);
                                        }
                                    }" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-[#eefcf7] text-[#0f766e] font-mono text-xs font-bold rounded-lg border border-[#ccfbf1] shadow-sm relative whitespace-nowrap">
                                        <span>{{ $spk->spk_number }}</span>
                                        <button @click="copyText()" class="hover:text-[#042f2e] focus:outline-none transition-colors cursor-pointer flex items-center justify-center" title="Salin SPK">
                                            <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                            <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                        <div x-show="copied" x-cloak class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-0.5 bg-slate-800 text-white text-[9px] font-bold rounded-lg shadow-md" style="width: max-content !important; min-width: max-content !important; white-space: nowrap !important; z-index: 30;">Disalin!</div>
                                    </div>

                                    {{-- Row 2: Invoice Badge --}}
                                    @if($spk->invoice)
                                        <div x-data="{ 
                                            copied: false,
                                            copyText() {
                                                const text = '{{ $spk->invoice->invoice_number }}';
                                                if (navigator.clipboard && navigator.clipboard.writeText) {
                                                    navigator.clipboard.writeText(text);
                                                } else {
                                                    const el = document.createElement('textarea');
                                                    el.value = text;
                                                    document.body.appendChild(el);
                                                    el.select();
                                                    document.execCommand('copy');
                                                    document.body.removeChild(el);
                                                }
                                                this.copied = true;
                                                setTimeout(() => this.copied = false, 1500);
                                            }
                                        }" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-[#2b5998] font-mono text-xs font-bold rounded-lg border border-blue-100 shadow-sm relative whitespace-nowrap">
                                            <a href="{{ route('finance.invoices.show', $spk->invoice->id) }}" class="hover:underline" title="Buka Detail Invoice">
                                                {{ $spk->invoice->invoice_number }}
                                            </a>
                                            <button @click="copyText()" class="hover:text-blue-950 focus:outline-none transition-colors cursor-pointer flex items-center justify-center" title="Salin Invoice">
                                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                            <div x-show="copied" x-cloak class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-0.5 bg-slate-800 text-white text-[9px] font-bold rounded-lg shadow-md z-30" style="width: max-content !important; min-width: max-content !important; white-space: nowrap !important;">Disalin!</div>
                                        </div>
                                    @else
                                        <div class="px-2.5 py-1 bg-slate-50 text-slate-400 font-mono text-xs font-semibold rounded-lg border border-slate-100 shadow-sm whitespace-nowrap select-none">
                                            Belum Rilis
                                        </div>
                                    @endif
                                </div>

                                {{-- Customer Profile --}}
                                <div class="mb-4">
                                    <h3 class="text-lg font-extrabold text-slate-900 leading-snug truncate" title="{{ optional($spk->customer)->name ?? $spk->customer_name ?? '-' }}">
                                        {{ optional($spk->customer)->name ?? $spk->customer_name ?? '-' }}
                                    </h3>
                                    
                                    @if($phone)
                                        <div class="flex items-center gap-1.5 mt-1 text-xs font-semibold text-slate-500 hover:text-[#22AF85] transition-colors duration-200 group/wa">
                                            <svg class="w-3.5 h-3.5 text-slate-450 group-hover/wa:text-[#22AF85] transition-colors flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.73-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.42 9.864-9.864.002-2.637-1.03-5.114-2.905-6.99C16.486 1.88 14.021.845 12.012.845c-5.437 0-9.866 4.418-9.87 9.862-.001 1.702.461 3.351 1.341 4.771l-.98 3.586 3.673-.963zm10.741-6.937c-.3-.15-1.774-.875-2.046-.974-.273-.1-.472-.15-.671.15-.198.3-.77.974-.944 1.173-.173.2-.347.225-.647.075-.3-.15-1.266-.466-2.41-1.487-.89-.794-1.49-1.774-1.664-2.074-.173-.3-.018-.462.13-.61.135-.13.3-.349.45-.523.15-.174.2-.3.3-.5.1-.2.05-.374-.025-.524-.075-.15-.671-1.62-.92-2.22-.242-.584-.487-.504-.671-.514-.172-.01-.371-.01-.57-.01-.2 0-.526.075-.801.374-.275.3-1.05 1.024-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.224 5.116 4.522.715.31 1.273.495 1.71.635.72.23 1.375.197 1.892.12.576-.087 1.774-.726 2.022-1.43.247-.704.247-1.306.173-1.43-.075-.124-.273-.198-.572-.348z"/>
                                            </svg>
                                            <a href="{{ $waLink }}" target="_blank" class="truncate" title="Hubungi via WhatsApp">
                                                {{ $phone }}
                                            </a>
                                            
                                            <div x-data="{ 
                                                copied: false,
                                                copyText() {
                                                    const text = '{{ $phone }}';
                                                    if (navigator.clipboard && navigator.clipboard.writeText) {
                                                        navigator.clipboard.writeText(text);
                                                    } else {
                                                        const el = document.createElement('textarea');
                                                        el.value = text;
                                                        document.body.appendChild(el);
                                                        el.select();
                                                        document.execCommand('copy');
                                                        document.body.removeChild(el);
                                                    }
                                                    this.copied = true;
                                                    setTimeout(() => this.copied = false, 1500);
                                                }
                                            }" class="relative flex items-center">
                                                <button 
                                                    @click="copyText()"
                                                    class="p-0.5 rounded text-slate-400 hover:text-emerald-600 transition-colors cursor-pointer flex items-center justify-center"
                                                    title="Salin No WhatsApp"
                                                >
                                                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                    <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                                <div x-show="copied" x-cloak class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-0.5 bg-slate-800 text-white text-[9px] font-bold rounded-lg shadow-md whitespace-nowrap z-30" style="width: max-content !important; min-width: max-content !important; white-space: nowrap !important;">Disalin!</div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="text-[10px] text-slate-400 font-medium flex items-center gap-1.5 mt-1 select-none">
                                        <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>Masuk: {{ $spk->entry_date ? $spk->entry_date->translatedFormat('d M Y H:i') : '-' }}</span>
                                    </div>
                                </div>

                                {{-- Brand & Shoe Info --}}
                                <div class="px-3.5 py-2 bg-[#f0f4f9] rounded-xl text-xs text-slate-700 font-bold flex items-center gap-2 select-none mb-2.5">
                                    <span class="text-slate-900 font-extrabold">{{ $spk->shoe_brand ?? '-' }}</span>
                                    <span class="w-1 h-1 rounded-full bg-slate-350"></span>
                                    <span class="text-slate-500 font-semibold">{{ $spk->shoe_color ?? '-' }}</span>
                                </div>

                                {{-- Unified Payment / Billing Status Info Panel --}}
                                @if($isPaid)
                                    <div class="px-3.5 py-2.5 bg-emerald-50/50 border border-emerald-100 rounded-xl flex items-center justify-between mb-3 shadow-sm select-none">
                                        <span class="inline-flex items-center gap-1.5 text-emerald-800 text-[10px] font-black tracking-wider uppercase">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Lunas
                                        </span>
                                        <span class="text-emerald-700 font-extrabold font-mono text-sm">Rp 0</span>
                                    </div>
                                @else
                                    <div class="px-3.5 py-2.5 bg-rose-50/50 border border-rose-100 rounded-xl flex items-center justify-between mb-3 shadow-sm select-none">
                                        <span class="inline-flex items-center gap-1.5 text-rose-800 text-[10px] font-black tracking-wider uppercase">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                            {{ $billingStatus === 'DP/Cicil' ? 'DP / CICIL' : 'BELUM BAYAR' }}
                                        </span>
                                        <span class="text-rose-700 font-extrabold font-mono text-sm">Rp {{ number_format($billingAmount, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                {{-- Status Info & Transition Timestamp --}}
                                <div class="px-3.5 py-3 bg-[#fafbfc] border border-slate-100 rounded-xl flex items-center justify-between mb-4 mt-2 select-none w-full">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Status Sekarang</span>
                                        <span class="inline-flex items-center gap-1.5 text-xs font-black {{ $statusTheme['text'] ?? 'text-slate-700' }} uppercase tracking-wide mt-0.5">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $statusTheme['dot'] ?? 'bg-slate-500' }} animate-pulse"></span>
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <div class="text-right flex flex-col">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Waktu Status</span>
                                        @php
                                            $statusLog = $spk->logs ? $spk->logs->where('step', $statusVal)->sortByDesc('created_at')->first() : null;
                                            $statusTime = $statusLog ? $statusLog->created_at : $spk->updated_at;
                                        @endphp
                                        <span class="text-xs font-black text-slate-900 mt-0.5">
                                            {{ $statusTime ? $statusTime->format('d/m/Y') : '-' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Card Footer Actions (STASIUN and HISTORY buttons side-by-side) --}}
                                <div class="grid grid-cols-2 gap-3 mt-auto select-none w-full">
                                    <a href="{{ $this->getRedirectUrl($spk) }}" class="flex items-center justify-center gap-1.5 py-3.5 px-4 bg-amber-500 hover:bg-amber-600 text-amber-950 rounded-xl text-xs font-black transition-all shadow-md transform hover:-translate-y-0.5 border border-amber-600/20 tracking-wider uppercase">
                                        Stasiun 
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>
                                    <a href="{{ route('admin.orders.show', $spk->id) }}" class="flex items-center justify-center gap-1.5 py-3.5 px-4 bg-white border border-slate-200 hover:border-slate-350 hover:bg-slate-50 text-slate-650 rounded-xl text-xs font-black transition-all shadow-sm tracking-wider uppercase" title="Lihat History Keseluruhan">
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
