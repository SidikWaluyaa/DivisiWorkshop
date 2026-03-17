<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kolam Cancel (Riwayat Pembatalan)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div class="space-y-1">
                            <h1 class="text-xl font-black text-gray-900 tracking-tight">Daftar Order Dibatalkan</h1>
                            <p class="text-xs font-medium text-gray-400">Arsip data yang telah dibatalkan dari sistem.</p>
                        </div>

                        <form method="GET" action="{{ route('cx.cancelled') }}" class="w-full md:w-auto bg-gray-50 p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-3 items-end">
                            <div class="w-full md:w-64 space-y-1.5">
                                <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 ml-1">Pencarian</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}" 
                                           class="w-full pl-9 pr-3 py-2 border-gray-200 rounded-xl text-xs font-bold text-gray-700 bg-white shadow-inner focus:ring-teal-500 focus:border-teal-500" 
                                           placeholder="Cari SPK / Nama...">
                                </div>
                            </div>

                            <div class="w-full md:w-40 space-y-1.5">
                                <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 ml-1">Urutan</label>
                                <select name="sort" class="w-full border-amber-100 rounded-xl text-xs font-black text-amber-600 bg-amber-50 focus:ring-amber-500 py-2 shadow-sm appearance-none px-3 pr-8" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22 fill=%22none%22 viewBox=%220 0 20 20%22%3E%3Cpath stroke=%22%23b45309%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6 8 4 4 4-4%22%2F%3E%3C%2Fsvg%3E'); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1rem auto;">
                                    <option value="asc" {{ request('sort', 'asc') == 'asc' ? 'selected' : '' }}>⏳ Terlama</option>
                                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>🔥 Terbaru</option>
                                </select>
                            </div>

                            <div class="flex gap-2 w-full md:w-auto">
                                <button type="submit" class="flex-1 md:flex-none bg-gray-900 hover:bg-black text-white px-5 py-2 rounded-xl text-xs font-black tracking-widest shadow-lg shadow-gray-200 transition-all border border-gray-800">
                                    CARI
                                </button>
                                @if(request()->anyFilled(['search']))
                                    <a href="{{ route('cx.cancelled') }}" class="p-2 bg-white border border-gray-100 text-gray-300 hover:text-red-400 rounded-xl flex items-center justify-center transition-all shadow-sm" title="Reset Filter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3">Tanggal Cancel</th>
                                    <th class="px-6 py-3">SPK & Customer</th>
                                    <th class="px-6 py-3">Sepatu</th>
                                    <th class="px-6 py-3">Alasan / History</th>
                                    <th class="px-6 py-3">Dibatalkan Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr class="bg-white border-b hover:bg-gray-50 opacity-75 grayscale hover:grayscale-0 transition-all">
                                        <td class="px-6 py-4">
                                            <div class="font-bold">{{ $order->updated_at->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ $order->updated_at->format('H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-mono bg-red-50 text-red-600 px-2 py-1 rounded inline-block text-xs font-bold mb-1">
                                                {{ $order->spk_number }}
                                            </div>
                                            <div class="font-bold text-gray-900">{{ $order->customer_name }}</div>
                                            <div class="text-xs">{{ $order->customer_phone }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold">{{ $order->shoe_brand }}</div>
                                            <div class="text-xs">{{ $order->shoe_color }} [{{ $order->shoe_size }}]</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            {{-- Display Last Manual Log or Issue Resolution --}}
                                            @php
                                                $lastLog = $order->logs()->where('step', 'CX_FOLLOWUP')->latest()->first();
                                                $msg = $lastLog ? $lastLog->description : ($order->reception_rejection_reason ?? 'Dibatalkan');
                                            @endphp
                                            <div class="italic text-gray-600 text-xs max-w-xs break-words">
                                                "{{ Str::limit($msg, 100) }}"
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                 @if($lastLog && $lastLog->user)
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">
                                                        {{ $lastLog->user->name }}
                                                    </span>
                                                 @else
                                                    -
                                                 @endif

                                                 {{-- Quick Delete Button --}}
                                                 <form action="{{ route('cx.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus permanen data ini? Action tidak bisa dibatalkan.');">
                                                     @csrf
                                                     @method('DELETE')
                                                     <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Hapus Permanen">
                                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                     </button>
                                                 </form>
                                             </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                            Tidak ada data cancel.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
