<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rack Manifest - <?php echo e($rack->rack_code); ?></title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; line-height: 1.4; color: #333; }
        .header { border-bottom: 2px solid #4a5568; padding-bottom: 10px; margin-bottom: 20px; }
        .header table { width: 100%; }
        .rack-code { font-size: 28px; font-weight: bold; color: #2d3748; margin: 0; }
        .meta-info { color: #718096; font-size: 11px; }
        
        .summary-box { background: #f7fafc; border: 1px solid #e2e8f0; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .summary-box table { width: 100%; }
        .label { font-weight: bold; color: #4a5568; }
        
        table.manifest-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.manifest-table th { background: #4a5568; color: white; text-align: left; padding: 8px; font-size: 11px; text-transform: uppercase; }
        table.manifest-table td { border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: top; }
        
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 10px; color: #a0aec0; border-top: 1px solid #e2e8f0; padding-top: 5px; }
        .signatures { margin-top: 50px; width: 100%; }
        .signature-box { width: 45%; text-align: center; }
        .signature-line { border-bottom: 1px solid #333; height: 60px; margin-bottom: 5px; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .badge-manual { background: #edf2f7; color: #4a5568; }
        .badge-lunas { background: #c6f6d5; color: #22543d; }
        .badge-tl { background: #fff5f5; color: #822727; }
        .badge-tn { background: #ebf8ff; color: #2a4365; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="rack-code"><?php echo e($rack->rack_code); ?></h1>
                    <div class="meta-info">RACK MANIFEST | LOKASI: <?php echo e(strtoupper($rack->location)); ?></div>
                </td>
                <td style="text-align: right;">
                    <div style="font-size: 14px; font-weight: bold;">GUDANG MANUAL</div>
                    <div class="meta-info">DIcetak: <?php echo e(now()->format('d M Y H:i')); ?></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td width="33%">
                    <div class="label">Kategori Rak</div>
                    <div><?php echo e(strtoupper($rack->category->label() ?? $rack->category)); ?></div>
                </td>
                <td width="33%">
                    <div class="label">Kapasitas Terisi</div>
                    <div><?php echo e($rack->current_count); ?> / <?php echo e($rack->capacity); ?> Item (<?php echo e(round(($rack->current_count / $rack->capacity) * 100)); ?>%)</div>
                </td>
                <td width="33%">
                    <div class="label">Status Rak</div>
                    <div style="color: <?php echo e($rack->status->value == 'active' ? '#38a169' : '#e53e3e'); ?>">
                        <?php echo e(strtoupper($rack->status->label() ?? $rack->status->value)); ?>

                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="manifest-table">
        <thead>
            <tr>
                <th width="30">#</th>
                <th width="100">No. SPK</th>
                <th>Nama Barang / Pelanggan</th>
                <th width="50">Qty</th>
                <th width="80">Tgl Masuk</th>
                <th width="100">Status Bayar</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td style="font-weight: bold;"><?php echo e($item->spk_number); ?></td>
                    <td>
                        <?php echo e($item->item_name); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->description): ?>
                            <div style="font-size: 9px; color: #718096; margin-top: 4px;">Ket: <?php echo e($item->description); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td><?php echo e($item->quantity); ?></td>
                    <td><?php echo e($item->in_date->format('d/m/y')); ?></td>
                    <td>
                        <span class="badge badge-<?php echo e($item->payment_status); ?>">
                            <?php echo e(strtoupper($item->payment_status)); ?>

                        </span>
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #a0aec0; padding: 30px;">
                        Tidak ada barang yang tersimpan di rak ini.
                    </td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

    <table class="signatures">
        <tr>
            <td class="signature-box">
                <div class="label">Petugas Gudang (Checker)</div>
                <div class="signature-line"></div>
                <div>Nama: ___________________</div>
            </td>
            <td width="10%"></td>
            <td class="signature-box">
                <div class="label">Mengetahui (Manager)</div>
                <div class="signature-line"></div>
                <div>Nama: ___________________</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        SISTEM WORKSHOP - DOKUMEN INTERNAL GUDANG MANUAL - ID RAK: <?php echo e($rack->id); ?>

    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\pdf\rack_manifest.blade.php ENDPATH**/ ?>