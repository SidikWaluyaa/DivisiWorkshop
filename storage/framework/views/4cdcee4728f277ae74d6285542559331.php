<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label', 'count', 'max' => 50, 'href' => null]));

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

foreach (array_filter((['label', 'count', 'max' => 50, 'href' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $percentage = $max > 0 ? min(($count / $max) * 100, 100) : 0;
    $color = $percentage > 80 ? 'bg-red-500' : ($percentage > 50 ? 'bg-yellow-500' : 'bg-teal-500');
?>

<div class="mb-4 <?php echo e($href ? 'cursor-pointer hover:bg-gray-50' : ''); ?> p-3 rounded-lg transition-colors"
     <?php if($href): ?> onclick="window.location='<?php echo e($href); ?>'" <?php endif; ?>>
    <div class="flex justify-between items-center mb-2">
        <span class="font-bold text-gray-800"><?php echo e($label); ?></span>
        <span class="text-sm font-semibold text-gray-600"><?php echo e($count); ?> orders</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
        <div class="<?php echo e($color); ?> h-3 rounded-full transition-all duration-500 ease-out shadow-sm" 
             style="width: <?php echo e($percentage); ?>%"></div>
    </div>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\components\workload-bar.blade.php ENDPATH**/ ?>