<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-green-100 mb-6 animate-bounce">
            <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
        </div>

        <h3 class="text-3xl font-black text-gray-800 mb-2">
            Keluhan Diterima!
        </h3>
        
        <div class="inline-flex items-center rounded-full bg-gray-100 px-4 py-1.5 text-sm font-bold text-gray-600 mb-8 border border-gray-200">
            Ticket #{{ $complaint->id }}
        </div>

        <p class="text-gray-500 mb-8 leading-relaxed">
            Terima kasih telah memberitahu kami. Tim Admin akan segera memeriksa laporan Anda dan menghubungi nomor WhatsApp 
            <span class="font-bold text-teal-600 bg-teal-50 px-1 rounded">{{ $complaint->customer_phone }}</span> 
            dalam waktu maksimal 1x24 jam.
        </p>

        <a href="{{ route('tracking.index') }}" class="w-full bg-gradient-to-r from-teal-500 to-teal-600 text-white font-black py-4 px-6 rounded-xl hover:from-teal-600 hover:to-teal-700 transform hover:-translate-y-1 hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Halaman Utama
        </a>
    </div>
</x-guest-layout>
