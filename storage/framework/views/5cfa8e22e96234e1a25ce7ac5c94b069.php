<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Finish Photo Report - <?php echo e($workOrder->spk_number); ?></title>
    <style>
        /* ============================================================
           PREMIUM FINISH REPORT PDF — Brand Palette
           Green:  #22AF85   Yellow: #FFC232
           White:  #FFFFFF   Dark:   #1E293B / #475569
           ============================================================ */
        @page {
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1E293B;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #FFFFFF;
        }

        /* ─── HEADER ─── */
        .header {
            background-color: #22AF85;
            color: #FFFFFF;
            padding: 0;
            position: relative;
        }
        .header-inner {
            padding: 32px 40px 28px 40px;
        }
        .header-brand {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
            opacity: 0.85;
            margin-bottom: 6px;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .header-subtitle {
            margin: 6px 0 0;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }
        /* Yellow accent bar under header */
        .header-accent {
            height: 5px;
            background-color: #FFC232;
        }

        /* ─── SPK BADGE (top-right) ─── */
        .spk-badge {
            position: absolute;
            top: 24px;
            right: 40px;
            background-color: #FFC232;
            color: #1E293B;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .spk-badge-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
            opacity: 0.7;
        }

        /* ─── CONTENT ─── */
        .content {
            padding: 28px 40px 20px 40px;
        }

        /* ─── ORDER INFO CARDS ─── */
        .info-row {
            width: 100%;
            margin-bottom: 24px;
            border-collapse: collapse;
        }
        .info-card {
            background-color: #F8FAF9;
            border: 1px solid #E2E8E6;
            border-radius: 8px;
            padding: 14px 16px;
            vertical-align: top;
        }
        .info-card-accent {
            border-top: 3px solid #22AF85;
        }
        .info-label {
            font-size: 9px;
            color: #22AF85;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1.2px;
            display: block;
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 14px;
            font-weight: 700;
            color: #1E293B;
            display: block;
        }
        .info-value-small {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            display: block;
        }

        /* ─── SECTION TITLE ─── */
        .section-header {
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #F0F0F0;
        }
        .section-title {
            font-size: 15px;
            font-weight: 800;
            color: #22AF85;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 0;
            display: inline;
        }
        .section-badge {
            background-color: #FFC232;
            color: #1E293B;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 10px;
            margin-left: 10px;
            letter-spacing: 0.5px;
        }

        /* ─── PHOTO GRID ─── */
        .photo-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
        }
        .photo-cell {
            width: 47%;
            vertical-align: top;
            padding: 0;
        }
        .photo-card {
            background-color: #FFFFFF;
            border: 1px solid #E2E8E6;
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
        }
        .photo-card-top {
            background-color: #F8FAF9;
            padding: 3px 0;
        }
        .photo-card-top span {
            font-size: 8px;
            font-weight: 700;
            color: #22AF85;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .photo-img {
            width: 100%;
            height: 240px;
            object-fit: contain;
            background-color: #F1F5F4;
            display: block;
        }
        .photo-caption-bar {
            background-color: #22AF85;
            padding: 8px 12px;
            text-align: left;
        }
        .photo-caption-text {
            font-size: 10px;
            color: #FFFFFF;
            font-weight: 600;
        }
        .photo-caption-date {
            font-size: 9px;
            color: #FFFFFF;
            opacity: 0.8;
        }

        /* ─── PHOTO NUMBER BADGE ─── */
        .photo-number {
            display: inline-block;
            background-color: #FFC232;
            color: #1E293B;
            font-size: 9px;
            font-weight: 800;
            width: 22px;
            height: 22px;
            line-height: 22px;
            text-align: center;
            border-radius: 50%;
            margin-right: 6px;
        }

        /* ─── FOOTER ─── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #1E293B;
            color: #94A3B8;
            padding: 12px 40px;
            font-size: 8px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .footer-brand {
            color: #22AF85;
            font-weight: 700;
        }
        .footer-divider {
            color: #475569;
            margin: 0 6px;
        }

        /* ─── UTILITIES ─── */
        .page-break {
            page-break-after: always;
        }
        .text-green { color: #22AF85; }
        .text-yellow { color: #FFC232; }
        .text-dark { color: #1E293B; }
        .text-muted { color: #64748B; }
        .fw-800 { font-weight: 800; }
    </style>
</head>
<body>

    
    <div class="header">
        <div class="header-inner">
            <div class="header-brand">Sistem Workshop</div>
            <h1>📋 Laporan Foto Hasil Akhir</h1>
            <div class="header-subtitle">Dokumentasi Resmi Pengerjaan Workshop</div>

            
            <div class="spk-badge">
                <span class="spk-badge-label">Nomor SPK</span>
                <?php echo e($workOrder->spk_number); ?>

            </div>
        </div>
        <div class="header-accent"></div>
    </div>

    
    <div class="content">

        
        <table class="info-row" cellspacing="0" cellpadding="0">
            <tr>
                <td width="32%" style="padding-right: 8px;">
                    <div class="info-card info-card-accent">
                        <span class="info-label">Customer</span>
                        <span class="info-value"><?php echo e($workOrder->customer_name); ?></span>
                    </div>
                </td>
                <td width="36%" style="padding: 0 4px;">
                    <div class="info-card info-card-accent">
                        <span class="info-label">Brand / Model</span>
                        <span class="info-value"><?php echo e($workOrder->shoe_brand ?: '-'); ?></span>
                    </div>
                </td>
                <td width="32%" style="padding-left: 8px;">
                    <div class="info-card info-card-accent">
                        <span class="info-label">Tanggal Selesai</span>
                        <span class="info-value"><?php echo e($workOrder->finished_date ? $workOrder->finished_date->format('d M Y') : now()->format('d M Y')); ?></span>
                    </div>
                </td>
            </tr>
        </table>

        
        <table class="info-row" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <div class="info-card">
                        <span class="info-label">Layanan yang Dikerjakan</span>
                        <span class="info-value-small"><?php echo e($workOrder->workOrderServices->map(fn($s) => $s->custom_service_name ?? ($s->service->name ?? '-'))->implode('  •  ')); ?></span>
                    </div>
                </td>
            </tr>
        </table>

        
        <div class="section-header">
            <span class="section-title">📸 Dokumentasi Hasil Akhir</span>
            <span class="section-badge"><?php echo e($photos->count()); ?> FOTO</span>
        </div>

        
        <table class="photo-grid" cellpadding="0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $photos->chunk(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pair): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pair; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <td class="photo-cell">
                            <div class="photo-card">
                                
                                <div class="photo-card-top">
                                    <span>FOTO #<?php echo e($loop->parent->index * 2 + $loop->iteration); ?></span>
                                </div>

                                
                                <img src="<?php echo e($photo->base64_image); ?>" class="photo-img">

                                
                                <div class="photo-caption-bar">
                                    <span class="photo-number"><?php echo e($loop->parent->index * 2 + $loop->iteration); ?></span>
                                    <span class="photo-caption-text">
                                        <?php echo e($photo->caption ?: 'Dokumentasi ' . ($photo->step == 'FINISH' ? 'Finishing' : ucfirst(strtolower(str_replace('_', ' ', $photo->step))))); ?>

                                    </span>
                                    <br>
                                    <span class="photo-caption-date" style="margin-left: 28px;">
                                        <?php echo e($photo->created_at->format('d/m/Y H:i')); ?>

                                    </span>
                                </div>
                            </div>
                        </td>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pair->count() < 2): ?>
                        <td class="photo-cell"></td>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </table>

    </div>

    
    <div class="footer">
        <span class="footer-brand">SISTEM WORKSHOP</span>
        <span class="footer-divider">|</span>
        Diterbitkan secara otomatis pada <?php echo e($generatedAt); ?>

        <span class="footer-divider">|</span>
        Dokumen ini merupakan bukti resmi hasil pengerjaan
    </div>

</body>
</html>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\reports\finish-report-pdf.blade.php ENDPATH**/ ?>