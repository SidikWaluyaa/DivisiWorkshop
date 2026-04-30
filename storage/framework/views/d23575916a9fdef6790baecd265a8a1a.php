<?php
    $services = \App\Models\Service::orderBy('name')->get()->map(function($service) { return ['name' => data_get($service, 'name'), 'price' => data_get($service, 'price')]; });
?>

<div x-data="reportModalData()"
     x-show="isOpen"
     style="display: none;"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <script>
        function reportModalData() {
            return {
                isOpen: false, 
                workOrderId: null,
                category: 'TEKNIS',
                kendala_1: '', kendala_1_custom: '',
                kendala_2: '', kendala_2_custom: '',
                opsi_solusi_1: '', opsi_solusi_1_custom: '',
                opsi_solusi_2: '', opsi_solusi_2_custom: '',
                estimasiSelesai: '',
                
                masterIssues: [],
                masterSolutions: [],
                isLoadingIssues: false,
                isLoadingSolutions: false,
                
                init() {
                    window.addEventListener('open-report-modal', (e) => {
                        this.workOrderId = e.detail;
                        this.isOpen = true;
                        this.fetchSolutions();
                        this.fetchIssues();
                    });
                    
                    this.$watch('category', () => {
                        if (this.isOpen && this.category !== 'OVERLOAD') {
                            this.fetchIssues();
                            this.fetchSolutions();
                            // Reset selections when category changes
                            this.kendala_1 = ''; this.kendala_1_custom = '';
                            this.kendala_2 = ''; this.kendala_2_custom = '';
                            this.opsi_solusi_1 = ''; this.opsi_solusi_1_custom = '';
                            this.opsi_solusi_2 = ''; this.opsi_solusi_2_custom = '';
                        }
                    });
                },

                async fetchIssues() {
                    this.isLoadingIssues = true;
                    this.masterIssues = [];
                    try {
                        const res = await fetch(`/api/cx/master-issues?category=${this.category}&_t=${Date.now()}`);
                        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                        const payload = await res.json();
                        this.masterIssues = payload.data || [];
                    } catch(e) {
                        console.error('Failed to fetch master issues', e);
                    } finally {
                        this.isLoadingIssues = false;
                    }
                },

                async fetchSolutions() {
                    this.isLoadingSolutions = true;
                    this.masterSolutions = [];
                    try {
                        const res = await fetch(`/api/cx/master-solutions?category=${this.category}&_t=${Date.now()}`);
                        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                        const payload = await res.json();
                        this.masterSolutions = payload.data || [];
                    } catch(e) {
                        console.error('Failed to fetch master solutions', e);
                    } finally {
                        this.isLoadingSolutions = false;
                    }
                },

                close() {
                    this.isOpen = false;
                    this.kendala_1 = ''; this.kendala_1_custom = '';
                    this.kendala_2 = ''; this.kendala_2_custom = '';
                    this.opsi_solusi_1 = ''; this.opsi_solusi_1_custom = '';
                    this.opsi_solusi_2 = ''; this.opsi_solusi_2_custom = '';
                    this.category = 'TEKNIS';
                    this.estimasiSelesai = '';
                }
            };
        }
    </script>

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh] overflow-hidden" 
         @click.away="close()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">
        
        
        <div class="bg-amber-500 p-4 flex justify-between items-center shrink-0">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Lapor Kendala / Follow Up
            </h3>
            <button @click="close()" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        
        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
            <form action="<?php echo e(route('cx-issues.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="work_order_id" :value="workOrderId">
                
                
                <input type="hidden" name="kendala_1" :value="kendala_1 === 'Lainnya' ? kendala_1_custom : kendala_1">
                <input type="hidden" name="kendala_2" :value="kendala_2 === 'Lainnya' ? kendala_2_custom : kendala_2">
                <input type="hidden" name="opsi_solusi_1" :value="opsi_solusi_1 === 'Lainnya' ? opsi_solusi_1_custom : opsi_solusi_1">
                <input type="hidden" name="opsi_solusi_2" :value="opsi_solusi_2 === 'Lainnya' ? opsi_solusi_2_custom : opsi_solusi_2">
                
                <div class="mb-4">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kategori Masalah</label>
                    <select name="category" x-model="category" class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm">
                        <option value="TEKNIS">Teknis</option>
                        <option value="MATERIAL">Material</option>
                        <option value="OVERLOAD">Overload</option>
                        <option value="KONFIRMASI">Konfirmasi</option>
                    </select>
                </div>

                
                <div class="mb-6 p-4 bg-red-50 rounded-2xl border border-red-200" x-show="category === 'OVERLOAD'" x-cloak>
                    <label class="block text-sm font-black text-red-700 mb-2 uppercase tracking-wider">Request Perubahan Estimasi Selesai</label>
                    <p class="text-xs text-red-600 mb-3">Tentukan estimasi selesai yang baru. Perubahan ini akan memohon persetujuan CX (tidak langsung mengubah data Order).</p>
                    <input type="date" x-model="estimasiSelesai" name="estimasi_selesai" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-sm p-2">
                </div>

                
                <div class="mb-4 space-y-4" x-show="['TEKNIS', 'MATERIAL', 'KONFIRMASI'].includes(category)" x-cloak>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 tracking-wider" x-text="category === 'KONFIRMASI' ? 'Tujuan / Detail Konfirmasi' : 'Detail Kendala'"></label>
                        <p class="text-[10px] text-gray-500 mb-2 leading-tight" x-text="category === 'KONFIRMASI' ? 'Pilih topik yang perlu dikonfirmasi kepada pelanggan, atau pilih \'Lainnya\' untuk mengetik. (Input 2 s/d 4 opsional).' : 'Pilih detail kendala yang sesuai atau pilih \'Lainnya\' untuk mengetik manual.'"></p>
                        <div class="space-y-3">
                             <div x-data="{ open: false }" :class="open ? 'relative z-50' : 'relative z-10'" class="flex flex-col gap-2 transition-all duration-200 ease-in-out">
                                <div class="flex items-center gap-2">
                                    <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-1.5 rounded">1</span>
                                    <div class="relative flex-1 w-full text-sm">
                                        <button type="button" @click="open = !open" @click.away="open = false" :disabled="isLoadingIssues"
                                            class="w-full text-left bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 p-2.5 transition-all flex justify-between items-center disabled:bg-gray-100 disabled:cursor-not-allowed">
                                            <span x-text="kendala_1 === '' ? (category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi Utama --' : '-- Pilih Kendala Pertama --') : kendala_1" class="truncate block pr-4"></span>
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        <div x-show="open" x-cloak x-transition
                                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                            <ul class="max-h-60 overflow-y-auto w-full custom-scrollbar">
                                                <li>
                                                    <button type="button" @click="kendala_1 = ''; open = false" 
                                                        class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-500 border-b border-gray-100"
                                                        x-text="category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi Utama --' : '-- Pilih Kendala Pertama --'">
                                                    </button>
                                                </li>
                                                <template x-for="issue in masterIssues" :key="issue.id">
                                                    <li>
                                                        <button type="button" @click="kendala_1 = issue.name; open = false"
                                                            class="w-full text-left px-4 py-3 hover:bg-amber-50 text-gray-700 border-b border-gray-100 transition-colors">
                                                            <span x-text="issue.name" class="block whitespace-normal break-words leading-relaxed"></span>
                                                        </button>
                                                    </li>
                                                </template>
                                                <li>
                                                    <button type="button" @click="kendala_1 = 'Lainnya'; open = false"
                                                        class="w-full text-left px-4 py-3 hover:bg-amber-100 text-amber-600 font-bold italic">
                                                        Lainnya (Ketik Manual)...
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="kendala_1 === 'Lainnya'" x-cloak class="pl-8">
                                    <input type="text" x-model="kendala_1_custom" class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm p-2" :placeholder="category === 'KONFIRMASI' ? 'Ketikan topik konfirmasi pertama secara manual...' : 'Ketikan detail kendala pertama secara manual...'">
                                </div>
                             </div>
                             
                             <div x-data="{ open: false }" :class="open ? 'relative z-50' : 'relative z-10'" class="flex flex-col gap-2 transition-all duration-200 ease-in-out">
                                <div class="flex items-center gap-2">
                                    <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-1.5 rounded">2</span>
                                    <div class="relative flex-1 w-full text-sm">
                                        <button type="button" @click="open = !open" @click.away="open = false" :disabled="isLoadingIssues"
                                            class="w-full text-left bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 p-2.5 transition-all flex justify-between items-center disabled:bg-gray-100 disabled:cursor-not-allowed">
                                            <span x-text="kendala_2 === '' ? (category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi 2 (Opsional) --' : '-- Pilih Kendala Kedua (Opsional) --') : kendala_2" class="truncate block pr-4"></span>
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        <div x-show="open" x-cloak x-transition
                                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                            <ul class="max-h-60 overflow-y-auto w-full custom-scrollbar">
                                                <li>
                                                    <button type="button" @click="kendala_2 = ''; open = false" 
                                                        class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-500 border-b border-gray-100"
                                                        x-text="category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi 2 (Opsional) --' : '-- Pilih Kendala Kedua (Opsional) --'">
                                                    </button>
                                                </li>
                                                <template x-for="issue in masterIssues" :key="issue.id">
                                                    <li>
                                                        <button type="button" @click="kendala_2 = issue.name; open = false"
                                                            class="w-full text-left px-4 py-3 hover:bg-amber-50 text-gray-700 border-b border-gray-100 transition-colors">
                                                            <span x-text="issue.name" class="block whitespace-normal break-words leading-relaxed"></span>
                                                        </button>
                                                    </li>
                                                </template>
                                                <li>
                                                    <button type="button" @click="kendala_2 = 'Lainnya'; open = false"
                                                        class="w-full text-left px-4 py-3 hover:bg-amber-100 text-amber-600 font-bold italic">
                                                        Lainnya (Ketik Manual)...
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="kendala_2 === 'Lainnya'" x-cloak class="pl-8">
                                    <input type="text" x-model="kendala_2_custom" class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm p-2" :placeholder="category === 'KONFIRMASI' ? 'Ketikan topik konfirmasi kedua secara manual...' : 'Ketikan detail kendala kedua secara manual...'">
                                </div>
                             </div>
                        </div>
                    </div>

                    <div>
                        <label x-show="category !== 'KONFIRMASI'" class="block text-sm font-bold text-gray-700 mb-2 tracking-wider">Opsi Solusi</label>
                         <p x-show="category !== 'KONFIRMASI'" class="text-[10px] text-gray-500 mb-2 leading-tight">Pilih saran atau solusi perbaikan untuk customer (Opsional namun disarankan).</p>
                         <div class="space-y-3">
                             <div x-data="{ open: false }" :class="open ? 'relative z-50' : 'relative z-10'" class="flex flex-col gap-2 transition-all duration-200 ease-in-out">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold px-2 py-1.5 rounded" :class="category === 'KONFIRMASI' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'" x-text="category === 'KONFIRMASI' ? '3' : '1'"></span>
                                    <div class="relative flex-1 w-full text-sm">
                                        <button type="button" @click="open = !open" @click.away="open = false" :disabled="isLoadingSolutions"
                                            class="w-full text-left bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 p-2.5 transition-all flex justify-between items-center disabled:bg-gray-100 disabled:cursor-not-allowed">
                                            <span x-text="opsi_solusi_1 === '' ? (category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi 3 (Opsional) --' : '-- Pilih Solusi Pertama --') : opsi_solusi_1" class="truncate block pr-4"></span>
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        <div x-show="open" x-cloak x-transition
                                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                            <ul class="max-h-60 overflow-y-auto w-full custom-scrollbar">
                                                <li>
                                                    <button type="button" @click="opsi_solusi_1 = ''; open = false" 
                                                        class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-500 border-b border-gray-100"
                                                        x-text="category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi 3 (Opsional) --' : '-- Pilih Solusi Pertama --'">
                                                    </button>
                                                </li>
                                                <template x-for="sol in masterSolutions" :key="sol.id">
                                                    <li>
                                                        <button type="button" @click="opsi_solusi_1 = sol.name; open = false"
                                                            class="w-full text-left px-4 py-3 hover:bg-emerald-50 text-gray-700 border-b border-gray-100 transition-colors">
                                                            <span x-text="sol.name" class="block whitespace-normal break-words leading-relaxed"></span>
                                                        </button>
                                                    </li>
                                                </template>
                                                <li>
                                                    <button type="button" @click="opsi_solusi_1 = 'Lainnya'; open = false"
                                                        class="w-full text-left px-4 py-3 hover:bg-emerald-100 text-emerald-600 font-bold italic">
                                                        Lainnya (Ketik Manual)...
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="opsi_solusi_1 === 'Lainnya'" x-cloak class="pl-8">
                                    <input type="text" x-model="opsi_solusi_1_custom" class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm p-2" :placeholder="category === 'KONFIRMASI' ? 'Ketikan topik konfirmasi ketiga secara manual...' : 'Ketikan opsi solusi pertama secara manual...'">
                                </div>
                             </div>

                             <div x-data="{ open: false }" :class="open ? 'relative z-50' : 'relative z-10'" class="flex flex-col gap-2 transition-all duration-200 ease-in-out">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold px-2 py-1.5 rounded" :class="category === 'KONFIRMASI' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'" x-text="category === 'KONFIRMASI' ? '4' : '2'"></span>
                                    <div class="relative flex-1 w-full text-sm">
                                        <button type="button" @click="open = !open" @click.away="open = false" :disabled="isLoadingSolutions"
                                            class="w-full text-left bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 p-2.5 transition-all flex justify-between items-center disabled:bg-gray-100 disabled:cursor-not-allowed">
                                            <span x-text="opsi_solusi_2 === '' ? (category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi 4 (Opsional) --' : '-- Pilih Solusi Kedua (Opsional) --') : opsi_solusi_2" class="truncate block pr-4"></span>
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        <div x-show="open" x-cloak x-transition
                                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                            <ul class="max-h-60 overflow-y-auto w-full custom-scrollbar">
                                                <li>
                                                    <button type="button" @click="opsi_solusi_2 = ''; open = false" 
                                                        class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-500 border-b border-gray-100"
                                                        x-text="category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi 4 (Opsional) --' : '-- Pilih Solusi Kedua (Opsional) --'">
                                                    </button>
                                                </li>
                                                <template x-for="sol in masterSolutions" :key="sol.id">
                                                    <li>
                                                        <button type="button" @click="opsi_solusi_2 = sol.name; open = false"
                                                            class="w-full text-left px-4 py-3 hover:bg-emerald-50 text-gray-700 border-b border-gray-100 transition-colors">
                                                            <span x-text="sol.name" class="block whitespace-normal break-words leading-relaxed"></span>
                                                        </button>
                                                    </li>
                                                </template>
                                                <li>
                                                    <button type="button" @click="opsi_solusi_2 = 'Lainnya'; open = false"
                                                        class="w-full text-left px-4 py-3 hover:bg-emerald-100 text-emerald-600 font-bold italic">
                                                        Lainnya (Ketik Manual)...
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="opsi_solusi_2 === 'Lainnya'" x-cloak class="pl-8">
                                    <input type="text" x-model="opsi_solusi_2_custom" class="w-full border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm p-2" :placeholder="category === 'KONFIRMASI' ? 'Ketikan topik konfirmasi keempat secara manual...' : 'Ketikan opsi solusi kedua secara manual...'">
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
                
                
                <template x-if="category === 'OVERLOAD'">
                    <input type="hidden" name="description" :value="(estimasiSelesai || 'TBD')">
                </template>
                <template x-if="category !== 'OVERLOAD'">
                    <input type="hidden" name="description" :value="'Kendala:\n' + (kendala_1 ? '1. ' + (kendala_1 === 'Lainnya' ? kendala_1_custom : kendala_1) + '\n' : '') + (kendala_2 ? '2. ' + (kendala_2 === 'Lainnya' ? kendala_2_custom : kendala_2) + '\n' : '') + '\nOpsi Solusi:\n' + (opsi_solusi_1 ? '1. ' + (opsi_solusi_1 === 'Lainnya' ? opsi_solusi_1_custom : opsi_solusi_1) + '\n' : '') + (opsi_solusi_2 ? '2. ' + (opsi_solusi_2 === 'Lainnya' ? opsi_solusi_2_custom : opsi_solusi_2) + '\n' : '')">
                </template>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Foto Bukti (Hanya JPG/PNG)</label>
                    <input type="file" name="photos[]" multiple accept=".jpg,.jpeg,.png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[11px] file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    <p class="text-[10px] text-gray-500 mt-1">Maksimal 2MB per gambar. Format: JPG, PNG.</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 sticky bottom-0 bg-white pb-2">
                    <button type="button" @click="close()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold text-xs hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-bold text-xs shadow-md transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\components\report-modal.blade.php ENDPATH**/ ?>