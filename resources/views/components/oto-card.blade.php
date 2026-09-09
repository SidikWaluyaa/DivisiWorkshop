@props([
    'oto',
    'techs',
    'loopIteration' => 1,
    'showCheckbox' => true,
    'isCompletedTab' => false,
])

@php
    $order = $oto->workOrder;

    // Detect required stations from proposed_services (supports comma-separated multiple services)
    $servicesList = array_filter(array_map('trim', explode(',', (string) ($oto->proposed_services ?? ''))));
    $hasSol = false;
    $hasUpper = false;
    $hasTreatment = false;

    foreach ($servicesList as $srvName) {
        $stationCode = \App\Helpers\ProductionStationHelper::getStationCode($srvName);
        if ($stationCode === 'SOLING') $hasSol = true;
        elseif ($stationCode === 'UPPER') $hasUpper = true;
        elseif ($stationCode === 'TREATMENT') $hasTreatment = true;
    }

    // Secondary inspection from attached OTO work order services if proposed_services string was empty
    if (!$hasSol && !$hasUpper && !$hasTreatment && $order && $order->relationLoaded('workOrderServices')) {
        foreach ($order->workOrderServices as $wos) {
            if (str_starts_with($wos->custom_service_name ?? '', 'OTO:')) {
                $stationCode = \App\Helpers\ProductionStationHelper::getStationCode($wos->custom_service_name ?? $wos->category_name ?? '');
                if ($stationCode === 'SOLING') $hasSol = true;
                elseif ($stationCode === 'UPPER') $hasUpper = true;
                elseif ($stationCode === 'TREATMENT') $hasTreatment = true;
            }
        }
    }

    // Default fallback if absolutely no services could be parsed
    if (!$hasSol && !$hasUpper && !$hasTreatment) {
        $hasUpper = true;
    }

    $allStationsFinished = true;
    if ($hasSol && !$oto->oto_sol_completed_at) $allStationsFinished = false;
    if ($hasUpper && !$oto->oto_upper_completed_at) $allStationsFinished = false;
    if ($hasTreatment && !$oto->oto_treatment_completed_at) $allStationsFinished = false;
@endphp

<tbody x-data="{ expanded: false }" class="divide-y divide-gray-100 dark:divide-gray-800 border-b border-gray-150 dark:border-gray-800 hover:bg-amber-50/20 dark:hover:bg-amber-950/10 transition-colors">
    <tr class="transition-colors cursor-pointer group" @click="expanded = !expanded">
        
        {{-- Column 1: Checkbox & Number --}}
        <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-400 dark:text-gray-500 w-20" @click.stop>
            <div class="flex items-center gap-2">
                @if($showCheckbox && !$isCompletedTab)
                    <input type="checkbox" 
                           value="{{ $oto->id }}" 
                           wire:model.live="selectedItems"
                           class="w-4 h-4 text-amber-600 rounded border-gray-300 focus:ring-amber-500 cursor-pointer">
                @endif
                <span>{{ $loopIteration }}</span>
            </div>
        </td>

        {{-- Column 2: SPK & Fast Track Badge --}}
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-1.5">
                    <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 font-black text-xs rounded-lg border border-amber-200 dark:border-amber-800 tracking-wide font-mono">
                        {{ $oto->spk_number ?? ($order->spk_number ?? '-') }}
                    </span>
                    <span class="px-1.5 py-0.5 bg-rose-500 text-white font-black text-[9px] uppercase tracking-widest rounded shadow-xs animate-pulse">
                        🔥 OTO
                    </span>
                </div>
                <div class="text-[10px] text-gray-400 font-semibold flex items-center gap-1">
                    <span>📅 {{ $oto->created_at ? $oto->created_at->format('d M Y H:i') : '-' }}</span>
                </div>
            </div>
        </td>

        {{-- Column 3: Pelanggan & Sepatu --}}
        <td class="px-6 py-4">
            <div class="flex flex-col">
                <span class="font-bold text-gray-900 dark:text-white text-xs">
                    {{ $oto->customer_name ?? ($order->customer_name ?? ($order->customer->name ?? '-')) }}
                </span>
                <span class="text-[11px] text-amber-600 dark:text-amber-400 font-bold">
                    👟 {{ $order->shoe_brand ?? '-' }} ({{ $order->shoe_color ?? '-' }})
                </span>
                @if($oto->customer_phone || ($order->customer_phone ?? false))
                    <span class="text-[10px] text-gray-400 font-mono">
                        📞 {{ $oto->customer_phone ?? $order->customer_phone }}
                    </span>
                @endif
            </div>
        </td>

        {{-- Column 4: Layanan OTO & Nilai Tagihan --}}
        <td class="px-6 py-4">
            <div class="flex flex-col gap-1 min-w-[180px]">
                <div class="flex items-center gap-1 flex-wrap">
                    <span class="px-2 py-0.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-black text-[10px] rounded-md shadow-xs">
                        {{ $oto->proposed_services }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="font-black text-emerald-600 dark:text-emerald-400">
                        {{ $oto->total_oto_price }}
                    </span>
                    @if($oto->discount_percent > 0)
                        <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 text-[9px] font-black rounded">
                            Diskon {{ round($oto->discount_percent) }}%
                        </span>
                    @endif
                </div>
            </div>
        </td>

        {{-- Column 5: Multi-Station Progress & Penugasan Teknisi --}}
        <td class="px-6 py-4" @click.stop>
            @if($isCompletedTab)
                <div class="flex flex-col gap-1 min-w-[200px]">
                    <div class="flex items-center gap-1.5">
                        <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-bold text-xs rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center gap-1">
                            <span>✅ Selesai Dikerjakan</span>
                        </span>
                    </div>
                    <div class="text-[10px] text-gray-500 dark:text-gray-400 flex flex-wrap gap-x-2 pt-0.5">
                        @if($hasSol && $oto->otoSolBy) <span>Sol: <b>{{ $oto->otoSolBy->name }}</b></span> @endif
                        @if($hasUpper && $oto->otoUpperBy) <span>Upper: <b>{{ $oto->otoUpperBy->name }}</b></span> @endif
                        @if($hasTreatment && $oto->otoTreatmentBy) <span>Treatment: <b>{{ $oto->otoTreatmentBy->name }}</b></span> @endif
                    </div>
                    @if($oto->completed_at)
                        <span class="text-[10px] text-gray-400">🕒 {{ $oto->completed_at->format('d M Y H:i') }}</span>
                    @endif
                </div>
            @else
                <div class="flex flex-col gap-1.5 min-w-[220px]">
                    {{-- 1. Soling --}}
                    @if($hasSol)
                        <div class="flex items-center justify-between gap-2 text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                            <span class="font-black text-orange-600 dark:text-orange-400 uppercase text-[10px] tracking-wide">Soling:</span>
                            <select wire:change="updateStationTechnician({{ $oto->id }}, 'sol', $event.target.value)"
                                    class="text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-0 rounded-lg px-2 py-1 focus:ring-2 focus:ring-amber-500 cursor-pointer min-w-[130px]">
                                <option value="">-- Pilih Teknisi --</option>
                                @foreach($techs['sol'] ?? [] as $t)
                                    <option value="{{ $t->id }}" {{ $oto->oto_sol_by == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- 2. Upper --}}
                    @if($hasUpper)
                        <div class="flex items-center justify-between gap-2 text-[11px] border-b border-gray-100 dark:border-gray-800 pb-1">
                            <span class="font-black text-purple-600 dark:text-purple-400 uppercase text-[10px] tracking-wide">Upper:</span>
                            <select wire:change="updateStationTechnician({{ $oto->id }}, 'upper', $event.target.value)"
                                    class="text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-0 rounded-lg px-2 py-1 focus:ring-2 focus:ring-amber-500 cursor-pointer min-w-[130px]">
                                <option value="">-- Pilih Teknisi --</option>
                                @foreach($techs['upper'] ?? [] as $t)
                                    <option value="{{ $t->id }}" {{ $oto->oto_upper_by == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- 3. Treatment / Cleaning / Repaint --}}
                    @if($hasTreatment)
                        <div class="flex items-center justify-between gap-2 text-[11px] pb-1">
                            <span class="font-black text-teal-600 dark:text-teal-400 uppercase text-[10px] tracking-wide">Treatment:</span>
                            <select wire:change="updateStationTechnician({{ $oto->id }}, 'treatment', $event.target.value)"
                                    class="text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-0 rounded-lg px-2 py-1 focus:ring-2 focus:ring-amber-500 cursor-pointer min-w-[130px]">
                                <option value="">-- Pilih Teknisi --</option>
                                @foreach($techs['treatment'] ?? [] as $t)
                                    <option value="{{ $t->id }}" {{ $oto->oto_treatment_by == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            @endif
        </td>

        {{-- Column 6: Aksi & Selesaikan OTO --}}
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" @click.stop>
            <div class="flex items-center justify-end gap-2">
                @if(!$isCompletedTab)
                    <button wire:click="completeOto({{ $oto->id }})" 
                            wire:confirm="Selesaikan seluruh pengerjaan OTO untuk SPK #{{ $oto->spk_number }}? Sepatu akan siap diambil pelanggan di Gudang."
                            class="px-3.5 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-500/20 transition-all active:scale-95 flex items-center gap-1.5 cursor-pointer"
                            title="Selesaikan Paket OTO & Pindahkan ke Rak Selesai Gudang">
                        <span>✨</span>
                        <span>Selesaikan OTO</span>
                    </button>
                @endif

                <button @click="expanded = !expanded" class="p-1.5 rounded-lg hover:bg-amber-100/50 dark:hover:bg-amber-950/40 text-amber-700 dark:text-amber-400 transition-colors">
                    <svg :class="{'rotate-180': expanded}" class="w-5 h-5 transform transition-transform duration-250" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </td>
    </tr>

    {{-- Collapsible Detail Row --}}
    <tr x-show="expanded" x-cloak x-transition>
        <td colspan="6" class="bg-amber-50/40 dark:bg-gray-850 p-6 border-t border-b border-amber-200/50 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Detail Info Card --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs">
                    <h4 class="text-xs font-black text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <span>📋</span> Rincian Paket OTO
                    </h4>
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-500">Layanan:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ $oto->proposed_services }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-500">Harga Normal:</span>
                            <span class="line-through text-gray-400 font-semibold">{{ $oto->total_normal_price }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-500">Harga Promo OTO:</span>
                            <span class="font-bold text-emerald-600">{{ $oto->total_oto_price }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-500">Hemat Diskon:</span>
                            <span class="font-bold text-emerald-600">{{ $oto->total_discount }} ({{ round($oto->discount_percent) }}%)</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-gray-500">Estimasi Tambahan HK:</span>
                            <span class="font-bold text-amber-600">+{{ $oto->estimated_days ?? 0 }} Hari Kerja</span>
                        </div>
                    </div>
                </div>

                {{-- Status & Timeline Card --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs">
                    <h4 class="text-xs font-black text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <span>⏳</span> Status & Penanganan CX
                    </h4>
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-500">Dibuat Oleh:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ $oto->creator->name ?? 'Workshop Staff' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-500">Ditangani CX:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ $oto->cxAssigned->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-500">Metode Kontak:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ $oto->cx_contact_method ?? 'WHATSAPP' }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-gray-500">Lokasi Fisik Sepatu:</span>
                            <span class="font-bold text-purple-600 dark:text-purple-400">{{ $order->current_location ?? 'Stasiun OTO' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Link & Photos Card --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs flex flex-col justify-between">
                    <div>
                        <h4 class="text-xs font-black text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <span>🔗</span> Aksi Cepat SPK
                        </h4>
                        <p class="text-xs text-gray-500 mb-3">
                            Status SPK Induk: <span class="font-bold text-emerald-600">{{ $order->status?->value ?? 'SELESAI' }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($order)
                            <a href="{{ url('/track?spk=' . $order->spk_number) }}" target="_blank" 
                               class="flex-1 text-center py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl transition-all">
                                🔍 Lacak SPK
                            </a>
                        @endif
                        <a href="{{ route('cx.oto.index', ['search' => $oto->spk_number]) }}" 
                           class="flex-1 text-center py-2 px-3 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-xs rounded-xl transition-all">
                            🎯 Buka di CX
                        </a>
                    </div>
                </div>

            </div>
        </td>
    </tr>
</tbody>
