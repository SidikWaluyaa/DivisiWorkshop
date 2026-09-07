<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lembar Audit & Cek Fisik Sepatu Produksi</title>
    <style>
        @page {
            margin: 25px 25px 35px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        
        /* Header styling */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .brand-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 8.5px;
            color: #475569;
            font-weight: bold;
            margin-top: 2px;
            margin-bottom: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .meta-box {
            font-size: 8px;
            color: #334155;
            text-align: right;
            line-height: 1.35;
        }

        /* KPI & Filter Row */
        .summary-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .kpi-cell {
            padding: 5px 8px;
            border-radius: 4px;
            text-align: center;
        }
        .kpi-total {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
        }
        .kpi-late {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
        }
        .kpi-warning {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
        }
        .kpi-ontrack {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
        }
        .kpi-num {
            font-size: 13px;
            font-weight: bold;
            display: block;
        }
        .kpi-label {
            font-size: 7.5px;
            text-transform: uppercase;
            font-weight: bold;
            color: #475569;
        }

        /* Instruction banner */
        .guide-box {
            background-color: #fffef0;
            border: 1px dashed #d97706;
            border-radius: 4px;
            padding: 5px 10px;
            margin-bottom: 10px;
            font-size: 8px;
            color: #92400e;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 5px 4px;
            border: 1px solid #0f172a;
            text-align: center;
            vertical-align: middle;
        }
        .data-table th.audit-header {
            background-color: #334155;
            color: #fef08a;
            border-left: 1.5px solid #0f172a;
        }
        .data-table td {
            padding: 4px 4px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            font-size: 8px;
        }

        /* Row status styling */
        .row-late {
            background-color: #fff5f5;
        }
        .row-warning {
            background-color: #fffdf0;
        }
        .row-ontrack {
            background-color: #fbfdfa;
        }
        
        .audit-cell {
            background-color: #f8fafc;
            border-left: 1px dashed #94a3b8 !important;
            border-right: 1px dashed #94a3b8 !important;
        }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }
        .badge-late {
            background-color: #ef4444;
            color: #ffffff;
        }
        .badge-warning {
            background-color: #f59e0b;
            color: #ffffff;
        }
        .badge-ontrack {
            background-color: #10b981;
            color: #ffffff;
        }

        .diff-late {
            color: #b91c1c;
            font-weight: bold;
            font-size: 7.5px;
        }
        .diff-warning {
            color: #b45309;
            font-weight: bold;
            font-size: 7.5px;
        }
        .diff-ontrack {
            color: #047857;
            font-size: 7.5px;
        }

        /* Checkbox styling */
        .cb-item {
            font-size: 7px;
            line-height: 1.25;
            white-space: nowrap;
            display: block;
        }
        .cb-box {
            font-family: monospace;
            font-weight: bold;
            color: #475569;
        }

        /* Sign-off Table */
        .signoff-table {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .signoff-box {
            width: 32%;
            text-align: center;
            vertical-align: top;
        }
        .signoff-title {
            font-weight: bold;
            font-size: 8px;
            color: #475569;
            margin-bottom: 40px;
        }
        .signoff-line {
            width: 75%;
            margin: 0 auto;
            border-bottom: 1px solid #64748b;
            margin-bottom: 3px;
        }
        .signoff-name {
            font-weight: bold;
            font-size: 8.5px;
            color: #0f172a;
        }
        .signoff-role {
            font-size: 7.5px;
            color: #64748b;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 15px;
            border-top: 1px solid #e2e8f0;
            padding-top: 3px;
            text-align: center;
            font-size: 7px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <!-- Header Table -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="60%">
                <h1 class="brand-title">LEMBAR AUDIT & CEK FISIK SEPATU PRODUKSI</h1>
                <p class="brand-subtitle">Divisi Workshop — Monitoring SLA & Verifikasi Stok Lapangan</p>
            </td>
            <td width="40%" class="meta-box">
                <strong>Waktu Cetak:</strong> {{ $printedAt }}<br>
                <strong>Auditor / Dicetak Oleh:</strong> {{ $printedBy }}<br>
                <strong>Filter Status:</strong> {{ $statusFilter }} | <strong>Pencarian:</strong> {{ $searchQuery }}
            </td>
        </tr>
    </table>

    <!-- KPI Summary Row -->
    <table class="summary-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="22%" style="padding-right: 6px;">
                <div class="kpi-cell kpi-total">
                    <span class="kpi-num" style="color: #0f172a;">{{ $totalOrders }}</span>
                    <span class="kpi-label">Total Unit Diperiksa</span>
                </div>
            </td>
            <td width="22%" style="padding-right: 6px;">
                <div class="kpi-cell kpi-late">
                    <span class="kpi-num" style="color: #dc2626;">{{ $lateCount }}</span>
                    <span class="kpi-label">Terlambat (Overdue)</span>
                </div>
            </td>
            <td width="22%" style="padding-right: 6px;">
                <div class="kpi-cell kpi-warning">
                    <span class="kpi-num" style="color: #d97706;">{{ $warningCount }}</span>
                    <span class="kpi-label">Mendekati Deadline</span>
                </div>
            </td>
            <td width="22%" style="padding-right: 6px;">
                <div class="kpi-cell kpi-ontrack">
                    <span class="kpi-num" style="color: #059669;">{{ $onTrackCount }}</span>
                    <span class="kpi-label">On Track (Aman)</span>
                </div>
            </td>
            <td width="12%">
                <div class="kpi-cell" style="background-color: #f8fafc; border: 1px solid #cbd5e1;">
                    <span class="kpi-num" style="color: #64748b; font-size: 11px;">100%</span>
                    <span class="kpi-label">Target Audit</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Instruction Guide Box -->
    <div class="guide-box">
        <strong>📋 PETUNJUK AUDIT LAPANGAN:</strong> Cocokkan data SPK di bawah ini dengan fisik sepatu riil di workshop/rak. Berikan tanda centang [✓] pada kolom audit (ADA, TIDAK ADA, SELESAI, atau DI QC), tulis nomor rak/stasiun pengerjaan saat ini, serta catat kendala fisik aktual.
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">NO</th>
                <th width="9%">NO. SPK</th>
                <th width="11%">PELANGGAN</th>
                <th width="12%">SEPATU</th>
                <th width="11%">LAYANAN JASA</th>
                <th width="9%">TEKNISI</th>
                <th width="6%">TGL MASUK</th>
                <th width="7%">ESTIMASI</th>
                <th width="8%">SISA WAKTU</th>
                <th width="7%">STATUS</th>
                <th width="9%" class="audit-header">[AUDIT] CEK FISIK</th>
                <th width="8%" class="audit-header">[AUDIT] LOKASI RAK</th>
                <th width="0%" class="audit-header">[AUDIT] CATATAN FISIK</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                @php
                    $isLate = $order->warning_status === 'LATE';
                    $isWarning = $order->warning_status === 'WARNING';
                    $rowClass = $isLate ? 'row-late' : ($isWarning ? 'row-warning' : 'row-ontrack');

                    // Services list
                    $servicesList = [];
                    if ($order->workOrderServices && $order->workOrderServices->count() > 0) {
                        $servicesList = $order->workOrderServices->pluck('service_name')->filter()->toArray();
                    }
                    if (empty($servicesList) && $order->services && $order->services->count() > 0) {
                        $servicesList = $order->services->pluck('name')->filter()->toArray();
                    }
                    $servicesStr = count($servicesList) > 0 ? implode(', ', $servicesList) : '-';

                    // Technicians list
                    $techs = [];
                    if ($order->prodUpperBy) $techs[] = 'U: ' . $order->prodUpperBy->name;
                    if ($order->prodSolBy) $techs[] = 'S: ' . $order->prodSolBy->name;
                    if ($order->qcJahitBy) $techs[] = 'QC: ' . $order->qcJahitBy->name;
                    $techsStr = count($techs) > 0 ? implode(' | ', $techs) : '-';

                    // Storage Rack
                    $rackName = '-';
                    if ($order->storageAssignments && $order->storageAssignments->count() > 0) {
                        $rackName = $order->storageAssignments->first()->rack?->name ?? '-';
                    }

                    // Format dates
                    $entryDateStr = $order->entry_date ? \Carbon\Carbon::parse($order->entry_date)->format('d/m/Y') : '-';
                    $estDate = $order->new_estimation_date ?: $order->estimation_date;
                    $estDateStr = $estDate ? \Carbon\Carbon::parse($estDate)->format('d/m/Y') : '-';

                    // Days Diff
                    $daysRemaining = $order->calendar_days_remaining;
                    if ($daysRemaining < 0) {
                        $daysDiffText = 'Telat ' . abs($daysRemaining) . ' hr';
                        $diffClass = 'diff-late';
                    } elseif ($daysRemaining === 0) {
                        $daysDiffText = 'Hari ini';
                        $diffClass = 'diff-warning';
                    } else {
                        $daysDiffText = $daysRemaining . ' hr lagi';
                        $diffClass = $isWarning ? 'diff-warning' : 'diff-ontrack';
                    }
                @endphp
                <tr class="{{ $rowClass }}">
                    <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold; color: #0f172a;">{{ $order->spk_number }}</td>
                    <td>
                        <strong style="color: #0f172a;">{{ $order->customer_name }}</strong>
                        @if($order->customer_phone)
                            <div style="font-size: 7px; color: #64748b;">{{ $order->customer_phone }}</div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $order->shoe_brand ?: '-' }}</strong> {{ $order->shoe_type }}
                        @if($order->shoe_color || $order->shoe_size)
                            <div style="font-size: 7px; color: #64748b;">{{ $order->shoe_color }} {{ $order->shoe_size ? '('.$order->shoe_size.')' : '' }}</div>
                        @endif
                    </td>
                    <td style="font-size: 7.5px; color: #334155;">{{ $servicesStr }}</td>
                    <td style="font-size: 7px; color: #475569;">{{ $techsStr }}</td>
                    <td style="text-align: center; font-size: 7.5px;">{{ $entryDateStr }}</td>
                    <td style="text-align: center; font-size: 7.5px; font-weight: bold;">{{ $estDateStr }}</td>
                    <td style="text-align: center;">
                        <span class="{{ $diffClass }}">{{ $daysDiffText }}</span>
                    </td>
                    <td style="text-align: center;">
                        @if($isLate)
                            <span class="badge badge-late">TERLAMBAT</span>
                        @elseif($isWarning)
                            <span class="badge badge-warning">WARNING</span>
                        @else
                            <span class="badge badge-ontrack">ON TRACK</span>
                        @endif
                    </td>

                    <!-- AUDIT COLUMNS -->
                    <td class="audit-cell" style="padding: 3px;">
                        <span class="cb-item"><span class="cb-box">[ &nbsp; ]</span> ADA</span>
                        <span class="cb-item"><span class="cb-box">[ &nbsp; ]</span> TDK ADA</span>
                        <span class="cb-item"><span class="cb-box">[ &nbsp; ]</span> SELESAI</span>
                        <span class="cb-item"><span class="cb-box">[ &nbsp; ]</span> DI QC</span>
                    </td>
                    <td class="audit-cell" style="font-size: 7.5px; color: #475569;">
                        @if($rackName !== '-')
                            <span style="font-size: 6.5px; color: #94a3b8;">Sistem: {{ $rackName }}</span><br>
                        @endif
                        <span style="font-size: 7px; color: #64748b;">Aktual: ............</span>
                    </td>
                    <td class="audit-cell" style="font-size: 7px; color: #94a3b8;">
                        @if($order->late_description)
                            <div style="color: #b91c1c; font-style: italic; font-size: 6.5px; margin-bottom: 2px;">Sys: {{ \Illuminate\Support\Str::limit($order->late_description, 35) }}</div>
                        @endif
                        ..........................................
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" style="text-align: center; padding: 25px; color: #64748b; font-weight: bold; font-size: 10px;">
                        Tidak ada data antrean produksi sesuai parameter filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Sign-off Section -->
    <table class="signoff-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="signoff-box">
                <div class="signoff-title">Petugas Audit Lapangan,</div>
                <div class="signoff-line"></div>
                <div class="signoff-name">( ............................................ )</div>
                <div class="signoff-role">Checker / Quality Inspector</div>
            </td>
            <td class="signoff-box">
                <div class="signoff-title">Koordinator Produksi Workshop,</div>
                <div class="signoff-line"></div>
                <div class="signoff-name">( ............................................ )</div>
                <div class="signoff-role">Supervisor Produksi</div>
            </td>
            <td class="signoff-box">
                <div class="signoff-title">Mengetahui,</div>
                <div class="signoff-line"></div>
                <div class="signoff-name">( ............................................ )</div>
                <div class="signoff-role">Workshop Manager</div>
            </td>
        </tr>
    </table>

    <!-- Footer Page Number -->
    <div class="footer">
        Halaman <script type="text/php">
            if (isset($pdf)) {
                echo $pdf->get_page_number() . ' dari ' . $pdf->get_page_count();
            }
        </script> &nbsp;|&nbsp; Dokumen Audit Fisik Produksi Workshop &nbsp;|&nbsp; Dicetak pada {{ $printedAt }}
    </div>

</body>
</html>
