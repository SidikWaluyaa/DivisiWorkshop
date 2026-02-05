{{-- Modal: Log Activity --}}
<div id="activityModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
    <div class="relative top-20 mx-auto p-0 border-0 w-full max-w-xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white mb-20 overflow-hidden">
        {{-- Modal Header --}}
        <div class="bg-gray-50/80 px-10 py-8 flex justify-between items-center border-b border-gray-100">
            <div>
                <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Log Aktivitas</h3>
                <div class="flex items-center gap-2 mt-2">
                    <div class="w-12 h-1.5 bg-[#22AF85] rounded-full"></div>
                    <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Lead Engagement Tracking</p>
                </div>
            </div>
            <button onclick="closeActivityModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 hover:border-gray-900 transition-all duration-300 group">
                <svg class="w-6 h-6 group-rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-10">
            <form action="{{ route('cs.activities.store', $lead->id) }}" method="POST" class="space-y-8">
                @csrf
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Tipe Interaksi *</label>
                        <select name="type" required class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                            <option value="CHAT">💬 Chat</option>
                            <option value="CALL">📞 Telepon</option>
                            <option value="EMAIL">📧 Email</option>
                            <option value="MEETING">🤝 Meeting</option>
                            <option value="NOTE">📝 Catatan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Channel / Media</label>
                        <input type="text" name="channel" placeholder="WhatsApp, IG, dll"
                               class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Isi Komunikasi *</label>
                    <textarea name="content" required rows="5" 
                              class="w-full px-6 py-4 rounded-3xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30 placeholder-gray-300"
                              placeholder="Detail percakapan atau perkembangan terbaru..."></textarea>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeActivityModal()" class="flex-1 px-8 py-5 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-xs rounded-3xl transition-all duration-300">Batal</button>
                    <button type="submit" class="flex-[2] px-8 py-5 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-widest text-xs rounded-3xl shadow-xl shadow-yellow-100 transition-all duration-300 transform hover:-translate-y-1">Simpan Aktivitas</button>
                </div>
            </form>
        </div>
    </div>
</div>
