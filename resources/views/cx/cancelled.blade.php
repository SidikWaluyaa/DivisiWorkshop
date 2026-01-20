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
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-lg font-bold">Daftar Order Dibatalkan</h1>
                        <form method="GET" action="{{ route('cx.cancelled') }}" class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari SPK / Nama..." class="px-3 py-2 border rounded-lg text-sm">
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm">Cari</button>
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
