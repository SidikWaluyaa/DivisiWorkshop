<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Analisis Workshop</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #0d9488; /* Teal-600 */
            color: white;
            padding: 40px;
            margin-bottom: 30px;
        }
        .header h1 { margin: 0; font-size: 28px; text-transform: uppercase; letter-spacing: 2px; }
        .header p { margin: 5px 0 0; opacity: 0.8; font-size: 14px; }
        .meta-grid {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .meta-item {
            display: table-cell;
            width: 33%;
        }
        
        /* KPI Cards */
        .kpi-container {
            padding: 0 40px;
            margin-bottom: 30px;
        }
        .kpi-table { width: 100%; border-collapse: separate; border-spacing: 15px 0; }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .kpi-value { font-size: 24px; font-weight: bold; color: #0f172a; margin: 5px 0; }
        .kpi-label { font-size: 11px; text-transform: uppercase; color: #64748b; letter-spacing: 1px; }

        /* Section Titles */
        .section-title {
            margin: 0 40px 15px;
            font-size: 16px;
            font-weight: bold;
            color: #0d9488;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }

        /* Two Column Layout */
        .columns {
            padding: 0 40px;
            margin-bottom: 30px;
        }
        .col-table { width: 100%; }
        .col-left { width: 48%; vertical-align: top; padding-right: 2%; }
        .col-right { width: 48%; vertical-align: top; padding-left: 2%; }

        /* Insight Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .data-table th { text-align: left; padding: 8px; border-bottom: 1px solid #cbd5e1; color: #64748b; }
        .data-table td { padding: 8px; border-bottom: 1px solid #f1f5f9; }
        
        /* Visual Bars */
        .bar-container { background: #e2e8f0; height: 6px; border-radius: 3px; width: 100%; margin-top: 5px; }
        .bar-fill { height: 100%; border-radius: 3px; background: #0d9488; }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px 40px;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            text-align: right;
        }

        /* Highlight Box */
        .highlight-box {
            background: #f0fdfa;
            border-left: 4px solid #0d9488;
            padding: 15px;
            margin: 0 40px 30px;
            font-size: 13px;
            color: #334155;
        }
        .highlight-title { font-weight: bold; margin-bottom: 5px; color: #115e59; }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <h1>Workshop Analytics</h1>
        <div class="meta-grid">
            <div class="meta-item">
                <p>PERIODE LAPORAN</p>
                <strong>{{ $startDate }} — {{ $endDate }}</strong>
            </div>
            <div class="meta-item" style="text-align: center;">
                <p>DIBUAT OLEH</p>
                <strong>System Administrator</strong>
            </div>
            <div class="meta-item" style="text-align: right;">
                <p>TANGGAL EXPORT</p>
                <strong>{{ now()->format('d M Y') }}</strong>
            </div>
        </div>
    </div>

    <!-- EXECUTIVE SUMMARY -->
    <div class="kpi-container">
        <table class="kpi-table">
            <tr>
                <td class="kpi-card">
                    <div class="kpi-label">Total Pendapatan</div>
                    <div class="kpi-value" style="color: #059669;">Rp {{ number_format($revenue/1000, 0) }}k</div>
                </td>
                <td class="kpi-card">
                    <div class="kpi-label">Order Selesai</div>
                    <div class="kpi-value">{{ $throughput }} <span style="font-size: 12px; color: #64748b;">Unit</span></div>
                </td>
                <td class="kpi-card">
                    <div class="kpi-label">Lolos QC (FPY)</div>
                    <div class="kpi-value" style="color: #7c3aed;">{{ $qcPassRate }}%</div>
                </td>
                <td class="kpi-card">
                    <div class="kpi-label">Avg. Duration</div>
                    <div class="kpi-value">{{ $avgCompletionTime }} <span style="font-size: 12px; color: #64748b;">Hari</span></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- AI INSIGHTS -->
    <div class="highlight-box">
        <div class="highlight-title">💡 Insight Performansi</div>
        Pada periode ini, workshop berhasil menyelesaikan <strong>{{ $throughput }} unit</strong> sepatu dengan tingkat kelolosan QC sebesar <strong>{{ $qcPassRate }}%</strong>. 
        Layanan paling dominan adalah <strong>{{ $serviceMix->first()->service->name ?? '-' }}</strong> yang menyumbang pendapatan terbesar.
        Teknisi dengan produktivitas output tertinggi adalah <strong>{{ $topPerformers->first()->name ?? '-' }}</strong>.
    </div>

    <!-- DETAILS COLUMNS -->
    <div class="columns">
        <table class="col-table">
            <tr>
                <td class="col-left">
                    <div class="section-title">CHAMPIONS (Top Teknisi)</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Teknisi</th>
                                <th style="text-align: right;">Output</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topPerformers as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                    <!-- Visual Bar -->
                                    @php $percent = ($user->completed_count / max($topPerformers->first()->completed_count, 1)) * 100; @endphp
                                    <div class="bar-container">
                                        <div class="bar-fill" style="width: {{ $percent }}%;"></div>
                                    </div>
                                </td>
                                <td style="text-align: right; vertical-align: top;">
                                    <strong>{{ $user->completed_count }}</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
                <td class="col-right">
                    <div class="section-title">REVENUE DRIVERS (Top Layanan)</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Layanan</th>
                                <th style="text-align: right;">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceMix as $mix)
                            <tr>
                                <td>
                                    <strong>{{ $mix->service->name }}</strong>
                                    <div style="font-size: 10px; color: #64748b;">{{ $mix->order_count }} Orders</div>
                                    <!-- Visual Bar -->
                                    @php $percent = ($mix->total_revenue / max($serviceMix->first()->total_revenue, 1)) * 100; @endphp
                                    <div class="bar-container">
                                        <div class="bar-fill" style="background-color: #3b82f6; width: {{ $percent }}%;"></div>
                                    </div>
                                </td>
                                <td style="text-align: right; vertical-align: top;">
                                    <strong>Rp {{ number_format($mix->total_revenue/1000, 0) }}k</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- ORDER LIST -->
    <div style="page-break-inside: avoid;">
        <div class="section-title">DAFTAR ORDER SELESAI (SAMPEL TERBARU)</div>
        <div style="padding: 0 40px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="15%">SPK</th>
                        <th width="25%">Layanan</th>
                        <th width="15%">Masuk</th>
                        <th width="15%">Selesai</th>
                        <th width="15%">QC By</th>
                        <th width="15%" style="text-align: right;">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td><strong>{{ $order->spk_number }}</strong></td>
                        <td>{{ $order->services->pluck('name')->implode(', ') }}</td>
                        <td>{{ $order->entry_date->format('d/m/y') }}</td>
                        <td>{{ $order->finished_date->format('d/m/y') }}</td>
                        <td>{{ $order->qcFinalPic->name ?? '-' }}</td>
                        <td style="text-align: right;">Rp {{ number_format($order->total_service_price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        Generated by Workshop System | Page 1 of 1
    </div>

</body>
</html>
