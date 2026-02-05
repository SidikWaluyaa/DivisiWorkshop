{{-- Modal: Set Follow Up --}}
<div id="followUpModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
    <div class="relative top-20 mx-auto p-0 border-0 w-full max-w-md shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white overflow-hidden">
        <div class="bg-gray-50/80 px-8 py-6 flex justify-between items-center border-b border-gray-100">
            <div>
                <h3 class="text-xl font-black text-gray-900 uppercase tracking-tighter">Schedule Follow Up</h3>
                <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mt-1">Nurturing Retention</p>
            </div>
            <button onclick="closeFollowUpModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-8">
            <form action="{{ route('cs.leads.set-follow-up', $lead->id) }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Next Contact Date & Time *</label>
                    <div class="relative group">
                        <input type="datetime-local" name="next_follow_up_at" required min="{{ date('Y-m-d\TH:i') }}" 
                               class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Strategi & Catatan</label>
                    <textarea name="notes" rows="4" 
                              class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30 placeholder-gray-300" 
                              placeholder="Rencana pembicaraan atau poin penting follow up..."></textarea>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeFollowUpModal()" 
                            class="flex-1 px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-[10px] rounded-2xl transition-all">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-[2] px-8 py-4 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-widest text-[10px] rounded-2xl shadow-xl shadow-yellow-100 transition-all transform hover:-translate-y-1">
                        Save Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
