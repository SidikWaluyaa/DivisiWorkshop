<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Audit SLA Tahap Produksi</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        
        /* Header & Brand styling */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .brand-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
            margin-bottom: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .meta-text {
            font-size: 10px;
            color: #475569;
            text-align: right;
        }

        /* Filter block styling */
        .filter-container {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
        }
        .filter-title {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .filter-grid {
            width: 100%;
        }
        .filter-label {
            font-weight: bold;
            color: #475569;
            width: 15%;
        }
        .filter-value {
            color: #1e293b;
            width: 35%;
        }

        /* KPI Blocks Table */
        .kpi-table {
            width: 100%;
            margin-bottom: 15px;
            border-spacing: 10px 0;
            margin-left: -10px;
            margin-right: -10px;
        }
        .kpi-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }
        .kpi-card-rose {
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }
        .kpi-card-amber {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }
        .kpi-title {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .kpi-value {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }
        .kpi-value-rose {
            font-size: 18px;
            font-weight: bold;
            color: #be123c;
        }
        .kpi-value-amber {
            font-size: 18px;
            font-weight: bold;
            color: #b45309;
        }

        /* Table styling */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 30px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #0f172a;
            text-align: left;
        }
        .data-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .data-table tr.overdue-row {
            background-color: #fff5f5;
        }
        .data-table tr.overdue-row td {
            border-top: 1px solid #fecdd3;
            border-bottom: 1px solid #fecdd3;
        }
        .data-table tr.upcoming-row {
            background-color: #fffbeb;
        }
        .data-table tr.upcoming-row td {
            border-top: 1px solid #fde68a;
            border-bottom: 1px solid #fde68a;
        }
        .data-table tr:nth-child(even):not(.overdue-row):not(.upcoming-row) {
            background-color: #f8fafc;
        }
        .data-table tr {
            page-break-inside: avoid;
        }

        /* Badge and status styles */
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-overdue {
            background-color: #ef4444;
            color: #ffffff;
        }
        .badge-upcoming {
            background-color: #f59e0b;
            color: #ffffff;
        }
        .badge-ontrack {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        .badge-days-overdue {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .badge-days-upcoming {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .badge-days-normal {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        /* Text utility classes */
        .text-mono {
            font-family: Courier, monospace;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        
        /* Sign-off section */
        .signoff-table {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signoff-cell {
            width: 33.3%;
            text-align: center;
        }
        .signoff-title {
            font-weight: bold;
            color: #475569;
            margin-bottom: 50px;
        }
        .signoff-line {
            width: 60%;
            margin: 0 auto;
            border-bottom: 1px solid #94a3b8;
            margin-bottom: 5px;
        }
        .signoff-name {
            font-weight: bold;
            color: #0f172a;
        }
        .signoff-role {
            font-size: 9px;
            color: #64748b;
        }

        /* Footer styling */
        .footer {
            position: fixed;
            bottom: -10px;
            left: 0px;
            right: 0px;
            height: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <h1 class="brand-title">Laporan Audit SLA Tahap Produksi</h1>
                <p class="brand-subtitle">Sistem Workshop Logistik & Monitoring Estimasi</p>
            </td>
            <td class="meta-text">
                Tanggal Cetak: {{ $date }}<br>
                Jumlah SPK: <strong>{{ count($items) }} Records</strong>
            </td>
        </tr>
    </table>

    <!-- Filter Metadata -->
    <div class="filter-container">
        <div class="filter-title">Parameter Filter Laporan:</div>
        <table class="filter-grid" cellpadding="0" cellspacing="0">
            <tr>
                <td class="filter-label">Periode:</td>
                <td class="filter-value">{{ $period['start'] }} s/d {{ $period['end'] }}</td>
                <td class="filter-label">Pencarian Kata Kunci:</td>
                <td class="filter-value">{{ $filter['search'] }}</td>
            </tr>
            <tr>
                <td class="filter-label">Kategori Filter:</td>
                <td class="filter-value">{{ $filter['status_filter'] }}</td>
                <td colspan="2"></td>
            </tr>
        </table>
    </div>

    <!-- KPI Summary Section -->
    <table class="kpi-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="33.3%">
                <div class="kpi-card">
                    <div class="kpi-title">Total Antrean Produksi</div>
                    <div class="kpi-value">{{ $summary['total_items_in_production'] }} SPK</div>
                </div>
            </td>
            <td width="33.3%">
                <div class="kpi-card-rose">
                    <div class="kpi-title">Terlewat Estimasi (Overdue)</div>
                    <div class="kpi-value-rose">{{ $summary['overdue_items_count'] }} SPK</div>
                </div>
            </td>
            <td width="33.3%">
                <div class="kpi-card-amber">
                    <div class="kpi-title">Mendekati Estimasi (≤ 2 Hari)</div>
                    <div class="kpi-value-amber">{{ $summary['upcoming_items_count'] }} SPK</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="15%">No. SPK</th>
                <th width="20%">Nama Pelanggan</th>
                <th width="25%">Detail Sepatu</th>
                <th width="18%">Estimasi Selesai</th>
                <th width="10%" class="text-center">Sisa Waktu</th>
                <th width="12%" class="text-center">Status / SLA Badge</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr class="{{ $item['is_overdue'] ? 'overdue-row' : ($item['is_upcoming'] ? 'upcoming-row' : '') }}">
                    <td class="text-mono">{{ $item['spk_number'] }}</td>
                    <td style="font-weight: bold;">{{ $item['customer_name'] }}</td>
                    <td>{{ $item['shoe_brand'] }} {{ $item['shoe_type'] }}</td>
                    <td>{{ $item['estimation_date_formatted'] }}</td>
                    <td class="text-center">
                        @if($item['has_estimation'])
                            @if($item['is_overdue'])
                                <span class="badge badge-days-overdue">
                                    Kelewat {{ $item['days_diff'] }} Hari
                                </span>
                            @elseif($item['is_upcoming'])
                                <span class="badge badge-days-upcoming">
                                    {{ $item['days_diff'] }} Hari Lagi
                                </span>
                            @else
                                <span class="badge badge-days-normal">
                                    {{ $item['days_diff'] }} Hari Lagi
                                </span>
                            @endif
                        @else
                            <span class="text-italic">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item['is_overdue'])
                            <span class="badge badge-overdue">OVERDUE</span>
                        @elseif($item['is_upcoming'])
                            <span class="badge badge-upcoming">DUE SOON</span>
                        @else
                            <span class="badge badge-ontrack">ON TRACK</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 30px; font-weight: bold; color: #64748b; font-size: 12px;">
                        Tidak ada data antrean produksi pada filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Sign-off Section -->
    <table class="signoff-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="signoff-cell">
                <div class="signoff-title">Dibuat Oleh,</div>
                <div class="signoff-line"></div>
                <div class="signoff-name">Kepala Gudang</div>
                <div class="signoff-role">Logistik & Inventory</div>
            </td>
            <td class="signoff-cell">
                <!-- Spacer for layout -->
            </td>
            <td class="signoff-cell">
                <div class="signoff-title">Disetujui Oleh,</div>
                <div class="signoff-line"></div>
                <div class="signoff-name">Operations Manager</div>
                <div class="signoff-role">Sistem Workshop</div>
            </td>
        </tr>
    </table>

    <!-- Footer Page Number -->
    <div class="footer">
        Halaman <script type="text/php">
            if (isset($pdf)) {
                echo $pdf->get_page_number() . ' dari ' . $pdf->get_page_count();
            }
        </script> - Laporan Audit SLA Produksi
    </div>

</body>
</html>
