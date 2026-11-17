<div class="min-h-screen bg-[#f8fafc] pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
                 class="mb-6 p-4 bg-emerald-600 text-white rounded-2xl shadow-lg flex items-center justify-between">
                <span class="text-sm font-bold">{{ session('success') }}</span>
                <button type="button" @click="show = false" class="text-white/80 hover:text-white font-black px-2">✕</button>
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="mb-6 p-4 bg-rose-600 text-white rounded-2xl shadow-lg flex items-center justify-between">
                <span class="text-sm font-bold">⚠️ {{ session('error') }}</span>
                <button type="button" @click="show = false" class="text-white/80 hover:text-white font-black px-2">✕</button>
            </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="px-2.5 py-0.5 rounded-md bg-teal-50 text-[#0f766e] border border-teal-200 text-[10px] font-black uppercase tracking-wider">Workshop Station</span>
                    <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-black uppercase tracking-wider">Non-OTO</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight uppercase">Stasiun Tambah Jasa</h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Antrean pengerjaan layanan tambahan (Non-OTO) dikelompokkan per SPK.</p>
            </div>

            {{-- Tabs --}}
            <div class="inline-flex bg-white p-1.5 rounded-2xl border border-slate-200 shadow-xs self-start">
                <button type="button" wire:click="$set('activeTab', 'antrean')"
                        class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer {{ $activeTab === 'antrean' ? 'bg-[#22AF85] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                    Antrean Aktif ({{ $this->counts['antrean'] }})
                </button>
                <button type="button" wire:click="$set('activeTab', 'riwayat')"
                        class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer {{ $activeTab === 'riwayat' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                    Riwayat Selesai ({{ $this->counts['riwayat'] }})
                </button>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-xs flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Cari nomor SPK, nama customer, atau nama layanan..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:border-[#22AF85] focus:ring-0">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <select wire:model.live="technicianFilter" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 focus:border-[#22AF85] focus:ring-0">
                <option value="all">👷 Semua Teknisi</option>
                @foreach($this->techs['all'] as $tech)
                    <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Grouped Cards List (1 Card per SPK) --}}
        <div class="space-y-5">
            @forelse($workOrders as $wo)
                @php
                    $tjItems = $wo->additionalServices;
                    $totalCost = $tjItems->sum('cost');
                    $allHaveTech = $tjItems->every(fn($i) => !empty($i->sol_by) || !empty($i->upper_by) || !empty($i->treatment_by));
                    $firstItem = $tjItems->first();
                @endphp
                <div wire:key="tj-wo-{{ $wo->id }}" class="bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all">
                    {{-- SPK Header Row --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-4">
                            @if($wo->spk_cover_photo_url)
                                <img src="{{ $wo->spk_cover_photo_url }}" class="w-14 h-14 rounded-2xl object-cover border border-slate-200 shrink-0">
                            @else
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center shrink-0 text-xs font-black">SPK</div>
                            @endif

                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <button type="button"
                                            wire:click="openDetailModal({{ $wo->id }})"
                                            class="font-mono text-base font-black text-[#0f766e] hover:underline cursor-pointer text-left">
                                        {{ $wo->spk_number }}
                                    </button>
                                    <button type="button"
                                            wire:click="openDetailModal({{ $wo->id }})"
                                            class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-teal-50 text-slate-600 hover:text-[#0f766e] border border-slate-200 text-[10px] font-bold transition-colors cursor-pointer">
                                        👁️ Detail &amp; Foto
                                    </button>
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase {{ $activeTab === 'antrean' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                        {{ $activeTab === 'antrean' ? 'Sedang Dikerjakan (' . $tjItems->count() . ' Jasa)' : 'Selesai (' . $tjItems->count() . ' Jasa)' }}
                                    </span>
                                    <span class="text-[11px] font-bold text-slate-400">• {{ $firstItem?->created_at?->format('d M Y H:i') }}</span>
                                </div>
                                <p class="text-xs font-semibold text-slate-600">
                                    <span class="font-bold text-slate-800">{{ $wo->customer_name ?? '-' }}</span>
                                    • {{ $wo->shoe_brand ?? '-' }} {{ $wo->shoe_type ?? '' }}
                                    • Total: <span class="font-mono font-black text-emerald-700">Rp {{ number_format($totalCost, 0, ',', '.') }}</span>
                                </p>
                            </div>
                        </div>

                        @if($activeTab === 'antrean')
                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <button type="button"
                                        x-data
                                        @click="
                                            @if(!$allHaveTech)
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Teknisi Belum Lengkap',
                                                    text: 'Harap pilih teknisi untuk seluruh layanan Tambah Jasa terlebih dahulu.',
                                                    confirmButtonColor: '#22AF85'
                                                });
                                            @else
                                                Swal.fire({
                                                    icon: 'question',
                                                    title: 'Selesaikan Tambah Jasa?',
                                                    text: 'Seluruh layanan tambahan untuk SPK {{ $wo->spk_number }} akan ditandai selesai dan kembali ke Finish.',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#22AF85',
                                                    cancelButtonColor: '#64748b',
                                                    confirmButtonText: 'Ya, Selesai!',
                                                    cancelButtonText: 'Batal'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.markWorkOrderCompleted({{ $wo->id }});
                                                    }
                                                });
                                            @endif
                                        "
                                        wire:loading.attr="disabled"
                                        class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer shrink-0 {{ $allHaveTech ? 'bg-[#22AF85] hover:bg-[#198f6b] text-white shadow-md shadow-emerald-200' : 'bg-slate-200 text-slate-500 hover:bg-slate-300' }}">
                                    ✓ Selesai Semua
                                </button>
                                @if(!$allHaveTech)
                                    <span class="text-[10px] font-bold text-amber-600">Pilih semua teknisi dulu</span>
                                @endif
                            </div>
                        @else
                            <div class="text-right text-xs text-slate-500 font-semibold shrink-0">
                                <div>Selesai: <span class="font-bold text-slate-800">{{ $firstItem?->completed_at?->format('d M Y H:i') }}</span></div>
                                <div>Oleh: <span class="font-bold text-slate-800">{{ $firstItem?->completer?->name ?? '-' }}</span></div>
                            </div>
                        @endif
                    </div>

                    {{-- Per-Service Rows inside 1 SPK Card --}}
                    <div class="mt-4 space-y-3">
                        @foreach($tjItems as $item)
                            @php
                                $catLower = strtolower(($item->category_name ?? '') . ' ' . ($item->service_name ?? ''));
                                $isSolCat = str_contains($catLower, 'sol') || str_contains($catLower, 'reglue') || str_contains($catLower, 'midsole');
                                $isUpperCat = str_contains($catLower, 'upper') || str_contains($catLower, 'jahit') || str_contains($catLower, 'stitch') || str_contains($catLower, 'leather') || str_contains($catLower, 'suede');
                                $isTreatmentCat = str_contains($catLower, 'treatment') || str_contains($catLower, 'clean') || str_contains($catLower, 'repaint') || str_contains($catLower, 'whitening');
                                $showAllStations = !$isSolCat && !$isUpperCat && !$isTreatmentCat;
                                $assignedTechName = $item->solBy?->name ?? $item->upperBy?->name ?? $item->treatmentBy?->name;
                            @endphp
                            <div wire:key="tj-item-{{ $item->id }}" class="flex flex-col md:flex-row md:items-center justify-between gap-3 p-3.5 rounded-2xl bg-slate-50/90 border border-slate-200/80">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-xs font-black text-slate-900">{{ $loop->iteration }}. {{ $item->service_name }}</span>
                                        <span class="text-[11px] font-bold text-slate-400">({{ $item->category_name }})</span>
                                        <span class="font-mono text-xs font-bold text-emerald-700">Rp {{ number_format($item->cost, 0, ',', '.') }}</span>
                                    </div>
                                    @if($item->notes)
                                        <p class="mt-1.5 text-[11px] text-slate-600 font-medium">📝 {{ $item->notes }}</p>
                                    @endif
                                </div>

                                @if($activeTab === 'antrean')
                                    <div class="flex flex-wrap items-end gap-2 shrink-0">
                                        @if($isSolCat || $showAllStations)
                                            <div class="min-w-[160px]">
                                                <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Teknisi Sol</label>
                                                <select wire:change="assignTechnician({{ $item->id }}, 'sol', $event.target.value)" class="w-full text-[11px] font-bold bg-white border border-slate-200 rounded-xl px-2.5 py-2">
                                                    <option value="">- Pilih Teknisi Sol -</option>
                                                    @foreach($this->techs['sol'] as $t)
                                                        <option value="{{ $t->id }}" {{ $item->sol_by == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif

                                        @if($isUpperCat || $showAllStations)
                                            <div class="min-w-[160px]">
                                                <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Teknisi Upper</label>
                                                <select wire:change="assignTechnician({{ $item->id }}, 'upper', $event.target.value)" class="w-full text-[11px] font-bold bg-white border border-slate-200 rounded-xl px-2.5 py-2">
                                                    <option value="">- Pilih Teknisi Upper -</option>
                                                    @foreach($this->techs['upper'] as $t)
                                                        <option value="{{ $t->id }}" {{ $item->upper_by == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif

                                        @if($isTreatmentCat || $showAllStations)
                                            <div class="min-w-[160px]">
                                                <label class="block text-[9px] font-black text-slate-400 uppercase mb-1">Teknisi Treatment</label>
                                                <select wire:change="assignTechnician({{ $item->id }}, 'treatment', $event.target.value)" class="w-full text-[11px] font-bold bg-white border border-slate-200 rounded-xl px-2.5 py-2">
                                                    <option value="">- Pilih Treatment -</option>
                                                    @foreach($this->techs['treatment'] as $t)
                                                        <option value="{{ $t->id }}" {{ $item->treatment_by == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-xs font-bold text-slate-600 shrink-0">
                                        👷 {{ $assignedTechName ?? '-' }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center">
                    <p class="text-sm font-black text-slate-400 uppercase tracking-widest">Belum ada data Tambah Jasa di tab ini</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $workOrders->links() }}
        </div>
    </div>

    {{-- Modal Detail SPK & Foto Before / After --}}
    @if($showDetailModal && $this->detailWorkOrder)
        @php
            $dwo = $this->detailWorkOrder;
            $afterSteps = ['QC_FINAL', 'SELESAI', 'FINISH'];
            $afterPhotos = $dwo->photos->whereIn('step', $afterSteps)->values();
            $beforePhotos = $dwo->photos->whereNotIn('step', $afterSteps)->values();
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/75 backdrop-blur-xs overflow-y-auto"
             wire:keydown.escape.window="closeDetailModal">
            <div class="bg-white rounded-3xl max-w-4xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8">
                {{-- Modal Header --}}
                <div class="px-6 py-4 bg-gradient-to-r from-[#22AF85] to-[#178a68] text-white flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-mono text-lg font-black">{{ $dwo->spk_number }}</h3>
                            <span class="px-2 py-0.5 rounded bg-white/20 text-[10px] font-black uppercase">
                                {{ is_object($dwo->status) ? $dwo->status->label() : $dwo->status }}
                            </span>
                        </div>
                        <p class="text-xs text-white/85 mt-0.5">{{ $dwo->customer_name ?? '-' }} • {{ $dwo->customer_phone ?? '-' }}</p>
                    </div>
                    <button type="button" wire:click="closeDetailModal" class="text-white/80 hover:text-white font-black text-lg px-2 cursor-pointer">✕</button>
                </div>

                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                    {{-- Informasi Sepatu & SPK --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-xs">
                        <div>
                            <span class="block text-[10px] font-black text-slate-400 uppercase">Merk / Tipe</span>
                            <span class="font-bold text-slate-800">{{ $dwo->shoe_brand ?? '-' }} {{ $dwo->shoe_type ?? '' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-slate-400 uppercase">Warna / Size</span>
                            <span class="font-bold text-slate-800">{{ $dwo->shoe_color ?? '-' }} / {{ $dwo->shoe_size ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-slate-400 uppercase">Tgl Masuk</span>
                            <span class="font-bold text-slate-800">{{ $dwo->entry_date ? $dwo->entry_date->format('d M Y') : '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-slate-400 uppercase">Estimasi</span>
                            <span class="font-bold text-slate-800">{{ $dwo->estimation_date ? $dwo->estimation_date->format('d M Y') : '-' }}</span>
                        </div>
                        @if($dwo->notes)
                            <div class="col-span-2 sm:col-span-4 pt-2 border-t border-slate-200/70">
                                <span class="block text-[10px] font-black text-slate-400 uppercase">Catatan SPK</span>
                                <p class="font-medium text-slate-700 mt-0.5">{{ $dwo->notes }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Foto Before & After --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Foto Before --}}
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-black text-slate-700 uppercase tracking-wider">📷 Foto Before ({{ $beforePhotos->count() }})</span>
                            </div>
                            @if($beforePhotos->isNotEmpty())
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($beforePhotos as $photo)
                                        <a href="{{ $photo->photo_url }}" target="_blank" class="aspect-square rounded-xl overflow-hidden border border-slate-200 bg-white hover:scale-105 transition-transform">
                                            <img src="{{ $photo->photo_url }}" class="w-full h-full object-cover" alt="Foto Before">
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic py-6 text-center">Belum ada foto before.</p>
                            @endif
                        </div>

                        {{-- Foto After --}}
                        <div class="p-4 rounded-2xl border border-emerald-200 bg-emerald-50/40">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-black text-emerald-800 uppercase tracking-wider">✨ Foto After ({{ $afterPhotos->count() }})</span>
                            </div>
                            @if($afterPhotos->isNotEmpty())
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($afterPhotos as $photo)
                                        <a href="{{ $photo->photo_url }}" target="_blank" class="aspect-square rounded-xl overflow-hidden border border-emerald-200 bg-white hover:scale-105 transition-transform">
                                            <img src="{{ $photo->photo_url }}" class="w-full h-full object-cover" alt="Foto After">
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic py-6 text-center">Belum ada foto after.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Daftar Layanan SPK & Tambah Jasa --}}
                    <div>
                        <h4 class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Daftar Layanan SPK</h4>
                        <div class="space-y-1.5">
                            @foreach($dwo->workOrderServices as $svc)
                                <div class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200/80 text-xs">
                                    <div>
                                        <span class="font-bold text-slate-800">{{ $svc->custom_service_name ?: $svc->service?->name }}</span>
                                        <span class="text-slate-400 ml-1">({{ $svc->category_name }})</span>
                                        @if(!empty($svc->service_details['is_tambah_jasa']))
                                            <span class="ml-1.5 px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-black uppercase">Tambah Jasa</span>
                                        @endif
                                    </div>
                                    <span class="font-mono font-bold text-slate-700">Rp {{ number_format($svc->cost ?? 0, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end">
                    <button type="button" wire:click="closeDetailModal" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('tambah-jasa-completed', (event) => {
                const spk = event?.spk || (Array.isArray(event) ? event[0]?.spk : '') || '';
                Swal.fire({
                    icon: 'success',
                    title: 'Tambah Jasa Selesai!',
                    text: spk ? `SPK ${spk} telah selesai dan kembali ke antrean Finish / Tracking.` : 'Seluruh Tambah Jasa berhasil diselesaikan!',
                    confirmButtonColor: '#22AF85',
                    timer: 3500,
                    timerProgressBar: true
                });
            });
        });
    </script>
</div>
