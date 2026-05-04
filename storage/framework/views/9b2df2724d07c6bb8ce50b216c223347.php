<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran - <?php echo e(date('d/m/Y')); ?></title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1B8A68;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #1B8A68;
            font-size: 20pt;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 10pt;
            letter-spacing: 2px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 2px 0;
        }
        .info-label {
            width: 120px;
            font-weight: bold;
            color: #666;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        table.data-table th {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            text-align: left;
            font-size: 9pt;
            text-transform: uppercase;
            color: #64748b;
        }
        table.data-table td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            font-size: 9pt;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .status-badge {
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-verified { background-color: #ecfdf5; color: #059669; }
        .status-pending { background-color: #fffbeb; color: #d97706; }
        
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 9pt;
        }
        .signature-box {
            margin-top: 60px;
            width: 200px;
            float: right;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
        .total-row {
            background-color: #f1f5f9;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pembayaran Invoice</h1>
        <p>SISTEM WORKSHOP MANAGEMENT</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Periode</td>
            <td>: <?php echo e($startDate ? date('d/m/Y', strtotime($startDate)) : 'Semua'); ?> s/d <?php echo e($endDate ? date('d/m/Y', strtotime($endDate)) : 'Sekarang'); ?></td>
            <td class="info-label" style="text-align: right;">Dicetak Pada</td>
            <td style="width: 150px;">: <?php echo e(date('d/m/Y H:i')); ?></td>
        </tr>
        <tr>
            <td class="info-label">Status</td>
            <td>: <?php echo e($status ? ucfirst($status) : 'Semua Status'); ?></td>
            <td class="info-label" style="text-align: right;">Dicetak Oleh</td>
            <td>: <?php echo e(auth()->user()->name); ?></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 25%">No. Invoice</th>
                <th style="width: 35%">Pelanggan</th>
                <th style="width: 15%" class="text-center">Tanggal</th>
                <th style="width: 20%" class="text-right">Jumlah Bayar</th>
            </tr>
        </thead>
        <tbody>
            <?php $totalAmount = 0; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <?php $totalAmount += $payment->amount; ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td class="font-bold"><?php echo e($payment->invoice->invoice_number ?? '-'); ?></td>
                    <td style="word-wrap: break-word;"><?php echo e($payment->invoice->customer->name ?? '-'); ?></td>
                    <td class="text-center"><?php echo e($payment->payment_date->format('d/m/Y')); ?></td>
                    <td class="text-right font-bold">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px;">Tidak ada data pembayaran ditemukan.</td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp <?php echo e(number_format($totalAmount, 0, ',', '.')); ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-box">
        <p>Bandung, <?php echo e(date('d F Y')); ?></p>
        <p>Finance Department,</p>
        <div class="signature-line">
            ( <?php echo e(auth()->user()->name); ?> )
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/finance/payments/print.blade.php ENDPATH**/ ?>