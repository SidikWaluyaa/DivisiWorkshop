<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Pengambilan Sepatu - {{ date('d F Y') }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 11px;
        }
        .filter-info {
            margin-bottom: 15px;
            font-style: italic;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            color: #444;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .font-mono {
            font-family: monospace;
            font-size: 10px;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
            }
        }
        .print-btn {
            background-color: #10b981;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .print-btn:hover {
            background-color: #059669;
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak Halaman</button>
    </div>

    <div class="header">
        <h1>Laporan Riwayat Pengambilan Sepatu</h1>
        <p>Divisi Gudang / Warehouse | Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    <div class="filter-info">
        <strong>Filter Aktif:</strong>
        Pencarian: "{{ $search ?: 'Semua' }}" | 
        Rentang Tanggal: 
        @if($startDate && $endDate)
            {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        @elseif($startDate)
            Mulai {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
        @elseif($endDate)
            Hingga {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        @else
            Semua Riwayat
        @endif
        | Total Data: {{ $orders->count() }} Sepatu
    </div>

    <table>
        <thead>
            <tr>
                <th style="text-align: center; width: 30px;">No</th>
                <th style="width: 80px;">Tgl Diambil</th>
                <th style="width: 90px;">No SPK</th>
                <th style="width: 120px;">Pelanggan</th>
                <th>Detail Sepatu</th>
                <th style="width: 90px; text-align: center;">Foto Sepatu</th>
                <th style="width: 100px;">Metode Pengambilan</th>
                <th style="width: 80px; text-align: right;">Real Ongkir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                <tr>
                    <td style="text-align: center; vertical-align: middle;">{{ $index + 1 }}</td>
                    <td style="vertical-align: middle;">{{ $order->taken_date ? \Carbon\Carbon::parse($order->taken_date)->format('d/m/Y H:i') : '-' }}</td>
                    <td class="font-mono text-emerald-600" style="vertical-align: middle;">{{ $order->spk_number }}</td>
                    <td style="vertical-align: middle;">
                        <strong>{{ $order->customer_name }}</strong><br>
                        <span style="font-size: 9px; color: #666;">{{ $order->customer_phone ?? '-' }}</span>
                    </td>
                    <td style="vertical-align: middle;">
                        <strong>{{ $order->shoe_brand }}</strong><br>
                        Warna: {{ $order->shoe_color ?? '-' }}
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        @if($order->spk_cover_photo_url)
                            <img src="{{ $order->spk_cover_photo_url }}" alt="Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                        @else
                            <span style="color: #ccc; font-style: italic; font-size: 8px;">Tidak Ada Foto</span>
                        @endif
                    </td>
                    <td>
                        {{ $order->pickup_method ?: 'Offline' }}
                    </td>
                    <td style="text-align: right;" class="font-mono">
                        Rp {{ number_format($order->actual_shipping_cost ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #999; font-style: italic;">Tidak ada data riwayat pengambilan yang cocok dengan kriteria pencarian Anda.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        // Auto trigger print dialog on page open
        window.onload = () => {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
