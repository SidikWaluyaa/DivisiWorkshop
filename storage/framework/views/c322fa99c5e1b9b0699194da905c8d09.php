<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #2563eb; margin-bottom: 20px; }
        .header h1 { color: #2563eb; margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #666; }
        .status-box { background-color: #dbeafe; color: #1e40af; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
        .details { margin-bottom: 20px; }
        .details table { wIdth: 100%; border-collapse: collapse; }
        .details th, .details td { text-align: left; padding: 8px; border-bottom: 1px solid #eee; }
        .details th { wIdth: 40%; color: #555; font-weight: bold; }
        .services { margin-top: 10px; background: #f9fafb; padding: 15px; border-radius: 8px; }
        .services ul { margin: 0; padding-left: 20px; }
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #2563eb; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="text-align: center; margin-bottom: 15px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($message) && is_object($message) && method_exists($message, 'embed')): ?>
                    <img src="<?php echo e($message->embed(public_path('images/logo-email.png'))); ?>" alt="Shoe Workshop Logo" style="max-height: 80px;">
                <?php else: ?>
                    <img src="<?php echo e(asset('images/logo-email.png')); ?>" alt="Shoe Workshop Logo" style="max-height: 80px;">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <h1>Shoe Workshop</h1>
            <p>Notifikasi Penyelesaian Order</p>
        </div>

        <div class="status-box">
            <h2 style="margin:0;">SEPATU SUDAH SELESAI ✨</h2>
            <p style="margin:5px 0 0;">Siap untuk diambil / dikirim kembali</p>
        </div>
        
        <p>Halo <strong><?php echo e($order->customer_name); ?></strong>,</p>
        <p>Kabar gembira! Sepatu kesayangan Anda telah selesai kami proses dengan penuh kasih sayang. Berikut detailnya:</p>
        
        <div class="details">
            <table>
                <tr>
                    <th>Nomor SPK</th>
                    <td style="font-weight: bold;"><?php echo e($order->spk_number); ?></td>
                </tr>
                <tr>
                    <th>Brand Sepatu</th>
                    <td><?php echo e($order->shoe_brand); ?></td>
                </tr>
                <tr>
                    <th>Warna / Size</th>
                    <td><?php echo e($order->shoe_color); ?> / <?php echo e($order->shoe_size); ?></td>
                </tr>
            </table>
        </div>

        <div class="services">
            <p style="font-weight: bold; margin-top: 0;">Layanan yang dikerjakan:</p>
            <ul>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <li><?php echo e($service->name); ?></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
        
        <p>Silakan datang ke workshop kami untuk pengambilan, atau hubungi kami jika ingin dikirim via ekspedisi / ojek online.</p>

        <div style="background-color: #f0fdf4; border: 1px dashed #16a34a; padding: 15px; border-radius: 8px; margin-top: 20px;">
            <p style="margin: 0; font-weight: bold; color: #166534;">Info Pengambilan / Hubungi Kami:</p>
            <p style="margin: 5px 0 0; font-size: 18px; color: #16a34a;">0812-3456-7890</p>
            <p style="margin: 5px 0 0; font-size: 12px; color: #666;">(Admin Workshop)</p>
        </div>

        <p style="margin-top: 30px; font-size: 14px;">
            Kami selalu berusaha memberikan hasil terbaik. Namun jika ada hasil yang kurang memuaskan, jangan ragu untuk memberitahu kami.
            <br>
            <a href="<?php echo e(route('complaints.index')); ?>" style="color: #ef4444; font-weight: bold; text-decoration: none;">Ajukan Komplain / Masukan di sini</a>
        </p>

        <div class="footer">
            <p>&copy; <?php echo e(date('Y')); ?> Shoe Workshop System. All rights reserved.</p>
            <p>Terima kasih telah mempercayakan sepatu Anda kepada kami!</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\emails\orders\finished.blade.php ENDPATH**/ ?>