<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation - {{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #333; line-height: 1.5; margin: 0; padding: 0; }
        .container { padding: 40px; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #22AF85; padding-bottom: 20px; }
        .header table { width: 100%; }
        .logo { font-size: 24px; font-weight: bold; color: #22AF85; }
        .quotation-title { font-size: 28px; font-weight: bold; color: #333; text-align: right; }
        
        .info-section { margin-bottom: 30px; width: 100%; }
        .info-section td { vertical-align: top; width: 50%; }
        .section-title { font-size: 12px; font-weight: bold; color: #888; text-transform: uppercase; margin-bottom: 5px; }
        .info-box { font-size: 14px; }
        .info-box strong { color: #000; }
        
        table.items { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table.items th { background-color: #f9f9f9; border-bottom: 2px solid #eee; padding: 12px 10px; text-align: left; font-weight: bold; color: #555; }
        table.items td { padding: 12px 10px; border-bottom: 1px solid #eee; }
        
        .totals { margin-top: 30px; width: 100%; }
        .totals table { float: right; width: 300px; }
        .totals td { padding: 5px 10px; }
        .totals .label { text-align: right; color: #888; }
        .totals .value { text-align: right; font-weight: bold; font-size: 16px; }
        .totals .grand-total { background-color: #22AF85; color: white; padding: 10px; }
        .totals .grand-total .label { color: white; font-weight: normal; }
        
        .footer { margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px; color: #888; font-size: 11px; }
        .notes { margin-top: 30px; border: 1px font-style: italic; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td class="logo">SHOE WORKSHOP</td>
                    <td class="quotation-title">QUOTATION</td>
                </tr>
            </table>
        </div>

        <table class="info-section">
            <tr>
                <td>
                    <div class="section-title">Kepada:</div>
                    <div class="info-box">
                        <strong>{{ $quotation->lead->customer_name }}</strong><br>
                        {{ $quotation->lead->customer_phone }}<br>
                        {{ $quotation->lead->customer_address ?? '' }}
                    </div>
                </td>
                <td style="text-align: right;">
                    <div class="section-title">Rincian:</div>
                    <div class="info-box">
                        Nomor: <strong>{{ $quotation->quotation_number }}</strong><br>
                        Tanggal: <strong>{{ $quotation->created_at->format('d/m/Y') }}</strong><br>
                        Berlaku: <strong>{{ $quotation->valid_until ? $quotation->valid_until->format('d/m/Y') : '-' }}</strong>
                    </div>
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Deskripsi Layanan</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Harga</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item['service_name'] }}</strong>
                        @if(!empty($item['description']))
                            <br><small style="color: #888;">{{ $item['description'] }}</small>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item['qty'] }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item['qty'] * $item['price'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">Rp {{ number_format($quotation->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($quotation->discount > 0)
                <tr>
                    <td class="label">Potongan Harga</td>
                    <td class="value" style="color: #ef4444;">-Rp {{ number_format($quotation->discount_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td class="label">Total Keseluruhan</td>
                    <td class="value">Rp {{ number_format($quotation->total, 0, ',', '.') }}</td>
                </tr>
            </table>
            <div style="clear: both;"></div>
        </div>

        @if($quotation->notes)
        <div class="notes">
            <strong>Catatan:</strong><br>
            {{ $quotation->notes }}
        </div>
        @endif

        <div class="footer">
            <table style="width: 100%;">
                <tr>
                    <td>
                        Metode Pembayaran:<br>
                        <strong>BCA: 1234567890 a/n Shoe Workshop</strong>
                    </td>
                    <td style="text-align: right;">
                        Terima kasih atas kepercayaan Anda!<br>
                        #LivingWithPassion
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
