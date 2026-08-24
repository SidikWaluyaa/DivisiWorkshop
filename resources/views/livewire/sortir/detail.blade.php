<div class="min-h-screen bg-[#f8fafc] dark:bg-slate-900 py-8">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <nav class="flex text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">
                    <span class="hover:text-emerald-500 transition-colors cursor-default">INVENTARIS</span>
                    <span class="mx-2">/</span>
                    <span class="text-emerald-600 dark:text-emerald-400">KLASIFIKASI & VALIDASI SORTIR</span>
                </nav>
                <div class="flex items-center gap-4">
                    <a href="{{ route('sortir.index') }}" wire:navigate class="w-10 h-10 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:border-emerald-600 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Stage Sortir: SPK {{ $order->spk_number }}</h1>
                        <p class="text-xs text-slate-500 font-medium">Klasifikasi Material, Alokasi Stok & Penugasan Teknisi</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-xl text-xs font-bold border border-indigo-200 dark:border-indigo-800">
                    Kategori: {{ $order->category_spk ?? 'REGULAR' }}
                </span>
            </div>
        </div>

        @if (session()->has('success') || session()->has('error') || session()->has('info'))
            <div class="mb-8">
                @if (session()->has('success'))
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-300 animate-fade-in shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-sm font-bold">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session()->has('info'))
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-2xl flex items-center gap-3 text-amber-700 dark:text-amber-300 animate-fade-in shadow-sm">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-sm font-bold">{{ session('info') }}</span>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl flex items-center gap-3 text-rose-700 dark:text-rose-300 animate-fade-in shadow-sm">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span class="text-sm font-bold">{{ session('error') }}</span>
                    </div>
                @endif
            </div>
        @endif

        @php
            // Hitung status material untuk GATE 3: jika ada yang ALLOCATED/RECEIVED = Material Siap
            $isMaterialSiap = $order->materials->contains(
                fn($m) => in_array($m->pivot->status ?? '', ['ALLOCATED', 'RECEIVED'])
            );
        @endphp

        {{-- PROMINENT KLASIFIKASI SORTIR CARD (GATE 3 - FR-3.1) --}}
        <div class="mb-8 bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-amber-500/20">
                        ⚡
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">KLASIFIKASI MATERIAL SORTIR (GATE 3)</h3>
                        <p class="text-xs text-slate-500 font-medium">Tentukan keputusan Bongkar & Belanja bahan baku sebelum SPK dapat meninggalkan Sortir</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg">Wajib diisi oleh Admin</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Perlu Bongkar --}}
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600">
                    <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">1. Apakah Sepatu Perlu Bongkar Komponen?</label>
                    <p class="text-xs text-slate-500 mb-4">Pengecekan/pembongkaran fisik sol atau upper yang terpisah.</p>
                    <div class="flex gap-4">
                        {{-- Opsi YA --}}
                        <label class="flex-1 flex items-center justify-center gap-2 p-3.5 rounded-xl border-2 cursor-pointer transition-all text-xs {{ $perlu_bongkar ? 'border-orange-500 bg-orange-50 dark:bg-orange-950/40 text-orange-800 dark:text-orange-300 ring-2 ring-orange-400/30 font-black shadow-sm' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-400 font-medium hover:border-slate-300' }}">
                            <input type="radio" wire:model.live="perlu_bongkar" value="1" class="hidden">
                            <span>🔨 YA (Perlu Bongkar)</span>
                            @if($perlu_bongkar)
                                <span class="ml-1 text-orange-600 font-black">✓</span>
                            @endif
                        </label>
                        {{-- Opsi TIDAK --}}
                        <label class="flex-1 flex items-center justify-center gap-2 p-3.5 rounded-xl border-2 cursor-pointer transition-all text-xs {{ !$perlu_bongkar ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 ring-2 ring-emerald-400/30 font-black shadow-sm' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-400 font-medium hover:border-slate-300' }}">
                            <input type="radio" wire:model.live="perlu_bongkar" value="0" class="hidden">
                            <span>✅ TIDAK (Langsung)</span>
                            @if(!$perlu_bongkar)
                                <span class="ml-1 text-emerald-600 font-black">✓</span>
                            @endif
                        </label>
                    </div>
                </div>

                {{-- Perlu Belanja --}}
                @if($isMaterialSiap)
                    {{-- Material sudah siap: tampilkan info, sembunyikan radio button --}}
                    <div class="p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border-2 border-emerald-400 dark:border-emerald-700 flex flex-col gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-700 dark:text-emerald-300 font-black text-sm">✅ 2. Status Belanja Bahan Baku</span>
                        </div>
                        <div class="flex items-center gap-3 px-4 py-3 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl border border-emerald-300 dark:border-emerald-700">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">Material Siap — Belanja Tidak Diperlukan</p>
                                <p class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-0.5">Stok material sudah dialokasikan ke SPK ini. Klasifikasi belanja otomatis tidak aktif.</p>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Material belum siap: tampilkan radio button seperti biasa --}}
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600">
                        <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">2. Apakah Perlu Belanja Bahan Baku (Finlog)?</label>
                        <p class="text-xs text-slate-500 mb-4">Pengadaan bahan baku khusus dari supplier eksternal via Finlog.</p>
                        <div class="flex gap-4">
                            {{-- Opsi YA --}}
                            <label class="flex-1 flex items-center justify-center gap-2 p-3.5 rounded-xl border-2 cursor-pointer transition-all text-xs {{ $perlu_belanja ? 'border-purple-500 bg-purple-50 dark:bg-purple-950/40 text-purple-800 dark:text-purple-300 ring-2 ring-purple-400/30 font-black shadow-sm' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-400 font-medium hover:border-slate-300' }}">
                                <input type="radio" wire:model.live="perlu_belanja" value="1" class="hidden">
                                <span>🛒 YA (Perlu Belanja)</span>
                                @if($perlu_belanja)
                                    <span class="ml-1 text-purple-600 font-black">✓</span>
                                @endif
                            </label>
                            {{-- Opsi TIDAK --}}
                            <label class="flex-1 flex items-center justify-center gap-2 p-3.5 rounded-xl border-2 cursor-pointer transition-all text-xs {{ !$perlu_belanja ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 ring-2 ring-emerald-400/30 font-black shadow-sm' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-400 font-medium hover:border-slate-300' }}">
                                <input type="radio" wire:model.live="perlu_belanja" value="0" class="hidden">
                                <span>✅ TIDAK (Stok Tersedia)</span>
                                @if(!$perlu_belanja)
                                    <span class="ml-1 text-emerald-600 font-black">✓</span>
                                @endif
                            </label>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Combination Routing Preview Badge --}}
            <div class="mt-4 p-3 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl border border-indigo-200 dark:border-indigo-800 flex items-center justify-between text-xs">
                <span class="font-bold text-indigo-900 dark:text-indigo-200">Hasil Rute Pengerjaan (FR-3.3):</span>
                @if($isMaterialSiap && $perlu_bongkar)
                    <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg font-bold">Bongkar Fisik ➔ Material Siap ➔ OTW Produksi</span>
                @elseif($isMaterialSiap && !$perlu_bongkar)
                    <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg font-bold">✅ Material Siap ➔ Direct OTW Produksi</span>
                @elseif($perlu_bongkar && $perlu_belanja)
                    <span class="px-3 py-1 bg-amber-500 text-white rounded-lg font-bold">Bongkar Fisik ➔ Rak Tunggu Belanja Finlog</span>
                @elseif($perlu_bongkar && !$perlu_belanja)
                    <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg font-bold">Bongkar Fisik ➔ Direct OTW Produksi</span>
                @elseif(!$perlu_bongkar && $perlu_belanja)
                    <span class="px-3 py-1 bg-purple-600 text-white rounded-lg font-bold">Rak Tunggu Belanja Finlog ➔ OTW Produksi</span>
                @else
                    <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg font-bold">Direct OTW Produksi (Langsung)</span>
                @endif
            </div>
        </div>

        {{-- PANEL KONTROL BONGKAR KOMPONEN (DISASSEMBLY) — Shown when perlu_bongkar = true --}}
        @if($perlu_bongkar)
        <div class="mb-8 bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border-2 border-orange-300 dark:border-orange-700">
            <div class="flex items-center justify-between border-b border-orange-200 dark:border-orange-800 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-orange-500/20">
                        🔨
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">PROSES BONGKAR KOMPONEN (DISASSEMBLY)</h3>
                        <p class="text-xs text-slate-500 font-medium">Selesaikan seluruh tugas Bongkar sebelum SPK dapat dikirim ke Produksi</p>
                    </div>
                </div>
                @php
                    $allBongkarDone = true;
                    if ($this->needsBongkarSol && !$order->prep_sol_completed_at) $allBongkarDone = false;
                    if ($this->needsBongkarUpper && !$order->prep_upper_completed_at) $allBongkarDone = false;
                @endphp
                <span class="text-xs font-bold px-3 py-1 rounded-lg {{ $allBongkarDone ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                    {{ $allBongkarDone ? '✅ Semua Bongkar Selesai' : '⏳ Proses Berjalan...' }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Bongkar Sol Card --}}
                @if($this->needsBongkarSol)
                <div class="p-5 rounded-2xl border-2 transition-all {{ $order->prep_sol_completed_at ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-300 dark:border-emerald-700' : ($order->prep_sol_started_at ? 'bg-amber-50 dark:bg-amber-950/30 border-amber-300 dark:border-amber-700' : 'bg-slate-50 dark:bg-slate-700/50 border-slate-200 dark:border-slate-600') }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">👟</span>
                            <span class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wide">Bongkar Sol</span>
                        </div>
                        @if($order->prep_sol_completed_at)
                            <span class="px-2.5 py-1 bg-emerald-500 text-white text-[10px] font-bold rounded-lg">✅ SELESAI</span>
                        @elseif($order->prep_sol_started_at)
                            <span class="px-2.5 py-1 bg-amber-500 text-white text-[10px] font-bold rounded-lg animate-pulse">🔨 SEDANG DIKERJAKAN</span>
                        @else
                            <span class="px-2.5 py-1 bg-slate-400 text-white text-[10px] font-bold rounded-lg">BELUM DIMULAI</span>
                        @endif
                    </div>

                    {{-- Tech Assignment --}}
                    <div class="mb-4">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Teknisi Bongkar Sol</label>
                        <select wire:model="bongkar_sol_tech_id" class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-xs" {{ $order->prep_sol_started_at ? 'disabled' : '' }}>
                            <option value="">-- Pilih Teknisi --</option>
                            @foreach($bongkarSolTechs as $tech)
                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Timestamps --}}
                    @if($order->prep_sol_started_at)
                    <div class="text-[10px] text-slate-500 space-y-1 mb-3">
                        <p>Mulai: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $order->prep_sol_started_at->format('d M Y H:i') }}</span></p>
                        @if($order->prep_sol_completed_at)
                        <p>Selesai: <span class="font-bold text-emerald-600">{{ $order->prep_sol_completed_at->format('d M Y H:i') }}</span></p>
                        @endif
                    </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="flex gap-2">
                        @if(!$order->prep_sol_started_at)
                            <button wire:click="startBongkar('sol')" class="flex-1 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold uppercase tracking-wide transition-all shadow-md">
                                ▶ Mulai Bongkar Sol
                            </button>
                        @elseif(!$order->prep_sol_completed_at)
                            <button wire:click="finishBongkar('sol')" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold uppercase tracking-wide transition-all shadow-md">
                                ✓ Selesaikan Bongkar Sol
                            </button>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Bongkar Upper Card --}}
                @if($this->needsBongkarUpper)
                <div class="p-5 rounded-2xl border-2 transition-all {{ $order->prep_upper_completed_at ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-300 dark:border-emerald-700' : ($order->prep_upper_started_at ? 'bg-amber-50 dark:bg-amber-950/30 border-amber-300 dark:border-amber-700' : 'bg-slate-50 dark:bg-slate-700/50 border-slate-200 dark:border-slate-600') }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🧤</span>
                            <span class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wide">Bongkar Upper</span>
                        </div>
                        @if($order->prep_upper_completed_at)
                            <span class="px-2.5 py-1 bg-emerald-500 text-white text-[10px] font-bold rounded-lg">✅ SELESAI</span>
                        @elseif($order->prep_upper_started_at)
                            <span class="px-2.5 py-1 bg-amber-500 text-white text-[10px] font-bold rounded-lg animate-pulse">🔨 SEDANG DIKERJAKAN</span>
                        @else
                            <span class="px-2.5 py-1 bg-slate-400 text-white text-[10px] font-bold rounded-lg">BELUM DIMULAI</span>
                        @endif
                    </div>

                    {{-- Tech Assignment --}}
                    <div class="mb-4">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Teknisi Bongkar Upper</label>
                        <select wire:model="bongkar_upper_tech_id" class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-xs" {{ $order->prep_upper_started_at ? 'disabled' : '' }}>
                            <option value="">-- Pilih Teknisi --</option>
                            @foreach($bongkarUpperTechs as $tech)
                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Timestamps --}}
                    @if($order->prep_upper_started_at)
                    <div class="text-[10px] text-slate-500 space-y-1 mb-3">
                        <p>Mulai: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $order->prep_upper_started_at->format('d M Y H:i') }}</span></p>
                        @if($order->prep_upper_completed_at)
                        <p>Selesai: <span class="font-bold text-emerald-600">{{ $order->prep_upper_completed_at->format('d M Y H:i') }}</span></p>
                        @endif
                    </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="flex gap-2">
                        @if(!$order->prep_upper_started_at)
                            <button wire:click="startBongkar('upper')" class="flex-1 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold uppercase tracking-wide transition-all shadow-md">
                                ▶ Mulai Bongkar Upper
                            </button>
                        @elseif(!$order->prep_upper_completed_at)
                            <button wire:click="finishBongkar('upper')" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold uppercase tracking-wide transition-all shadow-md">
                                ✓ Selesaikan Bongkar Upper
                            </button>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Info card when neither sol nor upper needed but perlu_bongkar is still true --}}
                @if(!$this->needsBongkarSol && !$this->needsBongkarUpper)
                <div class="md:col-span-2 p-5 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-700 text-center">
                    <p class="text-sm font-bold text-amber-800 dark:text-amber-300">⚠ Opsi Bongkar aktif tetapi SPK ini tidak memiliki layanan Sol atau Upper yang membutuhkan pembongkaran.</p>
                    <p class="text-xs text-amber-600 mt-1">Anda dapat melanjutkan klasifikasi tanpa proses Bongkar.</p>
                </div>
                @endif
            </div>

            {{-- Blocked Warning --}}
            @if(!$allBongkarDone)
            <div class="mt-4 p-3 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-800 flex items-center gap-2 text-xs text-rose-700 dark:text-rose-300">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="font-bold">Tombol "Selesaikan Klasifikasi" akan diblokir sampai seluruh proses Bongkar di atas selesai.</span>
            </div>
            @endif
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Column: SPK Info & Customer --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-slate-50/50 dark:bg-slate-700/50">
                        <h3 class="text-xs font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">DATA SPK & PELANGGAN</h3>
                        <span class="px-3 py-1 bg-slate-900 text-white text-[10px] font-mono font-bold rounded-lg">{{ $order->spk_number }}</span>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">PELANGGAN</p>
                            <h4 class="text-base font-black text-slate-900 dark:text-white">{{ $order->customer?->name ?? $order->customer_name ?? 'Guest' }}</h4>
                            <p class="text-xs font-bold text-emerald-600 font-mono">{{ $order->customer?->phone ?? $order->customer_phone ?? '-' }}</p>
                        </div>

                        <div class="p-4 bg-slate-50 dark:bg-slate-700/40 rounded-2xl border border-slate-100 dark:border-slate-700 space-y-2">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">DETAIL SEPATU</span>
                            <h5 class="text-sm font-black text-slate-900 dark:text-white">{{ $order->brand ?? $order->shoe_brand }} - {{ $order->type ?? $order->shoe_type }}</h5>
                            <p class="text-xs text-slate-500 font-medium">Warna: {{ $order->color ?? $order->shoe_color ?? '-' }} | Size: {{ $order->size ?? $order->shoe_size ?? '-' }}</p>
                        </div>

                        <div>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">LAYANAN YANG DIAMBIL</span>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($order->services as $service)
                                    <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600">
                                        {{ $service->name }}
                                    </span>
                                @empty
                                    <p class="text-xs text-slate-400 italic">Tidak ada layanan spesifik.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Finlog & Stock Availability Card --}}
                @php $availability = $this->stockAvailability; @endphp
                <div class="bg-slate-900 text-white rounded-3xl p-6 shadow-xl border border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-widest text-emerald-400">FINLOG INTEGRATION</span>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800">REST API</span>
                    </div>

                    <div>
                        <div class="flex items-baseline justify-between mb-2">
                            <span class="text-xs font-medium text-slate-400">Ketersediaan Stok Lokal:</span>
                            <span class="text-2xl font-black text-white">{{ $availability }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $availability }}%"></div>
                        </div>
                    </div>

                    @if($availability < 100)
                        <button wire:click="requestMaterial" wire:loading.attr="disabled"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-4 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-md flex items-center justify-center gap-2">
                            <svg wire:loading.remove wire:target="requestMaterial" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                            <svg wire:loading wire:target="requestMaterial" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Ajukan Belanja ke Finlog (REST API)
                        </button>
                    @endif
                </div>
            </div>

            {{-- Right Column: Material Table & Actions --}}
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-slate-50/50 dark:bg-slate-700/50">
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 dark:text-white">ALOKASI BAHAN MATERIAL SPK</h3>
                        <span class="text-xs font-bold text-slate-500">Total: {{ count($selectedMaterials) }} Item</span>
                    </div>

                    <div class="p-6">
                        {{-- MATERIAL CATALOG SELECTOR (TAMBAH MATERIAL) --}}
                        <div x-data="{ matTab: '{{ $activeTab == 'sol' ? 'sol' : 'upper' }}' }" class="mb-6 p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-black uppercase text-slate-800 dark:text-white">➕ TAMBAH MATERIAL SPK</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 rounded-lg">Katalog Master</span>
                                </div>
                                <div class="relative w-full sm:w-64">
                                    <input type="text" wire:model.live.debounce.300ms="searchMaterial" placeholder="Cari material..." class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                            </div>

                            {{-- Material Tabs --}}
                            <div class="flex border-b border-slate-200 dark:border-slate-600 mb-3 gap-2">
                                <button @click="matTab = 'sol'" :class="matTab === 'sol' ? 'border-amber-500 text-amber-600 dark:text-amber-400 font-black' : 'border-transparent text-slate-500 font-bold'" class="py-2 px-3 text-xs border-b-2 transition-all">
                                    👟 Material Sol ({{ $solMaterials->count() }})
                                </button>
                                <button @click="matTab = 'upper'" :class="matTab === 'upper' ? 'border-amber-500 text-amber-600 dark:text-amber-400 font-black' : 'border-transparent text-slate-500 font-bold'" class="py-2 px-3 text-xs border-b-2 transition-all">
                                    🧤 Material Upper/Cat ({{ $upperMaterials->count() }})
                                </button>
                                <button @click="matTab = 'other'" :class="matTab === 'other' ? 'border-amber-500 text-amber-600 dark:text-amber-400 font-black' : 'border-transparent text-slate-500 font-bold'" class="py-2 px-3 text-xs border-b-2 transition-all">
                                    🛒 Material Belanja/Lain ({{ $otherMaterials->count() }})
                                </button>
                            </div>

                            {{-- Tab 1: Sol Materials --}}
                            <div x-show="matTab === 'sol'" class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1">
                                @forelse($solMaterials as $mat)
                                <div class="p-2.5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-bold text-slate-800 dark:text-white block">{{ $mat->name }}</span>
                                        <span class="text-[10px] {{ $mat->stock > 0 ? 'text-emerald-600 font-bold' : 'text-rose-500 font-bold' }}">
                                            Stok: {{ $mat->stock }} {{ $mat->unit ?? 'unit' }} {{ $mat->stock <= 0 ? '(Akan Belanja Finlog)' : '' }}
                                        </span>
                                    </div>
                                    <button wire:click="addMaterial({{ $mat->id }})" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[10px] transition-all shadow-sm">
                                        + Tambah
                                    </button>
                                </div>
                                @empty
                                <p class="text-xs text-slate-400 py-2 col-span-2 text-center">Tidak ada material sol yang ditemukan.</p>
                                @endforelse
                            </div>

                            {{-- Tab 2: Upper Materials --}}
                            <div x-show="matTab === 'upper'" class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1">
                                @forelse($upperMaterials as $mat)
                                <div class="p-2.5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-bold text-slate-800 dark:text-white block">{{ $mat->name }}</span>
                                        <span class="text-[10px] {{ $mat->stock > 0 ? 'text-emerald-600 font-bold' : 'text-rose-500 font-bold' }}">
                                            Stok: {{ $mat->stock }} {{ $mat->unit ?? 'unit' }} {{ $mat->stock <= 0 ? '(Akan Belanja Finlog)' : '' }}
                                        </span>
                                    </div>
                                    <button wire:click="addMaterial({{ $mat->id }})" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[10px] transition-all shadow-sm">
                                        + Tambah
                                    </button>
                                </div>
                                @empty
                                <p class="text-xs text-slate-400 py-2 col-span-2 text-center">Tidak ada material upper yang ditemukan.</p>
                                @endforelse
                            </div>

                            {{-- Tab 3: Other / Shopping Materials --}}
                            <div x-show="matTab === 'other'" class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1">
                                @forelse($otherMaterials as $mat)
                                <div class="p-2.5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-bold text-slate-800 dark:text-white block">{{ $mat->name }}</span>
                                        <span class="text-[10px] {{ $mat->stock > 0 ? 'text-emerald-600 font-bold' : 'text-rose-500 font-bold' }}">
                                            Stok: {{ $mat->stock }} {{ $mat->unit ?? 'unit' }} {{ $mat->stock <= 0 ? '(Akan Belanja Finlog)' : '' }}
                                        </span>
                                    </div>
                                    <button wire:click="addMaterial({{ $mat->id }})" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[10px] transition-all shadow-sm">
                                        + Tambah
                                    </button>
                                </div>
                                @empty
                                <p class="text-xs text-slate-400 py-2 col-span-2 text-center">Tidak ada material lainnya yang ditemukan.</p>
                                @endforelse
                            </div>
                        </div>

                        <table class="w-full text-left text-sm">
                            <thead class="text-xs text-slate-400 uppercase border-b border-slate-100 dark:border-slate-700">
                                <tr>
                                    <th class="py-3 px-4">Nama Material</th>
                                    <th class="py-3 px-4 text-center">Jumlah</th>
                                    <th class="py-3 px-4 text-center">Status Stok</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($selectedMaterials as $id => $data)
                                    <tr>
                                        <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-white">
                                            {{ $data['name'] }}
                                            <span class="block text-[10px] font-normal text-slate-400">{{ $data['type'] ?? 'Material' }}</span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button wire:click="updateQuantity({{ $id }}, {{ $data['quantity'] - 1 }})" class="p-1 hover:text-slate-900 text-slate-400">-</button>
                                                <span class="font-bold w-6 text-center">{{ $data['quantity'] }}</span>
                                                <button wire:click="updateQuantity({{ $id }}, {{ $data['quantity'] + 1 }})" class="p-1 hover:text-emerald-600 text-slate-400">+</button>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if(($data['status'] ?? '') == 'ALLOCATED')
                                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 text-xs font-bold rounded-full">ALLOCATED</span>
                                            @else
                                                <span class="px-2.5 py-1 bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 text-xs font-bold rounded-full">REQUESTED</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            <button type="button" wire:click="removeMaterial({{ $id }})" class="text-rose-500 hover:text-rose-700 text-xs font-bold">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400 text-xs">Belum ada material yang dipilih untuk SPK ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-medium">Simpan alokasi stok untuk SPK</span>
                            <button type="button" wire:click="saveMaterials" class="px-6 py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow">
                                Simpan Perubahan Material
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Final Action Center Card --}}
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-900 dark:text-white">PENYELESAIAN & VALIDASI SORTIR</h3>
                        <span class="text-[10px] font-bold text-slate-400">Semua perubahan material & klasifikasi otomatis tersimpan saat submit</span>
                    </div>

                    {{-- Dynamic Routing Preview Banner --}}
                    @php
                        // Jika material sudah ALLOCATED, override rute menjadi Siap Surat Jalan
                        $isReadyRoute = $isMaterialSiap || !$perlu_belanja;
                    @endphp
                    <div class="p-4 rounded-2xl border transition-all {{ $isReadyRoute ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200' : 'bg-purple-50 dark:bg-purple-950/30 border-purple-200 dark:border-purple-800 text-purple-900 dark:text-purple-200' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-lg {{ $isReadyRoute ? 'bg-emerald-600 text-white' : 'bg-purple-500 text-white' }}">
                                {{ $isReadyRoute ? '🚀' : '🛒' }}
                            </div>
                            <div>
                                <span class="text-xs font-black uppercase tracking-wide block">
                                    @if($isMaterialSiap)
                                        ✅ Material Siap — Rute: Siap Surat Jalan (Sortir ➔ Produksi)
                                    @elseif(!$perlu_belanja)
                                        Rute: Siap Surat Jalan (Sortir ➔ Produksi)
                                    @else
                                        Rute: Rak Tunggu Belanja Finlog
                                    @endif
                                </span>
                                <p class="text-[11px] font-medium opacity-90 mt-0.5">
                                    @if($isMaterialSiap)
                                        Stok material sudah dialokasikan. SPK akan ditandai <strong>Siap Diterbitkan Surat Jalan</strong> untuk diserah-terimakan ke Tim Produksi.
                                    @elseif(!$perlu_belanja)
                                        Klasifikasi OK & material Siap! SPK akan ditandai <strong>Siap Diterbitkan Surat Jalan</strong> untuk diserah-terimakan ke Tim Produksi.
                                    @else
                                        SPK akan dipindahkan ke <strong>Rak Tunggu Belanja</strong>. Jika ada material berstatus REQUESTED, Pengajuan Belanja Finlog akan <strong>otomatis dibuat</strong>.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Form Bypass Alasan Wajib (FR-11.1) --}}
                    @if(in_array(auth()->user()->role, ['admin', 'owner', 'production_manager']))
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                            <label class="block text-xs font-bold text-rose-600 mb-1">Bypass Servis Alasan Wajib (FR-11.1)</label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="bypass_reason" placeholder="Ketik alasan bypass di sini (minimal 5 karakter)..." class="flex-1 text-xs rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                                <button type="button" wire:click="bypassSortir" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition">
                                    Bypass Servis
                                </button>
                            </div>
                            @error('bypass_reason') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    {{-- Main Actions Row --}}
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <button type="button" wire:click="saveDraft" wire:loading.attr="disabled" class="w-full sm:w-auto px-5 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl transition inline-flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="saveDraft">💾 Simpan Draft (Tanpa Pindah Status)</span>
                            <span wire:loading wire:target="saveDraft">⏳ Menyimpan...</span>
                        </button>

                        @if(!$isReadyRoute)
                            {{-- Belanja masih diperlukan --}}
                            <button type="button" wire:click="completeSortir" wire:loading.attr="disabled" class="w-full sm:w-auto px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-black rounded-xl text-xs uppercase tracking-wider shadow-lg shadow-purple-500/20 transition inline-flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="completeSortir">🛒 Simpan & Buat Pengajuan Belanja Finlog ➔</span>
                                <span wire:loading wire:target="completeSortir">⏳ Memproses...</span>
                            </button>
                        @else
                            {{-- Material siap / tidak perlu belanja → langsung surat jalan --}}
                            <button type="button" wire:click="completeSortir" wire:loading.attr="disabled" class="w-full sm:w-auto px-6 py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 transition inline-flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="completeSortir">🚀 Selesaikan Klasifikasi (Siap Surat Jalan Produksi) ➔</span>
                                <span wire:loading wire:target="completeSortir">⏳ Memproses...</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
