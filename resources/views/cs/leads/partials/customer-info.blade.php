{{-- Customer Information --}}
<div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100">
    <div class="p-8 pb-4 flex justify-between items-center bg-gray-50/50">
        <div>
            <h3 class="font-black text-gray-900 uppercase tracking-tighter text-2xl">Profil Customer</h3>
            <div class="w-16 h-2 bg-[#22AF85] rounded-full mt-2.5"></div>
        </div>
        <button onclick="openEditModal()" class="bg-white hover:bg-[#22AF85] hover:text-white text-[#22AF85] border-2 border-[#22AF85]/10 p-3 rounded-2xl transition shadow-sm group" title="Edit Profil">
            <svg class="w-6 h-6 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
        </button>
    </div>
    <div class="p-8 space-y-5">
        <div class="group">
            <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Nama Lengkap</label>
            <p class="text-gray-900 font-bold text-lg leading-tight">{{ $lead->customer_name ?? '-' }}</p>
        </div>
        <div class="group">
            <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Kontak Telepon</label>
            <div class="flex items-center gap-3">
                <p class="text-gray-900 font-bold text-lg leading-tight">{{ $lead->customer_phone }}</p>
                <a href="{{ $lead->wa_greeting_link }}" target="_blank" class="text-[#22AF85] hover:bg-[#22AF85] hover:text-white font-black text-[10px] uppercase tracking-widest flex items-center gap-2 border-2 border-[#22AF85]/20 bg-[#22AF85]/5 px-4 py-1.5 rounded-xl transition">
                    WhatsApp
                </a>
            </div>
        </div>
        <div class="group">
            <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Email</label>
            <p class="text-gray-900 font-semibold">{{ $lead->customer_email ?? '-' }}</p>
        </div>
        <div class="group">
            <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Alamat Pengiriman</label>
            <p class="text-gray-900 font-semibold leading-relaxed">{{ $lead->customer_address ?? '-' }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="group">
                <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Kota</label>
                <p class="text-gray-900 font-bold">{{ $lead->customer_city ?? '-' }}</p>
            </div>
            <div class="group">
                <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Provinsi</label>
                <p class="text-gray-900 font-bold">{{ $lead->customer_province ?? '-' }}</p>
            </div>
        </div>
        <hr class="border-gray-100 mt-6 mb-6">
        <div class="grid grid-cols-2 gap-4">
            <div class="group">
                <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Sumber Lead</label>
                <p class="text-gray-900 font-bold">📱 {{ $lead->source }}</p>
            </div>
            <div class="group">
                <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Tipe Channel</label>
                <span class="inline-block px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $lead->channel === 'ONLINE' ? 'bg-[#22AF85]/10 text-[#22AF85]' : 'bg-[#FFC232]/10 text-[#FFC232]' }}">
                    {{ $lead->channel }}
                </span>
            </div>
        </div>
    </div>
</div>
