<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- Navigation Tabs --}}
        <div class="flex flex-col gap-4 mb-6">
            <div class="flex flex-wrap gap-2">
                <button wire:click="switchTab('active')" 
                        class="px-4 py-2 rounded-lg shadow font-medium text-sm flex items-center gap-2 transition-all {{ $currentTab === 'active' ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    <span class="text-lg">⚠️</span> Butuh Follow Up ({{ $activeCount }})
                </button>
                <button wire:click="switchTab('cancelled')" 
                        class="px-4 py-2 rounded-lg shadow font-medium text-sm flex items-center gap-2 transition-all {{ $currentTab === 'cancelled' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    <span class="text-lg">🚫</span> Kolam Cancel
                </button>
                <button wire:click="switchTab('history')" 
                        class="px-4 py-2 rounded-lg shadow font-medium text-sm flex items-center gap-2 transition-all {{ $currentTab === 'history' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    <span class="text-lg">📜</span> Riwayat Resolusi
                </button>
                <a href="{{ route('complaints.index') }}" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 rounded-lg shadow font-medium text-sm flex items-center gap-2">
                    <span class="text-lg">📢</span> Data Komplain
                </a>
            </div>

            @if($currentTab === 'active')
                <div class="flex flex-wrap items-center gap-3">
                    <button wire:click="$set('delay_filter', '{{ $delay_filter === 'stuck_3_days' ? '' : 'stuck_3_days' }}')" 
                            class="px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition-all border flex items-center gap-2 {{ $delay_filter === 'stuck_3_days' ? 'bg-rose-600 text-white border-rose-600 shadow-xl shadow-rose-200 hover:bg-rose-700' : 'bg-white text-gray-500 hover:bg-gray-50 border-gray-200 shadow-sm' }}">
                        <span>⚠️</span> Tertahan > 3 Hari
                    </button>
                    <button wire:click="$set('est_filter', '{{ $est_filter === 'est_3_days' ? '' : 'est_3_days' }}')" 
                            class="px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition-all border flex items-center gap-2 {{ $est_filter === 'est_3_days' ? 'bg-amber-500 text-white border-amber-500 shadow-xl shadow-amber-200 hover:bg-amber-600' : 'bg-white text-gray-500 hover:bg-gray-50 border-gray-200 shadow-sm' }}">
                        <span>🚨</span> Est. Selesai ≤ 3 Hari / Overdue
                    </button>
                    @if($delay_filter || $est_filter)
                        <button wire:click="$set('delay_filter', ''); $set('est_filter', '');" 
                                class="px-5 py-3 rounded-2xl text-xs font-black uppercase tracking-wider text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition-all">
                            ❌ Clear Filter
                        </button>
                    @endif
                </div>
            @endif

            {{-- Search & Filter Form (Image 2 Standard) --}}
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

                            <button wire:click="$refresh" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-sm shadow-md transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                Filter
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Row 2: Advanced Filters --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-3 pt-4 border-t border-gray-50">
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

                    @if($currentTab === 'active')
                    <div class="relative">
                        <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 absolute -top-2.5 left-2 bg-white px-1 z-10">Handler CX</label>
                        <select wire:model.live="handler_id" class="w-full border-gray-200 rounded-xl text-xs font-bold text-gray-600 focus:ring-teal-500 py-2.5 bg-gray-50/30">
                            <option value="">Semua Handler</option>
                            @foreach(\App\Models\User::where('access_rights', 'LIKE', '%"cx"%')->get() as $h)
                                <option value="{{ $h->id }}">👤 {{ $h->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative">
                        <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 absolute -top-2.5 left-2 bg-white px-1 z-10">Status Terakhir</label>
                        <select wire:model.live="last_status" class="w-full border-gray-200 rounded-xl text-xs font-bold text-gray-600 focus:ring-teal-500 py-2.5 bg-gray-50/30">
                            <option value="">Semua Status</option>
                            <option value="QC_REJECT">🚩 QC Reject</option>
                            <option value="PRODUCTION">🔨 Production</option>
                            <option value="SORTIR">🔍 Sortir</option>
                            <option value="PREPARATION">🔧 Preparation</option>
                        </select>
                    </div>

                    <div class="relative">
                        <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 absolute -top-2.5 left-2 bg-white px-1 z-10">Sumber Kendala</label>
                        <select wire:model.live="source" class="w-full border-gray-200 rounded-xl text-xs font-bold text-gray-600 focus:ring-teal-500 py-2.5 bg-gray-50/30">
                            <option value="">Semua Sumber</option>
                            <option value="GUDANG">📦 Gudang</option>
                            <option value="WS">🔨 WS (Workshop)</option>
                            <option value="MANUAL">📝 Manual</option>
                        </select>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Table Content --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
            <div class="p-6">
                @if(session()->has('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-[10px] text-gray-400 uppercase bg-gray-50/50 border-b">
                            <tr>
                                <th class="px-6 py-4">Order Info</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4 w-1/3">Detail Kendala (Issue)</th>
                                <th class="px-6 py-4 text-center">Status Pengiriman</th>
                                <th class="px-6 py-4 text-center">Resolusi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($data as $item)
                                @php
                                    $order = $currentTab === 'active' || $currentTab === 'cancelled' ? $item : $item->workOrder;
                                    $openIssue = $currentTab === 'active' ? $item->cxIssues->where('status', 'OPEN')->first() : ($currentTab === 'history' ? $item : $item->cxIssues()->latest()->first());
                                    $reporter = $openIssue ? ($openIssue->reporter->name ?? 'Gudang/Admin') : 'Gudang/Admin';
                                @endphp
                                <tr class="hover:bg-gray-50 transition-all duration-300 bg-white">
                                    {{-- Info Order --}}
                                    <td class="px-6 py-4 align-top">
                                        @if($currentTab === 'history')
                                            <div class="text-[10px] font-black text-green-600 uppercase leading-none mb-1 text-center">Diselesaikan Pada</div>
                                            <div class="font-black text-gray-900 leading-tight text-center bg-green-50 rounded-lg py-2 border border-green-100 mb-3">
                                                {{ $item->resolved_at?->translatedFormat('d M Y') }}
                                                <div class="text-[9px] text-gray-400">{{ $item->resolved_at?->format('H:i') }}</div>
                                            </div>
                                        @else
                                            <div class="text-[10px] font-black text-gray-400 uppercase leading-none mb-1">Estimasi Selesai</div>
                                            @php
                                                $estimation = $order->new_estimation_date ?: $order->estimation_date;
                                            @endphp
                                            <div class="font-bold text-gray-900 leading-tight">
                                                {{ $estimation ? $estimation->format('d M Y') : 'Belum ditentukan' }}
                                            </div>
                                        @endif
                                        
                                        @if($openIssue && $currentTab !== 'history')
                                            <div class="mt-2 pt-2 border-t border-gray-50">
                                                <div class="text-[10px] font-black text-teal-600 uppercase leading-none mb-1">Masuk Divisi CX</div>
                                                <div class="text-[11px] font-bold text-gray-700">{{ $openIssue->created_at->translatedFormat('d M Y H:i') }}</div>
                                            </div>
                                        @endif

                                        <div class="font-mono bg-amber-50 text-amber-700 px-2 py-1 rounded inline-block mt-2 text-xs font-bold border border-amber-100">
                                            {{ $order->spk_number }}
                                        </div>
                                        
                                        <div class="mt-2">
                                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold border border-gray-200">
                                                Pre: {{ str_replace('_', ' ', $order->previous_status?->value ?? 'QC REJECT') }}
                                            </span>
                                        </div>

                                        @if($currentTab === 'active' && $openIssue)
                                            @php
                                                $isDelayStuck = $openIssue->created_at->diffInDays(now()) >= 3;
                                                $estDate = $order->new_estimation_date ?: $order->estimation_date;
                                                $isEstNear = $estDate && $estDate->isBefore(now()->addDays(3));
                                            @endphp
                                            
                                            @if($isDelayStuck)
                                                <div class="mt-2">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[9px] font-black uppercase tracking-wider bg-red-100 text-red-700 border border-red-200 animate-pulse shadow-sm">
                                                        ⚠️ Tertahan >3 Hari
                                                    </span>
                                                </div>
                                            @endif
                                            
                                            @if($isEstNear)
                                                <div class="mt-1.5">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[9px] font-black uppercase tracking-wider bg-amber-100 text-amber-700 border border-amber-200 shadow-sm">
                                                        🚨 Est. Selesai Dekat
                                                    </span>
                                                </div>
                                            @endif
                                        @endif
                                    </td>

                                    {{-- Customer --}}
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-bold text-gray-900 text-base leading-tight">{{ $order->customer_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                                        <div class="text-xs font-medium text-gray-700 mt-1">{{ $order->shoe_brand }}</div>

                                        <div class="flex items-center gap-1.5 mt-3 bg-gray-50 p-1.5 rounded-lg border border-gray-100 w-fit">
                                            <div class="w-5 h-5 rounded-full bg-teal-100 flex items-center justify-center text-[8px] text-teal-600 font-bold border border-teal-200">
                                                {{ $order->cxHandler ? substr($order->cxHandler->name, 0, 1) : '?' }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[9px] text-gray-400 leading-none">Handler CX</span>
                                                <span class="text-[10px] font-bold text-gray-700">{{ $order->cxHandler->name ?? 'Unassigned' }}</span>
                                            </div>
                                        </div>

                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', $order->customer_phone) }}" target="_blank" 
                                           class="inline-flex items-center gap-1.5 text-[10px] bg-green-100 text-green-700 px-3 py-1.5 rounded-lg mt-2 font-bold hover:bg-green-200 transition-colors">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                            Chat WA
                                        </a>
                                    </td>

                                    {{-- Issue Details --}}
                                    <td class="px-6 py-4 align-top">
                                        @if($openIssue)
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center gap-2">
                                                    @php
                                                        $srcLabel = match($openIssue->source) {
                                                            'GUDANG' => '📦 GUDANG',
                                                            'MANUAL' => '📝 MANUAL',
                                                            default => (str_starts_with($openIssue->source, 'WORKSHOP') ? '🔨 WORKSHOP' : $openIssue->source),
                                                        };
                                                        $srcColor = str_starts_with($openIssue->source, 'WORKSHOP') ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-red-100 text-red-700 border-red-200';
                                                    @endphp
                                                    <span class="text-[9px] uppercase font-black tracking-widest px-1.5 py-0.5 rounded border {{ $srcColor }}">{{ $srcLabel }}</span>
                                                    <span class="text-[9px] uppercase font-bold text-gray-400">PELAPOR: {{ $reporter }}</span>
                                                </div>
                                                
                                                @if($currentTab === 'active')
                                                    <button type="button" 
                                                            wire:click="openEditModal({{ $openIssue->id }})"
                                                            class="text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-200 px-2 py-1 rounded hover:bg-blue-100 flex items-center gap-1 transition-all">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        Edit
                                                    </button>
                                                @endif
                                            </div>

                                            <div class="mb-2">
                                                <span class="text-[9px] font-black uppercase text-teal-600 border border-teal-200 px-1.5 py-0.5 rounded">{{ $openIssue->category ?? 'TEKNIS' }}</span>
                                            </div>

                                            <div class="space-y-2">
                                                {{-- Card Kendala --}}
                                                @php
                                                    $kendalaText = $openIssue->kendala ?: ($openIssue->kendala_1 ?: ($openIssue->kendala_2 ?: $openIssue->description));
                                                @endphp
                                                @if($kendalaText)
                                                    <div class="bg-white border-l-4 border-l-red-500 rounded-lg shadow-sm p-3 border border-gray-100">
                                                        <div class="text-[9px] font-black text-red-600 uppercase tracking-widest flex items-center gap-1 mb-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                            Detail Kendala
                                                        </div>
                                                        <div class="text-[11px] font-semibold text-gray-800 leading-relaxed">
                                                            {!! nl2br(e($kendalaText)) !!}
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Card Solusi --}}
                                                @php
                                                    $solusiText = $openIssue->opsi_solusi ?: ($openIssue->opsi_solusi_1 ?: $openIssue->opsi_solusi_2);
                                                @endphp
                                                @if($solusiText)
                                                    <div class="bg-white border-l-4 border-l-amber-500 rounded-lg shadow-sm p-3 border border-gray-100">
                                                        <div class="text-[9px] font-black text-amber-600 uppercase tracking-widest flex items-center gap-1 mb-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                            Opsi Solusi
                                                        </div>
                                                        <div class="text-[11px] font-semibold text-gray-700 leading-relaxed">
                                                            {!! nl2br(e($solusiText)) !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            @if(count($openIssue->photo_urls) > 0)
                                                <div class="flex gap-2 mt-3 overflow-x-auto pb-1">
                                                    @foreach($openIssue->photo_urls as $url)
                                                        <a href="{{ route('cx-issues.report', $order->spk_number) }}" target="_blank" class="block hover:scale-110 transition-transform flex-shrink-0">
                                                            <img src="{{ $url }}" class="w-12 h-12 rounded-lg border border-gray-100 object-cover shadow-sm">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @else
                                            <div class="bg-red-50 p-4 rounded-xl border border-red-100 text-xs italic text-gray-600">
                                                "{{ $order->reception_rejection_reason ?? 'Tidak ada keterangan kendala.' }}"
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Shipping Status --}}
                                    <td class="px-6 py-4 align-middle text-center border-l border-r border-gray-50">
                                        @if($openIssue)
                                            <div class="flex flex-col items-center gap-1.5">
                                                <button wire:click="toggleShippingStatus({{ $openIssue->id }})" 
                                                        class="w-11 h-6 rounded-full relative transition-all duration-300 {{ $openIssue->shipping_status === 'SEND' ? 'bg-teal-500' : 'bg-red-400' }}">
                                                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform duration-300 {{ $openIssue->shipping_status === 'SEND' ? 'translate-x-5' : '' }}"></div>
                                                </button>
                                                <div class="flex flex-col items-center">
                                                    <span class="text-[9px] font-black uppercase tracking-tighter {{ $openIssue->shipping_status === 'SEND' ? 'text-teal-600' : 'text-red-500' }}">
                                                        {{ $openIssue->shipping_status === 'SEND' ? 'SEND ✅' : 'HOLD ⛔' }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-300">-</span>
                                        @endif
                                    </td>

                                    {{-- Resolusi Buttons / Info --}}
                                    <td class="px-6 py-4 align-middle">
                                        @if($currentTab === 'active')
                                            <div class="flex flex-col gap-2">
                                                <button wire:click="openActionModal({{ $order->id }}, 'lanjut')" 
                                                        class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-lg text-xs py-2.5 shadow-md flex items-center justify-center gap-2">
                                                    <span>✅</span> Lanjut (Resume)
                                                </button>
                                                <button wire:click="openActionModal({{ $order->id }}, 'tambah_jasa')" 
                                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs py-2.5 shadow-md flex items-center justify-center gap-2">
                                                    <span>➕</span> Tambah Jasa
                                                </button>
                                                <button wire:click="openActionModal({{ $order->id }}, 'komplain')" 
                                                        class="w-full bg-white border border-gray-200 text-gray-700 font-bold rounded-lg text-xs py-2.5 shadow-sm flex items-center justify-center gap-2 hover:bg-gray-50">
                                                    <span class="text-amber-500">⚠️</span> Komplain
                                                </button>
                                                <button wire:click="openActionModal({{ $order->id }}, 'cancel')" 
                                                        class="w-full bg-white border border-red-100 text-red-600 font-bold rounded-lg text-xs py-2.5 shadow-sm flex items-center justify-center gap-2 hover:bg-red-50">
                                                    <span>❌</span> Cancel Order
                                                </button>
                                            </div>
                                        @elseif($currentTab === 'cancelled')
                                            <button wire:click="restoreFromCancel({{ $order->id }})" 
                                                    class="w-full bg-teal-600 text-white text-[10px] font-black uppercase px-4 py-3 rounded-xl shadow-lg hover:bg-teal-700">
                                                ♻️ Restore ke CX
                                            </button>
                                        @else
                                            {{-- Riwayat Resolusi View --}}
                                            <div class="flex flex-col gap-3">
                                                <div class="bg-green-50 border border-green-100 rounded-xl p-3 text-center">
                                                    <div class="text-[9px] font-black text-green-600 uppercase tracking-widest mb-1">Status Akhir</div>
                                                    <div class="text-xs font-black text-green-700 flex items-center justify-center gap-2">
                                                        <span>RESOLVED</span>
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                    </div>
                                                </div>

                                                <div class="space-y-2">
                                                    <div class="flex flex-col">
                                                        <span class="text-[9px] font-black text-gray-400 uppercase leading-none">Diselesaikan Oleh</span>
                                                        <span class="text-[11px] font-bold text-gray-700">{{ $item->resolver->name ?? 'System' }}</span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="text-[9px] font-black text-gray-400 uppercase leading-none">Waktu</span>
                                                        <span class="text-[10px] font-medium text-gray-500">{{ $item->resolved_at?->translatedFormat('d M Y, H:i') }}</span>
                                                    </div>
                                                    
                                                    @if($item->resolution_type)
                                                        <div class="mt-2">
                                                            <span class="text-[9px] font-black uppercase px-2 py-1 rounded bg-gray-100 text-gray-600 border border-gray-200">
                                                                Tipe: {{ str_replace('_', ' ', $item->resolution_type) }}
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @if($item->resolution_notes)
                                                        <div class="mt-2 bg-gray-50 p-2 rounded-lg border border-dotted border-gray-200">
                                                            <div class="text-[9px] font-black text-gray-400 uppercase mb-1">Catatan:</div>
                                                            <div class="text-[10px] text-gray-600 leading-relaxed italic">"{{ $item->resolution_notes }}"</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="p-4 bg-gray-50 rounded-full mb-4">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-4 4m-8-4l4 4m4-4H4"/></svg>
                                            </div>
                                            <p class="text-gray-400 italic">Bagus! Tidak ada data di tab ini.</p>
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
                            Konfirmasi Aksi
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
                            <label class="text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] italic flex items-center gap-3 ml-1">
                                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                Tambah Layanan Baru
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

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                                4. Catatan NB (Tampil di Print SPK)
                                            </label>
                                            <input type="text" wire:model="serviceDetails" placeholder="Misal: Warna hitam, jahit double..." class="w-full bg-gray-900 border-amber-500/20 border-2 rounded-2xl px-6 py-4.5 text-sm font-bold text-white focus:border-amber-500 focus:ring-0 transition-all">
                                        </div>
                                    </div>
                                </div>

                                <button wire:click="addServiceToList" class="w-full bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white py-5 rounded-2xl text-xs font-black shadow-[0_15px_30px_rgba(20,184,166,0.2)] transition-all active:scale-[0.98] uppercase tracking-[0.2em] flex items-center justify-center gap-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                                    Tambahkan ke Daftar
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
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Catatan Resolusi</label>
                        <textarea wire:model="actionNotes" rows="3" class="w-full bg-gray-800/30 border border-gray-800 rounded-2xl p-5 text-white text-sm font-medium focus:ring-teal-500 focus:border-teal-500 transition-all shadow-inner custom-scrollbar" placeholder="Ketikkan instruksi khusus di sini..."></textarea>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-8 py-6 bg-gray-900 border-t border-gray-800 flex justify-end gap-4 items-center">
                    <button wire:click="closeActionModal" wire:loading.attr="disabled" class="px-6 py-3 text-xs font-black text-gray-500 uppercase tracking-widest hover:text-white transition-colors disabled:opacity-50">
                        Batal
                    </button>
                    <button wire:click="processAction" wire:loading.attr="disabled" wire:target="processAction" class="px-10 py-4 bg-teal-600 hover:bg-teal-500 text-white rounded-2xl text-xs font-black shadow-[0_15px_30px_rgba(20,184,166,0.3)] transition-all hover:scale-105 active:scale-95 uppercase tracking-widest disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg wire:loading wire:target="processAction" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading wire:target="processAction">Memproses...</span>
                        <span wire:loading.remove wire:target="processAction">Konfirmasi Aksi</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Edit Issue Modal Component --}}
    <livewire:cx.edit-issue-modal />
</div>
