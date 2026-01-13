<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .meta { margin-top: 5px; color: #666; }
        
        .section-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; margin-top: 20px; color: #444; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        
        table { w-full; border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { bg-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .summary-box { float: right; w-width: 300px; border: 1px solid #444; padding: 10px; background: #f9f9f9; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px; }
        .total-row { font-weight: bold; font-size: 16px; border-top: 1px solid #ccc; padding-top: 5px; margin-top: 5px; }
        .profit { color: green; }
        .loss { color: red; }
    </style>
</head>
<body>
    <header>
        <h1>Laporan Keuangan Workshop</h1>
        <div class="meta">Periode: {{ $rangeLabel }}</div>
    </header>

    <div class="section-title">A. Pemasukan (Completed Orders)</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal Ambil</th>
                <th>No SPK</th>
                <th>Item</th>
                <th>Layanan</th>
                <th class="text-right">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->taken_date->format('d/m/Y') }}</td>
                <td>{{ $order->spk_number }}</td>
                <td>{{ $order->shoe_brand }} - {{ $order->shoe_color }}</td>
                <td>
                    @foreach($order->services as $svc)
                        <div>- {{ $svc->name }}</div>
                    @endforeach
                </td>
                <td class="text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @if($orders->isEmpty())
            <tr><td colspan="5" class="text-center">Tidak ada data pemasukan.</td></tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><b>Total Pemasukan</b></td>
                <td class="text-right"><b>Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</b></td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">B. Pengeluaran (Belanja Material)</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No PO</th>
                <th>Supplier</th>
                <th>Material</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
            <tr>
                <td>{{ $purchase->created_at->format('d/m/Y') }}</td>
                <td>{{ $purchase->po_number }}</td>
                <td>{{ $purchase->supplier_name }}</td>
                <td>{{ $purchase->material->name }} ({{ $purchase->quantity }} {{ $purchase->material->unit }})</td>
                <td class="text-right">Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @if($purchases->isEmpty())
            <tr><td colspan="5" class="text-center">Tidak ada data pengeluaran.</td></tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><b>Total Pengeluaran</b></td>
                <td class="text-right"><b>Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}</b></td>
            </tr>
        </tfoot>
    </table>

    <div style="page-break-inside: avoid;">
        <div class="section-title">C. Ringkasan Laporan</div>
        <table style="width: 50%; margin-top: 10px;">
            <tr>
                <td>Total Pemasukan</td>
                <td class="text-right">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</td>
            </tr>
             <tr>
                <td>Total Pengeluaran</td>
                <td class="text-right" style="color: red;">(Rp {{ number_format($summary['total_expense'], 0, ',', '.') }})</td>
            </tr>
            <tr style="background-color: #eee;">
                <td><b>Profit Bersih</b></td>
                <td class="text-right">
                    <b class="{{ $summary['net_profit'] >= 0 ? 'profit' : 'loss' }}">
                        Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}
                    </b>
                </td>
            </tr>
        </table>
    </div>
    
    <div style="text-align: right; margin-top: 50px; font-size: 11px; color: #888;">
        Dicetak otomatis oleh Sistem Workshop pada {{ date('d M Y H:i') }}
    </div>
</body>
</html>
