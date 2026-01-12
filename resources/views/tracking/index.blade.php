<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Status Sepatu - Workshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f3f4f6; /* Gray-100 */
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4 min-h-screen">
    <div class="w-full max-w-lg">
        <!-- Logo/Header -->
        <div class="text-center mb-10">
            <div class="inline-block p-2 rounded-2xl mb-4 transform hover:scale-105 transition-transform duration-300">
                <img src="{{ asset('images/logo.png') }}" alt="Shoe Workshop Logo" class="h-24 mx-auto drop-shadow-lg">
            </div>
            <h1 class="text-4xl font-black text-gray-800 tracking-tight">Lacak Status <span class="text-teal-600">Sepatu</span></h1>
            <p class="text-gray-500 mt-2 text-lg">Masukkan nomor SPK untuk melihat progress.</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 border-t-8 border-teal-500 relative overflow-hidden">
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg flex items-center gap-3 animate-pulse">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="font-bold">{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('tracking.track') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="group">
                    <label for="spk_number" class="block text-sm font-bold text-gray-500 mb-2 uppercase tracking-wider group-focus-within:text-teal-600 transition-colors">
                        Nomor SPK / Nomor WhatsApp
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            #
                        </span>
                        <input 
                            type="text" 
                            id="spk_number" 
                            name="spk_number" 
                            required
                            placeholder="Contoh: SPK-XXX atau 08123456789"
                            class="w-full pl-10 pr-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-100 focus:border-teal-500 text-lg font-mono font-bold text-gray-800 transition-all duration-300 placeholder-gray-400"
                            value="{{ old('spk_number') }}"
                        >
                    </div>
                    <p class="mt-2 text-xs text-gray-400 italic">
                        *Masukkan Nomor SPK atau Nomor WA yang terdaftar.
                    </p>
                </div>

                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-black py-4 px-6 rounded-xl hover:from-orange-600 hover:to-orange-700 transform hover:-translate-y-1 hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-3 text-lg"
                >
                    <span>CARI STATUS</span>
                    <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-center">
                <a href="{{ route('login') }}" class="group flex items-center gap-2 text-sm text-gray-400 hover:text-teal-600 transition-colors px-4 py-2 rounded-lg hover:bg-teal-50">
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-teal-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span class="font-medium">Login Staff</span>
                </a>
            </div>
        </div>

        <!-- Info Footer -->
        <div class="mt-8 text-center text-gray-400 text-xs font-medium tracking-wide">
            <p>&copy; 2026 SHOE WORKSHOP.</p>
        </div>
    </div>
</body>
</html>
