<div class="relative bg-white rounded-3xl p-8 shadow-2xl shadow-gray-200/50 overflow-hidden border border-[#22AF85]/20">
    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-multiply"></div>
    <!-- Background Decor -->
    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-[#22AF85] rounded-full blur-3xl opacity-10 animate-pulse"></div>
    <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-[#FFC232] rounded-full blur-3xl opacity-10 animate-pulse" style="animation-delay: 1s;"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-1 bg-[#22AF85]/10 text-[#22AF85] rounded-full text-xs font-bold uppercase tracking-wider backdrop-blur-md border border-[#22AF85]/20">
                    {{ Auth::user()->role === 'owner' ? 'Owner Dashboard' : 'Workshop Admin' }}
                </span>
                <span class="w-2 h-2 rounded-full bg-[#22AF85] animate-pulse"></span>
                <span class="text-xs text-gray-500 font-bold uppercase">System Online</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 tracking-tight mb-2 drop-shadow-sm">
                Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋
            </h1>
            <p class="text-gray-500 text-lg font-medium max-w-xl">
                Selamat datang kembali di pusat kontrol operasional workshop Anda.
            </p>
        </div>
        <div class="text-right bg-white/50 p-4 rounded-2xl backdrop-blur-md border border-gray-100 shadow-sm">
            <div class="text-4xl font-bold text-[#22AF85] font-mono tracking-tighter">
                {{ \Carbon\Carbon::now()->format('H:i') }}
            </div>
            <div class="text-gray-400 font-bold uppercase tracking-widest text-xs mt-1">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
        </div>
    </div>
</div>
