<x-app-layout>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-red-500 via-red-600 to-orange-500 rounded-2xl shadow-xl p-8 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black">Sampah Komplain</h1>
                        <p class="text-white/80 text-sm font-medium mt-1">Kelola data keluhan yang dihapus</p>
                    </div>
                </div>
                <a href="{{ route('admin.complaints.index') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl text-sm font-bold transition-all flex items-center gap-2 border border-white/20 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Kembali ke List</span>
                </a>
            </div>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="p-6 bg-white border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Item yang Dihapus</h3>
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold shadow-sm">Total: {{ $deletedComplaints->total() }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gradient-to-r from-red-50 to-orange-50 border-b-2 border-red-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black text-red-700 uppercase tracking-wider">SPK / Pelanggan</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-red-700 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-red-700 uppercase tracking-wider">Dihapus Pada</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-red-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($deletedComplaints as $complaint)
                            <tr class="hover:bg-red-50/10 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $complaint->workOrder->spk_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $complaint->customer_name }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $complaint->customer_phone }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-md text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                        {{ $complaint->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">{{ $complaint->deleted_at->format('d M Y H:i') }}</div>
                                    <div class="text-xs text-red-400 font-medium">dihapus {{ $complaint->deleted_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    {{-- Restore Button --}}
                                    <form action="{{ route('admin.complaints.restore', $complaint->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 hover:text-green-800 rounded-lg text-xs font-bold transition-all border border-green-200">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            Pulihkan
                                        </button>
                                    </form>

                                    {{-- Force Delete Button --}}
                                    <form action="{{ route('admin.complaints.force-delete', $complaint->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus permanen? Data tidak bisa kembali!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 hover:bg-red-100 hover:text-red-800 rounded-lg text-xs font-bold transition-all border border-red-200">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus Permanen
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </div>
                                        <p class="font-medium">Sampah KOSONG</p>
                                        <p class="text-xs">Tidak ada data keluhan yang dihapus.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($deletedComplaints->hasPages())
                <div class="p-6 border-t border-gray-100 bg-gray-50">
                    {{ $deletedComplaints->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
