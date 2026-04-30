<div x-data="editIssueModal" x-show="showEditModal" 
class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true" style="display: none;">
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('editIssueModal', () => ({
            showEditModal: false,
            issue: {},
            availableServices: <?php echo \Illuminate\Support\Js::from($services)->toHtml() ?>,
            loading: false,
            alert: { show: false, message: '', type: 'success' },

            k1_select: '', k2_select: '', os1_select: '', os2_select: '',
            masterIssues: [], masterSolutions: [],
            isLoadingIssues: false, isLoadingSolutions: false,

            recService1Category: '', recService1Search: '', recService1Price: 0, recService1Open: false,
            recService2Category: '', recService2Search: '', recService2Price: 0, recService2Open: false,
            sugService1Category: '', sugService1Search: '', sugService1Price: 0, sugService1Open: false,
            sugService2Category: '', sugService2Search: '', sugService2Price: 0, sugService2Open: false,

            init() {
                window.addEventListener('open-edit-issue-modal', async (e) => {
                    this.issue = Object.assign({}, e.detail);
                    // Ensure all fields exist even if null from DB to enable two-way binding
                    const fields = ['kendala_1', 'kendala_2', 'opsi_solusi_1', 'opsi_solusi_2', 
                                   'desc_upper', 'desc_sol', 'desc_kondisi_bawaan', 
                                   'rec_service_1', 'rec_service_2', 'sug_service_1', 'sug_service_2',
                                   'recommended_services', 'suggested_services'];
                    fields.forEach(f => {
                        this.issue[f] = this.issue[f] || '';
                    });
                    
                    await this.fetchIssues();
                    await this.fetchSolutions();
                    this.initSelects();
                    
                    this.parseAllServices();
                    this.showEditModal = true;
                });
            },

            async fetchIssues() {
                if (!this.issue.category || this.issue.category === 'OVERLOAD') return;
                this.isLoadingIssues = true;
                this.masterIssues = [];
                try {
                    const res = await fetch(`/api/cx/master-issues?category=${this.issue.category}&_t=${Date.now()}`);
                    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                    const payload = await res.json();
                    this.masterIssues = payload.data || [];
                } catch(e) { console.error(e); }
                this.isLoadingIssues = false;
            },

            async fetchSolutions() {
                if (!this.issue.category || this.issue.category === 'OVERLOAD') return;
                this.isLoadingSolutions = true;
                this.masterSolutions = [];
                try {
                    const res = await fetch(`/api/cx/master-solutions?category=${this.issue.category}&_t=${Date.now()}`);
                    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                    const payload = await res.json();
                    this.masterSolutions = payload.data || [];
                } catch(e) { console.error(e); }
                this.isLoadingSolutions = false;
            },

            initSelects() {
                const checkMaster = (val, list) => {
                    if (!val) return '';
                    if (list.find(item => item.name === val)) return val;
                    return 'Lainnya';
                };
                this.k1_select = checkMaster(this.issue.kendala_1, this.masterIssues);
                this.k2_select = checkMaster(this.issue.kendala_2, this.masterIssues);
                this.os1_select = checkMaster(this.issue.opsi_solusi_1, this.masterSolutions);
                this.os2_select = checkMaster(this.issue.opsi_solusi_2, this.masterSolutions);
            },

            showAlert(message, type = 'success') {
                this.alert = { show: true, message, type };
                setTimeout(() => {
                    this.alert.show = false;
                }, 3000);
            },

            parseAllServices() {
                if (!this.issue.rec_service_1 && this.issue.recommended_services) {
                    const lines = this.issue.recommended_services.split('\n').map(l => l.replace(/^\d+\.\s+/, '').trim());
                    this.issue.rec_service_1 = lines[0] || '';
                    this.issue.rec_service_2 = lines[1] || '';
                }
                if (!this.issue.sug_service_1 && this.issue.suggested_services) {
                    const lines = this.issue.suggested_services.split('\n').map(l => l.replace(/^\d+\.\s+/, '').trim());
                    this.issue.sug_service_1 = lines[0] || '';
                    this.issue.sug_service_2 = lines[1] || '';
                }

                const parse = (str) => {
                    if (!str) return { name: '', price: 0, cat: '' };
                    const match = str.match(/(.+) \(Rp ([\d.]+)\)/);
                    if (match) {
                        const name = match[1].trim();
                        const priceMatch = match[2].replace(/\./g, '');
                        const price = parseInt(priceMatch) || 0;
                        const service = this.availableServices.find(s => s.name.toLowerCase() === name.toLowerCase());
                        return { name, price, cat: service ? service.category : '' };
                    }
                    return { name: str, price: 0, cat: '' };
                };

                const r1 = parse(this.issue.rec_service_1);
                this.recService1Category = r1.cat; this.recService1Search = r1.name; this.recService1Price = r1.price;
                const r2 = parse(this.issue.rec_service_2);
                this.recService2Category = r2.cat; this.recService2Search = r2.name; this.recService2Price = r2.price;
                const s1 = parse(this.issue.sug_service_1);
                this.sugService1Category = s1.cat; this.sugService1Search = s1.name; this.sugService1Price = s1.price;
                const s2 = parse(this.issue.sug_service_2);
                this.sugService2Category = s2.cat; this.sugService2Search = s2.name; this.sugService2Price = s2.price;
            },

            get uniqueCategories() {
                return [...new Set(this.availableServices.map(s => s.category))].sort();
            },

            getFilteredServices(category, search) {
                if (!category) return [];
                return this.availableServices.filter(s => 
                    s.category === category && 
                    s.name.toLowerCase().includes((search || '').toLowerCase())
                );
            },

            formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID').format(amount);
            },

            updateServiceValue(type, index) {
                const name = this[`${type}Service${index}Search`] || '';
                const price = this[`${type}Service${index}Price`] || 0;
                if (name) {
                    this.issue[`${type}_service_${index}`] = `${name} (Rp ${this.formatRupiah(price)})`;
                } else {
                    this.issue[`${type}_service_${index}`] = '';
                }
            },

            selectService(type, index, service) {
                this[`${type}Service${index}Search`] = service.name;
                this[`${type}Service${index}Price`] = service.price;
                this[`${type}Service${index}Open`] = false;
                this.updateServiceValue(type, index);
            },

            onCategoryChange(type, index) {
                this[`${type}Service${index}Search`] = '';
                this[`${type}Service${index}Price`] = 0;
                this[`${type}Service${index}Open`] = false;
                this.updateServiceValue(type, index);
            },

            async updateIssue() {
                this.loading = true;
                try {
                    const response = await fetch(`/cx-issues/${this.issue.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.issue)
                    });
                    
                    if (response.ok) {
                        this.showAlert('Berhasil mengupdate data issue!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        let result;
                        try {
                            result = await response.json();
                        } catch(e) {
                            result = { message: 'Terjadi kesalahan sistem.' };
                        }
                        
                        if (response.status === 422 && result.errors) {
                            const firstError = Object.values(result.errors)[0][0];
                            this.showAlert('Validasi Gagal: ' + firstError, 'error');
                        } else {
                            this.showAlert('Gagal: ' + (result.message || 'Error tidak diketahui'), 'error');
                        }
                    }
                } catch (error) {
                    console.error(error);
                    this.showAlert('Terjadi kesalahan koneksi.', 'error');
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
    </script>
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div x-show="showEditModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity" 
             aria-hidden="true" 
             @click="showEditModal = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        
        <div x-show="showEditModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-gray-900 rounded-[2.5rem] text-left overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-gray-800 relative">
            
            
            <div class="px-8 py-6 border-b border-gray-800 flex justify-between items-center bg-gray-900 rounded-t-3xl">
                <div>
                    <h3 class="text-2xl font-black text-white italic tracking-tighter uppercase leading-none">Elite Edit Issue</h3>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mt-2 italic flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        SPK: <span x-text="issue.spk_number || '-'" class="text-white border-b-2 border-emerald-500/20"></span>
                    </p>
                </div>
                <button @click="showEditModal = false" class="text-gray-500 hover:text-white hover:bg-gray-800 p-3 rounded-2xl transition-all active:scale-90">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            
            <div x-show="alert.show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="absolute top-24 left-8 right-8 z-[70]">
                <div :class="alert.type === 'success' ? 'bg-emerald-500/10 border-emerald-500/50 text-emerald-400' : 'bg-red-500/10 border-red-500/50 text-red-400'"
                     class="px-6 py-4 rounded-2xl border backdrop-blur-md shadow-2xl flex items-center gap-4">
                    <div :class="alert.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'" class="w-2 h-2 rounded-full animate-ping"></div>
                    <span class="text-xs font-black uppercase tracking-widest italic" x-text="alert.message"></span>
                </div>
            </div>

            <div class="px-8 py-8 space-y-8 bg-gray-900">
                
                <div class="space-y-4" x-show="['TEKNIS', 'MATERIAL', 'KONFIRMASI'].includes(issue.category)">
                    <label class="block text-sm font-black text-amber-500 uppercase tracking-widest italic flex items-center gap-2">
                        <span x-text="issue.category === 'KONFIRMASI' ? '⚠️ Detail Konfirmasi & Tindakan' : '⚠️ Detail Kendala & Solusi'"></span>
                        <span class="h-px flex-1 bg-amber-500/20"></span>
                    </label>
                    <div class="space-y-3">
                        <div x-data="{ open: false }" :class="open ? 'relative z-50' : 'relative z-10'" class="flex flex-col gap-2 shadow-lg bg-gray-800 rounded-2xl p-2 border border-gray-700 transition-all duration-200">
                            <div class="flex items-stretch">
                                <div class="w-32 flex-shrink-0 bg-black rounded-l-xl flex items-center px-4 justify-between">
                                    <span class="text-[9px] font-black text-amber-500/80 uppercase tracking-wider" x-text="issue.category === 'KONFIRMASI' ? 'Konfirmasi' : 'Kendala'"></span>
                                    <span class="bg-amber-500/20 text-amber-500 text-[10px] font-black px-1.5 py-0.5 rounded">1</span>
                                </div>
                                <div class="relative flex-1">
                                    <button type="button" @click="open = !open" @click.away="open = false" 
                                        class="w-full h-full text-left bg-gray-800 border-none text-white rounded-r-xl focus:ring-amber-500/50 focus:border-amber-500/50 font-bold text-sm py-4 px-5 transition-all flex justify-between items-center outline-none">
                                        <span x-text="k1_select === '' ? (issue.category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi Utama --' : '-- Pilih Kendala Pertama --') : k1_select" class="truncate pr-4 block"></span>
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="open" x-cloak x-transition
                                        class="absolute z-[60] w-full mt-1 bg-gray-800 border border-gray-700 rounded-xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.8)] overflow-hidden left-0">
                                        <ul class="max-h-60 overflow-y-auto w-full custom-scrollbar">
                                            <li>
                                                <button type="button" @click="k1_select = ''; issue.kendala_1 = ''; open = false" 
                                                    class="w-full text-left px-5 py-3 hover:bg-gray-700 border-b border-gray-700 text-gray-400 font-normal"
                                                    x-text="issue.category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi Utama --' : '-- Pilih Kendala Pertama --'">
                                                </button>
                                            </li>
                                            <template x-for="item in masterIssues" :key="item.id">
                                                <li>
                                                    <button type="button" @click="k1_select = item.name; issue.kendala_1 = item.name; open = false"
                                                        class="w-full text-left px-5 py-3 hover:bg-amber-500/10 hover:text-amber-400 border-b border-gray-700 text-gray-200 transition-colors">
                                                        <span x-text="item.name" class="block whitespace-normal break-words leading-relaxed font-normal"></span>
                                                    </button>
                                                </li>
                                            </template>
                                            <li>
                                                <button type="button" @click="k1_select = 'Lainnya'; issue.kendala_1 = ''; open = false"
                                                    class="w-full text-left px-5 py-3 hover:bg-amber-500/20 text-amber-500 font-bold italic">
                                                    Lainnya (Ketik Manual)...
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <input type="text" x-model="issue.kendala_1" x-show="k1_select === 'Lainnya'" x-cloak
                                :placeholder="issue.category === 'KONFIRMASI' ? 'Ketikan topik konfirmasi pertama secara manual...' : 'Ketikan detail kendala pertama secara manual...'"
                                class="bg-gray-900 border-gray-700 text-amber-400 rounded-xl focus:ring-amber-500/50 focus:border-amber-500/50 font-bold text-sm py-3 px-4 ml-32 transition-all">
                        </div>

                        <div x-data="{ open: false }" :class="open ? 'relative z-50' : 'relative z-10'" class="flex flex-col gap-2 shadow-lg bg-gray-800 rounded-2xl p-2 border border-gray-700 transition-all duration-200">
                             <div class="flex items-stretch">
                                <div class="w-32 flex-shrink-0 bg-black rounded-l-xl flex items-center px-4 justify-between">
                                    <span class="text-[9px] font-black text-amber-500/80 uppercase tracking-wider" x-text="issue.category === 'KONFIRMASI' ? 'Konfirmasi' : 'Kendala'"></span>
                                    <span class="bg-amber-500/20 text-amber-500 text-[10px] font-black px-1.5 py-0.5 rounded">2</span>
                                </div>
                                <div class="relative flex-1">
                                    <button type="button" @click="open = !open" @click.away="open = false" 
                                        class="w-full h-full text-left bg-gray-800 border-none text-white rounded-r-xl focus:ring-amber-500/50 focus:border-amber-500/50 font-bold text-sm py-4 px-5 transition-all flex justify-between items-center outline-none">
                                        <span x-text="k2_select === '' ? (issue.category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi 2 (Opsional) --' : '-- Pilih Kendala Kedua (Opsional) --') : k2_select" class="truncate pr-4 block"></span>
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="open" x-cloak x-transition
                                        class="absolute z-[60] w-full mt-1 bg-gray-800 border border-gray-700 rounded-xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.8)] overflow-hidden left-0">
                                        <ul class="max-h-60 overflow-y-auto w-full custom-scrollbar">
                                            <li>
                                                <button type="button" @click="k2_select = ''; issue.kendala_2 = ''; open = false" 
                                                    class="w-full text-left px-5 py-3 hover:bg-gray-700 border-b border-gray-700 text-gray-400 font-normal"
                                                    x-text="issue.category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi 2 (Opsional) --' : '-- Pilih Kendala Kedua (Opsional) --'">
                                                </button>
                                            </li>
                                            <template x-for="item in masterIssues" :key="item.id">
                                                <li>
                                                    <button type="button" @click="k2_select = item.name; issue.kendala_2 = item.name; open = false"
                                                        class="w-full text-left px-5 py-3 hover:bg-amber-500/10 hover:text-amber-400 border-b border-gray-700 text-gray-200 transition-colors">
                                                        <span x-text="item.name" class="block whitespace-normal break-words leading-relaxed font-normal"></span>
                                                    </button>
                                                </li>
                                            </template>
                                            <li>
                                                <button type="button" @click="k2_select = 'Lainnya'; issue.kendala_2 = ''; open = false"
                                                    class="w-full text-left px-5 py-3 hover:bg-amber-500/20 text-amber-500 font-bold italic">
                                                    Lainnya (Ketik Manual)...
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <input type="text" x-model="issue.kendala_2" x-show="k2_select === 'Lainnya'" x-cloak
                                :placeholder="issue.category === 'KONFIRMASI' ? 'Ketikan topik konfirmasi kedua secara manual...' : 'Ketikan detail kendala kedua secara manual...'"
                                class="bg-gray-900 border-gray-700 text-amber-400 rounded-xl focus:ring-amber-500/50 focus:border-amber-500/50 font-bold text-sm py-3 px-4 ml-32 transition-all">
                        </div>

                        <div x-data="{ open: false }" :class="open ? 'relative z-50' : 'relative z-10'" class="flex flex-col gap-2 shadow-lg bg-gray-800 rounded-2xl p-2 border border-gray-700 transition-all duration-200">
                             <div class="flex items-stretch">
                                <div class="w-32 flex-shrink-0 bg-black rounded-l-xl flex items-center px-4 justify-between">
                                    <span class="text-[9px] font-black text-emerald-500/80 uppercase tracking-wider" x-text="issue.category === 'KONFIRMASI' ? 'Konfirmasi' : 'Solusi'"></span>
                                    <span class="bg-emerald-500/20 text-emerald-500 text-[10px] font-black px-1.5 py-0.5 rounded" x-text="issue.category === 'KONFIRMASI' ? '3' : '1'"></span>
                                </div>
                                <div class="relative flex-1">
                                    <button type="button" @click="open = !open" @click.away="open = false" 
                                        class="w-full h-full text-left bg-gray-800 border-none text-white rounded-r-xl focus:ring-emerald-500/50 focus:border-emerald-500/50 font-bold text-sm py-4 px-5 transition-all flex justify-between items-center outline-none">
                                        <span x-text="os1_select === '' ? (issue.category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi 3 (Opsional) --' : '-- Pilih Opsi Solusi Pertama --') : os1_select" class="truncate pr-4 block"></span>
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="open" x-cloak x-transition
                                        class="absolute z-[60] w-full mt-1 bg-gray-800 border border-gray-700 rounded-xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.8)] overflow-hidden left-0">
                                        <ul class="max-h-60 overflow-y-auto w-full custom-scrollbar">
                                            <li>
                                                <button type="button" @click="os1_select = ''; issue.opsi_solusi_1 = ''; open = false" 
                                                    class="w-full text-left px-5 py-3 hover:bg-gray-700 border-b border-gray-700 text-gray-400 font-normal"
                                                    x-text="issue.category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi 3 (Opsional) --' : '-- Pilih Opsi Solusi Pertama --'">
                                                </button>
                                            </li>
                                            <template x-for="item in masterSolutions" :key="item.id">
                                                <li>
                                                    <button type="button" @click="os1_select = item.name; issue.opsi_solusi_1 = item.name; open = false"
                                                        class="w-full text-left px-5 py-3 hover:bg-emerald-500/10 hover:text-emerald-400 border-b border-gray-700 text-gray-200 transition-colors">
                                                        <span x-text="item.name" class="block whitespace-normal break-words leading-relaxed font-normal"></span>
                                                    </button>
                                                </li>
                                            </template>
                                            <li>
                                                <button type="button" @click="os1_select = 'Lainnya'; issue.opsi_solusi_1 = ''; open = false"
                                                    class="w-full text-left px-5 py-3 hover:bg-emerald-500/20 text-emerald-500 font-bold italic">
                                                    Lainnya (Ketik Manual)...
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <input type="text" x-model="issue.opsi_solusi_1" x-show="os1_select === 'Lainnya'" x-cloak
                                :placeholder="issue.category === 'KONFIRMASI' ? 'Ketikan topik konfirmasi ketiga secara manual...' : 'Ketikan opsi solusi pertama secara manual...'"
                                class="bg-gray-900 border-gray-700 text-emerald-400 rounded-xl focus:ring-emerald-500/50 focus:border-emerald-500/50 font-bold text-sm py-3 px-4 ml-32 transition-all">
                        </div>

                         <div x-data="{ open: false }" :class="open ? 'relative z-50' : 'relative z-10'" class="flex flex-col gap-2 shadow-lg bg-gray-800 rounded-2xl p-2 border border-gray-700 transition-all duration-200">
                             <div class="flex items-stretch">
                                <div class="w-32 flex-shrink-0 bg-black rounded-l-xl flex items-center px-4 justify-between">
                                    <span class="text-[9px] font-black text-emerald-500/80 uppercase tracking-wider" x-text="issue.category === 'KONFIRMASI' ? 'Konfirmasi' : 'Solusi'"></span>
                                    <span class="bg-emerald-500/20 text-emerald-500 text-[10px] font-black px-1.5 py-0.5 rounded" x-text="issue.category === 'KONFIRMASI' ? '4' : '2'"></span>
                                </div>
                                <div class="relative flex-1">
                                    <button type="button" @click="open = !open" @click.away="open = false" 
                                        class="w-full h-full text-left bg-gray-800 border-none text-white rounded-r-xl focus:ring-emerald-500/50 focus:border-emerald-500/50 font-bold text-sm py-4 px-5 transition-all flex justify-between items-center outline-none">
                                        <span x-text="os2_select === '' ? (issue.category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi 4 (Opsional) --' : '-- Pilih Opsi Solusi Kedua (Opsional) --') : os2_select" class="truncate pr-4 block"></span>
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="open" x-cloak x-transition
                                        class="absolute z-[60] w-full mt-1 bg-gray-800 border border-gray-700 rounded-xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.8)] overflow-hidden left-0">
                                        <ul class="max-h-60 overflow-y-auto w-full custom-scrollbar">
                                            <li>
                                                <button type="button" @click="os2_select = ''; issue.opsi_solusi_2 = ''; open = false" 
                                                    class="w-full text-left px-5 py-3 hover:bg-gray-700 border-b border-gray-700 text-gray-400 font-normal"
                                                    x-text="issue.category === 'KONFIRMASI' ? '-- Pilih Topik Konfirmasi 4 (Opsional) --' : '-- Pilih Opsi Solusi Kedua (Opsional) --'">
                                                </button>
                                            </li>
                                            <template x-for="item in masterSolutions" :key="item.id">
                                                <li>
                                                    <button type="button" @click="os2_select = item.name; issue.opsi_solusi_2 = item.name; open = false"
                                                        class="w-full text-left px-5 py-3 hover:bg-emerald-500/10 hover:text-emerald-400 border-b border-gray-700 text-gray-200 transition-colors">
                                                        <span x-text="item.name" class="block whitespace-normal break-words leading-relaxed font-normal"></span>
                                                    </button>
                                                </li>
                                            </template>
                                            <li>
                                                <button type="button" @click="os2_select = 'Lainnya'; issue.opsi_solusi_2 = ''; open = false"
                                                    class="w-full text-left px-5 py-3 hover:bg-emerald-500/20 text-emerald-500 font-bold italic">
                                                    Lainnya (Ketik Manual)...
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <input type="text" x-model="issue.opsi_solusi_2" x-show="os2_select === 'Lainnya'" x-cloak
                                :placeholder="issue.category === 'KONFIRMASI' ? 'Ketikan topik konfirmasi keempat secara manual...' : 'Ketikan opsi solusi kedua secara manual...'"
                                class="bg-gray-900 border-gray-700 text-emerald-400 rounded-xl focus:ring-emerald-500/50 focus:border-emerald-500/50 font-bold text-sm py-3 px-4 ml-32 transition-all">
                        </div>
                    </div>
                </div>

                
                <div class="space-y-4" x-show="issue.desc_upper || issue.desc_sol || issue.desc_kondisi_bawaan || issue.category === 'MATERIAL' || issue.category === 'QC'">
                    <label class="block text-sm font-black text-red-500 uppercase tracking-widest italic flex items-center gap-2">
                        <span>Alasan Penolakan / Kerusakan</span>
                        <span class="h-px flex-1 bg-red-500/20"></span>
                    </label>
                    <div class="space-y-3">
                        <div class="flex items-stretch shadow-lg">
                            <div class="w-32 flex-shrink-0 bg-black border-y border-l border-gray-800 rounded-l-2xl flex items-center px-4">
                                <span class="text-[9px] font-black text-red-500/80 uppercase tracking-wider">1. Upper</span>
                            </div>
                            <input type="text" x-model="issue.desc_upper" 
                                placeholder="Detail kondisi bagian atas sepatu..."
                                class="flex-1 bg-gray-800 border-gray-700 text-white rounded-r-2xl focus:ring-red-500/50 focus:border-red-500/50 font-bold text-sm py-4 px-5 transition-all">
                        </div>

                        <div class="flex items-stretch shadow-lg">
                            <div class="w-32 flex-shrink-0 bg-black border-y border-l border-gray-800 rounded-l-2xl flex items-center px-4">
                                <span class="text-[9px] font-black text-red-500/80 uppercase tracking-wider">2. Sol</span>
                            </div>
                            <input type="text" x-model="issue.desc_sol" 
                                placeholder="Detail kondisi bagian sol/bawah..."
                                class="flex-1 bg-gray-800 border-gray-700 text-white rounded-r-2xl focus:ring-red-500/50 focus:border-red-500/50 font-bold text-sm py-4 px-5 transition-all">
                        </div>

                        <div class="flex items-stretch shadow-lg">
                            <div class="w-32 flex-shrink-0 bg-black border-y border-l border-gray-800 rounded-l-2xl flex items-center px-4">
                                <span class="text-[9px] font-black text-red-500/80 uppercase tracking-wider leading-tight">3. Kondisi Bawaan</span>
                            </div>
                            <input type="text" x-model="issue.desc_kondisi_bawaan" 
                                placeholder="Detail kondisi bawaan lainnya..."
                                class="flex-1 bg-gray-800 border-gray-700 text-white rounded-r-2xl focus:ring-red-500/50 focus:border-red-500/50 font-bold text-sm py-4 px-5 transition-all">
                        </div>
                    </div>
                </div>

                
                <div class="space-y-4">
                    <label class="block text-sm font-black text-blue-400 uppercase tracking-widest italic flex items-center gap-2">
                        <span>💎 Saran Layanan (Rekomendasi)</span>
                        <span class="h-px flex-1 bg-blue-500/20"></span>
                    </label>
                    <div class="space-y-4">
                        
                        <div class="flex gap-2">
                            <div class="w-40">
                                <select x-model="recService1Category" @change="onCategoryChange('rec', 1)"
                                    class="w-full bg-black border-gray-800 rounded-2xl px-4 py-4 text-sm font-bold text-gray-400 focus:ring-blue-500/50 focus:border-blue-500 transition-all cursor-pointer">
                                    <option value="">-- Kategori --</option>
                                    <template x-for="cat in uniqueCategories" :key="cat">
                                        <option :value="cat" x-text="cat"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="flex-1 relative" @click.away="recService1Open = false">
                                <div class="flex items-stretch shadow-xl group">
                                    <div class="w-20 flex-shrink-0 bg-black border-y border-l border-gray-800 rounded-l-2xl flex items-center px-4">
                                        <span class="text-[8px] font-black text-blue-500/80 uppercase tracking-wider leading-tight text-center">Rec 1</span>
                                    </div>
                                    <input type="text" x-model="recService1Search" 
                                        @focus="recService1Open = true"
                                        @input="updateServiceValue('rec', 1)"
                                        :disabled="!recService1Category"
                                        placeholder="Cari atau nama jasa..."
                                        class="flex-1 bg-gray-800 border-gray-700 text-white focus:ring-blue-500/50 focus:border-blue-500 font-bold text-sm py-4 px-5 disabled:opacity-30 transition-all">
                                    <input type="number" x-model="recService1Price"
                                        @input="updateServiceValue('rec', 1)"
                                        placeholder="Harga"
                                        class="w-40 bg-black border-y border-r border-gray-800 text-blue-400 rounded-r-2xl focus:ring-blue-500/50 focus:border-blue-500 font-black text-sm py-4 px-5 text-right transition-all">
                                </div>
                                <div x-show="recService1Open && recService1Category" x-transition
                                    class="absolute left-0 right-0 top-full mt-2 bg-black border border-gray-800 rounded-2xl shadow-2xl z-[100] max-h-60 overflow-y-auto overflow-x-hidden border-t-0 p-1">
                                    <template x-for="service in getFilteredServices(recService1Category, recService1Search)">
                                        <div @click="selectService('rec', 1, service)" 
                                            class="px-5 py-4 hover:bg-gray-800 rounded-xl cursor-pointer flex justify-between items-center group transition-colors">
                                            <span class="text-sm font-bold text-gray-300 group-hover:text-blue-400" x-text="service.name"></span>
                                            <span class="text-xs font-black text-blue-500/80" x-text="'Rp ' + parseInt(service.price).toLocaleString()"></span>
                                        </div>
                                    </template>
                                    <div x-show="recService1Search && getFilteredServices(recService1Category, recService1Search).length === 0" class="px-5 py-4 text-gray-500 italic text-xs">
                                        ✏️ Layanan Kustom Terdeteksi...
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-2">
                            <div class="w-40">
                                <select x-model="recService2Category" @change="onCategoryChange('rec', 2)"
                                    class="w-full bg-black border-gray-800 rounded-2xl px-4 py-4 text-sm font-bold text-gray-400 focus:ring-blue-500/50 focus:border-blue-500 transition-all cursor-pointer">
                                    <option value="">-- Kategori --</option>
                                    <template x-for="cat in uniqueCategories" :key="cat">
                                        <option :value="cat" x-text="cat"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="flex-1 relative" @click.away="recService2Open = false">
                                <div class="flex items-stretch shadow-xl group">
                                    <div class="w-20 flex-shrink-0 bg-black border-y border-l border-gray-800 rounded-l-2xl flex items-center px-4">
                                        <span class="text-[8px] font-black text-blue-500/80 uppercase tracking-wider leading-tight text-center">Rec 2</span>
                                    </div>
                                    <input type="text" x-model="recService2Search" 
                                        @focus="recService2Open = true"
                                        @input="updateServiceValue('rec', 2)"
                                        :disabled="!recService2Category"
                                        placeholder="Cari atau nama jasa..."
                                        class="flex-1 bg-gray-800 border-gray-700 text-white focus:ring-blue-500/50 focus:border-blue-500 font-bold text-sm py-4 px-5 disabled:opacity-30 transition-all">
                                    <input type="number" x-model="recService2Price"
                                        @input="updateServiceValue('rec', 2)"
                                        placeholder="Harga"
                                        class="w-40 bg-black border-y border-r border-gray-800 text-blue-400 rounded-r-2xl focus:ring-blue-500/50 focus:border-blue-500 font-black text-sm py-4 px-5 text-right transition-all">
                                </div>
                                <div x-show="recService2Open && recService2Category" x-transition
                                    class="absolute left-0 right-0 top-full mt-2 bg-black border border-gray-800 rounded-2xl shadow-2xl z-[100] max-h-60 overflow-y-auto overflow-x-hidden border-t-0 p-1">
                                    <template x-for="service in getFilteredServices(recService2Category, recService2Search)">
                                        <div @click="selectService('rec', 2, service)" 
                                            class="px-5 py-4 hover:bg-gray-800 rounded-xl cursor-pointer flex justify-between items-center group transition-colors">
                                            <span class="text-sm font-bold text-gray-300 group-hover:text-blue-400" x-text="service.name"></span>
                                            <span class="text-xs font-black text-blue-500/80" x-text="'Rp ' + parseInt(service.price).toLocaleString()"></span>
                                        </div>
                                    </template>
                                    <div x-show="recService2Search && getFilteredServices(recService2Category, recService2Search).length === 0" class="px-5 py-4 text-gray-500 italic text-xs">
                                        ✏️ Layanan Kustom Terdeteksi...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="space-y-4">
                    <label class="block text-sm font-black text-amber-500 uppercase tracking-widest italic flex items-center gap-2">
                        <span>✨ Saran Layanan (Opsional)</span>
                        <span class="h-px flex-1 bg-amber-500/20"></span>
                    </label>
                    <div class="space-y-4">
                        
                        <div class="flex gap-2">
                            <div class="w-40">
                                <select x-model="sugService1Category" @change="onCategoryChange('sug', 1)"
                                    class="w-full bg-black border-gray-800 rounded-2xl px-4 py-4 text-sm font-bold text-gray-400 focus:ring-amber-500/50 focus:border-amber-500 transition-all cursor-pointer">
                                    <option value="">-- Kategori --</option>
                                    <template x-for="cat in uniqueCategories" :key="cat">
                                        <option :value="cat" x-text="cat"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="flex-1 relative" @click.away="sugService1Open = false">
                                <div class="flex items-stretch shadow-xl group">
                                    <div class="w-20 flex-shrink-0 bg-black border-y border-l border-gray-800 rounded-l-2xl flex items-center px-4">
                                        <span class="text-[8px] font-black text-amber-500/80 uppercase tracking-wider leading-tight text-center">Opt 1</span>
                                    </div>
                                    <input type="text" x-model="sugService1Search" 
                                        @focus="sugService1Open = true"
                                        @input="updateServiceValue('sug', 1)"
                                        :disabled="!sugService1Category"
                                        placeholder="Cari atau nama jasa..."
                                        class="flex-1 bg-gray-800 border-gray-700 text-white focus:ring-amber-500/50 focus:border-amber-500 font-bold text-sm py-4 px-5 disabled:opacity-30 transition-all">
                                    <input type="number" x-model="sugService1Price"
                                        @input="updateServiceValue('sug', 1)"
                                        placeholder="Harga"
                                        class="w-40 bg-black border-y border-r border-gray-800 text-amber-400 rounded-r-2xl focus:ring-amber-500/50 focus:border-amber-500 font-black text-sm py-4 px-5 text-right transition-all">
                                </div>
                                <div x-show="sugService1Open && sugService1Category" x-transition
                                    class="absolute left-0 right-0 top-full mt-2 bg-black border border-gray-800 rounded-2xl shadow-2xl z-[100] max-h-60 overflow-y-auto overflow-x-hidden border-t-0 p-1">
                                    <template x-for="service in getFilteredServices(sugService1Category, sugService1Search)">
                                        <div @click="selectService('sug', 1, service)" 
                                            class="px-5 py-4 hover:bg-gray-800 rounded-xl cursor-pointer flex justify-between items-center group transition-colors">
                                            <span class="text-sm font-bold text-gray-300 group-hover:text-amber-500" x-text="service.name"></span>
                                            <span class="text-xs font-black text-amber-500/80" x-text="'Rp ' + parseInt(service.price).toLocaleString()"></span>
                                        </div>
                                    </template>
                                    <div x-show="sugService1Search && getFilteredServices(sugService1Category, sugService1Search).length === 0" class="px-5 py-4 text-gray-500 italic text-xs">
                                        ✏️ Layanan Kustom Terdeteksi...
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-2">
                            <div class="w-40">
                                <select x-model="sugService2Category" @change="onCategoryChange('sug', 2)"
                                    class="w-full bg-black border-gray-800 rounded-2xl px-4 py-4 text-sm font-bold text-gray-400 focus:ring-amber-500/50 focus:border-amber-500 transition-all cursor-pointer">
                                    <option value="">-- Kategori --</option>
                                    <template x-for="cat in uniqueCategories" :key="cat">
                                        <option :value="cat" x-text="cat"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="flex-1 relative" @click.away="sugService2Open = false">
                                <div class="flex items-stretch shadow-xl group">
                                    <div class="w-20 flex-shrink-0 bg-black border-y border-l border-gray-800 rounded-l-2xl flex items-center px-4">
                                        <span class="text-[8px] font-black text-amber-500/80 uppercase tracking-wider leading-tight text-center">Opt 2</span>
                                    </div>
                                    <input type="text" x-model="sugService2Search" 
                                        @focus="sugService2Open = true"
                                        @input="updateServiceValue('sug', 2)"
                                        :disabled="!sugService2Category"
                                        placeholder="Cari atau nama jasa..."
                                        class="flex-1 bg-gray-800 border-gray-700 text-white focus:ring-amber-500/50 focus:border-amber-500 font-bold text-sm py-4 px-5 disabled:opacity-30 transition-all">
                                    <input type="number" x-model="sugService2Price"
                                        @input="updateServiceValue('sug', 2)"
                                        placeholder="Harga"
                                        class="w-40 bg-black border-y border-r border-gray-800 text-amber-400 rounded-r-2xl focus:ring-amber-500/50 focus:border-amber-500 font-black text-sm py-4 px-5 text-right transition-all">
                                </div>
                                <div x-show="sugService2Open && sugService2Category" x-transition
                                    class="absolute left-0 right-0 top-full mt-2 bg-black border border-gray-800 rounded-2xl shadow-2xl z-[100] max-h-60 overflow-y-auto overflow-x-hidden border-t-0 p-1">
                                    <template x-for="service in getFilteredServices(sugService2Category, sugService2Search)">
                                        <div @click="selectService('sug', 2, service)" 
                                            class="px-5 py-4 hover:bg-gray-800 rounded-xl cursor-pointer flex justify-between items-center group transition-colors">
                                            <span class="text-sm font-bold text-gray-300 group-hover:text-amber-500" x-text="service.name"></span>
                                            <span class="text-xs font-black text-amber-500/80" x-text="'Rp ' + parseInt(service.price).toLocaleString()"></span>
                                        </div>
                                    </template>
                                    <div x-show="sugService2Search && getFilteredServices(sugService2Category, sugService2Search).length === 0" class="px-5 py-4 text-gray-500 italic text-xs">
                                        ✏️ Layanan Kustom Terdeteksi...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="bg-black/50 px-8 py-8 border-t border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <button @click="showEditModal = false" 
                        class="text-gray-500 hover:text-white font-black italic text-[10px] uppercase tracking-[0.3em] transition-all hover:translate-x-1">
                    ← KEMBALI / BATALKAN
                </button>
                <button @click="updateIssue()" 
                        :disabled="loading" 
                        class="w-full sm:w-auto px-12 py-5 bg-white text-gray-900 font-black italic text-xs uppercase tracking-[0.2em] rounded-[1.5rem] shadow-2xl shadow-white/5 transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-4 group disabled:opacity-50">
                    <template x-if="loading">
                        <svg class="animate-spin h-5 w-5 text-gray-900" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                    <template x-if="!loading">
                        <div class="flex items-center gap-4">
                            <span class="group-hover:rotate-12 transition-transform">⚡</span>
                            <span>SIMPAN PERUBAHAN</span>
                        </div>
                    </template>
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\SistemWorkshop\resources\views\components\edit-issue-modal.blade.php ENDPATH**/ ?>