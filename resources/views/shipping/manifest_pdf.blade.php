<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manifest Pengiriman - {{ $date_start }}</title>
    <style>
        @page {
            size: a4;
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #111;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .header {
            background-color: #22AF85; /* Premium Green */
            color: white;
            padding: 30px 40px;
            text-align: left;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 10px;
            opacity: 0.9;
        }
        .info-section {
            padding: 25px 40px;
            background-color: #f9fafb;
            border-bottom: 1px solid #edf2f7;
        }
        .info-grid {
            width: 100%;
        }
        .info-grid td {
            vertical-align: top;
        }
        .label {
            color: #718096;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .value {
            font-size: 11px;
            font-weight: 900;
            color: #2d3748;
        }
        .value.highlight {
            color: #22AF85;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        th {
            background-color: #fff;
            color: #718096;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.5px;
            text-align: left;
            padding: 15px 10px;
            border-bottom: 2px solid #edf2f7;
        }
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #f7fafc;
            vertical-align: middle;
        }
        .spk-badge {
            font-family: 'Helvetica', sans-serif;
            font-weight: 900;
            color: #1a202c;
            letter-spacing: -0.2px;
        }
        .cat-badge {
            background-color: #FFC232; /* Premium Yellow */
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 900;
            font-size: 8px;
            color: #000;
            text-transform: uppercase;
        }
        .resi {
            color: #22AF85;
            font-weight: bold;
        }
        .summary-box {
            background-color: #22AF85;
            color: white;
            padding: 20px 30px;
            float: right;
            margin-top: 40px;
            margin-right: 40px;
            border-radius: 12px;
        }
        .summary-item {
            display: inline-block;
            margin-left: 30px;
            text-align: right;
        }
        .summary-label {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: bold;
            opacity: 0.7;
            margin-bottom: 2px;
        }
        .summary-value {
            font-size: 24px;
            font-weight: 900;
        }
        .footer-text {
            position: fixed;
            bottom: 30px;
            width: 100%;
            text-align: center;
            color: #cbd5e0;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .paraf {
            width: 35px;
            height: 35px;
            border: 1px dashed #e2e8f0;
            border-radius: 6px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MANIFEST PENGIRIMAN</h1>
        <p>Divisi - Shoe Workshop</p>
    </div>

    <div class="info-section">
        <table class="info-grid text-center" style="width: 100%">
            <tr>
                <td width="33%" style="text-align: left;">
                    <div class="label">Periode Pengiriman</div>
                    <div class="value highlight">
                        @if($date_start == $date_end)
                            {{ \Carbon\Carbon::parse($date_start)->translatedFormat('l, d F Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($date_start)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($date_end)->translatedFormat('d M Y') }}
                        @endif
                    </div>
                </td>
                <td width="33%">
                    <div class="label">Kategori</div>
                    <div class="value">
                        <span style="background-color: #FFC232; padding: 2px 8px; border-radius: 4px;">{{ $category ?: 'Semua Kategori' }}</span>
                    </div>
                </td>
                <td width="33%" style="text-align: right;">
                    <div class="label">Waktu Cetak</div>
                    <div class="value">{{ $printed_at->translatedFormat('d F Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div style="padding: 0 30px;">
        <table>
            <thead>
                <tr>
                    <th width="20" class="text-center">#</th>
                    <th width="110">No. SPK</th>
                    <th>Detail Kustomer</th>
                    <th width="90">Kategori</th>
                    <th width="110">Resi / PIC</th>
                    <th width="60" class="text-center">Paraf</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shippings as $index => $item)
                    <tr>
                        <td class="text-center" style="color: #cbd5e0; font-weight: bold;">{{ $index + 1 }}</td>
                        <td class="spk-badge">{{ $item->workOrder->spk_number ?? 'N/A' }}</td>
                        <td>
                            <div style="font-weight: 900; color: #1a202c; font-size: 11px;">{{ $item->workOrder->customer_name ?? 'N/A' }}</div>
                            <div style="color: #a0aec0; font-size: 8px; font-weight: bold; margin-top: 2px;">{{ $item->workOrder->customer_phone ?? '' }}</div>
                        </td>
                        <td>
                            <span class="cat-badge" style="background-color: rgba(255, 194, 50, 0.2); color: #000;">{{ $item->kategori_pengiriman ?: '-' }}</span>
                        </td>
                        <td>
                            @if($item->resi_pengiriman)
                                <div class="resi">{{ $item->resi_pengiriman }}</div>
                            @endif
                            @if($item->pic)
                                <div style="color: #a0aec0; font-size: 8px; font-weight: bold; text-transform: uppercase;">PIC: {{ $item->pic }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="paraf"></div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="summary-box">
        <div class="summary-item" style="border-right: 1px solid rgba(255,255,255,0.2); padding-right: 30px; margin-left: 0;">
            <div class="summary-label">Total Pengiriman</div>
            <div class="summary-value">{{ count($shippings) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Pasang</div>
            <div class="summary-value">{{ count($shippings) }}</div>
        </div>
    </div>

    <div class="footer-text">
        Dokumen Resmi Shoe Workshop - Halaman 1 / 1
    </div>
</body>
</html>
