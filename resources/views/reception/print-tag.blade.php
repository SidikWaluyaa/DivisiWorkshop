<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Tag - {{ $order->spk_number }}</title>
    <style>
        body { font-family: sans-serif; width: 300px; margin: 0 auto; text-align: center; border: 1px dashed #000; padding: 10px; }
        .spk { font-size: 20px; font-weight: bold; }
        .date { font-size: 12px; }
        .customer { margin-top: 10px; font-weight: bold; }
        .item { font-size: 14px; margin-bottom: 20px; }
        .barcode { background: #eee; height: 50px; margin-top: 10px; line-height: 50px; }
    </style>
</head>
<body>
    <div class="spk">{{ $order->spk_number }}</div>
    <div class="date">{{ $order->entry_date->format('d/m/Y H:i') }}</div>
    
    <div class="customer">{{ $order->customer_name }}</div>
    <div class="item">{{ $order->shoe_brand }} ({{ $order->shoe_size }})</div>

    <div class="barcode">
        [ BARCODE: {{ $order->spk_number }} ]
    </div>
    <br>
    <button onclick="window.print()">Print</button>
</body>
</html>
