<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $reportTitle }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .header {
            background-color: #0f766e; /* Teal-700 */
            color: white;
            padding: 30px 40px;
            margin-bottom: 25px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 24px; 
            text-transform: uppercase; 
            letter-spacing: 1.5px;
            font-weight: 900;
        }
        .header p { 
            margin: 5px 0 0; 
            opacity: 0.9; 
            font-size: 13px; 
        }
        .meta-grid {
            display: table;
            width: 100%;
            margin-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 15px;
        }
        .meta-item {
            display: table-cell;
            width: 33%;
            font-size: 11px;
        }
        .meta-item p {
            margin: 0 0 3px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ccfbf1;
        }
        .meta-item strong {
            font-size: 12px;
        }
        
        /* KPI Cards */
        .kpi-container {
            padding: 0 40px;
            margin-bottom: 25px;
        }
        .kpi-table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 15px 0; 
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            width: 50%;
        }
        .kpi-value { 
            font-size: 22px; 
            font-weight: bold; 
            color: #0f172a; 
            margin: 5px 0; 
        }
        .kpi-label { 
            font-size: 10px; 
            text-transform: uppercase; 
            color: #64748b; 
            letter-spacing: 1px; 
            font-weight: bold;
        }

        /* Section Titles */
        .section-title {
            margin: 0 40px 15px;
            font-size: 14px;
            font-weight: bold;
            color: #0f766e;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Insight Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .data-table th { 
            text-align: left; 
            padding: 10px 8px; 
            border-bottom: 2px solid #cbd5e1; 
            color: #475569; 
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
        .data-table td { 
            padding: 10px 8px; 
            border-bottom: 1px solid #e2e8f0; 
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        .badge-danger { background-color: #fee2e2; color: #b91c1c; }
        .badge-info { background-color: #dbeafe; color: #1d4ed8; }
        .badge-neutral { background-color: #f1f5f9; color: #475569; }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px 40px;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            text-align: right;
        }

        .text-right { text-align: right; }
        .font-mono { font-family: monospace; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <h1>{{ $reportTitle }}</h1>
        <p>Laporan Kinerja Layanan Fast Track Shoe Workshop</p>
        <div class="meta-grid">
            <div class="meta-item">
                <p>PERIODE LAPORAN</p>
                <strong>{{ $startDate }} — {{ $endDate }}</strong>
            </div>
            <div class="meta-item" style="text-align: center;">
                <p>METRIK ANALITIS</p>
                <strong>{{ strtoupper(str_replace('_', ' ', $metric)) }}</strong>
            </div>
            <div class="meta-item" style="text-align: right;">
                <p>TANGGAL EXPORT</p>
                <strong>{{ now()->format('d M Y H:i') }} WIB</strong>
            </div>
        </div>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="kpi-container">
        <table class="kpi-table">
            <tr>
                <td class="kpi-card">
                    <div class="kpi-label">Jumlah SPK Terkait</div>
                    <div class="kpi-value" style="color: #0f766e;">{{ $totalCount }} SPK</div>
                </td>
                <td class="kpi-card">
                    <div class="kpi-label">Total Nilai Transaksi</div>
                    <div class="kpi-value" style="color: #1e3a8a;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ORDER LIST -->
    <div>
        <div class="section-title">Rincian SPK Fast Track</div>
        <div style="padding: 0 40px; margin-bottom: 60px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="15%">No. SPK</th>
                        <th width="20%">Pelanggan</th>
                        <th width="20%">Sepatu</th>
                        <th width="13%">Status Stasiun</th>
                        <th width="12%" class="text-right">Nilai</th>
                        @if($metric === 'failed_fast_track' || $metric === 'operational_failed_fast_track' || $metric === 'pending_fast_track')
                            <th width="15%">Keterangan</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-mono font-bold">{{ $order->spk_number }}</td>
                            <td>{{ $order->customer?->name ?? $order->customer_name }}</td>
                            <td>{{ $order->shoe_brand }} - {{ $order->shoe_type }}</td>
                            <td>
                                @php
                                    $statusVal = $order->status->value;
                                    $badgeClass = 'badge-neutral';
                                    if (in_array($statusVal, ['FINISH', 'SELESAI', 'COMPLETED'])) {
                                        $badgeClass = 'badge-success';
                                    } elseif (in_array($statusVal, ['PREPARATION', 'SORTIR', 'PRODUCTION'])) {
                                        $badgeClass = 'badge-warning';
                                    } elseif ($statusVal === 'BATAL') {
                                        $badgeClass = 'badge-danger';
                                    } elseif ($statusVal === 'QC') {
                                        $badgeClass = 'badge-info';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $statusVal }}</span>
                            </td>
                            <td class="text-right font-bold">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</td>
                            @if($metric === 'failed_fast_track')
                                <td>
                                    <div style="font-size: 9px; color: #b91c1c; font-weight: bold;">
                                        @php
                                            $logs = $order->logs->where('action', 'STATUS_CHANGE')->sortBy('created_at');
                                            $transitions = [];
                                            foreach ($logs as $log) {
                                                $transitions[$log->step] = $log->created_at;
                                            }

                                            $prepStart = $transitions['PREPARATION'] ?? $order->created_at;
                                            $prepEnd = $transitions['SORTIR'] ?? $transitions['PRODUCTION'] ?? $transitions['QC'] ?? $transitions['FINISH'] ?? ($order->status->value === 'PREPARATION' ? now() : null);
                                            
                                            $sortirStart = $transitions['SORTIR'] ?? null;
                                            $sortirEnd = $transitions['PRODUCTION'] ?? $transitions['QC'] ?? $transitions['FINISH'] ?? ($order->status->value === 'SORTIR' ? now() : null);

                                            $prodStart = $transitions['PRODUCTION'] ?? null;
                                            $prodEnd = $transitions['QC'] ?? $transitions['FINISH'] ?? ($order->status->value === 'PRODUCTION' ? now() : null);

                                            $qcStart = $transitions['QC'] ?? null;
                                            $qcEnd = $transitions['FINISH'] ?? ($order->status->value === 'QC' ? now() : null);
                                        @endphp
                                        
                                        @if($prepEnd && $prepStart->diffInDays($prepEnd) > 1)
                                            <div>• Prep: {{ (int) $prepStart->diffInDays($prepEnd) }} Hari (SLA 1H)</div>
                                        @endif
                                        @if($sortirStart && $sortirEnd && $sortirStart->diffInDays($sortirEnd) > 3)
                                            <div>• Sortir: {{ (int) $sortirStart->diffInDays($sortirEnd) }} Hari (SLA 3H)</div>
                                        @endif
                                        @if($prodStart && $prodEnd && $prodStart->diffInDays($prodEnd) > 4)
                                            <div>• Prod: {{ (int) $prodStart->diffInDays($prodEnd) }} Hari (SLA 4H)</div>
                                        @endif
                                        @if($qcStart && $qcEnd && $qcStart->diffInDays($qcEnd) > 1)
                                            <div>• QC: {{ (int) $qcStart->diffInDays($qcEnd) }} Hari (SLA 1H)</div>
                                        @endif
                                    </div>
                                </td>
                            @elseif($metric === 'operational_failed_fast_track')
                                <td>
                                    <div style="font-size: 9px; color: #475569;">
                                        @php
                                            $reason = $order->getNonSlaFailureReason();
                                        @endphp
                                        @if($reason === 'TAMBAH_JASA')
                                            <span style="color: #d97706; font-weight: bold;">🔄 Penambahan Jasa</span>
                                        @elseif($reason === 'CX_FOLLOWUP')
                                            <span style="color: #7c3aed; font-weight: bold;">💬 CX FollowUp</span>
                                        @elseif($reason === 'BATAL_DONASI')
                                            <span style="color: #dc2626; font-weight: bold;">❌ Batal / Donasi</span>
                                        @endif
                                    </div>
                                </td>
                            @elseif($metric === 'pending_fast_track')
                                <td>
                                    <span style="color: #7c3aed; font-size: 9px; font-weight: bold;">⏳ Menunggu Verifikasi CS</span>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $metric === 'failed_fast_track' || $metric === 'operational_failed_fast_track' || $metric === 'pending_fast_track' ? '7' : '6' }}" style="text-align: center; color: #64748b; padding: 20px;">
                                Tidak ada data SPK yang sesuai untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- REPORT FOOTER / SIGNATURES -->
    <div style="page-break-inside: avoid; margin-top: 40px; padding: 0 40px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div style="font-weight: bold; color: #475569; margin-bottom: 5px;">Catatan:</div>
                    <div style="color: #64748b; font-size: 9px; line-height: 1.4;">
                        * Laporan ini mencakup data performansi layanan Fast Track secara real-time.<br/>
                        * Data diambil secara langsung dari basis data operasional Shoe Workshop.
                    </div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <div style="margin-bottom: 50px; color: #475569;">
                        Bandung, {{ now()->format('d M Y') }}<br/>
                        <strong>Disetujui Oleh,</strong>
                    </div>
                    <div style="border-bottom: 1px solid #475569; width: 180px; display: inline-block; margin-bottom: 5px;"></div>
                    <div style="color: #64748b; font-weight: bold; padding-right: 25px;">Kepala Workshop</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generated by Workshop System | Laporan Fast Track
    </div>

</body>
</html>
