<div>
    {{-- Floating Trigger Button (FAB Icon Saja di Pojok Kanan Bawah) --}}
    <div class="fixed bottom-6 right-6 z-50">
        <button 
            type="button"
            wire:click="toggleDrawer"
            class="group relative w-14 h-14 rounded-full bg-gradient-to-tr from-teal-600 via-teal-500 to-emerald-500 text-white shadow-xl shadow-teal-700/25 hover:shadow-2xl hover:shadow-teal-500/40 hover:scale-105 active:scale-95 transition-all duration-300 border-2 border-white/80 flex items-center justify-center focus:outline-none"
            title="Buka Workshop AI Copilot"
            aria-label="Workshop AI Copilot"
        >
            {{-- Pulsing Live Beacon Indicator --}}
            <span class="absolute top-0.5 right-0.5 flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-80"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-400 border-2 border-white shadow-xs"></span>
            </span>

            {{-- Modern AI Sparkles Icon --}}
            <svg class="w-6 h-6 text-white transition-transform duration-300 group-hover:rotate-12 group-hover:scale-110 drop-shadow-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
            </svg>
        </button>
    </div>

    {{-- Slide-over Right Drawer Container --}}
    @if($isOpen)
        {{-- Backdrop Dim Overlay (Click to close) --}}
        <div 
            class="fixed inset-0 bg-slate-900/30 backdrop-blur-[2px] z-[998] transition-opacity duration-300 animate-in fade-in"
            wire:click="closeDrawer"
            aria-hidden="true"
        ></div>

        {{-- Full-height Slide-over Panel from Right --}}
        <div 
            class="fixed inset-y-0 right-0 w-full sm:w-[500px] md:w-[540px] lg:w-[580px] h-full bg-white shadow-2xl border-l border-slate-200/80 z-[999] flex flex-col transition-all duration-300 animate-in slide-in-from-right text-slate-800"
            x-data="{
                lightboxOpen: false,
                activePhotoUrl: '',
                activePhotoCaption: '',
                activePhotoBadge: '',
                openLightbox(url, caption = '', badge = '') {
                    this.activePhotoUrl = url;
                    this.activePhotoCaption = caption;
                    this.activePhotoBadge = badge;
                    this.lightboxOpen = true;
                },
                closeLightbox() {
                    this.lightboxOpen = false;
                }
            }"
            x-on:ai-drawer-opened.window="setTimeout(() => { const el = document.getElementById('ai-chat-messages'); if(el) el.scrollTop = el.scrollHeight; }, 100)"
            x-on:scroll-ai-chat-bottom.window="setTimeout(() => { const el = document.getElementById('ai-chat-messages'); if(el) el.scrollTop = el.scrollHeight; }, 100)"
            x-on:trigger-ai-process.window="setTimeout(() => { $wire.processAiResponse(); }, 50)"
            x-on:keydown.escape.window="if(lightboxOpen) { closeLightbox(); } else { $wire.closeDrawer(); }"
        >
            {{-- Header (Official Shoe Workshop Logo & Actions) --}}
            <div class="px-4 sm:px-5 py-3.5 bg-white border-b border-slate-100 flex items-center justify-between gap-2 relative flex-shrink-0 shadow-2xs">
                {{-- Left: Brand & Status --}}
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    {{-- Official Shoe Workshop Logo --}}
                    <div class="w-9 h-9 bg-slate-50 rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-center flex-shrink-0 p-1">
                        <img src="{{ asset('images/logo.png') }}" class="h-full w-auto object-contain" alt="Shoe Workshop Logo" />
                    </div>

                    <div class="min-w-0">
                        <h3 class="text-xs sm:text-sm font-black tracking-tight text-slate-900 font-poppins truncate leading-tight">
                            Workshop AI Copilot
                        </h3>
                        <div class="flex items-center gap-1.5 text-[10px] sm:text-[11px] text-slate-500 font-medium truncate mt-0.5">
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Online</span>
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="truncate text-slate-400">Internal Tracking SPK</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Mode Switcher & Actions --}}
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    {{-- Dynamic Model Selector Dropdown (Gemini Flash Lite, Flash, Groq) --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        {{-- Trigger Badge Button --}}
                        <button 
                            type="button" 
                            @click="open = !open"
                            class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-[11px] font-black border transition-all cursor-pointer shadow-2xs whitespace-nowrap active:scale-95 {{ $useLocalEngine ? 'bg-amber-50 text-amber-950 border-amber-300 hover:bg-amber-100' : 'bg-slate-50 hover:bg-slate-100 text-slate-800 border-slate-200/90' }}"
                            title="Pilih Model AI (Klik untuk ganti model)"
                        >
                            @if($useLocalEngine)
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse flex-shrink-0"></span>
                                <span class="text-amber-950 font-black">⚡ Groq Qwen</span>
                            @else
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 flex-shrink-0"></span>
                                <span class="text-slate-800 font-bold">{{ $this->activeModelLabel }}</span>
                            @endif
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown Popover Menu (UI/UX Pro Max: Floating Glass Card with Rich Card Info) --}}
                        <div 
                            x-show="open" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                            class="absolute right-[-4.5rem] sm:right-0 top-full mt-2 w-[320px] sm:w-[350px] !max-w-[calc(100vw-1.5rem)] bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-200/90 z-[1001] text-left overflow-hidden animate-in fade-in"
                            style="display: none; width: 340px; max-width: min(350px, calc(100vw - 1.5rem));"
                        >
                            {{-- Dropdown Header with Status & Protection Badge --}}
                            <div class="px-3.5 py-2.5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <div class="w-5 h-5 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-black tracking-tight text-slate-800 uppercase font-poppins truncate">Pilih Engine Model</span>
                                </div>
                                <div class="flex items-center gap-1 text-[9px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60 flex-shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Fallback Aktif</span>
                                </div>
                            </div>

                            {{-- Scrollable Models List --}}
                            <div class="p-2 space-y-2.5 max-h-[60vh] sm:max-h-[440px] overflow-y-auto overscroll-contain">
                                {{-- Group 1: Kuota Lega (500 RPD / 15 RPM) --}}
                                <div>
                                    <div class="px-2 py-1 text-[10px] font-black uppercase tracking-wider text-emerald-800 flex items-center justify-between">
                                        <span>🚀 Rekomendasi Kuota Lega</span>
                                        <span class="text-[9px] bg-emerald-100 text-emerald-900 px-1.5 py-0.2 rounded-md font-bold">500 RPD</span>
                                    </div>
                                    
                                    <div class="space-y-1.5 mt-1">
                                        {{-- Gemini 3.5 Flash Lite --}}
                                        <button 
                                            type="button" 
                                            wire:click="selectModel('gemini-3.5-flash-lite')" 
                                            @click="open = false"
                                            class="w-full text-left p-2.5 rounded-xl text-xs transition-all flex items-start justify-between gap-2.5 border active:scale-[0.98] {{ (!$useLocalEngine && $selectedModel === 'gemini-3.5-flash-lite') ? 'bg-teal-50/70 border-teal-300 ring-2 ring-teal-500/20 shadow-2xs' : 'bg-white hover:bg-slate-50 border-slate-200/70 hover:border-slate-300' }}"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-slate-900 leading-tight">Gemini 3.5 Flash Lite</span>
                                                    <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800 font-black">Utama</span>
                                                </div>
                                                <div class="flex items-center gap-1.5 mt-1 text-[10px] text-slate-500 font-medium">
                                                    <span class="bg-slate-100 px-1.5 py-0.2 rounded text-[9px] text-slate-700 font-bold">500 RPD • 15 RPM</span>
                                                    <span class="text-slate-400 truncate">Paling stabil harian</span>
                                                </div>
                                            </div>
                                            @if(!$useLocalEngine && $selectedModel === 'gemini-3.5-flash-lite')
                                                <div class="w-5 h-5 rounded-full bg-teal-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs mt-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                                </div>
                                            @endif
                                        </button>

                                        {{-- Gemini 3.1 Flash Lite --}}
                                        <button 
                                            type="button" 
                                            wire:click="selectModel('gemini-3.1-flash-lite')" 
                                            @click="open = false"
                                            class="w-full text-left p-2.5 rounded-xl text-xs transition-all flex items-start justify-between gap-2.5 border active:scale-[0.98] {{ (!$useLocalEngine && $selectedModel === 'gemini-3.1-flash-lite') ? 'bg-teal-50/70 border-teal-300 ring-2 ring-teal-500/20 shadow-2xs' : 'bg-white hover:bg-slate-50 border-slate-200/70 hover:border-slate-300' }}"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-slate-900 leading-tight">Gemini 3.1 Flash Lite</span>
                                                    <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-teal-100 text-teal-800 font-black">Cepat</span>
                                                </div>
                                                <div class="flex items-center gap-1.5 mt-1 text-[10px] text-slate-500 font-medium">
                                                    <span class="bg-slate-100 px-1.5 py-0.2 rounded text-[9px] text-slate-700 font-bold">500 RPD • 15 RPM</span>
                                                    <span class="text-slate-400 truncate">Alternatif kuota besar</span>
                                                </div>
                                            </div>
                                            @if(!$useLocalEngine && $selectedModel === 'gemini-3.1-flash-lite')
                                                <div class="w-5 h-5 rounded-full bg-teal-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs mt-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                                </div>
                                            @endif
                                        </button>
                                    </div>
                                </div>

                                {{-- Group 2: Model Akurasi Tinggi (20 RPD / 5 RPM) --}}
                                <div class="pt-1 border-t border-slate-100">
                                    <div class="px-2 py-1 text-[10px] font-black uppercase tracking-wider text-slate-500 flex items-center justify-between">
                                        <span>🧠 Akurasi Tinggi & Analitik</span>
                                        <span class="text-[9px] bg-slate-100 text-slate-600 px-1.5 py-0.2 rounded-md font-bold">20 RPD</span>
                                    </div>

                                    <div class="space-y-1.5 mt-1">
                                        {{-- Gemini 3.5 Flash --}}
                                        <button 
                                            type="button" 
                                            wire:click="selectModel('gemini-3.5-flash')" 
                                            @click="open = false"
                                            class="w-full text-left p-2.5 rounded-xl text-xs transition-all flex items-start justify-between gap-2.5 border active:scale-[0.98] {{ (!$useLocalEngine && $selectedModel === 'gemini-3.5-flash') ? 'bg-teal-50/70 border-teal-300 ring-2 ring-teal-500/20 shadow-2xs' : 'bg-white hover:bg-slate-50 border-slate-200/70 hover:border-slate-300' }}"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-slate-900 leading-tight">Gemini 3.5 Flash</span>
                                                    <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-purple-100 text-purple-800 font-bold">Analitik</span>
                                                </div>
                                                <div class="flex items-center gap-1.5 mt-1 text-[10px] text-slate-500 font-medium">
                                                    <span class="bg-slate-100 px-1.5 py-0.2 rounded text-[9px] text-slate-700 font-bold">20 RPD • 5 RPM</span>
                                                    <span class="text-slate-400 truncate">Penalaran cerdas & audit</span>
                                                </div>
                                            </div>
                                            @if(!$useLocalEngine && $selectedModel === 'gemini-3.5-flash')
                                                <div class="w-5 h-5 rounded-full bg-teal-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs mt-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                                </div>
                                            @endif
                                        </button>

                                        {{-- Gemini 3.8 Flash --}}
                                        <button 
                                            type="button" 
                                            wire:click="selectModel('gemini-3.8-flash')" 
                                            @click="open = false"
                                            class="w-full text-left p-2.5 rounded-xl text-xs transition-all flex items-start justify-between gap-2.5 border active:scale-[0.98] {{ (!$useLocalEngine && $selectedModel === 'gemini-3.8-flash') ? 'bg-teal-50/70 border-teal-300 ring-2 ring-teal-500/20 shadow-2xs' : 'bg-white hover:bg-slate-50 border-slate-200/70 hover:border-slate-300' }}"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-slate-900 leading-tight">Gemini 3.8 Flash</span>
                                                    <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-indigo-100 text-indigo-800 font-bold">New</span>
                                                </div>
                                                <div class="flex items-center gap-1.5 mt-1 text-[10px] text-slate-500 font-medium">
                                                    <span class="bg-slate-100 px-1.5 py-0.2 rounded text-[9px] text-slate-700 font-bold">20 RPD • 5 RPM</span>
                                                    <span class="text-slate-400 truncate">Versi Flash mutakhir</span>
                                                </div>
                                            </div>
                                            @if(!$useLocalEngine && $selectedModel === 'gemini-3.8-flash')
                                                <div class="w-5 h-5 rounded-full bg-teal-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs mt-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                                </div>
                                            @endif
                                        </button>

                                        {{-- Gemini 3.6 Flash & 3.7 Flash Sub-Grid --}}
                                        <div class="grid grid-cols-2 gap-1.5 pt-0.5">
                                            <button 
                                                type="button" 
                                                wire:click="selectModel('gemini-3.6-flash')" 
                                                @click="open = false"
                                                class="text-left px-2.5 py-2 rounded-xl text-[11px] font-medium transition-all flex items-center justify-between border active:scale-[0.98] {{ (!$useLocalEngine && $selectedModel === 'gemini-3.6-flash') ? 'bg-teal-50/70 border-teal-300 text-teal-900 font-bold' : 'bg-white hover:bg-slate-50 border-slate-200/70 text-slate-700' }}"
                                            >
                                                <span>3.6 Flash</span>
                                                @if(!$useLocalEngine && $selectedModel === 'gemini-3.6-flash')
                                                    <span class="text-teal-600 font-black text-xs">✓</span>
                                                @endif
                                            </button>
                                            <button 
                                                type="button" 
                                                wire:click="selectModel('gemini-3.7-flash')" 
                                                @click="open = false"
                                                class="text-left px-2.5 py-2 rounded-xl text-[11px] font-medium transition-all flex items-center justify-between border active:scale-[0.98] {{ (!$useLocalEngine && $selectedModel === 'gemini-3.7-flash') ? 'bg-teal-50/70 border-teal-300 text-teal-900 font-bold' : 'bg-white hover:bg-slate-50 border-slate-200/70 text-slate-700' }}"
                                            >
                                                <span>3.7 Flash</span>
                                                @if(!$useLocalEngine && $selectedModel === 'gemini-3.7-flash')
                                                    <span class="text-teal-600 font-black text-xs">✓</span>
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Group 3: Cadangan Groq AI --}}
                                <div class="pt-1 border-t border-slate-100">
                                    <div class="px-2 py-1 text-[10px] font-black uppercase tracking-wider text-amber-800 flex items-center justify-between">
                                        <span>⚡ Cadangan Darurat (Groq)</span>
                                        <span class="text-[9px] bg-amber-100 text-amber-900 px-1.5 py-0.2 rounded-md font-bold">Bypass Limit</span>
                                    </div>

                                    <div class="mt-1">
                                        <button 
                                            type="button" 
                                            wire:click="selectModel('groq-qwen')" 
                                            @click="open = false"
                                            class="w-full text-left p-2.5 rounded-xl text-xs transition-all flex items-start justify-between gap-2.5 border active:scale-[0.98] {{ $useLocalEngine ? 'bg-amber-50/80 border-amber-300 ring-2 ring-amber-500/20 shadow-2xs' : 'bg-white hover:bg-slate-50 border-slate-200/70 hover:border-slate-300' }}"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-amber-950 leading-tight">⚡ Groq AI (Qwen 27B)</span>
                                                    <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-amber-200 text-amber-950 font-black">Ultra Cepat</span>
                                                </div>
                                                <div class="flex items-center gap-1.5 mt-1 text-[10px] text-slate-500 font-medium">
                                                    <span class="bg-amber-100 text-amber-900 px-1.5 py-0.2 rounded text-[9px] font-bold">~1 Detik</span>
                                                    <span class="text-slate-400 truncate">Solusi saat Gemini 429</span>
                                                </div>
                                            </div>
                                            @if($useLocalEngine)
                                                <div class="w-5 h-5 rounded-full bg-amber-600 text-white flex items-center justify-center flex-shrink-0 shadow-xs mt-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                                </div>
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Dropdown Footer Info --}}
                            <div class="px-3.5 py-2 bg-slate-50/90 border-t border-slate-100 flex items-center gap-1.5 text-[10px] text-slate-500 leading-tight">
                                <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="truncate">Otomatis beralih ke cadangan jika kuota limit (429).</span>
                            </div>
                        </div>
                    </div>

                    {{-- Reset Chat --}}
                    <button 
                        type="button"
                        wire:click="clearChat"
                        class="w-8 h-8 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200/70 active:scale-95 flex-shrink-0"
                        title="Bersihkan Percakapan"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>

                    {{-- Close Slider --}}
                    <button 
                        type="button"
                        wire:click="closeDrawer"
                        class="w-8 h-8 flex items-center justify-center rounded-xl text-slate-400 hover:text-red-500 bg-slate-50 hover:bg-red-50 transition-colors border border-slate-200/70 hover:border-red-200 active:scale-95 flex-shrink-0"
                        title="Tutup Panel AI"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Context Alert Banner (if viewing a specific SPK on order detail page or focused) --}}
            @if($contextSpkNumber)
                <div class="px-5 py-2.5 bg-gradient-to-r from-teal-50/90 to-emerald-50/70 border-b border-teal-100 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-2 text-xs text-teal-950 font-semibold truncate">
                        <span class="text-teal-600">🎯</span>
                        <span class="truncate">Fokus SPK: <strong class="font-black font-mono text-teal-800 bg-teal-100/80 px-2 py-0.5 rounded-md border border-teal-200/80">{{ $contextSpkNumber }}</strong> <span class="text-teal-700/80">({{ $contextCustomerName }})</span></span>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-[#F5C518] text-slate-950 uppercase tracking-wider shadow-2xs">
                            Aktif
                        </span>
                        <button 
                            type="button"
                            wire:click="clearContextOrder"
                            class="text-[10px] text-slate-500 hover:text-red-600 font-bold hover:underline px-1.5 py-0.5 rounded transition-colors"
                            title="Lepas fokus SPK ini dan kembali ke pencarian umum"
                        >
                            ✕ Lepas
                        </button>
                    </div>
                </div>
            @endif

            {{-- Chat Messages Area (Full Height Scrollable) --}}
            <div id="ai-chat-messages" class="flex-1 p-5 overflow-y-auto space-y-4 text-xs font-sans bg-slate-50/70 custom-scrollbar">
                @foreach($messages as $msg)
                    @if($msg['role'] === 'user')
                        {{-- User Message --}}
                        <div class="flex justify-end gap-2.5 items-start pl-10">
                            <div class="flex flex-col items-end max-w-[82%]">
                                <div class="flex items-center gap-1.5 mb-1 pr-1 text-[11px] text-slate-500 font-medium">
                                    <span class="font-bold text-slate-700">{{ auth()->user()?->name ?? 'Anda' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $msg['time'] ?? '' }}</span>
                                </div>
                                <div class="w-fit inline-block bg-teal-600 text-white rounded-2xl rounded-tr-xs px-4 py-2.5 shadow-xs text-[13.5px] leading-relaxed font-medium break-words text-left">
                                    {{ $msg['content'] }}
                                </div>
                            </div>

                            {{-- User Avatar (Clean, Soft Emerald/Teal Badge) --}}
                            <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-800 font-black text-xs flex items-center justify-center border border-teal-200/80 shadow-2xs flex-shrink-0 mt-0.5" title="{{ auth()->user()?->name }} ({{ $this->userRole }})">
                                {{ $this->userInitials }}
                            </div>
                        </div>
                    @else
                        {{-- AI Copilot Message --}}
                        <div class="flex justify-start gap-2.5 items-start pr-10">
                            {{-- AI Avatar with Shoe Workshop Logo --}}
                            <div class="w-8 h-8 rounded-full bg-white p-1 flex items-center justify-center border border-slate-200 shadow-2xs flex-shrink-0 mt-0.5">
                                <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-contain" alt="Shoe Workshop AI" />
                            </div>

                            <div class="space-y-1.5 max-w-[85%]">
                                <div class="flex items-center gap-1.5 pl-1 text-[11px] text-slate-500 font-medium flex-wrap">
                                    <span class="font-bold text-slate-900 font-poppins">Workshop AI Copilot</span>
                                    @if(($msg['source'] ?? '') === 'groq')
                                        <span class="text-[8.5px] font-black px-1.5 py-0.5 rounded-full uppercase bg-amber-50 text-amber-900 border border-amber-300/80 flex items-center gap-1 shadow-2xs" title="Berjalan menggunakan Groq Cloud AI (Qwen 27B)">
                                            <span>⚡</span> <span>Groq AI</span>
                                        </span>
                                    @else
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded-full uppercase bg-teal-50 text-teal-700 border border-teal-200/80" title="Berjalan menggunakan Google Gemini Cloud AI">
                                            <span>🌐</span> <span>Gemini AI</span>
                                        </span>
                                    @endif
                                    <span class="text-slate-300">•</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $msg['time'] ?? '' }}</span>
                                </div>

                                {{-- Natural Text Bubble (Clean White Card with Markdown Parsing & High Contrast Typography) --}}
                                @if(!empty(trim($msg['content'] ?? '')))
                                    <div 
                                        class="bg-white border border-slate-200/90 text-slate-800 rounded-2xl rounded-tl-xs p-4 shadow-xs"
                                        x-init="$nextTick(() => enhanceAiMessage($el))"
                                    >
                                        <div class="ai-markdown-content">
                                            {!! \Illuminate\Support\Str::markdown($msg['content']) !!}
                                        </div>

                                        {{-- Truncation Warning Banner + Quick Continue Button --}}
                                        @if(!empty($msg['is_truncated']))
                                            <div class="mt-3.5 p-3 bg-amber-50/90 border border-amber-200/90 rounded-xl flex items-center justify-between gap-3 text-amber-900 shadow-2xs">
                                                <div class="flex items-start gap-2.5 text-xs">
                                                    <span class="text-base flex-shrink-0 mt-0.5">⚠️</span>
                                                    <div>
                                                        <p class="font-black text-amber-950 text-xs">Respons Terpotong (Batas Token Tercapai)</p>
                                                        <p class="text-[11px] text-amber-800/90 mt-0.5 leading-snug">
                                                            Data yang dirangkum sangat panjang sehingga mencapai batas kapasitas respon.
                                                        </p>
                                                    </div>
                                                </div>
                                                <button 
                                                    type="button" 
                                                    wire:click="sendQuickPrompt('Lanjutkan penjelasan sebelumnya')" 
                                                    class="flex-shrink-0 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 active:scale-95 text-white font-extrabold text-xs rounded-lg shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer"
                                                    title="Klik untuk melanjutkan sisa penjelasan"
                                                >
                                                    <span>⏩</span>
                                                    <span>Lanjutkan</span>
                                                </button>
                                            </div>
                                        @endif

                                        {{-- Quota Exceeded 429 Banner with Reset Time & Retry Button --}}
                                        @if(!empty($msg['quota_exceeded']))
                                            <div class="mt-3.5 p-3.5 bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50 border border-amber-300 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-amber-950 shadow-2xs">
                                                <div class="flex items-start gap-2.5 text-xs">
                                                    <span class="text-lg flex-shrink-0 mt-0.5">⏳</span>
                                                    <div>
                                                        <p class="font-black text-amber-950 text-xs">
                                                            @if(($msg['source'] ?? '') === 'groq')
                                                                Kuota Groq AI Pulih dalam ~30 - 60 Detik
                                                            @else
                                                                Kuota Gemini AI Pulih dalam ~30 - 60 Detik
                                                            @endif
                                                        </p>
                                                        <p class="text-[11px] text-amber-900/90 mt-0.5 leading-snug">
                                                            @if(($msg['source'] ?? '') === 'groq')
                                                                Beralih ke 🌐 Gemini AI atau tunggu 30-60 detik lalu klik Coba Lagi.
                                                            @else
                                                                Beralih ke ⚡ Groq AI atau tunggu 30-60 detik lalu klik Coba Lagi.
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 flex-shrink-0">
                                                    @if(($msg['source'] ?? '') === 'groq')
                                                        <button 
                                                            type="button" 
                                                            wire:click="toggleEngineMode(false)" 
                                                            class="px-3 py-1.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 active:scale-95 text-white font-black text-xs rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
                                                            title="Beralih ke Google Gemini Cloud AI"
                                                        >
                                                            <span>🌐 Ke Gemini</span>
                                                        </button>
                                                    @else
                                                        <button 
                                                            type="button" 
                                                            wire:click="toggleEngineMode(true)" 
                                                            class="px-3 py-1.5 bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 active:scale-95 text-white font-black text-xs rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
                                                            title="Beralih ke Groq Cloud AI"
                                                        >
                                                            <span>⚡ Ke Groq AI</span>
                                                        </button>
                                                    @endif

                                                    <button 
                                                        type="button" 
                                                        wire:click="retryLastMessage" 
                                                        class="px-3 py-1.5 bg-white hover:bg-amber-100 text-amber-950 font-black text-xs rounded-xl border border-amber-300 shadow-2xs transition-all flex items-center gap-1 cursor-pointer active:scale-95"
                                                        title="Kirim ulang pertanyaan sebelumnya"
                                                    >
                                                        <span>🔄</span>
                                                        <span>Coba Lagi</span>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- SPK Cover Photo (Before Condition) Compact Preview Card --}}
                                @if(!empty($msg['cover_photo']) && empty($msg['photos']))
                                    <div 
                                        @click="openLightbox('{{ $msg['cover_photo']['url'] }}', '{{ addslashes($msg['cover_photo']['shoe'] ?? '') }} ({{ addslashes($msg['cover_photo']['customer_name'] ?? '') }})', 'Cover SPK (Before) • {{ $msg['cover_photo']['spk_number'] ?? '' }}')"
                                        class="bg-white border border-slate-200/90 rounded-2xl p-3 shadow-2xs hover:border-teal-400 hover:shadow-md transition-all duration-200 cursor-pointer group relative overflow-hidden"
                                    >
                                        <div class="flex items-center gap-3">
                                            {{-- Image Thumbnail with Zoom Hover Hint --}}
                                            <div class="relative w-16 h-16 rounded-xl overflow-hidden bg-slate-100 border border-slate-200/80 flex-shrink-0">
                                                <img 
                                                    src="{{ $msg['cover_photo']['url'] }}" 
                                                    alt="Foto Cover Before" 
                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                                    loading="lazy"
                                                />
                                                <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-teal-900/20 transition-colors flex items-center justify-center">
                                                    <span class="opacity-0 group-hover:opacity-100 transition-opacity p-1 rounded-full bg-white/90 text-teal-800 shadow-xs">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Information & Action Hint --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200/80">
                                                        📸 Cover SPK (Before)
                                                    </span>
                                                    <span class="text-[10px] text-slate-400 font-mono font-bold">{{ $msg['cover_photo']['spk_number'] ?? '' }}</span>
                                                </div>
                                                <h5 class="text-xs font-bold text-slate-900 truncate mt-1 group-hover:text-teal-700 transition-colors">
                                                    {{ $msg['cover_photo']['shoe'] ?? 'Sepatu Pelanggan' }}
                                                </h5>
                                                <p class="text-[10.5px] text-teal-600 font-bold flex items-center gap-1 mt-0.5">
                                                    <span>🔍 Klik untuk perbesar</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Full Interactive Visual Photo Gallery (Before, After, Referensi) --}}
                                @if(!empty($msg['photos']))
                                    <div 
                                        class="bg-white border border-slate-200/90 rounded-2xl p-3.5 shadow-xs space-y-2.5"
                                        x-data="{ currentFilter: 'all' }"
                                    >
                                        {{-- Header Gallery --}}
                                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2">
                                            <div>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-xs">🖼️</span>
                                                    <h5 class="text-xs font-black text-slate-900 tracking-tight">Dokumentasi Foto Sepatu</h5>
                                                </div>
                                                <p class="text-[10.5px] text-slate-500 font-medium truncate max-w-[220px]">
                                                    {{ $msg['photos']['spk_number'] ?? '' }} • {{ $msg['photos']['shoe'] ?? '' }}
                                                </p>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[9.5px] font-black bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ count($msg['photos']['items'] ?? []) }} Foto
                                            </span>
                                        </div>

                                        {{-- Filter Pills --}}
                                        @php
                                            $galleryItems = $msg['photos']['items'] ?? [];
                                            $cBefore = count(array_filter($galleryItems, fn($p) => in_array($p['group'] ?? '', ['Before'])));
                                            $cAfter = count(array_filter($galleryItems, fn($p) => in_array($p['group'] ?? '', ['After'])));
                                            $cRef = count(array_filter($galleryItems, fn($p) => in_array($p['group'] ?? '', ['Referensi'])));
                                        @endphp
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <button 
                                                type="button" 
                                                @click="currentFilter = 'all'"
                                                :class="currentFilter === 'all' ? 'bg-teal-700 text-white shadow-2xs font-black' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200 font-bold'"
                                                class="px-2.5 py-1 rounded-lg text-[10px] transition-all cursor-pointer"
                                            >
                                                Semua ({{ count($galleryItems) }})
                                            </button>
                                            @if($cBefore > 0)
                                                <button 
                                                    type="button" 
                                                    @click="currentFilter = 'Before'"
                                                    :class="currentFilter === 'Before' ? 'bg-amber-600 text-white shadow-2xs font-black' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200 font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[10px] transition-all cursor-pointer"
                                                >
                                                    🏭 Before ({{ $cBefore }})
                                                </button>
                                            @endif
                                            @if($cAfter > 0)
                                                <button 
                                                    type="button" 
                                                    @click="currentFilter = 'After'"
                                                    :class="currentFilter === 'After' ? 'bg-emerald-600 text-white shadow-2xs font-black' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[10px] transition-all cursor-pointer"
                                                >
                                                    🏁 After ({{ $cAfter }})
                                                </button>
                                            @endif
                                            @if($cRef > 0)
                                                <button 
                                                    type="button" 
                                                    @click="currentFilter = 'Referensi'"
                                                    :class="currentFilter === 'Referensi' ? 'bg-sky-600 text-white shadow-2xs font-black' : 'bg-sky-50 text-sky-800 hover:bg-sky-100 border border-sky-200 font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[10px] transition-all cursor-pointer"
                                                >
                                                    📦 Referensi ({{ $cRef }})
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Photos Grid --}}
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-0.5">
                                            @foreach($galleryItems as $pItem)
                                                <div 
                                                    x-show="currentFilter === 'all' || currentFilter === '{{ $pItem['group'] ?? '' }}'"
                                                    x-transition
                                                    @click="openLightbox('{{ $pItem['url'] }}', '{{ addslashes($pItem['caption'] ?: ($msg['photos']['shoe'] ?? '')) }}', '{{ $pItem['group'] ?? 'Foto' }} • {{ $msg['photos']['spk_number'] ?? '' }}')"
                                                    class="group relative bg-slate-50 border border-slate-200/80 rounded-xl overflow-hidden shadow-2xs hover:shadow-md hover:border-teal-400 transition-all duration-200 cursor-pointer flex flex-col"
                                                >
                                                    <div class="relative aspect-square w-full overflow-hidden bg-slate-100">
                                                        <img 
                                                            src="{{ $pItem['url'] }}" 
                                                            alt="{{ $pItem['caption'] ?? 'Foto' }}" 
                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                            loading="lazy"
                                                        />
                                                        {{-- Hover Overlay --}}
                                                        <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-teal-900/20 transition-colors flex items-center justify-center">
                                                            <span class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 rounded-full bg-white/90 text-teal-800 shadow-sm scale-90 group-hover:scale-100 duration-200">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                                                                </svg>
                                                            </span>
                                                        </div>

                                                        {{-- Floating Stage Badge --}}
                                                        <div class="absolute top-1.5 left-1.5 flex items-center gap-1">
                                                            @if(($pItem['group'] ?? '') === 'Before')
                                                                <span class="px-1.5 py-0.5 rounded text-[8.5px] font-black uppercase bg-amber-500 text-white shadow-2xs">
                                                                    Before
                                                                </span>
                                                            @elseif(($pItem['group'] ?? '') === 'After')
                                                                <span class="px-1.5 py-0.5 rounded text-[8.5px] font-black uppercase bg-emerald-600 text-white shadow-2xs">
                                                                    After
                                                                </span>
                                                            @elseif(($pItem['group'] ?? '') === 'Referensi')
                                                                <span class="px-1.5 py-0.5 rounded text-[8.5px] font-black uppercase bg-sky-600 text-white shadow-2xs">
                                                                    Referensi
                                                                </span>
                                                            @else
                                                                <span class="px-1.5 py-0.5 rounded text-[8.5px] font-black uppercase bg-slate-700 text-white shadow-2xs">
                                                                    {{ $pItem['group'] ?? 'Foto' }}
                                                                </span>
                                                            @endif

                                                            @if(!empty($pItem['is_cover']))
                                                                <span class="px-1 py-0.5 rounded text-[8px] font-black uppercase bg-white/90 text-amber-800 shadow-2xs" title="Cover SPK">
                                                                    ⭐ Cover
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if(!empty($pItem['caption']))
                                                        <div class="p-1.5 bg-white border-t border-slate-100 text-[9.5px] text-slate-600 font-medium truncate" title="{{ $pItem['caption'] }}">
                                                            {{ $pItem['caption'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Rich Interactive SPK Cards (Shown during general searches when discovering SPKs) --}}
                                @if(!empty($msg['cards']))
                                    <div class="space-y-2.5 pt-1">
                                        @foreach($msg['cards'] as $card)
                                            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm hover:border-teal-400 hover:shadow-md transition-all duration-200 relative overflow-hidden">
                                                <div class="flex items-start justify-between gap-3 mb-2.5">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-black bg-slate-100 text-slate-800 border border-slate-200">
                                                                {{ $card['spk_number'] }}
                                                            </span>
                                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                                                {{ $card['status'] }}
                                                            </span>
                                                        </div>
                                                        <h4 class="text-sm font-extrabold text-slate-900 mt-1.5">{{ $card['customer_name'] }}</h4>
                                                        <p class="text-[11px] text-slate-500 font-medium">{{ $card['shoe_brand'] }} • {{ $card['shoe_color'] }}</p>
                                                    </div>

                                                    @if(!empty($card['photo_url']))
                                                        <img src="{{ $card['photo_url'] }}" alt="Sepatu" class="w-14 h-14 rounded-xl object-cover border border-slate-200 flex-shrink-0 shadow-2xs" />
                                                    @endif
                                                </div>

                                                <div class="grid grid-cols-2 gap-2 text-[10px] bg-slate-50 rounded-xl p-3 mb-3 border border-slate-100 text-slate-700">
                                                    <div>
                                                        <span class="text-slate-400 font-bold uppercase tracking-wider block text-[8px]">Posisi / Rak:</span>
                                                        <span class="font-black text-teal-700 text-xs">{{ $card['rack'] ?? $card['current_location'] }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-slate-400 font-bold uppercase tracking-wider block text-[8px]">Estimasi Selesai:</span>
                                                        <span class="font-extrabold text-slate-800">{{ $card['estimation_date'] ?? '-' }}</span>
                                                    </div>
                                                </div>

                                                {{-- Action Buttons: Focus & Ask (Primary) + Open Web (Secondary) --}}
                                                <div class="flex items-center gap-2">
                                                    <button 
                                                        type="button" 
                                                        wire:click="focusOrder({{ $card['id'] }})"
                                                        class="flex-1 flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl bg-gradient-to-r from-teal-600 via-teal-500 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white font-black text-xs transition-all shadow-sm active:scale-95 border border-teal-400/30"
                                                    >
                                                        <svg class="w-4 h-4 text-[#F5C518]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                        </svg>
                                                        <span>Fokus & Tanya SPK Ini</span>
                                                    </button>
                                                    <a 
                                                        href="{{ $card['url'] }}" 
                                                        target="_blank"
                                                        class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition-colors border border-slate-200 flex-shrink-0"
                                                        title="Buka Halaman Detail SPK di Tab Baru"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Rich Workshop Activity Timeline Log Card --}}
                                @if(!empty($msg['timeline']))
                                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-sm space-y-3">
                                        {{-- Header --}}
                                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                            <div class="space-y-0.5">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-sm">⏱️</span>
                                                    <h5 class="font-black text-xs text-slate-900 font-poppins">
                                                        Timeline Riwayat SPK
                                                    </h5>
                                                    <span class="px-2 py-0.5 rounded-md text-[9.5px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                        {{ $msg['timeline']['spk_number'] }}
                                                    </span>
                                                </div>
                                                @if(!empty($msg['timeline']['customer_name']) || !empty($msg['timeline']['shoe']))
                                                    <p class="text-[10.5px] text-slate-500 font-medium pl-5 truncate">
                                                        {{ $msg['timeline']['customer_name'] ?? '' }} @if(!empty($msg['timeline']['shoe'])) • {{ $msg['timeline']['shoe'] }} @endif
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold text-teal-700 bg-teal-50 border border-teal-200/80 uppercase tracking-wider flex-shrink-0">
                                                Audit Trail
                                            </span>
                                        </div>

                                        {{-- Vertical Connected Timeline --}}
                                        <div class="space-y-0 max-h-64 overflow-y-auto pr-1.5 custom-scrollbar relative pl-4 before:absolute before:left-1.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                                            @foreach($msg['timeline']['events'] as $index => $evt)
                                                @php
                                                    $isStatusChange = ($evt['action'] ?? '') === 'STATUS_CHANGE';
                                                    $stepName = strtoupper($evt['step'] ?? $evt['action'] ?? '');
                                                    $dotColor = 'bg-slate-400 ring-slate-100';
                                                    $badgeColor = 'bg-slate-100 text-slate-700 border-slate-200';

                                                    if (str_contains($stepName, 'PREPARAT')) {
                                                        $dotColor = 'bg-amber-500 ring-amber-100';
                                                        $badgeColor = 'bg-amber-50 text-amber-800 border-amber-200/80';
                                                    } elseif (str_contains($stepName, 'SORTIR')) {
                                                        $dotColor = 'bg-blue-500 ring-blue-100';
                                                        $badgeColor = 'bg-blue-50 text-blue-800 border-blue-200/80';
                                                    } elseif (str_contains($stepName, 'PROD')) {
                                                        $dotColor = 'bg-purple-500 ring-purple-100';
                                                        $badgeColor = 'bg-purple-50 text-purple-800 border-purple-200/80';
                                                    } elseif (str_contains($stepName, 'QC')) {
                                                        $dotColor = 'bg-teal-500 ring-teal-100';
                                                        $badgeColor = 'bg-teal-50 text-teal-800 border-teal-200/80';
                                                    } elseif (str_contains($stepName, 'SELESAI') || str_contains($stepName, 'DELIVER')) {
                                                        $dotColor = 'bg-emerald-500 ring-emerald-100';
                                                        $badgeColor = 'bg-emerald-50 text-emerald-800 border-emerald-200/80';
                                                    }
                                                @endphp
                                                <div class="relative flex items-start gap-3 pb-3.5 last:pb-1 group">
                                                    {{-- Node Dot --}}
                                                    <div class="absolute -left-4 mt-1 w-2.5 h-2.5 rounded-full {{ $dotColor }} ring-4 flex-shrink-0 z-10 transition-transform group-hover:scale-125"></div>

                                                    <div class="flex-1 bg-slate-50/70 group-hover:bg-slate-50 border border-slate-100 rounded-xl p-2.5 transition-colors">
                                                        <div class="flex items-center justify-between gap-2 mb-1 flex-wrap">
                                                            <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase border {{ $badgeColor }}">
                                                                {{ $evt['step'] ?? $evt['action'] }}
                                                            </span>
                                                            <span class="text-[9.5px] text-slate-400 font-mono font-medium">
                                                                {{ $evt['time'] }}
                                                            </span>
                                                        </div>
                                                        <p class="text-slate-700 text-xs leading-snug font-medium">
                                                            {{ $evt['description'] }}
                                                        </p>
                                                        <div class="flex items-center justify-between gap-2 mt-1.5 pt-1.5 border-t border-slate-100 text-[10px] text-slate-400">
                                                            <span>PIC / Staf: <strong class="text-slate-600 font-bold">{{ $evt['user'] }}</strong></span>
                                                            @if(!empty($evt['time_diff']))
                                                                <span class="italic text-[9px]">{{ $evt['time_diff'] }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- In-chat Loading / Thinking Indicator (Room Chat) --}}
                @if($isLoading)
                    <div class="flex justify-start gap-3 items-start pr-8 animate-in fade-in">
                        <div class="w-8 h-8 rounded-full bg-white p-1 flex items-center justify-center border border-slate-200 shadow-2xs flex-shrink-0 mt-0.5">
                            <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-contain" alt="AI" />
                        </div>
                        <div class="bg-white border border-slate-200/90 px-4 py-3 rounded-2xl rounded-tl-xs flex items-center gap-2.5 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-teal-500 animate-bounce"></span>
                            <span class="w-2 h-2 rounded-full bg-teal-500 animate-bounce [animation-delay:0.2s]"></span>
                            <span class="w-2 h-2 rounded-full bg-teal-500 animate-bounce [animation-delay:0.4s]"></span>
                            <span class="text-xs font-bold text-slate-600 ml-1">Sedang menganalisis data workshop...</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Quick Prompt Chips (Wrapped, No Horizontal Scroll) --}}
            <div class="px-5 py-3 bg-white border-t border-slate-100 flex flex-wrap gap-2 flex-shrink-0">
                @if($contextSpkNumber)
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Rangkum timeline riwayat pengerjaan SPK ini')"
                        class="px-3.5 py-2 rounded-xl bg-teal-50/80 hover:bg-teal-100 text-teal-800 text-xs font-bold transition-all border border-teal-200 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>⏱️</span>
                        <span>Rangkum Timeline</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Berapa rincian biaya invoice dan status pembayarannya?')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-teal-50 hover:text-teal-800 hover:border-teal-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>💰</span>
                        <span>Rincian Biaya & Invoice</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Siapa saja teknisi yang mengerjakan sepatu ini?')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-teal-50 hover:text-teal-800 hover:border-teal-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>👨‍🔧</span>
                        <span>Teknisi yang Menangani</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Dimana posisi rak fisik sepatu ini sekarang?')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-teal-50 hover:text-teal-800 hover:border-teal-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>📍</span>
                        <span>Posisi Rak Fisik</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Apa saja layanan jasa yang dikerjakan pada SPK ini?')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-teal-50 hover:text-teal-800 hover:border-teal-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>👟</span>
                        <span>Layanan Jasa</span>
                    </button>
                @elseif($isWorkshopPage)
                    {{-- Workshop & Production Live Floor Pills --}}
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Ada SPK apa saja yang telat atau terancam telat (overdue SLA) di workshop hari ini?')"
                        class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-900 text-xs font-bold transition-all border border-rose-300 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>🚨</span>
                        <span>SPK Overdue & Deadline</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Siapa saja teknisi yang bebannya paling tinggi atau sedang overload saat ini?')"
                        class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-950 text-xs font-bold transition-all border border-amber-300 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>👥</span>
                        <span>Beban Kerja Teknisi</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Berapa banyak SPK yang sedang antre di setiap stasiun workshop (Prep, Sortir, Produksi, QC) sekarang?')"
                        class="px-3.5 py-2 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold transition-all border border-teal-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>⏳</span>
                        <span>Antrean Stasiun Live</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Bagaimana performa throughput stasiun workshop bulan ini dan di mana bottleneck-nya?')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-teal-50 hover:text-teal-800 hover:border-teal-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>🏭</span>
                        <span>Analisis Bottleneck KPI</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Berapa banyak SPK Fast Track yang aktif saat ini dan di tahap mana saja posisinya?')"
                        class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 hover:border-amber-300 text-xs font-bold transition-all border border-amber-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>⚡</span>
                        <span>SPK Fast Track Aktif</span>
                    </button>
                @elseif($isKpiPage)
                    {{-- KPI Workshop Quick Pills --}}
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Bagaimana performa throughput stasiun workshop bulan ini dan di mana bottleneck-nya?')"
                        class="px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-950 text-xs font-bold transition-all border border-indigo-300 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>🏭</span>
                        <span>KPI Workshop & Throughput</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Ada SPK apa saja yang telat atau terancam telat (overdue SLA) di workshop hari ini?')"
                        class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-900 text-xs font-bold transition-all border border-rose-300 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>🚨</span>
                        <span>SPK Overdue Workshop</span>
                    </button>

                    {{-- KPI Gudang Quick Pills --}}
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Berapa ringkasan KPI Gudang dan pergerakan logistik sepatu bulan ini?')"
                        class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-950 text-xs font-bold transition-all border border-amber-300 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>📦</span>
                        <span>KPI Gudang Bulan Ini</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Berapa jumlah sepatu masuk fisik di gudang dan sepatu keluar yang sudah diambil pelanggan?')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-amber-50 hover:text-amber-900 hover:border-amber-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>📥</span>
                        <span>Sepatu Masuk vs Keluar</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Bagaimana status rak penyimpanan sepatu saat ini? Apakah ada barang yang tertahan lebih dari 7 hari?')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-amber-50 hover:text-amber-900 hover:border-amber-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>🏷️</span>
                        <span>Status Rak & Overdue</span>
                    </button>

                    {{-- KPI Finance Quick Pills --}}
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Berapa ringkasan KPI Finance dan kas masuk bulan ini?')"
                        class="px-3.5 py-2 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold transition-all border border-teal-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>📊</span>
                        <span>KPI Finance Bulan Ini</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Berapa kas masuk tervalidasi dan sisa piutang aktif saat ini?')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-teal-50 hover:text-teal-800 hover:border-teal-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>💰</span>
                        <span>Kas Masuk & Piutang</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Bagaimana rekap transaksi SPK yang dibatalkan dan total dana refund-nya?')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-rose-50 hover:text-rose-800 hover:border-rose-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>↩️</span>
                        <span>SPK Batal & Refund</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Berapa rasio penagihan (collection rate) dan rincian status invoice saat ini?')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-teal-50 hover:text-teal-800 hover:border-teal-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>🎯</span>
                        <span>Rasio Penagihan</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Audit apakah ada sepatu yang statusnya sudah Selesai atau Diantar tapi pembayarannya belum lunas? Berapa total risikonya dan rekomendasinya?')"
                        class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 hover:border-amber-300 text-xs font-bold transition-all border border-amber-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>🚨</span>
                        <span>Audit Selesai Belum Lunas</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Berapa total kebocoran biaya dan kerugian workshop bulan ini dari pembatalan, refund, dan revisi teknisi?')"
                        class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-900 hover:border-rose-300 text-xs font-bold transition-all border border-rose-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>📉</span>
                        <span>Cek Kerugian Workshop</span>
                    </button>
                @else
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Ada SPK apa saja yang telat atau terancam telat (overdue SLA) di workshop hari ini?')"
                        class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-900 text-xs font-bold transition-all border border-rose-300 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>🚨</span>
                        <span>SPK Overdue</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Siapa saja teknisi yang bebannya paling tinggi atau sedang overload saat ini?')"
                        class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-950 text-xs font-bold transition-all border border-amber-300 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>👥</span>
                        <span>Beban Teknisi</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Berapa ringkasan KPI Gudang dan pergerakan logistik sepatu bulan ini?')"
                        class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-950 text-xs font-bold transition-all border border-amber-300 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>📦</span>
                        <span>KPI Gudang</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Berapa ringkasan KPI Finance dan kas masuk bulan ini?')"
                        class="px-3.5 py-2 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold transition-all border border-teal-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>📊</span>
                        <span>KPI Finance</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Bagaimana cara melacak pesanan via Internal Tracking?')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-teal-50 hover:text-teal-800 hover:border-teal-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>🔍</span>
                        <span>Panduan Tracking</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Cari SPK 18')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-teal-50 hover:text-teal-800 hover:border-teal-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>👟</span>
                        <span>Cek SPK 18</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="sendQuickPrompt('Cari pesanan atas nama Budi')"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-teal-50 hover:text-teal-800 hover:border-teal-300 text-slate-700 text-xs font-semibold transition-all border border-slate-200/90 shadow-2xs active:scale-95 flex items-center gap-1.5"
                    >
                        <span>👤</span>
                        <span>Cari Pelanggan Budi</span>
                    </button>
                @endif
            </div>

            {{-- Input Bar (Enlarged Height & Modern Dock) --}}
            <form wire:submit.prevent="submitMessage" class="p-4 bg-white border-t border-slate-100 flex items-center gap-3 flex-shrink-0 shadow-2xs">
                <input 
                    type="text" 
                    wire:model="currentInput"
                    placeholder="Ketik pertanyaan (misal: Cari SPK 18 atau posisi sepatu Budi)..."
                    class="h-12 flex-1 px-4.5 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/15 outline-none transition-all shadow-2xs font-medium"
                    @if($isLoading) disabled @endif
                    autocomplete="off"
                />
                <button 
                    type="submit"
                    @if($isLoading) disabled @endif
                    class="h-12 px-6 rounded-2xl bg-gradient-to-r from-teal-600 via-teal-500 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-black text-sm shadow-md shadow-teal-700/20 active:scale-95 transition-all flex items-center justify-center gap-2 flex-shrink-0"
                >
                    @if($isLoading)
                        <span class="flex items-center gap-1.5">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Menganalisis...</span>
                        </span>
                    @else
                        <span>Kirim</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    @endif
                </button>
            </form>

            {{-- Modern Built-in Lightbox Zoom Modal for Chat Photos --}}
            <div 
                x-show="lightboxOpen"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[10000] flex items-center justify-center p-3 sm:p-5 bg-slate-950/85 backdrop-blur-sm"
                @click.self="closeLightbox()"
            >
                <div 
                    class="relative max-w-2xl w-full max-h-[92vh] bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-white/10 flex flex-col"
                    x-show="lightboxOpen"
                    x-transition:enter="transition ease-out duration-200 transform"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150 transform"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                >
                    {{-- Lightbox Top Navigation / Header --}}
                    <div class="px-4 py-3 bg-slate-900/90 border-b border-white/10 flex items-center justify-between text-white flex-shrink-0">
                        <div class="flex items-center gap-2 min-w-0 pr-3">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-teal-500/20 text-teal-300 border border-teal-400/30 flex-shrink-0" x-text="activePhotoBadge"></span>
                            <span class="text-xs text-slate-300 font-bold truncate" x-text="activePhotoCaption"></span>
                        </div>
                        <button 
                            type="button" 
                            @click="closeLightbox()"
                            class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-colors cursor-pointer flex-shrink-0"
                            title="Tutup (Esc)"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Lightbox Image Canvas --}}
                    <div class="flex-1 overflow-auto p-3 flex items-center justify-center bg-black/50 min-h-[260px] max-h-[75vh]">
                        <img 
                            :src="activePhotoUrl" 
                            alt="Foto Sepatu" 
                            class="max-w-full max-h-[72vh] object-contain rounded-2xl shadow-xl transition-transform"
                        />
                    </div>

                    {{-- Lightbox Bottom Bar --}}
                    <div class="px-4 py-2.5 bg-slate-900/95 border-t border-white/10 flex items-center justify-between text-slate-400 text-[11px] flex-shrink-0">
                        <span class="hidden sm:inline">Tekan <kbd class="px-1.5 py-0.5 rounded bg-white/10 text-white font-mono text-[10px]">Esc</kbd> atau klik di luar untuk menutup</span>
                        <span class="sm:hidden">Ketuk di luar untuk menutup</span>
                        <a 
                            :href="activePhotoUrl" 
                            target="_blank" 
                            rel="noopener"
                            class="inline-flex items-center gap-1.5 text-teal-400 hover:text-teal-300 font-bold transition-colors cursor-pointer"
                        >
                            <span>Buka Ukuran Asli</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Custom Scoped Styles for Clean AI Markdown Breakdown & Copy Cards --}}
    <style>
        .ai-markdown-content {
            font-size: 13.5px;
            line-height: 1.65;
            color: #1e293b;
        }
        .ai-markdown-content p {
            margin-top: 0.4rem;
            margin-bottom: 0.65rem;
        }
        .ai-markdown-content p:first-child {
            margin-top: 0;
        }
        .ai-markdown-content p:last-child {
            margin-bottom: 0;
        }
        .ai-markdown-content h1,
        .ai-markdown-content h2,
        .ai-markdown-content h3,
        .ai-markdown-content h4 {
            font-size: 13.5px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 0.95rem;
            margin-bottom: 0.45rem;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.35rem;
        }
        .ai-markdown-content h3 {
            background: #f8fafc;
            padding: 0.45rem 0.75rem;
            border-radius: 0.6rem;
            border: 1px solid #e2e8f0;
            border-left: 3.5px solid #0d9488;
        }
        .ai-markdown-content hr {
            border: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, #cbd5e1 15%, #cbd5e1 85%, transparent);
            margin: 1rem 0;
        }
        .ai-markdown-content ul,
        .ai-markdown-content ol {
            margin: 0.4rem 0 0.6rem 0;
            padding-left: 0.25rem;
            list-style: none;
        }
        .ai-markdown-content li {
            margin-bottom: 0.35rem;
            line-height: 1.6;
        }
        .ai-markdown-content strong {
            color: #0f172a;
            font-weight: 700;
        }
        .ai-markdown-content blockquote {
            margin: 0.85rem 0;
            padding: 0.85rem 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #0d9488;
            border-radius: 0 0.85rem 0.85rem 0;
            color: #1e293b;
            font-style: normal;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            position: relative;
        }
        .ai-markdown-content blockquote p {
            margin: 0.35rem 0;
            line-height: 1.65;
        }
        .ai-markdown-content blockquote p:first-child {
            margin-top: 0;
        }
        .ai-markdown-content blockquote p:last-child {
            margin-bottom: 0;
        }
    </style>

    <script>
        function enhanceAiMessage(el) {
            if (!el) return;
            el.querySelectorAll('blockquote').forEach((bq) => {
                if (bq.querySelector('.copy-btn')) return;

                if (!bq.querySelector('.draft-badge')) {
                    const badge = document.createElement('div');
                    badge.className = 'draft-badge flex items-center justify-between pb-2 mb-2.5 border-b border-slate-200/80 text-[10.5px] font-extrabold text-teal-800 uppercase tracking-wider';
                    badge.innerHTML = '<span class="flex items-center gap-1.5"><span>💬</span> <span>Draft Pesan WhatsApp</span></span>';
                    bq.prepend(badge);
                }

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'copy-btn inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 rounded-lg bg-white border border-teal-200 text-xs font-bold text-teal-800 hover:bg-teal-50 hover:border-teal-400 shadow-2xs transition-all active:scale-95 cursor-pointer';
                btn.innerHTML = '<span>📋</span> <span>Salin Pesan</span>';
                btn.onclick = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const clone = bq.cloneNode(true);
                    const b = clone.querySelector('.copy-btn');
                    if (b) b.remove();
                    const d = clone.querySelector('.draft-badge');
                    if (d) d.remove();
                    navigator.clipboard.writeText(clone.innerText.trim());
                    btn.innerHTML = '<span>✅</span> <span class="text-emerald-700 font-bold">Tersalin!</span>';
                    btn.classList.add('bg-emerald-50', 'border-emerald-300');
                    setTimeout(() => {
                        btn.innerHTML = '<span>📋</span> <span>Salin Pesan</span>';
                        btn.classList.remove('bg-emerald-50', 'border-emerald-300');
                    }, 2000);
                };
                bq.appendChild(btn);
            });
        }
    </script>
</div>
