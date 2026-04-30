<!DOCTYPE html>
<html>
<head>
    <title>Laporan Produktivitas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .meta { margin-top: 5px; color: #666; }
        
        table { w-full; border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { bg-color: #f2f2f2; font-weight: bold; }
        .text-left { text-align: left; }
        
        .high-perf { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <header>
        <h1>Laporan Produktivitas Karyawan</h1>
        <div class="meta">Periode: <?php echo e($rangeLabel); ?></div>
    </header>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle;">No</th>
                <th rowspan="2" style="vertical-align: middle;" class="text-left">Nama Karyawan</th>
                <th rowspan="2" style="vertical-align: middle;" class="text-left">Role</th>
                <th colspan="3">Jumlah Pekerjaan Selesai</th>
                <th rowspan="2" style="vertical-align: middle;">Total Task</th>
            </tr>
            <tr>
                <th style="font-size: 10px;">Sortir/Prep</th>
                <th style="font-size: 10px;">Produksi<br>(Cuci/Sol/Jahit)</th>
                <th style="font-size: 10px;">Quality Control</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <?php
                $total = $user->sortir_sol_count + $user->production_count + $user->qc_count;
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($total > 0): ?>
            <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td class="text-left"><?php echo e($user->name); ?></td>
                <td class="text-left"><?php echo e(ucfirst($user->role)); ?></td>
                <td><?php echo e($user->sortir_sol_count); ?></td>
                <td><?php echo e($user->production_count); ?></td>
                <td><?php echo e($user->qc_count); ?></td>
                <td class="<?php echo e($total >= 10 ? 'high-perf' : ''); ?>"><?php echo e($total); ?></td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->sum(fn($u) => $u->sortir_sol_count + $u->production_count + $u->qc_count) == 0): ?>
            <tr>
                <td colspan="7">Tidak ada aktivitas pada periode ini.</td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
    
    <div style="text-align: right; margin-top: 50px; font-size: 11px; color: #888;">
        Dicetak otomatis oleh Sistem Workshop pada <?php echo e(date('d M Y H:i')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\admin\reports\productivity_pdf.blade.php ENDPATH**/ ?>