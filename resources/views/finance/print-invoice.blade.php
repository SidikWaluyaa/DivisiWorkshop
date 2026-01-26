<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->spk_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            color: #1f2937;
        }

        .invoice-container {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: white;
            padding: 15mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
        }

        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            body {
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .invoice-container {
                width: 100%;
                min-height: 100vh;
                margin: 0;
                box-shadow: none;
                padding: 15mm;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    
    <!-- Print Controls -->
    <div class="no-print fixed top-4 right-4 z-50 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Invoice
        </button>
        <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow-lg">
            Tutup
        </button>
    </div>

    <div class="invoice-container">
        <!-- Header -->
        <div class="flex justify-between items-start mb-12">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">INVOICE</h1>
                <p class="text-gray-500 mt-1">#{{ $order->spk_number }}</p>
                
                @if($order->payment_due_date)
                    <div class="mt-4 inline-block bg-red-50 border border-red-200 rounded px-2 py-1">
                        <p class="text-xs font-bold text-red-600 uppercase tracking-wider">Jatuh Tempo</p>
                        <p class="font-bold text-red-800">{{ $order->payment_due_date->format('d M Y') }}</p>
                    </div>
                @endif
            </div>
            <div class="text-right">
                <div class="font-black text-xl text-teal-600 mb-1">SHOE WORKSHOP</div>
                <p class="text-sm text-gray-500">Jl. Contoh Workshop No. 123</p>
                <p class="text-sm text-gray-500">Bandung, Jawa Barat</p>
                <p class="text-sm text-gray-500">WhatsApp: 0812-3456-7890</p>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-12 mb-12 border-t border-b border-gray-100 py-8">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">DITAGIHKAN KEPADA</h3>
                <div class="text-gray-900 font-bold text-lg">{{ $order->customer_name }}</div>
                <div class="text-gray-600 mt-1">{{ $order->customer_phone }}</div>
                <div class="text-gray-600 mt-1 text-sm max-w-xs">{{ $order->customer->address ?? ($order->customer_address ?? '-') }}</div>
            </div>
            <div class="text-right">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-medium">Tanggal Masuk:</span>
                        <span class="font-bold text-gray-900">{{ $order->entry_date ? $order->entry_date->format('d/m/Y') : '-' }}</span>
                    </div>
                    @if($order->finished_date)
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-medium">Tanggal Selesai:</span>
                        <span class="font-bold text-gray-900">{{ $order->finished_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-medium">Item Sepatu:</span>
                        <span class="font-bold text-gray-900 text-right">{{ $order->shoe_brand }} {{ $order->shoe_size }}<br><span class="text-xs font-normal text-gray-500">{{ $order->shoe_color }}</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Table -->
        <table class="w-full mb-12">
            <thead>
                <tr class="border-b-2 border-gray-900">
                    <th class="text-left py-3 font-black text-gray-900 uppercase tracking-wider text-sm">Deskripsi Layanan</th>
                    <th class="text-right py-3 font-black text-gray-900 uppercase tracking-wider text-sm w-40">Biaya</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($order->workOrderServices as $detail)
                <tr>
                    <td class="py-4 text-gray-700 font-medium">
                        {{ $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan') }}
                        @if($detail->service && $detail->service->category)
                            <div class="text-xs text-gray-400 mt-0.5">{{ $detail->service->category }}</div>
                        @endif
                    </td>
                    <td class="py-4 text-right font-bold text-gray-900">Rp {{ number_format($detail->cost, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                
                @if(($order->cost_oto + $order->cost_add_service) > 0)
                <tr>
                    <td class="py-4 text-gray-700 font-medium">Biaya Tambahan / OTO</td>
                    <td class="py-4 text-right font-bold text-gray-900">Rp {{ number_format($order->cost_oto + $order->cost_add_service, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($order->shipping_cost > 0)
                <tr>
                    <td class="py-4 text-gray-700 font-medium">
                        Ongkos Kirim
                        <span class="text-xs text-gray-400 ml-2">({{ $order->shipping_type ?? 'Ekspedisi' }})</span>
                    </td>
                    <td class="py-4 text-right font-bold text-gray-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Summary & Totals -->
        <div class="flex justify-end mb-12">
            <div class="w-1/2 space-y-3">
                @if($order->discount > 0)
                <div class="flex justify-between text-gray-600">
                    <span>Diskon</span>
                    <span class="font-medium text-red-500">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                
                @if($order->unique_code > 0)
                <div class="flex justify-between text-gray-600">
                    <span>Kode Unik</span>
                    <span class="font-medium">Rp {{ number_format($order->unique_code, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="flex justify-between border-t border-gray-900 pt-3">
                    <span class="font-black text-gray-900 text-lg">TOTAL TAGIHAN</span>
                    <span class="font-black text-gray-900 text-lg">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</span>
                </div>
                
                <!-- Payment History Summary -->
                @if($order->payments->count() > 0)
                    <div class="pt-4 mt-4 border-t border-dashed border-gray-300">
                        <div class="mb-2 text-xs font-bold text-gray-400 uppercase">Riwayat Pembayaran</div>
                        @foreach($order->payments as $payment)
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>{{ $payment->paid_at->format('d/m/y') }} ({{ $payment->payment_method }})</span>
                            <span class="font-medium text-teal-600">- Rp {{ number_format($payment->amount_total, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex justify-between bg-gray-100 p-3 rounded font-bold mt-4">
                    <span class="{{ $order->sisa_tagihan > 0 ? 'text-red-600' : 'text-teal-600' }}">SISA TAGIHAN</span>
                    <span class="{{ $order->sisa_tagihan > 0 ? 'text-red-600' : 'text-teal-600' }}">Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</span>
                </div>
                
                @if($order->sisa_tagihan <= 0)
                    <div class="text-center mt-4">
                        <span class="inline-block border-2 border-teal-500 text-teal-600 font-black px-4 py-1 rounded uppercase tracking-widest transform rotate-[-2deg]">LUNAS / PAID</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer / Bank Info -->
        <div class="grid grid-cols-2 gap-12 mt-auto pt-12 border-t border-gray-200">
            <div>
                <h4 class="font-bold text-gray-900 mb-3 text-sm">Metode Pembayaran</h4>
                <div class="text-sm text-gray-600 leading-relaxed">
                    <p>Silakan transfer pembayaran ke:</p>
                    <p class="font-bold text-gray-900 mt-1">BCA: 123-456-7890</p>
                    <p class="font-bold text-gray-900">Mandiri: 987-654-3210</p>
                    <p class="mt-1">A.N. Shoe Workshop Owner</p>
                </div>
            </div>
            <div class="text-center pt-8">
                <div class="mb-16 font-bold text-gray-900 text-sm">Hormat Kami,</div>
                <div class="border-b border-gray-300 w-2/3 mx-auto"></div>
                <div class="mt-2 text-sm text-gray-500">( Admin Finance )</div>
            </div>
        </div>

    </div>
</body>
</html>
