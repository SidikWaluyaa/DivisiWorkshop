<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Status Sepatu - Workshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">🔍 Lacak Sepatu</h1>
            <p class="text-white/80">Cek status perbaikan sepatu Anda</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('tracking.track') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="spk_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor SPK
                    </label>
                    <input 
                        type="text" 
                        id="spk_number" 
                        name="spk_number" 
                        required
                        placeholder="Contoh: SPK-20260108-001"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-lg font-mono"
                        value="{{ old('spk_number') }}"
                    >
                    <p class="mt-2 text-sm text-gray-500">
                        Masukkan nomor SPK yang tertera di struk Anda
                    </p>
                </div>

                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold py-4 px-6 rounded-lg hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 shadow-lg"
                >
                    🔎 Lacak Sekarang
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600 text-center">
                    Punya akun staff? 
                    <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-800 font-semibold">
                        Login di sini
                    </a>
                </p>
            </div>
        </div>

        <!-- Info Footer -->
        <div class="mt-6 text-center text-white/70 text-sm">
            <p>© 2026 Workshop Sepatu. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
