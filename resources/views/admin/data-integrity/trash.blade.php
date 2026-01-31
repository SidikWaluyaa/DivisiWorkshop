<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.data-integrity.index') }}" class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l-7 7m7-7H21"></path></svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl leading-tight tracking-wide">{{ __('Tempat Sampah Global') }}</h2>
                    <p class="text-xs font-medium opacity-90">Pemulihan & Penghapusan Permanen</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50" x-data="{ selectedItems: [], type: '{{ $type }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Tabs --}}
            <div class="flex gap-2 overflow-x-auto pb-4 no-scrollbar">
                @php
                    $tabs = [
                        'Workshop' => ['work_order' => 'SPK / Order'],
                        'CS Pipeline' => ['cs_lead' => 'Leads', 'cs_quotation' => 'Penawaran', 'cs_spk' => 'SPK CS'],
                        'Warehouse' => ['material_request' => 'MR / Belanja', 'purchase' => 'Pembelian', 'storage_rack' => 'Rak Gudang'],
                        'Customer Experience' => ['complaint' => 'Komplain', 'oto' => 'OTO'],
                        'Master Data' => ['service' => 'Layanan', 'material' => 'Material', 'customer' => 'Pelanggan']
                    ];
                @endphp

                @foreach($tabs as $group => $items)
                    <div class="flex items-center gap-2 bg-white p-1 rounded-2xl border border-gray-100 shadow-sm mr-2">
                        <span class="px-3 text-[10px] font-black text-gray-400 uppercase tracking-tighter border-r border-gray-50">{{ $group }}</span>
                        @foreach($items as $key => $label)
                            <a href="{{ route('admin.data-integrity.trash', ['type' => $key]) }}" 
                               class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap {{ $type === $key ? 'bg-indigo-600 text-white shadow-lg' : 'bg-transparent text-gray-400 hover:text-gray-600' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>

            {{-- Search & Info --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <form action="{{ route('admin.data-integrity.trash') }}" method="GET" class="w-full md:w-96 relative">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full pl-10 pr-4 py-3 border-gray-100 rounded-2xl text-sm shadow-sm focus:ring-indigo-500"
                           placeholder="Cari data...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </form>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total: {{ $data->total() }} Item</div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 text-gray-500 text-[10px] uppercase tracking-widest font-black">
                                <th class="px-6 py-5 w-10 text-center">
                                    <input type="checkbox" @click="if($event.target.checked) { selectedItems = {{ json_encode($data->pluck('id')) }} } else { selectedItems = [] }" class="rounded border-gray-200 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="px-6 py-5">
                                    @php
                                        $label = match($type) {
                                            'service', 'material' => 'Nama Item',
                                            'customer' => 'Nama Pelanggan',
                                            'cs_lead' => 'Nama Lead',
                                            'cs_quotation' => 'No. Penawaran',
                                            'cs_spk', 'work_order', 'complaint' => 'No. SPK',
                                            'material_request' => 'No. Request',
                                            'purchase' => 'No. PO',
                                            'oto' => 'Title OTO',
                                            default => 'Identitas'
                                        };
                                    @endphp
                                    {{ $label }}
                                </th>
                                <th class="px-6 py-5 text-center">Tgl Hapus</th>
                                <th class="px-6 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-center">
                                        <input type="checkbox" value="{{ $item->id }}" x-model="selectedItems" class="rounded border-gray-200 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            @if($type === 'work_order' || $type === 'cs_spk')
                                                <span class="font-black text-gray-900 text-sm tracking-tight">{{ $item->spk_number }}</span>
                                                <span class="text-xs text-gray-500 font-bold uppercase">{{ $item->customer_name }}</span>
                                            @elseif($type === 'cs_lead')
                                                <span class="font-black text-gray-900 text-sm tracking-tight">{{ $item->customer_name }}</span>
                                                <span class="text-xs text-gray-500 font-bold uppercase">{{ $item->customer_phone }}</span>
                                            @elseif($type === 'cs_quotation')
                                                <span class="font-black text-gray-900 text-sm tracking-tight">{{ $item->quotation_number }}</span>
                                                <span class="text-xs text-gray-500 font-bold uppercase">{{ $item->lead->customer_name ?? 'N/A' }}</span>
                                            @elseif($type === 'service' || $type === 'material')
                                                <span class="font-black text-gray-900 text-sm tracking-tight">{{ $item->name }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase">CATEGORY: {{ $item->category }}</span>
                                            @elseif($type === 'customer')
                                                <span class="font-black text-gray-900 text-sm tracking-tight">{{ $item->name }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $item->phone }}</span>
                                            @elseif($type === 'complaint')
                                                <span class="font-black text-gray-900 text-sm tracking-tight">{{ $item->spk_number }}</span>
                                                <span class="text-xs text-red-500 font-bold uppercase tracking-widest">{{ $item->type }}</span>
                                            @elseif($type === 'material_request')
                                                <span class="font-black text-gray-900 text-sm tracking-tight">{{ $item->request_number }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase">Requested by: {{ $item->requestedBy->name ?? 'N/A' }}</span>
                                            @elseif($type === 'purchase')
                                                <span class="font-black text-gray-900 text-sm tracking-tight">{{ $item->po_number }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $item->supplier_name }}</span>
                                            @elseif($type === 'storage_rack')
                                                <span class="font-black text-gray-900 text-sm tracking-tight">{{ $item->code }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $item->category }}</span>
                                            @elseif($type === 'oto')
                                                <span class="font-black text-gray-900 text-sm tracking-tight">{{ $item->title }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase">WO: {{ $item->workOrder->spk_number ?? 'N/A' }}</span>
                                            @else
                                                <span class="font-black text-gray-900 text-sm tracking-tight">ID: {{ $item->id }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-gray-700">{{ $item->deleted_at?->format('d M Y') }}</span>
                                            <span class="text-[10px] text-gray-400 font-bold">{{ $item->deleted_at?->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <form action="{{ route('admin.data-integrity.restore-many') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="type" value="{{ $type }}">
                                                <input type="hidden" name="ids[]" value="{{ $item->id }}">
                                                <button type="submit" class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-100 transition-colors">Pulihkan</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <div class="text-4xl mb-4 opacity-20">🍃</div>
                                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Tidak ada data terhapus</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($data->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $data->appends(['type' => $type])->links() }}
                    </div>
                @endif
            </div>

            {{-- Bulk Actions --}}
            <div x-show="selectedItems.length > 0" x-cloak class="fixed bottom-8 inset-x-0 z-50 flex justify-center px-4">
                <div class="bg-gray-900/90 backdrop-blur-xl shadow-2xl rounded-3xl p-5 w-full max-w-2xl flex items-center justify-between border border-white/10">
                    <div class="flex items-center gap-4 ml-2">
                        <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white font-black text-lg shadow-lg" x-text="selectedItems.length"></div>
                        <div>
                            <p class="text-xs font-black text-white uppercase tracking-widest">Item Terpilih</p>
                            <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-widest">Pilih tindakan untuk data masal</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('admin.data-integrity.restore-many') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" :value="type">
                            <template x-for="id in selectedItems" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="px-8 py-4 bg-white text-gray-900 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-100 transition-all shadow-xl">Pulihkan Semua</button>
                        </form>
                        <form action="{{ route('admin.data-integrity.force-delete-many') }}" method="POST" onsubmit="return confirm('HAPUS PERMANEN? Data tidak bisa dipulihkan kembali!')">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="type" :value="type">
                            <template x-for="id in selectedItems" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="px-8 py-4 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all shadow-xl shadow-red-500/20">Hapus Permanen</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
