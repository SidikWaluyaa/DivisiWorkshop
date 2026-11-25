<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Divisi R&D</title>
    <style>
        @page { margin: 22px 26px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9.5px; color: #1e293b; }
        h1 { font-size: 18px; margin: 0; color: #0f172a; }
        h2 { font-size: 12.5px; margin: 16px 0 6px; padding: 5px 8px; background: #22B086; color: #fff; }
        h3 { font-size: 11px; margin: 0 0 4px; color: #0f172a; }
        .muted { color: #64748b; }
        .small { font-size: 8.5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 3px 5px; vertical-align: top; text-align: left; }
        th { background: #f1f5f9; font-size: 8.5px; text-transform: uppercase; color: #475569; }
        .right { text-align: right; }
        .center { text-align: center; }
        .header td { border: none; padding: 0; }
        .summary td { text-align: center; padding: 6px 4px; }
        .summary .num { font-size: 16px; font-weight: bold; color: #0f172a; }
        .summary .lbl { font-size: 8px; text-transform: uppercase; color: #64748b; }
        .badge { padding: 1px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; background: #e2e8f0; }
        .ok { background: #d1fae5; color: #065f46; }
        .warn { background: #fef3c7; color: #92400e; }
        .bad { background: #fee2e2; color: #991b1b; }
        .info { background: #dbeafe; color: #1e40af; }
        .card { border: 1px solid #94a3b8; padding: 8px; margin-bottom: 10px; }
        .card table { margin-top: 4px; }
        .kv td { border: none; padding: 1px 6px 1px 0; }
        .kv td.k { color: #64748b; width: 95px; }
        .sub { font-size: 9px; font-weight: bold; margin: 7px 0 2px; color: #334155; }
        .photo { width: 110px; height: 82px; object-fit: cover; border: 1px solid #cbd5e1; }
        .page-break { page-break-before: always; }
        .footer { position: fixed; bottom: -12px; left: 0; right: 0; font-size: 8px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
@php
    $fmtDate = fn ($d, $f = 'd M Y H:i') => $d ? \Carbon\Carbon::parse($d)->format($f) : '-';
    $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $statusLabel = fn ($o) => $o->status instanceof \BackedEnum ? $o->status->value : (string) $o->status;
    $hasil = [
        'SUCCESS' => ['Berhasil', 'ok'], 'IN_PROGRESS' => ['Proses', 'info'],
        'NEED_REVISION' => ['Perlu Revisi', 'warn'], 'FAILED' => ['Gagal', 'bad'],
    ];
    $stageLabel = ['all' => 'Semua', 'PREPARATION' => '1. Prep', 'SORTIR' => '2. Sortir', 'PRODUCTION' => '3. Produksi', 'QC' => '4. QC'];
    $no = 0;
@endphp

<div class="footer">Laporan Divisi R&D · dicetak {{ $printedAt->format('d M Y H:i') }} WIB</div>

<table class="header">
    <tr>
        <td>
            <h1>Laporan Divisi Riset &amp; Pengembangan (R&amp;D)</h1>
            <div class="muted">Shoe Workshop · Stasiun riset formulasi, pengujian material, dan teknik reparasi khusus</div>
        </td>
        <td class="right small muted">
            Dicetak: {{ $printedAt->format('d M Y H:i') }} WIB<br>
            Oleh: {{ $printedBy ?? '-' }}<br>
            Filter: tahap {{ $stageLabel[$filters['stage']] ?? $filters['stage'] }}{{ $filters['search'] ? ' · cari "' . $filters['search'] . '"' : '' }}
        </td>
    </tr>
</table>

{{-- 1. Ringkasan --}}
<h2>1. Ringkasan</h2>
<table class="summary">
    <tr>
        @foreach($summary as $label => $count)
            <td><div class="num">{{ $count }}</div><div class="lbl">{{ $label }}</div></td>
        @endforeach
    </tr>
</table>

{{-- 2. Daftar SPK per kelompok --}}
<h2>2. Daftar SPK</h2>
@foreach($groups as $group)
    <h3 style="margin-top:8px">{{ $group['label'] }} ({{ $group['orders']->count() }} SPK)</h3>
    @if($group['orders']->isEmpty())
        <div class="muted small">Tidak ada SPK.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:18px">#</th>
                    <th>No. SPK / Invoice</th>
                    <th>Customer</th>
                    <th>Sepatu</th>
                    <th>Status</th>
                    <th>Teknisi</th>
                    <th class="center">Jasa</th>
                    <th class="right">Nilai Jasa</th>
                    <th class="center">Foto</th>
                    <th>Masuk</th>
                    <th>Estimasi</th>
                    <th>Selesai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($group['orders'] as $i => $o)
                    @php
                        $techs = collect($subStations)->map(fn ($s) => $o->{$s[1]}?->name)->filter()->unique()->implode(', ');
                    @endphp
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td><strong>{{ $o->spk_number }}</strong><br><span class="muted small">{{ $o->invoice?->invoice_number ?? '-' }}</span></td>
                        <td>{{ $o->customer_name }}</td>
                        <td>{{ $o->shoe_brand }} - {{ $o->shoe_type }}<br><span class="muted small">Sz {{ $o->shoe_size ?? '-' }} | {{ $o->shoe_color ?? '-' }}</span></td>
                        <td><span class="badge">{{ $statusLabel($o) }}</span></td>
                        <td class="small">{{ $techs ?: 'Belum ditugaskan' }}</td>
                        <td class="center">{{ $o->workOrderServices->count() }}</td>
                        <td class="right">{{ $rp($o->workOrderServices->sum('cost')) }}</td>
                        <td class="center">{{ $o->rndProgresses->whereNotNull('photo_path')->count() }}</td>
                        <td class="small">{{ $fmtDate($o->entry_date, 'd M Y') }}</td>
                        <td class="small">{{ $fmtDate($o->new_estimation_date ?? $o->estimation_date, 'd M Y') }}</td>
                        <td class="small">{{ $fmtDate($o->finished_date, 'd M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endforeach

{{-- 3. Rincian per SPK --}}
<div class="page-break"></div>
<h2>3. Rincian per SPK</h2>
@foreach($groups as $group)
    @foreach($group['orders'] as $o)
        @php
            $no++;
            $mulaiRiset = $o->logs->firstWhere('action', 'RND_RESEARCH_STARTED')?->created_at;
            $selesaiRiset = $o->logs->where('action', 'RND_RESEARCH_COMPLETED')->last()?->created_at;
            $alur = $o->logs->filter(fn ($l) => str_starts_with($l->action, 'RND_') || $l->action === 'STATUS_CHANGE');
        @endphp
        <div class="card">
            <table class="header">
                <tr>
                    <td><h3>{{ $no }}. {{ $o->spk_number }} <span class="badge">{{ $statusLabel($o) }}</span></h3></td>
                    <td class="right small muted">{{ $group['label'] }}</td>
                </tr>
            </table>

            <table class="kv">
                <tr>
                    <td class="k">Customer</td><td>{{ $o->customer_name }}</td>
                    <td class="k">Invoice</td><td>{{ $o->invoice?->invoice_number ?? '-' }} ({{ $o->invoice?->status ?? '-' }})</td>
                    <td class="k">Prioritas</td><td>{{ $o->priority }}</td>
                </tr>
                <tr>
                    <td class="k">Sepatu</td><td>{{ $o->shoe_brand }} - {{ $o->shoe_type }} · Sz {{ $o->shoe_size ?? '-' }} · {{ $o->shoe_color ?? '-' }}</td>
                    <td class="k">Masuk gudang</td><td>{{ $fmtDate($o->entry_date) }}</td>
                    <td class="k">Estimasi</td><td>{{ $fmtDate($o->new_estimation_date ?? $o->estimation_date, 'd M Y') }}</td>
                </tr>
                <tr>
                    <td class="k">Mulai riset</td><td>{{ $fmtDate($mulaiRiset) }}</td>
                    <td class="k">Riset selesai</td><td>{{ $fmtDate($selesaiRiset ?? $o->finished_date) }}</td>
                    <td class="k">Diambil</td><td>{{ $fmtDate($o->taken_date) }}</td>
                </tr>
                @if($o->technician_notes || $o->notes)
                    <tr><td class="k">Catatan</td><td colspan="5">{{ $o->technician_notes ?: $o->notes }}</td></tr>
                @endif
            </table>

            <div class="sub">Jasa &amp; Biaya</div>
            <table>
                <thead><tr><th>Jasa</th><th>Kategori</th><th>Catatan</th><th class="right" style="width:90px">Biaya</th></tr></thead>
                <tbody>
                    @forelse($o->workOrderServices as $s)
                        @php $detail = collect($s->service_details['manual_detail'] ?? [])->filter()->implode('; '); @endphp
                        <tr>
                            <td>{{ $s->custom_service_name ?? $s->service?->name ?? '-' }}</td>
                            <td>{{ $s->category_name ?? $s->service?->category ?? '-' }}</td>
                            <td class="small">{{ trim(($s->notes ?? '') . ($detail ? ' ' . $detail : '')) ?: '-' }}</td>
                            <td class="right">{{ $rp($s->cost) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">Belum ada jasa.</td></tr>
                    @endforelse
                    <tr><td colspan="3" class="right"><strong>Total jasa</strong></td><td class="right"><strong>{{ $rp($o->workOrderServices->sum('cost')) }}</strong></td></tr>
                </tbody>
            </table>

            <div class="sub">Teknisi per Sub-Stasiun</div>
            <table>
                <thead><tr><th>Sub-stasiun</th><th>Teknisi</th><th>Mulai</th><th>Selesai</th></tr></thead>
                <tbody>
                    @php $adaTeknisi = false; @endphp
                    @foreach($subStations as $prefix => [$label, $relation])
                        @php
                            $tech = $o->{$relation}?->name;
                            $start = $o->{$prefix . '_started_at'};
                            $end = $o->{$prefix . '_completed_at'};
                        @endphp
                        @if($tech || $start || $end)
                            @php $adaTeknisi = true; @endphp
                            <tr><td>{{ $label }}</td><td>{{ $tech ?? '-' }}</td><td>{{ $fmtDate($start) }}</td><td>{{ $fmtDate($end) }}</td></tr>
                        @endif
                    @endforeach
                    @unless($adaTeknisi)
                        <tr><td colspan="4" class="muted">Belum ada teknisi ditugaskan.</td></tr>
                    @endunless
                </tbody>
            </table>

            <div class="sub">Jurnal Riset ({{ $o->rndProgresses->count() }} catatan)</div>
            <table>
                <thead><tr><th style="width:80px">Tanggal</th><th>Tahap / Uji</th><th style="width:70px">Hasil</th><th>Catatan</th><th style="width:70px">Oleh</th><th style="width:116px">Foto</th></tr></thead>
                <tbody>
                    @forelse($o->rndProgresses as $p)
                        @php
                            [$hLabel, $hClass] = $hasil[$p->result_status] ?? [$p->result_status ?? '-', ''];
                        @endphp
                        <tr>
                            <td class="small">{{ $fmtDate($p->created_at) }}</td>
                            <td>{{ $p->stage_title ?? '-' }}</td>
                            <td><span class="badge {{ $hClass }}">{{ $hLabel }}</span></td>
                            <td class="small">{{ $p->notes ?? '-' }}</td>
                            <td class="small">{{ $p->user?->name ?? '-' }}</td>
                            <td>
                                @if($p->pdf_photo)
                                    <img class="photo" src="{{ $p->pdf_photo }}">
                                @else
                                    <span class="muted small">{{ $p->photo_path ? 'File foto tidak ditemukan' : 'Tanpa foto' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="muted">Belum ada jurnal riset.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="sub">Riwayat Alur</div>
            <table>
                <thead><tr><th style="width:80px">Waktu</th><th style="width:120px">Kejadian</th><th>Keterangan</th></tr></thead>
                <tbody>
                    @forelse($alur as $l)
                        <tr>
                            <td class="small">{{ $fmtDate($l->created_at) }}</td>
                            <td class="small">{{ $l->action === 'STATUS_CHANGE' ? 'Status → ' . $l->step : str_replace('_', ' ', $l->action) }}</td>
                            <td class="small">{{ $l->description }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="muted">Belum ada riwayat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endforeach
@endforeach

@if($no === 0)
    <div class="muted">Tidak ada SPK R&amp;D yang sesuai filter.</div>
@endif
</body>
</html>
