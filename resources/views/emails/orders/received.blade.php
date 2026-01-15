<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #0f766e; margin-bottom: 20px; }
        .header h1 { color: #0f766e; margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #666; }
        .details { margin-bottom: 20px; }
        .details table { wIdth: 100%; border-collapse: collapse; }
        .details th, .details td { text-align: left; padding: 8px; border-bottom: 1px solid #eee; }
        .details th { wIdth: 40%; color: #555; font-weight: bold; }
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #0f766e; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="text-align: center; margin-bottom: 15px;">
                @if(isset($message) && is_object($message) && method_exists($message, 'embed'))
                    <img src="{{ $message->embed(public_path('images/logo-email.png')) }}" alt="Shoe Workshop Logo" style="max-height: 80px;">
                @else
                    <img src="{{ asset('images/logo-email.png') }}" alt="Shoe Workshop Logo" style="max-height: 80px;">
                @endif
            </div>
            <h1>Shoe Workshop</h1>
            <p>Nota Digital Penerimaan Sepatu</p>
        </div>
        
        <p>Halo <strong>{{ $order->customer_name }}</strong>,</p>
        <p>Terima kasih telah mempercayakan perawatan sepatu Anda kepada kami. Berikut adalah detail pesanan Anda yang telah kami terima:</p>
        
        <div class="details">
            <table>
                <tr>
                    <th>Nomor SPK</th>
                    <td style="font-weight: bold; color: #0f766e;">{{ $order->spk_number }}</td>
                </tr>
                <tr>
                    <th>Tanggal Masuk</th>
                    <td>{{ $order->entry_date->format('d M Y') }}</td>
                </tr>
                <tr>
                    <th>Estimasi Selesai</th>
                    <td>{{ $order->estimation_date->format('d M Y') }}</td>
                </tr>
                <tr>
                    <th>Brand Sepatu</th>
                    <td>{{ $order->shoe_brand }}</td>
                </tr>
                <tr>
                    <th>Warna / Size</th>
                    <td>{{ $order->shoe_color }} / {{ $order->shoe_size }}</td>
                </tr>
            </table>
        </div>

        <p>Tim kami akan segera melakukan pengecekan (Assessment) dan pengerjaan sesuai antrian. Anda akan mendapatkan notifikasi selanjutnya ketika sepatu sudah selesai dikerjakan.</p>
        
        <p>Anda dapat memantau progress pengerjaan sepatu Anda secara berkala melalui halaman Tracking kami:</p>
        
        <div style="text-align: center;">
            <a href="{{ route('tracking.index') }}" class="btn">Cek Progress Pesanan</a>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Shoe Workshop System. All rights reserved.</p>
            <p>Ini adalah pesan otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
