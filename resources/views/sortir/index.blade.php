<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    {{ __('Sortir & Material Station') }}
                </h2>
                <div class="text-xs font-medium opacity-90">
                    {{ \Carbon\Carbon::now()->format('l, d F Y') }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50" id="sortir-container" x-data="{ 
        selectedItems: [],
        toggleGroup(ids) {
            // Convert IDs to strings for consistent comparison
            ids = ids.map(String);
            const allSelected = ids.every(id => this.selectedItems.includes(id));
            
            if (allSelected) {
                // Unselect all in this group
                this.selectedItems = this.selectedItems.filter(id => !ids.includes(id));
            } else {
                // Select all in this group
                ids.forEach(id => {
                    if (!this.selectedItems.includes(id)) this.selectedItems.push(id);
                });
            }
        },
        isGroupSelected(ids) {
            if (ids.length === 0) return false;
            ids = ids.map(String);
            return ids.every(id => this.selectedItems.includes(id));
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ activeTab: 'ready' }">
            {{-- Tab Headers --}}
            <div class="flex items-center gap-1 mb-6 bg-gray-200/50 p-1 rounded-2xl w-fit mx-auto shadow-inner">
                <button @click="activeTab = 'ready'" 
                        :class="activeTab === 'ready' ? 'bg-white text-teal-700 shadow-md' : 'text-gray-500 hover:text-gray-700'"
                        class="px-8 py-3 rounded-xl text-sm font-black uppercase tracking-widest transition-all flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Siap Produksi
                    <span class="bg-teal-100 text-teal-700 px-2 py-0.5 rounded-md text-[10px]">{{ $readyOrders->total() }}</span>
                </button>
                <button @click="activeTab = 'waiting'" 
                        :class="activeTab === 'waiting' ? 'bg-white text-orange-700 shadow-md' : 'text-gray-500 hover:text-gray-700'"
                        class="px-8 py-3 rounded-xl text-sm font-black uppercase tracking-widest transition-all flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Waiting List
                    <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded-md text-[10px]">{{ $waitingOrders->total() }}</span>
                </button>
            </div>

            <div class="dashboard-card overflow-hidden">
                <div class="dashboard-card-header flex flex-col md:flex-row justify-between md:items-center gap-3">
                    <h3 class="dashboard-card-title flex items-center gap-2">
                        <template x-if="activeTab === 'ready'">
                            <span>✅ List Pesanan Siap Produksi</span>
                        </template>
                        <template x-if="activeTab === 'waiting'">
                            <span>⏳ List Menunggu Material (Waiting List)</span>
                        </template>
                    </h3>
                    <div class="flex flex-wrap items-center gap-2">
                         {{-- Search Form --}}
                         <form method="GET" action="{{ route('sortir.index') }}" class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Cari SPK / Customer..." 
                                   class="pl-9 pr-4 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 shadow-sm w-48 transition-all focus:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="dashboard-card-body p-0">
                    
                    {{-- SIAP PRODUKSI TAB --}}
                    <div x-show="activeTab === 'ready'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 text-[10px] font-black text-gray-500 uppercase tracking-wider text-left">
                                    <tr>
                                        <th class="px-6 py-3 text-center w-12">
                                            <input type="checkbox" 
                                                   @click="toggleGroup({{ $readyOrders->pluck('id') }})"
                                                   :checked="isGroupSelected({{ $readyOrders->pluck('id') }})"
                                                   class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        </th>
                                        <th class="px-6 py-3">SPK & Status</th>
                                        <th class="px-6 py-3">Customer & Shoe</th>
                                        <th class="px-6 py-3">Layanan</th>
                                        <th class="px-6 py-3">Material Check</th>
                                        <th class="px-6 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($readyOrders as $order)
                                    <tr class="hover:bg-teal-50/30 transition-colors" :class="selectedItems.includes('{{ $order->id }}') ? 'bg-teal-50' : ''">
                                        <td class="px-6 py-4 text-center">
                                            <input type="checkbox" value="{{ $order->id }}" x-model="selectedItems" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-mono font-bold text-gray-800 text-sm mb-1">{{ $order->spk_number }}</div>
                                            @include('sortir.partials.priority-badge', ['priority' => $order->priority])
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900 leading-none">{{ $order->customer_name }}</div>
                                            <div class="text-[10px] text-gray-500 mt-1 uppercase font-medium">{{ $order->shoe_brand }} - {{ $order->shoe_color }} ({{ $order->shoe_size }})</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($order->services->take(3) as $s)
                                                    <span class="px-2 py-0.5 rounded text-[9px] bg-gray-100 text-gray-600 border border-gray-200 font-bold uppercase">
                                                        {{ $s->name === 'Custom Service' && $s->pivot->custom_name ? $s->pivot->custom_name : $s->name }}
                                                    </span>
                                                @endforeach
                                                @if($order->services->count() > 3)
                                                    <span class="text-[9px] text-gray-400 font-bold">+{{ $order->services->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php 
                                                $materials = $order->materials;
                                                $totalMat = $materials->count();
                                                $allocatedMat = $materials->where('pivot.status', 'ALLOCATED')->count();
                                                $isReady = ($totalMat === 0) || ($allocatedMat === $totalMat);
                                            @endphp
                                            <div class="flex flex-col gap-1.5 min-w-[140px]">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex-grow h-1 bg-gray-100 rounded-full overflow-hidden">
                                                        <div class="h-full {{ $isReady ? 'bg-[#22AF85]' : 'bg-[#FFC232]' }} transition-all duration-500" 
                                                             style="width: {{ $totalMat > 0 ? ($allocatedMat / $totalMat * 100) : 100 }}%"></div>
                                                    </div>
                                                    <span class="text-[9px] font-black {{ $isReady ? 'text-[#22AF85]' : 'text-[#FFC232]' }} uppercase tracking-widest">
                                                        {{ $isReady ? 'READY' : 'PARTIAL' }}
                                                    </span>
                                                </div>
                                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">
                                                    {{ $allocatedMat }}/{{ $totalMat ?: 0 }} Material Tersedia
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex justify-end gap-2.5">
                                                {{-- Check Detail Button --}}
                                                <a href="{{ route('sortir.show', $order->id) }}" 
                                                   title="Cek Material"
                                                   class="w-10 h-10 flex items-center justify-center bg-white text-[#22AF85] border-2 border-gray-100 rounded-xl hover:border-[#22AF85] hover:bg-teal-50 transition-all shadow-sm group">
                                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>
                                                
                                                {{-- Bypass Button --}}
                                                <form action="{{ route('sortir.skip-production', $order->id) }}" method="POST" onsubmit="return confirm('Bypass Sortir (Legacy Data)? SPK akan langsung masuk Production.')">
                                                    @csrf
                                                    <button type="submit" 
                                                            title="Bypass to Production"
                                                            class="w-10 h-10 flex items-center justify-center bg-[#6366f1] text-white rounded-xl hover:bg-[#4f46e5] transition-all shadow-lg shadow-indigo-100 active:scale-95">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center opacity-30">
                                                <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                                <p class="text-sm font-bold uppercase tracking-widest italic">Tidak ada antrian siap produksi</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($readyOrders->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                            {{ $readyOrders->links() }}
                        </div>
                        @endif
                    </div>

                    {{-- WAITING LIST TAB --}}
                    <div x-show="activeTab === 'waiting'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 text-[10px] font-black text-gray-500 uppercase tracking-wider text-left">
                                    <tr>
                                        <th class="px-6 py-3 text-center w-12 text-orange-200 italic">--</th>
                                        <th class="px-6 py-3">SPK & Status</th>
                                        <th class="px-6 py-3">Customer & Shoe</th>
                                        <th class="px-6 py-3">Layanan</th>
                                        <th class="px-6 py-3">Kendala Stok</th>
                                        <th class="px-6 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($waitingOrders as $order)
                                    <tr class="hover:bg-orange-50/30 transition-colors">
                                        <td class="px-6 py-4 text-center">
                                            <div class="w-2 h-2 rounded-full bg-orange-400 mx-auto animate-pulse"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-mono font-bold text-gray-800 text-sm mb-1 opacity-70">{{ $order->spk_number }}</div>
                                            @include('sortir.partials.priority-badge', ['priority' => $order->priority])
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900 leading-none">{{ $order->customer_name }}</div>
                                            <div class="text-[10px] text-gray-500 mt-1 uppercase font-medium">{{ $order->shoe_brand }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($order->services->take(2) as $s)
                                                    <span class="px-2 py-0.5 rounded text-[9px] bg-gray-100 text-gray-400 border border-gray-200 font-bold uppercase">
                                                        {{ $s->name === 'Custom Service' && $s->pivot->custom_name ? $s->pivot->custom_name : $s->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php 
                                                $materials = $order->materials;
                                                $totalMat = $materials->count();
                                                $allocatedMat = $materials->where('pivot.status', 'ALLOCATED')->count();
                                            @endphp
                                            <div class="flex flex-col gap-1.5 min-w-[140px]">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex-grow h-1 bg-gray-100 rounded-full overflow-hidden">
                                                        <div class="h-full bg-[#FFC232] transition-all duration-500" 
                                                             style="width: {{ $totalMat > 0 ? ($allocatedMat / $totalMat * 100) : 0 }}%"></div>
                                                    </div>
                                                    <span class="text-[9px] font-black text-[#FFC232] uppercase tracking-widest">
                                                        MISSING
                                                    </span>
                                                </div>
                                                <div class="text-[9px] text-red-400 font-bold uppercase tracking-tight">
                                                    {{ $totalMat - $allocatedMat }} Material Belum Siap
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex justify-end gap-2.5">
                                                {{-- Check Detail Button --}}
                                                <a href="{{ route('sortir.show', $order->id) }}" 
                                                   title="Update Stok"
                                                   class="w-10 h-10 flex items-center justify-center bg-white text-[#FFC232] border-2 border-gray-100 rounded-xl hover:border-[#FFC232] hover:bg-orange-50 transition-all shadow-sm group">
                                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                
                                                {{-- Bypass Button --}}
                                                <form action="{{ route('sortir.skip-production', $order->id) }}" method="POST" onsubmit="return confirm('Bypass Sortir (Legacy Data)? SPK akan langsung masuk Production.')">
                                                    @csrf
                                                    <button type="submit" 
                                                            title="Bypass to Production"
                                                            class="w-10 h-10 flex items-center justify-center bg-[#6366f1] text-white rounded-xl hover:bg-[#4f46e5] transition-all shadow-lg shadow-indigo-100 active:scale-95">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center opacity-30">
                                                <svg class="w-16 h-16 mb-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <p class="text-sm font-bold uppercase tracking-widest italic text-teal-600">Alhamdulillah, stok aman semua!</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                         @if($waitingOrders->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                            {{ $waitingOrders->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div> </div>
                </div>
            </div>
        </div>

    {{-- Floating Bulk Action Bar --}}
    <div x-show="selectedItems.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0 scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100 scale-100"
         x-transition:leave-end="translate-y-full opacity-0 scale-95"
         class="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4"
         style="display: none;">
        
        <div class="bg-white/90 backdrop-blur-md border border-gray-200 shadow-2xl rounded-2xl p-4 w-full max-w-2xl flex items-center justify-between gap-4 ring-1 ring-black/5">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-teal-100 px-3 py-1.5 rounded-lg text-teal-700">
                    <span class="text-xs font-bold uppercase tracking-wider">Terpilih</span>
                    <span class="bg-teal-600 text-white px-2 py-0.5 rounded-md font-bold text-sm" x-text="selectedItems.length"></span>
                </div>
                <button @click="selectedItems = []" class="text-xs font-bold text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                    Batal
                </button>
            </div>

            <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

            <button type="button" 
                    onclick="bulkSkipToProduction()" 
                    class="bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:shadow-indigo-200 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                To Production (Massal)
            </button>
        </div> {{-- max-w-7xl --}}
    </div> {{-- py-12 --}}

    {{-- REPORT ISSUE MODAL --}}
    <x-report-modal />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openReportModal(id) {
            window.dispatchEvent(new CustomEvent('open-report-modal', { detail: id }));
        }

        function bulkFinishSortir() {
            let ids = [];
            try {
                ids = Alpine.$data(document.getElementById('sortir-container')).selectedItems;
            } catch (e) {
                console.error('Alpine selection failed (bulkFinishSortir):', e);
                const checked = document.querySelectorAll('input[type=checkbox][value]:checked');
                ids = Array.from(checked)
                    .filter(cb => cb.value && cb.value !== 'on' && !isNaN(cb.value))
                    .map(cb => cb.value);
            }

            if (ids.length === 0) {
                Swal.fire('Peringatan', 'Pilih item terlebih dahulu.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Bulk Finish',
                text: `Selesaikan proses sortir untuk ${ids.length} item? Item akan dipindah ke tahap Preparation.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                confirmButtonText: 'Ya, Selesaikan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('sortir.bulk-update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ids: ids, action: 'finish' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                    });
                }
            });
        }

        function bulkSkipToProduction() {
            let ids = [];
            try {
                ids = Alpine.$data(document.getElementById('sortir-container')).selectedItems;
            } catch (e) {
                console.error('Alpine selection failed (bulkSkipToProduction):', e);
                const checked = document.querySelectorAll('input[type=checkbox][value]:checked');
                ids = Array.from(checked)
                    .filter(cb => cb.value && cb.value !== 'on' && !isNaN(cb.value))
                    .map(cb => cb.value);
            }

            if (ids.length === 0) {
                Swal.fire('Peringatan', 'Pilih item terlebih dahulu.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Bulk Direct to Production',
                text: `Langsung kirim ${ids.length} item ke Production (Skip Material Check)?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                confirmButtonText: 'Ya, Kirim ke Production!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('sortir.bulk-skip-production') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ids: ids })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                    });
                }
            });
        }
    </script>
</x-app-layout>
