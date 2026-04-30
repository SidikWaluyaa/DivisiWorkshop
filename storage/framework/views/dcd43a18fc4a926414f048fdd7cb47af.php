
<section class="animate-fade-in-up delay-100">
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4">

        
        <div x-data="{ open: false }" class="stat-card group bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl p-5 shadow-lg border border-teal-400/30 !overflow-visible relative transition-all duration-300"
             :class="{ 'z-[50] scale-[1.02]': open, 'hover:z-[40]': !open }">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="relative">
                    
                    <button @click.stop="open = !open" class="p-1 text-white/70 hover:text-white transition-colors cursor-pointer outline-none focus:outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-[100] w-64 max-w-none p-4 bg-white rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 top-full mt-3 whitespace-normal text-left">
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative text-gray-800">
                            <div class="text-[10px] font-black text-teal-500 uppercase tracking-widest mb-1">Maksud</div>
                            <div class="text-[12px] text-gray-700 leading-relaxed font-medium">Total leads/customer baru yang masuk ke CS dalam periode ini.</div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2 mb-1">Sumber Data</div>
                            <div class="text-[11px] text-gray-500 italic">Tabel receptions (lead masuk).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="kpi-cs-leads" class="text-3xl font-black text-white mb-1"><?php echo e($kpi['cs']['leads'] ?? 0); ?></div>
            <div class="text-xs font-bold text-white/70 uppercase tracking-wider">Total Leads</div>
            <div class="mt-2 flex items-center gap-2">
                <span id="kpi-cs-delta" class="px-2 py-0.5 rounded-md text-[10px] font-bold <?php echo e(($kpi['cs']['leads_delta'] ?? 0) > 0 ? 'bg-white/20 text-white' : (($kpi['cs']['leads_delta'] ?? 0) < 0 ? 'bg-white/20 text-white' : 'bg-white/10 text-white/60')); ?>">
                    <?php echo e(($kpi['cs']['leads_delta'] ?? 0) > 0 ? '+' : ''); ?><?php echo e($kpi['cs']['leads_delta'] ?? 0); ?>%
                </span>
                <span class="text-[10px] text-white/50 font-medium whitespace-nowrap">vs periode lalu</span>
            </div>
        </div>

        
        <div x-data="{ open: false }" class="stat-card group bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-5 shadow-lg border border-emerald-400/30 !overflow-visible relative transition-all duration-300"
             :class="{ 'z-[50] scale-[1.02]': open, 'hover:z-[40]': !open }">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="relative">
                    <button @click.stop="open = !open" class="p-1 text-white/70 hover:text-white transition-colors cursor-pointer outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-[100] w-64 max-w-none p-4 bg-white rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 top-full mt-3 whitespace-normal text-left">
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative text-gray-800">
                            <div class="text-[10px] font-black text-teal-500 uppercase tracking-widest mb-1">Maksud</div>
                            <div class="text-[12px] text-gray-700 leading-relaxed font-medium">Persentase leads yang berhasil menjadi SPK (Closing).</div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2 mb-1">Rumus</div>
                            <div class="text-[11px] text-gray-500 italic">Closing / Total Leads × 100%.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="kpi-cs-conversion" class="text-3xl font-black text-white mb-1"><?php echo e($kpi['cs']['conversion'] ?? 0); ?>%</div>
            <div class="text-xs font-bold text-white/70 uppercase tracking-wider">Conversion Rate</div>
            <div class="mt-4 w-full bg-white/20 rounded-full h-1.5 overflow-hidden shadow-inner">
                <div id="kpi-cs-conversion-progress" class="bg-white h-1.5 rounded-full shadow-[0_0_10px_rgba(255,255,255,0.5)] transition-all duration-1000" style="width: <?php echo e($kpi['cs']['conversion'] ?? 0); ?>%"></div>
            </div>
            <div class="mt-2 text-[10px] text-white/60 font-medium">
                <span id="kpi-cs-closings"><?php echo e($kpi['cs']['closings'] ?? 0); ?></span> closing dari <?php echo e($kpi['cs']['leads'] ?? 0); ?> leads
            </div>
        </div>

        
        <div x-data="{ open: false }" class="stat-card group bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-5 shadow-lg border border-orange-400/30 !overflow-visible relative transition-all duration-300"
             :class="{ 'z-[50] scale-[1.02]': open, 'hover:z-[40]': !open }">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div class="relative">
                    <button @click.stop="open = !open" class="p-1 text-white/70 hover:text-white transition-colors cursor-pointer outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-[100] w-64 max-w-none p-4 bg-white rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 top-full mt-3 whitespace-normal text-left">
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative text-gray-800">
                            <div class="text-[10px] font-black text-teal-500 uppercase tracking-widest mb-1">Maksud</div>
                            <div class="text-[12px] text-gray-700 leading-relaxed font-medium">Jumlah SPK yang sedang dikerjakan atau dalam antrian produksi.</div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2 mb-1">Sumber Data</div>
                            <div class="text-[11px] text-gray-500 italic">Tabel spks (status != selesai/batal).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="kpi-ws-active" class="text-3xl font-black text-white mb-1"><?php echo e($kpi['workshop']['active'] ?? 0); ?></div>
            <div class="text-xs font-bold text-white/70 uppercase tracking-wider">SPK Aktif</div>
            <div class="mt-4 flex items-center gap-2">
                <div class="flex-1 bg-white/20 h-1.5 rounded-full overflow-hidden shadow-inner">
                    <div id="kpi-workshop-progress" class="bg-white h-1.5 rounded-full shadow-[0_0_10px_rgba(255,255,255,0.5)] transition-all duration-1000" style="width: 65%"></div>
                </div>
                <div id="kpi-ws-overdue" class="text-[10px] font-black text-white px-1.5 py-0.5 bg-white/20 rounded">
                    <span class="count-text"><?php echo e($kpi['workshop']['overdue'] ?? 0); ?></span>
                    <span class="opacity-50 mx-0.5">/</span>
                    <span id="kpi-ws-normal"><?php echo e($kpi['workshop']['active'] ?? 0); ?></span>
                </div>
            </div>
        </div>

        
        <div x-data="{ open: false }" class="stat-card group bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl p-5 shadow-lg border border-amber-300/30 !overflow-visible relative transition-all duration-300"
             :class="{ 'z-[50] scale-[1.02]': open, 'hover:z-[40]': !open }">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div class="relative">
                    <button @click.stop="open = !open" class="p-1 text-white/70 hover:text-white transition-colors cursor-pointer outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-[100] w-64 max-w-none p-4 bg-white rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 top-full mt-3 whitespace-normal text-left">
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative text-gray-800">
                            <div class="text-[10px] font-black text-teal-500 uppercase tracking-widest mb-1">Maksud</div>
                            <div class="text-[12px] text-gray-700 leading-relaxed font-medium">Total SPK yang telah selesai diproduksi dan siap diserahkan/dikirim.</div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2 mb-1">Sumber Data</div>
                            <div class="text-[11px] text-gray-500 italic">Tabel spks (status = selesai).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="kpi-ws-completed" class="text-3xl font-black text-white mb-1">✓ <?php echo e($kpi['workshop']['completed'] ?? 0); ?></div>
            <div class="text-xs font-bold text-white/70 uppercase tracking-wider">SPK Selesai</div>
            <div class="mt-2 flex items-center justify-between">
                <div class="text-[10px] text-white/60 font-medium">Growth <span id="kpi-ws-delta" class="font-bold">(<?php echo e(($kpi['workshop']['completed_delta'] ?? 0) > 0 ? '+' : ''); ?><?php echo e($kpi['workshop']['completed_delta'] ?? 0); ?>%)</span></div>
                <div class="flex -space-x-2">
                    <div class="w-5 h-5 rounded-full border-2 border-amber-400 bg-white/20"></div>
                    <div class="w-5 h-5 rounded-full border-2 border-amber-400 bg-white/40"></div>
                </div>
            </div>
        </div>

        
        <div x-data="{ open: false }" class="stat-card group bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl p-5 shadow-lg border border-indigo-400/30 !overflow-visible relative transition-all duration-300"
             :class="{ 'z-[50] scale-[1.02]': open, 'hover:z-[40]': !open }">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div class="relative">
                    <button @click.stop="open = !open" class="p-1 text-white/70 hover:text-white transition-colors cursor-pointer outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-[100] w-64 max-w-none p-4 bg-white rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 top-full mt-3 whitespace-normal text-left">
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative text-gray-800">
                            <div class="text-[10px] font-black text-teal-500 uppercase tracking-widest mb-1">Maksud</div>
                            <div class="text-[12px] text-gray-700 leading-relaxed font-medium">Estimasi total nilai stok bahan baku dan aksesoris yang ada di gudang.</div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2 mb-1">Sumber Data</div>
                            <div class="text-[11px] text-gray-500 italic">Tabel materials (qty * price).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="kpi-gd-value" class="text-3xl font-black text-white mb-1">Rp <?php echo e(number_format(($kpi['gudang']['inventory_value'] ?? 0) / 1000000, 1, ',', '.')); ?>jt</div>
            <div class="text-xs font-bold text-white/70 uppercase tracking-wider">Nilai Inventori</div>
            <div class="mt-4 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                    <span class="text-[10px] font-black text-white">📍</span>
                </div>
                <div id="kpi-gd-stored" class="text-[11px] font-bold text-white/80">
                    📍 <?php echo e($kpi['gudang']['stored_items'] ?? 0); ?> Item Tersimpan
                </div>
            </div>
        </div>

        
        <div x-data="{ open: false }" class="stat-card group bg-white rounded-2xl p-5 shadow-lg border border-gray-100 hover:border-red-200 !overflow-visible relative transition-all duration-300"
             :class="{ 'z-[50] scale-[1.02]': open, 'hover:z-[40]': !open }">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <div class="relative">
                    <button @click.stop="open = !open" class="p-1 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-[100] w-64 max-w-none p-4 bg-white rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 top-full mt-3 whitespace-normal text-left">
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative text-gray-800">
                            <div class="text-[10px] font-black text-teal-500 uppercase tracking-widest mb-1">Maksud</div>
                            <div class="text-[12px] text-gray-700 leading-relaxed font-medium">Bahan baku yang stoknya sudah di bawah minimal threshold.</div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2 mb-1">Sumber Data</div>
                            <div class="text-[11px] text-gray-500 italic">Tabel materials (stock <= min_stock).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="kpi-gd-lowstock-val" class="text-3xl font-black text-gray-800 mb-1 <?php echo e(($kpi['gudang']['low_stock'] ?? 0) > 0 ? 'text-red-600' : ''); ?>"><?php echo e($kpi['gudang']['low_stock'] ?? 0); ?></div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Stok Kritis</div>
            <div class="mt-4 flex items-center gap-2">
                <div id="kpi-gd-lowstock" class="px-2 py-0.5 rounded-md bg-red-100 text-red-600 text-[10px] font-bold <?php echo e(($kpi['gudang']['low_stock'] ?? 0) > 0 ? 'animate-pulse' : 'hidden'); ?>">
                    <span class="count-text">🔻 <?php echo e($kpi['gudang']['low_stock'] ?? 0); ?></span> Needs Restock
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($kpi['gudang']['low_stock'] ?? 0) <= 0): ?>
                    <span class="px-2 py-0.5 rounded-md bg-green-100 text-green-600 text-[10px] font-bold">Stock Safe</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div x-data="{ open: false }" class="stat-card group bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-2xl p-5 shadow-lg border border-purple-400/30 !overflow-visible relative transition-all duration-300"
             :class="{ 'z-[50] scale-[1.02]': open, 'hover:z-[40]': !open }">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="relative">
                    <button @click.stop="open = !open" class="p-1 text-white/70 hover:text-white transition-colors cursor-pointer outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-[100] w-64 max-w-none p-4 bg-white rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 top-full mt-3 whitespace-normal text-left">
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative text-gray-800">
                            <div class="text-[10px] font-black text-teal-500 uppercase tracking-widest mb-1">Maksud</div>
                            <div class="text-[12px] text-gray-700 leading-relaxed font-medium">Laju penyelesaian keluhan pelanggan dalam 30 hari terakhir.</div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2 mb-1">Sumber Data</div>
                            <div class="text-[11px] text-gray-500 italic">Tabel cx_tickets (resolved / total).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="kpi-cx-rate" class="text-3xl font-black text-white mb-1"><?php echo e($kpi['cx']['resolution_rate'] ?? 0); ?>%</div>
            <div class="text-xs font-bold text-white/70 uppercase tracking-wider">Resolution Rate</div>
            <div class="mt-4 w-full bg-white/20 rounded-full h-1.5 overflow-hidden shadow-inner">
                <div id="kpi-cx-resolution-progress" class="bg-white h-1.5 rounded-full shadow-[0_0_10px_rgba(255,255,255,0.5)] transition-all duration-1000" style="width: <?php echo e($kpi['cx']['resolution_rate'] ?? 0); ?>%"></div>
            </div>
            <div id="kpi-cx-delta" class="hidden mt-2"></div>
        </div>

        
        <div x-data="{ open: false }" class="stat-card group bg-white rounded-2xl p-5 shadow-lg border border-red-100 hover:border-red-200 !overflow-visible relative transition-all duration-300 <?php echo e(($kpi['cx']['open_issues'] ?? 0) > 0 ? 'urgent-glow' : ''); ?>"
             :class="{ 'z-[50] scale-[1.02]': open, 'hover:z-[40]': !open }">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm">
                    <svg class="w-6 h-6 text-red-500 <?php echo e(($kpi['cx']['open_issues'] ?? 0) > 0 ? 'animate-pulse' : ''); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="relative">
                    <button @click.stop="open = !open" class="p-1 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                    
                    <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-[100] w-64 max-w-none p-4 bg-white rounded-2xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 top-full mt-3 whitespace-normal text-left">
                        <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                        <div class="relative text-gray-800">
                            <div class="text-[10px] font-black text-teal-500 uppercase tracking-widest mb-1">Maksud</div>
                            <div class="text-[12px] text-gray-700 leading-relaxed font-medium">Jumlah tiket/keluhan pelanggan yang masih dalam status Open/Pending.</div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2 mb-1">Sumber Data</div>
                            <div class="text-[11px] text-gray-500 italic">Tabel cx_tickets (status = open).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="kpi-cx-open" class="text-3xl font-black text-gray-800 mb-1 <?php echo e(($kpi['cx']['open_issues'] ?? 0) > 0 ? 'text-red-600' : ''); ?>"><?php echo e($kpi['cx']['open_issues'] ?? 0); ?></div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">CX Open Issues</div>
            <div class="mt-4 flex items-center gap-2">
                <span id="kpi-cx-avgtime" class="text-[10px] text-gray-400 font-medium italic">Avg Response: <?php echo e($kpi['cx']['avg_response'] ?? 0); ?>h</span>
            </div>
        </div>

    </div>
</section>

<style>
.urgent-glow {
    box-shadow: 0 0 15px rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.3) !important;
}
</style>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\dashboard-v2\sections\kpi-cards.blade.php ENDPATH**/ ?>