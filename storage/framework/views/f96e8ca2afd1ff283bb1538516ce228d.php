<?php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storage Label - <?php echo e($assignment->workOrder->spk_number); ?></title>
    <style>
        @page {
            size: 10cm 15cm;
            margin: 0;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            width: 10cm;
            height: 15cm;
        }
        
        .label {
            width: 100%;
            height: 100%;
            padding: 1cm;
            box-sizing: border-box;
            border: 2px solid #000;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .header p {
            margin: 4px 0 0 0;
            font-size: 10px;
        }
        
        .qr-section {
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
        
        .qr-code {
            flex-shrink: 0;
        }
        
        .qr-info {
            flex: 1;
        }
        
        .qr-info .spk {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 4px 0;
        }
        
        .qr-info .rack {
            font-size: 28px;
            font-weight: bold;
            color: #0d9488;
            margin: 0;
            padding: 4px 8px;
            background: #f0fdfa;
            border: 2px solid #0d9488;
            display: inline-block;
        }
        
        .customer-section {
            border-bottom: 2px solid #ddd;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
        
        .customer-section h3 {
            margin: 0 0 6px 0;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        
        .customer-section .name {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 4px 0;
        }
        
        .customer-section .phone {
            font-size: 14px;
            margin: 0 0 8px 0;
        }
        
        .address {
            font-size: 13px;
            line-height: 1.4;
            margin: 0;
        }
        
        .footer-section {
            margin-top: auto;
            padding-top: 8px;
            border-top: 2px solid #ddd;
            font-size: 11px;
        }
        
        .footer-section .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        
        .footer-section .label-text {
            color: #666;
        }
        
        .footer-section .value {
            font-weight: bold;
        }
        
        .notes {
            margin-top: 8px;
            padding: 6px;
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            font-size: 10px;
            font-style: italic;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="label">
        
        <div class="header">
            <h1>SHOE WORKSHOP</h1>
            <p>STORAGE LABEL</p>
        </div>
        
        
        <div class="qr-section">
            <div class="qr-code">
                <?php echo QrCode::size(100)->generate(json_encode([
                    'spk' => $assignment->workOrder->spk_number,
                    'rack' => $assignment->rack_code,
                    'customer' => $assignment->workOrder->customer->name,
                    'phone' => $assignment->workOrder->customer->phone,
                    'stored_at' => $assignment->stored_at->format('Y-m-d H:i:s'),
                ])); ?>

            </div>
            <div class="qr-info">
                <p class="spk">SPK: <?php echo e($assignment->workOrder->spk_number); ?></p>
                <p class="rack"><?php echo e($assignment->rack_code); ?></p>
            </div>
        </div>
        
        
        <div class="customer-section">
            <h3>Customer</h3>
            <p class="name"><?php echo e($assignment->workOrder->customer->name); ?></p>
            <p class="phone">📞 <?php echo e($assignment->workOrder->customer->phone); ?></p>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($assignment->workOrder->customer->address): ?>
                <h3 style="margin-top: 8px;">Alamat Pengiriman</h3>
                <p class="address"><?php echo e($assignment->workOrder->customer->address); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        
        
        <div class="footer-section">
            <div class="row">
                <span class="label-text">Selesai:</span>
                <span class="value"><?php echo e($assignment->workOrder->qc_final_completed_at?->format('d M Y') ?? $assignment->stored_at->format('d M Y')); ?></span>
            </div>
            <div class="row">
                <span class="label-text">Service:</span>
                <span class="value"><?php echo e($assignment->workOrder->service_type ?? 'General Service'); ?></span>
            </div>
            <div class="row">
                <span class="label-text">Disimpan:</span>
                <span class="value"><?php echo e($assignment->stored_at->format('d M Y H:i')); ?></span>
            </div>
            <div class="row">
                <span class="label-text">Lokasi:</span>
                <span class="value"><?php echo e($assignment->rack->location); ?></span>
            </div>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($assignment->notes): ?>
                <div class="notes">
                    <strong>Notes:</strong> <?php echo e($assignment->notes); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\storage\label.blade.php ENDPATH**/ ?>