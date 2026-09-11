<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $suratJalan->nomor_surat }} — Sistem Workshop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 10mm 12mm 12mm 12mm;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 20px 0 60px;
            color: #0f172a;
            line-height: 1.35;
            background: #cbd5e1;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Top Action Bar (Screen Only) */
        .no-print-toolbar {
            max-width: 210mm;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 4px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: #ffffff;
            color: #334155;
            font-weight: 700;
            font-size: 12px;
            text-decoration: none;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            transition: all 0.2s;
        }
        .btn-back:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 22px;
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 13px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
            transition: all 0.2s;
        }
        .btn-print:hover {
            background: linear-gradient(135deg, #4338ca 0%, #312e81 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.45);
        }
        .btn-print:active {
            transform: scale(0.98);
        }

        /* A4 Sheet Container */
        .print-paper {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 12mm 14mm;
            box-shadow: 0 10px 35px -5px rgba(0,0,0,0.18), 0 0 0 1px rgba(0,0,0,0.06);
            border-radius: 8px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header Document */
        .header {
            text-align: center;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h1 {
            margin: 0;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #0f172a;
        }
        .header p {
            margin: 3px 0 0;
            font-size: 9px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* 3-Box Highlight Summary */
        .highlight-bar {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }
        .highlight-box {
            flex: 1;
            padding: 6px 10px;
            border-radius: 8px;
            text-align: center;
        }
        .highlight-box.spk { background: #0f172a; color: #ffffff; }
        .highlight-box.jasa { background: #4f46e5; color: #ffffff; }
        .highlight-box.mat { background: #059669; color: #ffffff; }
        .highlight-label {
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            opacity: 0.9;
        }
        .highlight-val {
            font-size: 11.5px;
            font-weight: 900;
            margin-top: 1px;
            display: block;
        }

        /* Breakdown Summary Badges */
        .breakdown-container {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 8px;
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .breakdown-row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .breakdown-title {
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            min-width: 90px;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .breakdown-title.jasa { color: #4338ca; }
        .breakdown-title.mat { color: #047857; }
        .breakdown-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            flex: 1;
        }
        .breakdown-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 1.5px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 700;
            line-height: 1.3;
        }
        .breakdown-chip.jasa {
            background: #eef2ff;
            color: #312e81;
            border: 1px solid #c7d2fe;
        }
        .breakdown-chip.jasa .chip-qty {
            background: #4f46e5;
            color: #ffffff;
            padding: 0.5px 4px;
            border-radius: 3px;
            font-weight: 900;
            font-size: 7.5px;
        }
        .breakdown-chip.mat {
            background: #ecfdf5;
            color: #064e3b;
            border: 1px solid #a7f3d0;
        }
        .breakdown-chip.mat .chip-qty {
            background: #059669;
            color: #ffffff;
            padding: 0.5px 4px;
            border-radius: 3px;
            font-weight: 900;
            font-size: 7.5px;
        }

        /* Meta Information Table */
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            overflow: hidden;
        }
        .meta-table td {
            padding: 4.5px 8px;
            vertical-align: middle;
            font-size: 8.5px;
            border-bottom: 1px solid #f1f5f9;
        }
        .meta-table tr:last-child td {
            border-bottom: none;
        }

        /* Main Item Table */
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            table-layout: fixed;
        }
        .item-table th, .item-table td {
            border: 1px solid #94a3b8;
            padding: 5.5px 6px;
            text-align: left;
            font-size: 8.5px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .item-table th {
            background-color: #f1f5f9;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #1e293b;
            text-align: center;
        }

        .service-list, .mat-list, .tech-list {
            margin: 0;
            padding-left: 10px;
        }
        .service-list li {
            margin-bottom: 1.5px;
            font-weight: 600;
            color: #0f172a;
        }
        .tech-list li {
            margin-bottom: 1.5px;
            font-weight: 700;
            color: #1e1b4b;
        }
        .mat-list li {
            margin-bottom: 1.5px;
            font-weight: 600;
            color: #047857;
        }

        /* Signatures */
        .signatures {
            width: 100%;
            margin-top: 18px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signatures td {
            text-align: center;
            width: 33.33%;
            vertical-align: top;
            font-size: 8.5px;
            font-weight: 700;
            color: #334155;
        }
        .sig-box {
            height: 48px;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 8.5px;
            }
            .no-print, .no-print-toolbar {
                display: none !important;
            }
            .print-paper {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                display: block !important;
            }
            .item-table th {
                background-color: #e2e8f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .highlight-box.spk { background: #0f172a !important; color: #ffffff !important; -webkit-print-color-adjust: exact; }
            .highlight-box.jasa { background: #4f46e5 !important; color: #ffffff !important; -webkit-print-color-adjust: exact; }
            .highlight-box.mat { background: #059669 !important; color: #ffffff !important; -webkit-print-color-adjust: exact; }
            .breakdown-container { background: #f8fafc !important; border: 1px solid #cbd5e1 !important; -webkit-print-color-adjust: exact; }
            .breakdown-chip.jasa { background: #eef2ff !important; color: #312e81 !important; border: 1px solid #c7d2fe !important; -webkit-print-color-adjust: exact; }
            .breakdown-chip.jasa .chip-qty { background: #4f46e5 !important; color: #ffffff !important; -webkit-print-color-adjust: exact; }
            .breakdown-chip.mat { background: #ecfdf5 !important; color: #064e3b !important; border: 1px solid #a7f3d0 !important; -webkit-print-color-adjust: exact; }
            .breakdown-chip.mat .chip-qty { background: #059669 !important; color: #ffffff !important; -webkit-print-color-adjust: exact; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Screen-Only Toolbar -->
    <div class="no-print-toolbar">
        <a href="{{ route('surat-jalan.show', $suratJalan->id) }}" class="btn-back">
            ← Kembali ke Detail Surat Jalan
        </a>
        <button onclick="window.print()" class="btn-print">
            🖨️ Cetak Dokumen Surat Jalan
        </button>
    </div>

    <!-- Printable Paper Canvas (A4 Portrait) -->
    <div class="print-paper">
        <div>
            <!-- Header Section -->
            <div class="header">
                <h1>SURAT JALAN SERAH-TERIMA INTERNAL WORKSHOP</h1>
                <p>Sistem Workshop Management • Divisi Sortir, Produksi & Quality Control</p>
            </div>

            @php
                $totalSpk = $suratJalan->items->count();
                $totalJasa = $suratJalan->items->sum(function($item) {
                    $wo = $item->workOrder;
                    if (!$wo) return 0;
                    return ($wo->workOrderServices && $wo->workOrderServices->isNotEmpty()) 
                        ? $wo->workOrderServices->count() 
                        : ($wo->services?->count() ?? 0);
                });
                $totalMaterial = $suratJalan->items->sum(function($item) {
                    return $item->workOrder?->materials?->count() ?? 0;
                });

                // Breakdown Layanan Jasa (Mendukung workOrderServices & services)
                $serviceBreakdown = [];
                foreach ($suratJalan->items as $item) {
                    $wo = $item->workOrder;
                    if (!$wo) continue;

                    $services = ($wo->workOrderServices && $wo->workOrderServices->isNotEmpty())
                        ? $wo->workOrderServices
                        : ($wo->services ?? collect());

                    foreach ($services as $srv) {
                        $serviceName = is_a($srv, \App\Models\WorkOrderService::class)
                            ? ($srv->custom_service_name ?: ($srv->service?->name ?: ($srv->category_name ?: 'Layanan Servis')))
                            : ($srv->pivot->custom_service_name ?? $srv->name ?? $srv->service_name ?? 'Layanan Servis');

                        $serviceBreakdown[$serviceName] = ($serviceBreakdown[$serviceName] ?? 0) + 1;
                    }
                }
                arsort($serviceBreakdown);

                // Breakdown Bahan Baku / Material
                $materialBreakdown = [];
                foreach ($suratJalan->items as $item) {
                    if ($item->workOrder && $item->workOrder->materials) {
                        foreach ($item->workOrder->materials as $mat) {
                            $matName = $mat->name;
                            $qty = (float)($mat->pivot->quantity ?? 1);
                            $unit = $mat->unit ?? 'pcs';
                            if (!isset($materialBreakdown[$matName])) {
                                $materialBreakdown[$matName] = ['qty' => 0, 'unit' => $unit];
                            }
                            $materialBreakdown[$matName]['qty'] += $qty;
                        }
                    }
                }
                uasort($materialBreakdown, fn($a, $b) => $b['qty'] <=> $a['qty']);
            @endphp

            <!-- Summary Highlight Bar -->
            <div class="highlight-bar">
                <div class="highlight-box spk">
                    <span class="highlight-label">TOTAL MUATAN SPK</span>
                    <span class="highlight-val">📦 {{ $totalSpk }} Unit Sepatu</span>
                </div>
                <div class="highlight-box jasa">
                    <span class="highlight-label">TOTAL LAYANAN JASA</span>
                    <span class="highlight-val">🔨 {{ $totalJasa }} Layanan</span>
                </div>
                <div class="highlight-box mat">
                    <span class="highlight-label">TOTAL BAHAN BAKU</span>
                    <span class="highlight-val">🧵 {{ $totalMaterial }} Material Terkait</span>
                </div>
            </div>

            <!-- Compact Sub-Summary Breakdown Chips -->
            @if(!empty($serviceBreakdown) || !empty($materialBreakdown))
                <div class="breakdown-container">
                    @if(!empty($serviceBreakdown))
                        <div class="breakdown-row">
                            <span class="breakdown-title jasa">
                                <span>🔨</span> Rincian Jasa ({{ count($serviceBreakdown) }}):
                            </span>
                            <div class="breakdown-chips">
                                @foreach($serviceBreakdown as $name => $count)
                                    <span class="breakdown-chip jasa">
                                        <span>{{ $name }}</span>
                                        <span class="chip-qty">{{ $count }}x</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!empty($materialBreakdown))
                        <div class="breakdown-row">
                            <span class="breakdown-title mat">
                                <span>🧵</span> Bahan Baku ({{ count($materialBreakdown) }}):
                            </span>
                            <div class="breakdown-chips">
                                @foreach($materialBreakdown as $name => $data)
                                    <span class="breakdown-chip mat">
                                        <span>{{ $name }}</span>
                                        <span class="chip-qty">{{ $data['qty'] }} {{ $data['unit'] }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Meta Table Information -->
            <table class="meta-table">
                <tr>
                    <td style="width: 15%; font-weight: bold; color: #64748b;">Nomor Surat:</td>
                    <td style="width: 35%; font-family: 'JetBrains Mono', monospace; font-weight: bold; font-size: 10px; color: #0f172a;">{{ $suratJalan->nomor_surat }}</td>
                    <td style="width: 15%; font-weight: bold; color: #64748b;">Tanggal Kirim:</td>
                    <td style="width: 35%; font-weight: 600;">{{ $suratJalan->dikirim_at ? $suratJalan->dikirim_at->translatedFormat('d F Y H:i') : '-' }} WIB</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #64748b;">Rute Serah Terima:</td>
                    <td><strong style="color: #4f46e5;">{{ $suratJalan->jenis_serah_terima === 'sortir_to_produksi' ? 'SORTIR ➔ PRODUKSI' : 'PRODUKSI ➔ QC' }}</strong></td>
                    <td style="font-weight: bold; color: #64748b;">Pengirim (PIC):</td>
                    <td><strong>{{ $suratJalan->pengirim?->name ?? 'Admin Workshop' }}</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #64748b;">Status Dokumen:</td>
                    <td><strong style="text-transform: uppercase; color: #059669;">{{ $suratJalan->status }}</strong></td>
                    <td style="font-weight: bold; color: #64748b;">Penerima (PIC):</td>
                    <td><strong>{{ $suratJalan->penerima?->name ?? '....................................' }}</strong></td>
                </tr>
            </table>

            <!-- Item Table with Technician & Material Columns -->
            <table class="item-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 18%; text-align: left;">Nomor SPK & Customer</th>
                        <th style="width: 16%; text-align: left;">Merk & Tipe Sepatu</th>
                        <th style="width: 22%; text-align: left;">Rincian Jasa</th>
                        <th style="width: 22%; text-align: left;">Teknisi Pelaksana Stasiun</th>
                        <th style="width: 18%; text-align: left;">Bahan Baku / Material</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suratJalan->items as $index => $item)
                        @php
                            $wo = $item->workOrder;
                            $services = ($wo?->workOrderServices && $wo->workOrderServices->isNotEmpty())
                                ? $wo->workOrderServices
                                : ($wo?->services ?? collect());
                            $materials = $wo?->materials ?? collect();
                        @endphp
                        <tr>
                            <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                            <td>
                                <strong style="font-family: 'JetBrains Mono', monospace; font-size: 9px;">{{ $wo?->spk_number }}</strong>
                                @if($wo?->has_active_oto)
                                    <span style="background: #f59e0b; color: #000; font-size: 7.5px; font-weight: 900; padding: 1px 3px; border-radius: 2px; margin-left: 2px;">OTO</span>
                                @endif
                                <br>
                                <span style="color: #334155; font-weight: bold;">{{ $wo?->customer_name }}</span>
                            </td>
                            <td>
                                <strong>{{ $wo?->shoe_brand }}</strong> {{ $wo?->shoe_type }}<br>
                                <span style="color: #64748b; font-size: 8px;">Size: {{ $wo?->shoe_size ?? '-' }}</span>
                            </td>
                            <td>
                                @if ($services->isNotEmpty())
                                    <ul class="service-list">
                                        @foreach ($services as $srv)
                                            @php
                                                $serviceName = is_a($srv, \App\Models\WorkOrderService::class)
                                                    ? ($srv->custom_service_name ?: ($srv->service?->name ?: ($srv->category_name ?: 'Layanan Servis')))
                                                    : ($srv->pivot->custom_service_name ?? $srv->name ?? $srv->service_name ?? 'Layanan Servis');
                                            @endphp
                                            <li>{{ $serviceName }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span style="color: #94a3b8; font-style: italic;">- Tidak Ada Jasa -</span>
                                @endif
                            </td>
                            <td>
                                <ul class="tech-list">
                                    @if($wo?->needs_prod_upper)
                                        <li>Upper: <strong>{{ $wo?->prodUpperBy?->name ?? 'Belum Ditugaskan' }}</strong></li>
                                    @endif
                                    @if($wo?->needs_prod_sol)
                                        <li>Soling: <strong>{{ $wo?->prodSolBy?->name ?? 'Belum Ditugaskan' }}</strong></li>
                                    @endif
                                    @if($wo?->needs_prod_jahit)
                                        <li>QC Jahit: <strong>{{ $wo?->qcJahitBy?->name ?? 'Belum Ditugaskan' }}</strong></li>
                                    @endif
                                    @if($wo?->needs_prod_treatment)
                                        <li style="color: #64748b;">Treatment: <em>(Tahap QC)</em></li>
                                    @endif
                                    @if(!$wo?->needs_prod_upper && !$wo?->needs_prod_sol && !$wo?->needs_prod_jahit && !$wo?->needs_prod_treatment)
                                        <li style="color: #94a3b8;">- Standar Workshop -</li>
                                    @endif
                                </ul>
                            </td>
                            <td>
                                @if ($materials->isNotEmpty())
                                    <ul class="mat-list">
                                        @foreach ($materials as $mat)
                                            <li>
                                                {{ $mat->name }} ({{ $mat->pivot->quantity }} {{ $mat->unit ?? 'pcs' }})
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span style="color: #94a3b8; font-style: italic; font-size: 8px;">- Tidak Butuh Material -</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($suratJalan->catatan)
                <div style="background: #f8fafc; padding: 5px 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 12px; font-size: 8.5px;">
                    <strong>Catatan Tambahan:</strong> {{ $suratJalan->catatan }}
                </div>
            @endif
        </div>

        <!-- Signatures Section -->
        <table class="signatures">
            <tr>
                <td>
                    Pengirim (Sortir / Produksi)<br>
                    <div class="sig-box"></div>
                    <strong>( {{ $suratJalan->pengirim?->name ?? 'Admin Workshop' }} )</strong>
                </td>
                <td>
                    Pembawa / Kurir Internal<br>
                    <div class="sig-box"></div>
                    <strong>( .................................... )</strong>
                </td>
                <td>
                    Penerima (Produksi / QC)<br>
                    <div class="sig-box"></div>
                    <strong>( {{ $suratJalan->penerima?->name ?? '....................................' }} )</strong>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
