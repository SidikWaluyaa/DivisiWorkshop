<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pembelian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .stats {
            width: 100%;
            margin-bottom: 20px;
        }
        .stats td {
            background-color: #f9f9f9;
            text-align: center;
            border: 1px solid #ddd;
            padding: 10px;
            width: 33%;
        }
        .stats h3 { margin: 0; font-size: 14px; color: #555; }
        .stats p { margin: 5px 0 0; font-size: 16px; font-weight: bold; }
        .status-received { color: green; }
        .status-pending { color: orange; }
        .status-ordered { color: blue; }
        .status-cancelled { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" style="max-width: 150px; margin-bottom: 10px;">
        <h2>Laporan Pembelian</h2>
        <p>Periode: {{ $rangeLabel }}</p>
        <span style="font-size: 10px; color: #777;">Dicetak: {{ date('d M Y H:i:s') }}</span>
    </div>

    <table class="stats">
        <tr>
            <td>
                <h3>Total Belanja</h3>
                <p>Rp {{ number_format($analytics['total_spend'], 0, ',', '.') }}</p>
            </td>
            <td>
                <h3>Total Transaksi</h3>
                <p>{{ $analytics['total_transactions'] }}</p>
            </td>
            <td>
                <h3>Top Supplier</h3>
                <p>{{ $analytics['top_supplier'] }}</p>
                @if($analytics['avg_rating'] > 0)
                    <small style="color: goldenrod;">★ {{ number_format($analytics['avg_rating'], 1) }}</small>
                @endif
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No. PO</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Material</th>
                <th>Qty</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Rating</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
            <tr>
                <td>{{ $purchase->po_number }}</td>
                <td>{{ \Carbon\Carbon::parse($purchase->created_at)->format('d M Y') }}</td>
                <td>{{ $purchase->supplier_name ?? '-' }}</td>
                <td>
                    {{ $purchase->material->name }}<br>
                    <small>@ Rp {{ number_format($purchase->unit_price, 0, ',', '.') }}</small>
                </td>
                <td>{{ $purchase->quantity }} {{ $purchase->material->unit }}</td>
                <td>Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
                <td>
                    <span class="status-{{ $purchase->status }}">{{ ucfirst($purchase->status) }}</span>
                    @if($purchase->payment_status !== 'paid')
                        <br><small style="color:red">(Unpaid)</small>
                    @endif
                </td>
                <td>
                    @if($purchase->quality_rating)
                        {{ $purchase->quality_rating }}/5
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
