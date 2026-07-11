<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Follow-up Customer Experience (CX)</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
        }
        /* Header Styling */
        .header-container {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 3px solid #0d9488; /* Teal accent border */
            padding-bottom: 12px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 12px;
            color: #0d9488;
            margin: 0;
            font-weight: 600;
        }
        .meta-info {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
        }

        /* Summary Cards / Metrics */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table td {
            width: 25%;
            padding: 0 8px 0 0;
            border: none;
        }
        .summary-table td:last-child {
            padding-right: 0;
        }
        .metric-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            text-align: left;
        }
        .metric-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .metric-value {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }

        /* Main Table Styling */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-align: left;
            letter-spacing: 0.3px;
        }
        .data-table td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            font-size: 10px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
            text-align: center;
        }
        .badge-danger {
            background-color: #fef2f2;
            color: #ef4444;
            border: 1px solid #fee2e2;
        }
        .badge-warning {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fef3c7;
        }
        .badge-success {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #dcfce7;
        }
        .badge-info {
            background-color: #f0f9ff;
            color: #0284c7;
            border: 1px solid #e0f2fe;
        }

        .spk-badge {
            font-family: monospace;
            font-weight: bold;
            color: #0f172a;
            font-size: 10px;
        }
        .customer-name {
            font-weight: bold;
            color: #1e293b;
        }
        .text-center {
            text-align: center;
        }
        .text-gray {
            color: #64748b;
            font-size: 9px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="title">Laporan Follow-up Customer Experience</h1>
                    <p class="subtitle">Sistem Monitoring Kendala & Resolusi SPK</p>
                </td>
                <td class="meta-info">
                    <strong>Dicetak Pada:</strong> {{ now()->format('d F Y H:i') }}<br>
                    <strong>Oleh:</strong> {{ auth()->user()->name ?? 'System Admin' }}<br>
                    <strong>Status Tab:</strong> {{ strtoupper($tab) }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Summary Metrics -->
    <table class="summary-table">
        <tr>
            <td>
                <div class="metric-card">
                    <div class="metric-label">Total Kasus Terfilter</div>
                    <div class="metric-value">{{ $summary['total'] }} SPK</div>
                </div>
            </td>
            <td>
                <div class="metric-card" style="border-left: 4px solid #ef4444;">
                    <div class="metric-label">Kasus Aktif (Open)</div>
                    <div class="metric-value" style="color: #ef4444;">{{ $summary['open'] }} SPK</div>
                </div>
            </td>
            <td>
                <div class="metric-card" style="border-left: 4px solid #16a34a;">
                    <div class="metric-label">Kasus Selesai (Resolved)</div>
                    <div class="metric-value" style="color: #16a34a;">{{ $summary['resolved'] }} SPK</div>
                </div>
            </td>
            <td>
                <div class="metric-card" style="border-left: 4px solid #d97706;">
                    <div class="metric-label">Pengiriman Ditahan (Hold)</div>
                    <div class="metric-value" style="color: #d97706;">{{ $summary['hold'] }} SPK</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">No</th>
                <th style="width: 12%;">Info SPK</th>
                <th style="width: 20%;">Customer</th>
                <th style="width: 10%;" class="text-center">Sumber & Kategori</th>
                <th style="width: 9%;" class="text-center">Foto Kendala</th>
                <th style="width: 40%;">Detail Kendala (Issue)</th>
                <th style="width: 6%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                @php
                    if ($item instanceof \App\Models\WorkOrder) {
                        $workOrder = $item;
                        $openIssue = $workOrder->cxIssues->first();
                    } else {
                        $openIssue = $item;
                        $workOrder = $item->workOrder;
                    }
                    $reporter = $openIssue ? ($openIssue->reporter->name ?? 'Gudang/Admin') : 'Gudang/Admin';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span class="spk-badge">{{ $workOrder->spk_number ?? '-' }}</span><br>
                        <span class="text-gray">Tgl Masuk:</span> {{ $workOrder->entry_date ? $workOrder->entry_date->format('d/m/Y') : '-' }}<br>
                        <span class="text-gray">Est. Selesai:</span> 
                        @if($workOrder->new_estimation_date)
                            <strong style="color: #d97706;">{{ $workOrder->new_estimation_date->format('d/m/Y') }}</strong>
                        @elseif($workOrder->estimation_date)
                            {{ $workOrder->estimation_date->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <span class="customer-name">{{ $workOrder->customer_name ?? ($openIssue->customer_name ?? '-') }}</span><br>
                        <span class="text-gray">WA:</span> {{ $workOrder->customer_phone ?? ($openIssue->customer_phone ?? '-') }}<br>
                        <span class="text-gray">Brand:</span> {{ $workOrder->shoe_brand ?? '-' }}
                    </td>
                    <td class="text-center">
                        <span class="badge badge-info">{{ $openIssue->source ?? 'MANUAL' }}</span><br>
                        <span class="badge badge-warning" style="margin-top: 4px;">{{ $openIssue->category ?? 'TEKNIS' }}</span>
                    </td>
                    <td class="text-center">
                        @php
                            $photoPath = ($openIssue && $openIssue->photos && is_array($openIssue->photos) && count($openIssue->photos) > 0) ? $openIssue->photos[0] : null;
                            $fullPath = null;
                            if ($photoPath) {
                                if (str_starts_with($photoPath, 'storage/')) {
                                    $photoPath = substr($photoPath, 8);
                                }
                                $fullPath = storage_path('app/public/' . $photoPath);
                            }
                        @endphp
                        @if($fullPath && file_exists($fullPath))
                            <img src="{{ $fullPath }}" style="max-height: 40px; max-width: 60px; border: 1px solid #e2e8f0; border-radius: 4px;" />
                        @else
                            <span class="text-gray" style="font-size: 8px;">Tidak Ada Foto</span>
                        @endif
                    </td>
                    <td>
                        <strong>Pelapor:</strong> {{ $reporter }}<br>
                        <div style="margin-top: 4px; font-size: 9.5px; color: #475569;">
                            {!! nl2br(e($openIssue->description ?? ($openIssue->kendala ?? '-'))) !!}
                        </div>
                    </td>
                    <td class="text-center">
                        @if($openIssue && $openIssue->status === 'RESOLVED')
                            <span class="badge badge-success">RESOLVED</span>
                        @else
                            <span class="badge badge-danger">OPEN</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data followup yang sesuai dengan filter saat ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
