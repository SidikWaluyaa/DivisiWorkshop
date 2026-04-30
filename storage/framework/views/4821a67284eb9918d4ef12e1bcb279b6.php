<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['order']));

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

foreach (array_filter((['order']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<!-- Hidden Forms -->
<form id="process-<?php echo e($order->id); ?>" action="<?php echo e(route('reception.process', $order->id)); ?>" method="POST" class="hidden">
    <?php echo csrf_field(); ?>
</form>



<!-- Photo Modal -->
<div x-data="{ open: false }" @open-photo-modal-<?php echo e($order->id); ?>.window="open = true">
    <template x-teleport="body">
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-show="open" style="display: none;">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg" @click.away="open = false">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Dokumentasi Foto - <?php echo e($order->spk_number); ?></h3>
                                    <div class="mt-2 text-left">
                                        <p class="text-sm text-gray-500 mb-4">Upload foto kondisi awal sepatu sebelum diproses.</p>
                                        
                                        <?php if (isset($component)) { $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.photo-uploader','data' => ['order' => $order,'step' => 'RECEIVING']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('photo-uploader'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'step' => 'RECEIVING']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $attributes = $__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__attributesOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6)): ?>
<?php $component = $__componentOriginal0b55eebd37945af6c95e63b8e56be4d6; ?>
<?php unset($__componentOriginal0b55eebd37945af6c95e63b8e56be4d6); ?>
<?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="open = false">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views/reception/partials/order-actions.blade.php ENDPATH**/ ?>