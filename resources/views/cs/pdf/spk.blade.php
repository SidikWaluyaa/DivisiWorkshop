<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SPK - {{ $spk->spk_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #333; line-height: 1.4; margin: 0; padding: 0; }
        .container { padding: 30px; }
        .header { background-color: #22AF85; color: white; padding: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header .spk-no { text-align: right; font-weight: bold; font-size: 18px; }
        
        .section { margin-bottom: 20px; }
        .grid { width: 100%; }
        .grid td { vertical-align: top; }
        
        .box { border: 1px solid #ddd; padding: 15px; border-radius: 5px; background-color: #fcfcfc; }
        .box-title { font-size: 11px; font-weight: bold; color: #888; text-transform: uppercase; margin-bottom: 5px; }
        
        table.services { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.services th { background-color: #333; color: white; padding: 8px; text-align: left; font-size: 11px; }
        table.services td { padding: 8px; border-bottom: 1px solid #eee; }
        
        .payment-info { margin-top: 20px; background-color: #fffbeb; border: 1px solid #fde68a; padding: 15px; border-radius: 5px; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background-color: #dcfce7; color: #166534; }
        .status-pending { background-color: #fef9c3; color: #854d0e; }
        
        .footer { margin-top: 40px; text-align: center; color: #888; font-size: 11px; border-top: 1px solid #eee; padding-top: 20px; }
        .signature-grid { width: 100%; margin-top: 30px; }
        .signature-box { height: 80px; border-bottom: 1px solid #333; width: 150px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1>SHOE WORKSHOP</h1>
                    <div style="font-size: 12px; opacity: 0.9;">Surat Perintah Kerja (Customer Copy)</div>
                </td>
                <td class="spk-no">
                    #{{ $spk->spk_number }}
                </td>
            </tr>
        </table>
    </div>

    <div class="container">
        <table class="grid">
            <tr>
                <td style="width: 50%; padding-right: 10px;">
                    <div class="box">
                        <div class="box-title">Data Customer</div>
                        <div style="font-size: 16px; font-weight: bold;">{{ $spk->lead->customer_name }}</div>
                        <div>{{ $spk->lead->customer_phone }}</div>
                        <div>{{ $spk->lead->customer_address ?? '-' }}</div>
                    </div>
                </td>
                <td style="width: 50%; padding-left: 10px;">
                    <div class="box">
                        <div class="box-title">Detail Order</div>
                        <table style="width: 100%;">
                            <tr>
                                <td style="color: #888;">Sepatu:</td>
                                <td style="text-align: right; font-weight: bold;">{{ $spk->shoe_brand }} (Size: {{ $spk->shoe_size }})</td>
                            </tr>
                            <tr>
                                <td style="color: #888;">Warna:</td>
                                <td style="text-align: right;">{{ $spk->shoe_color }}</td>
                            </tr>
                            <tr>
                                <td style="color: #888;">Kategori:</td>
                                <td style="text-align: right;">{{ $spk->category ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="color: #888;">Prioritas:</td>
                                <td style="text-align: right; font-weight: bold; color: {{ $spk->priority === 'Reguler' ? '#22AF85' : '#ef4444' }};">{{ $spk->priority }}</td>
                            </tr>
                            <tr>
                                <td style="color: #888;">Est. Selesai:</td>
                                <td style="text-align: right; font-weight: bold;">{{ $spk->expected_delivery_date ? $spk->expected_delivery_date->format('d M Y') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        @if($spk->special_instructions)
        <div class="section" style="margin-top: 20px;">
            <div class="box-title">Instruksi Khusus (CS)</div>
            <div class="box" style="font-style: italic; background-color: #fffbeb; border-color: #fde68a;">
                {{ $spk->special_instructions }}
            </div>
        </div>
        @endif

        <div class="section" style="margin-top: 20px;">
            <div class="box-title">Daftar Item & Layanan</div>
            @foreach($spk->items as $item)
                <div style="margin-bottom: 15px; border: 1px solid #eee; border-radius: 5px; overflow: hidden;">
                    <div style="background-color: #f8f9fa; padding: 10px; border-bottom: 1px solid #eee;">
                        <span style="font-weight: bold; color: #333;">Item #{{ $item->item_number }}: {{ $item->label }}</span>
                        <span style="float: right; font-size: 11px; color: #666;">Kategori: {{ $item->category }}</span>
                    </div>
                    <table class="services">
                        <thead>
                            <tr>
                                <th style="width: 70%;">Deskripsi Layanan</th>
                                <th style="text-align: right;">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->services as $service)
                            <tr>
                                <td>
                                    <div style="font-weight: bold;">{{ $service['name'] ?? 'Layanan' }}</div>
                                    @if(!empty($service['manual_detail']))
                                        <div style="font-size: 10px; color: #666; font-style: italic; margin-top: 2px;">
                                            Catatan: {{ $service['manual_detail'] }}
                                        </div>
                                    @endif
                                    @if(!empty($service['is_custom']))
                                        <span style="font-size: 9px; background-color: #22AF85; color: white; padding: 1px 4px; border-radius: 3px; font-weight: bold; text-transform: uppercase; margin-top: 3px; display: inline-block;">Custom</span>
                                    @endif
                                </td>
                                <td style="text-align: right; vertical-align: middle;">
                                    Rp {{ number_format($service['price'] ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="text-align: right; font-weight: bold; padding: 8px; background-color: #fcfcfc;">Subtotal Item:</td>
                                <td style="text-align: right; font-weight: bold; padding: 8px; background-color: #fcfcfc;">Rp {{ number_format($item->item_total_price ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endforeach
        </div>

        <div class="payment-info">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <div class="box-title" style="color: #92400e;">Status Pembayaran</div>
                        @if($spk->dp_status === 'PAID')
                            <span class="status-badge status-paid">DP LUNAS ({{ $spk->payment_method }})</span>
                        @else
                            <span class="status-badge status-pending">MENUNGGU PEMBAYARAN DP</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <table style="float: right;">
                            <tr>
                                <td style="text-align: right; color: #888;">Total Biaya:</td>
                                <td style="text-align: right; font-weight: bold; width: 120px;">Rp {{ number_format($spk->total_price, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; color: #888;">Uang Muka (DP):</td>
                                <td style="text-align: right; font-weight: bold;">Rp {{ number_format($spk->dp_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr style="font-size: 16px;">
                                <td style="text-align: right; font-weight: bold;">Sisa:</td>
                                <td style="text-align: right; font-weight: bold; color: #22AF85;">Rp {{ number_format($spk->total_price - $spk->dp_amount, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        @if($spk->lead->notes)
        <div class="section" style="margin-top: 20px;">
            <div class="box-title">Catatan Tambahan</div>
            <div class="box" style="font-style: italic;">
                {{ $spk->lead->notes }}
            </div>
        </div>
        @endif

        <table class="signature-grid">
            <tr>
                <td style="text-align: center;">
                    <div class="box-title">Customer</div>
                    <div class="signature-box"></div>
                    <div style="margin-top: 5px;">{{ $spk->lead->customer_name }}</div>
                </td>
                <td style="text-align: center;">
                    <div class="box-title">Customer Service</div>
                    <div class="signature-box"></div>
                    <div style="margin-top: 5px;">{{ Auth::user()->name }}</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Harap simpan SPK ini sebagai bukti pengambilan sepatu.<br>
            <strong>Shoe Workshop - Modern Professional Shoe Care</strong><br>
            #MoreThanRepair
        </div>
    </div>
</body>
</html>
