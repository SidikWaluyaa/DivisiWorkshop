<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
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

        /* Table styling */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
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
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-stage {
            background-color: #e2e8f0;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .badge-overdue-est {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .badge-overdue-stage {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .badge-ontrack {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        /* Text utility classes */
        .text-mono {
            font-family: Courier, monospace;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-italic {
            font-style: italic;
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
                <h1 class="brand-title">Operational SLA Audit</h1>
                <p class="brand-subtitle">Dashboard Monitoring Overdue SLA</p>
            </td>
            <td class="meta-text">
                Dicetak pada: {{ $date }}<br>
                Total Data: <strong>{{ count($orders) }} SPK</strong>
            </td>
        </tr>
    </table>

    <!-- Filter Metadata -->
    <div class="filter-container">
        <div class="filter-title">Parameter Audit Saat Ini:</div>
        <table class="filter-grid" cellpadding="0" cellspacing="0">
            <tr>
                <td class="filter-label">Tahapan Divisi:</td>
                <td class="filter-value">{{ $filter['card'] }}</td>
                <td class="filter-label">Nomor SPK:</td>
                <td class="filter-value">{{ $filter['spk'] }}</td>
            </tr>
            <tr>
                <td class="filter-label">Nama Pelanggan:</td>
                <td class="filter-value">{{ $filter['customer'] }}</td>
                <td class="filter-label">Rentang Masuk:</td>
                <td class="filter-value">{{ $filter['date_range'] }}</td>
            </tr>
            <tr>
                <td class="filter-label">Status Estimasi:</td>
                <td class="filter-value">{{ $filter['estimation'] }}</td>
                <td colspan="2"></td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="12%">No. SPK</th>
                <th width="20%">Pelanggan</th>
                <th width="15%">Brand / Tipe Sepatu</th>
                <th width="12%">Tahap Aktif</th>
                <th width="11%">Masuk Stage</th>
                <th width="11%">Estimasi Selesai</th>
                <th width="10%">Hari Kelewat</th>
                <th width="19%">Keterangan Hambatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $wo)
                <tr>
                    <td class="text-mono">{{ $wo->spk_number }}</td>
                    <td style="font-weight: bold;">{{ $wo->customer_name }}</td>
                    <td>
                        {{ $wo->shoe_brand }}
                        @if($wo->shoe_type)
                            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">{{ $wo->shoe_type }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-stage">
                            {{ $wo->status->label() }}
                        </span>
                    </td>
                    <td>
                        {{ $wo->waktu ? $wo->waktu->translatedFormat('d M Y') : $wo->updated_at->translatedFormat('d M Y') }}
                        <div style="font-size: 8px; color: #64748b; margin-top: 1px;">
                            Pukul {{ $wo->waktu ? $wo->waktu->format('H:i') : $wo->updated_at->format('H:i') }}
                        </div>
                    </td>
                    <td>
                        {{ $wo->estimation_date && $wo->estimation_date->year > 2000 ? $wo->estimation_date->translatedFormat('d M Y') : 'Belum Set' }}
                    </td>
                    <td class="text-center">
                        @php $hasEstimation = $wo->estimation_date && $wo->estimation_date->year > 2000; @endphp
                        @if($wo->days_overdue > 0 && !$hasEstimation)
                            <span class="badge badge-overdue-stage">
                                {{ $wo->days_overdue }} Hari
                            </span>
                            <div style="font-size: 7px; color: #d97706; margin-top: 2px;">Dari Masuk Stage</div>
                        @elseif($wo->days_overdue > 0)
                            <span class="badge badge-overdue-est">
                                {{ $wo->days_overdue }} Hari
                            </span>
                            <div style="font-size: 7px; color: #dc2626; margin-top: 2px;">Dari Estimasi</div>
                        @else
                            <span class="badge badge-ontrack">
                                On Track
                            </span>
                        @endif
                    </td>
                    <td class="text-italic">
                        "{{ $wo->late_description ?: 'Tidak ada catatan hambatan khusus.' }}"
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 30px; font-weight: bold; color: #64748b; font-size: 12px;">
                        Audit Bersih! Tidak ada data antrean overdue SLA pada filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer Page Number -->
    <div class="footer">
        Halaman <script type="text/php">
            if (isset($pdf)) {
                echo $pdf->get_page_number() . ' dari ' . $pdf->get_page_count();
            }
        </script> - Laporan SLA Overdue Audit Workshop
    </div>

</body>
</html>
