<div class="procurement-create-root min-h-screen bg-slate-50/60 dark:bg-slate-900 pb-20">
    {{-- Top Header Bar --}}
    <div class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-700/80 px-4 sm:px-8 py-5 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('material-requests.index') }}" wire:navigate 
                   class="w-10 h-10 rounded-2xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 flex items-center justify-center text-slate-500 dark:text-slate-300 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Buat Pengajuan Belanja Bahan Baku</h1>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Pengadaan bahan baku stok gudang atau kebutuhan khusus SPK</p>
                </div>
            </div>

            {{-- Top Action Buttons --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('material-requests.index') }}" wire:navigate 
                   class="px-5 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-2xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-all">
                    Batal
                </a>
                <button type="button" 
                        wire:click="submit" 
                        wire:loading.attr="disabled" 
                        class="px-6 py-2.5 bg-[#FFC232] hover:bg-amber-400 text-slate-950 text-xs font-black rounded-2xl shadow-lg shadow-amber-500/20 hover:shadow-xl transition-all duration-200 active:scale-95 flex items-center gap-2 uppercase tracking-wider">
                    <span wire:loading.remove>Kirim Pengajuan Ke Finlog</span>
                    <span wire:loading class="w-4 h-4 border-2 border-slate-950/30 border-t-slate-950 rounded-full animate-spin"></span>
                    <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Main Form Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-8 pt-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Left Column: Request Type & Pending SPKs (4 Cols) --}}
            <div class="lg:col-span-4 space-y-6">
                {{-- Card: Jenis Pengajuan --}}
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-sm space-y-6">
                    <div>
                        <label class="block text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">1. Jenis Pengajuan Belanja</label>
                        <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 dark:bg-slate-700/60 rounded-2xl border border-slate-200 dark:border-slate-600">
                            <button type="button" 
                                    wire:click="$set('type', 'SHOPPING')" 
                                    class="py-3 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all {{ $type === 'SHOPPING' ? 'bg-white dark:bg-slate-800 text-[#22AF85] shadow-sm font-black' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                                🛒 Belanja Umum
                            </button>
                            <button type="button" 
                                    wire:click="$set('type', 'PRODUCTION_PO')" 
                                    class="py-3 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all {{ $type === 'PRODUCTION_PO' ? 'bg-white dark:bg-slate-800 text-[#22AF85] shadow-sm font-black' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                                👟 PO Spesifik SPK
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-2">
                            {{ $type === 'SHOPPING' ? 'Sifatnya umum untuk isi ulang stok gudang/rutin.' : 'Pengajuan bahan baku khusus untuk 1 atau beberapa SPK sepatu.' }}
                        </p>
                    </div>

                    {{-- Checklist SPK (Shown when PRODUCTION_PO) --}}
                    @if($type === 'PRODUCTION_PO')
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/80 space-y-3">
                            <label class="block text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">2. Antrean SPK Butuh Belanja</label>
                            
                            <div class="space-y-2.5 max-h-[380px] overflow-y-auto pr-1">
                                @forelse($pendingOrders as $order)
                                    <div wire:click="toggleSpk({{ $order->id }})" 
                                         class="group flex items-center justify-between p-3.5 rounded-2xl border transition-all cursor-pointer {{ in_array($order->id, $selectedSpks) ? 'bg-[#22AF85]/10 border-[#22AF85] dark:border-[#22AF85]' : 'bg-slate-50 dark:bg-slate-700/40 border-slate-200 dark:border-slate-700 hover:border-slate-300' }}">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-5 h-5 rounded-lg border-2 flex items-center justify-center transition-all flex-shrink-0 {{ in_array($order->id, $selectedSpks) ? 'bg-[#22AF85] border-[#22AF85]' : 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600' }}">
                                                @if(in_array($order->id, $selectedSpks))
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                                @endif
                                            </div>
                                            <div class="flex flex-col min-w-0">
                                                <span class="text-xs font-black font-mono {{ in_array($order->id, $selectedSpks) ? 'text-[#22AF85]' : 'text-slate-900 dark:text-white' }}">SPK #{{ $order->spk_number }}</span>
                                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 truncate">{{ $order->customer_name }}</span>
                                                <span class="text-[10px] font-semibold text-[#22AF85] mt-0.5 truncate">
                                                    Butuh: {{ $order->materials->pluck('name')->join(', ') }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="text-[9px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-white dark:bg-slate-800 px-2 py-0.5 rounded-lg border border-slate-200 dark:border-slate-700 whitespace-nowrap ml-2">
                                            {{ $order->materials->count() }} Item
                                        </span>
                                    </div>
                                @empty
                                    <div class="p-6 text-center bg-slate-50 dark:bg-slate-700/30 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                                        <p class="text-xs font-bold text-slate-400">Tidak ada antrean SPK butuh belanja saat ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    {{-- Catatan Tambahan --}}
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700/80">
                        <label class="block text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">3. Catatan Tambahan &amp; Alasan Belanja</label>
                        <textarea wire:model="notes" rows="3" placeholder="Tuliskan instruksi khusus atau catatan pengiriman ke Finlog..." 
                                  class="w-full p-4 border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 rounded-2xl text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#22AF85]/30 focus:border-[#22AF85] outline-none transition-all resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- Right Column: Material Search & List (8 Cols) --}}
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm overflow-hidden min-h-[480px] flex flex-col">
                    {{-- Search Header --}}
                    <div class="p-6 border-b border-slate-100 dark:border-slate-700/80">
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <h3 class="text-base font-black text-slate-900 dark:text-white">Daftar Material Yang Diajukan</h3>
                            <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/50 px-3 py-1 rounded-xl border border-emerald-200 dark:border-emerald-800">
                                {{ count($selectedItems) }} Jenis Material Terpilih
                            </span>
                        </div>

                        {{-- Material Search Input --}}
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" 
                                   wire:model.live.debounce.300ms="searchMaterial" 
                                   placeholder="Cari nama material (contoh: Lem, Tali, Insole, Sol Rubber)..." 
                                   class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-2xl text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-[#22AF85]/30 focus:border-[#22AF85] outline-none transition-all">
                            
                            {{-- Dropdown Results --}}
                            @if(!empty($materialResults))
                                <div class="absolute left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 z-50 overflow-hidden">
                                    @foreach($materialResults as $material)
                                        <button type="button" 
                                                wire:click="addItem({{ $material['id'] }})" 
                                                class="w-full flex items-center justify-between px-6 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-700/60 transition-colors border-b border-slate-100 dark:border-slate-700/60 last:border-none text-left">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 bg-emerald-50 dark:bg-emerald-950/50 rounded-xl flex items-center justify-center text-[#22AF85]">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                </div>
                                                <div>
                                                    <span class="text-xs font-black text-slate-900 dark:text-white block">{{ $material['name'] }}</span>
                                                    <span class="text-[10px] font-bold text-slate-400">Kategori: {{ $material['type'] }} • Stok: {{ $material['stock'] }} {{ $material['unit'] }}</span>
                                                </div>
                                            </div>
                                            <span class="text-xs font-black text-[#22AF85]">Rp {{ number_format($material['price'], 0, ',', '.') }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Items Table --}}
                    <div class="flex-1">
                        @if(empty($selectedItems))
                            <div class="h-full flex flex-col items-center justify-center p-12 text-center text-slate-400">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700/50 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <h4 class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">Belum Ada Material Terpilih</h4>
                                <p class="text-[11px] font-medium text-slate-400 mt-1">Gunakan kolom pencarian di atas atau pilih antrean SPK untuk menambahkan material.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left table-auto">
                                    <thead>
                                        <tr class="bg-slate-50/80 dark:bg-slate-700/50 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-700">
                                            <th class="px-6 py-3.5">Nama Material</th>
                                            <th class="px-4 py-3.5 text-center">Jumlah (Qty)</th>
                                            <th class="px-4 py-3.5 text-right">Harga Satuan</th>
                                            <th class="px-6 py-3.5 text-right">Subtotal</th>
                                            <th class="px-4 py-3.5 text-center w-12">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs font-bold text-slate-800 dark:text-slate-200">
                                        @foreach($selectedItems as $index => $item)
                                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/40 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="font-black text-slate-900 dark:text-white">{{ $item['name'] }}</div>
                                                    @if(!empty($item['orders']))
                                                        <div class="text-[10px] font-semibold text-[#22AF85] mt-0.5">SPK: {{ implode(', ', $item['orders']) }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <div class="inline-flex items-center bg-slate-100 dark:bg-slate-700 rounded-xl p-1 border border-slate-200 dark:border-slate-600">
                                                        <button type="button" 
                                                                wire:click="$set('selectedItems.{{ $index }}.quantity', {{ max(1, $item['quantity'] - 1) }})" 
                                                                class="w-7 h-7 rounded-lg bg-white dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 font-black shadow-sm hover:bg-slate-50">
                                                            -
                                                        </button>
                                                        <input type="number" 
                                                               wire:model.live="selectedItems.{{ $index }}.quantity" 
                                                               class="w-12 text-center border-none bg-transparent text-xs font-black text-slate-900 dark:text-white p-0 focus:ring-0">
                                                        <button type="button" 
                                                                wire:click="$set('selectedItems.{{ $index }}.quantity', {{ $item['quantity'] + 1 }})" 
                                                                class="w-7 h-7 rounded-lg bg-white dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 font-black shadow-sm hover:bg-slate-50">
                                                            +
                                                        </button>
                                                    </div>
                                                    <span class="ml-1 text-[10px] text-slate-400 font-semibold">{{ $item['unit'] }}</span>
                                                </td>
                                                <td class="px-4 py-4 text-right font-mono text-slate-500 dark:text-slate-400">
                                                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                </td>
                                                <td class="px-6 py-4 text-right font-mono font-black text-slate-900 dark:text-white">
                                                    Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <button type="button" 
                                                            wire:click="removeItem({{ $index }})" 
                                                            class="p-1.5 text-slate-400 hover:text-rose-600 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Total Footer --}}
                            @php
                                $totalCost = collect($selectedItems)->sum(fn($i) => $i['price'] * $i['quantity']);
                            @endphp
                            <div class="p-6 bg-slate-50 dark:bg-slate-750 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-xs font-semibold text-slate-500">
                                    Total {{ count($selectedItems) }} material akan dikirimkan dalam 1 Nota Pengajuan Belanja ke Finlog.
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-black text-[#22AF85] uppercase tracking-wider">Total Estimasi:</span>
                                    <span class="text-xl font-black text-slate-900 dark:text-white font-mono">Rp {{ number_format($totalCost, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800;900&display=swap');
    
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    [x-cloak] { display: none !important; }
    
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
