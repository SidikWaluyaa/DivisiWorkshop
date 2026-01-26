<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-red-500/20 rounded-lg backdrop-blur-sm shadow-sm border border-red-500/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    {{ __('Tempat Sampah') }}
                </h2>
                <div class="text-xs font-medium opacity-90">
                    Pengelolaan Data Terhapus (Reception)
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50" x-data="{ selectedItems: [] }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex justify-between items-center mb-4">
                <a href="{{ route('reception.index') }}" class="flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-teal-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l-7 7m7-7H21"></path></svg>
                    Kembali ke Penerimaan
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800">Daftar Data Terhapus</h3>
                    <div class="text-sm text-gray-500">Total: {{ $orders->total() }} data</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 text-gray-600 text-xs uppercase tracking-widest font-bold">
                                <th class="px-6 py-4 w-10">
                                    <input type="checkbox" @click="if($event.target.checked) { selectedItems = {{ json_encode($orders->pluck('id')) }} } else { selectedItems = [] }" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                </th>
                                <th class="px-6 py-4">SPK & Customer</th>
                                <th class="px-6 py-4 text-center">Tgl Hapus</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" value="{{ $order->id }}" x-model="selectedItems" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900">{{ $order->spk_number }}</span>
                                            <span class="text-sm text-gray-500">{{ $order->customer_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm text-gray-600">{{ $order->deleted_at->format('d M Y H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <form action="{{ route('reception.restore', $order->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-teal-50 text-teal-700 rounded-lg text-xs font-bold hover:bg-teal-100 transition-colors">
                                                    Pulihkan
                                                </button>
                                            </form>
                                            <form action="{{ route('reception.force-delete', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus permanen data ini? Nomor SPK akan tersedia kembali untuk import baru.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors">
                                                    Hapus Permanen
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            <p>Tempat sampah kosong.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

            <!-- Bulk Actions -->
            <div x-show="selectedItems.length > 0" x-cloak class="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4">
                <div class="bg-white/90 backdrop-blur shadow-2xl rounded-2xl p-4 w-full max-w-lg flex items-center justify-between gap-4 border border-gray-200">
                    <span class="text-sm font-bold text-gray-700 ml-2">
                        <span class="text-teal-600" x-text="selectedItems.length"></span> Item Terpilih
                    </span>
                    <form action="{{ route('reception.bulk-force-delete') }}" method="POST" id="bulk-force-delete-form" onsubmit="return confirm('HAPUS PERMANEN semua data terpilih? SPK yang dihapus bisa di-import ulang.')">
                        @csrf
                        @method('DELETE')
                        <template x-for="id in selectedItems" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg transition-all">
                            Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
