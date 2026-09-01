<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $suratJalan->nomor_surat }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 10px; 
            margin: 0; 
            color: #0f172a; 
            line-height: 1.4; 
            background: #ffffff;
        }
        .no-print { 
            margin: 15px 0; 
            text-align: right; 
        }
        .btn-print {
            padding: 9px 20px; 
            background: #4f46e5; 
            color: #ffffff; 
            font-weight: 800; 
            font-size: 12px;
            border: none; 
            border-radius: 8px; 
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);
        }
        .header { 
            text-align: center; 
            border-bottom: 2.5px solid #0f172a; 
            padding-bottom: 8px; 
            margin-bottom: 12px; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 16px; 
            font-weight: 900; 
            letter-spacing: 1px; 
            text-transform: uppercase; 
        }
        .header p { 
            margin: 2px 0 0; 
            font-size: 9.5px; 
            font-weight: 700; 
            color: #475569; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }
        
        .highlight-bar { 
            display: flex; 
            gap: 10px; 
            margin-bottom: 12px; 
        }
        .highlight-box { 
            flex: 1; 
            padding: 8px 12px; 
            border-radius: 6px; 
            text-align: center; 
        }
        .highlight-box.spk { background: #0f172a; color: #ffffff; }
        .highlight-box.jasa { background: #4f46e5; color: #ffffff; }
        .highlight-box.mat { background: #059669; color: #ffffff; }
        .highlight-label { 
            font-size: 8px; 
            font-weight: 800; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            display: block;
            opacity: 0.9; 
        }
        .highlight-val { 
            font-size: 13px; 
            font-weight: 900; 
            margin-top: 1px; 
            display: block; 
        }

        .meta-table { 
            width: 100%; 
            margin-bottom: 12px; 
            border-collapse: collapse; 
            background: #f8fafc; 
            border: 1px solid #cbd5e1; 
            border-radius: 6px; 
        }
        .meta-table td { 
            padding: 5px 8px; 
            vertical-align: top; 
            font-size: 10px; 
        }

        .item-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 16px; 
        }
        .item-table th, .item-table td { 
            border: 1px solid #94a3b8; 
            padding: 6px 8px; 
            text-align: left; 
            font-size: 9.5px; 
            vertical-align: top; 
        }
        .item-table th { 
            background-color: #f1f5f9; 
            font-size: 9px; 
            font-weight: 900; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            color: #1e293b; 
        }
        
        .service-list, .mat-list { 
            margin: 0; 
            padding-left: 12px; 
        }
        .service-list li { 
            margin-bottom: 1.5px; 
            font-weight: 600; 
            color: #0f172a; 
        }
        .mat-list li {
            margin-bottom: 1.5px;
            font-weight: 600;
            color: #047857;
        }

        .signatures { 
            width: 100%; 
            margin-top: 25px; 
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signatures td { 
            text-align: center; 
            width: 33.33%; 
            vertical-align: top; 
            font-size: 9.5px; 
            font-weight: 700; 
        }
        .sig-box { 
            height: 48px; 
        }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; font-size: 9.5px; }
            .item-table th { background-color: #e2e8f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .highlight-box.spk { background: #0f172a !important; color: #ffffff !important; -webkit-print-color-adjust: exact; }
            .highlight-box.jasa { background: #4f46e5 !important; color: #ffffff !important; -webkit-print-color-adjust: exact; }
            .highlight-box.mat { background: #059669 !important; color: #ffffff !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen Surat Jalan</button>
    </div>

    <div class="header">
        <h1>SURAT JALAN SERAH-TERIMA INTERNAL WORKSHOP</h1>
        <p>Sistem Workshop Management • Divisi Sortir & Produksi</p>
    </div>

    @php
        $totalSpk = $suratJalan->items->count();
        $totalJasa = $suratJalan->items->sum(function($item) {
            return $item->workOrder?->services?->count() ?? 0;
        });
        $totalMaterial = $suratJalan->items->sum(function($item) {
            return $item->workOrder?->materials?->count() ?? 0;
        });
    @endphp

    {{-- Highlight Ringkasan 3-Box --}}
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

    {{-- Meta Table Info --}}
    <table class="meta-table">
        <tr>
            <td style="width: 16%;"><strong>Nomor Surat:</strong></td>
            <td style="width: 34%; font-family: monospace; font-weight: bold; font-size: 11px;">{{ $suratJalan->nomor_surat }}</td>
            <td style="width: 16%;"><strong>Tanggal Kirim:</strong></td>
            <td style="width: 34%;">{{ $suratJalan->dikirim_at ? $suratJalan->dikirim_at->translatedFormat('d F Y H:i') : '-' }} WIB</td>
        </tr>
        <tr>
            <td><strong>Rute Serah Terima:</strong></td>
            <td><strong>{{ $suratJalan->jenis_serah_terima === 'sortir_to_produksi' ? 'SORTIR ➔ PRODUKSI' : 'PRODUKSI ➔ QC' }}</strong></td>
            <td><strong>Pengirim (PIC):</strong></td>
            <td>{{ $suratJalan->pengirim?->name ?? 'Admin Workshop' }}</td>
        </tr>
        <tr>
            <td><strong>Status Dokumen:</strong></td>
            <td><strong>{{ $suratJalan->status }}</strong></td>
            <td><strong>Penerima (PIC):</strong></td>
            <td>{{ $suratJalan->penerima?->name ?? '....................................' }}</td>
        </tr>
    </table>

    {{-- Item Table with Material Column --}}
    <table class="item-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 20%;">Nomor SPK & Customer</th>
                <th style="width: 18%;">Merk & Tipe Sepatu</th>
                <th style="width: 24%;">Rincian Jasa / Layanan</th>
                <th style="width: 20%;">Bahan Baku / Material</th>
                <th style="width: 14%; text-align: center;">Est. Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($suratJalan->items as $index => $item)
                @php
                    $wo = $item->workOrder;
                    $services = $wo?->services ?? collect();
                    $materials = $wo?->materials ?? collect();
                    $estDate = $wo?->new_estimation_date ?? $wo?->estimation_date;
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                    <td>
                        <strong style="font-family: monospace; font-size: 10px;">{{ $wo?->spk_number }}</strong>
                        @if($wo?->has_active_oto)
                            <span style="background: #f59e0b; color: #000; font-size: 8px; font-weight: 900; padding: 1px 3px; border-radius: 2px; margin-left: 2px;">OTO</span>
                        @endif
                        <br>
                        <span style="color: #334155; font-weight: bold;">{{ $wo?->customer_name }}</span>
                    </td>
                    <td>
                        <strong>{{ $wo?->shoe_brand }}</strong> {{ $wo?->shoe_type }}<br>
                        <span style="color: #64748b; font-size: 9px;">Size: {{ $wo?->shoe_size ?? '-' }}</span>
                    </td>
                    <td>
                        @if ($services->isNotEmpty())
                            <ul class="service-list">
                                @foreach ($services as $srv)
                                    @php
                                        $serviceName = $srv->pivot->custom_service_name ?? $srv->name ?? $srv->service_name ?? 'Layanan Servis';
                                    @endphp
                                    <li>{{ $serviceName }}</li>
                                @endforeach
                            </ul>
                            <span style="font-size: 8.5px; font-weight: bold; color: #4f46e5; margin-top: 2px; display: inline-block;">Total: {{ $services->count() }} Jasa</span>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">- Tidak Ada Jasa -</span>
                        @endif
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
                            <span style="color: #94a3b8; font-style: italic; font-size: 8.5px;">- Tidak Butuh Material -</span>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: bold; color: #0f172a;">
                        {{ $estDate ? $estDate->translatedFormat('d M Y') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($suratJalan->catatan)
        <div style="background: #f8fafc; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 15px; font-size: 9.5px;">
            <strong>Catatan Tambahan:</strong> {{ $suratJalan->catatan }}
        </div>
    @endif

    {{-- Signatures Section --}}
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

</body>
</html>
