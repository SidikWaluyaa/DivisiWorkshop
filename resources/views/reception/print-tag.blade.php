<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Tag - {{ $order->spk_number }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 0 auto; text-align: center; border: 2px solid #000; padding: 15px; box-sizing: border-box; }
        .header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .spk { font-size: 24px; font-weight: 900; letter-spacing: 1px; }
        .date { font-size: 11px; margin-top: 5px; }
        .customer { margin-bottom: 5px; font-weight: bold; font-size: 16px; text-transform: uppercase; }
        .item { font-size: 14px; margin-bottom: 20px; word-wrap: break-word; }
        .barcode { margin-top: 15px; display: flex; justify-content: center; }
        .barcode > svg { width: 150px; height: 150px; margin: 0 auto; } 
        @media print {
            button { display: none; }
            body { border: none; }
        }
        .btn-print { margin-top: 20px; padding: 10px 20px; cursor: pointer; background: #000; color: #fff; border: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="spk">{{ $order->spk_number }}</div>
        <div class="date">{{ $order->entry_date->format('d/m/Y H:i') }}</div>
    </div>
    
    <div class="customer">{{ $order->customer_name }}</div>
    <div class="item">{{ $order->shoe_brand }} - {{ $order->shoe_type }} <br> ({{ $order->shoe_color }} / {{ $order->shoe_size }})</div>

    <div class="barcode">
        {!! $barcode !!}
    </div>
    <div style="font-size: 10px; margin-top: 2px; font-family: monospace;">{{ $order->spk_number }}</div>
    
    <button class="btn-print" onclick="window.print()">PRINT TAG</button>
</body>
</html>
