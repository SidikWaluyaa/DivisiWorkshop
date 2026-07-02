{{-- Modal: Add New Lead --}}
<div x-show="leadModalOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-90"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-90"
     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center"
     style="display: none;">
     
    <div @click.outside="leadModalOpen = false" class="relative mx-auto w-full max-w-lg shadow-2xl rounded-3xl bg-white overflow-hidden m-4"
         x-data="{ 
             isSubmitting: false,
             source: 'WhatsApp',
             channel: 'ONLINE',
             customer_phone: '',
             customer_email: '',
             
             onSourceChange() {
                 this.channel = (this.source === 'Walk-in') ? 'OFFLINE' : 'ONLINE';
             },
             
             sanitizePhone() {
                 let cleaned = this.customer_phone.replace(/[^0-9+]/g, '');
                 if (cleaned.startsWith('+62')) {
                     cleaned = '0' + cleaned.substring(3);
                 } else if (cleaned.startsWith('62') && cleaned.length > 4) {
                     cleaned = '0' + cleaned.substring(2);
                 }
                 this.customer_phone = cleaned.replace(/[^0-9]/g, '');
             },
             
             appendEmail(suffix) {
                 if (!this.customer_email.includes('@')) {
                     this.customer_email = this.customer_email.trim() + suffix;
                 }
             }
         }">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#22AF85] to-[#1a9b74] px-6 py-5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-white uppercase tracking-tight">Lead Baru</h3>
                    <p class="text-[10px] font-bold text-white/70 uppercase tracking-widest">Tambah Data Customer</p>
                </div>
            </div>
            <button @click="leadModalOpen = false" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Form Body --}}
        <form action="{{ route('cs.leads.store') }}" method="POST" 
              @submit="if(isSubmitting) { $event.preventDefault(); return; } isSubmitting = true" 
              class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
            @csrf

            {{-- Section: Identitas Customer --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-5 h-5 rounded-md bg-[#22AF85]/10 flex items-center justify-center">
                        <svg class="w-3 h-3 text-[#22AF85]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Identitas Customer</span>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nama Customer</label>
                    <input type="text" name="customer_name" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] font-bold transition-all" placeholder="Nama lengkap customer">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">No. Telepon <span class="text-red-400">*</span></label>
                        <input type="text" name="customer_phone" required x-model="customer_phone" @input="sanitizePhone()" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] font-bold transition-all" placeholder="08xxx">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Email <span class="text-red-400">*</span></label>
                        <input type="email" name="customer_email" required x-model="customer_email" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] font-bold transition-all" placeholder="nama@email.com">
                    </div>
                </div>

                {{-- Quick Email Suffix Buttons --}}
                <div class="flex gap-1.5 -mt-1">
                    <button type="button" @click="appendEmail('@gmail.com')" class="text-[9px] font-black text-[#22AF85] bg-[#22AF85]/5 border border-[#22AF85]/10 px-2 py-0.5 rounded-md hover:bg-[#22AF85]/10 transition active:scale-95">
                        @gmail.com
                    </button>
                    <button type="button" @click="appendEmail('@yahoo.com')" class="text-[9px] font-black text-gray-400 bg-gray-50 border border-gray-100 px-2 py-0.5 rounded-md hover:bg-gray-100 transition active:scale-95">
                        @yahoo.com
                    </button>
                    <button type="button" @click="appendEmail('@outlook.com')" class="text-[9px] font-black text-gray-400 bg-gray-50 border border-gray-100 px-2 py-0.5 rounded-md hover:bg-gray-100 transition active:scale-95">
                        @outlook.com
                    </button>
                </div>
            </div>

            {{-- Separator --}}
            <div class="border-t border-dashed border-gray-100"></div>

            {{-- Section: Detail Lead --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-5 h-5 rounded-md bg-amber-500/10 flex items-center justify-center">
                        <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Detail Lead</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Sumber Lead <span class="text-red-400">*</span></label>
                        <select name="source" required x-model="source" @change="onSourceChange()" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] font-bold transition-all">
                            <option value="WhatsApp">📱 WhatsApp</option>
                            <option value="Instagram">📸 Instagram</option>
                            <option value="Website">🌐 Website</option>
                            <option value="Referral">🤝 Referral</option>
                            <option value="Walk-in">🚶 Walk-in</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tipe Lead <span class="text-red-400">*</span></label>
                        <select name="channel" required x-model="channel" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] font-bold transition-all">
                            <option value="ONLINE">🟢 Online</option>
                            <option value="OFFLINE">🟠 Offline</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Prioritas <span class="text-red-400">*</span></label>
                    <div class="grid grid-cols-3 gap-2" x-data="{ priority: 'WARM' }">
                        <input type="hidden" name="priority" :value="priority">
                        <button type="button" @click="priority = 'HOT'" 
                                :class="priority === 'HOT' ? 'bg-red-50 border-red-300 text-red-600 ring-2 ring-red-200' : 'bg-gray-50 border-gray-100 text-gray-500 hover:bg-red-50/50'"
                                class="py-2.5 rounded-xl border text-xs font-black uppercase tracking-wider transition-all active:scale-95 flex items-center justify-center gap-1">
                            🔥 Hot
                        </button>
                        <button type="button" @click="priority = 'WARM'" 
                                :class="priority === 'WARM' ? 'bg-amber-50 border-amber-300 text-amber-600 ring-2 ring-amber-200' : 'bg-gray-50 border-gray-100 text-gray-500 hover:bg-amber-50/50'"
                                class="py-2.5 rounded-xl border text-xs font-black uppercase tracking-wider transition-all active:scale-95 flex items-center justify-center gap-1">
                            ☀️ Warm
                        </button>
                        <button type="button" @click="priority = 'COLD'" 
                                :class="priority === 'COLD' ? 'bg-blue-50 border-blue-300 text-blue-600 ring-2 ring-blue-200' : 'bg-gray-50 border-gray-100 text-gray-500 hover:bg-blue-50/50'"
                                class="py-2.5 rounded-xl border text-xs font-black uppercase tracking-wider transition-all active:scale-95 flex items-center justify-center gap-1">
                            ❄️ Cold
                        </button>
                    </div>
                </div>
            </div>

            {{-- Separator --}}
            <div class="border-t border-dashed border-gray-100"></div>

            {{-- Section: Catatan --}}
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Catatan Awal</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-[#22AF85] focus:border-[#22AF85] font-bold transition-all resize-none" placeholder="Catatan awal tentang kebutuhan customer..."></textarea>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-2 flex gap-3">
                <button type="button" @click="leadModalOpen = false" class="flex-1 py-3.5 text-xs font-black uppercase tracking-widest text-gray-400 hover:bg-gray-50 rounded-2xl transition border border-gray-100 active:scale-[0.98]">
                    Batal
                </button>
                <button type="submit" :disabled="isSubmitting" 
                        :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-2xl hover:scale-[1.02]'" 
                        class="flex-1 py-3.5 bg-gradient-to-r from-[#22AF85] to-[#1a9b74] text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl transition transform flex items-center justify-center gap-2 active:scale-[0.98]">
                    <template x-if="isSubmitting">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="isSubmitting ? 'Menyimpan...' : '✨ Simpan Lead'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
