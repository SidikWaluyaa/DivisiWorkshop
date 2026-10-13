<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Analisis Warehouse QC</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.35;
            margin: 0;
            padding: 10px 15px;
        }
        
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
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .meta-text {
            font-size: 9px;
            color: #475569;
            text-align: right;
        }

        /* Banner Summary Cards */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            margin-bottom: 15px;
        }
        .card-cell {
            padding: 8px 6px;
            text-align: center;
            border-radius: 6px;
            color: #ffffff;
        }
        .card-label {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .card-value {
            font-size: 16px;
            font-weight: 800;
        }
        .card-sub {
            font-size: 8.5px;
            font-weight: 600;
            margin-top: 2px;
        }

        .bg-green { background-color: #16a34a; }
        .bg-yellow { background-color: #ca8a04; color: #ffffff; }
        .bg-red { background-color: #dc2626; }
        .bg-teal { background-color: #0d9488; }
        .bg-blue { background-color: #2563eb; }

        /* Tables styling */
        .section-title {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 14px;
            margin-bottom: 6px;
            border-left: 4px solid #16a34a;
            padding-left: 6px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8.5px;
        }
        .data-table th {
            background-color: #15803d;
            color: #ffffff;
            font-weight: 700;
            text-align: center;
            padding: 5px 4px;
            border: 1px solid #166534;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 4.5px 4px;
            border: 1px solid #cbd5e1;
            text-align: center;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .data-table .total-row td {
            background-color: #dcfce7 !important;
            font-weight: 800;
            color: #14532d;
            border-top: 2px solid #166534;
        }
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .font-bold { font-weight: bold; }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="brand-title">SHOE WORKSHOP</div>
                <div class="brand-subtitle">LAPORAN ANALISIS WAREHOUSE QC (QC BEFORE) — DIVISI CC</div>
            </td>
            <td style="width: 40%;" class="meta-text">
                <div><strong>Periode:</strong> {{ $data['period']['start_formatted'] }} s/d {{ $data['period']['end_formatted'] }}</div>
                <div><strong>Dicetak:</strong> {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
            </td>
        </tr>
    </table>

    <!-- Banner Summary Cards -->
    <table class="summary-table">
        <tr>
            <td class="card-cell bg-green" style="width: 17%;">
                <div class="card-label">Sepatu Masuk</div>
                <div class="card-value">{{ number_format($data['summary']['total_masuk']) }}</div>
                <div class="card-sub">Unit Terdaftar</div>
            </td>
            <td class="card-cell bg-green" style="width: 17%;">
                <div class="card-label">Lolos (Murni)</div>
                <div class="card-value">{{ number_format($data['summary']['total_lolos']) }}</div>
                <div class="card-sub">{{ $data['summary']['pct_lolos'] }}%</div>
            </td>
            <td class="card-cell bg-yellow" style="width: 18%;">
                <div class="card-label">Lolos Catatan</div>
                <div class="card-value">{{ number_format($data['summary']['total_lolos_catatan']) }}</div>
                <div class="card-sub">{{ $data['summary']['pct_lolos_catatan'] }}%</div>
            </td>
            <td class="card-cell bg-red" style="width: 15%;">
                <div class="card-label">Tolak (Reject)</div>
                <div class="card-value">{{ number_format($data['summary']['total_tolak']) }}</div>
                <div class="card-sub">{{ $data['summary']['pct_tolak'] }}%</div>
            </td>
            <td class="card-cell bg-teal" style="width: 16%;">
                <div class="card-label">Total QC</div>
                <div class="card-value">{{ number_format($data['summary']['total_qc']) }}</div>
                <div class="card-sub">Selesai Diperiksa</div>
            </td>
            <td class="card-cell bg-blue" style="width: 17%;">
                <div class="card-label">Belum Di-QC</div>
                <div class="card-value">{{ number_format($data['summary']['belum_qc']) }}</div>
                <div class="card-sub">Sisa Antrean</div>
            </td>
        </tr>
    </table>

    <!-- Section 1: CS QC Contribution -->
    <div class="section-title">Rekapan Kontribusi CS QC</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 22%;" class="text-left">Nama CS (Kode)</th>
                <th style="width: 11%;">Jml Sepatu Masuk</th>
                <th style="width: 13%;">Lolos</th>
                <th style="width: 14%;">Lolos dg Catatan</th>
                <th style="width: 12%;">Tolak</th>
                <th style="width: 14%;">Jumlah Ter-QC</th>
                <th style="width: 14%;">Jml Blm Ter-QC</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['cs_table']['rows'] as $row)
                <tr>
                    <td class="text-left font-bold">{{ $row['display_name'] }}</td>
                    <td>{{ $row['masuk'] }}</td>
                    <td>
                        {{ $row['lolos'] }}
                        <span style="color: #64748b; font-size: 7.5px;">({{ $row['pct_lolos'] }}%)</span>
                    </td>
                    <td>
                        {{ $row['lolos_catatan'] }}
                        <span style="color: #64748b; font-size: 7.5px;">({{ $row['pct_lolos_catatan'] }}%)</span>
                    </td>
                    <td>
                        {{ $row['tolak'] }}
                        <span style="color: #64748b; font-size: 7.5px;">({{ $row['pct_tolak'] }}%)</span>
                    </td>
                    <td>
                        <strong>{{ $row['ter_qc'] }}</strong>
                        <span style="color: #64748b; font-size: 7.5px;">({{ $row['pct_ter_qc'] }}%)</span>
                    </td>
                    <td>
                        {{ $row['blm_qc'] }}
                        <span style="color: #64748b; font-size: 7.5px;">({{ $row['pct_blm_qc'] }}%)</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 10px; color: #64748b;">Tidak ada data kontribusi CS pada periode ini.</td>
                </tr>
            @endforelse

            @if(!empty($data['cs_table']['total']))
                @php $tot = $data['cs_table']['total']; @endphp
                <tr class="total-row">
                    <td class="text-left">{{ $tot['display_name'] }}</td>
                    <td>{{ $tot['masuk'] }}</td>
                    <td>{{ $tot['lolos'] }} ({{ $tot['pct_lolos'] }}%)</td>
                    <td>{{ $tot['lolos_catatan'] }} ({{ $tot['pct_lolos_catatan'] }}%)</td>
                    <td>{{ $tot['tolak'] }} ({{ $tot['pct_tolak'] }}%)</td>
                    <td>{{ $tot['ter_qc'] }} ({{ $tot['pct_ter_qc'] }}%)</td>
                    <td>{{ $tot['blm_qc'] }} ({{ $tot['pct_blm_qc'] }}%)</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Page Break for Daily Table -->
    <div class="page-break"></div>

    <!-- Section 2: Daily QC Before -->
    <div class="section-title">Rekapan QC Before Harian</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;" class="text-left">Tanggal</th>
                <th style="width: 15%;">Jumlah Sepatu Masuk</th>
                <th style="width: 15%;">Lolos</th>
                <th style="width: 15%;">Lolos dg Catatan</th>
                <th style="width: 15%;">Tolak</th>
                <th style="width: 15%;">Jumlah Ter-QC</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['daily_table']['rows'] as $d)
                <tr>
                    <td class="text-left">{{ $d['tanggal'] }}</td>
                    <td>{{ $d['masuk'] }}</td>
                    <td>{{ $d['lolos'] }}</td>
                    <td>{{ $d['lolos_catatan'] }}</td>
                    <td>{{ $d['tolak'] }}</td>
                    <td><strong>{{ $d['ter_qc'] }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 10px; color: #64748b;">Tidak ada data harian pada periode ini.</td>
                </tr>
            @endforelse

            @if(!empty($data['daily_table']['total']))
                @php $totD = $data['daily_table']['total']; @endphp
                <tr class="total-row">
                    <td class="text-left">{{ $totD['tanggal'] }}</td>
                    <td>{{ $totD['masuk'] }}</td>
                    <td>{{ $totD['lolos'] }}</td>
                    <td>{{ $totD['lolos_catatan'] }}</td>
                    <td>{{ $totD['tolak'] }}</td>
                    <td>{{ $totD['ter_qc'] }}</td>
                </tr>
            @endif
        </tbody>
    </table>

</body>
</html>
