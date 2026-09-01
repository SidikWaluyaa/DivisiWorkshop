{{-- MOBILE ACTION SHEET (SLIDE-UP BOTTOM SHEET FOR SUBMENUS) --}}
<div class="md:hidden">
    {{-- Backdrop Overlay --}}
    <div x-show="activeDrawer !== null" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="activeDrawer = null"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50"></div>

    {{-- Bottom Sheet Container --}}
    <div x-show="activeDrawer !== null" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t-4 border-[#FFC232] rounded-t-3xl p-6 text-slate-800 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto">
        
        {{-- Handle Bar --}}
        <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto -mt-2 mb-4"></div>

        {{-- 1. INBOUND SHEET --}}
        <div x-show="activeDrawer === 'inbound'" class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-black text-[#0F172A] uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#FFC232]"></span>
                    <span>Antrean Inbound Workshop</span>
                </h3>
                <button @click="activeDrawer = null" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900">✕</button>
            </div>
            
            <div class="grid grid-cols-1 gap-2 pt-1">
                <a href="{{ route('manifest.index', ['status' => 'SENT', 'mode' => 'pwa']) }}" 
                   class="p-4 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 flex items-center justify-between active:scale-95 transition-all">
                    <div>
                        <div class="font-black text-sm text-[#22AF85] flex items-center gap-2">
                            <span>📄 Manifest Inbound Masuk</span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-500 mt-0.5">Daftar Surat Jalan dari Toko/Gudang ke Workshop</p>
                    </div>
                    @php $pendingInboundCount = \App\Models\WorkshopManifest::where('status', 'SENT')->where('manifest_number', 'not like', 'MNF-OUT-%')->count(); @endphp
                    @if($pendingInboundCount > 0)
                        <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-[#FFC232] text-slate-950 shadow-sm animate-pulse">
                            {{ $pendingInboundCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        {{-- 2. PROSES PENGERJAAN SHEET --}}
        <div x-show="activeDrawer === 'proses'" class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-black text-[#0F172A] uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#22AF85]"></span>
                    <span>Tahap Proses Pengerjaan</span>
                </h3>
                <button @click="activeDrawer = null" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900">✕</button>
            </div>
            
            <div class="grid grid-cols-2 gap-2.5 pt-1">
                {{-- 1. Prep --}}
                <a href="{{ route('preparation.index') }}" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between hover:border-[#22AF85] active:scale-95 transition-all">
                    <div class="text-[10px] font-black text-[#22AF85] uppercase">Tahap 1</div>
                    <div class="font-black text-xs text-[#0F172A] mt-1">1. Persiapan (Cuci)</div>
                    <div class="text-[9px] font-bold text-slate-400 mt-0.5">Washing & Prep</div>
                </a>

                {{-- 2. Sortir --}}
                <a href="{{ route('sortir.index') }}" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between hover:border-[#22AF85] active:scale-95 transition-all">
                    <div class="text-[10px] font-black text-[#22AF85] uppercase">Tahap 2</div>
                    <div class="font-black text-xs text-[#0F172A] mt-1">2. Sortir & Klasifikasi</div>
                    <div class="text-[9px] font-bold text-slate-400 mt-0.5">Bongkar & Material</div>
                </a>

                {{-- 3. Produksi --}}
                <a href="{{ route('production.index') }}" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between hover:border-[#22AF85] active:scale-95 transition-all">
                    <div class="text-[10px] font-black text-[#22AF85] uppercase">Tahap 3</div>
                    <div class="font-black text-xs text-[#0F172A] mt-1">3. Produksi (Reparasi)</div>
                    <div class="text-[9px] font-bold text-slate-400 mt-0.5">Soling, Upper, Treatment</div>
                </a>

                {{-- 4. QC --}}
                <a href="{{ route('qc.index') }}" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between hover:border-[#22AF85] active:scale-95 transition-all">
                    <div class="text-[10px] font-black text-[#22AF85] uppercase">Tahap 4</div>
                    <div class="font-black text-xs text-[#0F172A] mt-1">4. Quality Control</div>
                    <div class="text-[9px] font-bold text-slate-400 mt-0.5">Inspeksi Lolos/Revisi</div>
                </a>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('internal-tracking.index') }}" class="p-3 rounded-xl bg-teal-50 text-teal-800 text-xs font-black border border-teal-200 flex items-center justify-between col-span-2">
                    <span>🔍 Internal Tracking SPK Kilat</span>
                </a>
                <a href="{{ route('production.late-info') }}" class="p-3 rounded-xl bg-slate-50 text-[#0F172A] text-xs font-bold border border-slate-200 flex items-center justify-between">
                    <span>⚠️ Info Keterlambatan</span>
                </a>
                <a href="{{ route('surat-jalan.index') }}" class="p-3 rounded-xl bg-slate-50 text-[#0F172A] text-xs font-bold border border-slate-200 flex items-center justify-between">
                    <span>📄 Surat Jalan WS</span>
                </a>
            </div>
        </div>

        {{-- 3. MATERIAL SHEET --}}
        <div x-show="activeDrawer === 'material'" class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-black text-[#0F172A] uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#22AF85]"></span>
                    <span>Manajemen Material Workshop</span>
                </h3>
                <button @click="activeDrawer = null" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900">✕</button>
            </div>
            
            <div class="grid grid-cols-1 gap-2 pt-1">
                @if(Auth::user()->hasAccess('admin.materials'))
                <a href="{{ route('admin.materials.index') }}" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between active:scale-95 transition-all">
                    <div>
                        <div class="font-black text-xs text-[#22AF85]">Stok & Katalog Material</div>
                        <p class="text-[10px] font-bold text-slate-500 mt-0.5">Kelola stok lem, sol, cat, & benang</p>
                    </div>
                </a>
                @endif

                @if(Route::has('material-requests.index'))
                <a href="{{ route('material-requests.index') }}" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between active:scale-95 transition-all">
                    <div>
                        <div class="font-black text-xs text-[#0F172A]">Belanja Material WS</div>
                        <p class="text-[10px] font-bold text-slate-500 mt-0.5">Pengadaan & Pembelian bahan baku baru</p>
                    </div>
                </a>
                @endif

                <a href="{{ route('storage.disbursement.index') }}" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between active:scale-95 transition-all">
                    <div>
                        <div class="font-black text-xs text-[#0F172A]">Barang Keluar WS</div>
                        <p class="text-[10px] font-bold text-slate-500 mt-0.5">Pencatatan pengeluaran bahan ke produksi</p>
                    </div>
                </a>

                <a href="{{ route('storage.history') }}" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between active:scale-95 transition-all">
                    <div>
                        <div class="font-black text-xs text-[#22AF85]">Riwayat Mutasi Stok</div>
                        <p class="text-[10px] font-bold text-slate-500 mt-0.5">Jurnal mutasi masuk-keluar stok material</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- 4. OUTBOUND SHEET --}}
        <div x-show="activeDrawer === 'outbound'" class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-black text-[#0F172A] uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#22AF85]"></span>
                    <span>Antrean Outbound Workshop</span>
                </h3>
                <button @click="activeDrawer = null" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900">✕</button>
            </div>
            
            <div class="grid grid-cols-1 gap-2 pt-1">
                <a href="{{ route('qc.outbound') }}" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between active:scale-95 transition-all">
                    <div>
                        <div class="font-black text-sm text-[#22AF85]">Staging & Manifest Outbound</div>
                        <p class="text-[10px] font-bold text-slate-500 mt-0.5">Terbitkan Manifest pengiriman kembali ke Gudang Utama</p>
                    </div>
                    @php $outboundCount = \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::STAGING_OUTBOUND)->count(); @endphp
                    @if($outboundCount > 0)
                        <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-[#FFC232] text-slate-950 shadow-sm animate-pulse">
                            {{ $outboundCount }} SPK
                        </span>
                    @endif
                </a>
            </div>
        </div>

        {{-- 5. REVISI & GARANSI SHEET --}}
        <div x-show="activeDrawer === 'revisi'" class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-black text-[#0F172A] uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#FFC232]"></span>
                    <span>Revisi &amp; Garansi Workshop</span>
                </h3>
                <button @click="activeDrawer = null" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900">✕</button>
            </div>
            
            <div class="grid grid-cols-1 gap-2 pt-1">
                <a href="{{ route('revision.index') }}" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between active:scale-95 transition-all">
                    <div>
                        <div class="font-black text-xs text-[#0F172A] flex items-center gap-2">
                            <span>Revisi Teknik</span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-500 mt-0.5">Penanganan &amp; rework SPK revisi dari QC/Pelanggan</p>
                    </div>
                    @php $revCount = \App\Models\WorkOrderRevision::where('status', 'OPEN')->count(); @endphp
                    @if($revCount > 0)
                        <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-[#FFC232] text-slate-950">
                            {{ $revCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('garansi.index') }}" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between active:scale-95 transition-all">
                    <div>
                        <div class="font-black text-xs text-[#22AF85]">Sistem Garansi</div>
                        <p class="text-[10px] font-bold text-slate-500 mt-0.5">Form klaim &amp; pembuatan SPK Garansi baru</p>
                    </div>
                </a>

                <a href="{{ route('finish.list-garansi') }}" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between active:scale-95 transition-all">
                    <div>
                        <div class="font-black text-xs text-[#22AF85]">List Garansi</div>
                        <p class="text-[10px] font-bold text-slate-500 mt-0.5">Laporan &amp; daftar aktif garansi sepatu</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Bottom Portal Switch Button --}}
        <div class="pt-3 border-t border-slate-100">
            <a href="{{ route('dashboard') }}" class="p-3.5 rounded-2xl bg-[#FFC232] text-slate-950 flex items-center justify-center gap-2 font-black text-xs uppercase tracking-wider shadow-md active:scale-95 transition-all">
                <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span>🏠 Portal Utama Admin</span>
            </a>
        </div>
    </div>
</div>
