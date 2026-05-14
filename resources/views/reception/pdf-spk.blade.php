<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SPK - {{ $order->spk_number }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #1e293b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .sidebar {
            width: 75mm;
            background-color: #22B086;
            color: white;
            vertical-align: top;
            height: 297mm;
        }
        .main-content {
            width: 135mm;
            vertical-align: top;
            padding: 20px;
            background-color: white;
        }
        .sidebar-inner {
            padding: 20px;
        }
        .logo-section {
            margin-bottom: 20px;
        }
        .qr-code {
            background-color: white;
            padding: 5px;
            border-radius: 8px;
            display: inline-block;
        }
        .sidebar-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            color: #FFC232;
        }
        .sidebar-box {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 12px;
        }
        .main-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 5px;
        }
        .main-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
        }
        .spk-number {
            font-size: 16px;
            font-weight: bold;
            color: #22B086;
            font-family: monospace;
        }
        .orange-bar {
            background-color: #FFC232;
            color: #1e293b;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            border-radius: 6px;
            margin-bottom: 5px;
        }
        .service-detail {
            padding-left: 15px;
            border-left: 2px solid #e2e8f0;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .footer-sidebar {
            position: absolute;
            bottom: 30px;
            left: 20px;
        }
        .footer-tag {
            font-size: 12px;
            font-weight: bold;
        }
        .acc-qc-box {
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            margin-top: 20px;
        }
        .acc-qc-header {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 5px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            color: #FFC232;
        }
        .acc-qc-body {
            padding: 10px;
        }
        .signature-grid {
            margin-top: 30px;
        }
        .signature-box {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            font-size: 9px;
            height: 60px;
        }
    </style>
</head>
<body>
    @include('reception.pdf-spk-content', ['order' => $order, 'barcode' => $barcode])
</body>
</html>
