<div class="min-h-screen bg-slate-100 text-slate-800 pb-16 pb-safe" wire:poll.12s>
    {{-- Top Mobile Header (Clean Navigation Bar) --}}
    <header class="sticky top-0 z-30 bg-gradient-to-r from-[#22AF85] via-[#1fa57d] to-[#1a906d] border-b border-[#188564] text-white shadow-md">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <button @click="burgerOpen = true" type="button" class="p-2 rounded-xl bg-white/15 hover:bg-white/25 text-white border border-white/20 transition-all active:scale-95 focus:outline-none" title="Buka Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="{{ route('mobile.production.index') }}" class="flex items-center gap-1.5 text-xs font-black text-white hover:text-white bg-white/15 hover:bg-white/25 px-3 py-1.5 rounded-xl border border-white/20 transition-all active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    <span>Antrean SPK</span>
                </a>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/20 text-white border border-white/30 backdrop-blur-xs shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#FFC232] animate-pulse"></span>
                    <span>{{ $order->status?->label() ?? 'PRODUKSI' }}</span>
                </span>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-5 space-y-5">

        {{-- Shoe Identity Card with Dedicated SPK Banner --}}
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200/90 relative overflow-hidden">
            {{-- SPK Header Banner inside Card (Single-line Monospace) --}}
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100">
                <div class="flex items-center gap-2 min-w-0 pr-2">
                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-[#22AF85]/15 text-[#22AF85] border border-[#22AF85]/25">
                        NO. SPK
                    </span>
                    <span class="text-sm sm:text-base font-black font-mono tracking-tight text-slate-900 select-all truncate">
                        {{ $order->spk_number }}
                    </span>
                </div>
                <div class="flex-shrink-0 text-[10px] font-bold text-slate-400">
                    {{ $order->created_at ? $order->created_at->format('d M Y') : '' }}
                </div>
            </div>

            <div class="flex gap-4 items-start">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-slate-900 flex-shrink-0 border border-slate-200 relative shadow-inner">
                    @if($order->spk_cover_photo_url)
                        <img src="{{ $order->spk_cover_photo_url }}" alt="Cover Sepatu" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-500 text-[10px] font-bold uppercase gap-1">
                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>No Foto</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0 pr-14 sm:pr-16">
                    <h2 class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">{{ $order->customer_name ?: ($order->customer->name ?? 'Pelanggan Workshop') }}</h2>
                    <p class="text-sm font-semibold text-slate-600 mt-1">{{ $order->shoe_brand ?? '-' }} {{ $order->shoe_type ?: ($order->shoe_model ?? '') }}</p>
                    <p class="text-xs text-slate-400 font-mono mt-1">{{ $order->shoe_color ?? '-' }}</p>
                    
                    {{-- Priority / Warranty Tag --}}
                    <div class="flex gap-2 mt-2.5 flex-wrap">
                        @if($order->is_warranty)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                🛡️ Garansi
                            </span>
                        @endif
                        @if($order->priority && $order->priority !== 'normal')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300 uppercase">
                                🔥 {{ $order->priority }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Shoe Size Extra Big Badge (Brand Colors) --}}
                <div class="absolute top-16 right-4 sm:top-16 sm:right-5 bg-gradient-to-br from-[#22AF85] to-teal-700 text-white w-13 h-13 sm:w-15 sm:h-15 rounded-2xl flex flex-col items-center justify-center shadow-lg" style="width:52px;height:52px">
                    <span class="text-[8px] font-bold uppercase tracking-wider text-emerald-100 mb-0.5">Size</span>
                    <span class="text-xl font-black leading-none">{{ $order->shoe_size ?? '-' }}</span>
                </div>
            </div>

            {{-- Service Details --}}
            <div class="mt-5 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-4 rounded-full bg-[#22AF85]"></span>
                        <p class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Layanan & Treatment SPK</p>
                    </div>
                    <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200/60">
                        {{ $order->workOrderServices->count() }} Jasa
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($order->workOrderServices as $svc)
                        <div class="bg-slate-50/90 hover:bg-slate-50 border border-slate-200/90 rounded-2xl p-4 transition-all shadow-2xs">
                            {{-- Header Service: Name + Category & Badge --}}
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-xl bg-[#22AF85]/10 text-[#22AF85] flex items-center justify-center shrink-0 mt-0.5 border border-[#22AF85]/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 pt-0.5">
                                        <h4 class="text-sm font-extrabold text-slate-800 leading-snug break-words">
                                            {{ $svc->service_name }}
                                        </h4>
                                        @if($svc->is_cx_additional)
                                            <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 mt-1.5 rounded-md bg-amber-100 text-amber-900 border border-amber-300">
                                                <span>⚡ Layanan Tambahan (CX)</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-[10px] font-black px-3 py-1.5 rounded-xl bg-[#22AF85]/10 text-[#22AF85] border border-[#22AF85]/20 uppercase whitespace-nowrap shrink-0 shadow-2xs leading-none">
                                    {{ $svc->category_label }}
                                </span>
                            </div>

                            {{-- Sub-Item Detail Jasa (Jika Ada) --}}
                            @if(!empty($svc->parsed_details) && count($svc->parsed_details) > 0)
                                <div class="mt-3 pt-3 border-t border-slate-200/60">
                                    <div class="flex items-center gap-1.5 text-[9px] font-black text-slate-400 uppercase tracking-wider mb-2">
                                        <svg class="w-3 h-3 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                        </svg>
                                        <span>Rincian & Instruksi Jasa:</span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($svc->parsed_details as $detail)
                                            <div class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 bg-white px-3 py-1.5 rounded-xl border border-slate-200/90 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#22AF85] shrink-0"></span>
                                                <span class="leading-snug">{{ $detail }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-400 text-sm italic">
                            Tidak ada rincian jasa khusus.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Technician Notes / Catatan Gudang & Assessment --}}
            @php
                $techNote = trim($order->technician_notes ?? '');
                $qcNote = trim($order->warehouse_qc_notes ?? '');
                $hasNotes = !empty($techNote) || !empty($qcNote);
                $isSameNote = $techNote !== '' && $qcNote !== '' && (strtolower($techNote) === strtolower($qcNote));
            @endphp

            @if($hasNotes)
                <div class="mt-5 bg-gradient-to-br from-amber-50 to-amber-50/50 border-l-[5px] border-l-amber-500 border border-amber-200/80 rounded-2xl p-4 sm:p-5 shadow-sm">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-3.5">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-black text-amber-900 uppercase tracking-wider leading-tight">Catatan Gudang & Assessment</p>
                                <p class="text-[10px] font-semibold text-amber-700 mt-0.5">Instruksi Khusus Teknisi Workshop</p>
                            </div>
                        </div>
                        <span class="text-[9px] font-black px-2.5 py-1 rounded-full bg-amber-500 text-white uppercase tracking-wider shrink-0 shadow-sm">
                            PENTING
                        </span>
                    </div>

                    {{-- Note Content Box --}}
                    @if($isSameNote || (!empty($techNote) && empty($qcNote)))
                        <div class="bg-white rounded-xl p-4 border border-amber-200 shadow-xs">
                            <p class="text-sm font-bold text-amber-950 leading-relaxed break-words">
                                {{ $techNote }}
                            </p>
                        </div>
                    @elseif(empty($techNote) && !empty($qcNote))
                        <div class="bg-white rounded-xl p-4 border border-amber-200 shadow-xs">
                            <p class="text-sm font-bold text-amber-950 leading-relaxed break-words">
                                {{ $qcNote }}
                            </p>
                        </div>
                    @else
                        {{-- Keduanya ada dan berbeda isinya --}}
                        <div class="space-y-3">
                            <div class="bg-white rounded-xl p-4 border border-amber-200 shadow-xs">
                                <span class="text-[10px] font-black text-amber-700 uppercase tracking-wider block mb-1.5">📋 Catatan Gudang / Teknisi:</span>
                                <p class="text-sm font-bold text-amber-950 leading-relaxed break-words">
                                    {{ $techNote }}
                                </p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-amber-200 shadow-xs">
                                <span class="text-[10px] font-black text-amber-700 uppercase tracking-wider block mb-1.5">🔍 Catatan QC Gudang:</span>
                                <p class="text-sm font-bold text-amber-950 leading-relaxed break-words">
                                    {{ $qcNote }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Section Title --}}
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-600 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#22AF85]"></span>
                <span>Stasiun Pengerjaan Produksi</span>
            </h3>
            <span class="text-[10px] font-bold text-slate-400">Tekan tombol untuk aksi</span>
        </div>

        {{-- Station Cards List (Responsive Grid: 1 col on mobile, 2 cols on tablet/desktop) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
            @foreach($stations as $stKey => $st)
                @php
                    $isPending = !$st['started_at'] && !$st['completed_at'] && !$st['is_unneeded'];
                    $isRunning = $st['started_at'] && !$st['completed_at'] && !$st['is_unneeded'];
                    $isCompleted = (bool) $st['completed_at'] && !$st['is_unneeded'];
                    $isUnneeded = (bool) $st['is_unneeded'];
                    $relevantTechs = $techOptions[$stKey] ?? $techOptions['all'];

                    $isPaused = (bool) ($st['is_paused'] ?? false);

                    if ($isUnneeded) {
                        $cardClass = 'bg-slate-100 border-slate-300 opacity-60';
                        $dotClass = 'bg-slate-400';
                    } elseif ($isRunning && $isPaused) {
                        $cardClass = 'bg-amber-50/90 border-amber-300 ring-2 ring-amber-300/40';
                        $dotClass = 'bg-amber-500';
                    } elseif ($isRunning) {
                        $cardClass = 'bg-blue-50/90 border-blue-400 ring-2 ring-blue-400/30';
                        $dotClass = 'bg-blue-500 animate-ping';
                    } elseif ($isCompleted) {
                        $cardClass = 'bg-emerald-50/90 border-[#22AF85]';
                        $dotClass = 'bg-[#22AF85]';
                    } else {
                        $cardClass = 'bg-white border-slate-200';
                        $dotClass = 'bg-[#FFC232]';
                    }
                @endphp

                <div class="rounded-2xl border transition-all duration-200 shadow-sm p-4 relative flex flex-col justify-between {{ $cardClass }}">

                    <div>
                        {{-- Station Header --}}
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $dotClass }}"></span>
                                <h4 class="text-sm font-black text-slate-900">{{ $st['name'] }}</h4>
                            </div>

                            {{-- Status Pill --}}
                            @if($isUnneeded)
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-200 text-slate-600">
                                    ⚪ Tidak Diperlukan
                                </span>
                            @elseif($isRunning && $isPaused)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-slate-950 uppercase tracking-wider">
                                    ⏸ Dijeda
                                </span>
                            @elseif($isRunning)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-600 text-white uppercase tracking-wider animate-pulse">
                                    ⚡ Sedang Berjalan
                                </span>
                            @elseif($isCompleted)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-[#22AF85] text-white uppercase tracking-wider">
                                    ✅ Selesai
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900">
                                    ⏳ Belum Mulai
                                </span>
                            @endif
                        </div>

                        {{-- Card Body --}}
                        @if($isUnneeded)
                            <div class="py-2 flex items-center justify-between text-xs text-slate-500">
                                <span>Stasiun dilewati untuk sepatu ini.</span>
                                <button wire:click="enableStation('{{ $stKey }}')" type="button" class="text-xs font-bold text-[#22AF85] hover:underline px-2 py-1 rounded bg-[#22AF85]/10 border border-[#22AF85]/20">
                                    Aktifkan
                                </button>
                            </div>
                        @elseif($isCompleted)
                            @php
                                $start = $st['started_at'] ? \Carbon\Carbon::parse($st['started_at']) : null;
                                $end = \Carbon\Carbon::parse($st['completed_at']);
                                $durationMin = $start ? $start->diffInMinutes($end) : 0;
                                $durationStr = $durationMin > 60 ? floor($durationMin / 60) . ' Jam ' . ($durationMin % 60) . ' Menit' : $durationMin . ' Menit';
                            @endphp
                            <div class="mt-2 pt-2 border-t border-emerald-200/80 flex items-center justify-between text-xs">
                                <div class="text-emerald-900 font-bold">
                                    <span>Oleh: <strong class="font-extrabold text-slate-900">{{ $st['tech_name'] ?? 'Teknisi' }}</strong></span>
                                </div>
                                <div class="text-[#22AF85] font-mono font-black">
                                    ⏱ {{ $durationStr }}
                                </div>
                            </div>
                        @elseif($isRunning)
                            @if(!$isPaused)
                                {{-- Active Running Station with Live Net Stopwatch --}}
                                <div class="mt-2 space-y-3">
                                    <div class="flex items-center justify-between bg-white px-3.5 py-2.5 rounded-xl border border-blue-200 shadow-2xs">
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase">Teknisi Bertugas:</p>
                                            <p class="text-xs font-black text-slate-900">{{ $st['tech_name'] ?? 'Teknisi' }}</p>
                                        </div>
                                        <div class="text-right" 
                                             x-data="{
                                                 baseSeconds: {{ $st['net_seconds'] }},
                                                 clientStart: Math.floor(Date.now() / 1000),
                                                 timerText: '⏱ 00:00',
                                                 updateTimer() {
                                                     let diff = Math.max(0, this.baseSeconds + (Math.floor(Date.now() / 1000) - this.clientStart));
                                                     let hours = Math.floor(diff / 3600);
                                                     let minutes = Math.floor((diff % 3600) / 60);
                                                     let seconds = diff % 60;
                                                     this.timerText = '⏱ ' + (hours > 0 ? (hours < 10 ? '0' + hours : hours) + ':' : '') + 
                                                                      (minutes < 10 ? '0' + minutes : minutes) + ':' + 
                                                                      (seconds < 10 ? '0' + seconds : seconds);
                                                 }
                                             }"
                                             x-init="updateTimer(); setInterval(() => updateTimer(), 1000)">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase">Durasi Kerja:</p>
                                            <p class="text-sm font-black font-mono text-blue-600" x-text="timerText">⏱ 00:00</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <button wire:click="openPauseSheet('{{ $stKey }}')"
                                                type="button"
                                                class="h-11 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl font-black text-xs uppercase tracking-wider shadow-md shadow-amber-500/20 flex items-center justify-center gap-1.5 active:scale-95 transition-all">
                                            <span>⏸</span>
                                            <span>Jeda Pengerjaan</span>
                                        </button>
                                        <button wire:click="promptFinish('{{ $stKey }}')"
                                                type="button"
                                                class="h-11 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-md shadow-rose-600/30 flex items-center justify-center gap-1.5 active:scale-95 transition-all">
                                            <span>✓</span>
                                            <span>Selesaikan</span>
                                        </button>
                                    </div>
                                </div>
                            @else
                                {{-- Paused Station (Frozen Timer) --}}
                                @php
                                    $fSecs = $st['net_seconds'];
                                    $fH = floor($fSecs / 3600);
                                    $fM = floor(($fSecs % 3600) / 60);
                                    $fS = $fSecs % 60;
                                    $frozenStr = '⏱ ' . ($fH > 0 ? sprintf('%02d:', $fH) : '') . sprintf('%02d:%02d', $fM, $fS);
                                @endphp
                                <div class="mt-2 space-y-3">
                                    <div class="bg-amber-100/70 border border-amber-300 px-3.5 py-2.5 rounded-xl flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-bold text-amber-800 uppercase">Status & Alasan Jeda:</p>
                                            <p class="text-xs font-black text-amber-950">{{ $st['pause_reason'] ?: 'Istirahat' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[10px] font-bold text-amber-800 uppercase">Waktu Bersih Terakhir:</p>
                                            <p class="text-sm font-black font-mono text-amber-950">{{ $frozenStr }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <button wire:click="resumeStation('{{ $stKey }}')"
                                                type="button"
                                                class="h-11 bg-gradient-to-r from-[#22AF85] to-teal-600 hover:from-[#1b8e6c] hover:to-teal-700 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-md shadow-[#22AF85]/30 flex items-center justify-center gap-1.5 active:scale-95 transition-all animate-pulse">
                                            <span>▶</span>
                                            <span>Lanjutkan</span>
                                        </button>
                                        <button wire:click="promptFinish('{{ $stKey }}')"
                                                type="button"
                                                class="h-11 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-md shadow-rose-600/30 flex items-center justify-center gap-1.5 active:scale-95 transition-all">
                                            <span>✓</span>
                                            <span>Selesaikan</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @else
                            {{-- Pending Station --}}
                            <div class="mt-2 space-y-2.5">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Pilih Teknisi:</label>
                                    <select wire:change="selectTechnician('{{ $stKey }}', $event.target.value)"
                                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-[#22AF85] focus:outline-none">
                                        <option value="">-- Pilih Teknisi --</option>
                                        <option value="unneeded">⚪ Tidak Diperlukan</option>
                                        @foreach($relevantTechs as $tech)
                                            <option value="{{ $tech->id }}" {{ $st['tech_id'] == $tech->id ? 'selected' : '' }}>
                                                {{ $tech->name }} ({{ $tech->specialization ?: $tech->station }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button wire:click="startStation('{{ $stKey }}')"
                                        type="button"
                                        class="w-full h-11 bg-gradient-to-r from-[#22AF85] to-teal-600 hover:from-[#1b8e6c] hover:to-teal-700 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-md shadow-[#22AF85]/30 flex items-center justify-center gap-2 active:scale-95 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Mulai Kerja</span>
                                </button>
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    </main>

    {{-- Bottom Sheet: Jeda Pengerjaan (Pilih Alasan Cepat) --}}
    @if($pauseStation)
        @php
            $stationTitle = $stations[$pauseStation]['name'] ?? $pauseStation;
            $reasonIcons = [
                'Istirahat / Makan / Sholat' => '☕',
                'Menunggu Bahan / Material / Sparepart' => '📦',
                'Menunggu Lem Kering / Proses Kimia' => '⏳',
                'Kerjakan SPK Prioritas Lain' => '🔄',
                'Kendala Mesin / Alat Workshop' => '⚠️',
            ];
        @endphp
        <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/70 backdrop-blur-xs animate-in fade-in duration-200">
            <div class="bg-white w-full max-w-md rounded-t-3xl shadow-2xl border-t border-slate-200 flex flex-col max-h-[85vh] overflow-hidden animate-in slide-in-from-bottom duration-250">
                
                {{-- Sheet Header (Pinned at Top) --}}
                <div class="p-4 pb-3 border-b border-slate-100 bg-white shrink-0">
                    <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-3"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black font-mono text-[#22AF85]">{{ $order->spk_number }}</span>
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300">
                                    ⏸ Jeda {{ $stationTitle }}
                                </span>
                            </div>
                            <h3 class="text-sm font-black text-slate-900 mt-1">
                                Pilih Alasan Jeda Pengerjaan
                            </h3>
                        </div>
                        <button wire:click="closePauseSheet" type="button" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Sheet Scrollable Content --}}
                <div class="p-4 space-y-3.5 overflow-y-auto flex-1 overscroll-contain">
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">
                        Timer pengerjaan akan dibekukan (freeze) dan durasi jeda tidak dihitung sebagai jam kerja aktif teknisi.
                    </p>

                    {{-- 5 Preset Reason Cards --}}
                    <div class="space-y-2">
                        @foreach(\App\Livewire\Mobile\SpkTracker::PAUSE_REASONS as $rOption)
                            @php
                                $isSelected = $pauseReason === $rOption;
                            @endphp
                            <button wire:click="selectPauseReason('{{ $rOption }}')"
                                    type="button"
                                    class="w-full text-left p-3 rounded-2xl border transition-all flex items-center justify-between gap-3 active:scale-[0.99]
                                    {{ $isSelected ? 'border-amber-400 bg-amber-50/70 shadow-xs ring-1 ring-amber-300' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                    <span class="text-lg shrink-0">{{ $reasonIcons[$rOption] ?? '⏸' }}</span>
                                    <span class="text-xs font-black text-slate-900 leading-snug">{{ $rOption }}</span>
                                </div>
                                <div class="w-5 h-5 rounded-full border flex items-center justify-center shrink-0 {{ $isSelected ? 'border-amber-500 bg-amber-500 text-slate-950 font-black' : 'border-slate-300 bg-white' }}">
                                    @if($isSelected)
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>

                    {{-- Optional Custom Note --}}
                    <div class="pt-1">
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Catatan Tambahan (Opsional):</label>
                        <input type="text" 
                               wire:model="pauseCustomNote" 
                               placeholder="Contoh: Menunggu lem fox kering / istirahat dzuhur..."
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-amber-400 focus:outline-none placeholder:text-slate-400">
                    </div>
                </div>

                {{-- Sheet Footer (Pinned at Bottom) --}}
                <div class="p-4 pt-3 border-t border-slate-100 bg-white shrink-0 grid grid-cols-2 gap-2.5 shadow-lg">
                    <button wire:click="closePauseSheet" 
                            type="button" 
                            class="h-11 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 transition-colors active:scale-95">
                        Batal
                    </button>
                    <button wire:click="confirmPause" 
                            type="button" 
                            class="h-11 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs shadow-md shadow-amber-500/30 transition-all active:scale-95 flex items-center justify-center gap-1.5">
                        <span>⏸ Konfirmasi Jeda</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Selesai --}}
    @if($confirmStation)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 animate-in fade-in duration-150">
            <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl border border-slate-100 text-center space-y-4">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Selesaikan Pengerjaan?</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Pengerjaan untuk <strong class="text-slate-800">{{ $stations[$confirmStation]['name'] ?? 'Stasiun' }}</strong> akan ditandai selesai.
                    </p>
                    <div class="mt-3 py-2 px-3 bg-slate-100 rounded-xl inline-block text-xs font-mono font-bold text-slate-700">
                        Durasi Kerja: <span class="text-[#22AF85] font-black">{{ $confirmDuration }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2.5 pt-2">
                    <button wire:click="cancelConfirm" type="button" class="h-11 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 transition-colors">
                        Batal
                    </button>
                    <button wire:click="executeFinish" type="button" class="h-11 rounded-xl bg-rose-600 text-white font-black text-xs hover:bg-rose-700 shadow-md shadow-rose-600/30 transition-colors">
                        Ya, Selesaikan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
