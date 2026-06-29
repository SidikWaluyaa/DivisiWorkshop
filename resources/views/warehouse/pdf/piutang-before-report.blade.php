<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Piutang SPK Aktif (Before Selesai)</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        
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
        .kpi-title {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .kpi-value {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }
        .kpi-value-rose {
            font-size: 16px;
            font-weight: bold;
            color: #be123c;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #1e293b;
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
        
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-unpaid {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-partial {
            background-color: #fef3c7;
            color: #92400e;
        }

        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-mono {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }

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
                <h1 class="brand-title">Laporan Piutang SPK Aktif (Before Selesai)</h1>
                <p class="brand-subtitle">Sistem Workshop Logistik & Pemantauan Tagihan</p>
            </td>
            <td class="meta-text">
                Tanggal Cetak: {{ $date }}<br>
                Jumlah Invoice: <strong>{{ count($items) }} Records</strong>
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
                <td class="filter-label">Kata Kunci:</td>
                <td class="filter-value">{{ $filter['search'] }}</td>
            </tr>
            <tr>
                <td class="filter-label">Status Tagihan:</td>
                <td class="filter-value">{{ $filter['status_filter'] }}</td>
                <td colspan="2"></td>
            </tr>
        </table>
    </div>

    <!-- KPI Summary Section -->
    <table class="kpi-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="50%">
                <div class="kpi-card">
                    <div class="kpi-title">Total Invoice Aktif</div>
                    <div class="kpi-value">{{ count($items) }} Invoice</div>
                </div>
            </td>
            <td width="50%">
                <div class="kpi-card-rose">
                    <div class="kpi-title">Total Outstanding Piutang</div>
                    <div class="kpi-value-rose">Rp {{ number_format($total_outstanding, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="15%">No. Invoice / SPK</th>
                <th width="20%">Nama Pelanggan / WhatsApp</th>
                <th width="20%">Detail Sepatu</th>
                <th width="12%" class="text-right">Total Biaya</th>
                <th width="12%" class="text-right">Terbayar</th>
                <th width="12%" class="text-right">Piutang</th>
                <th width="9%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $invoice)
                <tr>
                    <td>
                        <span class="text-mono">{{ $invoice->invoice_number }}</span>
                        <div style="font-size: 8px; color: #64748b; margin-top: 3px;">
                            SPK: {{ $invoice->workOrders->pluck('spk_number')->implode(', ') }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: bold; color: #1e293b;">{{ $invoice->customer->name ?? 'N/A' }}</div>
                        <div style="font-size: 9px; color: #475569; font-family: monospace; margin-top: 2px;">
                            📞 {{ $invoice->customer->phone ?? 'N/A' }}
                        </div>
                    </td>
                    <td>
                        @foreach($invoice->workOrders as $wo)
                            <div style="margin-bottom: 4px;">
                                • {{ $wo->shoe_brand ?: '-' }} {{ $wo->shoe_type ?: '' }}
                                <span style="font-size: 8px; color: #64748b;">(Size: {{ $wo->shoe_size ?: '-' }}, Warna: {{ $wo->shoe_color ?: '-' }})</span>
                            </div>
                        @endforeach
                    </td>
                    <td class="text-right">Rp {{ number_format($invoice->total_amount + $invoice->shipping_cost - $invoice->discount, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold; color: #be123c;">Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $invoice->status === 'Belum Bayar' ? 'badge-unpaid' : 'badge-partial' }}">
                            {{ $invoice->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="color: #64748b; padding: 20px;">Tidak ada data piutang sebelum selesai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan Piutang SPK Aktif - Sistem Workshop Logistik &copy; {{ date('Y') }}
    </div>

</body>
</html>
