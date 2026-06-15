<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Invoices</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #1e293b;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            padding: 15px 0;
            border-bottom: 3px solid #10b981;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }
        .header .subtitle {
            font-size: 10px;
            color: #64748b;
            font-weight: 600;
        }
        .meta-bar {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .meta-bar .meta-item {
            display: inline;
            font-size: 9px;
            color: #334155;
            margin-right: 25px;
        }
        .meta-bar .meta-item strong {
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table thead th {
            background: #0f172a;
            color: #ffffff;
            font-weight: 700;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 6px;
            text-align: left;
            border: 1px solid #1e293b;
        }
        table thead th.text-right { text-align: right; }
        table thead th.text-center { text-align: center; }
        table tbody tr:nth-child(even) { background: #f8fafc; }
        table tbody tr:hover { background: #f1f5f9; }
        table tbody td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            font-size: 8.5px;
            vertical-align: middle;
        }
        table tbody td.text-right { text-align: right; }
        table tbody td.text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-bb { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }
        .badge-bl { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-l { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .summary-bar {
            background: #0f172a;
            color: #ffffff;
            padding: 10px 15px;
            border-radius: 6px;
            margin-top: 10px;
        }
        .summary-bar table {
            margin-bottom: 0;
        }
        .summary-bar table td {
            border: none;
            padding: 3px 8px;
            color: #ffffff;
            font-size: 9px;
        }
        .summary-bar table td.label {
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-bar table td.value {
            font-weight: 900;
            text-align: right;
            font-size: 10px;
        }
        .summary-bar table td.value-highlight {
            color: #34d399;
            font-weight: 900;
            text-align: right;
            font-size: 11px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 8px;
        }
        .text-mono { font-family: 'DejaVu Sans Mono', monospace; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data Invoices</h1>
        <p class="subtitle">Sistem Workshop — Finance Report</p>
    </div>

    <div class="meta-bar">
        <span class="meta-item"><strong>Periode:</strong> {{ $periodLabel }}</span>
        <span class="meta-item"><strong>Filter Status:</strong> {{ $statusLabel }}</span>
        <span class="meta-item"><strong>Total Data:</strong> {{ $invoices->count() }} record</span>
        <span class="meta-item"><strong>Dicetak:</strong> {{ $printDate }}</span>
    </div>

    {{-- Status Breakdown Cards --}}
    @if($invoices->count() > 0)
    <div style="margin-bottom: 14px;">
        <div style="font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 6px;">Distribusi Status Tagihan</div>
        <table style="margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="background: #64748b; text-align: center; width: 33.33%;">BB — Belum Bayar</th>
                    <th style="background: #b45309; text-align: center; width: 33.33%;">BL — DP/Cicil</th>
                    <th style="background: #047857; text-align: center; width: 33.33%;">L — Lunas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center; padding: 8px;">
                        <div style="font-size: 12px; font-weight: 900; color: #64748b;">Rp {{ number_format($statusBreakdown['BB']['total'], 0, ',', '.') }}</div>
                        <div style="font-size: 8px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 2px;">{{ $statusBreakdown['BB']['count'] }} Transaksi</div>
                    </td>
                    <td style="text-align: center; padding: 8px; background: #fffbeb;">
                        <div style="font-size: 12px; font-weight: 900; color: #b45309;">Rp {{ number_format($statusBreakdown['BL']['total'], 0, ',', '.') }}</div>
                        <div style="font-size: 8px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 2px;">{{ $statusBreakdown['BL']['count'] }} Transaksi</div>
                    </td>
                    <td style="text-align: center; padding: 8px; background: #ecfdf5;">
                        <div style="font-size: 12px; font-weight: 900; color: #047857;">Rp {{ number_format($statusBreakdown['L']['total'], 0, ',', '.') }}</div>
                        <div style="font-size: 8px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 2px;">{{ $statusBreakdown['L']['count'] }} Transaksi</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">#</th>
                <th style="width: 100px;">No. Invoice</th>
                <th>Customer</th>
                <th class="text-right" style="width: 90px;">Total Amount</th>
                <th class="text-right" style="width: 70px;">Ongkir</th>
                <th class="text-right" style="width: 70px;">Diskon</th>
                <th class="text-right" style="width: 90px;">Terbayar</th>
                <th class="text-right" style="width: 90px;">Sisa Tagihan</th>
                <th class="text-center" style="width: 65px;">Status</th>
                <th class="text-center" style="width: 75px;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $idx => $inv)
                @php
                    $grandTotal = $inv->total_amount + $inv->shipping_cost - $inv->discount;
                    $remaining = $inv->remaining_balance;
                    $statusCode = $inv->payment_status_code;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-mono" style="font-weight:700;">{{ $inv->invoice_number }}</td>
                    <td>{{ $inv->customer->name ?? '-' }}</td>
                    <td class="text-right text-mono">{{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                    <td class="text-right text-mono">{{ number_format($inv->shipping_cost, 0, ',', '.') }}</td>
                    <td class="text-right text-mono">{{ number_format($inv->discount, 0, ',', '.') }}</td>
                    <td class="text-right text-mono">{{ number_format($inv->paid_amount, 0, ',', '.') }}</td>
                    <td class="text-right text-mono" style="font-weight:700; color: {{ $remaining > 0 ? '#dc2626' : '#047857' }};">
                        {{ number_format($remaining, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span class="badge badge-{{ strtolower($statusCode) }}">{{ $statusCode }}</span>
                    </td>
                    <td class="text-center">{{ $inv->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center; padding: 20px; color: #94a3b8;">
                        Tidak ada data invoice untuk filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($invoices->count() > 0)
    <div class="summary-bar">
        <table>
            <tr>
                <td class="label">Total Nilai Tagihan</td>
                <td class="value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                <td class="label" style="padding-left:25px;">Total Terbayar</td>
                <td class="value-highlight">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
                <td class="label" style="padding-left:25px;">Total Sisa Piutang</td>
                <td class="value" style="color:#f87171;">Rp {{ number_format($totalRemaining, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    @endif

    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh Sistem Workshop pada {{ $printDate }}.
    </div>
</body>
</html>
