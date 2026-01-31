<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.data-integrity.index') }}" class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l-7 7m7-7H21"></path></svg>
            </a>
            <div>
                <h2 class="font-bold text-xl leading-tight tracking-wide">{{ __('Pusat Data Tersembunyi (Limbo)') }}</h2>
                <p class="text-xs font-medium opacity-90">Pemantauan SPK dalam Status Terminal / Hidden</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex gap-2 overflow-x-auto pb-2">
                @foreach(['DONASI' => 'Donasi / Hangus', 'BATAL' => 'Dibatalkan', 'DIANTAR' => 'Dalam Pengantaran', 'WAITING_VERIFICATION' => 'Verifikasi Finance', 'CX_FOLLOWUP' => 'Menunggu CX'] as $key => $label)
                    <a href="{{ route('admin.data-integrity.limbo', ['status' => $key]) }}" 
                       class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all {{ $status === $key ? 'bg-yellow-500 text-white shadow-lg' : 'bg-white text-gray-400 hover:text-gray-600 border border-gray-100' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <form action="{{ route('admin.data-integrity.limbo') }}" method="GET" class="w-full md:w-96 relative">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full pl-10 pr-4 py-3 border-gray-100 rounded-2xl text-sm shadow-sm focus:ring-yellow-500"
                           placeholder="Cari SPK atau Customer...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </form>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest text-right">
                    Menampilkan {{ $orders->total() }} Data dengan status <span class="text-yellow-600 underline">{{ $status }}</span>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 text-gray-500 text-[10px] uppercase tracking-widest font-black">
                                <th class="px-6 py-5">SPK & Customer</th>
                                <th class="px-6 py-5">Status Terakhir</th>
                                <th class="px-6 py-5">Update Terakhir</th>
                                <th class="px-6 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-black text-gray-900 text-sm tracking-tight">{{ $order->spk_number }}</span>
                                            <span class="text-xs text-gray-500 font-bold uppercase">{{ $order->customer_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-yellow-200">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-gray-700">{{ $order->updated_at?->format('d M Y') }}</span>
                                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $order->updated_at?->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-200 transition-colors">Lihat Detail</a>
                                            <form action="{{ route('reception.process', $order->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="RE_PROCESS">
                                                <button type="submit" class="px-4 py-2 bg-yellow-50 text-yellow-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-yellow-100 transition-colors">Proses Ulang</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <div class="text-4xl mb-4 opacity-20">🍃</div>
                                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Tidak ada data dalam status ini</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($orders->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $orders->appends(['status' => $status])->links() }}
                    </div>
                @endif
            </div>

            <div class="bg-yellow-50 rounded-2xl p-6 border border-yellow-100">
                <div class="flex gap-4">
                    <div class="text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-yellow-800 uppercase tracking-widest mb-1">Catatan Penting</h4>
                        <p class="text-xs text-yellow-700 leading-relaxed">Status <b>Limbo</b> adalah status akhir atau status sistem yang tidak memerlukan tindakan teknis di workshop. SPK dalam status ini tidak akan muncul di dashboard workshop atau gudang demi menjaga kebersihan antrean operasional.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
