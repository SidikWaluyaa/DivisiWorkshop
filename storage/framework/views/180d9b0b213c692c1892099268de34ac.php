<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['id', 'labels', 'data', 'colors', 'height' => 120]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['id', 'labels', 'data', 'colors', 'height' => 120]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<canvas id="<?php echo e($id); ?>" height="<?php echo e($height); ?>"></canvas>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('<?php echo e($id); ?>').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($labels, 15, 512) ?>,
                datasets: [{
                    data: <?php echo json_encode($data, 15, 512) ?>,
                    backgroundColor: <?php echo json_encode($colors, 15, 512) ?>,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                layout: {
                    padding: 20
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 8,
                            usePointStyle: true,
                            padding: 12,
                            font: {
                                size: 10,
                                family: "'Inter', sans-serif"
                            }
                        }
                    }
                }
            }
        });
    });
</script>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\components\donut-chart.blade.php ENDPATH**/ ?>