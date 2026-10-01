<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#141414">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <title>Living Report R&D — {{ $order->spk_number }}</title>
    <meta name="description" content="Laporan riset laboratorium R&D untuk SPK {{ $order->spk_number }} — {{ $order->shoe_brand }} {{ $order->shoe_type }}">

    {{-- Tailwind CSS & Alpine.js --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'sw-green': '#2EBD8A',
                        'sw-yellow': '#FBBB2D',
                        'ink': '#141414',
                        'ink-panel': '#1C1C1C',
                        'ink-border': '#2A2A2A',
                        'concrete-light': '#F2F1EC',
                        'concrete': '#EDEBE3',
                        'concrete-dark': '#ACB0B5',
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        * { -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #141414; color: #E8E6E1; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .amber-glow-sm { box-shadow: 0 0 12px rgba(46,189,138,0.2); }
        .glass { background: rgba(28,28,28,0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.06); }
        .glass-light { background: rgba(255,255,255,0.04); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.06); }
        .hero-gradient { background: radial-gradient(ellipse 140% 100% at 50% -10%, rgba(46,189,138,0.13) 0%, transparent 70%), radial-gradient(ellipse 60% 40% at 90% 20%, rgba(251,187,45,0.07) 0%, transparent 60%), #141414; }
        .timeline-spine { background: linear-gradient(to bottom, #2EBD8A 0%, rgba(46,189,138,0.3) 50%, rgba(42,42,42,0.2) 100%); }
        .badge-success  { background: rgba(46,189,138,0.15);  color: #2EBD8A; border: 1px solid rgba(46,189,138,0.3); }
        .badge-progress { background: rgba(251,187,45,0.12);   color: #FBBB2D; border: 1px solid rgba(251,187,45,0.3); }
        .badge-revision { background: rgba(251,146,60,0.12);   color: #FB923C; border: 1px solid rgba(251,146,60,0.3); }
        .badge-failed   { background: rgba(248,113,113,0.12);  color: #F87171; border: 1px solid rgba(248,113,113,0.3); }
        .photo-card:hover .photo-overlay { opacity: 1 !important; }
        .photo-card:hover .photo-img { transform: scale(1.04); }
        .photo-overlay { transition: opacity 0.25s ease; }
        .photo-img { transition: transform 0.35s ease; }
        @keyframes pulse-green { 0%,100% { box-shadow: 0 0 0 0 rgba(46,189,138,0.5); } 50% { box-shadow: 0 0 0 5px rgba(46,189,138,0); } }
        .pulse-green { animation: pulse-green 2s infinite; }
        #scroll-bar { transform-origin: left; }
        @media print { .no-print { display: none !important; } body { background: white !important; color: black !important; } }
    </style>
</head>
<body class="min-h-screen antialiased" x-data="livingReportApp()">

    {{-- SCROLL PROGRESS --}}
    <div class="fixed top-0 left-0 right-0 z-[100] h-0.5 bg-ink-border no-print">
        <div id="scroll-bar" class="h-full bg-sw-green origin-left scale-x-0" style="transition: transform 0.075s linear;"></div>
    </div>

    {{-- STICKY HEADER --}}
    <header class="sticky top-0 z-40 glass border-b border-ink-border no-print">
        <div class="max-w-2xl mx-auto px-4 h-14 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-7 w-auto object-contain shrink-0">
                <div class="w-px h-4 bg-ink-border shrink-0"></div>
                <div class="flex items-center gap-1.5 min-w-0">
                    <span class="text-[10px] font-black uppercase tracking-widest text-sw-green shrink-0">R&D</span>
                    <span class="text-[10px] text-concrete-dark hidden sm:inline truncate">Living Report</span>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full glass-light text-[10px] font-bold text-concrete-dark">
                    <span class="relative flex h-1.5 w-1.5 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sw-green opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-sw-green"></span>
                    </span>
                    <span class="hidden sm:inline">Real-Time</span>
                </div>
                <button type="button" @click="copySpkNumber('{{ $order->spk_number }}')"
                        class="flex items-center gap-1.5 px-2.5 py-1 rounded-full glass-light text-[10px] font-mono font-bold text-concrete-dark hover:text-sw-green transition-all active:scale-95">
                    <span class="opacity-50 text-[9px]">SPK</span>
                    <span>{{ $order->spk_number }}</span>
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3"/></svg>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 pt-5 space-y-4 pb-4">

        {{-- HERO CARD --}}
        @php
            $isFinished = ($order->status->value ?? $order->status) === 'SELESAI';
            $successRate = $totalStages > 0 ? round(($countSuccess / $totalStages) * 100) : 0;
        @endphp
        <section class="hero-gradient rounded-3xl overflow-hidden border border-ink-border relative">
            <div class="h-px w-full" style="background: linear-gradient(90deg, transparent, #2EBD8A 50%, transparent); opacity: 0.6;"></div>
            <div class="p-5 space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[9px] font-black uppercase tracking-widest text-concrete-dark bg-ink-panel px-2.5 py-1 rounded-full border border-ink-border">Proyek Eksperimen</span>
                    @if($isFinished)
                    <span class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full badge-success">✓ Riset Selesai</span>
                    @else
                    <span class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full badge-progress pulse-green">⏳ Dalam Proses</span>
                    @endif
                </div>
                <div class="space-y-0.5">
                    <h1 class="font-poppins font-black text-concrete-light tracking-tight leading-tight" style="font-size: 1.5rem;">{{ $order->shoe_brand ?? 'Sepatu Riset' }}</h1>
                    <p class="font-mono font-semibold text-sw-green tracking-tight" style="font-size: 1rem;">{{ $order->shoe_type }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex items-center gap-1.5 bg-ink-panel border border-ink-border rounded-xl px-2.5 py-1.5 text-[10px]">
                        <span class="text-concrete-dark">Customer</span>
                        <span class="font-bold text-concrete-light">{{ $order->customer_name ?? 'Internal' }}</span>
                    </div>
                    @if($order->shoe_size)
                    <div class="flex items-center gap-1.5 bg-ink-panel border border-ink-border rounded-xl px-2.5 py-1.5 text-[10px]">
                        <span class="text-concrete-dark">Size</span>
                        <span class="font-mono font-bold text-concrete-light">{{ $order->shoe_size }}</span>
                    </div>
                    @endif
                    @if($order->shoe_color)
                    <div class="flex items-center gap-1.5 bg-ink-panel border border-ink-border rounded-xl px-2.5 py-1.5 text-[10px]">
                        <span class="text-concrete-dark">Warna</span>
                        <span class="font-bold text-concrete-light">{{ $order->shoe_color }}</span>
                    </div>
                    @endif
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-[10px]">
                        <span class="text-concrete-dark font-semibold uppercase tracking-wider">Tingkat Keberhasilan</span>
                        <span class="font-mono font-black text-sw-green">{{ $successRate }}%</span>
                    </div>
                    <div class="h-1.5 w-full bg-ink-border rounded-full overflow-hidden">
                        <div class="h-full rounded-full amber-glow-sm" style="width: {{ $successRate }}%; background: linear-gradient(90deg, #2EBD8A, #FBBB2D);"></div>
                    </div>
                </div>
            </div>
        </section>


        {{-- VIEW TOGGLE --}}
        <section class="flex items-center justify-between gap-3 px-0.5">
            <div class="min-w-0">
                <h2 class="text-sm font-poppins font-black text-concrete-light tracking-tight">Kronologi Riset Lab</h2>
                <p class="text-[10px] text-concrete-dark"><span x-text="filteredCount()"></span> tahap eksperimen</p>
            </div>
            <div class="flex items-center gap-1 bg-ink-panel border border-ink-border p-1 rounded-2xl shrink-0">
                <button type="button" @click="viewMode = 'timeline'"
                        :class="viewMode === 'timeline' ? 'bg-sw-green text-ink font-black shadow-sm' : 'text-concrete-dark hover:text-concrete-light font-semibold'"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] transition-all active:scale-95">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Timeline
                </button>
                <button type="button" @click="viewMode = 'grid'"
                        :class="viewMode === 'grid' ? 'bg-sw-green text-ink font-black shadow-sm' : 'text-concrete-dark hover:text-concrete-light font-semibold'"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] transition-all active:scale-95">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Grid
                </button>
            </div>
        </section>

        {{-- EMPTY STATE --}}
        @if($progresses->isEmpty())
        <section class="rounded-3xl border border-dashed border-ink-border bg-ink-panel p-10 text-center space-y-3">
            <div class="w-12 h-12 mx-auto rounded-2xl flex items-center justify-center text-sw-green"
                 style="background: rgba(46,189,138,0.1); border: 1px solid rgba(46,189,138,0.2);">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-concrete-light">Belum Ada Foto Terunggah</h3>
            <p class="text-[11px] text-concrete-dark max-w-xs mx-auto leading-relaxed">Teknisi lab belum mengunggah foto tahap pertama. Foto akan muncul secara real-time setelah diunggah.</p>
        </section>
        @else

        {{-- TIMELINE MODE --}}
        <div x-show="viewMode === 'timeline'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="relative space-y-0 pl-5">
            <div class="absolute left-[22px] top-5 bottom-5 w-px timeline-spine"></div>

            @foreach($progresses as $index => $item)
            @php
                $statusKey = $item->result_status;
                $conf = match($statusKey) {
                    'SUCCESS'       => ['label' => 'Berhasil',   'badge' => 'badge-success',  'node' => '#10B981', 'ring' => 'rgba(16,185,129,0.25)'],
                    'NEED_REVISION' => ['label' => 'Revisi',     'badge' => 'badge-revision', 'node' => '#F97316', 'ring' => 'rgba(249,115,22,0.25)'],
                    'FAILED'        => ['label' => 'Drop/Gagal', 'badge' => 'badge-failed',   'node' => '#EF4444', 'ring' => 'rgba(239,68,68,0.25)'],
                    default         => ['label' => 'Proses',     'badge' => 'badge-progress', 'node' => '#2EBD8A', 'ring' => 'rgba(245,197,24,0.25)'],
                };
            @endphp
            <div class="relative flex items-start gap-4 pb-5"
                 x-show="shouldShowItem('{{ $statusKey }}')"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0">
                <div class="relative z-10 w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-3"
                     style="background: {{ $conf['node'] }}; box-shadow: 0 0 0 4px {{ $conf['ring'] }}, 0 0 0 5px #141414;">
                    <span class="text-[9px] font-black font-mono text-ink leading-none">{{ $index + 1 }}</span>
                </div>
                <div class="flex-1 photo-card rounded-2xl bg-ink-panel border border-ink-border overflow-hidden transition-all duration-300 cursor-pointer hover:border-sw-green/30"
                     @click="openLightbox('{{ asset('storage/' . $item->photo_path) }}', '{{ addslashes($item->stage_title) }}', '{{ addslashes($item->notes ?? '') }}', '{{ $statusKey }}', '{{ $item->created_at->format('d M Y • H:i') }}', {{ $index + 1 }})">
                    <div class="px-3.5 py-2.5 flex items-center justify-between gap-2 border-b border-ink-border">
                        <span class="text-xs font-poppins font-bold text-concrete-light truncate">{{ $item->stage_title }}</span>
                        <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full shrink-0 {{ $conf['badge'] }}">{{ $conf['label'] }}</span>
                    </div>
                    <div class="relative aspect-video bg-ink overflow-hidden">
                        <img src="{{ asset('storage/' . $item->photo_path) }}" alt="{{ $item->stage_title }}" loading="lazy"
                             class="photo-img w-full h-full object-cover">
                        <div class="photo-overlay absolute inset-0 flex items-end justify-between p-3"
                             style="opacity: 0; background: linear-gradient(to top, rgba(20,20,20,0.8), transparent);">
                            <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-black shadow-lg"
                                  style="background: #2EBD8A; color: white;">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Perbesar
                            </span>
                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-lg"
                                  style="color: rgba(255,255,255,0.8); background: rgba(20,20,20,0.7);">{{ $item->created_at->format('d/m H:i') }}</span>
                        </div>
                    </div>
                    <div class="p-3.5 space-y-2">
                        @if($item->notes)
                        <div class="bg-ink rounded-xl border border-ink-border p-3 space-y-1">
                            <span class="text-[9px] font-black uppercase tracking-widest font-mono block" style="color: rgba(46,189,138,0.7);">🧪 Catatan Formula:</span>
                            <p class="text-[11px] text-concrete-dark font-mono whitespace-pre-line leading-relaxed">{{ $item->notes }}</p>
                        </div>
                        @else
                        <p class="text-[11px] italic" style="color: rgba(172,176,181,0.5);">Tidak ada catatan tambahan.</p>
                        @endif
                        <div class="flex items-center justify-between pt-1.5 border-t border-ink-border text-[9px] font-mono" style="color: rgba(172,176,181,0.5);">
                            <span>Tahap #{{ $index + 1 }} / {{ $totalStages }}</span>
                            <span class="font-bold" style="color: rgba(46,189,138,0.6);">Lab R&D Verified ✓</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- GRID MODE --}}
        <div x-show="viewMode === 'grid'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-2 gap-3">
            @foreach($progresses as $index => $item)
            @php
                $statusKey = $item->result_status;
                $conf = match($statusKey) {
                    'SUCCESS'       => ['label' => 'Berhasil', 'badge' => 'badge-success',  'dot' => '#10B981'],
                    'NEED_REVISION' => ['label' => 'Revisi',   'badge' => 'badge-revision', 'dot' => '#F97316'],
                    'FAILED'        => ['label' => 'Gagal',    'badge' => 'badge-failed',   'dot' => '#EF4444'],
                    default         => ['label' => 'Proses',   'badge' => 'badge-progress', 'dot' => '#2EBD8A'],
                };
            @endphp
            <div class="photo-card rounded-2xl bg-ink-panel border border-ink-border overflow-hidden transition-all duration-300 cursor-pointer flex flex-col hover:border-sw-green/30"
                 x-show="shouldShowItem('{{ $statusKey }}')"
                 x-transition
                 @click="openLightbox('{{ asset('storage/' . $item->photo_path) }}', '{{ addslashes($item->stage_title) }}', '{{ addslashes($item->notes ?? '') }}', '{{ $statusKey }}', '{{ $item->created_at->format('d M Y • H:i') }}', {{ $index + 1 }})">
                <div class="relative aspect-video bg-ink overflow-hidden">
                    <img src="{{ asset('storage/' . $item->photo_path) }}" alt="{{ $item->stage_title }}" loading="lazy"
                         class="photo-img w-full h-full object-cover">
                    <div class="absolute top-2 left-2 w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-mono font-black"
                         style="background: rgba(20,20,20,0.8); backdrop-filter: blur(4px); color: #2EBD8A; border: 1px solid rgba(46,189,138,0.3);">{{ $index + 1 }}</div>
                    <div class="absolute top-2 right-2 w-2 h-2 rounded-full" style="background: {{ $conf['dot'] }}; box-shadow: 0 0 0 2px #141414;"></div>
                    <div class="photo-overlay absolute inset-0 flex items-center justify-center" style="opacity: 0; background: rgba(20,20,20,0.5);">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: rgba(46,189,138,0.9);">
                            <svg class="w-4 h-4" fill="none" stroke="#141414" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="p-2.5 flex-1 flex flex-col justify-between gap-2">
                    <p class="text-[11px] font-bold text-concrete-light truncate leading-tight">{{ $item->stage_title }}</p>
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[8px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded-full {{ $conf['badge'] }}">{{ $conf['label'] }}</span>
                        <span class="text-[9px] font-mono" style="color: rgba(172,176,181,0.6);">{{ $item->created_at->format('d/m H:i') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @endif

        {{-- LAB SPEC ACCORDION --}}
        <section class="rounded-3xl bg-ink-panel border border-ink-border overflow-hidden">
            <button type="button" @click="specOpen = !specOpen"
                    class="w-full px-4 py-3.5 flex items-center justify-between gap-3 text-left transition-colors hover:bg-ink-border/20">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-2xl flex items-center justify-center text-sm"
                         style="background: rgba(46,189,138,0.1); border: 1px solid rgba(46,189,138,0.2);">📋</div>
                    <div>
                        <h3 class="text-xs font-poppins font-black text-concrete-light">Spesifikasi & Parameter Riset</h3>
                        <p class="text-[10px] text-concrete-dark">Ketuk untuk detail eksperimen</p>
                    </div>
                </div>
                <div class="w-7 h-7 rounded-full glass-light flex items-center justify-center text-concrete-dark transition-transform duration-300" :class="specOpen ? '-rotate-180' : ''">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>
            <div x-show="specOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak
                 class="px-4 pb-4 space-y-3 border-t border-ink-border">
                <div class="pt-4 grid grid-cols-1 gap-3 text-xs">
                    <div class="p-3.5 rounded-2xl bg-ink border border-ink-border space-y-2.5">
                        <span class="text-[9px] font-black uppercase tracking-widest font-mono block" style="color: rgba(46,189,138,0.7);">Identitas SPK</span>
                        @foreach([
                            ['Nomor SPK', $order->spk_number, true],
                            ['Merk & Tipe', ($order->shoe_brand ?? '') . ' ' . ($order->shoe_type ?? ''), false],
                            ['Warna / Size', ($order->shoe_color ?? '-') . ' / ' . ($order->shoe_size ?? '-'), false],
                            ['Klasifikasi', 'R&D EXPERIMENT', false],
                        ] as [$label, $value, $mono])
                        <div class="flex items-center justify-between py-1.5 border-b border-ink-border/60 last:border-0">
                            <span class="text-concrete-dark">{{ $label }}</span>
                            <span class="{{ $mono ? 'font-mono font-bold text-sw-green' : 'font-bold text-concrete-light' }}">{{ $value }}</span>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </section>

        <footer class="text-center py-2" style="font-size: 10px; color: rgba(172,176,181,0.4);">
            <p class="font-semibold">© {{ date('Y') }} Sistem Workshop R&D Division</p>
        </footer>

    </main>


    {{-- IMMERSIVE LIGHTBOX --}}
    <template x-teleport="body">
        <div x-show="lightboxOpen" x-cloak
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] flex flex-col"
             style="background: #0A0A0A;"
             @keydown.escape.window="lightboxOpen = false; document.body.style.overflow = ''"
             @keydown.arrow-left.window="navigatePrev()"
             @keydown.arrow-right.window="navigateNext()"
             @touchstart.passive="lbTouchStart($event)"
             @touchend.passive="lbTouchEnd($event)">

            {{-- TOP BAR --}}
            <div class="shrink-0 px-4 pt-safe-top pt-4 pb-3 flex items-center justify-between gap-3"
                 style="background: linear-gradient(to bottom, rgba(10,10,10,0.9) 0%, transparent 100%); position: absolute; top: 0; left: 0; right: 0; z-index: 10;">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-mono font-black shrink-0"
                         style="background: #2EBD8A; color: white; box-shadow: 0 0 0 3px rgba(46,189,138,0.2);"
                         x-text="activeStep"></div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-poppins font-bold text-white truncate leading-tight" x-text="activeTitle"></h3>
                        <p class="text-[9px] font-mono" style="color: rgba(255,255,255,0.45);"
                           x-text="'Tahap ' + activeStep + ' dari ' + totalPhotos"></p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full"
                          :class="{ 'badge-success': activeStatus === 'SUCCESS', 'badge-revision': activeStatus === 'NEED_REVISION', 'badge-failed': activeStatus === 'FAILED', 'badge-progress': activeStatus === 'IN_PROGRESS' }"
                          x-text="statusLabel(activeStatus)"></span>
                    <button @click="lightboxOpen = false"
                            class="w-9 h-9 rounded-full flex items-center justify-center transition active:scale-90"
                            style="background: rgba(255,255,255,0.1); backdrop-filter: blur(8px);">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- PHOTO AREA --}}
            <div class="flex-1 flex items-center justify-center relative overflow-hidden select-none"
                 @click.self="lightboxOpen = false">
                <img :src="activeImage"
                     :key="activeImage"
                     class="max-w-full max-h-full object-contain"
                     style="touch-action: pinch-zoom; user-select: none; -webkit-user-drag: none;"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">

                {{-- PREV BUTTON --}}
                <button @click.stop="navigatePrev()"
                        x-show="activeStep > 1"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full flex items-center justify-center transition active:scale-90 no-print"
                        style="background: rgba(255,255,255,0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.15);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>

                {{-- NEXT BUTTON --}}
                <button @click.stop="navigateNext()"
                        x-show="activeStep < totalPhotos"
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full flex items-center justify-center transition active:scale-90 no-print"
                        style="background: rgba(255,255,255,0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.15);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            {{-- DOT INDICATOR --}}
            <div class="shrink-0 flex justify-center gap-1.5 py-2"
                 style="position: absolute; bottom: 10rem; left: 0; right: 0; z-index: 10;">
                <template x-for="i in totalPhotos" :key="i">
                    <div class="rounded-full transition-all duration-300"
                         :style="i === activeStep ? 'width: 1.5rem; height: 0.25rem; background: #2EBD8A;' : 'width: 0.25rem; height: 0.25rem; background: rgba(255,255,255,0.3);'"></div>
                </template>
            </div>

            {{-- BOTTOM INFO PANEL --}}
            <div class="shrink-0 px-4 pb-6 pt-3 space-y-2"
                 style="background: linear-gradient(to top, rgba(10,10,10,0.97) 60%, transparent 100%); position: absolute; bottom: 0; left: 0; right: 0; z-index: 10;">
                <div class="flex items-center justify-between text-[10px]">
                    <span class="font-bold" style="color: rgba(46,189,138,0.8);">🧪 Catatan Laboratorium</span>
                    <span class="font-mono" style="color: rgba(255,255,255,0.4);" x-text="activeTime"></span>
                </div>
                <div class="rounded-2xl p-3 text-[11px] font-mono leading-relaxed overflow-y-auto whitespace-pre-line"
                     style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.65); max-height: 5.5rem;"
                     x-text="activeNotes || 'Tidak ada catatan tambahan untuk tahap ini.'">
                </div>
            </div>

        </div>
    </template>

    {{-- TOAST --}}
    <div x-show="toastShow" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-16 inset-x-4 max-w-xs mx-auto z-[200] py-2.5 px-4 rounded-2xl glass text-xs font-bold text-center shadow-2xl flex items-center justify-center gap-2">
        <span class="text-sw-green">✓</span>
        <span class="text-concrete-light" x-text="toastMessage"></span>
    </div>

    {{-- ALPINE CONTROLLER --}}
    <script>
        function livingReportApp() {
            return {
                viewMode: 'timeline',
                filterStatus: 'ALL',
                specOpen: false,
                lightboxOpen: false,
                activeImage: '',
                activeTitle: '',
                activeNotes: '',
                activeStatus: '',
                activeTime: '',
                activeStep: 1,
                totalPhotos: {{ $totalStages }},
                photos: [
                    @foreach($progresses as $index => $item)
                    {
                        img: '{{ asset('storage/' . $item->photo_path) }}',
                        title: '{{ addslashes($item->stage_title) }}',
                        notes: '{{ addslashes($item->notes ?? '') }}',
                        status: '{{ $item->result_status }}',
                        time: '{{ $item->created_at->format('d M Y • H:i') }}',
                        step: {{ $index + 1 }}
                    }{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ],
                copiedLink: false,
                toastShow: false,
                toastMessage: '',
                _lbTouchX: 0,

                init() {
                    window.addEventListener('scroll', () => {
                        const el = document.getElementById('scroll-bar');
                        if (!el) return;
                        const scrollTop = window.scrollY;
                        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                        const progress = docHeight > 0 ? scrollTop / docHeight : 0;
                        el.style.transform = `scaleX(${progress})`;
                    }, { passive: true });
                },

                openLightbox(img, title, notes, status, time, step) {
                    this.activeImage = img;
                    this.activeTitle = title;
                    this.activeNotes = notes;
                    this.activeStatus = status;
                    this.activeTime = time;
                    this.activeStep = step || 1;
                    this.lightboxOpen = true;
                    // Lock body scroll
                    document.body.style.overflow = 'hidden';
                },

                closeLightbox() {
                    this.lightboxOpen = false;
                    document.body.style.overflow = '';
                },

                navigatePrev() {
                    const idx = this.activeStep - 2;
                    if (idx >= 0 && this.photos[idx]) {
                        const p = this.photos[idx];
                        this.activeImage  = p.img;
                        this.activeTitle  = p.title;
                        this.activeNotes  = p.notes;
                        this.activeStatus = p.status;
                        this.activeTime   = p.time;
                        this.activeStep   = p.step;
                    }
                },

                navigateNext() {
                    const idx = this.activeStep;
                    if (idx < this.photos.length && this.photos[idx]) {
                        const p = this.photos[idx];
                        this.activeImage  = p.img;
                        this.activeTitle  = p.title;
                        this.activeNotes  = p.notes;
                        this.activeStatus = p.status;
                        this.activeTime   = p.time;
                        this.activeStep   = p.step;
                    }
                },

                lbTouchStart(e) {
                    this._lbTouchX = e.changedTouches[0].clientX;
                },

                lbTouchEnd(e) {
                    const dx = e.changedTouches[0].clientX - (this._lbTouchX || 0);
                    if (Math.abs(dx) > 50) {
                        dx < 0 ? this.navigateNext() : this.navigatePrev();
                    }
                },

                statusLabel(status) {
                    const map = { 'SUCCESS': 'Berhasil', 'NEED_REVISION': 'Revisi', 'FAILED': 'Gagal', 'IN_PROGRESS': 'Proses' };
                    return map[status] || status;
                },

                shouldShowItem(itemStatus) {
                    if (this.filterStatus === 'ALL') return true;
                    return this.filterStatus === itemStatus;
                },

                filteredCount() {
                    const total = {{ $totalStages }};
                    if (this.filterStatus === 'ALL') return total;
                    if (this.filterStatus === 'SUCCESS') return {{ $countSuccess }};
                    if (this.filterStatus === 'IN_PROGRESS') return {{ $countInProgress }};
                    if (this.filterStatus === 'NEED_REVISION') return {{ $countRevision }};
                    if (this.filterStatus === 'FAILED') return {{ $countFailed }};
                    return total;
                },

                showToast(msg) {
                    this.toastMessage = msg;
                    this.toastShow = true;
                    setTimeout(() => { this.toastShow = false; }, 2500);
                },

                copyToClipboard(text) {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(text).catch(() => this._fallbackCopy(text));
                    } else {
                        this._fallbackCopy(text);
                    }
                },

                _fallbackCopy(text) {
                    const el = document.createElement('textarea');
                    el.value = text;
                    el.style.position = 'fixed';
                    el.style.opacity = '0';
                    document.body.appendChild(el);
                    el.focus();
                    el.select();
                    try { document.execCommand('copy'); } catch(e) {}
                    document.body.removeChild(el);
                },

                copyReportLink() {
                    this.copyToClipboard(window.location.href);
                    this.copiedLink = true;
                    this.showToast('Tautan laporan berhasil disalin!');
                    setTimeout(() => { this.copiedLink = false; }, 3000);
                },

                copySpkNumber(spk) {
                    this.copyToClipboard(spk);
                    this.showToast('SPK ' + spk + ' disalin!');
                },

                shareWhatsApp() {
                    const title = encodeURIComponent(`Laporan R&D: {{ $order->spk_number }} ({{ $order->shoe_brand }} {{ $order->shoe_type }})`);
                    const url = encodeURIComponent(window.location.href);
                    const text = `${title}%0A%0APantau perkembangan riset lab secara real-time:%0A${url}`;
                    window.open(`https://wa.me/?text=${text}`, '_blank');
                }
            };
        }
    </script>
</body>
</html>
