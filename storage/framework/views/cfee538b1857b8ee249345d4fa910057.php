<section class="section-gradient rounded-3xl p-8 animate-fade-in-up">
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-[#22AF85] flex items-center justify-center shadow-lg shadow-[#22AF85]/30 section-icon-glow">
            <span class="text-2xl">📦</span>
        </div>
        <div class="flex-1">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Supply Chain & Inventory</h2>
            <p class="text-sm text-gray-500 font-medium">Monitoring inventori, material, dan supplier analytics</p>
        </div>
        <div class="hidden md:block flex-grow h-px section-divider"></div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
         
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-2xl">💎</div>
            <div>
                <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest">Nilai Inventori</div>
                <div class="text-xl font-black text-gray-800">Rp <?php echo e(number_format($inventoryValue['total'] / 1000000, 1, ',', '.')); ?>jt</div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-2xl">📝</div>
            <div>
                <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest">Pending PO</div>
                <div class="text-xl font-black text-gray-800"><?php echo e($purchaseStats['pending_po']); ?> Order</div>
            </div>
        </div>

         
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-2xl">💸</div>
            <div>
                <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest">Belanja Periode</div>
                <div class="text-xl font-black text-gray-800">Rp <?php echo e(number_format($purchaseStats['monthly_spend'] / 1000000, 1, ',', '.')); ?>jt</div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow <?php echo e(count($materialAlerts) > 0 ? 'ring-2 ring-red-100' : ''); ?>">
            <div class="w-12 h-12 rounded-xl <?php echo e(count($materialAlerts) > 0 ? 'bg-red-50 text-red-500 animate-pulse' : 'bg-green-50 text-green-500'); ?> flex items-center justify-center text-2xl">⚠️</div>
            <div>
                <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest">Stok Alert</div>
                <div class="text-xl font-black <?php echo e(count($materialAlerts) > 0 ? 'text-red-600' : 'text-green-600'); ?>"><?php echo e(count($materialAlerts)); ?> Item</div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="dashboard-card shadow-xl shadow-gray-200/50">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">📉 Penggunaan Material</h3>
            </div>
            <div class="dashboard-card-body">
                 <div class="chart-container" style="height: 250px;">
                    <canvas id="materialTrendsChart"></canvas>
                </div>
            </div>
        </div>

        
        <div class="dashboard-card shadow-xl shadow-gray-200/50">
             <div x-data="{ supplierTab: 'spend' }">
                <div class="dashboard-card-header flex justify-between items-center">
                    <h3 class="dashboard-card-title">🤝 Supplier Analytics</h3>
                    <div class="flex bg-gray-100 p-0.5 rounded-lg">
                        <button @click="supplierTab = 'spend'" :class="supplierTab === 'spend' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-400'" class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-md transition-all">Spend</button>
                        <button @click="supplierTab = 'rating'" :class="supplierTab === 'rating' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-400'" class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-md transition-all">Rating</button>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div x-show="supplierTab === 'spend'" class="chart-container" style="height: 250px;">
                        <canvas id="supplierSpendChart"></canvas>
                    </div>
                    <div x-show="supplierTab === 'rating'" class="chart-container" style="height: 250px;">
                        <canvas id="supplierRatingChart"></canvas>
                    </div>
                </div>
             </div>
        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\dashboard\partials\inventory.blade.php ENDPATH**/ ?>