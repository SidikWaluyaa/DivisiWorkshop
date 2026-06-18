<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Pembayaran</title>
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
            border-bottom: 3px solid #3b82f6;
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
            background: #eff6ff;
            border: 1px solid #bfdbfe;
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
        .badge-type {
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .badge-verified {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .badge-pending {
            background: #fefce8;
            color: #a16207;
            border: 1px solid #fde68a;
        }
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
            font-size: 11px;
            color: #60a5fa;
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
        .notes-cell {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data Pembayaran</h1>
        <p class="subtitle">Sistem Workshop — Finance Report</p>
    </div>

    <div class="meta-bar">
        <span class="meta-item"><strong>Periode:</strong> {{ $periodLabel }}</span>
        <span class="meta-item"><strong>Filter Type:</strong> {{ $typeLabel }}</span>
        <span class="meta-item"><strong>Total Data:</strong> {{ $payments->count() }} record</span>
        <span class="meta-item"><strong>Dicetak:</strong> {{ $printDate }}</span>
    </div>

    {{-- Type Breakdown Cards --}}
    @if($payments->count() > 0)
    <div style="margin-bottom: 14px;">
        <div style="font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 6px;">Distribusi Type Pembayaran</div>
        <table style="margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="background: #2563eb; text-align: center; width: 16.66%;">BEFORE</th>
                    <th style="background: #047857; text-align: center; width: 16.66%;">AFTER</th>
                    <th style="background: #7c3aed; text-align: center; width: 16.66%;">TAMBAH JASA</th>
                    <th style="background: #b45309; text-align: center; width: 16.66%;">LUNAS AWAL</th>
                    <th style="background: #e11d48; text-align: center; width: 16.66%;">ONGKIR</th>
                    <th style="background: #db2777; text-align: center; width: 16.66%;">OTO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center; padding: 8px; background: #eff6ff;">
                        <div style="font-size: 11px; font-weight: 900; color: #2563eb;">Rp {{ number_format($typeBreakdown['BEFORE']['total'], 0, ',', '.') }}</div>
                        <div style="font-size: 8px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 2px;">{{ $typeBreakdown['BEFORE']['count'] }} Trx</div>
                    </td>
                    <td style="text-align: center; padding: 8px; background: #ecfdf5;">
                        <div style="font-size: 11px; font-weight: 900; color: #047857;">Rp {{ number_format($typeBreakdown['AFTER']['total'], 0, ',', '.') }}</div>
                        <div style="font-size: 8px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 2px;">{{ $typeBreakdown['AFTER']['count'] }} Trx</div>
                    </td>
                    <td style="text-align: center; padding: 8px; background: #f5f3ff;">
                        <div style="font-size: 11px; font-weight: 900; color: #7c3aed;">Rp {{ number_format($typeBreakdown['TAMBAH_JASA']['total'], 0, ',', '.') }}</div>
                        <div style="font-size: 8px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 2px;">{{ $typeBreakdown['TAMBAH_JASA']['count'] }} Trx</div>
                    </td>
                    <td style="text-align: center; padding: 8px; background: #fffbeb;">
                        <div style="font-size: 11px; font-weight: 900; color: #b45309;">Rp {{ number_format($typeBreakdown['LUNAS_AWAL']['total'], 0, ',', '.') }}</div>
                        <div style="font-size: 8px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 2px;">{{ $typeBreakdown['LUNAS_AWAL']['count'] }} Trx</div>
                    </td>
                    <td style="text-align: center; padding: 8px; background: #fff1f2;">
                        <div style="font-size: 11px; font-weight: 900; color: #e11d48;">Rp {{ number_format($typeBreakdown['ONGKIR']['total'], 0, ',', '.') }}</div>
                        <div style="font-size: 8px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 2px;">{{ $typeBreakdown['ONGKIR']['count'] }} Trx</div>
                    </td>
                    <td style="text-align: center; padding: 8px; background: #fdf2f8;">
                        <div style="font-size: 11px; font-weight: 900; color: #db2777;">Rp {{ number_format($typeBreakdown['OTO']['total'], 0, ',', '.') }}</div>
                        <div style="font-size: 8px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 2px;">{{ $typeBreakdown['OTO']['count'] }} Trx</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">#</th>
                <th style="width: 100px;">No. Invoice</th>
                <th class="text-right" style="width: 95px;">Jumlah (Rp)</th>
                <th class="text-center" style="width: 70px;">Tgl Bayar</th>
                <th class="text-center" style="width: 80px;">Type</th>
                <th class="text-center" style="width: 60px;">Verifikasi</th>
                <th>Catatan</th>
                <th style="width: 90px;">Dibuat Oleh</th>
                <th class="text-center" style="width: 75px;">Waktu Input</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $idx => $pay)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-mono" style="font-weight:700;">{{ $pay->invoice->invoice_number ?? '-' }}</td>
                    <td class="text-right text-mono" style="font-weight:700;">{{ number_format($pay->amount, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $pay->payment_date ? $pay->payment_date->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">
                        <span class="badge badge-type">{{ $pay->type ?? '-' }}</span>
                    </td>
                    <td class="text-center">
                        @if($pay->verified)
                            <span class="badge badge-verified">✓ Ya</span>
                        @else
                            <span class="badge badge-pending">Pending</span>
                        @endif
                    </td>
                    <td class="notes-cell">{{ $pay->notes ?? '-' }}</td>
                    <td>{{ $pay->creator->name ?? '-' }}</td>
                    <td class="text-center">{{ $pay->created_at ? $pay->created_at->format('d/m/Y H:i') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding: 20px; color: #94a3b8;">
                        Tidak ada data pembayaran untuk filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($payments->count() > 0)
    <div class="summary-bar">
        <table>
            <tr>
                <td class="label">Total Pembayaran</td>
                <td class="value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                <td class="label" style="padding-left:25px;">Jumlah Transaksi</td>
                <td class="value">{{ $payments->count() }}</td>
            </tr>
        </table>
    </div>
    @endif

    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh Sistem Workshop pada {{ $printDate }}.
    </div>
</body>
</html>
