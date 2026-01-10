<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok Material</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { padding: 2px 5px; border-radius: 4px; font-size: 10px; color: white; }
        .badge-ready { background-color: #10b981; }
        .badge-low { background-color: #ef4444; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Stok Material Workshop</h1>
        <p>Tanggal: {{ now()->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Material</th>
                <th>Type / Kategori</th>
                <th>Stok</th>
                <th>Satuan</th>
                <th>Harga Beli</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materials as $index => $material)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $material->name }}</td>
                <td>
                    {{ $material->type }}
                    @if($material->sub_category)
                        <br><small class="text-gray-500">{{ $material->sub_category }}</small>
                    @endif
                </td>
                <td class="text-center">{{ $material->stock }}</td>
                <td class="text-center">{{ $material->unit }}</td>
                <td class="text-right">Rp {{ number_format($material->price, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($material->stock <= $material->min_stock)
                        <span class="badge badge-low">Low Stock</span>
                    @else
                        <span class="badge badge-ready">Safe</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
