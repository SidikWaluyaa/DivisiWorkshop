<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manifest Pengiriman - {{ $date_start }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #22AF85; /* Premium Green */
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .info-section {
            padding: 15px 20px;
            border-bottom: 2px solid #f3f4f6;
            margin-bottom: 20px;
        }
        .info-grid {
            width: 100%;
        }
        .info-grid td {
            vertical-align: top;
        }
        .label {
            color: #6b7280;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            margin-bottom: 2px;
        }
        .value {
            font-size: 12px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            text-align: left;
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        .spk-badge {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            color: #111827;
        }
        .cat-badge {
            background-color: #FFC232; /* Premium Yellow */
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            color: #000;
        }
        .footer {
            margin-top: 50px;
            width: 100%;
        }
        .sign-box {
            width: 200px;
            text-align: center;
        }
        .sign-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            width: 180px;
            margin-left: auto;
            margin-right: auto;
        }
        .signature-section {
            margin-top: 40px;
        }
        .page-break {
            page-break-after: always;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MANIFEST PENGIRIMAN</h1>
        <p style="margin: 5px 0 0 0; opacity: 0.8;">Divisi Workshop - SidikWaluyaa</p>
    </div>

    <div class="info-section">
        <table class="info-grid">
            <tr>
                <td width="33%">
                    <div class="label">Periode Pengiriman</div>
                    <div class="value">
                        @if($date_start == $date_end)
                            {{ \Carbon\Carbon::parse($date_start)->format('l, d F Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($date_start)->format('d M') }} - {{ \Carbon\Carbon::parse($date_end)->format('d M Y') }}
                        @endif
                    </div>
                </td>
                <td width="33%">
                    <div class="label">Kategori</div>
                    <div class="value">{{ $category ?: 'Semua Kategori' }}</div>
                </td>
                <td width="33%" class="text-right">
                    <div class="label">Waktu Cetak</div>
                    <div class="value">{{ $printed_at->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30" class="text-center">No</th>
                <th width="120">No. SPK</th>
                <th>Kustomer</th>
                <th width="100">Kategori</th>
                <th width="120">Resi / PIC</th>
                <th width="100" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shippings as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="spk-badge">{{ $item->workOrder->spk_number ?? 'N/A' }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $item->workOrder->customer_name ?? 'N/A' }}</div>
                        <div style="color: #6b7280; font-size: 9px;">{{ $item->workOrder->customer_phone ?? '' }}</div>
                    </td>
                    <td>
                        <span class="cat-badge">{{ $item->kategori_pengiriman ?: '-' }}</span>
                    </td>
                    <td>
                        @if($item->resi_pengiriman)
                            <div style="font-family: monospace; font-size: 10px;">{{ $item->resi_pengiriman }}</div>
                        @endif
                        @if($item->pic)
                            <div style="color: #6b7280; font-size: 9px;">PIC: {{ $item->pic }}</div>
                        @endif
                        @if(!$item->resi_pengiriman && !$item->pic)
                            <span style="color: #d1d5db;">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div style="width: 20px; height: 20px; border: 1px solid #ccc; margin: 0 auto;"></div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none;" width="33%">
                    <div class="sign-box">
                        <div class="label">Disiapkan Oleh,</div>
                        <div class="sign-line"></div>
                        <div style="margin-top: 5px;">( {{ $prepared_by ?: 'Bagian Shipping' }} )</div>
                    </div>
                </td>
                <td style="border: none;" width="33%">
                    <div class="sign-box">
                        <div class="label">Disetujui Oleh,</div>
                        <div class="sign-line"></div>
                        <div style="margin-top: 5px;">( Koordinator )</div>
                    </div>
                </td>
                <td style="border: none;" width="33%">
                    <div class="sign-box">
                        <div class="label">Diterima Oleh,</div>
                        <div class="sign-line"></div>
                        <div style="margin-top: 5px;">( Kurir / Driver )</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div style="position: fixed; bottom: 20px; width: 100%; text-align: center; color: #9ca3af; font-size: 8px;">
        Dokumen ini dibuat secara otomatis oleh Sistem Workshop SidikWaluyaa - Halaman 1
    </div>
</body>
</html>
