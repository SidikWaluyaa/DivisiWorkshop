<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'value', 'icon', 'color' => 'teal', 'href' => null]));

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

foreach (array_filter((['title', 'value', 'icon', 'color' => 'teal', 'href' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $colorClasses = match($color) {
        'teal' => 'from-teal-500 to-teal-600',
        'orange' => 'from-orange-500 to-orange-600',
        'blue' => 'from-blue-500 to-blue-600',
        'green' => 'from-green-500 to-green-600',
        'red' => 'from-red-500 to-red-600',
        'purple' => 'from-purple-500 to-purple-600',
        default => 'from-gray-500 to-gray-600',
    };
?>

<div class="bg-gradient-to-br <?php echo e($colorClasses); ?> rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 <?php echo e($href ? 'cursor-pointer hover:scale-105' : ''); ?>"
     <?php if($href): ?> onclick="window.location='<?php echo e($href); ?>'" <?php endif; ?>>
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <div class="text-4xl font-black mb-2"><?php echo e($value); ?></div>
            <div class="text-sm font-semibold opacity-90 uppercase tracking-wide"><?php echo e($title); ?></div>
        </div>
        <div class="text-5xl opacity-20">
            <?php echo $icon; ?>

        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\components\kpi-card.blade.php ENDPATH**/ ?>