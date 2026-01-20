<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembayaran - {{ $order->spk_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                margin: 1cm;
                size: A4;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>
<body class="bg-white p-8">
    {{-- Header with Logo --}}
    <div class="border-b-4 border-teal-600 pb-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-4xl font-black text-gray-900 mb-2">WORKSHOP</h1>
                <p class="text-sm text-gray-600">Jl. Workshop No. 123, Jakarta</p>
                <p class="text-sm text-gray-600">Telp: (021) 1234-5678</p>
                <p class="text-sm text-gray-600">Email: info@workshop.com</p>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-bold text-teal-600 mb-2">RIWAYAT PEMBAYARAN</h2>
                <p class="text-sm text-gray-600">Tanggal Cetak: {{ now()->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Order Information --}}
    <div class="grid grid-cols-2 gap-6 mb-8">
        <div>
            <h3 class="font-bold text-gray-900 mb-3 text-lg border-b border-gray-300 pb-2">Informasi Order</h3>
            <table class="w-full text-sm">
                <tr>
                    <td class="py-1 text-gray-600 w-32">No. SPK</td>
                    <td class="py-1 font-bold">{{ $order->spk_number }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-600">Status</td>
                    <td class="py-1 font-semibold">{{ str_replace('_', ' ', $order->status->value) }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-600">Tanggal Masuk</td>
                    <td class="py-1">{{ $order->entry_date?->format('d M Y') ?? '-' }}</td>
                </tr>
            </table>
        </div>
        
        <div>
            <h3 class="font-bold text-gray-900 mb-3 text-lg border-b border-gray-300 pb-2">Informasi Customer</h3>
            <table class="w-full text-sm">
                <tr>
                    <td class="py-1 text-gray-600 w-32">Nama</td>
                    <td class="py-1 font-bold">{{ $order->customer_name }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-600">Telepon</td>
                    <td class="py-1">{{ $order->customer_phone }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-600">Sepatu</td>
                    <td class="py-1">{{ $order->shoe_brand }} - {{ $order->shoe_color }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="bg-gradient-to-r from-teal-50 to-orange-50 border-2 border-teal-200 rounded-lg p-6 mb-8">
        <h3 class="font-bold text-gray-900 mb-4 text-lg">Ringkasan Keuangan</h3>
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Total Tagihan</p>
                <p class="text-2xl font-black text-gray-900">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-green-600 uppercase font-semibold mb-1">Sudah Dibayar</p>
                <p class="text-2xl font-black text-green-600">Rp {{ number_format($order->total_paid, 0, ',', '.') }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs {{ $order->sisa_tagihan > 0 ? 'text-red-600' : 'text-gray-600' }} uppercase font-semibold mb-1">Sisa Tagihan</p>
                <p class="text-2xl font-black {{ $order->sisa_tagihan > 0 ? 'text-red-600' : 'text-gray-400' }}">Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Payment History --}}
    <div class="mb-8">
        <h3 class="font-bold text-gray-900 mb-4 text-lg border-b-2 border-gray-300 pb-2">Riwayat Pembayaran</h3>
        
        @forelse($order->payments as $payment)
            <div class="border-l-4 border-teal-500 pl-4 mb-6 relative">
                <div class="absolute -left-2 top-0 w-3 h-3 bg-teal-500 rounded-full"></div>
                
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg">
                                {{ $payment->type === 'BEFORE' ? '💵 DP / Pembayaran Awal' : '✅ Pelunasan' }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                {{ $payment->paid_at->format('d M Y, H:i') }} • {{ $payment->payment_method }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-black text-teal-600">Rp {{ number_format($payment->amount_total, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    @if($payment->notes)
                        <div class="bg-amber-50 border-l-4 border-amber-400 p-3 rounded-r mt-3">
                            <p class="text-sm text-amber-800 italic">"{{ $payment->notes }}"</p>
                        </div>
                    @endif
                    
                    <div class="mt-3 pt-3 border-t border-gray-300 flex justify-between text-xs text-gray-500">
                        <span>PIC: {{ $payment->pic->name ?? '-' }}</span>
                        @if($payment->proof_image)
                            <span class="text-blue-600">✓ Bukti transfer tersedia</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                <p>Belum ada pembayaran</p>
            </div>
        @endforelse
    </div>

    {{-- Total Summary --}}
    @if($order->payments->count() > 0)
        <div class="border-t-4 border-teal-600 pt-4">
            <div class="flex justify-between items-center">
                <span class="text-lg font-bold text-gray-900">TOTAL TERBAYAR</span>
                <span class="text-3xl font-black text-teal-600">Rp {{ number_format($order->payments->sum('amount_total'), 0, ',', '.') }}</span>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="mt-12 pt-6 border-t border-gray-300 text-center text-sm text-gray-600">
        <p>Dokumen ini dicetak otomatis dari sistem Workshop Management</p>
        <p class="mt-1">Untuk informasi lebih lanjut, hubungi customer service kami</p>
    </div>

    {{-- Print Button (hidden when printing) --}}
    <div class="no-print fixed bottom-8 right-8">
        <button onclick="window.print()" 
                class="bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print / Save as PDF
        </button>
    </div>

    {{-- Auto-print on load (optional) --}}
    <script>
        // Uncomment to auto-print on page load
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
