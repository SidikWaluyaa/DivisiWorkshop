<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi SPK CS</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 15mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
        }
        /* Header section */
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #22AF85;
            padding-bottom: 10px;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 5px 0 0 0;
        }
        .header-meta {
            float: right;
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }

        /* Metrics cards */
        .metrics-container {
            margin-bottom: 20px;
            width: 100%;
        }
        .metric-card {
            width: 48%;
            display: inline-block;
            background: #f8fafc;
            border-left: 5px solid #22AF85;
            padding: 10px 15px;
            box-sizing: border-box;
            border-radius: 4px;
        }
        .metric-card.right {
            float: right;
        }
        .metric-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .metric-value {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }

        /* Helpers & Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 9999px;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-warning { background-color: #fef9c3; color: #a16207; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; }
        .badge-gray { background-color: #f1f5f9; color: #475569; }

        .spk-num {
            font-weight: bold;
            color: #22AF85;
        }
        .cust-name {
            font-weight: bold;
            color: #0f172a;
        }
        .item-brand {
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
        }
        .item-services {
            color: #22AF85;
            font-size: 10px;
            margin-top: 2px;
        }
        .item-row {
            margin-bottom: 5px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #f1f5f9;
        }
        .item-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-meta">
            Tanggal Cetak: {{ now()->format('d M Y H:i') }}<br>
            Oleh: {{ auth()->user()->name ?? 'CS Admin' }}
        </div>
        <h1 class="header-title">Laporan Transaksi SPK CS</h1>
        <p class="header-subtitle">
            Periode: 
            @if(request('date_from') && request('date_to'))
                {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }} s/d {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}
            @elseif(request('date_from'))
                Mulai {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}
            @elseif(request('date_to'))
                Sampai {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}
            @else
                Semua Periode Tanggal
            @endif
            | Status: {{ request('status') ? str_replace('_', ' ', request('status')) : 'Semua Status' }}
        </p>
    </div>

    <table style="width: 100%; border: none; margin-bottom: 20px; border-collapse: collapse;">
        <tr>
            <td style="width: 48%; border: none; padding: 0; vertical-align: top;">
                <div style="background: #f8fafc; border-left: 5px solid #22AF85; padding: 10px 15px; border-radius: 4px;">
                    <div style="font-size: 9px; text-transform: uppercase; color: #64748b; font-weight: bold; margin-bottom: 4px;">Total SPK Dibuat</div>
                    <div style="font-size: 16px; font-weight: bold; color: #0f172a;">{{ $totalSpk }} SPK</div>
                </div>
            </td>
            <td style="width: 4%; border: none;"></td>
            <td style="width: 48%; border: none; padding: 0; vertical-align: top;">
                <div style="background: #f8fafc; border-left: 5px solid #22AF85; padding: 10px 15px; border-radius: 4px;">
                    <div style="font-size: 9px; text-transform: uppercase; color: #64748b; font-weight: bold; margin-bottom: 4px;">Total Nilai Transaksi (Omzet)</div>
                    <div style="font-size: 16px; font-weight: bold; color: #0f172a;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">No SPK</th>
                <th width="12%">Tanggal</th>
                <th width="20%">Customer</th>
                <th width="28%">Detail Item & Jasa</th>
                <th width="12%" class="text-right">Total Transaksi</th>
                <th width="8%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($spks as $index => $spk)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="spk-num">{{ $spk->spk_number }}</td>
                    <td>{{ $spk->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <span class="cust-name">{{ $spk->lead?->customer_name ?? 'Unknown Customer' }}</span><br>
                        <span style="color: #64748b; font-size: 10px;">{{ $spk->lead?->customer_phone ?? '-' }}</span>
                    </td>
                    <td>
                        @if($spk->items && count($spk->items) > 0)
                            @foreach($spk->items as $item)
                                <div class="item-row">
                                    <span class="item-brand">{{ $item->shoe_brand }}</span> 
                                    <span style="color: #64748b;">({{ $item->shoe_type }})</span>
                                    <div class="item-services">
                                        @if(is_array($item->services))
                                            {{ collect($item->services)->map(fn($s) => is_array($s) ? ($s['name'] ?? '-') : $s)->implode(' • ') }}
                                        @else
                                            {{ $item->services }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @elseif($spk->shoe_brand)
                            <div class="item-row">
                                <span class="item-brand">{{ $spk->shoe_brand }}</span>
                                <span style="color: #64748b;">({{ $spk->shoe_type }})</span>
                                <div class="item-services">
                                    @if(is_array($spk->services))
                                        {{ collect($spk->services)->map(fn($s) => is_array($s) ? ($s['name'] ?? '-') : $s)->implode(' • ') }}
                                    @else
                                        {{ $spk->services }}
                                    @endif
                                </div>
                            </div>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        <strong>Rp {{ number_format($spk->total_price, 0, ',', '.') }}</strong><br>
                        <span style="color: #64748b; font-size: 9px;">DP: Rp {{ number_format($spk->dp_amount, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-center">
                        @php
                            $badge = 'badge-gray';
                            if ($spk->status === 'DP_PAID') $badge = 'badge-success';
                            elseif ($spk->status === 'WAITING_DP') $badge = 'badge-warning';
                            elseif ($spk->status === 'HANDED_TO_WORKSHOP') $badge = 'badge-info';
                        @endphp
                        <span class="badge {{ $badge }}">{{ $spk->label }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px; color: #64748b;">
                        Belum ada data SPK yang sesuai dengan filter pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
