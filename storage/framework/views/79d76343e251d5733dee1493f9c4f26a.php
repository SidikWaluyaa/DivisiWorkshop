<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($priority === 'Express'): ?>
    <span class="px-2 py-0.5 inline-flex text-[9px] leading-none font-black rounded bg-purple-100 text-purple-700 border border-purple-200 uppercase tracking-tighter">
        EXPRESS
    </span>
<?php elseif($priority === 'Urgent'): ?>
    <span class="px-2 py-0.5 inline-flex text-[9px] leading-none font-black rounded bg-red-100 text-red-700 border border-red-200 uppercase tracking-tighter">
        URGENT
    </span>
<?php elseif($priority === 'Prioritas'): ?>
    <span class="px-2 py-0.5 inline-flex text-[9px] leading-none font-black rounded bg-orange-100 text-orange-700 border border-orange-200 uppercase tracking-tighter">
        PRIORITAS
    </span>
<?php else: ?>
    <span class="px-2 py-0.5 inline-flex text-[9px] leading-none font-black rounded bg-gray-100 text-gray-500 border border-gray-200 uppercase tracking-tighter">
        REGULER
    </span>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\sortir\partials\priority-badge.blade.php ENDPATH**/ ?>