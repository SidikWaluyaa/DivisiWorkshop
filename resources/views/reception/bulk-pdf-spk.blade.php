<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Follow-up - {{ date('d/m/Y') }}</title>
    <style>
        @page { margin: 1cm; size: a4 portrait; }
        body { font-family: sans-serif; font-size: 9px; line-height: 1.2; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 5px; vertical-align: top; }
        th { background: #eee; text-align: left; text-transform: uppercase; font-size: 8px; }
        .header { margin-bottom: 15px; border-bottom: 2px solid #22B086; padding-bottom: 5px; }
        .summary-table { margin-bottom: 15px; border: none; }
        .summary-table td { border: none; padding: 2px; }
        .prio-tag { font-weight: bold; font-size: 7px; color: #92400e; }
        .spk-row { margin-bottom: 3px; border-bottom: 1px dotted #eee; padding-bottom: 2px; }
        .spk-row:last-child { border-bottom: none; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0; color:#22B086;">Daftar Follow-up Customer</h2>
        <p style="margin:2px 0; font-size:8px;">Dicetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table class="summary-table">
        <tr>
            <td width="100">Total Customer:</td>
            <td><strong>{{ $totalCustomer }}</strong></td>
            <td width="100">Total SPK Pending:</td>
            <td><strong>{{ $totalSpk }}</strong></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="20">No</th>
                <th width="120">Customer / WhatsApp</th>
                <th>Data SPK & Detail Sepatu</th>
                <th width="40">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($groupedOrders as $contact => $orders)
                @php $first = $orders->first(); @endphp
                <tr>
                    <td style="text-align:center;">{{ $no++ }}</td>
                    <td>
                        <strong>{{ $first->customer_name }}</strong><br>
                        {{ $first->customer_phone }}
                    </td>
                    <td>
                        @foreach($orders as $order)
                            <div class="spk-row">
                                <span style="color:#22B086; font-weight:bold;">{{ $order->spk_number }}</span>
                                <span style="font-size:7px; color:#999;">({{ $order->created_at->format('d/m/y') }})</span>
                                <br>
                                {{ $order->shoe_brand }} {{ $order->shoe_type }} ({{ $order->shoe_color }}/{{ $order->shoe_size }})
                            </div>
                        @endforeach
                    </td>
                    <td style="text-align:center; font-weight:bold;">{{ $orders->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
