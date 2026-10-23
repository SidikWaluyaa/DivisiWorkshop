<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Revisi - {{ $periodLabel }}</title>
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
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .brand-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
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
            color: #3b82f6;
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
            color: #0f172a;
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

        .kpi-blue { border-left: 3px solid #3b82f6; }
        .kpi-blue .kpi-title { color: #2563eb; }
        .kpi-blue .kpi-value { color: #1d4ed8; }

        .kpi-green { border-left: 3px solid #10b981; }
        .kpi-green .kpi-title { color: #059669; }
        .kpi-green .kpi-value { color: #047857; }

        .kpi-amber { border-left: 3px solid #f59e0b; }
        .kpi-amber .kpi-title { color: #d97706; }
        .kpi-amber .kpi-value { color: #b45309; }

        .kpi-purple { border-left: 3px solid #8b5cf6; }
        .kpi-purple .kpi-title { color: #7c3aed; }
        .kpi-purple .kpi-value { color: #6d28d9; }

        /* Section Title */
        .section-header {
            background-color: #f1f5f9;
            border-left: 3px solid #0f172a;
            padding: 5px 8px;
            margin-top: 10px;
            margin-bottom: 6px;
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-count {
            float: right;
            font-size: 9px;
            font-weight: 600;
            color: #475569;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 5px 6px;
            text-align: left;
            border: 1px solid #1e293b;
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
        .badge-qc {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-prod {
            background-color: #e0e7ff;
            color: #3730a3;
        }
        .badge-selesai {
            background-color: #e2e8f0;
            color: #334155;
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
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <h1 class="brand-title">SHOE WORKSHOP INDONESIA</h1>
                <p class="brand-subtitle">Divisi Workshop & Quality Control • Sistem Manajemen Revisi</p>
                <div class="report-title">LAPORAN REKAPITULASI REVISI TEKNIS (BULANAN)</div>
            </td>
            <td style="width: 40%; vertical-align: top;" class="meta-text">
                <div>Periode Laporan: <strong>{{ $periodLabel }}</strong></div>
                <div>Tanggal Cetak: <strong>{{ $printedAt }}</strong></div>
                <div>Dicetak Oleh: <strong>{{ $printedBy }}</strong></div>
                <div>Total Revisi Aktif Saat Ini: <strong>{{ $activePending }} SPK Pending</strong></div>
            </td>
        </tr>
    </table>

    {{-- Kartu Ringkasan KPI --}}
    <table class="kpi-table">
        <tr>
            <td style="width: 25%;">
                <div class="kpi-card kpi-blue">
                    <div class="kpi-title">SPK Revisi Masuk</div>
                    <div class="kpi-value">{{ $totalMasuk }}</div>
                    <div class="kpi-sub">{{ $masukSelesai }} Selesai • {{ $masukOpen }} Masih Open</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card kpi-green">
                    <div class="kpi-title">SPK Revisi Selesai</div>
                    <div class="kpi-value">{{ $totalSelesai }}</div>
                    <div class="kpi-sub">Total Diselesaikan di Periode Ini</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card kpi-amber">
                    <div class="kpi-title">Tunggakan / Open Saat Ini</div>
                    <div class="kpi-value">{{ $activePending }}</div>
                    <div class="kpi-sub">SPK Belum Tuntas di Bengkel</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card kpi-purple">
                    <div class="kpi-title">Rasio Tuntas Periode Ini</div>
                    <div class="kpi-value">{{ $completionRate }}%</div>
                    <div class="kpi-sub">{{ $masukSelesai }} dari {{ $totalMasuk }} Masuk Tuntas</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- SEKSI 1: TABEL SPK MASUK PADA PERIODE INI --}}
    <div class="section-header">
        <span>1. Rincian SPK Revisi Masuk Pada Periode ({{ $periodLabel }})</span>
        <span class="section-count">Total: {{ $totalMasuk }} SPK</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 13%;">No. SPK</th>
                <th style="width: 14%;">Pelanggan</th>
                <th style="width: 15%;">Merek / Tipe Sepatu</th>
                <th style="width: 10%; text-align: center;">Asal Tahap</th>
                <th>Deskripsi / Keluhan Revisi</th>
                <th style="width: 10%; text-align: center;">Tgl Masuk</th>
                <th style="width: 9%; text-align: center;">Status Terkini</th>
                <th style="width: 10%;">Pelapor (PIC)</th>
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
                    <td>{{ $wo->customer_name ?? '-' }}</td>
                    <td>
                        <div class="font-bold">{{ $wo->shoe_brand ?? '-' }}</div>
                        <div class="text-muted" style="font-size: 7.5px;">{{ $wo->shoe_type ?? '-' }} ({{ $wo->shoe_color ?? '-' }})</div>
                    </td>
                    <td class="text-center">
                        @if($item->origin_status === 'QC')
                            <span class="badge badge-qc">QC Reject</span>
                        @elseif($item->origin_status === 'PRODUCTION')
                            <span class="badge badge-prod">Produksi</span>
                        @else
                            <span class="badge badge-selesai">Selesai/Fin</span>
                        @endif
                    </td>
                    <td>{{ Str::limit($item->description, 100) }}</td>
                    <td class="text-center">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center">
                        @if($item->status === 'FINISHED')
                            <span class="badge badge-finished">SELESAI</span>
                        @else
                            <span class="badge badge-open">DALAM PROSES</span>
                        @endif
                    </td>
                    <td>{{ $item->creator->name ?? 'Sistem' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-row">Tidak ada SPK revisi yang masuk pada periode {{ $periodLabel }}.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SEKSI 2: TABEL SPK YANG DISELESAIKAN PADA PERIODE INI --}}
    <div class="section-header" style="margin-top: 15px;">
        <span>2. Rincian SPK Revisi Yang Berhasil Diselesaikan Pada Periode ({{ $periodLabel }})</span>
        <span class="section-count">Total: {{ $totalSelesai }} SPK</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 13%;">No. SPK</th>
                <th style="width: 15%;">Pelanggan</th>
                <th style="width: 16%;">Merek / Tipe Sepatu</th>
                <th style="width: 11%; text-align: center;">Tgl Pengajuan</th>
                <th style="width: 11%; text-align: center;">Tgl Selesai</th>
                <th style="width: 10%; text-align: center;">Durasi Kerja</th>
                <th style="width: 10%;">Pelapor (QC/PIC)</th>
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
                    <td class="font-bold">{{ $wo->spk_number ?? '-' }}</td>
                    <td>{{ $wo->customer_name ?? '-' }}</td>
                    <td>
                        <div class="font-bold">{{ $wo->shoe_brand ?? '-' }}</div>
                        <div class="text-muted" style="font-size: 7.5px;">{{ $wo->shoe_type ?? '-' }}</div>
                    </td>
                    <td class="text-center">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center font-bold" style="color: #047857;">{{ $item->finished_at ? $item->finished_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center font-bold">{{ $leadTime }}</td>
                    <td>{{ $item->creator->name ?? '-' }}</td>
                    <td class="font-bold">{{ $item->resolver->name ?? 'Teknisi Workshop' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-row">Tidak ada SPK revisi yang selesai pada periode {{ $periodLabel }}.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <table class="report-footer">
        <tr>
            <td style="width: 50%;">
                Dokumen resmi rekapitulasi performa teknis divisi workshop • Shoe Workshop Indonesia.
            </td>
            <td style="width: 50%; text-align: right;">
                Halaman dicetak secara otomatis dari Sistem Workshop (info.shoeworkshop.id)
            </td>
        </tr>
    </table>

</body>
</html>
