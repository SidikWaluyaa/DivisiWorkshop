<div class="min-h-screen bg-slate-100 text-slate-800 pb-24 pb-safe" wire:poll.15s.visible
     x-data="{
         scannerOpen: false,
         html5QrCode: null,
         startScanner() {
             this.scannerOpen = true;
             this.$nextTick(() => {
                 if (typeof Html5Qrcode === 'undefined') {
                     alert('Library Scanner QR belum termuat.');
                     return;
                 }
                 if (!this.html5QrCode) {
                     this.html5QrCode = new Html5Qrcode('qr-reader-container');
                 }
                 const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                 this.html5QrCode.start(
                     { facingMode: 'environment' },
                     config,
                     (decodedText) => {
                         this.stopScanner();
                         let spk = decodedText;
                         if (decodedText.includes('/m/spk/')) {
                             spk = decodedText.split('/m/spk/')[1].split('?')[0].split('#')[0];
                         } else if (decodedText.includes('/')) {
                             let parts = decodedText.split('/');
                             spk = parts[parts.length - 1];
                         }
                         spk = spk.trim();
                         window.location.href = '{{ url('/m/spk') }}/' + encodeURIComponent(spk);
                     },
                     (errorMessage) => {}
                 ).catch(err => {
                     console.error('Camera error: ', err);
                     alert('Gagal mengakses kamera: ' + err);
                     this.scannerOpen = false;
                 });
             });
         },
         stopScanner() {
             if (this.html5QrCode && this.html5QrCode.isScanning) {
                 this.html5QrCode.stop().then(() => {
                     this.scannerOpen = false;
                 }).catch(err => {
                     this.scannerOpen = false;
                 });
             } else {
                 this.scannerOpen = false;
             }
         },
         photoModalOpen: false,
         activePhotoSpk: '',
         activePhotoCustomer: '',
         activePhotos: [],
         activePhotoIndex: 0,
         touchStartX: 0,
         openPhotoModal(spk, customer, photos) {
             if (!photos || photos.length === 0) return;
             this.activePhotoSpk = spk;
             this.activePhotoCustomer = customer;
             this.activePhotos = photos;
             this.activePhotoIndex = 0;
             this.photoModalOpen = true;
         },
         nextPhoto() {
             if (this.activePhotos.length <= 1) return;
             this.activePhotoIndex = (this.activePhotoIndex + 1) % this.activePhotos.length;
         },
         prevPhoto() {
             if (this.activePhotos.length <= 1) return;
             this.activePhotoIndex = (this.activePhotoIndex - 1 + this.activePhotos.length) % this.activePhotos.length;
         },
         closePhotoModal() {
             this.photoModalOpen = false;
             this.activePhotos = [];
         }
     }">

    {{-- Top App Header with Burger Toggle (Brand Teal #22AF85 Gradient & Gold #FFC232 Accent) --}}
    <header class="sticky top-0 z-30 bg-gradient-to-r from-[#22AF85] via-[#1fa57d] to-[#1a906d] border-b border-[#188564] text-white shadow-md">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button @click="burgerOpen = true" type="button" class="p-2 rounded-xl bg-white/15 hover:bg-white/25 text-white border border-white/20 transition-all active:scale-95 focus:outline-none" title="Buka Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-white p-1 flex items-center justify-center shadow-md">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-xs sm:text-sm font-black tracking-wide text-white flex items-center gap-1.5">
                            <span>PRODUKSI REPARASI</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-[#FFC232] shadow-sm animate-pulse" title="Live Sync"></span>
                        </h1>
                        <p class="text-[9px] sm:text-[10px] text-emerald-100 font-medium">Stasiun Soling, Upper & QC Jahit</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="$wire.$refresh()" class="p-2 rounded-xl bg-white/15 hover:bg-white/25 text-white border border-white/20 transition-all active:scale-95" title="Refresh Data">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </div>
        </div>
    </header>

    {{-- Main Container (Responsive Width) --}}
    <main class="max-w-5xl mx-auto px-4 py-4 space-y-3.5">

        {{-- Search Input (Brand Accent Focus) --}}
        <div class="relative">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari No SPK, Pelanggan, atau Sepatu..." 
                   class="w-full bg-white border border-slate-200 rounded-2xl pl-10 pr-4 py-2.5 text-xs font-bold text-slate-800 shadow-sm focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] focus:outline-none placeholder:text-slate-400">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        {{-- 3 Segmented Tabs (Identik dengan Antrean Kerja Reparasi & Siap ACC Desktop) --}}
        <div class="grid grid-cols-3 gap-1.5 bg-slate-200/90 p-1.5 rounded-2xl">
            <button wire:click="setTab('running')" 
                    type="button" 
                    class="py-2 rounded-xl text-center transition-all flex flex-col items-center justify-center 
                    {{ $activeTab === 'running' ? 'bg-[#22AF85] text-white font-black shadow-md shadow-[#22AF85]/30' : 'text-slate-600 font-bold hover:text-slate-900' }}">
                <span class="text-[11px] leading-tight flex items-center gap-1">
                    <span>⚡ Berjalan</span>
                </span>
                <span class="text-[9px] px-2 py-0.5 rounded-full font-black mt-0.5 
                    {{ $activeTab === 'running' ? 'bg-white/20 text-white' : 'bg-slate-300 text-slate-700' }}">
                    {{ $counts['running'] ?? 0 }}
                </span>
            </button>

            <button wire:click="setTab('queued')" 
                    type="button" 
                    class="py-2 rounded-xl text-center transition-all flex flex-col items-center justify-center 
                    {{ $activeTab === 'queued' ? 'bg-[#FFC232] text-slate-950 font-black shadow-md shadow-[#FFC232]/30' : 'text-slate-600 font-bold hover:text-slate-900' }}">
                <span class="text-[11px] leading-tight flex items-center gap-1">
                    <span>⏳ Antrean</span>
                </span>
                <span class="text-[9px] px-2 py-0.5 rounded-full font-black mt-0.5 
                    {{ $activeTab === 'queued' ? 'bg-slate-950/15 text-slate-950' : 'bg-slate-300 text-slate-700' }}">
                    {{ $counts['queued'] ?? 0 }}
                </span>
            </button>

            <button wire:click="setTab('completed_today')" 
                    type="button" 
                    class="py-2 rounded-xl text-center transition-all flex flex-col items-center justify-center 
                    {{ $activeTab === 'completed_today' ? 'bg-[#22AF85] text-white font-black shadow-md shadow-[#22AF85]/30' : 'text-slate-600 font-bold hover:text-slate-900' }}">
                <span class="text-[11px] leading-tight flex items-center gap-1">
                    <span>✓ Selesai</span>
                </span>
                <span class="text-[9px] px-2 py-0.5 rounded-full font-black mt-0.5 
                    {{ $activeTab === 'completed_today' ? 'bg-white/20 text-white' : 'bg-slate-300 text-slate-700' }}">
                    {{ $counts['completed_today'] ?? 0 }}
                </span>
            </button>
        </div>

        {{-- Station Filter Pills (Hanya Stasiun Produksi Reparasi: Soling, Upper, QC Jahit) --}}
        <div class="flex gap-1.5 overflow-x-auto pb-1 scrollbar-none">
            @php
                $pills = [
                    'all' => 'Semua Stasiun',
                    'prod_sol' => 'Soling',
                    'prod_upper' => 'Upper',
                    'qc_jahit' => 'QC Jahit',
                ];
            @endphp
            @foreach($pills as $fKey => $fLabel)
                <button wire:click="setStationFilter('{{ $fKey }}')" 
                        type="button"
                        class="px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5
                        {{ $stationFilter === $fKey ? 'bg-[#22AF85] text-white shadow-sm shadow-[#22AF85]/25 ring-2 ring-[#22AF85]/30' : 'bg-white text-slate-600 border border-slate-200 hover:border-[#22AF85]' }}">
                    <span>{{ $fLabel }}</span>
                    @if(isset($stationCounts[$fKey]))
                        <span class="text-[9px] px-1.5 py-0.2 rounded-full font-black {{ $stationFilter === $fKey ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600' }}">
                            {{ $stationCounts[$fKey] }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Work Order Cards List (Responsive Grid: 1 col on mobile, 2 cols on tablet/desktop) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-0.5">
            @forelse($orders as $order)
                @php
                    $stMap = [
                        'prod_sol' => 'Soling',
                        'prod_upper' => 'Upper',
                        'qc_jahit' => 'QC Jahit',
                    ];

                    $hasSol = (bool) $order->needs_prod_sol;
                    $hasUpper = (bool) $order->needs_prod_upper;
                    $hasJahit = (bool) $order->needs_prod_jahit;
                    if (!$hasUpper && !$hasSol && !$hasJahit) {
                        $hasUpper = true;
                    }

                    $checkStationUnneeded = function($sKey) use ($order, $hasSol, $hasUpper, $hasJahit) {
                        if ($order->{"{$sKey}_started_at"} || $order->{"{$sKey}_by"}) {
                            return false;
                        }
                        $isOrdered = match($sKey) {
                            'prod_sol' => $hasSol,
                            'prod_upper' => $hasUpper,
                            'qc_jahit' => $hasJahit,
                            default => true,
                        };
                        return !$isOrdered || $order->isStationUnneeded($sKey);
                    };

                    $runningSt = null;
                    $nextPendingSt = null;

                    foreach (\App\Livewire\Mobile\ProductionControlling::STATIONS as $sKey) {
                        $isUnneeded = $checkStationUnneeded($sKey);
                        $isRun = $order->{"{$sKey}_started_at"} && !$order->{"{$sKey}_completed_at"} && !$isUnneeded;
                        $isWait = !$order->{"{$sKey}_started_at"} && !$order->{"{$sKey}_completed_at"} && !$isUnneeded;

                        if ($isRun && !$runningSt) {
                            $stPauseInfo = $this->getStationPauseInfo($order, $sKey);
                            $runningSt = [
                                'key' => $sKey,
                                'name' => $stMap[$sKey],
                                'start' => $order->{"{$sKey}_started_at"},
                                'by' => $order->{"{$sKey}_by"},
                                'is_paused' => $stPauseInfo['is_paused'],
                                'reason' => $stPauseInfo['reason'],
                                'net_seconds' => $stPauseInfo['net_elapsed_seconds'],
                                'total_paused_seconds' => $stPauseInfo['total_paused_seconds'],
                            ];
                        }
                        if ($isWait && !$nextPendingSt) {
                            $nextPendingSt = [
                                'key' => $sKey,
                                'name' => $stMap[$sKey],
                            ];
                        }
                    }
                @endphp

                <div class="bg-white rounded-2xl p-3.5 shadow-sm border border-slate-200/90 hover:border-[#22AF85]/50 transition-all flex flex-col justify-between gap-2.5 relative overflow-hidden">
                    
                    {{-- Row 1: Header (Thumbnail + Info + Shoe Size Badge) --}}
                    @php
                        $orderPhotos = $order->photos ? $order->photos->map(function($p) {
                            return [
                                'url' => $p->photo_url,
                                'caption' => $p->caption ?: ($p->step ? str_replace('_', ' ', $p->step) : 'Foto Dokumentasi'),
                                'step' => $p->step,
                                'date' => $p->created_at ? $p->created_at->format('d M Y H:i') : null,
                            ];
                        })->filter(fn($p) => !empty($p['url']))->values() : collect();
                        $hasPhotos = $orderPhotos->isNotEmpty();
                        $coverPhotoUrl = $order->spk_cover_photo_url ?: ($hasPhotos ? $orderPhotos[0]['url'] : null);
                        $custName = $order->customer_name ?: ($order->customer->name ?? '-');
                    @endphp
                    <div class="flex gap-3 items-center">
                        <div class="relative flex-shrink-0">
                            <div @if($hasPhotos) 
                                    @click="openPhotoModal('{{ $order->spk_number }}', '{{ addslashes($custName) }}', {{ json_encode($orderPhotos) }})"
                                    role="button"
                                    title="Klik untuk lihat {{ $orderPhotos->count() }} foto dokumentasi"
                                    class="w-13 h-13 rounded-2xl overflow-hidden bg-slate-900 flex-shrink-0 border-2 border-slate-200/90 relative shadow-sm cursor-pointer group active:scale-95 transition-all hover:border-[#22AF85]"
                                 @else
                                    class="w-13 h-13 rounded-2xl overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-200/90 relative shadow-inner flex flex-col items-center justify-center text-slate-400"
                                 @endif>
                                @if($coverPhotoUrl)
                                    <img src="{{ $coverPhotoUrl }}" 
                                         alt="{{ $order->spk_number }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         loading="lazy">
                                    @if($orderPhotos->count() > 1)
                                        <div class="absolute bottom-1 right-1 bg-black/75 backdrop-blur-xs text-white text-[8px] font-black px-1.5 py-0.5 rounded-md flex items-center gap-0.5 shadow-sm">
                                            <span>📷</span>
                                            <span>{{ $orderPhotos->count() }}</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-slate-50 text-slate-400">
                                        <svg class="w-5 h-5 text-slate-300 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-[7px] font-extrabold uppercase tracking-tight text-slate-400 leading-none">No Foto</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex-1 min-w-0 pr-11">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('mobile.spk.track', $order->spk_number) }}" class="text-xs font-black font-mono text-[#22AF85] hover:underline truncate block">
                                    {{ $order->spk_number }}
                                </a>
                                @if($order->is_warranty)
                                    <span class="text-[8px] font-black px-1.5 py-0.2 rounded bg-amber-100 text-amber-900 border border-amber-300">
                                        🛡️ Garansi
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs font-extrabold text-slate-900 truncate mt-0.5">{{ $custName }}</p>
                            <p class="text-[10px] font-semibold text-slate-500 truncate">{{ $order->shoe_brand }} {{ $order->shoe_type }}</p>
                        </div>

                        {{-- Shoe Size Badge --}}
                        <div class="absolute top-3 right-3 bg-gradient-to-br from-[#22AF85] to-teal-700 text-white w-9 h-9 rounded-xl flex flex-col items-center justify-center shadow-xs">
                            <span class="text-[6px] font-bold uppercase text-emerald-100 leading-none">Size</span>
                            <span class="text-[11px] font-black leading-none mt-0.5">{{ $order->shoe_size ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Row 2: 3-Station Progress Strip (Khusus Stasiun Produksi Reparasi: Soling, Upper, QC Jahit) --}}
                    <div class="grid grid-cols-3 gap-1.5 pt-1 border-t border-slate-100">
                        @foreach(\App\Livewire\Mobile\ProductionControlling::STATIONS as $sKey)
                            @php
                                $isUnneeded = $checkStationUnneeded($sKey);
                                $isRun = $order->{"{$sKey}_started_at"} && !$order->{"{$sKey}_completed_at"} && !$isUnneeded;
                                $isDone = (bool) $order->{"{$sKey}_completed_at"} && !$isUnneeded;
                                $isWait = !$order->{"{$sKey}_started_at"} && !$order->{"{$sKey}_completed_at"} && !$isUnneeded;

                                $stPauseInfo = $isRun ? $this->getStationPauseInfo($order, $sKey) : null;
                                $isStPaused = $isRun && ($stPauseInfo['is_paused'] ?? false);

                                if ($isRun) {
                                    if ($isStPaused) {
                                        $pillBg = 'bg-amber-50 text-amber-900 border-amber-300 ring-1 ring-amber-400';
                                        $icon = '⏸';
                                    } else {
                                        $pillBg = 'bg-blue-50 text-blue-700 border-blue-300 ring-1 ring-blue-400';
                                        $icon = '⚡';
                                    }
                                } elseif ($isDone) {
                                    $pillBg = 'bg-emerald-50 text-[#22AF85] border-emerald-200';
                                    $icon = '✓';
                                } elseif ($isUnneeded) {
                                    $pillBg = 'bg-slate-100 text-slate-400 border-slate-200 line-through opacity-70';
                                    $icon = '—';
                                } else {
                                    $pillBg = 'bg-slate-50 text-slate-600 border-slate-200';
                                    $icon = '⏳';
                                }
                            @endphp
                            <button wire:click="openStartSheet({{ $order->id }}, '{{ $sKey }}')" 
                                    type="button"
                                    class="py-1 px-1 rounded-lg border text-[10px] font-extrabold flex items-center justify-center gap-1 transition-all active:scale-95 {{ $pillBg }}"
                                    title="{{ $stMap[$sKey] }}: {{ $isRun ? ($isStPaused ? 'Sedang dijeda (Klik untuk detail)' : 'Sedang berjalan') : ($isDone ? 'Selesai' : ($isUnneeded ? 'Tidak diperlukan' : 'Belum mulai (Klik untuk atur teknisi)')) }}">
                                <span>{{ $icon }}</span>
                                <span class="truncate">{{ $stMap[$sKey] }}</span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Row 3: Primary Action Button --}}
                    <div class="pt-1.5 flex items-center justify-between gap-2 border-t border-slate-100">
                        @if($activeTab === 'completed_today')
                            {{-- State: Selesai Hari Ini --}}
                            <div class="flex-1 flex items-center gap-1.5 text-[11px] font-black text-emerald-700 bg-emerald-50 px-2.5 py-1.5 rounded-xl border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-[#22AF85]"></span>
                                <span>Pengerjaan Selesai Hari Ini</span>
                            </div>
                        @elseif($runningSt)
                            @if(!$runningSt['is_paused'])
                                {{-- State 1: Sedang Berjalan Aktif + Live Timer Bersih + [⏸ Jeda] + [SELESAI] --}}
                                <div class="flex-1 flex items-center justify-between bg-blue-50/90 border border-blue-200 px-2.5 py-1.5 rounded-xl gap-2"
                                     x-data="{
                                         baseSeconds: {{ $runningSt['net_seconds'] }},
                                         startedClientTime: Math.floor(Date.now() / 1000),
                                         currentDuration: '00:00',
                                         updateTimer() {
                                             let elapsed = Math.floor(Date.now() / 1000) - this.startedClientTime;
                                             let total = Math.max(0, this.baseSeconds + elapsed);
                                             let hours = Math.floor(total / 3600);
                                             let minutes = Math.floor((total % 3600) / 60);
                                             let seconds = total % 60;
                                             this.currentDuration = (hours > 0 ? (hours < 10 ? '0' + hours : hours) + ':' : '') +
                                                                    (minutes < 10 ? '0' + minutes : minutes) + ':' +
                                                                    (seconds < 10 ? '0' + seconds : seconds);
                                         }
                                     }"
                                     x-init="updateTimer(); setInterval(() => updateTimer(), 1000)">
                                    <div class="flex items-center gap-1.5 min-w-0 pr-1">
                                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-ping flex-shrink-0"></span>
                                        <span class="text-[10px] font-black text-blue-900 truncate">{{ $runningSt['name'] }}</span>
                                        <span class="text-[10px] font-mono font-black text-blue-700" x-text="currentDuration">00:00</span>
                                    </div>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <button wire:click="openPauseSheet({{ $order->id }}, '{{ $runningSt['key'] }}')" 
                                                type="button" 
                                                class="px-2 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 text-[10px] font-black uppercase tracking-wider shadow-xs active:scale-95 transition-all flex items-center gap-1"
                                                title="Jeda Pengerjaan">
                                            <span>⏸</span>
                                            <span>Jeda</span>
                                        </button>
                                        <button wire:click="promptFinishInline({{ $order->id }}, '{{ $runningSt['key'] }}')" 
                                                type="button" 
                                                class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-black uppercase tracking-wider shadow-xs active:scale-95 transition-all">
                                            Selesai
                                        </button>
                                    </div>
                                </div>
                            @else
                                {{-- State 2: Status Dijeda + Timer Dibekukan (Freeze) + [▶ Lanjutkan] + [SELESAI] --}}
                                @php
                                    $frozenSecs = $runningSt['net_seconds'];
                                    $fH = floor($frozenSecs / 3600);
                                    $fM = floor(($frozenSecs % 3600) / 60);
                                    $fS = $frozenSecs % 60;
                                    $frozenTimeText = ($fH > 0 ? sprintf('%02d:', $fH) : '') . sprintf('%02d:%02d', $fM, $fS);
                                @endphp
                                <div class="flex-1 flex items-center justify-between bg-amber-50/95 border-2 border-amber-300 px-2.5 py-1.5 rounded-xl gap-2">
                                    <div class="flex items-center gap-1.5 min-w-0 pr-1">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 flex-shrink-0"></span>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1">
                                                <span class="text-[10px] font-black text-amber-950 truncate">{{ $runningSt['name'] }}</span>
                                                <span class="text-[8px] font-black px-1 rounded bg-amber-200 text-amber-900 uppercase">Dijeda</span>
                                            </div>
                                            <p class="text-[8px] font-bold text-amber-800 truncate">{{ $runningSt['reason'] ?: 'Istirahat' }}</p>
                                        </div>
                                        <span class="text-[10px] font-mono font-black text-amber-900">{{ $frozenTimeText }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <button wire:click="resumeStationInline({{ $order->id }}, '{{ $runningSt['key'] }}')" 
                                                type="button" 
                                                class="px-2.5 py-1 rounded-lg bg-gradient-to-r from-[#22AF85] to-teal-600 hover:from-[#1b8e6c] hover:to-teal-700 text-white text-[10px] font-black uppercase tracking-wider shadow-xs active:scale-95 transition-all flex items-center gap-1 animate-pulse">
                                            <span>▶</span>
                                            <span>Lanjutkan</span>
                                        </button>
                                        <button wire:click="promptFinishInline({{ $order->id }}, '{{ $runningSt['key'] }}')" 
                                                type="button" 
                                                class="px-2 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-black uppercase tracking-wider shadow-xs active:scale-95 transition-all"
                                                title="Langsung selesaikan stasiun">
                                            Selesai
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @elseif($nextPendingSt)
                            {{-- Button to Start Next Station via Bottom Sheet --}}
                            <button wire:click="openStartSheet({{ $order->id }}, '{{ $nextPendingSt['key'] }}')" 
                                    type="button" 
                                    class="flex-1 h-8 rounded-xl bg-gradient-to-r from-[#22AF85] to-teal-600 hover:from-[#1b8e6c] hover:to-teal-700 text-white text-[10px] font-black uppercase tracking-wider shadow-xs flex items-center justify-center gap-1.5 active:scale-95 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Mulai {{ $nextPendingSt['name'] }}</span>
                            </button>
                        @else
                            {{-- All repair stations complete --}}
                            <div class="flex-1 text-[11px] font-black text-[#22AF85] flex items-center gap-1">
                                <span>✓ Pengerjaan reparasi selesai</span>
                            </div>
                        @endif

                        {{-- Link to Detail SPK --}}
                        <a href="{{ route('mobile.spk.track', $order->spk_number) }}" 
                           class="p-2 rounded-xl text-slate-400 hover:text-[#22AF85] hover:bg-slate-50 transition-colors flex-shrink-0"
                           title="Buka Lembar Kerja SPK">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 bg-white rounded-2xl p-8 text-center border border-slate-200 text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-xs font-bold text-slate-500">Tidak ada SPK pada kategori ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="pt-2">
            {{ $orders->links() }}
        </div>

    </main>

    {{-- Floating Camera Action Button (FAB 📷) --}}
    <button @click="startScanner()" 
            type="button"
            class="fixed bottom-6 right-5 z-40 w-14 h-14 rounded-full bg-gradient-to-tr from-[#22AF85] to-[#1cb082] text-white shadow-xl shadow-[#22AF85]/40 flex items-center justify-center active:scale-90 transition-all border-2 border-white focus:outline-none"
            title="Scan QR Code SPK Fisik">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    </button>

    {{-- Bottom Sheet Modal: Mulai Stasiun (Pilih Teknisi / Tidak Diperlukan) --}}
    @if($sheetOrder && $sheetStation)
        @php
            $stationNames = [
                'prod_sol' => 'Soling',
                'prod_upper' => 'Upper',
                'qc_jahit' => 'QC Jahit',
            ];
            $currentTechs = $techOptions[$sheetStation] ?? ($techOptions['all'] ?? collect());
            $isCurUnneeded = $sheetOrder->isStationUnneeded($sheetStation);
            $isCurRunning = $sheetOrder->{"{$sheetStation}_started_at"} && !$sheetOrder->{"{$sheetStation}_completed_at"} && !$isCurUnneeded;
            $isCurDone = (bool) $sheetOrder->{"{$sheetStation}_completed_at"} && !$isCurUnneeded;
        @endphp
        @php
            $selectedTech = $currentTechs->firstWhere('id', (int)$sheetTechId);
        @endphp
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-950/70 backdrop-blur-xs p-0 sm:p-4 animate-in fade-in duration-150">
            <div class="bg-white rounded-t-3xl sm:rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 text-left flex flex-col max-h-[85vh] overflow-hidden">
                
                {{-- Sheet Header (Pinned) --}}
                <div class="p-4 pb-3 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-wider text-[#22AF85] bg-[#22AF85]/10 px-2 py-0.5 rounded-md border border-[#22AF85]/20">
                            Atur Stasiun Produksi Reparasi
                        </span>
                        <h3 class="text-sm font-black font-mono text-slate-900 mt-1 select-all">
                            {{ $sheetOrder->spk_number }}
                        </h3>
                        <p class="text-[11px] font-bold text-slate-500 truncate">
                            {{ $sheetOrder->customer->name ?? '-' }} • {{ $sheetOrder->shoe_brand }} {{ $sheetOrder->shoe_model }}
                        </p>
                    </div>
                    <button wire:click="closeSheet" type="button" class="p-2 rounded-xl bg-slate-100 text-slate-400 hover:text-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Scrollable Sheet Body --}}
                <div class="p-4 flex-1 overflow-y-auto space-y-3.5 pr-3">
                    
                    {{-- Station Pills Selector Inside Modal (Soling, Upper, QC Jahit) --}}
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Pilih Stasiun:</label>
                        <div class="grid grid-cols-3 gap-1.5">
                            @foreach(\App\Livewire\Mobile\ProductionControlling::STATIONS as $stKey)
                                @php
                                    $isOrdered = match($stKey) {
                                        'prod_sol' => (bool)$sheetOrder->needs_prod_sol,
                                        'prod_upper' => (bool)$sheetOrder->needs_prod_upper,
                                        'qc_jahit' => (bool)$sheetOrder->needs_prod_jahit,
                                        default => true,
                                    };
                                    $sUnneeded = (!$isOrdered || $sheetOrder->isStationUnneeded($stKey)) && !$sheetOrder->{"{$stKey}_by"} && !$sheetOrder->{"{$stKey}_started_at"};
                                    $sRunning = $sheetOrder->{"{$stKey}_started_at"} && !$sheetOrder->{"{$stKey}_completed_at"} && !$sUnneeded;
                                    $sDone = (bool) $sheetOrder->{"{$stKey}_completed_at"} && !$sUnneeded;
                                    $isSelected = $sheetStation === $stKey;
                                @endphp
                                <button wire:click="setSheetStation('{{ $stKey }}')" 
                                        type="button" 
                                        class="py-2 px-1 rounded-xl text-center border text-[10px] font-black transition-all flex flex-col items-center justify-center
                                        {{ $isSelected ? 'bg-[#22AF85] text-white border-[#22AF85] shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                    <span>{{ $stationNames[$stKey] }}</span>
                                    <span class="text-[8px] font-extrabold mt-0.5 opacity-90">
                                        {{ $sRunning ? '⚡ Jalan' : ($sDone ? '✓ Beres' : ($sUnneeded ? '— Lewat' : '⏳ Antre')) }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Current Status Alert --}}
                    @if($isCurRunning)
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-900 flex items-center justify-between">
                            <span>⚡ Stasiun ini <strong>sedang berjalan</strong>.</span>
                            <button wire:click="promptFinishInline({{ $sheetOrder->id }}, '{{ $sheetStation }}')" 
                                    type="button" 
                                    class="px-2.5 py-1 rounded-lg bg-rose-600 text-white text-[10px] font-black uppercase shadow-xs">
                                Selesaikan
                            </button>
                        </div>
                    @elseif($isCurDone)
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-900 flex items-center justify-between">
                            <span>✓ Stasiun ini <strong>sudah selesai</strong>.</span>
                            <button wire:click="openStartSheet({{ $sheetOrder->id }}, '{{ $sheetStation }}')" 
                                    type="button" 
                                    class="text-[10px] font-bold text-slate-600 underline">
                                Mulai Ulang
                            </button>
                        </div>
                    @endif

                    {{-- Option 1: Tandai Tidak Diperlukan (Bypass) Card --}}
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1.5">
                            Status atau Aksi Khusus:
                        </label>
                        <button type="button" 
                                wire:click="selectSheetTech('unneeded')"
                                class="w-full text-left p-3 rounded-2xl border transition-all flex items-center justify-between active:scale-98
                                {{ $sheetTechId === 'unneeded' ? 'bg-amber-50 border-amber-400 ring-2 ring-amber-400/40 shadow-xs' : 'bg-slate-50 border-slate-200 hover:bg-slate-100/70' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-black shrink-0">⊘</span>
                                <div>
                                    <p class="text-xs font-black text-slate-900">Tandai Tidak Diperlukan</p>
                                    <p class="text-[10px] text-slate-500 font-medium">Bypass / lewati pengerjaan stasiun ini</p>
                                </div>
                            </div>
                            <div class="w-5 h-5 rounded-full border flex items-center justify-center shrink-0 {{ $sheetTechId === 'unneeded' ? 'border-amber-600 bg-amber-600 text-white' : 'border-slate-300 bg-white' }}">
                                @if($sheetTechId === 'unneeded')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </div>
                        </button>
                    </div>

                    {{-- Option 2: Technician Cards List (Interactive Mobile Selector) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500">
                                Pilih Teknisi Tersedia ({{ $stationNames[$sheetStation] }}):
                            </label>
                            <span class="text-[10px] font-bold text-[#22AF85]">
                                {{ $currentTechs->count() }} Teknisi
                            </span>
                        </div>
                        
                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                            @forelse($currentTechs as $tech)
                                @php
                                    $isTechSelected = (string)$sheetTechId === (string)$tech->id;
                                @endphp
                                <button type="button" 
                                        wire:click="selectSheetTech('{{ $tech->id }}')"
                                        class="w-full text-left p-2.5 rounded-2xl border transition-all flex items-center justify-between active:scale-98
                                        {{ $isTechSelected ? 'bg-emerald-50/80 border-[#22AF85] ring-2 ring-[#22AF85]/30 shadow-xs' : 'bg-white border-slate-200 hover:border-slate-300' }}">
                                    <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black shrink-0 transition-colors
                                            {{ $isTechSelected ? 'bg-[#22AF85] text-white shadow-xs' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            {{ strtoupper(substr($tech->name, 0, 1)) }}
                                        </div>
                                        <div class="truncate">
                                            <p class="text-xs font-black text-slate-900 truncate">{{ $tech->name }}</p>
                                            <p class="text-[10px] font-semibold text-slate-500 truncate">
                                                {{ $tech->specialization ?: ($tech->station ?: 'Teknisi Produksi') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="w-5 h-5 rounded-full border flex items-center justify-center shrink-0 {{ $isTechSelected ? 'border-[#22AF85] bg-[#22AF85] text-white' : 'border-slate-300 bg-white' }}">
                                        @if($isTechSelected)
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </div>
                                </button>
                            @empty
                                <div class="p-4 text-center text-xs text-slate-400 italic bg-slate-50 rounded-2xl border border-slate-200">
                                    Tidak ada teknisi aktif untuk stasiun ini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

                {{-- Sheet Footer (Pinned at Bottom — Always Visible) --}}
                <div class="p-4 pt-3 border-t border-slate-100 bg-white shrink-0 grid grid-cols-2 gap-2.5 shadow-lg">
                    <button wire:click="closeSheet" 
                            type="button" 
                            class="h-11 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 transition-colors active:scale-95">
                        Batal
                    </button>
                    <button wire:click="confirmStartSheet" 
                            type="button" 
                            class="h-11 rounded-xl text-white font-black text-xs shadow-md transition-all active:scale-95 flex items-center justify-center gap-1.5
                            {{ $sheetTechId === 'unneeded' ? 'bg-amber-600 hover:bg-amber-700 shadow-amber-600/30' : ($sheetTechId !== '' ? 'bg-gradient-to-r from-[#22AF85] to-teal-600 hover:from-[#1b8e6c] hover:to-teal-700 shadow-[#22AF85]/30' : 'bg-slate-300 text-slate-500 cursor-not-allowed') }}">
                        @if($sheetTechId === 'unneeded')
                            <span>⚪ Simpan Bypass</span>
                        @elseif($selectedTech)
                            <span>▶ Mulai ({{ Str::limit($selectedTech->name, 10) }})</span>
                        @else
                            <span>Pilih Teknisi</span>
                        @endif
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- Bottom Sheet: Jeda Pengerjaan (Pilih Alasan Cepat) --}}
    @if($pauseOrderId && $pauseStation)
        @php
            $pauseOrder = \App\Models\WorkOrder::find($pauseOrderId);
            $stationTitle = $stMap[$pauseStation] ?? $pauseStation;
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
                                <span class="text-xs font-black font-mono text-[#22AF85]">{{ $pauseOrder->spk_number ?? '-' }}</span>
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300">
                                    ⏸ Jeda Stasiun {{ $stationTitle }}
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
                        @foreach(\App\Livewire\Mobile\ProductionControlling::PAUSE_REASONS as $rOption)
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

    {{-- In-App HTML5 QR Scanner Modal --}}
    <div x-show="scannerOpen" 
         x-cloak
         class="fixed inset-0 z-50 flex flex-col bg-slate-950/95 text-white animate-in fade-in duration-200">
        <div class="p-4 flex items-center justify-between border-b border-slate-800">
            <h3 class="text-sm font-black text-white flex items-center gap-2">
                <span>📷</span>
                <span>Pindai QR Code SPK Fisik</span>
            </h3>
            <button @click="stopScanner()" type="button" class="p-2 text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 flex flex-col items-center justify-center p-4">
            <div id="qr-reader-container" class="w-full max-w-sm rounded-2xl overflow-hidden border-2 border-[#22AF85] shadow-2xl"></div>
            <p class="text-xs text-slate-400 font-bold mt-4 text-center">
                Arahkan kamera HP ke QR Code pada lembar cetak SPK fisik.
            </p>
        </div>
    </div>

    {{-- Interactive Fullscreen Photo Viewer Lightbox Modal (Alpine.js) --}}
    <div x-show="photoModalOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="closePhotoModal()"
         @keydown.arrow-right.window="nextPhoto()"
         @keydown.arrow-left.window="prevPhoto()"
         class="fixed inset-0 z-50 flex flex-col bg-slate-950/95 backdrop-blur-md text-white select-none">
        
        {{-- Header --}}
        <div class="p-4 flex items-center justify-between border-b border-white/10 bg-slate-900/60">
            <div class="min-w-0 pr-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono font-black text-[#22AF85] tracking-wide" x-text="activePhotoSpk"></span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/10 text-emerald-300"
                          x-show="activePhotos.length > 0"
                          x-text="(activePhotoIndex + 1) + ' / ' + activePhotos.length"></span>
                </div>
                <p class="text-xs font-bold text-slate-300 truncate mt-0.5" x-text="activePhotoCustomer"></p>
            </div>
            <div class="flex items-center gap-2">
                <a :href="activePhotos[activePhotoIndex] ? activePhotos[activePhotoIndex].url : '#'" 
                   target="_blank" 
                   class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition-all active:scale-95" 
                   title="Buka resolusi penuh di tab baru">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <button @click="closePhotoModal()" type="button" class="p-2 rounded-xl bg-white/10 hover:bg-rose-500/80 text-white transition-all active:scale-95" title="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Main Image Stage with Touch Swipe --}}
        <div class="flex-1 relative flex items-center justify-center p-2 sm:p-4 overflow-hidden touch-pan-y"
             x-on:touchstart="touchStartX = $event.changedTouches[0].screenX"
             x-on:touchend="if ($event.changedTouches[0].screenX < touchStartX - 40) nextPhoto(); if ($event.changedTouches[0].screenX > touchStartX + 40) prevPhoto();">
            
            <template x-if="activePhotos.length > 0 && activePhotos[activePhotoIndex]">
                <img :src="activePhotos[activePhotoIndex].url" 
                     :alt="activePhotoSpk" 
                     class="max-w-full max-h-[72vh] object-contain rounded-xl shadow-2xl transition-all duration-200">
            </template>

            {{-- Nav Prev Button --}}
            <button x-show="activePhotos.length > 1" 
                    @click="prevPhoto()" 
                    type="button" 
                    class="absolute left-3 top-1/2 -translate-y-1/2 p-2.5 rounded-full bg-slate-900/70 hover:bg-slate-900 border border-white/20 text-white transition-all active:scale-90 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>

            {{-- Nav Next Button --}}
            <button x-show="activePhotos.length > 1" 
                    @click="nextPhoto()" 
                    type="button" 
                    class="absolute right-3 top-1/2 -translate-y-1/2 p-2.5 rounded-full bg-slate-900/70 hover:bg-slate-900 border border-white/20 text-white transition-all active:scale-90 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        {{-- Caption & Thumbnail Strip Footer --}}
        <div class="p-3 border-t border-white/10 bg-slate-900/80 backdrop-blur-md">
            <div class="max-w-md mx-auto text-center space-y-2">
                <template x-if="activePhotos.length > 0 && activePhotos[activePhotoIndex]">
                    <div>
                        <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#22AF85]/30 border border-[#22AF85]/50 text-emerald-300 font-extrabold text-[10px] uppercase tracking-wider"
                              x-text="activePhotos[activePhotoIndex].caption"></span>
                        <span class="block text-[10px] text-slate-400 mt-0.5 font-medium"
                              x-show="activePhotos[activePhotoIndex].date"
                              x-text="activePhotos[activePhotoIndex].date"></span>
                    </div>
                </template>

                {{-- Thumbnail Dots / Mini Previews --}}
                <div x-show="activePhotos.length > 1" class="flex items-center justify-center gap-1.5 overflow-x-auto py-1">
                    <template x-for="(ph, idx) in activePhotos" :key="idx">
                        <button @click="activePhotoIndex = idx" 
                                type="button" 
                                class="w-9 h-9 rounded-lg overflow-hidden border-2 transition-all flex-shrink-0"
                                :class="activePhotoIndex === idx ? 'border-[#22AF85] ring-2 ring-[#22AF85]/50 scale-105' : 'border-white/20 opacity-50 hover:opacity-80'">
                            <img :src="ph.url" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Selesai --}}
    @if($confirmOrderId && $confirmStation)
        @php
            $stationNames = [
                'prod_sol' => 'Soling',
                'prod_upper' => 'Upper',
                'qc_jahit' => 'QC Jahit',
            ];
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 animate-in fade-in duration-150">
            <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl border border-slate-100 text-center space-y-4">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Selesaikan Pengerjaan?</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Stasiun <strong class="text-slate-800">{{ $stationNames[$confirmStation] ?? $confirmStation }}</strong> akan ditandai selesai secara real-time.
                    </p>
                    <div class="mt-3 py-2 px-3 bg-slate-100 rounded-xl inline-block text-xs font-mono font-bold text-slate-700">
                        Durasi Kerja: <span class="text-[#22AF85] font-black">{{ $confirmDuration }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2.5 pt-2">
                    <button wire:click="cancelConfirm" type="button" class="h-11 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 transition-colors">
                        Batal
                    </button>
                    <button wire:click="executeFinishInline" type="button" class="h-11 rounded-xl bg-rose-600 text-white font-black text-xs hover:bg-rose-700 shadow-md shadow-rose-600/30 transition-colors">
                        Ya, Selesaikan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
