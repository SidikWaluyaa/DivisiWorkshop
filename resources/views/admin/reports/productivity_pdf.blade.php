<!DOCTYPE html>
<html>
<head>
    <title>Laporan Produktivitas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .meta { margin-top: 5px; color: #666; }
        
        table { w-full; border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { bg-color: #f2f2f2; font-weight: bold; }
        .text-left { text-align: left; }
        
        .high-perf { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <header>
        <h1>Laporan Produktivitas Karyawan</h1>
        <div class="meta">Periode: {{ $rangeLabel }}</div>
    </header>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle;">No</th>
                <th rowspan="2" style="vertical-align: middle;" class="text-left">Nama Karyawan</th>
                <th rowspan="2" style="vertical-align: middle;" class="text-left">Role</th>
                <th colspan="3">Jumlah Pekerjaan Selesai</th>
                <th rowspan="2" style="vertical-align: middle;">Total Task</th>
            </tr>
            <tr>
                <th style="font-size: 10px;">Sortir/Prep</th>
                <th style="font-size: 10px;">Produksi<br>(Cuci/Sol/Jahit)</th>
                <th style="font-size: 10px;">Quality Control</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            @php
                $total = $user->sortir_sol_count + $user->production_count + $user->qc_count;
            @endphp
            @if($total > 0)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">{{ $user->name }}</td>
                <td class="text-left">{{ ucfirst($user->role) }}</td>
                <td>{{ $user->sortir_sol_count }}</td>
                <td>{{ $user->production_count }}</td>
                <td>{{ $user->qc_count }}</td>
                <td class="{{ $total >= 10 ? 'high-perf' : '' }}">{{ $total }}</td>
            </tr>
            @endif
            @endforeach
            
            @if($users->sum(fn($u) => $u->sortir_sol_count + $u->production_count + $u->qc_count) == 0)
            <tr>
                <td colspan="7">Tidak ada aktivitas pada periode ini.</td>
            </tr>
            @endif
        </tbody>
    </table>
    
    <div style="text-align: right; margin-top: 50px; font-size: 11px; color: #888;">
        Dicetak otomatis oleh Sistem Workshop pada {{ date('d M Y H:i') }}
    </div>
</body>
</html>
