<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Follow-up SPK Pending - {{ date('d/m/Y H:i') }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 10mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #334155;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #22B086;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #22B086;
            font-size: 18px;
            text-transform: uppercase;
        }
        
        .summary-boxes {
            margin-bottom: 20px;
            width: 100%;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            width: 45%;
            display: inline-block;
            text-align: center;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #22B086;
            display: block;
        }
        .summary-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            padding: 8px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .spk-item {
            margin-bottom: 5px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #f1f5f9;
        }
        .spk-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .spk-num { color: #22B086; font-weight: bold; font-size: 9px; }
        
        .badge-count {
            background-color: #22B086;
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daftar Follow-up Customer</h1>
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>

    <div class="summary-boxes">
        <div class="summary-box">
            <span class="summary-value">{{ $totalCustomer }}</span>
            <span class="summary-label">Total Customer</span>
        </div>
        <div class="summary-box" style="float: right;">
            <span class="summary-value">{{ $totalSpk }}</span>
            <span class="summary-label">Total SPK Pending</span>
        </div>
        <div style="clear: both;"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30" class="text-center">No</th>
                <th width="150">Customer / Kontak</th>
                <th>Data SPK & Detail Sepatu</th>
                <th width="60" class="text-center">Jumlah SPK</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($groupedOrders as $contact => $orders)
                @php $firstOrder = $orders->first(); @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>
                        <div class="font-bold" style="font-size: 11px;">{{ $firstOrder->customer_name }}</div>
                        <div style="color: #64748b; margin-top: 2px; font-size: 10px;">{{ $firstOrder->customer_phone }}</div>
                    </td>
                    <td>
                        @foreach($orders as $order)
                            <div class="spk-item">
                                <span class="spk-num">{{ $order->spk_number }}</span>
                                <span style="font-size: 8px; color: #94a3b8; margin-left: 5px;">({{ $order->created_at->format('d/m/Y') }})</span>
                                <div style="margin-top: 2px;">
                                    <strong>{{ $order->shoe_brand }}</strong> {{ $order->shoe_type }} 
                                    <span style="color: #64748b;">({{ $order->shoe_color }} / {{ $order->shoe_size }})</span>
                                </div>
                            </div>
                        @endforeach
                    </td>
                    <td class="text-center">
                        <span class="badge-count">{{ $orders->count() }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistem Workshop ShoeWorkshop - Halaman 1 dari 1
    </div>
</body>
</html>
