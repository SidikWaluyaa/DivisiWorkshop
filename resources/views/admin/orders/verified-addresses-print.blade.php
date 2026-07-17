<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Alamat Terverifikasi - {{ date('d F Y') }}</title>
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
        .spk-badge {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 10px;
            display: inline-block;
            margin-bottom: 2px;
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
        <h1>Laporan Alamat Terverifikasi</h1>
        <p>Divisi Customer Experience (CX) | Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    <div class="filter-info">
        <strong>Filter Aktif:</strong>
        Pencarian: "{{ $search ?: 'Semua' }}" | 
        Rentang Tanggal: 
        @if($dateStart && $dateEnd)
            {{ \Carbon\Carbon::parse($dateStart)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($dateEnd)->format('d/m/Y') }}
        @elseif($dateStart)
            Mulai {{ \Carbon\Carbon::parse($dateStart)->format('d/m/Y') }}
        @elseif($dateEnd)
            Hingga {{ \Carbon\Carbon::parse($dateEnd)->format('d/m/Y') }}
        @else
            Hari Ini ({{ date('d/m/Y') }})
        @endif
        | Total Data: {{ $customers->count() }} Pelanggan
    </div>

    <table>
        <thead>
            <tr>
                <th style="text-align: center; width: 30px;">No</th>
                <th style="width: 100px;">Waktu Verifikasi</th>
                <th style="width: 130px;">Nama Pelanggan</th>
                <th style="width: 90px;">No. Telepon</th>
                <th>Alamat Pengiriman</th>
                <th style="width: 150px;">SPK Aktif</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $index => $customer)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $customer->address_verified_at?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td style="font-weight: bold;">{{ $customer->name }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>
                        <strong>{{ $customer->address }}</strong><br>
                        Kel. {{ $customer->village ?? '-' }} | Kec. {{ $customer->district ?? '-' }} | {{ $customer->city ?? '-' }} | {{ $customer->province ?? '-' }} ({{ $customer->postal_code ?? '-' }})
                    </td>
                    <td>
                        @forelse($customer->workOrders as $order)
                            <span class="spk-badge">{{ $order->spk_number }}</span><br>
                            <span style="font-size: 8px; color: #777;">{{ $order->shoe_brand }}</span><br>
                        @empty
                            <span style="color: #999; font-style: italic;">Tidak ada SPK aktif</span>
                        @endforelse
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999; font-style: italic;">Tidak ada data pelanggan yang cocok dengan kriteria pencarian Anda.</td>
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
