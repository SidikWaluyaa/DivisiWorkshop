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
                    <div class="bg-white rounded-xl border border-red-200 shadow-sm overflow-hidden mb-6">
                        <div class="bg-red-50/50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                            <h4 class="text-sm font-bold text-red-700 flex items-center gap-2">
                                <span class="text-lg">🔥</span> Antrian Prioritas
                            </h4>
                            <span class="bg-white text-red-700 border border-red-200 px-3 py-1 rounded-full text-xs font-bold">{{ $prioritas->count() }} Orders</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider text-left">
                                    <tr>
                                        <th class="px-6 py-3 text-center w-12">
                                            <input type="checkbox" 
                                                   @click="toggleGroup({{ $prioritas->pluck('id') }})"
                                                   :checked="isGroupSelected({{ $prioritas->pluck('id') }})"
                                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        </th>
                                        <th class="px-6 py-3">SPK</th>
                                        <th class="px-6 py-3 text-center">Prioritas</th>
                                        <th class="px-6 py-3">Customer</th>
                                        <th class="px-6 py-3">Layanan</th>
                                        <th class="px-6 py-3 text-center">Status Material</th>
                                        <th class="px-6 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($prioritas as $order)
                                    <tr class="hover:bg-red-50/20 transition-colors" :class="{ 'bg-red-50': selectedItems.includes('{{ $order->id }}') }">
                                        <td class="px-6 py-4 text-center">
                                            <input type="checkbox" value="{{ $order->id }}" x-model="selectedItems"
                                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono font-bold text-gray-700">
                                                {{ $order->spk_number }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            @if($order->priority === 'Express')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-purple-100 text-purple-800">
                                                    EXPRESS
                                                </span>
                                            @elseif($order->priority === 'Urgent')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">
                                                    URGENT
                                                </span>
                                            @else
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-orange-100 text-orange-800">
                                                    PRIORITAS
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $order->customer_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->shoe_brand }}</div>
                                            
                                            @if($order->technician_notes)
                                                <div class="mt-1 text-[10px] text-amber-700 font-bold bg-amber-50 px-2 py-1 rounded-md border border-amber-100 block w-fit">
                                                    ⚠️ {{ $order->technician_notes }}
                                                </div>
                                            @endif
                                            @if($order->notes)
                                                <div class="mt-1 text-[10px] text-blue-700 font-bold bg-blue-50 px-2 py-1 rounded-md border border-blue-100 block w-fit">
                                                    💬 {{ $order->notes }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($order->services as $s)
                                                    <span class="px-2 py-0.5 rounded text-[10px] bg-gray-100 text-gray-600 border border-gray-200">
                                                        {{ $s->name === 'Custom Service' && $s->pivot->custom_name ? $s->pivot->custom_name : $s->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            @php $hasPending = $order->materials->where('pivot.status', 'REQUESTED')->count() > 0; @endphp
                                            @if($order->materials->isEmpty())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Pending Check
                                                </span>
                                            @elseif($hasPending)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Butuh Belanja
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Ready
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex flex-col gap-2 items-end">
                                                <a href="{{ route('sortir.show', $order->id) }}" class="flex items-center justify-center w-full px-2 py-1 bg-teal-50 text-teal-700 border border-teal-200 rounded hover:bg-teal-100 transition-colors font-bold text-[10px] uppercase">
                                                    Check Detail 🔍
                                                </a>
                                                <button type="button" onclick="openReportModal('{{ $order->id }}')" class="flex items-center justify-center w-full px-2 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded hover:bg-amber-100 transition-colors font-bold text-[10px] uppercase">
                                                    Follow Up ⚠️
                                                </button>
                                                <form action="{{ route('sortir.skip-production', $order->id) }}" method="POST" onsubmit="return confirm('Langsung kirim ke Production (Skip Material Check)?')">
                                                    @csrf
                                                    <button type="submit" class="flex items-center justify-center w-full px-2 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded hover:bg-indigo-100 transition-colors font-bold text-[10px] uppercase">
                                                        To Prod ⏩
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    {{-- REGULER TABLE --}}
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-widest">
                                Antrian Reguler
                            </h4>
                            <span class="bg-white text-gray-600 border border-gray-200 px-3 py-1 rounded-full text-xs font-bold">{{ $reguler->total() }} Orders</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider text-left">
                                    <tr>
                                        <th class="px-6 py-3 text-center w-12">
                                            <input type="checkbox" 
                                                   @click="toggleGroup({{ $reguler->pluck('id') }})"
                                                   :checked="isGroupSelected({{ $reguler->pluck('id') }})"
                                                   class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        </th>
                                        <th class="px-6 py-3">SPK</th>
                                        <th class="px-6 py-3">Customer</th>
                                        <th class="px-6 py-3">Layanan</th>
                                        <th class="px-6 py-3 text-center">Status Material</th>
                                        <th class="px-6 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($reguler as $order)
                                    <tr class="hover:bg-gray-50 transition-colors" :class="{ 'bg-teal-50/50': selectedItems.includes('{{ $order->id }}') }">
                                        <td class="px-6 py-4 text-center">
                                            <input type="checkbox" value="{{ $order->id }}" x-model="selectedItems"
                                                   class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono font-bold text-gray-700">
                                                {{ $order->spk_number }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $order->customer_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->shoe_brand }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                             <div class="flex flex-wrap gap-1">
                                                @foreach($order->services as $s)
                                                    <span class="px-2 py-0.5 rounded text-[10px] bg-gray-100 text-gray-600 border border-gray-200">
                                                        {{ $s->name === 'Custom Service' && $s->pivot->custom_name ? $s->pivot->custom_name : $s->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            @php $hasPending = $order->materials->where('pivot.status', 'REQUESTED')->count() > 0; @endphp
                                            @if($order->materials->isEmpty())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Pending
                                                </span>
                                            @elseif($hasPending)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Butuh Belanja
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Ready
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex flex-col gap-2 items-end">
                                                <a href="{{ route('sortir.show', $order->id) }}" class="flex items-center justify-center w-full px-2 py-1 bg-teal-50 text-teal-700 border border-teal-200 rounded hover:bg-teal-100 transition-colors font-bold text-[10px] uppercase">
                                                    Check Detail 🔍
                                                </a>
                                                <button type="button" onclick="openReportModal('{{ $order->id }}')" class="flex items-center justify-center w-full px-2 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded hover:bg-amber-100 transition-colors font-bold text-[10px] uppercase">
                                                    Follow Up ⚠️
                                                </button>
                                                  <form action="{{ route('sortir.skip-production', $order->id) }}" method="POST" onsubmit="return confirm('Langsung kirim ke Production (Skip Material Check)?')">
                                                    @csrf
                                                    <button type="submit" class="flex items-center justify-center w-full px-2 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded hover:bg-indigo-100 transition-colors font-bold text-[10px] uppercase">
                                                        To Prod ⏩
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                            Antrian Reguler Kosong
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                         @if($reguler->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
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
                    onclick="bulkSkipToProduction()" 
                    class="bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:shadow-indigo-200 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                To Production (Massal)
            </button>
        </div>
    </div>
    </div>

    {{-- REPORT ISSUE MODAL --}}
    <div id="reportModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 max-w-full text-left transform transition-all scale-100">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="font-bold text-lg text-amber-600 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Lapor Kendala / Follow Up
                </h3>
                <button type="button" onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('cx-issues.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="work_order_id" id="report_work_order_id">
                <input type="hidden" name="redirect_url" value="{{ route('sortir.index') }}">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Kategori Kendala</label>
                        <select name="category" class="w-full text-sm border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Teknis">Kendala Teknis</option>
                            <option value="Material">Masalah Material</option>
                            <option value="Estimasi">Estimasi Meleset</option>
                            <option value="Tambahan">Saran Tambah Jasa</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Deskripsi Masalah</label>
                        <textarea name="description" rows="3" class="w-full text-sm border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500" placeholder="Jelaskan masalahnya..." required></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Foto Bukti (Wajib)</label>
                        <input type="file" name="photos[]" multiple class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100" accept="image/*" required>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" onclick="closeReportModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-bold hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-bold shadow transition-colors flex items-center gap-2">
                        <span>Kirim ke CX</span>
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openReportModal(id) {
            document.getElementById('report_work_order_id').value = id;
            document.getElementById('reportModal').style.display = 'flex';
            document.getElementById('reportModal').classList.remove('hidden');
        }

        function closeReportModal() {
            document.getElementById('reportModal').style.display = 'none';
            document.getElementById('reportModal').classList.add('hidden');
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
