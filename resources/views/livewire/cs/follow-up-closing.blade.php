<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- Navigation Header --}}
        <div class="flex flex-col gap-4 mb-6">
            <div class="flex flex-wrap gap-2">
                <button class="px-5 py-3 bg-teal-600 text-white rounded-xl shadow-lg font-black text-sm flex items-center gap-2.5 transition-all">
                    <span class="text-lg">📦</span> Follow Up Closing ({{ $activeCount }})
                </button>
                <a href="{{ route('cs.dashboard') }}" class="px-5 py-3 bg-white text-gray-700 hover:bg-gray-50 rounded-xl shadow font-bold text-sm flex items-center gap-2.5 transition-all">
                    <span class="text-lg">📊</span> Dashboard Leads
                </a>
            </div>

            {{-- Search & Filter Form (Big 4 Standard) --}}
            <div class="w-full bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    <div class="lg:col-span-12 xl:col-span-8 flex flex-col md:flex-row gap-3">
                        <div class="relative flex-grow">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search" 
                                   class="w-full pl-10 pr-4 py-2.5 border-gray-200 rounded-xl text-sm focus:ring-teal-500 focus:border-teal-500 shadow-sm transition-all bg-gray-50/30" 
                                   placeholder="Cari Nomor SPK, Nama Pelanggan, atau WhatsApp...">
                        </div>

                        <div class="flex gap-2 shrink-0">
                            <select wire:model.live="sort" class="w-full md:w-auto border-amber-200 rounded-xl text-sm font-black text-amber-700 focus:ring-amber-500 py-2.5 px-4 bg-amber-50/50 shadow-sm appearance-none">
                                <option value="asc">⏳ Terlama</option>
                                <option value="desc">🔥 Terbaru</option>
                            </select>

                            <button wire:click="resetFilters" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm shadow-sm transition-all">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Row 2: Date Filters --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 pt-4 border-t border-gray-50">
                    <div class="flex items-center gap-2 md:col-span-2">
                        <div class="relative flex-grow">
                            <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 absolute -top-2.5 left-2 bg-white px-1 z-10">Dari Tgl</label>
                            <input type="date" wire:model.live="start_date" class="w-full border-gray-200 rounded-xl text-xs font-bold text-gray-600 focus:ring-teal-500 py-2.5 bg-gray-50/30">
                        </div>
                        <span class="text-gray-300 text-xs">s/d</span>
                        <div class="relative flex-grow">
                            <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 absolute -top-2.5 left-2 bg-white px-1 z-10">Sampai Tgl</label>
                            <input type="date" wire:model.live="end_date" class="w-full border-gray-200 rounded-xl text-xs font-bold text-gray-600 focus:ring-teal-500 py-2.5 bg-gray-50/30">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Content --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
            <div class="p-6">
                @if(session()->has('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl font-bold">
                        🎉 {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-[10px] text-gray-400 uppercase bg-gray-50/50 border-b">
                            <tr>
                                <th class="px-6 py-4">Informasi SPK</th>
                                <th class="px-6 py-4">Data Pelanggan</th>
                                <th class="px-6 py-4 w-1/3">Detail Penolakan Gudang (QC Awal Gagal)</th>
                                <th class="px-6 py-4 text-center">Resolusi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($data as $order)
                                @php
                                    $openIssue = $order->cxIssues->where('status', 'OPEN')->where('source', 'GUDANG')->first();
                                    $reporter = $openIssue ? ($openIssue->reporter->name ?? 'Gudang QC') : 'Gudang QC';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-all duration-300 bg-white">
                                    {{-- Info Order --}}
                                    <td class="px-6 py-4 align-top">
                                        <div class="text-[10px] font-black text-gray-400 uppercase leading-none mb-1">Estimasi Selesai</div>
                                        <div class="font-bold text-gray-900 leading-tight">{{ $order->estimation_date ? $order->estimation_date->format('d M Y') : '-' }}</div>
                                        
                                        @if($openIssue)
                                            <div class="mt-2 pt-2 border-t border-gray-50">
                                                <div class="text-[10px] font-black text-red-500 uppercase leading-none mb-1">Rejected Physical QC</div>
                                                <div class="text-[11px] font-bold text-gray-700">{{ $openIssue->created_at->translatedFormat('d M Y H:i') }}</div>
                                            </div>
                                        @endif

                                        <div class="font-mono bg-amber-50 text-amber-700 px-2.5 py-1 rounded inline-block mt-2 text-xs font-black border border-amber-100">
                                            {{ $order->spk_number }}
                                        </div>
                                    </td>

                                    {{-- Customer --}}
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-bold text-gray-900 text-base leading-tight">{{ $order->customer_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                                        <div class="text-xs font-bold text-teal-600 mt-1 uppercase tracking-tight">{{ $order->shoe_brand }} ({{ $order->shoe_color ?? '-' }})</div>

                                        <div class="flex items-center gap-1.5 mt-3 bg-gray-50 p-1.5 rounded-lg border border-gray-100 w-fit">
                                            <div class="w-5 h-5 rounded-full bg-teal-100 flex items-center justify-center text-[8px] text-teal-600 font-bold border border-teal-200">
                                                CS
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[9px] text-gray-400 leading-none">Pemilik Akun</span>
                                                <span class="text-[10px] font-bold text-gray-700">{{ $order->cs_id ? (\App\Models\User::find($order->cs_id)->name ?? 'Unassigned') : 'Unassigned' }}</span>
                                            </div>
                                        </div>

                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', $order->customer_phone) }}" target="_blank" 
                                           class="inline-flex items-center gap-1.5 text-[10px] bg-green-100 text-green-700 px-3 py-1.5 rounded-lg mt-2 font-bold hover:bg-green-200 transition-colors">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                            Hubungi via WA
                                        </a>
                                    </td>

                                    {{-- Issue Details --}}
                                    <td class="px-6 py-4 align-top">
                                        @if($openIssue)
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="text-[9px] uppercase font-black tracking-widest px-1.5 py-0.5 rounded border bg-red-100 text-red-700 border-red-200">
                                                    📦 GUDANG QC REJECT
                                                </span>
                                                <span class="text-[9px] uppercase font-bold text-gray-400">Pemeriksa: {{ $reporter }}</span>
                                            </div>

                                            <div class="space-y-3">
                                                @php
                                                    // Helper to clean up strings and detect if they are empty or just dash
                                                    $cleanFn = function($str) {
                                                        $trimmed = trim($str ?? '');
                                                        return ($trimmed === '-' || $trimmed === '' || strtolower($trimmed) === 'null') ? '' : $trimmed;
                                                    };

                                                    // 1. Try to get values from the structured columns
                                                    $upper = $cleanFn($openIssue->desc_upper);
                                                    $sol = $cleanFn($openIssue->desc_sol);
                                                    $bawaan = $cleanFn($openIssue->desc_kondisi_bawaan);

                                                    // 2. If all of them are empty, or if we have a piped description, parse it from description
                                                    if (empty($upper) && empty($sol) && empty($bawaan) && !empty($openIssue->description)) {
                                                        $parts = explode('|', $openIssue->description);
                                                        $upper = isset($parts[0]) ? $cleanFn($parts[0]) : '';
                                                        $sol = isset($parts[1]) ? $cleanFn($parts[1]) : '';
                                                        $bawaan = isset($parts[2]) ? $cleanFn($parts[2]) : '';
                                                    }

                                                    // 3. Fallback: check if raw description itself is not just dashes
                                                    $rawDesc = trim($openIssue->description ?? '');
                                                    $isRawDescValid = ($rawDesc !== '-' && $rawDesc !== '' && $rawDesc !== '||' && $rawDesc !== '| |' && $rawDesc !== '- | - | -');
                                                @endphp

                                                <div class="bg-gradient-to-br from-red-50 to-orange-50/50 p-4.5 rounded-2xl border border-red-100/70 space-y-3.5 shadow-sm">
                                                    <div class="text-[11px] font-black text-red-700 uppercase tracking-widest flex items-center gap-2 mb-1">
                                                        <span class="flex h-2 w-2 relative">
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                                        </span>
                                                        Catatan Kerusakan & Kondisi Fisik
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-2.5">
                                                        @if(!empty($upper))
                                                            <div class="bg-white/80 backdrop-blur-sm p-3 rounded-xl border border-red-100/50 shadow-sm flex items-start gap-3 hover:shadow-md transition-all duration-300">
                                                                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-base shrink-0 border border-red-100">
                                                                    👟
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-[9px] font-extrabold text-red-600 uppercase tracking-wider leading-none mb-1">Upper / Bagian Atas</p>
                                                                    <p class="text-xs text-gray-700 font-bold leading-relaxed break-words">{{ $upper }}</p>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if(!empty($sol))
                                                            <div class="bg-white/80 backdrop-blur-sm p-3 rounded-xl border border-orange-100/50 shadow-sm flex items-start gap-3 hover:shadow-md transition-all duration-300">
                                                                <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-base shrink-0 border border-orange-100">
                                                                    👣
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-[9px] font-extrabold text-orange-600 uppercase tracking-wider leading-none mb-1">Midsole & Outsole</p>
                                                                    <p class="text-xs text-gray-700 font-bold leading-relaxed break-words">{{ $sol }}</p>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if(!empty($bawaan))
                                                            <div class="bg-white/80 backdrop-blur-sm p-3 rounded-xl border border-blue-100/50 shadow-sm flex items-start gap-3 hover:shadow-md transition-all duration-300">
                                                                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-base shrink-0 border border-blue-100">
                                                                    💼
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-[9px] font-extrabold text-blue-600 uppercase tracking-wider leading-none mb-1">Kondisi Bawaan</p>
                                                                    <p class="text-xs text-gray-700 font-bold leading-relaxed break-words">{{ $bawaan }}</p>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if(empty($upper) && empty($sol) && empty($bawaan))
                                                            @if($isRawDescValid)
                                                                <div class="bg-white/80 backdrop-blur-sm p-4 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-3">
                                                                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-base shrink-0 border border-gray-200">
                                                                        📝
                                                                    </div>
                                                                    <div class="flex-1 min-w-0">
                                                                        <p class="text-[9px] font-extrabold text-gray-500 uppercase tracking-wider leading-none mb-1">Deskripsi Kerusakan</p>
                                                                        <p class="text-xs text-gray-700 font-medium italic leading-relaxed break-words">"{{ $openIssue->description }}"</p>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="bg-white/80 backdrop-blur-sm p-4 rounded-xl border border-gray-200/50 text-center">
                                                                    <p class="text-xs text-gray-400 italic font-medium">Tidak ada catatan kerusakan fisik spesifik.</p>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Evidence Photos --}}
                                                @if($openIssue->photo_urls && count($openIssue->photo_urls) > 0)
                                                    <div>
                                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Foto Bukti Fisik:</div>
                                                        <div class="flex flex-wrap gap-2">
                                                            @foreach($openIssue->photo_urls as $photoUrl)
                                                                @if($photoUrl)
                                                                    <a href="{{ route('cx-issues.report', $order->spk_number) }}" target="_blank" class="block relative group overflow-hidden rounded-lg border border-gray-200 shadow-sm transition-all hover:scale-105 active:scale-95">
                                                                        <img src="{{ $photoUrl }}" alt="QC Reject Evidence" class="w-16 h-16 object-cover bg-gray-50">
                                                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center transition-opacity">
                                                                            <span class="text-white text-[9px] font-black uppercase tracking-wider">Laporan</span>
                                                                            <span class="text-teal-400 text-[8px] font-bold">Buka ↗</span>
                                                                        </div>
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Recommended/Suggested Services --}}
                                                @if($openIssue->recommended_services)
                                                    <div class="pt-2 border-t border-gray-50">
                                                        <div class="text-[10px] font-black text-teal-600 uppercase tracking-widest mb-1">Rekomendasi Tambahan Layanan dari Gudang:</div>
                                                        <div class="text-xs text-gray-600 font-bold bg-teal-50/50 p-2.5 rounded-lg border border-teal-100 leading-relaxed whitespace-pre-line">{{ $openIssue->recommended_services }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-gray-400 italic">Isu sudah diselesaikan.</p>
                                        @endif
                                    </td>

                                    {{-- Action Resolusi --}}
                                    <td class="px-6 py-4 align-top text-center">
                                        <div class="flex flex-col gap-2 max-w-[180px] mx-auto">
                                            <button wire:click="openActionModal({{ $order->id }}, 'lanjut')" 
                                                    class="w-full bg-teal-600 hover:bg-teal-700 text-white font-black rounded-xl text-[10px] uppercase py-3 shadow-md transition-all active:scale-[0.98]">
                                                ✅ Lanjutkan ke Assessment
                                            </button>
                                            
                                            <button wire:click="openActionModal({{ $order->id }}, 'tambah_jasa')" 
                                                    class="w-full bg-amber-500 hover:bg-amber-600 text-white font-black rounded-xl text-[10px] uppercase py-3 shadow-md transition-all active:scale-[0.98]">
                                                ➕ Input Tambah Jasa (Upsell)
                                            </button>

                                            <button wire:click="openActionModal({{ $order->id }}, 'cancel')" 
                                                    class="w-full bg-white border border-red-100 text-red-600 font-black rounded-xl text-[10px] uppercase py-3 shadow-sm transition-all hover:bg-red-50">
                                                ❌ Batal / Cancel SPK
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="p-4 bg-gray-50 rounded-full mb-4">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <p class="text-gray-400 italic font-bold">Hebat! Semua antrean QC Reject Gudang telah selesai di-follow up.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Universal Action Modal (Elite Design) --}}
    @if($showActionModal)
    <div class="fixed inset-0 z-[150] overflow-y-auto" x-data="{ show: true }">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-md transition-opacity" wire:click="closeActionModal"></div>

            {{-- Modal Content --}}
            <div class="inline-block align-middle bg-gray-900 border border-gray-800 rounded-[2.5rem] text-left shadow-[0_0_100px_rgba(0,0,0,0.8)] transform transition-all sm:my-8 sm:max-w-xl w-full overflow-hidden relative">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 px-8 py-6 border-b border-gray-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-tighter flex items-center gap-3">
                            <span class="w-2 h-8 bg-teal-500 rounded-full"></span>
                            Konfirmasi Aksi CS
                        </h3>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">Aksi: {{ str_replace('_', ' ', $actionType) }}</p>
                    </div>
                    <button wire:click="closeActionModal" class="bg-gray-800 text-gray-400 hover:text-white p-2.5 rounded-2xl transition-all shadow-lg hover:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="px-8 py-8 space-y-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    {{-- Info Box --}}
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-teal-500/20 to-blue-500/20 rounded-2xl blur opacity-25"></div>
                        <div class="relative bg-gray-900/50 border border-gray-800 p-5 rounded-2xl flex gap-4 items-center">
                            <div class="w-12 h-12 bg-teal-500/10 rounded-xl flex items-center justify-center text-xl shadow-inner border border-teal-500/20">
                                ℹ️
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Memproses aksi untuk SPK</p>
                                <p class="text-lg font-black text-white tracking-tighter">#{{ $selectedOrder->spk_number }}</p>
                            </div>
                        </div>
                    </div>

                    @if($actionType === 'tambah_jasa')
                        <div class="space-y-6">
                            <label class="block text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] italic flex items-center gap-3 ml-1">
                                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                Tambah Layanan Baru (Upsell)
                            </label>

                            <div class="space-y-5 p-8 bg-gray-800/30 rounded-[2.5rem] border border-gray-800 shadow-inner">
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-2">1. Pilih Kategori</label>
                                        <select wire:model.live="selectedCategory" class="w-full bg-gray-900 border-gray-700 rounded-2xl px-6 py-4.5 text-sm font-bold text-gray-300 focus:ring-teal-500 transition-all">
                                            <option value="">-- Kategori --</option>
                                            @foreach($masterCategories as $cat) <option value="{{ $cat }}">{{ $cat }}</option> @endforeach
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-2">2. Pilih Layanan</label>
                                        <select wire:model.live="selectedServiceId" class="w-full bg-gray-900 border-gray-700 rounded-2xl px-6 py-4.5 text-sm font-bold text-white focus:ring-teal-500 transition-all">
                                            <option value="">-- Pilih Jasa --</option>
                                            @foreach($masterServices->where('category', $selectedCategory) as $s) 
                                                <option value="{{ $s->id }}">{{ $s->name }} (Rp{{ number_format($s->price) }})</option> 
                                            @endforeach
                                            <option value="custom" class="bg-teal-900 text-teal-400 font-black">✏️ JASA CUSTOM (KETIK MANUAL)</option>
                                        </select>
                                    </div>

                                    @if($selectedServiceId === 'custom')
                                        <div class="space-y-2 animate-in slide-in-from-top-2 duration-300">
                                            <label class="text-[9px] font-black text-teal-500 uppercase tracking-widest ml-2">Nama Jasa Manual</label>
                                            <input type="text" wire:model="customServiceName" placeholder="Ketik nama jasa di sini..." class="w-full bg-gray-900 border-teal-500/30 border-2 rounded-2xl px-6 py-4.5 text-sm font-black text-white focus:border-teal-500 focus:ring-0 shadow-[0_0_20px_rgba(20,184,166,0.1)]">
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-1 gap-4">
                                        <div class="space-y-2">
                                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-2">3. Harga (Rp)</label>
                                            <div class="relative group">
                                                <div class="absolute left-6 top-1/2 -translate-y-1/2 text-xs font-black text-teal-500">RP</div>
                                                <input type="number" wire:model="servicePrice" placeholder="0" class="w-full bg-gray-900 border-gray-700 rounded-2xl pl-14 pr-6 py-4.5 text-base font-black text-teal-400 focus:ring-teal-500">
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[9px] font-black text-amber-500 uppercase tracking-widest ml-2 flex items-center gap-2">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                                4. Catatan Jasa (NB SPK)
                                            </label>
                                            <input type="text" wire:model="serviceDetails" placeholder="Misal: Warna hitam, jahit double..." class="w-full bg-gray-900 border-amber-500/20 border-2 rounded-2xl px-6 py-4.5 text-sm font-bold text-white focus:border-amber-500 focus:ring-0 transition-all">
                                        </div>
                                    </div>
                                </div>

                                <button wire:click="addServiceToList" class="w-full bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white py-5 rounded-2xl text-xs font-black shadow-[0_15px_30px_rgba(20,184,166,0.2)] transition-all active:scale-[0.98] uppercase tracking-[0.2em] flex items-center justify-center gap-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                                    Tambahkan ke Daftar Layanan
                                </button>

                                @if(count($addedServices) > 0)
                                <div class="space-y-2 pt-4 border-t border-gray-700/50">
                                    @foreach($addedServices as $s)
                                        <div class="flex justify-between items-center bg-gray-900/80 p-3.5 rounded-xl border border-gray-700 group/item hover:border-teal-500/50 transition-all shadow-sm">
                                            <div class="flex flex-col gap-0.5">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[9px] font-black text-gray-500 uppercase tracking-tighter">{{ $s['category_name'] }}</span>
                                                    @if($s['details'])
                                                        <span class="text-[8px] bg-teal-500/10 text-teal-400 px-1.5 py-0.5 rounded border border-teal-500/20 font-bold uppercase tracking-widest">Detail Included</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs font-bold text-gray-200">{{ $s['display_name'] }}</span>
                                                @if($s['details'])
                                                    <span class="text-[10px] text-gray-500 italic mt-0.5 font-medium">"{{ $s['details'] }}"</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <span class="text-sm font-black text-teal-400 tracking-tighter italic">Rp{{ number_format($s['cost']) }}</span>
                                                <button wire:click="removeService({{ $s['id'] }})" class="text-gray-600 hover:text-red-500 transition-colors p-1 hover:bg-red-500/10 rounded-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Catatan Negosiasi / Resolusi</label>
                        <textarea wire:model="actionNotes" rows="3" class="w-full bg-gray-800/30 border border-gray-800 rounded-2xl p-5 text-white text-sm font-medium focus:ring-teal-500 focus:border-teal-500 transition-all shadow-inner custom-scrollbar" placeholder="Ketikkan kesepakatan negosiasi dengan pelanggan di sini..."></textarea>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-8 py-6 bg-gray-900 border-t border-gray-800 flex justify-end gap-4 items-center">
                    <button wire:click="closeActionModal" class="px-6 py-3 text-xs font-black text-gray-500 uppercase tracking-widest hover:text-white transition-colors">
                        Batal
                    </button>
                    <button wire:click="processAction" class="px-10 py-4 bg-teal-600 hover:bg-teal-500 text-white rounded-2xl text-xs font-black shadow-[0_15px_30px_rgba(20,184,166,0.3)] transition-all hover:scale-105 active:scale-95 uppercase tracking-widest">
                        Konfirmasi Penyelesaian
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
