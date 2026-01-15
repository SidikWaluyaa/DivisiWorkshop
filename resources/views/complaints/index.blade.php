<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Layanan Keluhan - Solv Shoe Workshop</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-[Outfit] antialiased h-full text-slate-900">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                 <!-- Logo or Brand -->
                 <div class="h-12 w-12 bg-gradient-to-br from-teal-500 to-orange-500 rounded-xl shadow-lg flex items-center justify-center text-white font-bold text-xl">
                    S
                 </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-slate-900">
                Pusat Bantuan & Keluhan
            </h2>
            <p class="mt-2 text-center text-sm text-slate-600">
                Sampaikan kendala Anda, kami siap membantu.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-[600px]">
            <div class="bg-white py-8 px-4 shadow-xl shadow-slate-200/50 sm:rounded-2xl sm:px-10 border border-slate-100">
                
                @if ($errors->any())
                    <div class="mb-6 rounded-lg bg-red-50 p-4 border border-red-100">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan input</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul role="list" class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form class="space-y-6" action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <!-- SPK Number -->
                        <div>
                            <label for="spk_number" class="block text-sm font-medium text-slate-700">Nomor SPK / Order ID</label>
                            <div class="mt-1">
                                <input id="spk_number" name="spk_number" type="text" autocomplete="off" required 
                                    class="block w-full appearance-none rounded-lg border border-slate-300 px-3 py-2 placeholder-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-teal-500 sm:text-sm transition-all duration-200"
                                    placeholder="Contoh: SPK-2023001"
                                    value="{{ old('spk_number') }}">
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-slate-700">Nomor WhatsApp</label>
                            <div class="mt-1">
                                <input id="customer_phone" name="customer_phone" type="tel" autocomplete="off" required 
                                    class="block w-full appearance-none rounded-lg border border-slate-300 px-3 py-2 placeholder-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-teal-500 sm:text-sm transition-all duration-200"
                                    placeholder="Nomor saat order"
                                    value="{{ old('customer_phone') }}">
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Gunakan nomor yang terdaftar saat order untuk verifikasi.</p>
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-700">Kategori Keluhan</label>
                        <div class="mt-1">
                            <select id="category" name="category" required 
                                class="block w-full rounded-lg border border-slate-300 py-2 pl-3 pr-10 text-base focus:border-teal-500 focus:outline-none focus:ring-teal-500 sm:text-sm transition-all duration-200">
                                <option value="" disabled selected>Pilih Kategori Masalah</option>
                                <option value="QUALITY" {{ old('category') == 'QUALITY' ? 'selected' : '' }}>Kualitas Pengerjaan (Kurang Bersih/Rapi/Kuat)</option>
                                <option value="DAMAGE" {{ old('category') == 'DAMAGE' ? 'selected' : '' }}>Kerusakan Barang (Sepatu Rusak/Luntur/Hilang)</option>
                                <option value="LATE" {{ old('category') == 'LATE' ? 'selected' : '' }}>Keterlambatan (Lewat Estimasi)</option>
                                <option value="SERVICE" {{ old('category') == 'SERVICE' ? 'selected' : '' }}>Pelayanan (Admin/Teknisi)</option>
                                <option value="OTHER" {{ old('category') == 'OTHER' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700">Detail Keluhan</label>
                        <div class="mt-1">
                            <textarea id="description" name="description" rows="4" required 
                                class="block w-full appearance-none rounded-lg border border-slate-300 px-3 py-2 placeholder-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-teal-500 sm:text-sm transition-all duration-200"
                                placeholder="Jelaskan masalah secara detail agar kami bisa segera membantu...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Photos -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Foto Bukti (Opsional)</label>
                        <div class="mt-1 flex justify-center rounded-lg border-2 border-dashed border-slate-300 px-6 pt-5 pb-6 hover:border-teal-500 transition-colors duration-200 bg-slate-50">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-slate-600 justify-center">
                                    <label for="photos" class="relative cursor-pointer rounded-md bg-white font-medium text-teal-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-teal-500 focus-within:ring-offset-2 hover:text-teal-500">
                                        <span>Upload file</span>
                                        <input id="photos" name="photos[]" type="file" class="sr-only" multiple accept="image/*">
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-slate-500">PNG, JPG, GIF up to 2MB (Max 3 files)</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-lg border border-transparent bg-gradient-to-r from-teal-600 to-teal-500 py-3 px-4 text-sm font-bold text-white shadow-sm hover:from-teal-700 hover:to-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02]">
                            Kirim Keluhan
                        </button>
                    </div>
                </form>
            </div>
            
            <p class="mt-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} Solv Shoe Workshop. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
