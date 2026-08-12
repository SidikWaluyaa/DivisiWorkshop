<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $suratJalan->nomor_surat }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; margin: 20px; color: #0f172a; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; }
        .header p { margin: 2px 0 0; font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .highlight-bar { display: flex; gap: 12px; margin-bottom: 15px; }
        .highlight-box { flex: 1; padding: 10px 14px; border-radius: 8px; text-align: center; }
        .highlight-box.spk { background: #0f172a; color: #ffffff; }
        .highlight-box.jasa { background: #4f46e5; color: #ffffff; }
        .highlight-label { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; block; }
        .highlight-val { font-size: 18px; font-weight: 900; margin-top: 2px; block; }

        .meta-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; }
        .meta-table td { padding: 6px 10px; vertical-align: top; font-size: 11px; }

        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .item-table th, .item-table td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; font-size: 11px; vertical-align: top; }
        .item-table th { background-color: #f1f5f9; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; color: #334155; }
        
        .service-list { margin: 0; padding-left: 14px; }
        .service-list li { margin-bottom: 2px; font-weight: 600; color: #1e293b; }

        .signatures { width: 100%; margin-top: 35px; }
        .signatures td { text-align: center; width: 33%; vertical-align: top; font-size: 11px; font-weight: 700; }
        .sig-box { height: 55px; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 18px; background: #4f46e5; color: #fff; font-weight: bold; border: none; border-radius: 8px; cursor: pointer;">🖨️ Cetak Dokumen Surat Jalan</button>
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
    @endphp

    {{-- Highlight Ringkasan SPK & Jasa --}}
    <div class="highlight-bar">
        <div class="highlight-box spk">
            <span class="highlight-label">TOTAL MUATAN SPK</span>
            <span class="highlight-val">📦 {{ $totalSpk }} SPK (Unit Sepatu)</span>
        </div>
        <div class="highlight-box jasa">
            <span class="highlight-label">TOTAL JASA / LAYANAN</span>
            <span class="highlight-val">🔨 {{ $totalJasa }} Layanan Pengerjaan</span>
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 18%;"><strong>Nomor Surat:</strong></td>
            <td style="width: 32%; font-family: monospace; font-weight: bold; font-size: 12px;">{{ $suratJalan->nomor_surat }}</td>
            <td style="width: 18%;"><strong>Tanggal Kirim:</strong></td>
            <td style="width: 32%;">{{ $suratJalan->dikirim_at ? $suratJalan->dikirim_at->translatedFormat('d F Y H:i') : '-' }} WIB</td>
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
            <td>{{ $suratJalan->penerima?->name ?? '....................' }}</td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 22%;">Nomor SPK & Customer</th>
                <th style="width: 22%;">Merk & Tipe Sepatu</th>
                <th style="width: 28%;">Rincian Jasa / Layanan</th>
                <th style="width: 12%; text-align: center;">Est. Selesai</th>
                <th style="width: 12%;">Catatan / Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($suratJalan->items as $index => $item)
                @php
                    $wo = $item->workOrder;
                    $services = $wo?->services ?? collect();
                    $estDate = $wo?->new_estimation_date ?? $wo?->estimation_date;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <strong style="font-family: monospace; font-size: 11px;">{{ $wo?->spk_number }}</strong><br>
                        <span style="color: #475569; font-weight: 600;">{{ $wo?->customer_name }}</span>
                    </td>
                    <td>
                        <strong>{{ $wo?->shoe_brand }}</strong> {{ $wo?->shoe_type }}<br>
                        <span style="color: #64748b; font-size: 10px;">Size: {{ $wo?->shoe_size ?? '-' }}</span>
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
                            <span style="font-size: 9px; font-weight: bold; color: #4f46e5; margin-top: 4px; display: inline-block;">Total: {{ $services->count() }} Jasa</span>
                        @else
                            <span style="color: #94a3b8; italic;">- Tidak Ada Rincian Jasa -</span>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: bold; color: #0f172a;">
                        {{ $estDate ? $estDate->translatedFormat('d M Y') : '-' }}
                    </td>
                    <td>&nbsp;</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($suratJalan->catatan)
        <div style="background: #f8fafc; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 20px;">
            <strong>Catatan Tambahan:</strong> {{ $suratJalan->catatan }}
        </div>
    @endif

    <table class="signatures">
        <tr>
            <td>
                <p>Pengirim (Sortir / Produksi)</p>
                <div class="sig-box"></div>
                <p>( {{ $suratJalan->pengirim?->name ?? '....................' }} )</p>
            </td>
            <td>
                <p>Pembawa / Kurir Internal</p>
                <div class="sig-box"></div>
                <p>( .................... )</p>
            </td>
            <td>
                <p>Penerima (Produksi / QC)</p>
                <div class="sig-box"></div>
                <p>( {{ $suratJalan->penerima?->name ?? '....................' }} )</p>
            </td>
        </tr>
    </table>

</body>
</html>
