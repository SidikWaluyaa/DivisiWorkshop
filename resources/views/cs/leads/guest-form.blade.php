<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Data Customer - Workshop</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center p-6">
        <div class="w-full max-w-lg bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-teal-600 to-teal-500 px-6 py-8 text-center">
                <h2 class="text-2xl font-bold text-white mb-2">Lengkapi Data Diri</h2>
                <p class="text-teal-100 text-sm">Mohon isi data berikut untuk melanjutkan proses konsultasi.</p>
            </div>

            {{-- Form --}}
            <div class="p-8">
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 flex items-center gap-3">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <span class="font-bold block">Berhasil Disimpan!</span>
                            <span class="text-sm">Data Anda telah kami terima. Silakan konfirmasi ke Admin.</span>
                        </div>
                    </div>
                @else

                <form action="{{ route('cs.guest.update', $lead->id) }}?signature={{ request()->query('signature') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    {{-- Read Only Info --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nomor WhatsApp</label>
                        <input type="text" value="{{ $lead->customer_phone }}" class="w-full bg-gray-100 text-gray-500 border-gray-300 rounded-lg cursor-not-allowed" readonly>
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $lead->customer_name) }}" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" placeholder="Nama Anda" required>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email <span class="text-gray-400 font-normal">(Opsional)</span></label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', $lead->customer_email) }}" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" placeholder="email@contoh.com">
                    </div>

                    {{-- Kota & Provinsi (Simple Text for now, or dropdown if requested) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kota/Kabupaten</label>
                            <input type="text" name="customer_city" value="{{ old('customer_city', $lead->customer_city) }}" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" placeholder="Contoh: Jakarta Selatan" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Provinsi</label>
                            <input type="text" name="customer_province" value="{{ old('customer_province', $lead->customer_province) }}" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" placeholder="Contoh: DKI Jakarta" required>
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="customer_address" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" placeholder="Jalan, Blok, Nomor Rumah, RT/RW..." required>{{ old('customer_address', $lead->customer_address) }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 rounded-lg shadow-lg transform active:scale-95 transition-all">
                        Simpan Data Saya
                    </button>
                </form>

                @endif
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 border-t p-4 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Workshop Shoes & Care
            </div>
        </div>
    </div>
</body>
</html>
