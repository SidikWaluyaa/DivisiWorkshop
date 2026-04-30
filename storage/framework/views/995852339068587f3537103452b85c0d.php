<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pembelian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .stats {
            width: 100%;
            margin-bottom: 20px;
        }
        .stats td {
            background-color: #f9f9f9;
            text-align: center;
            border: 1px solid #ddd;
            padding: 10px;
            width: 33%;
        }
        .stats h3 { margin: 0; font-size: 14px; color: #555; }
        .stats p { margin: 5px 0 0; font-size: 16px; font-weight: bold; }
        .status-received { color: green; }
        .status-pending { color: orange; }
        .status-ordered { color: blue; }
        .status-cancelled { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?php echo e(public_path('images/logo.png')); ?>" style="max-width: 150px; margin-bottom: 10px;">
        <h2>Laporan Pembelian</h2>
        <p>Periode: <?php echo e($rangeLabel); ?></p>
        <span style="font-size: 10px; color: #777;">Dicetak: <?php echo e(date('d M Y H:i:s')); ?></span>
    </div>

    <table class="stats">
        <tr>
            <td>
                <h3>Total Belanja</h3>
                <p>Rp <?php echo e(number_format($analytics['total_spend'], 0, ',', '.')); ?></p>
            </td>
            <td>
                <h3>Total Transaksi</h3>
                <p><?php echo e($analytics['total_transactions']); ?></p>
            </td>
            <td>
                <h3>Top Supplier</h3>
                <p><?php echo e($analytics['top_supplier']); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($analytics['avg_rating'] > 0): ?>
                    <small style="color: goldenrod;">★ <?php echo e(number_format($analytics['avg_rating'], 1)); ?></small>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No. PO</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Material</th>
                <th>Jml</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Rating</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <tr>
                <td><?php echo e($purchase->po_number); ?></td>
                <td><?php echo e(\Carbon\Carbon::parse($purchase->created_at)->format('d M Y')); ?></td>
                <td><?php echo e($purchase->supplier_name ?? '-'); ?></td>
                <td>
                    <?php echo e($purchase->material->name); ?><br>
                    <small>@ Rp <?php echo e(number_format($purchase->unit_price, 0, ',', '.')); ?></small>
                </td>
                <td><?php echo e($purchase->quantity); ?> <?php echo e($purchase->material->unit); ?></td>
                <td>Rp <?php echo e(number_format($purchase->total_price, 0, ',', '.')); ?></td>
                <td>
                    <span class="status-<?php echo e($purchase->status); ?>"><?php echo e(ucfirst($purchase->status)); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($purchase->payment_status !== 'paid'): ?>
                        <br><small style="color:red">(Belum Lunas)</small>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>
                <td>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($purchase->quality_rating): ?>
                        <?php echo e($purchase->quality_rating); ?>/5
                    <?php else: ?>
                        -
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </td>
            </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\admin\purchases\pdf.blade.php ENDPATH**/ ?>