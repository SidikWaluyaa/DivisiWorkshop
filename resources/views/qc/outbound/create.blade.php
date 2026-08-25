<x-workshop-pwa-layout title="Buat Manifest Outbound">
<div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Navigation Header --}}
        <div class="mb-8 flex justify-between items-center">
            <div>
                <a href="{{ route('qc.outbound') }}" class="inline-flex items-center text-xs font-bold text-gray-500 hover:text-teal-600 dark:text-gray-400 dark:hover:text-teal-400 transition-colors mb-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Staging Outbound
                </a>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                    Form Pembuatan <span class="text-teal-600">Manifest Outbound</span>
                </h1>
                <p class="text-xs text-gray-500 mt-1">Pilih SPK yang telah lolos QC Akhir untuk diterbitkan manifest pengirimannya ke Gudang.</p>
            </div>
        </div>

        {{-- Embedded Form for Outbound Creation --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
            {{-- Form submits via Livewire component or standard route --}}
            <form action="{{ route('qc.outbound') }}" method="GET">
                <div class="mb-6 p-4 bg-teal-50/60 dark:bg-teal-950/30 rounded-xl border border-teal-200 dark:border-teal-900/50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold text-lg">
                            📦
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase">Siap Terbitkan Manifest</h3>
                            <p class="text-xs text-gray-500">Terdapat {{ $stagingOrders->count() }} SPK yang berada di Staging Outbound Siap Kirim.</p>
                        </div>
                    </div>
                    <a href="{{ route('qc.outbound') }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow transition-all active:scale-95">
                        Kelola via Modul Outbound
                    </a>
                </div>

                {{-- Table Preview --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-750 text-[10px] font-black text-gray-500 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                                <th class="py-3 px-4">#</th>
                                <th class="py-3 px-4">Nomor SPK</th>
                                <th class="py-3 px-4">Pelanggan</th>
                                <th class="py-3 px-4">Brand & Sepatu</th>
                                <th class="py-3 px-4">Prioritas</th>
                                <th class="py-3 px-4">Layanan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                            @forelse($stagingOrders as $index => $item)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750/30">
                                    <td class="py-3 px-4 font-bold text-gray-400">{{ $index + 1 }}</td>
                                    <td class="py-3 px-4 font-mono font-bold text-blue-600 dark:text-blue-400">
                                        {{ $item->spk_number }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-gray-800 dark:text-gray-200">
                                        {{ $item->customer_name }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                                        {{ $item->shoe_brand }} - {{ $item->shoe_type }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase
                                            {{ in_array($item->priority, ['Prioritas', 'Urgent', 'Express', 'OTO']) ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $item->priority ?? 'Regular' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($item->workOrderServices as $svc)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                                    {{ $svc->custom_service_name ?? ($svc->service ? $svc->service->name : 'Layanan') }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 italic">
                                        Tidak ada SPK di Staging Outbound saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </form>
        </div>
    </div>
</div>
</x-workshop-pwa-layout>
