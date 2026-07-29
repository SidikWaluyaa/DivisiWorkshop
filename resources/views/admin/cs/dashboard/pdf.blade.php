<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Analitik CS</title>
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
            margin-bottom: 20px;
        }
        .brand-title {
            font-size: 18px;
            font-weight: bold;
            color: #22AF85; /* Brand color */
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
            font-size: 9px;
            color: #475569;
            text-align: right;
            line-height: 1.5;
        }

        /* Filter block styling */
        .filter-container {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
        }
        .filter-title {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 4px;
        }
        .filter-table {
            width: 100%;
        }
        .filter-label {
            font-weight: bold;
            color: #475569;
            width: 15%;
            font-size: 10px;
        }
        .filter-value {
            color: #1e293b;
            width: 35%;
            font-size: 10px;
        }

        /* Metrics grid/card layout using tables */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 15px;
            margin-bottom: 10px;
            padding-left: 6px;
            border-left: 3px solid #22AF85;
        }

        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .metrics-table td {
            width: 20%;
            padding: 0px;
            vertical-align: top;
        }
        .metric-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-top: 3px solid #22AF85;
            border-radius: 8px;
            padding: 12px 10px;
            margin: 4px;
            text-align: center;
        }
        .metric-card.color-closing {
            border-top-color: #eab308;
        }
        .metric-card.color-sepatu {
            border-top-color: #06b6d4;
        }
        .metric-card.color-revenue {
            border-top-color: #a855f7;
        }
        .metric-card.color-avg {
            border-top-color: #ec4899;
        }
        
        .metric-label {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            display: block;
        }
        .metric-value {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .metric-subtext {
            font-size: 8px;
            color: #475569;
            background-color: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        /* Closing Path styling */
        .path-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .path-table td {
            width: 25%;
            padding: 0px;
            vertical-align: top;
        }
        .path-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-top: 3px solid #10b981;
            border-radius: 8px;
            padding: 12px 10px;
            margin: 4px;
            text-align: center;
        }
        .path-card.color-followup {
            border-top-color: #f97316;
        }
        .path-card.color-konsul {
            border-top-color: #eab308;
        }
        .path-card.color-aktif {
            border-top-color: #3b82f6;
        }

        /* Footer info */
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 20px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            font-size: 8px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td>
                <h1 class="brand-title">Shoe Workshop</h1>
                <p class="brand-subtitle">Laporan Kinerja & Analitik CS</p>
            </td>
            <td class="meta-text">
                <strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y H:i') }}<br>
                <strong>Dicetak Oleh:</strong> {{ Auth::user()->name }}
            </td>
        </tr>
    </table>

    <!-- Filter & Parameters Box -->
    <div class="filter-container">
        <div class="filter-title">Parameter Filter Laporan</div>
        <table class="filter-table">
            <tr>
                <td class="filter-label">Periode Mulai:</td>
                <td class="filter-value">{{ $startDate->format('d/m/Y') }}</td>
                <td class="filter-label">Akun CS:</td>
                <td class="filter-value">{{ $selectedCs ? $selectedCs->name : 'Keseluruhan (Global)' }}</td>
            </tr>
            <tr>
                <td class="filter-label">Periode Selesai:</td>
                <td class="filter-value">{{ $endDate->format('d/m/Y') }}</td>
                <td class="filter-label">Tingkat Hak Akses:</td>
                <td class="filter-value">{{ $selectedCs ? 'Customer Service' : 'Semua CS' }}</td>
            </tr>
        </table>
    </div>

    <!-- Section: Global Overview Metrics -->
    <div class="section-title">Global Overview Metrics</div>
    <table class="metrics-table">
        <tr>
            <!-- Card 1: Lead Intake -->
            <td>
                <div class="metric-card">
                    <span class="metric-label">Total Lead Intake</span>
                    <div class="metric-value">{{ number_format($overview['total_leads']) }}</div>
                    <span class="metric-subtext">Input Periode Ini</span>
                </div>
            </td>
            <!-- Card 2: Total Closing -->
            <td>
                <div class="metric-card color-closing">
                    <span class="metric-label">Total Closing</span>
                    <div class="metric-value">{{ number_format($overview['total_closings']) }}</div>
                    <span class="metric-subtext">{{ $overview['conversion_rate'] }}% Conversion</span>
                </div>
            </td>
            <!-- Card 3: Sepatu Masuk -->
            <td>
                <div class="metric-card color-sepatu">
                    <span class="metric-label">Sepatu Masuk</span>
                    <div class="metric-value">{{ number_format($overview['total_incoming_items']) }}</div>
                    <span class="metric-subtext">Volume Fisik</span>
                </div>
            </td>
            <!-- Card 4: Revenue -->
            <td>
                <div class="metric-card color-revenue">
                    <span class="metric-label">Revenue Realization</span>
                    <div class="metric-value" style="font-size: 12px; margin-top: 4px; margin-bottom: 6px;">
                        Rp {{ number_format($overview['total_revenue'], 0, ',', '.') }}
                    </div>
                    <span class="metric-subtext">Omset Closing Valid</span>
                </div>
            </td>
            <!-- Card 5: Avg Deal Value -->
            <td>
                <div class="metric-card color-avg">
                    <span class="metric-label">Avg Deal Value</span>
                    <div class="metric-value" style="font-size: 12px; margin-top: 4px; margin-bottom: 6px;">
                        Rp {{ number_format($overview['avg_deal_value'], 0, ',', '.') }}
                    </div>
                    <span class="metric-subtext">Rerata Per Deal</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Section: Closing Path Analysis -->
    <div class="section-title">Closing Path Analysis (Jalur Lead Menuju Closing)</div>
    <table class="path-table">
        <tr>
            <!-- Card 1: Closing Langsung -->
            <td>
                <div class="path-card">
                    <span class="metric-label">Closing Langsung</span>
                    <div class="metric-value">{{ number_format($pathAnalysis['closed_direct']) }}</div>
                    <span class="metric-subtext">Konsultasi &rarr; Closing</span>
                </div>
            </td>
            <!-- Card 2: Closing via Follow-Up -->
            <td>
                <div class="path-card color-followup">
                    <span class="metric-label">Closing via Follow-Up</span>
                    <div class="metric-value">{{ number_format($pathAnalysis['closed_via_followup']) }}</div>
                    <span class="metric-subtext">Lewat Follow-Up</span>
                </div>
            </td>
            <!-- Card 3: Konsultasi -> Follow-up -->
            <td>
                <div class="path-card color-konsul">
                    <span class="metric-label">Konsul &rarr; Follow-Up</span>
                    <div class="metric-value">{{ number_format($pathAnalysis['total_to_followup']) }}</div>
                    <span class="metric-subtext">Efektivitas: {{ $pathAnalysis['followup_effectiveness'] }}%</span>
                </div>
            </td>
            <!-- Card 4: Follow-up Aktif -->
            <td>
                <div class="path-card color-aktif">
                    <span class="metric-label">Follow-Up Aktif</span>
                    <div class="metric-value">{{ number_format($pathAnalysis['active_followup']) }}</div>
                    <span class="metric-subtext">Live Count</span>
                </div>
            </td>
        </tr>
    </table>

    <div style="margin-top: 30px; font-size: 9px; color: #64748b; border-top: 1px dashed #cbd5e1; padding-top: 10px;">
        <strong>Catatan Internal Laporan:</strong><br>
        1. <em>Total Lead Intake</em> menghitung seluruh leads yang baru masuk pada periode filter.<br>
        2. <em>Total Closing</em> menghitung leads yang statusnya diubah menjadi CLOSING atau CONVERTED pada periode filter.<br>
        3. <em>Revenue Realization</em> dihitung berdasarkan total nominal transaksi SPK yang berstatus aktif (tidak batal dan bukan spk pending) yang memiliki entry date pada periode filter.
    </div>

    <!-- Footer -->
    <div class="footer">
        Shoe Workshop System &bull; Laporan Kinerja CS &bull; Halaman 1 dari 1
    </div>

</body>
</html>
