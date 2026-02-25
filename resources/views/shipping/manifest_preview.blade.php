<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview Manifest Pengiriman - {{ $date_start }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-container { box-shadow: none !important; border: none !important; width: 100% !important; max-width: none !important; margin: 0 !important; }
            @page { margin: 1cm; }
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F3F4F6;
        }
        .manifest-table th {
            background-color: #F9FAFB;
            color: #374151;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.05em;
        }
        .spk-badge {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
        }
    </style>
</head>
<body class="p-4 md:p-8">

    <!-- Actions Bar -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print">
        <a href="{{ route('shipping.index') }}" class="flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Antrean
        </a>
        <div class="flex gap-3">
            <button onclick="window.print()" class="bg-[#22AF85] text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-[#22AF85]/20 hover:brightness-95 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Halaman
            </button>
            <a href="{{ route('shipping.manifest.download', request()->all()) }}" class="bg-gray-800 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-gray-900 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
        </div>
    </div>

    <!-- Manifest Document -->
    <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-sm border border-gray-100 p-10 print-container">
        <!-- Header -->
        <div class="flex justify-between items-start mb-10 pb-10 border-b-2 border-gray-50">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tighter uppercase mb-1">Manifest Pengiriman</h1>
                <p class="text-[#22AF85] font-bold text-sm tracking-widest uppercase">Divisi Workshop - SidikWaluyaa</p>
            </div>
            <div class="text-right">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Waktu Cetak</div>
                <div class="text-sm font-bold text-gray-900">{{ $printed_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="grid grid-cols-3 gap-8 mb-12">
            <div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Periode Pengiriman</div>
                <div class="text-sm font-extrabold text-gray-900">
                    @if($date_start == $date_end)
                        {{ \Carbon\Carbon::parse($date_start)->translatedFormat('l, d F Y') }}
                    @else
                        {{ \Carbon\Carbon::parse($date_start)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($date_end)->translatedFormat('d M Y') }}
                    @endif
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Kategori</div>
                <div class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold uppercase tracking-wider">
                    {{ $category ?: 'Semua Kategori' }}
                </div>
            </div>
            <div class="text-right">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Petugas Penyiap</div>
                <div class="text-sm font-extrabold text-gray-900 underline decoration-[#22AF85] decoration-2 underline-offset-4">
                    {{ $prepared_by ?: 'Bagian Shipping' }}
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full mb-12 border border-gray-100 rounded-lg overflow-hidden manifest-table">
            <thead>
                <tr class="border-b-2 border-gray-100">
                    <th class="px-4 py-4 text-center w-12 text-gray-400">#</th>
                    <th class="px-4 py-4 text-left">No. SPK</th>
                    <th class="px-4 py-4 text-left">Kustomer</th>
                    <th class="px-4 py-4 text-left">Kategori</th>
                    <th class="px-4 py-4 text-left">Resi / PIC</th>
                    <th class="px-4 py-4 text-center w-16">Paraf</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shippings as $index => $item)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-5 text-center text-xs font-bold text-gray-400">{{ $index + 1 }}</td>
                    <td class="px-4 py-5 text-sm font-black spk-badge text-gray-900 uppercase tracking-tighter">
                        {{ $item->workOrder->spk_number ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-5">
                        <div class="text-sm font-bold text-gray-900">{{ $item->workOrder->customer_name ?? 'N/A' }}</div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $item->workOrder->customer_phone ?? '' }}</div>
                    </td>
                    <td class="px-4 py-5">
                        <span class="text-[10px] font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded">
                            {{ $item->kategori_pengiriman ?: '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-5 font-medium text-xs text-gray-600">
                        @if($item->resi_pengiriman)
                            <div class="font-bold text-gray-900 spk-badge">{{ $item->resi_pengiriman }}</div>
                        @endif
                        @if($item->pic)
                            <div class="text-[10px] font-bold uppercase tracking-widest text-[#22AF85]/70 mt-1">PIC: {{ $item->pic }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-5 text-center">
                        <div class="w-8 h-8 mx-auto border-2 border-dashed border-gray-200 rounded"></div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="mt-20">
            <div class="grid grid-cols-3 gap-12 text-center">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-16">Disiapkan Oleh</p>
                    <div class="border-b border-gray-900 mx-auto w-32"></div>
                    <p class="text-[11px] font-black text-gray-900 uppercase mt-2">( {{ $prepared_by ?: 'Bagian Shipping' }} )</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-16">Disetujui Oleh</p>
                    <div class="border-b border-gray-900 mx-auto w-32"></div>
                    <p class="text-[11px] font-black text-gray-900 uppercase mt-2">( Koordinator )</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-16">Diterima Oleh</p>
                    <div class="border-b border-gray-900 mx-auto w-32"></div>
                    <p class="text-[11px] font-black text-gray-900 uppercase mt-2">( Kurir / Driver )</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-20 pt-10 border-t border-gray-50 flex justify-between items-center opacity-30 text-[10px] font-bold uppercase tracking-widest text-gray-400">
            <div>Dokumen Resmi SidikWaluyaa</div>
            <div>Halamanan 1 / 1</div>
        </div>
    </div>

    <!-- Print Shortcut Label -->
    <div class="fixed bottom-6 right-6 no-print bg-white/80 backdrop-blur-md px-4 py-2 rounded-full shadow-lg border border-gray-100 text-[10px] font-black uppercase text-gray-400 tracking-widest">
        Shortcut: <span class="text-gray-900">Ctrl + P</span> untuk mencetak langsung
    </div>

</body>
</html>
