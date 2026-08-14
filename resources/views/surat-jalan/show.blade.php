<x-workshop-pwa-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-slate-800 dark:text-white leading-tight flex items-center gap-2">
            <span>📄</span> Detail Surat Jalan: {{ $suratJalan->nomor_surat }}
        </h2>
    </x-slot>

    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Action Header Card inside body --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700">
                <div>
                    <h1 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📄</span> Surat Jalan {{ $suratJalan->nomor_surat }}
                    </h1>
                    <p class="text-xs text-slate-500 font-medium mt-1">Dokumen serah-terima fisik internal Workshop</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('surat-jalan.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 rounded-xl text-xs font-bold transition">
                        ← Kembali
                    </a>
                    <a href="{{ route('surat-jalan.print', $suratJalan->id) }}" target="_blank" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-indigo-200 transition active:scale-95 flex items-center gap-2">
                        <span>🖨️ Cetak Surat Jalan</span>
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 dark:border-slate-700 space-y-6">
                
                @php
                    $totalSpk = $suratJalan->items->count();
                    $totalJasa = $suratJalan->items->sum(function($item) {
                        return $item->workOrder?->services?->count() ?? 0;
                    });
                @endphp

                {{-- Header Summary Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 p-5 bg-slate-50 dark:bg-slate-700/40 rounded-2xl border border-slate-100 dark:border-slate-600">
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 block mb-1">Nomor Surat Jalan</span>
                        <span class="font-mono font-black text-sm text-slate-800 dark:text-white">{{ $suratJalan->nomor_surat }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 block mb-1">Rute Serah Terima</span>
                        @if($suratJalan->jenis_serah_terima === 'sortir_to_produksi')
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase bg-blue-100 text-blue-700">Sortir ➔ Produksi</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase bg-purple-100 text-purple-700">Produksi ➔ QC</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 block mb-1">Muatan SPK & Jasa</span>
                        <span class="font-black text-xs text-indigo-600 dark:text-indigo-400">{{ $totalSpk }} SPK ({{ $totalJasa }} Jasa)</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 block mb-1">Pengirim</span>
                        <span class="font-bold text-xs text-slate-700 dark:text-slate-200">{{ $suratJalan->pengirim?->name ?? 'Admin' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 block mb-1">Status</span>
                        @if($suratJalan->status === 'DITERIMA')
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase bg-emerald-100 text-emerald-700">✅ DITERIMA</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase bg-amber-100 text-amber-700">🚚 DIKIRIM</span>
                        @endif
                    </div>
                </div>

                {{-- SPK Items Table --}}
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-black text-sm uppercase text-slate-800 dark:text-white tracking-wider flex items-center gap-2">
                            <span>📦</span> Daftar SPK & Rincian Layanan
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 rounded-full text-xs font-black">
                                Total: {{ $totalSpk }} SPK
                            </span>
                            <span class="px-3 py-1 bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 rounded-full text-xs font-black">
                                {{ $totalJasa }} Layanan Jasa
                            </span>
                        </div>
                    </div>

                    <div class="border border-slate-100 dark:border-slate-700 rounded-2xl overflow-hidden shadow-sm">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-700/50 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                                <tr>
                                    <th class="px-5 py-3.5">No SPK & Customer</th>
                                    <th class="px-5 py-3.5">Merk & Tipe Sepatu</th>
                                    <th class="px-5 py-3.5">Rincian Jasa / Layanan</th>
                                    <th class="px-5 py-3.5 text-center">Est. Selesai</th>
                                    <th class="px-5 py-3.5">Catatan / Note</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                                @foreach ($suratJalan->items as $item)
                                    @php
                                        $wo = $item->workOrder;
                                        $services = $wo?->services ?? collect();
                                        $estDate = $wo?->new_estimation_date ?? $wo?->estimation_date;
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                                        <td class="px-5 py-4">
                                            <span class="font-mono font-black text-slate-800 dark:text-white block">{{ $wo?->spk_number }}</span>
                                            <span class="text-slate-500 font-bold text-[11px]">{{ $wo?->customer_name }}</span>
                                        </td>
                                        <td class="px-5 py-4 font-bold text-slate-600 dark:text-slate-400">
                                            <span>{{ $wo?->shoe_brand }} {{ $wo?->shoe_type }}</span>
                                            <span class="block text-[10px] text-slate-400 font-bold">Size: {{ $wo?->shoe_size ?? '-' }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if($services->isNotEmpty())
                                                <ul class="list-disc list-inside space-y-0.5 font-bold text-slate-700 dark:text-slate-300">
                                                    @foreach($services as $srv)
                                                        @php
                                                            $serviceName = $srv->pivot->custom_service_name ?? $srv->name ?? $srv->service_name ?? 'Layanan Servis';
                                                        @endphp
                                                        <li>{{ $serviceName }}</li>
                                                    @endforeach
                                                </ul>
                                                <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 mt-1 block">Total: {{ $services->count() }} Jasa</span>
                                            @else
                                                <span class="text-slate-400 italic">- Tidak ada layanan -</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-center font-bold text-slate-800 dark:text-white">
                                            {{ $estDate ? $estDate->translatedFormat('d M Y') : '-' }}
                                        </td>
                                        <td class="px-5 py-4 text-slate-400 italic text-[11px]"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Action Button for Receiver --}}
                @if ($suratJalan->status == 'DIKIRIM')
                    <form action="{{ route('surat-jalan.receive', $suratJalan->id) }}" method="POST" class="pt-6 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                        @csrf
                        <button type="submit" class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl text-xs uppercase tracking-widest shadow-lg shadow-emerald-200 transition-all active:scale-95 flex items-center gap-2">
                            <span>✓ Konfirmasi Terima Surat Jalan Ini</span>
                        </button>
                    </form>
                @else
                    <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center justify-between text-xs font-bold text-emerald-800">
                        <span>Surat Jalan ini telah dikonfirmasi diterima oleh {{ $suratJalan->penerima?->name ?? 'Penerima' }} pada {{ $suratJalan->diterima_at ? $suratJalan->diterima_at->translatedFormat('d M Y H:i') : '-' }} WIB.</span>
                        <span class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg text-[10px] font-black uppercase">STATUS OK</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-workshop-pwa-layout>
