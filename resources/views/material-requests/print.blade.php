<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembelian — {{ $materialRequest->request_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #0f172a;
            background: #ffffff;
            line-height: 1.5;
        }

        /* ══════════════════════════════════════
           Layout
        ══════════════════════════════════════ */
        .page {
            max-width: 760px;
            margin: 24px auto;
            padding: 0 24px;
        }

        /* ══════════════════════════════════════
           Header / Kop
        ══════════════════════════════════════ */
        .kop {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding-bottom: 14px;
            border-bottom: 2.5px solid #0f172a;
            margin-bottom: 16px;
        }

        .kop-left h1 {
            font-size: 19px;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #0f172a;
        }

        .kop-left p {
            font-size: 10px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 2px;
        }

        .kop-right {
            text-align: right;
        }

        .kop-right .nomor-nota {
            font-size: 14px;
            font-weight: 900;
            font-family: monospace;
            color: #1e293b;
            letter-spacing: 0.5px;
        }

        .kop-right .tipe-badge {
            display: inline-block;
            margin-top: 4px;
            padding: 3px 10px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border-radius: 999px;
            color: #fff;
        }

        .tipe-belanja { background: #f59e0b; }
        .tipe-produksi { background: #22AF85; }

        .status-badge {
            display: inline-block;
            margin-top: 4px;
            padding: 3px 10px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border-radius: 999px;
            border: 1.5px solid currentColor;
        }

        .status-PENDING   { color: #d97706; background: #fef3c7; }
        .status-APPROVED  { color: #059669; background: #d1fae5; }
        .status-REJECTED  { color: #dc2626; background: #fee2e2; }
        .status-PURCHASED { color: #2563eb; background: #dbeafe; }
        .status-CANCELLED { color: #64748b; background: #f1f5f9; }

        /* ══════════════════════════════════════
           Summary Bar
        ══════════════════════════════════════ */
        .summary-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .summary-box {
            flex: 1;
            padding: 10px 14px;
            border-radius: 8px;
            text-align: center;
        }

        .summary-box.dark { background: #0f172a; color: #fff; }
        .summary-box.green { background: #22AF85; color: #fff; }
        .summary-box.indigo { background: #4f46e5; color: #fff; }

        .summary-label {
            display: block;
            font-size: 8.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            opacity: 0.85;
            margin-bottom: 3px;
        }

        .summary-val {
            display: block;
            font-size: 15px;
            font-weight: 900;
        }

        /* ══════════════════════════════════════
           Meta Info Grid
        ══════════════════════════════════════ */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 16px;
            background: #f8fafc;
        }

        .meta-cell {
            padding: 9px 12px;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .meta-cell:nth-child(3n) { border-right: none; }

        .meta-label {
            font-size: 8.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .meta-val {
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
        }

        .meta-val.accent { color: #22AF85; }
        .meta-val.mono { font-family: monospace; font-size: 10px; }

        /* ══════════════════════════════════════
           SPK List
        ══════════════════════════════════════ */
        .spk-section {
            margin-bottom: 16px;
        }

        .spk-section-title {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .spk-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .spk-chip {
            display: inline-block;
            padding: 4px 10px;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
            color: #4338ca;
            font-family: monospace;
        }

        /* ══════════════════════════════════════
           Notes
        ══════════════════════════════════════ */
        .notes-box {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 10px;
            color: #78350f;
            font-style: italic;
        }

        .notes-box strong {
            font-style: normal;
            font-weight: 800;
            display: block;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #92400e;
            margin-bottom: 2px;
        }

        /* ══════════════════════════════════════
           Items Table
        ══════════════════════════════════════ */
        .section-title {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #0f172a;
            padding: 8px 12px;
            background: #f1f5f9;
            border-radius: 8px 8px 0 0;
            border: 1px solid #e2e8f0;
            border-bottom: none;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 8px 8px;
            overflow: hidden;
        }

        .item-table th {
            background: #f8fafc;
            padding: 8px 10px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            border-bottom: 1.5px solid #e2e8f0;
            text-align: left;
        }

        .item-table th.right { text-align: right; }
        .item-table th.center { text-align: center; }

        .item-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
            font-size: 10.5px;
        }

        .item-table tr:last-child td { border-bottom: none; }

        .item-name {
            font-weight: 700;
            color: #1e293b;
        }

        .item-spec {
            font-size: 9.5px;
            color: #94a3b8;
            margin-top: 1px;
        }

        .spk-tag {
            display: inline-block;
            margin-top: 3px;
            padding: 2px 6px;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 800;
            color: #4338ca;
            font-family: monospace;
        }

        .item-table td.right { text-align: right; }
        .item-table td.center { text-align: center; }

        .qty-badge {
            display: inline-block;
            padding: 3px 8px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            font-weight: 800;
            font-size: 10px;
            color: #334155;
        }

        .tfoot-row td {
            background: #0f172a;
            color: #fff;
            font-weight: 900;
            font-size: 12px;
            padding: 10px 12px;
        }

        /* ══════════════════════════════════════
           Signature Section
        ══════════════════════════════════════ */
        .signatures {
            margin-top: 28px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .sig-box {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            text-align: center;
        }

        .sig-role {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            margin-bottom: 2px;
        }

        .sig-name {
            font-size: 10.5px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .sig-area {
            height: 52px;
            border-bottom: 1.5px solid #cbd5e1;
            margin: 6px 0 4px;
        }

        .sig-date-line {
            font-size: 9px;
            color: #94a3b8;
            font-weight: 600;
        }

        /* ══════════════════════════════════════
           Footer
        ══════════════════════════════════════ */
        .doc-footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px dashed #cbd5e1;
            font-size: 8.5px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }

        /* ══════════════════════════════════════
           Print Controls (no-print)
        ══════════════════════════════════════ */
        .no-print {
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .no-print-title {
            font-size: 12px;
            font-weight: 800;
            color: #1e293b;
        }

        .no-print-actions {
            display: flex;
            gap: 8px;
        }

        .btn-print {
            padding: 8px 18px;
            background: #4f46e5;
            color: #fff;
            font-weight: 800;
            font-size: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }

        .btn-back {
            padding: 8px 16px;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            color: #64748b;
            font-weight: 700;
            font-size: 12px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .page { margin: 0; padding: 12px; }
        }
    </style>
</head>
<body>

{{-- ════ Print Controls (hidden on print) ════ --}}
<div class="no-print">
    <span class="no-print-title">🖨️ Preview Dokumen — {{ $materialRequest->request_number }}</span>
    <div class="no-print-actions">
        <a href="{{ route('material-requests.show', $materialRequest->id) }}" class="btn-back">← Kembali</a>
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen</button>
    </div>
</div>

<div class="page">

    {{-- ════ KOP SURAT ════ --}}
    <div class="kop">
        <div class="kop-left">
            <h1>Nota Pembelian Material</h1>
            <p>Workshop Management System • Divisi Finlog & Pengadaan</p>
        </div>
        <div class="kop-right">
            <div class="nomor-nota">{{ $materialRequest->request_number }}</div>
            <br>
            @if($materialRequest->type === 'SHOPPING')
                <span class="tipe-badge tipe-belanja">🛒 Belanja</span>
            @else
                <span class="tipe-badge tipe-produksi">📦 PO Produksi</span>
            @endif
            &nbsp;
            <span class="status-badge status-{{ $materialRequest->status }}">{{ $materialRequest->status }}</span>
        </div>
    </div>

    {{-- ════ RINGKASAN ════ --}}
    @php
        $totalItems = $materialRequest->items->count();
        $totalSpkCount = $spkList->count();
        $totalNilai = $materialRequest->total_estimated_cost;
    @endphp

    <div class="summary-bar">
        <div class="summary-box dark">
            <span class="summary-label">📦 Total Item Material</span>
            <span class="summary-val">{{ $totalItems }} Item</span>
        </div>
        <div class="summary-box green">
            <span class="summary-label">🔖 SPK Terlibat</span>
            <span class="summary-val">{{ $totalSpkCount > 0 ? $totalSpkCount . ' SPK' : 'Umum' }}</span>
        </div>
        <div class="summary-box indigo">
            <span class="summary-label">💰 Total Estimasi Nilai</span>
            <span class="summary-val" style="font-size: 13px;">Rp {{ number_format($totalNilai, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- ════ INFORMASI NOTA ════ --}}
    <div class="meta-grid">
        <div class="meta-cell">
            <div class="meta-label">Nomor Nota</div>
            <div class="meta-val mono">{{ $materialRequest->request_number }}</div>
        </div>
        <div class="meta-cell">
            <div class="meta-label">Tanggal Pengajuan</div>
            <div class="meta-val">{{ $materialRequest->created_at->translatedFormat('d F Y, H:i') }} WIB</div>
        </div>
        <div class="meta-cell">
            <div class="meta-label">Tipe Nota</div>
            <div class="meta-val">{{ $materialRequest->type === 'SHOPPING' ? 'Belanja Reguler' : 'PO Produksi' }}</div>
        </div>
        <div class="meta-cell">
            <div class="meta-label">Diminta Oleh</div>
            <div class="meta-val">{{ $materialRequest->requestedBy->name ?? 'N/A' }}</div>
        </div>
        <div class="meta-cell">
            <div class="meta-label">Status Saat Cetak</div>
            <div class="meta-val">{{ $materialRequest->status }}</div>
        </div>
        @if($materialRequest->approved_by)
        <div class="meta-cell">
            <div class="meta-label">Disetujui Oleh</div>
            <div class="meta-val accent">{{ $materialRequest->approvedBy->name ?? 'N/A' }}</div>
        </div>
        <div class="meta-cell">
            <div class="meta-label">Tanggal Approval</div>
            <div class="meta-val">{{ $materialRequest->approved_at?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</div>
        </div>
        @else
        <div class="meta-cell">
            <div class="meta-label">Disetujui Oleh</div>
            <div class="meta-val" style="color: #94a3b8;">Belum disetujui</div>
        </div>
        @endif
        @if($materialRequest->work_order_id)
        <div class="meta-cell">
            <div class="meta-label">Work Order Utama</div>
            <div class="meta-val mono">{{ $materialRequest->workOrder->spk_number ?? 'N/A' }}</div>
        </div>
        @else
        <div class="meta-cell">
            <div class="meta-label">Tanggal Cetak</div>
            <div class="meta-val">{{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
        </div>
        @endif
    </div>

    {{-- ════ SPK TERLIBAT ════ --}}
    @if($spkList->count() > 0)
    <div class="spk-section">
        <div class="spk-section-title">🔖 SPK / Work Order yang Terlibat</div>
        <div class="spk-list">
            @foreach($spkList as $wo)
                <span class="spk-chip">{{ $wo->spk_number }} — {{ $wo->customer_name }}</span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ════ CATATAN ════ --}}
    @if($materialRequest->notes)
    <div class="notes-box">
        <strong>📝 Catatan / Keterangan:</strong>
        "{{ $materialRequest->notes }}"
    </div>
    @endif

    {{-- ════ TABEL MATERIAL ════ --}}
    <div class="section-title">📋 Daftar Item Material Yang Diajukan</div>
    <table class="item-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">Material & Spesifikasi</th>
                <th style="width: 15%;">SPK Terkait</th>
                <th class="center" style="width: 12%;">Qty</th>
                <th class="right" style="width: 15%;">Harga Satuan</th>
                <th class="right" style="width: 18%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materialRequest->items as $index => $item)
                @php
                    $itemWo = $item->workOrder ?? $materialRequest->workOrder;
                @endphp
                <tr>
                    <td style="color: #94a3b8; font-weight: 700;">{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->material_name }}</div>
                        @if($item->specification)
                            <div class="item-spec">{{ $item->specification }}</div>
                        @endif
                        @if($item->notes)
                            <div class="item-spec" style="color: #b45309; font-style: italic; margin-top: 2px;">💬 {{ $item->notes }}</div>
                        @endif
                    </td>
                    <td>
                        @if($itemWo)
                            <span class="spk-tag">{{ $itemWo->spk_number }}</span>
                        @else
                            <span style="color: #94a3b8; font-size: 9.5px;">Umum</span>
                        @endif
                    </td>
                    <td class="center">
                        <span class="qty-badge">{{ $item->quantity }} {{ $item->unit ?? 'Unit' }}</span>
                    </td>
                    <td class="right">Rp {{ number_format($item->estimated_price, 0, ',', '.') }}</td>
                    <td class="right" style="font-weight: 800; color: #1e293b;">Rp {{ number_format($item->getSubtotal(), 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="tfoot-row">
                <td colspan="5" class="right" style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.4px; color: #cbd5e1;">Total Estimasi Nilai Pembelian</td>
                <td class="right" style="font-size: 14px;">Rp {{ number_format($materialRequest->total_estimated_cost, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- ════ TANDA TANGAN ════ --}}
    <div class="signatures">
        <div class="sig-box">
            <div class="sig-role">Pemohon (Requester)</div>
            <div class="sig-name">{{ $materialRequest->requestedBy->name ?? '_______________' }}</div>
            <div class="sig-area"></div>
            <div class="sig-date-line">Tanda Tangan & Tanggal</div>
        </div>
        <div class="sig-box">
            <div class="sig-role">Penyetuju (Finlog / Approver)</div>
            <div class="sig-name">
                @if($materialRequest->approvedBy)
                    {{ $materialRequest->approvedBy->name }}
                @else
                    _______________
                @endif
            </div>
            <div class="sig-area"></div>
            <div class="sig-date-line">Tanda Tangan & Tanggal</div>
        </div>
    </div>

    {{-- ════ FOOTER DOKUMEN ════ --}}
    <div class="doc-footer">
        <span>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB &nbsp;•&nbsp; Sistem Workshop Management</span>
        <span>{{ $materialRequest->request_number }} &nbsp;•&nbsp; Status: {{ $materialRequest->status }}</span>
    </div>

</div>

</body>
</html>
