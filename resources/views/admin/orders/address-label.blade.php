<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Address Label (Plain) - {{ $order->customer_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            margin: 0;
            padding: 0;
            width: 210mm;
            height: 297mm;
            background-color: #ffffff;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            color: #1a1a1a;
        }
        .canvas {
            width: 210mm;
            height: 148.5mm;
            position: relative;
            overflow: hidden;
            background-color: #ffffff;
            border-bottom: 2px dashed #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="canvas">
        <div class="relative w-full h-full pb-12 pl-20 pr-12 flex flex-col justify-end">
            <div class="mb-2">
                <h2 class="text-lg font-bold text-slate-900 uppercase tracking-tight mb-1.5">{{ $order->customer_name }}</h2>
                <div class="space-y-0.5 border-l-2 border-slate-300 pl-4 py-0.5 mt-1.5">
                    <p class="text-xs font-semibold text-slate-800 uppercase tracking-tight">
                        {{ $order->customer?->address ?? $order->customer_address ?? 'Alamat tidak tersedia' }}
                    </p>
                    <p class="text-[9px] font-medium text-slate-500 uppercase tracking-wider">
                        {{ $order->customer?->district ?? '-' }} | {{ $order->customer?->city ?? '-' }}
                    </p>
                    <p class="text-[9px] font-medium text-slate-500 uppercase tracking-wider">
                        {{ $order->customer?->province ?? '-' }} - {{ $order->customer?->postal_code ?? '-' }}
                    </p>
                    <div class="pt-1 flex items-center gap-1.5">
                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">No. Telp:</span>
                        <span class="text-[10px] font-extrabold text-slate-900 tracking-tight font-mono">
                            {{ trim($order->customer_phone ?? $order->customer?->phone ?? '-') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = () => {
             setTimeout(() => {
                 window.print();
             }, 800);
        };
    </script>
</body>
</html>
