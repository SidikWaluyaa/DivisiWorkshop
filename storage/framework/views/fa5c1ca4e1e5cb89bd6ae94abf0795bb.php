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
        .badge-active { background: #c6f6d5; color: #22543d; }
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
                    <div style="font-size: 14px; font-weight: bold;">GUDANG UTAMA</div>
                    <div class="meta-info">Dicetak: <?php echo e(now()->format('d M Y H:i')); ?></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td width="33%">
                    <div class="label">Kategori Rak</div>
                    <div><?php echo e(strtoupper(is_object($rack->category) ? ($rack->category->label() ?? $rack->category->value) : $rack->category)); ?></div>
                </td>
                <td width="33%">
                    <div class="label">Kapasitas Terisi</div>
                    <div><?php echo e($rack->current_count); ?> / <?php echo e($rack->capacity); ?> Item (<?php echo e($rack->capacity > 0 ? round(($rack->current_count / $rack->capacity) * 100) : 0); ?>%)</div>
                </td>
                <td width="33%">
                    <div class="label">Status Rak</div>
                    <div style="color: <?php echo e((is_object($rack->status) ? $rack->status->value : $rack->status) == 'active' ? '#38a169' : '#e53e3e'); ?>">
                        <?php echo e(strtoupper(is_object($rack->status) ? ($rack->status->label() ?? $rack->status->value) : $rack->status)); ?>

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
                <th>Tipe & Customer</th>
                <th width="100">Layanan</th>
                <th width="80">Tgl Masuk</th>
                <th width="90">Petugas</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td style="font-weight: bold;"><?php echo e(optional($item->workOrder)->spk_number ?? '-'); ?></td>
                    <td>
                        <span style="font-weight: bold;"><?php echo e(ucfirst(str_replace('_', ' ', $item->item_type))); ?> <?php echo e(optional($item->workOrder)->shoe_brand ? '- ' . $item->workOrder->shoe_brand : ''); ?></span><br>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(optional($item->workOrder)->shoe_type): ?>
                            <span style="font-size: 10px; color: #718096;">Model: <?php echo e($item->workOrder->shoe_type); ?></span><br>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span style="color: #4a5568;"><?php echo e(optional($item->workOrder)->customer_name ?? '-'); ?></span>
                    </td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->workOrder && $item->workOrder->services && $item->workOrder->services->count() > 0): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $item->workOrder->services->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div style="font-size: 10px;">- <?php echo e($svc->pivot->custom_service_name ?? $svc->name); ?></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->workOrder->services->count() > 3): ?>
                                <div style="font-size: 9px; color: #718096;">+<?php echo e($item->workOrder->services->count() - 3); ?> lainnya</div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <span style="font-size: 10px; color: #a0aec0;">-</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td><?php echo e(optional($item->stored_at)->format('d/m/y H:i') ?? '-'); ?></td>
                    <td>
                        <?php echo e(optional($item->storedByUser)->name ?? '-'); ?>

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
                <div class="label">Petugas Gudang Utama</div>
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
        SISTEM WORKSHOP - MANIFEST GUDANG UTAMA - ID RAK: <?php echo e($rack->id); ?>

    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\pdf\warehouse_rack_manifest.blade.php ENDPATH**/ ?>