<div class="outbound-create-root min-h-screen bg-[#FDFDFD] pb-16">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        
        :root {
            --primary-green: #22AF85;
            --accent-yellow: #FFC232;
        }

        .outbound-create-root {
            font-family: 'Inter', sans-serif;
        }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f9fafb; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: var(--primary-green); }
    </style>

    {{-- Dynamic Notifications --}}
    <div x-data="{ show: false, message: '', type: 'success' }" 
         x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3500)"
         x-show="show" x-transition 
         class="fixed top-24 right-8 z-50 pointer-events-none">
        <div :class="type === 'success' ? 'bg-[#22AF85]' : (type === 'error' ? 'bg-rose-600' : 'bg-blue-600')" 
             class="px-6 py-4 rounded-2xl shadow-2xl text-white font-black text-sm flex items-center gap-3">
             <template x-if="type === 'success'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></template>
             <span x-text="message"></span>
        </div>
    </div>

    {{-- Detail Header --}}
    <div class="bg-white border-b border-gray-100 px-8 py-6 flex items-center justify-between sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-6">
            <a href="{{ route('qc.outbound') }}" wire:navigate class="p-2.5 bg-gray-50 border border-gray-200 text-gray-400 hover:text-[#22AF85] rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-lg bg-[#22AF85] text-white text-[10px] font-black uppercase tracking-widest">
                        Outbound QC
                    </span>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">Buat Manifest Outbound Baru</h1>
                </div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">
                    Pilih SPK Lolos QC Akhir & Terbitkan Surat Jalan Pengiriman ke Gudang Utama
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('qc.outbound') }}" wire:navigate class="px-5 py-3 bg-gray-100 text-gray-600 text-sm font-black rounded-xl hover:bg-gray-200 transition-all">
                Batal
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-8 pt-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Left Column: Selection Table (8 cols) --}}
            <div class="lg:col-span-8 space-y-6">
                
                {{-- Filter & Search Header --}}
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="relative w-full sm:w-80">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="Cari SPK / Customer / Brand..." 
                               class="w-full text-xs font-medium border border-gray-200 rounded-2xl pl-10 pr-4 py-3 bg-gray-50/50 text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#22AF85] focus:bg-white transition-all">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <select wire:model.live="priority" class="text-xs font-bold border border-gray-200 rounded-2xl px-4 py-3 bg-gray-50/50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#22AF85] cursor-pointer">
                            <option value="all">Semua Prioritas</option>
                            <option value="urgent">Urgent / OTO / Express</option>
                            <option value="regular">Regular</option>
                        </select>

                        <label class="flex items-center gap-2 text-xs font-bold text-gray-600 bg-gray-50 border border-gray-200 px-4 py-3 rounded-2xl cursor-pointer hover:bg-gray-100 transition-all whitespace-nowrap">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded text-[#22AF85] focus:ring-[#22AF85] w-4 h-4">
                            <span>Pilih Semua ({{ $this->stagingOrders->count() }})</span>
                        </label>
                    </div>
                </div>

                {{-- Table Card --}}
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/40">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#22AF85] animate-pulse"></span>
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Daftar SPK Siap Outbound ({{ $this->stagingOrders->count() }})</h3>
                        </div>
                        <span class="text-xs font-black text-[#22AF85] bg-emerald-50 px-3 py-1 rounded-xl">
                            {{ count($selectedItems) }} SPK Dipilih
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-white border-b border-gray-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center w-12">Select</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Nomor SPK</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Pelanggan &amp; Sepatu</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Prioritas</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Layanan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($this->stagingOrders as $order)
                                    @php
                                        $isSelected = in_array((string)$order->id, $selectedItems);
                                        $isUrgent = in_array($order->priority, ['Prioritas', 'Urgent', 'Express', 'OTO']);
                                    @endphp
                                    <tr class="hover:bg-emerald-50/30 transition-colors {{ $isSelected ? 'bg-emerald-50/20' : '' }}">
                                        <td class="px-6 py-5 text-center">
                                            <input type="checkbox" 
                                                   wire:model.live="selectedItems" 
                                                   value="{{ $order->id }}" 
                                                   class="rounded text-[#22AF85] focus:ring-[#22AF85] w-4 h-4 cursor-pointer">
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $order->spk_number }}</span>
                                                <span class="text-[10px] font-medium text-gray-400">{{ $order->created_at?->format('d M Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-gray-800">{{ $order->customer_name ?? $order->customer?->name ?? 'Pelanggan' }}</span>
                                                <span class="text-[11px] text-gray-400 mt-0.5">{{ $order->shoe_brand ?? 'Sepatu' }} {{ $order->shoe_color ? '• '.$order->shoe_color : '' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            @if($isUrgent)
                                                <span class="px-2.5 py-1 rounded-lg bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest shadow-sm">
                                                    {{ $order->priority }}
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest">
                                                    REGULAR
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($order->workOrderServices as $serv)
                                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-md">
                                                        {{ $serv->service?->name ?? $serv->service_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-8 py-16 text-center">
                                            <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto text-2xl mb-3 text-gray-300">📦</div>
                                            <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Tidak ada SPK di Antrean Staging Outbound</p>
                                            <p class="text-xs text-gray-400 mt-1">Seluruh SPK yang lolos QC Akhir telah diterbitkan Surat Jalan Manifest</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right Column: Summary Card & Action (4 cols) --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-8 sticky top-28 space-y-6">
                    <div class="flex items-center justify-between pb-6 border-b border-gray-100">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Ringkasan Manifest</h3>
                        <span class="px-3 py-1 rounded-xl bg-amber-50 text-amber-700 text-xs font-black border border-amber-200">
                            {{ count($selectedItems) }} SPK
                        </span>
                    </div>

                    {{-- Dispatcher Info --}}
                    <div class="space-y-4">
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pengirim (Dispatcher)</span>
                            <span class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</span>
                            <span class="text-xs text-gray-400">Tim QC &amp; Logistik Workshop</span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tujuan Pengiriman</span>
                            <span class="text-sm font-bold text-gray-800">Gudang Utama (Central Logistics)</span>
                        </div>

                        <div class="space-y-2 pt-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Kurir / Ekspedisi</label>
                            <input type="text" 
                                   wire:model="courierName" 
                                   placeholder="Contoh: Kurir Internal / JNE / Driver" 
                                   class="w-full text-xs font-medium border border-gray-200 rounded-2xl px-4 py-3 bg-gray-50/50 text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#22AF85] focus:bg-white transition-all">
                        </div>

                        <div class="space-y-2 pt-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Catatan Manifest (Opsional)</label>
                            <textarea wire:model="manifestNotes" 
                                      rows="3" 
                                      placeholder="Tambahkan catatan khusus pengiriman manifest..." 
                                      class="w-full text-xs font-medium border border-gray-200 rounded-2xl p-4 bg-gray-50/50 text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#22AF85] focus:bg-white transition-all resize-none"></textarea>
                        </div>
                    </div>

                    {{-- Action Button --}}
                    <div class="pt-4 border-t border-gray-100">
                        <button wire:click="generateManifest" 
                                wire:loading.attr="disabled"
                                @if(count($selectedItems) === 0) disabled @endif
                                class="w-full py-4 bg-[#FFC232] text-gray-900 text-sm font-black rounded-2xl hover:shadow-xl hover:shadow-[#FFC232]/20 transition-all flex items-center justify-center gap-2 border-none disabled:opacity-50 disabled:cursor-not-allowed active:scale-95">
                            <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span wire:loading class="w-5 h-5 border-2 border-gray-900/30 border-t-gray-900 rounded-full animate-spin"></span>
                            Terbitkan Surat Jalan Outbound
                        </button>
                        <p class="text-[10px] text-gray-400 text-center mt-3 font-medium">
                            Surat Jalan Outbound otomatis dibuat dengan nomor acuan MNF-OUT-YYYYMMDD-XXXX
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
