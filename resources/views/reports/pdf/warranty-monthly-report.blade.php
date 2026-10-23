<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Garansi - {{ $periodLabel }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 12mm 15mm 12mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        /* Header Table */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1a3b34;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .brand-title {
            font-size: 16px;
            font-weight: 800;
            color: #1a3b34;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }
        .brand-subtitle {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
            margin-bottom: 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .report-title {
            font-size: 14px;
            font-weight: 700;
            color: #22AF85;
            margin-top: 4px;
            margin-bottom: 0;
            text-transform: uppercase;
        }
        .meta-text {
            font-size: 9px;
            color: #475569;
            text-align: right;
            line-height: 1.4;
        }
        .meta-text strong {
            color: #1a3b34;
        }

        /* KPI Cards Grid */
        .kpi-table {
            width: 100%;
            margin-bottom: 14px;
            border-spacing: 6px 0;
            margin-left: -6px;
            margin-right: -6px;
        }
        .kpi-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            text-align: center;
        }
        .kpi-title {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 4px;
        }
        .kpi-value {
            font-size: 18px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 2px;
        }
        .kpi-sub {
            font-size: 8px;
            color: #64748b;
        }

        .kpi-emerald { border-left: 3px solid #22AF85; }
        .kpi-emerald .kpi-title { color: #1a3b34; }
        .kpi-emerald .kpi-value { color: #22AF85; }

        .kpi-blue { border-left: 3px solid #0284c7; }
        .kpi-blue .kpi-title { color: #0369a1; }
        .kpi-blue .kpi-value { color: #0284c7; }

        .kpi-amber { border-left: 3px solid #f59e0b; }
        .kpi-amber .kpi-title { color: #d97706; }
        .kpi-amber .kpi-value { color: #b45309; }

        .kpi-teal { border-left: 3px solid #0d9488; }
        .kpi-teal .kpi-title { color: #0f766e; }
        .kpi-teal .kpi-value { color: #0d9488; }

        /* Section Title */
        .section-header {
            background-color: #f0fdf4;
            border-left: 3px solid #22AF85;
            padding: 5px 8px;
            margin-top: 10px;
            margin-bottom: 6px;
            font-size: 10px;
            font-weight: 700;
            color: #1a3b34;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-count {
            float: right;
            font-size: 9px;
            font-weight: 600;
            color: #15803d;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #1a3b34;
            color: #ffffff;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 5px 6px;
            text-align: left;
            border: 1px solid #1a3b34;
        }
        .data-table td {
            font-size: 8.5px;
            padding: 4.5px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
        }
        .badge-open {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .badge-finished {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        /* Utility */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .text-muted { color: #64748b; }
        .empty-row {
            text-align: center;
            padding: 12px !important;
            color: #94a3b8;
            font-style: italic;
        }

        /* Footer */
        .report-footer {
            margin-top: 15px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            font-size: 8px;
            color: #94a3b8;
            width: 100%;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <h1 class="brand-title">SHOE WORKSHOP INDONESIA</h1>
                <p class="brand-subtitle">Divisi Workshop & Customer Care • Sistem Manajemen Garansi</p>
                <div class="report-title">LAPORAN REKAPITULASI KLAIM GARANSI (BULANAN)</div>
            </td>
            <td style="width: 40%; vertical-align: top;" class="meta-text">
                <div>Periode Laporan: <strong>{{ $periodLabel }}</strong></div>
                <div>Tanggal Cetak: <strong>{{ $printedAt }}</strong></div>
                <div>Dicetak Oleh: <strong>{{ $printedBy }}</strong></div>
                <div>Total Klaim Garansi Aktif: <strong>{{ $activePending }} SPK Pending</strong></div>
            </td>
        </tr>
    </table>

    {{-- Kartu Ringkasan KPI --}}
    <table class="kpi-table">
        <tr>
            <td style="width: 25%;">
                <div class="kpi-card kpi-emerald">
                    <div class="kpi-title">Klaim Garansi Masuk</div>
                    <div class="kpi-value">{{ $totalMasuk }}</div>
                    <div class="kpi-sub">{{ $masukSelesai }} Selesai • {{ $masukOpen }} Masih Open</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card kpi-blue">
                    <div class="kpi-title">Garansi Selesai</div>
                    <div class="kpi-value">{{ $totalSelesai }}</div>
                    <div class="kpi-sub">Total Dituntaskan di Periode Ini</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card kpi-amber">
                    <div class="kpi-title">Klaim Open Saat Ini</div>
                    <div class="kpi-value">{{ $activePending }}</div>
                    <div class="kpi-sub">Klaim Masih Dalam Pengerjaan</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card kpi-teal">
                    <div class="kpi-title">Rasio Tuntas Periode Ini</div>
                    <div class="kpi-value">{{ $completionRate }}%</div>
                    <div class="kpi-sub">{{ $masukSelesai }} dari {{ $totalMasuk }} Masuk Selesai</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- SEKSI 1: TABEL KLAIM GARANSI MASUK PADA PERIODE INI --}}
    <div class="section-header">
        <span>1. Rincian Klaim Garansi Masuk Pada Periode ({{ $periodLabel }})</span>
        <span class="section-count">Total: {{ $totalMasuk }} SPK</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 12%;">No. SPK Asli</th>
                <th style="width: 13%;">No. SPK Garansi</th>
                <th style="width: 14%;">Pelanggan</th>
                <th style="width: 15%;">Merek / Tipe Sepatu</th>
                <th>Keluhan Pelanggan / Catatan Garansi</th>
                <th style="width: 10%; text-align: center;">Tgl Klaim</th>
                <th style="width: 9%; text-align: center;">Status Terkini</th>
                <th style="width: 10%;">PIC Penerima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($itemsMasuk as $index => $item)
                @php
                    $wo = $item->workOrder;
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $wo->spk_number ?? '-' }}</td>
                    <td class="font-bold" style="color: #0d9488;">{{ $item->garansi_spk_number ?? '-' }}</td>
                    <td>
                        <div class="font-bold">{{ $wo->customer_name ?? '-' }}</div>
                        <div class="text-muted" style="font-size: 7.5px;">{{ $wo->customer_phone ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="font-bold">{{ $wo->shoe_brand ?? '-' }}</div>
                        <div class="text-muted" style="font-size: 7.5px;">{{ $wo->shoe_type ?? '-' }} ({{ $wo->shoe_color ?? '-' }})</div>
                    </td>
                    <td>
                        <div>{{ Str::limit($item->description, 100) }}</div>
                        @if($item->penggunaan)
                            <div class="text-muted" style="font-size: 7.5px; margin-top: 2px;">
                                <em>Penggunaan:</em> {{ $item->penggunaan }}
                            </div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center">
                        @if($item->status === 'FINISHED')
                            <span class="badge badge-finished">SELESAI</span>
                        @else
                            <span class="badge badge-open">PROSES</span>
                        @endif
                    </td>
                    <td>{{ $item->creator->name ?? 'CS Staff' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-row">Tidak ada klaim garansi yang masuk pada periode {{ $periodLabel }}.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SEKSI 2: TABEL KLAIM GARANSI DISELESAIKAN PADA PERIODE INI --}}
    <div class="section-header" style="margin-top: 15px;">
        <span>2. Rincian Klaim Garansi Yang Berhasil Diselesaikan Pada Periode ({{ $periodLabel }})</span>
        <span class="section-count">Total: {{ $totalSelesai }} SPK</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 13%;">No. SPK Garansi</th>
                <th style="width: 12%;">No. SPK Asli</th>
                <th style="width: 15%;">Pelanggan</th>
                <th style="width: 15%;">Merek / Tipe Sepatu</th>
                <th style="width: 11%; text-align: center;">Tgl Masuk Klaim</th>
                <th style="width: 11%; text-align: center;">Tgl Selesai</th>
                <th style="width: 9%; text-align: center;">Durasi Kerja</th>
                <th>Diselesaikan Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($itemsSelesai as $index => $item)
                @php
                    $wo = $item->workOrder;
                    $leadTime = '-';
                    if ($item->created_at && $item->finished_at) {
                        $diffHours = $item->created_at->diffInHours($item->finished_at);
                        if ($diffHours < 24) {
                            $leadTime = $diffHours . ' Jam';
                        } else {
                            $diffDays = $item->created_at->diffInDays($item->finished_at);
                            $leadTime = $diffDays . ' Hari';
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td class="font-bold" style="color: #0d9488;">{{ $item->garansi_spk_number ?? '-' }}</td>
                    <td>{{ $wo->spk_number ?? '-' }}</td>
                    <td>{{ $wo->customer_name ?? '-' }}</td>
                    <td>
                        <div class="font-bold">{{ $wo->shoe_brand ?? '-' }}</div>
                        <div class="text-muted" style="font-size: 7.5px;">{{ $wo->shoe_type ?? '-' }}</div>
                    </td>
                    <td class="text-center">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center font-bold" style="color: #047857;">{{ $item->finished_at ? $item->finished_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center font-bold">{{ $leadTime }}</td>
                    <td class="font-bold">{{ $item->finisher->name ?? 'Teknisi Workshop' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-row">Tidak ada klaim garansi yang diselesaikan pada periode {{ $periodLabel }}.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <table class="report-footer">
        <tr>
            <td style="width: 50%;">
                Dokumen resmi rekapitulasi layanan purnajual garansi • Shoe Workshop Indonesia.
            </td>
            <td style="width: 50%; text-align: right;">
                Halaman dicetak secara otomatis dari Sistem Workshop (info.shoeworkshop.id)
            </td>
        </tr>
    </table>

</body>
</html>
