<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $order->spk_number }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            @page { size: A5 landscape; margin: 8mm; }
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 11px; color: #1e293b; background: #f8fafc; }
        .page { max-width: 700px; margin: 20px auto; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1e293b; padding-bottom: 12px; margin-bottom: 16px; }
        .header-left h1 { font-size: 18px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; }
        .header-left p { font-size: 9px; color: #64748b; margin-top: 2px; }
        .header-right { text-align: right; }
        .header-right .spk { font-size: 16px; font-weight: 900; font-family: 'Courier New', monospace; }
        .header-right .date { font-size: 9px; color: #64748b; }
        .section { margin-bottom: 14px; }
        .section-title { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1.5px; color: #64748b; margin-bottom: 6px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 3px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 16px; }
        .info-item label { font-size: 8px; font-weight: 700; text-transform: uppercase; color: #94a3b8; display: block; }
        .info-item span { font-size: 11px; font-weight: 700; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        table th { background: #f1f5f9; font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; padding: 6px 8px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        table td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; }
        .signature-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-top: 24px; text-align: center; }
        .signature-box { border-top: 1px solid #cbd5e1; padding-top: 4px; }
        .signature-box .role { font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #64748b; }
        .signature-box .line { height: 50px; }
        .footer { margin-top: 16px; text-align: center; font-size: 8px; color: #94a3b8; }
        .btn-print { display: inline-flex; align-items: center; gap: 6px; padding: 10px 24px; background: #1e293b; color: #fff; border: none; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; margin: 20px auto; }
        .btn-print:hover { background: #334155; }
        .print-bar { text-align: center; }
    </style>
</head>
<body>
    <div class="print-bar no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Surat Jalan</button>
        <a href="{{ url()->previous() }}" style="margin-left: 12px; font-size: 12px; color: #64748b; text-decoration: underline;">← Kembali</a>
    </div>

    <div class="page">
        <div class="header">
            <div class="header-left">
                <h1>Surat Jalan</h1>
                <p>Workshop Management System — Dokumen Pengiriman Internal</p>
            </div>
            <div class="header-right">
                <div class="spk">{{ $order->spk_number }}</div>
                <div class="date">Tanggal: {{ now()->format('d M Y H:i') }}</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Data Pelanggan & Sepatu</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Nama Pelanggan</label>
                    <span>{{ $order->customer?->name ?? $order->customer_name ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <label>Telepon</label>
                    <span>{{ $order->customer?->phone ?? $order->customer_phone ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <label>Brand / Tipe Sepatu</label>
                    <span>{{ $order->shoe_brand ?? '-' }} — {{ $order->shoe_type ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <label>Warna / Ukuran</label>
                    <span>{{ $order->shoe_color ?? '-' }} / {{ $order->shoe_size ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <label>Prioritas</label>
                    <span>{{ $order->priority ?? 'Regular' }}</span>
                </div>
                <div class="info-item">
                    <label>Estimasi Selesai</label>
                    <span>{{ $order->estimated_completion_date ? \Carbon\Carbon::parse($order->estimated_completion_date)->format('d M Y') : '-' }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Layanan Jasa</div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Layanan</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->services as $i => $service)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td style="font-weight: 700;">{{ $service->name }}</td>
                        <td>{{ $service->category ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align: center; color: #94a3b8;">—</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($order->materials->isNotEmpty())
        <div class="section">
            <div class="section-title">Material Terlampir</div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Material</th>
                        <th>Qty</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->materials as $i => $mat)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td style="font-weight: 700;">{{ $mat->name }}</td>
                        <td>{{ $mat->pivot->quantity }}</td>
                        <td>{{ $mat->pivot->status }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="signature-row">
            <div class="signature-box">
                <div class="line"></div>
                <div class="role">Pengirim (Admin Workshop)</div>
            </div>
            <div class="signature-box">
                <div class="line"></div>
                <div class="role">Pembawa (Mamang)</div>
            </div>
            <div class="signature-box">
                <div class="line"></div>
                <div class="role">Penerima (Produksi)</div>
            </div>
        </div>

        <div class="footer">
            Dicetak otomatis oleh Sistem Workshop &mdash; {{ now()->format('d M Y H:i:s') }} &mdash; Dokumen ini SAH tanpa tanda tangan basah.
        </div>
    </div>
</body>
</html>
