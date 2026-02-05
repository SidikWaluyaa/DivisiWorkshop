{{-- Modal: Edit Profil (Governed Edit) --}}
<div id="editModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
    <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white mb-20 overflow-hidden">
        {{-- Modal Header --}}
        <div class="bg-gray-50/80 px-10 py-8 flex justify-between items-center border-b border-gray-100">
            <div>
                <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Profil Customer</h3>
                <div class="flex items-center gap-2 mt-2">
                    <div class="w-12 h-1.5 bg-[#22AF85] rounded-full"></div>
                    <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Governed Revision System</p>
                </div>
            </div>
            <button onclick="closeEditModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 hover:border-gray-900 transition-all duration-300 group">
                <svg class="w-6 h-6 group-rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-10">
            @if(in_array($lead->status, ['CONVERTED', 'LOST']))
                <div class="mb-10 p-6 bg-red-50 border-2 border-red-100 rounded-[2rem] flex items-start gap-5">
                    <div class="w-12 h-12 flex-shrink-0 bg-red-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-red-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div>
                        <p class="text-red-900 font-black text-sm uppercase tracking-widest mb-1.5 text-red-500">Data Terkunci (Locked)</p>
                        <p class="text-gray-600 text-xs font-bold leading-relaxed">
                            Lead ini sudah berada di tahap <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-lg">{{ $lead->status }}</span>. Log audit akan mencatat setiap perubahan secara mendalam.
                        </p>
                    </div>
                </div>
            @endif

            <form action="{{ route('cs.leads.update', $lead->id) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Nama Lengkap *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', $lead->customer_name) }}" required 
                                   class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">No. WhatsApp *</label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone', $lead->customer_phone) }}" required 
                                   class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Email</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email', $lead->customer_email) }}" 
                                   class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Sumber Lead *</label>
                            <select name="source" required class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                                <option value="WhatsApp" {{ $lead->source == 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="Instagram" {{ $lead->source == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                                <option value="Website" {{ $lead->source == 'Website' ? 'selected' : '' }}>Website</option>
                                <option value="Referral" {{ $lead->source == 'Referral' ? 'selected' : '' }}>Referral</option>
                                <option value="Walk-in" {{ $lead->source == 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Prioritas *</label>
                            <select name="priority" required class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#FFC232] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                                <option value="HOT" {{ $lead->priority == 'HOT' ? 'selected' : '' }}>🔥 HOT</option>
                                <option value="WARM" {{ $lead->priority == 'WARM' ? 'selected' : '' }}>☀️ WARM</option>
                                <option value="COLD" {{ $lead->priority == 'COLD' ? 'selected' : '' }}>❄️ COLD</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Alamat Lengkap</label>
                        <textarea name="customer_address" rows="1" class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">{{ old('customer_address', $lead->customer_address) }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Kota</label>
                            <input type="text" name="customer_city" value="{{ old('customer_city', $lead->customer_city) }}" 
                                   class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Provinsi</label>
                            <input type="text" name="customer_province" value="{{ old('customer_province', $lead->customer_province) }}" 
                                   class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-100">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Alasan Revisi / Perubahan *</label>
                    <textarea name="revision_reason" rows="3" {{ in_array($lead->status, ['CONVERTED', 'LOST']) ? 'required' : '' }}
                              class="w-full px-6 py-4 rounded-3xl border-2 border-gray-100 focus:border-red-400 focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-red-50/10 placeholder-red-200"
                              placeholder="Jelaskan alasan perubahan data untuk audit trail..."></textarea>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeEditModal()" class="flex-1 px-8 py-5 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-xs rounded-3xl transition-all duration-300">Tutup</button>
                    <button type="submit" class="flex-[2] px-8 py-5 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-widest text-xs rounded-3xl shadow-xl shadow-yellow-100 transition-all duration-300 transform hover:-translate-y-1">Simpan Revisi Profil</button>
                </div>
            </form>
        </div>
    </div>
</div>
