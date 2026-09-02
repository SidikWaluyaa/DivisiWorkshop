<div class="py-8 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs">
            <div>
                <div class="flex items-center gap-2 text-xs font-black text-emerald-600 uppercase tracking-widest mb-1">
                    <span>CUSTOMER SERVICE</span>
                    <span>•</span>
                    <span>LOGISTIK INBOUND</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>🚚</span>
                    <span>Monitoring Kiriman SPK Pending</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                    Pantau status pengiriman sepatu pelanggan khusus SPK Pending. Sepatu otomatis keluar dari daftar ini setelah diterima oleh tim Gudang.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('cs.dashboard') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                    <span>← Dashboard CS</span>
                </a>
            </div>
        </div>

        {{-- 3 KPI Metric Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Card 1: All Pending --}}
            <button type="button" 
                    wire:click="setTab('all')" 
                    class="p-5 sm:p-6 rounded-3xl border transition-all text-left relative overflow-hidden group cursor-pointer shadow-xs {{ $activeTab === 'all' ? 'bg-slate-900 text-white border-slate-900 shadow-lg shadow-slate-900/10' : 'bg-white hover:border-slate-300 text-slate-800 border-slate-200' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl {{ $activeTab === 'all' ? 'bg-white/10 text-amber-300' : 'bg-slate-100 text-slate-700' }} flex items-center justify-center font-black text-lg">
                        📦
                    </div>
                    @if($activeTab === 'all')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/20 text-white">Aktif</span>
                    @endif
                </div>
                <div class="text-[11px] font-black uppercase tracking-wider {{ $activeTab === 'all' ? 'text-slate-400' : 'text-slate-500' }}">
                    Total SPK Pending
                </div>
                <div class="text-3xl font-black mt-1">
                    {{ $counts['all'] }} <span class="text-xs font-medium opacity-60">SPK</span>
                </div>
            </button>

            {{-- Card 2: In-Transit (Ada Resi) --}}
            <button type="button" 
                    wire:click="setTab('in_transit')" 
                    class="p-5 sm:p-6 rounded-3xl border transition-all text-left relative overflow-hidden group cursor-pointer shadow-xs {{ $activeTab === 'in_transit' ? 'bg-emerald-700 text-white border-emerald-700 shadow-lg shadow-emerald-700/15' : 'bg-white hover:border-emerald-200 text-slate-800 border-slate-200' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl {{ $activeTab === 'in_transit' ? 'bg-white/10 text-emerald-200' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center font-black text-lg">
                        🚚
                    </div>
                    @if($activeTab === 'in_transit')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/20 text-white">Aktif</span>
                    @endif
                </div>
                <div class="text-[11px] font-black uppercase tracking-wider {{ $activeTab === 'in_transit' ? 'text-emerald-200' : 'text-slate-500' }}">
                    Sedang Dikirim (Ada Resi)
                </div>
                <div class="text-3xl font-black mt-1">
                    {{ $counts['in_transit'] }} <span class="text-xs font-medium opacity-60">Sepatu</span>
                </div>
            </button>

            {{-- Card 3: Waiting (Tanpa Resi) --}}
            <button type="button" 
                    wire:click="setTab('waiting')" 
                    class="p-5 sm:p-6 rounded-3xl border transition-all text-left relative overflow-hidden group cursor-pointer shadow-xs {{ $activeTab === 'waiting' ? 'bg-amber-600 text-white border-amber-600 shadow-lg shadow-amber-600/15' : 'bg-white hover:border-amber-200 text-slate-800 border-slate-200' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl {{ $activeTab === 'waiting' ? 'bg-white/10 text-amber-200' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center font-black text-lg">
                        ⏳
                    </div>
                    @if($activeTab === 'waiting')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/20 text-white">Aktif</span>
                    @endif
                </div>
                <div class="text-[11px] font-black uppercase tracking-wider {{ $activeTab === 'waiting' ? 'text-amber-200' : 'text-slate-500' }}">
                    Belum Dikirim (Menunggu Resi)
                </div>
                <div class="text-3xl font-black mt-1">
                    {{ $counts['waiting'] }} <span class="text-xs font-medium opacity-60">Pelanggan</span>
                </div>
            </button>
        </div>

        {{-- Main Table Card --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            
            {{-- Search & Controls --}}
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/50">
                <div class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari No SPK, Pelanggan, Resi..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-2xs transition-all">
                </div>

                <div class="text-xs font-bold text-slate-500 w-full sm:w-auto text-right">
                    Menampilkan {{ $orders->count() }} dari {{ $orders->total() }} data
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/70 border-b border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-wider">
                            <th class="py-4 px-6 w-12">No</th>
                            <th class="py-4 px-6">Nomor SPK &amp; Waktu</th>
                            <th class="py-4 px-6">Pelanggan &amp; Kontak</th>
                            <th class="py-4 px-6">Sepatu &amp; Layanan</th>
                            <th class="py-4 px-6 text-center">Status Kiriman</th>
                            <th class="py-4 px-6">Nomor Resi</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                        @forelse($orders as $idx => $order)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-4 px-6 text-slate-400 font-bold">
                                    {{ $orders->firstItem() + $idx }}
                                </td>

                                {{-- SPK & Waktu --}}
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-mono font-black text-slate-900 bg-slate-100 px-2.5 py-1 rounded-xl inline-block border border-slate-200">
                                        {{ $order->spk_number }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-medium mt-1">
                                        {{ $order->created_at ? $order->created_at->format('d M Y • H:i') : '-' }}
                                    </div>
                                </td>

                                {{-- Pelanggan & Kontak --}}
                                <td class="py-4 px-6">
                                    <div class="font-black text-slate-900 text-sm">
                                        {{ $order->customer_name }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1.5 mt-0.5">
                                        <span>📞 {{ $order->customer_phone ?: '-' }}</span>
                                    </div>
                                </td>

                                {{-- Sepatu & Layanan --}}
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900">
                                        {{ $order->shoe_brand }} <span class="text-slate-500 font-normal">({{ $order->shoe_type ?: 'Sepatu' }})</span>
                                    </div>
                                    <div class="text-[10px] text-indigo-600 font-bold mt-0.5">
                                        {{ $order->services?->count() ?? 0 }} Layanan Jasa
                                    </div>
                                </td>

                                {{-- Status Kiriman --}}
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    @if($order->customer_tracking_number)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            🚚 Sedang Dikirim
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                            <span>⏳ Menunggu Resi</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Resi & Shipped At --}}
                                <td class="py-4 px-6 whitespace-nowrap">
                                    @if($order->customer_tracking_number)
                                        <div class="flex items-center gap-1.5 font-mono font-black text-slate-900 text-xs bg-slate-50 px-2.5 py-1 rounded-xl border border-slate-200 w-max">
                                            <span>{{ $order->customer_tracking_number }}</span>
                                            <button type="button" 
                                                    onclick="navigator.clipboard.writeText('{{ $order->customer_tracking_number }}'); alert('Nomor resi disalin!');" 
                                                    class="text-slate-400 hover:text-slate-700 cursor-pointer" 
                                                    title="Salin Resi">
                                                📋
                                            </button>
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-medium mt-0.5">
                                            Diinput: {{ $order->customer_shipped_at ? $order->customer_shipped_at->format('d M, H:i') : '-' }}
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs italic font-medium">Belum ada resi</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        @if(!$order->customer_tracking_number && $order->customer_phone)
                                            @php
                                                $waText = urlencode("Halo kak {$order->customer_name}, kami dari Shoe Workshop ingin mengonfirmasi apakah sepatu ({$order->shoe_brand}) untuk SPK #{$order->spk_number} sudah dikirimkan? Jika sudah, boleh minta nomor resinya ya kak agar tim kami bantu pantau penerimaannya. Terima kasih! 😊");
                                                $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $order->customer_phone));
                                            @endphp
                                            <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" 
                                               target="_blank" 
                                               class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl text-xs font-black transition-all border border-emerald-200 flex items-center gap-1" 
                                               title="Follow-up via WhatsApp">
                                                <span>💬 WA</span>
                                            </a>
                                        @endif

                                        <button type="button" 
                                                wire:click="openResiModal({{ $order->id }})" 
                                                class="px-3.5 py-1.5 {{ $order->customer_tracking_number ? 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs' }} rounded-xl text-xs font-black transition-all cursor-pointer">
                                            {{ $order->customer_tracking_number ? '✏️ Edit Resi' : '+ Input Resi' }}
                                        </button>

                                        @if($order->customer_tracking_number)
                                            <button type="button" 
                                                    wire:click="clearResi({{ $order->id }})" 
                                                    wire:confirm="Hapus nomor resi dari SPK ini?" 
                                                    class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all cursor-pointer" 
                                                    title="Hapus Resi">
                                                ✕
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-12 text-center text-slate-400 text-sm font-bold italic">
                                    Tidak ada data SPK Pending yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards (< 768px) --}}
            <div class="md:hidden divide-y divide-slate-100">
                @forelse($orders as $order)
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-black text-xs text-slate-900 bg-slate-100 px-2.5 py-1 rounded-xl border border-slate-200">
                                {{ $order->spk_number }}
                            </span>
                            @if($order->customer_tracking_number)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    🚚 Sedang Dikirim
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                    ⏳ Menunggu Resi
                                </span>
                            @endif
                        </div>

                        <div>
                            <div class="font-black text-slate-900 text-sm">{{ $order->customer_name }}</div>
                            <div class="text-xs text-slate-500">{{ $order->shoe_brand }} • {{ $order->customer_phone ?: '-' }}</div>
                        </div>

                        @if($order->customer_tracking_number)
                            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-1">
                                <div class="text-[10px] font-black text-slate-400 uppercase">Nomor Resi:</div>
                                <div class="font-mono font-black text-slate-900 text-sm flex items-center justify-between">
                                    <span>{{ $order->customer_tracking_number }}</span>
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $order->customer_tracking_number }}'); alert('Resi disalin!');" class="text-slate-500">📋</button>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            @if(!$order->customer_tracking_number && $order->customer_phone)
                                @php
                                    $waText = urlencode("Halo kak {$order->customer_name}, kami dari Shoe Workshop ingin mengonfirmasi apakah sepatu ({$order->shoe_brand}) untuk SPK #{$order->spk_number} sudah dikirimkan? Jika sudah, boleh minta nomor resinya ya kak. Terima kasih! 😊");
                                    $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $order->customer_phone));
                                @endphp
                                <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-black border border-emerald-200">
                                    💬 WA
                                </a>
                            @endif

                            <button type="button" wire:click="openResiModal({{ $order->id }})" class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-xs font-black shadow-xs">
                                {{ $order->customer_tracking_number ? '✏️ Edit Resi' : '+ Input Resi' }}
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs font-bold italic">
                        Tidak ada data SPK Pending.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($orders->hasPages())
                <div class="p-5 bg-slate-50 border-t border-slate-100">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- Modal Input / Edit Resi (Teleported to Body) --}}
    @if($showModal && $selectedWorkOrder)
    <template x-teleport="body">
        <div class="fixed inset-0 z-[999999] overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm font-sans animate-fadeIn">
            <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full overflow-hidden relative z-10">
                {{-- Header --}}
                <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400 font-black text-lg">
                            🚚
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">INPUT RESI PELANGGAN</div>
                            <h3 class="text-base font-black text-white">SPK #{{ $selectedWorkOrder->spk_number }}</h3>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                        ✕
                    </button>
                </div>

                {{-- Body --}}
                <form wire:submit.prevent="saveResi" class="p-6 space-y-4">
                    {{-- Order Summary Info --}}
                    <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-medium">Pelanggan:</span>
                            <span class="font-black text-slate-800">{{ $selectedWorkOrder->customer_name }} ({{ $selectedWorkOrder->customer_phone ?: '-' }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-medium">Sepatu:</span>
                            <span class="font-bold text-slate-800">{{ $selectedWorkOrder->shoe_brand }} ({{ $selectedWorkOrder->shoe_type ?: 'Sepatu' }})</span>
                        </div>
                    </div>

                    {{-- Tracking Number Input --}}
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                            Nomor Resi Pengiriman <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               wire:model="trackingNumber" 
                               placeholder="Contoh: JP1234567890 / SPXID048123..." 
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-bold text-slate-900 uppercase focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50/50 shadow-2xs">
                        @error('trackingNumber')
                            <p class="text-[11px] font-bold text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="text-[11px] text-slate-400 font-medium">
                        💡 <em>Waktu pengiriman (`customer_shipped_at`) akan otomatis dicatat saat Anda menyimpan resi ini.</em>
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeModal" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black transition-all shadow-md shadow-emerald-600/20 active:scale-95 cursor-pointer">
                            💾 Simpan Resi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @endif

</div>
