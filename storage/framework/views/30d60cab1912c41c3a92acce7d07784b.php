
<div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100">
    <div class="p-8 pb-4 bg-gray-50/50">
        <h3 class="font-black text-gray-900 uppercase tracking-tighter text-2xl">Quick Actions</h3>
        <div class="w-16 h-2 bg-[#FFC232] rounded-full mt-2.5"></div>
    </div>
    <div class="p-8 space-y-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->status === 'GREETING'): ?>
            <button onclick="moveToKonsultasi()" class="w-full bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-yellow-100 transition transform hover:-translate-y-1">
                → Pindah ke Konsultasi
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->status === 'KONSULTASI'): ?>
            <button onclick="openQuotationModal()" class="w-full bg-[#22AF85] hover:bg-[#22AF85]/90 text-white py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-green-100 transition transform hover:-translate-y-1">
                ➕ Buat Quotation
            </button>
            <button onclick="moveToFollowUp()" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-orange-100 transition transform hover:-translate-y-1">
                🔥 Pindah ke Follow-up
            </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->canMoveToClosing()): ?>
                <button onclick="moveToClosing()" class="w-full bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-yellow-100 transition transform hover:-translate-y-1">
                    → Pindah ke Closing
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->status === 'FOLLOW_UP'): ?>
            <button onclick="openQuotationModal()" class="w-full bg-[#22AF85] hover:bg-[#22AF85]/90 text-white py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-green-100 transition transform hover:-translate-y-1">
                ➕ Buat Quotation
            </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->canMoveToClosing()): ?>
                <button onclick="moveToClosing()" class="w-full bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-yellow-100 transition transform hover:-translate-y-1">
                    → Pindah ke Closing
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button onclick="backToKonsultasi()" class="w-full border-2 border-yellow-100 text-yellow-600 hover:bg-yellow-50 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition">
                ↩️ Kembali ke Konsultasi
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->status === 'CLOSING'): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$lead->spk): ?>
                <button onclick="openSpkModal()" class="w-full bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-yellow-100 transition transform hover:-translate-y-1">
                    📄 Generate SPK
                </button>
            <?php elseif($lead->spk->canBeHandedToWorkshop()): ?>
                <button onclick="openHandoverModal()" class="w-full bg-[#22AF85] hover:bg-[#22AF85]/90 text-white py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-green-100 transition transform hover:-translate-y-1">
                    ✅ Serahkan ke Workshop
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="grid grid-cols-2 gap-3">
            <button onclick="openActivityModal()" class="bg-gray-50 border-2 border-gray-100 hover:bg-[#22AF85]/5 hover:border-[#22AF85]/20 text-gray-900 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition">
                📝 Log Aktivitas
            </button>

            <button onclick="openFollowUpModal()" class="bg-gray-50 border-2 border-gray-100 hover:bg-[#FFC232]/5 hover:border-[#FFC232]/20 text-gray-900 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition">
                ⏰ Set Follow Up
            </button>
        </div>

        <button onclick="markLost()" class="w-full border-2 border-red-50 text-red-400 hover:bg-red-50 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition">
            ❌ Mark as LOST
        </button>
    </div>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\cs\leads\partials\quick-actions.blade.php ENDPATH**/ ?>