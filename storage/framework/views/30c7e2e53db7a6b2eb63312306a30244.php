<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['id', 'labels', 'data', 'label' => 'Data', 'color' => '#0d9488', 'height' => 300]));

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

foreach (array_filter((['id', 'labels', 'data', 'label' => 'Data', 'color' => '#0d9488', 'height' => 300]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
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
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels, 15, 512) ?>,
                datasets: [{
                    label: '<?php echo e($label); ?>',
                    data: <?php echo json_encode($data, 15, 512) ?>,
                    backgroundColor: '<?php echo e($color); ?>',
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bars
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\components\bar-chart.blade.php ENDPATH**/ ?>