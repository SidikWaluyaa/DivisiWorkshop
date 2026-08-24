<div class="procurement-show-root min-h-screen bg-slate-50/60 dark:bg-slate-900 pb-16">
    {{-- Dynamic Notifications --}}
    <div x-data="{ show: false, message: '', type: 'success' }" 
         x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
         x-show="show" x-transition 
         class="fixed top-24 right-8 z-50 pointer-events-none">
        <div :class="type === 'success' ? 'bg-[#22AF85]' : (type === 'error' ? 'bg-rose-600' : 'bg-blue-600')" 
             class="px-6 py-4 rounded-2xl shadow-2xl text-white font-black text-sm flex items-center gap-3">
             <template x-if="type === 'success'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></template>
             <span x-text="message"></span>
        </div>
    </div>

    {{-- Detail Header Bar --}}
    <div class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-700/80 px-4 sm:px-8 py-5 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('material-requests.index') }}" wire:navigate 
                   class="w-10 h-10 rounded-2xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 flex items-center justify-center text-slate-500 dark:text-slate-300 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight font-mono">{{ $materialRequest->request_number }}</h1>
                        @php
                            $statusColor = match($materialRequest->status) {
                                'PENDING' => 'bg-amber-500',
                                'APPROVED', 'PURCHASED' => 'bg-blue-600',
                                'RECEIVED' => 'bg-[#22AF85]',
                                'REJECTED', 'CANCELLED' => 'bg-rose-500',
                                default => 'bg-slate-400'
                            };
                            $statusLabel = match($materialRequest->status) {
                                'PENDING' => 'Menunggu Approval Finlog',
                                'APPROVED', 'PURCHASED' => 'Dalam Pengiriman (Finlog)',
                                'RECEIVED' => 'Bahan Baku Tiba di Workshop',
                                'REJECTED' => 'Ditolak',
                                'CANCELLED' => 'Dibatalkan',
                                default => $materialRequest->status
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full {{ $statusColor }} text-white text-[10px] font-black uppercase tracking-wider shadow-sm">
                            {{ $statusLabel }}
                        </span>
                        @if($materialRequest->finlog_request_id)
                            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 text-[10px] font-black uppercase tracking-wider border border-indigo-200 dark:border-indigo-800">
                                Ref Finlog: {{ $materialRequest->finlog_request_id }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-0.5">
                        {{ $materialRequest->type == 'SHOPPING' ? 'Belanja Umum / Gudang' : 'PO Spesifik SPK' }} • Dibuat: {{ $materialRequest->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>

            {{-- Action Buttons Header --}}
            <div class="flex flex-wrap items-center gap-2">
                {{-- Tombol JSON Finlog --}}
                <a href="{{ route('material-requests.json', $materialRequest->id) }}" target="_blank"
                   class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl transition-all flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    <span>Data JSON</span>
                </a>

                {{-- Tombol Cetak Nota --}}
                <a href="{{ route('material-requests.print', $materialRequest->id) }}" target="_blank"
                   class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-black rounded-xl transition-all flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Nota</span>
                </a>

                @if(in_array($materialRequest->status, ['PURCHASED', 'RECEIVED', 'APPROVED']))
                    <button type="button" 
                            wire:click="verifyAndReceiveMaterial" 
                            wire:loading.attr="disabled" 
                            class="px-5 py-2.5 bg-[#22AF85] hover:bg-emerald-600 text-white text-xs font-black rounded-xl shadow-md shadow-emerald-500/20 transition-all flex items-center gap-1.5 active:scale-95">
                        <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span wire:loading class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <span>Terima &amp; Verifikasi Material</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-8 pt-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Left Column: Information Card & Items Table (8 Cols) --}}
            <div class="lg:col-span-8 space-y-8">
                {{-- Information Card --}}
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 sm:p-8">
                    <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight mb-6">Informasi Pengajuan Belanja</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-xs">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Pemohon (Requested By)</span>
                            <span class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ $materialRequest->requestedBy->name ?? 'Sistem Workshop' }}</span>
                            <span class="text-[10px] text-slate-400 font-medium">{{ $materialRequest->requestedBy->email ?? '-' }}</span>
                        </div>

                        <div>
                            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Status Audit Finlog</span>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-2.5 h-2.5 rounded-full {{ $statusColor }}"></div>
                                <span class="font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">{{ $statusLabel }}</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-wider block">Estimasi Total Nilai</span>
                            <span class="text-xl font-black text-[#22AF85] font-mono block mt-0.5">Rp {{ number_format($materialRequest->total_estimated_cost, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-xs mt-6 pt-6 border-t border-slate-100 dark:border-slate-700/80">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Konteks SPK</span>
                            @if($materialRequest->work_order_id)
                                <span class="font-bold text-slate-900 dark:text-white block mt-0.5">SPK #{{ $materialRequest->workOrder->spk_number }}</span>
                                <span class="text-[10px] text-slate-400 font-medium">{{ $materialRequest->workOrder->customer_name }}</span>
                            @else
                                <span class="font-bold text-[#22AF85] block mt-0.5">{{ $materialRequest->items->pluck('work_order_id')->unique()->filter()->count() }} SPK (Pengajuan Gabungan)</span>
                            @endif
                        </div>

                        <div>
                            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Disetujui Pada</span>
                            <span class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ $materialRequest->approved_at ? $materialRequest->approved_at->format('d M Y • H:i') : '-' }}</span>
                            @if($materialRequest->approved_by)
                                <span class="text-[10px] text-[#22AF85] font-bold">Oleh: {{ $materialRequest->approvedBy->name }}</span>
                            @endif
                        </div>

                        <div>
                            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Jenis Pengadaan</span>
                            <span class="font-bold text-slate-900 dark:text-white uppercase tracking-wider block mt-0.5">{{ $materialRequest->type }}</span>
                        </div>
                    </div>

                    @if($materialRequest->notes)
                        <div class="mt-6 p-4 bg-slate-50 dark:bg-slate-750 rounded-2xl border border-slate-200/80 dark:border-slate-700 flex gap-3 items-start text-xs">
                            <svg class="w-5 h-5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            <p class="text-slate-600 dark:text-slate-300 font-medium italic leading-relaxed">"{{ $materialRequest->notes }}"</p>
                        </div>
                    @endif
                </div>

                {{-- Items Table --}}
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-750">
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Rincian Material Yang Membutuhkan Pengadaan</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left table-auto">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-700/50 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-700">
                                    <th class="px-6 py-3.5">Nama Material &amp; Spesifikasi</th>
                                    <th class="px-4 py-3.5 text-center">Jumlah (Qty)</th>
                                    <th class="px-4 py-3.5 text-right">Harga Satuan</th>
                                    <th class="px-6 py-3.5 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs font-bold text-slate-800 dark:text-slate-200">
                                @foreach($materialRequest->items as $item)
                                    @php
                                        $wo = $item->workOrder ?? $materialRequest->workOrder;
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/40 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-black text-slate-900 dark:text-white">{{ $item->material_name }}</div>
                                            @if($item->specification)
                                                <div class="text-[10px] text-slate-400 font-semibold mt-0.5">Spesifikasi: {{ $item->specification }}</div>
                                            @endif
                                            @if($wo)
                                                <div class="text-[10px] font-semibold text-[#22AF85] mt-0.5">Untuk SPK #{{ $wo->spk_number }} ({{ $wo->customer_name }})</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center font-mono font-black text-slate-900 dark:text-white">
                                            {{ $item->quantity }} {{ $item->unit ?? 'pcs' }}
                                        </td>
                                        <td class="px-4 py-4 text-right font-mono text-slate-500 dark:text-slate-400">
                                            Rp {{ number_format($item->estimated_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-mono font-black text-slate-900 dark:text-white">
                                            Rp {{ number_format($item->estimated_price * $item->quantity, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right Column (History / Timeline) --}}
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 sticky top-32">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Riwayat Aktivitas</h3>
                        <div class="w-2 h-2 rounded-full bg-[#22AF85] animate-pulse"></div>
                    </div>

                    <div class="space-y-8 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                        {{-- Log Entry Template --}}
                        @if($materialRequest->workOrder && $materialRequest->workOrder->logs)
                             @foreach($materialRequest->workOrder->logs->whereIn('action', ['APPROVED', 'REJECTED', 'PURCHASED', 'CANCELLED'])->sortByDesc('created_at') as $log)
                                <div class="relative pl-8 group">
                                    {{-- Connector Line --}}
                                    @if(!$loop->last)
                                        <div class="absolute left-[3.5px] top-4 bottom-[-32px] w-[1px] bg-gray-100"></div>
                                    @endif
                                    
                                    {{-- Dot --}}
                                    <div class="absolute left-0 top-1.5 w-2 h-2 rounded-full border-2 border-white ring-4 ring-gray-50 bg-gray-300 group-hover:ring-[#22AF85]/10 group-hover:bg-[#22AF85] transition-all"></div>
                                    
                                    <div class="flex flex-col">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1.5">{{ $log->created_at->format('d M, H:i') }}</div>
                                        <div class="text-xs font-black text-gray-800 uppercase tracking-tight">{{ $log->action }}</div>
                                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $log->description }}</p>
                                        <div class="text-[9px] font-bold text-gray-400 mt-2 flex items-center gap-1">
                                            <div class="w-4 h-4 rounded-full bg-gray-100 flex items-center justify-center text-[7px]">{{ substr($log->user->name ?? 'S', 0, 1) }}</div>
                                            {{ $log->user->name ?? 'System' }}
                                        </div>
                                    </div>
                                </div>
                             @endforeach
                        @else
                            <div class="text-center py-10 opacity-40">
                                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs font-bold uppercase tracking-widest">No matching history</p>
                            </div>
                        @endif

                        {{-- Final Creation Log --}}
                        <div class="relative pl-8 group">
                            <div class="absolute left-0 top-1.5 w-2 h-2 rounded-full border-2 border-white ring-4 ring-gray-50 bg-[#22AF85]"></div>
                            <div class="flex flex-col">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1.5">{{ $material_activity_date ?? $materialRequest->created_at->format('d M, H:i') }}</div>
                                <div class="text-xs font-black text-gray-800 uppercase tracking-tight">CONCEIVED</div>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">Request created by {{ $materialRequest->requestedBy->name ?? 'System' }}.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
