<div>
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-gray-50 to-red-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-gray-800 tracking-tight">SPK Mendesak</h3>
                            {{-- Info Tooltip --}}
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click.stop="open = !open" class="text-gray-300 hover:text-red-500 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-50 w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-gray-100 left-0 mt-3 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-4 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-red-500 rounded-full"></div>
                                            <div class="text-[10px] font-black text-red-600 uppercase tracking-widest">Maksud</div>
                                        </div>
                                        <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Daftar SPK yang membutuhkan perhatian segera karena sudah terlambat atau mendekati batas waktu pengerjaan.</div>
                                        
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                            <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                        </div>
                                        <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Perhitungan selisih hari ini dengan estimasi tanggal selesai SPK (Order ≤ 5 hari sisa).</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Overdue & at-risk orders (≤5 hari)</p>
                    </div>
                </div>
                @if($urgentOrders && $urgentOrders->count() > 0)
                <span class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-bold animate-pulse">
                    {{ $urgentOrders->count() }} perlu perhatian
                </span>
                @endif
            </div>
        </div>
        <div class="p-5">
            {{-- Search --}}
            <div class="mb-4">
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Cari SPK / Nama Customer..."
                       class="w-full rounded-xl border-gray-200 text-sm focus:border-teal-500 focus:ring-teal-500 px-4 py-2.5 bg-gray-50">
            </div>

            {{-- Table --}}
            @if($urgentOrders && $urgentOrders->count() > 0)
            <div class="space-y-2 max-h-96 overflow-y-auto custom-scrollbar">
                @foreach($urgentOrders as $order)
                @php
                    $daysLeft = $order->days_remaining ?? 999;
                    $isOverdue = $order->is_overdue ?? false;
                    $statusValue = $order->status instanceof \App\Enums\WorkOrderStatus ? $order->status->value : $order->status;
                    $routeName = match($statusValue) {
                        'ASSESSMENT' => 'assessment.create',
                        'PREPARATION' => 'preparation.show',
                        'SORTIR' => 'sortir.show',
                        'QC' => 'qc.show',
                        default => null,
                    };
                @endphp
                <div class="p-4 rounded-xl border-l-4 transition-all hover:shadow-md
                    {{ $isOverdue ? 'bg-gradient-to-r from-red-50 to-orange-50 border-red-500' : 'bg-gradient-to-r from-amber-50 to-yellow-50 border-amber-500' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-gray-800 text-sm">{{ $order->spk_number }}</div>
                            <div class="text-xs text-gray-600 mb-2 truncate">{{ $order->customer_name }}</div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-block px-2 py-1 bg-white rounded text-xs font-bold text-gray-700 shadow-sm">
                                    {{ $order->status instanceof \App\Enums\WorkOrderStatus ? $order->status->label() : $order->status }}
                                </span>
                                @if($order->estimation_date)
                                <span class="text-[10px] text-gray-400 font-medium">
                                    Est: {{ $order->estimation_date->format('d M') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            @if($isOverdue)
                            <span class="px-2.5 py-1 bg-red-600 text-white rounded-lg text-xs font-black shadow animate-pulse">
                                {{ abs($daysLeft) }} hari telat
                            </span>
                            @else
                            <span class="px-2.5 py-1 bg-amber-500 text-white rounded-lg text-xs font-black shadow">
                                {{ $daysLeft }} hari lagi
                            </span>
                            @endif
                            @if($routeName)
                            <a href="{{ route($routeName, $order->id) }}" class="text-xs font-bold text-teal-600 hover:text-teal-700 hover:underline">
                                Lihat →
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <div class="text-5xl mb-4">🎉</div>
                <div class="text-gray-500 font-bold text-sm">Tidak ada SPK mendesak!</div>
                <div class="text-gray-400 text-xs mt-1">Semua berjalan sesuai jadwal</div>
            </div>
            @endif
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: linear-gradient(to bottom, #14b8a6, #f97316); border-radius: 10px; }
    </style>
</div>
