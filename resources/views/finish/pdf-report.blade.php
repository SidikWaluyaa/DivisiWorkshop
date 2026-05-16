<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 0.8cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1f2937;
            line-height: 1.2;
            font-size: 8pt;
        }
        .header {
            border-bottom: 1.5px solid #22B086;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header table {
            width: 100%;
        }
        .brand-name {
            color: #22B086;
            font-size: 16pt;
            font-weight: 900;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .report-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 2px;
            color: #374151;
        }
        .meta-info {
            font-size: 7.5pt;
            color: #6b7280;
            text-align: right;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .summary-box table {
            width: 100%;
        }
        .summary-label {
            font-size: 7pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-value {
            font-size: 10pt;
            font-weight: 900;
            color: #22B086;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table.data-table th {
            background-color: #22B086;
            color: white;
            text-align: left;
            padding: 6px 8px;
            font-size: 7.5pt;
            text-transform: uppercase;
            border: 0.5px solid #1c8d6c;
        }
        table.data-table td {
            padding: 5px 8px;
            border: 0.5px solid #e2e8f0;
            font-size: 8pt;
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .priority-badge {
            color: #b91c1c;
            font-size: 7pt;
            font-weight: 900;
        }
        .payment-lunas {
            color: #059669;
            font-weight: 900;
            font-size: 7pt;
        }
        .payment-pending {
            color: #dc2626;
            font-weight: 900;
            font-size: 7pt;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
            border-top: 0.5px solid #e2e8f0;
            padding-top: 5px;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="brand-name">SIDEKICK WORKSHOP</h1>
                    <div class="report-title">{{ $title }}</div>
                </td>
                <td class="meta-info">
                    Dicetak pada: {{ $date }}<br>
                    Oleh: {{ auth()->user()->name }}
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td width="25%">
                    <div class="summary-label">Total Item</div>
                    <div class="summary-value">{{ count($orders) }} Pair</div>
                </td>
                <td width="35%">
                    <div class="summary-label">Kategori Laporan</div>
                    <div class="summary-value">
                        @if($type === 'stored') SIAP DIAMBIL (DI RAK)
                        @elseif($type === 'not_stored') MENUNGGU DISIMPAN (NON-RAK)
                        @else SEMUA BARANG SELESAI
                        @endif
                    </div>
                </td>
                <td width="20%">
                    <div class="summary-label">Status Barang</div>
                    <div class="summary-value">SELESAI QC</div>
                </td>
                <td width="20%">
                    <div class="summary-label">Tipe</div>
                    <div class="summary-value">INVENTORI</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">NO</th>
                <th width="14%">SPK</th>
                <th width="18%">CUSTOMER</th>
                <th width="20%">ITEM & WARNA</th>
                <th width="18%">LAYANAN</th>
                <th width="12%">PAYMENT</th>
                @if($type !== 'not_stored')
                    <th width="15%">LOKASI RAK</th>
                @else
                    <th width="15%">TGL SELESAI</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $index => $order)
                @php
                    $isLunas = ($order->invoice && $order->invoice->status === 'Lunas') || (!$order->invoice && in_array($order->status_pembayaran, ['L', 'Lunas']));
                @endphp
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $order->spk_number }}</strong>
                        @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                            <br><span class="priority-badge">● PRIORITAS</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: bold">{{ $order->customer_name }}</div>
                        <div style="font-size: 7pt; color: #666">{{ $order->customer_phone }}</div>
                    </td>
                    <td>
                        {{ $order->shoe_brand }}<br>
                        <small style="color: #666">{{ $order->shoe_color }}</small>
                    </td>
                    <td>
                        @foreach($order->workOrderServices as $wos)
                            <div style="font-size: 7.5pt">• {{ $wos->custom_service_name ?? $wos->service->name }}</div>
                        @endforeach
                    </td>
                    <td align="center">
                        @if($isLunas)
                            <span class="payment-lunas">LUNAS</span>
                        @else
                            <span class="payment-pending">BELUM LUNAS</span>
                        @endif
                    </td>
                    @if($type !== 'not_stored')
                        <td align="center">
                            <strong style="font-size: 10pt; color: #111">{{ $order->storage_rack_code ?? '-' }}</strong>
                        </td>
                    @else
                        <td align="center">
                            {{ $order->finished_date ? $order->finished_date->format('d/m/Y') : '-' }}
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Halaman <span class="page-number"></span> | Laporan Sistem Workshop Otomatis
    </div>
</body>
</html>
