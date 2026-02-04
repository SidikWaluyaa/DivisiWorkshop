<x-app-layout>
<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Logistik <span class="text-[#22AF85]">Manifest</span></h1>
                <p class="text-sm text-gray-500 mt-1 font-medium italic">Manajemen pengiriman batch antar gudang & workshop</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('manifest.create') }}" class="inline-flex items-center px-6 py-3 bg-[#FFC232] border border-transparent rounded-xl font-bold text-sm text-gray-900 uppercase tracking-widest hover:bg-[#e6af2e] focus:outline-none focus:ring-2 focus:ring-[#FFC232] focus:ring-offset-2 transition-all shadow-lg shadow-yellow-200/50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Pengiriman
                </a>
            </div>
        </div>

        <!-- Layered Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between group hover:border-[#22AF85]/30 transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-blue-50 text-blue-600 transition-colors group-hover:bg-[#22AF85]/10 group-hover:text-[#22AF85]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Manifest OTW</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ $manifests->where('status', 'SENT')->count() }}</p>
                    </div>
                </div>
                <div class="text-xs font-bold text-[#22AF85] bg-[#22AF85]/5 px-2 py-1 rounded">ACTIVE</div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between group hover:border-[#22AF85]/30 transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-emerald-50 text-[#22AF85]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Manifest Selesai</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ $manifests->where('status', 'RECEIVED')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between group hover:border-[#22AF85]/30 transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-amber-50 text-[#FFC232]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Item Terkirim</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ $manifests->sum('work_orders_count') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-white/50 backdrop-blur-sm">
                <h2 class="text-lg font-bold text-gray-800">Daftar Pengiriman Barang</h2>
                <div class="text-xs font-bold text-gray-400">Total: {{ $manifests->total() }} Records</div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">No. Manifest</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Logistik Info</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Batch Size</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                            <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($manifests as $manifest)
                        <tr class="hover:bg-[#22AF85]/[0.02] transition-colors group">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="text-sm font-black text-[#22AF85] tracking-tight">{{ $manifest->manifest_number }}</span>
                                <div class="text-[10px] text-gray-400 font-bold mt-1 uppercase">Generated Auto</div>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs">
                                        {{ strtoupper(substr($manifest->dispatcher->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-gray-800">{{ $manifest->dispatcher->name }}</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $manifest->dispatched_at->format('d M Y • H:i') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-black">
                                    {{ $manifest->work_orders_count }} Pasang
                                </span>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap text-center">
                                @if($manifest->status === 'SENT')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-tighter">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2 animate-pulse"></span>
                                        In-Transit
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-[#22AF85]/10 text-[#22AF85] border border-[#22AF85]/20 uppercase tracking-tighter">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Diterima
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right">
                                <a href="{{ route('manifest.show', $manifest->id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-[#22AF85] hover:border-[#22AF85] transition-all group-hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="bg-gray-50/50 inline-flex p-8 rounded-full mb-4">
                                    <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900">Belum ada manifest</h3>
                                <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">Silakan buat pengiriman baru untuk melacak perpindahan sepatu dari gudang ke workshop.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($manifests->hasPages())
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                {{ $manifests->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>
