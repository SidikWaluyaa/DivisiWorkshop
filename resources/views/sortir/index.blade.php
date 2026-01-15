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

    <div class="py-12 bg-gray-50/50" x-data="{ 
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="dashboard-card overflow-hidden">
                <div class="dashboard-card-header flex flex-col md:flex-row justify-between md:items-center gap-3">
                    <h3 class="dashboard-card-title">
                        📋 Validasi Material & Distribusi
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

                        <span class="px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-xs font-bold shadow-sm">
                            Total: {{ $prioritas->count() + $reguler->total() }}
                        </span>
                    </div>
                </div>

                <div class="dashboard-card-body p-0 space-y-8">
                    
                    {{-- PRIORITAS TABLE --}}
                    @if($prioritas->isNotEmpty())
                    <div class="border-b-4 border-red-500 shadow-md transform scale-[1.01]">
                        <div class="bg-red-50 px-6 py-3 border-b border-red-100 flex justify-between items-center">
                            <h4 class="text-sm font-black text-red-700 uppercase tracking-widest flex items-center gap-2">
                                <span class="text-lg">🔥</span> Antrian Prioritas
                            </h4>
                            <span class="bg-red-200 text-red-800 px-2 py-0.5 rounded text-xs font-bold">{{ $prioritas->count() }} Items</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-red-700 uppercase bg-red-50/50 border-b border-red-100">
                                    <tr>
                                        <th class="px-4 py-4 text-center w-16">
                                            <input type="checkbox" 
                                                   @click="toggleGroup({{ $prioritas->pluck('id') }})"
                                                   :checked="isGroupSelected({{ $prioritas->pluck('id') }})"
                                                   class="rounded border-red-300 text-red-600 focus:ring-red-500">
                                        </th>
                                        <th class="px-6 py-4 font-black">SPK</th>
                                        <th class="px-6 py-4 font-black text-center">Status</th>
                                        <th class="px-6 py-4 font-black">Customer</th>
                                        <th class="px-6 py-4 font-black">Services</th>
                                        <th class="px-6 py-4 font-black text-center">Material</th>
                                        <th class="px-6 py-4 font-black text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-red-100 bg-white">
                                    @foreach($prioritas as $order)
                                    <tr class="hover:bg-red-50/30 transition-colors" :class="{ 'bg-red-50': selectedItems.includes('{{ $order->id }}') }">
                                        <td class="px-4 py-4 text-center">
                                            <input type="checkbox" value="{{ $order->id }}" x-model="selectedItems"
                                                   class="rounded border-red-300 text-red-600 focus:ring-red-500">
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-mono font-black text-red-700 bg-red-100 px-2 py-1 rounded border border-red-200 shadow-sm">
                                                {{ $order->spk_number }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-red-600 text-white shadow-lg animate-pulse">
                                                PRIORITAS
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-gray-800">
                                            {{ $order->customer_name }}
                                            <div class="text-[10px] text-gray-400 font-normal uppercase mt-0.5">{{ $order->shoe_brand }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($order->services as $s)
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] bg-red-50 text-red-600 font-bold border border-red-100">{{ $s->name }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php $hasPending = $order->materials->where('pivot.status', 'REQUESTED')->count() > 0; @endphp
                                            @if($order->materials->isEmpty())
                                                <span class="text-xs text-gray-400 italic">Pending Check</span>
                                            @elseif($hasPending)
                                                <span class="text-xs font-bold text-red-600">⚠ BELANJA</span>
                                            @else
                                                <span class="text-xs font-bold text-green-600">✅ READY</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('sortir.show', $order->id) }}" class="inline-block px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded shadow hover:shadow-lg transition-all">
                                                CHECK
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    {{-- REGULER TABLE --}}
                    <div>
                        <div class="px-6 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                            <h4 class="text-sm font-bold text-gray-600 uppercase tracking-widest">
                                Antrian Reguler
                            </h4>
                            <span class="bg-gray-200 text-gray-600 px-2 py-0.5 rounded text-xs font-bold">{{ $reguler->total() }} Items</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-4 text-center w-16">
                                            <input type="checkbox" 
                                                   @click="toggleGroup({{ $reguler->pluck('id') }})"
                                                   :checked="isGroupSelected({{ $reguler->pluck('id') }})"
                                                   class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        </th>
                                        <th class="px-6 py-4 font-bold text-teal-800">SPK</th>
                                        {{-- Prioritas column removed for Reguler table as per request implied logic, or keep specific for non-priority? --}}
                                        {{-- User asked to create "Specific Table Reguler". No need to show Prioritas column since this IS the regular table --}}
                                        {{-- But maybe there are other tags? "Normal"? --}}
                                        <th class="px-6 py-4 font-bold text-teal-800">Customer</th>
                                        <th class="px-6 py-4 font-bold text-teal-800">Services</th>
                                        <th class="px-6 py-4 font-bold text-teal-800 text-center">Material</th>
                                        <th class="px-6 py-4 font-bold text-teal-800 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse($reguler as $order)
                                    <tr class="hover:bg-teal-50/30 transition-colors" :class="{ 'bg-teal-50/50': selectedItems.includes('{{ $order->id }}') }">
                                        <td class="px-4 py-4 text-center">
                                            <input type="checkbox" value="{{ $order->id }}" x-model="selectedItems"
                                                   class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-mono font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded border border-teal-100">
                                                {{ $order->spk_number }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800">{{ $order->customer_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->shoe_brand }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($order->services as $s)
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] bg-gray-100 text-gray-600 border border-gray-200">{{ $s->name }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php $hasPending = $order->materials->where('pivot.status', 'REQUESTED')->count() > 0; @endphp
                                            @if($order->materials->isEmpty())
                                                <span class="text-xs text-gray-400 italic">Pending</span>
                                            @elseif($hasPending)
                                                <span class="text-xs font-bold text-red-600">BUTUH BELANJA</span>
                                            @else
                                                <span class="text-xs font-bold text-green-600">READY</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('sortir.show', $order->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-teal-500 text-teal-600 hover:bg-teal-50 text-xs font-bold uppercase rounded-lg transition-all">
                                                Check
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 bg-gray-50/20">
                                            Antrian Reguler Kosong
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                         @if($reguler->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                            {{ $reguler->links() }}
                        </div>
                        @endif
                    </div>
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
                    onclick="bulkFinishSortir()" 
                    class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:shadow-teal-200 transition-all flex items-center gap-2 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Selesaikan Sortir (Massal)
            </button>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function bulkFinishSortir() {
            let ids = [];
            try {
                ids = Alpine.$data(document.querySelector('[x-data]')).selectedItems;
            } catch (e) {
                // Fallback for older Alpine or if Alpine not global
                const checked = document.querySelectorAll('input[name="selected_orders[]"]:checked');
                ids = Array.from(checked).map(p => p.value);
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
                cancelButtonColor: '#d33',
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
                            Swal.fire('Berhasil!', data.message, 'success').then(() => {
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
