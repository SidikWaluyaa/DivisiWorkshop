<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Status & Keluhan - Workshop</title>
    
    <!-- Load CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Load Local QR Scanner Library -->
    <script src="{{ asset('js/vendor/html5-qrcode.min.js') }}"></script>

    <style>
        body {
            background-color: #f3f4f6; /* Gray-100 */
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 20px 20px;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="flex items-center justify-center p-4 min-h-screen">
    <div class="w-full max-w-lg" x-data="{ tab: '{{ $errors->any() ? 'complaint' : 'tracking' }}' }">
        <!-- Logo/Header -->
        <div class="text-center mb-6">
            <div class="inline-block p-2 rounded-2xl mb-4 transform hover:scale-105 transition-transform duration-300">
                <img src="{{ asset('images/logo.png') }}" alt="Shoe Workshop Logo" class="h-20 mx-auto drop-shadow-lg">
            </div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight" x-show="tab === 'tracking'">Lacak Status <span class="text-teal-600">Sepatu</span></h1>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight" x-show="tab === 'complaint'" x-cloak>Layanan <span class="text-rose-600">Keluhan</span></h1>
            <p class="text-gray-500 mt-2 text-sm" x-show="tab === 'tracking'">Masukkan nomor SPK untuk melihat progress.</p>
            <p class="text-gray-500 mt-2 text-sm" x-show="tab === 'complaint'" x-cloak>Sampaikan kendala pesanan Anda di sini.</p>
        </div>

        <!-- Tab Navigation -->
        <div class="flex p-1 bg-gray-200 rounded-xl mb-6 shadow-inner mx-8">
            <button 
                @click="tab = 'tracking'"
                :class="tab === 'tracking' ? 'bg-white text-teal-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 rounded-lg font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Cari Pesanan
            </button>
            <button 
                @click="tab = 'complaint'"
                :class="tab === 'complaint' ? 'bg-white text-rose-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 rounded-lg font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Buat Keluhan
            </button>
        </div>

        <!-- Content Area -->
        <div class="relative">
            <!-- TAB 1: Tracking Form -->
            <div x-show="tab === 'tracking'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="bg-white rounded-3xl shadow-xl p-8 border-t-8 border-teal-500 relative overflow-hidden">
                    
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
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">#</span>
                                <input type="text" id="spk_number" name="spk_number" required placeholder="Contoh: SPK-XXX atau 081234..."
                                    class="w-full pl-10 pr-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-100 focus:border-teal-500 text-lg font-mono font-bold text-gray-800 transition-all duration-300 placeholder-gray-400"
                                    value="{{ old('spk_number') }}">
                            </div>
                        </div>
        
                        <!-- QR Scanner Section -->
                        <div id="scanner-container" class="hidden mb-4 overflow-hidden rounded-xl border-2 border-teal-500 shadow-inner bg-black">
                            <div id="reader" class="w-full"></div>
                            <button type="button" onclick="stopScanner()" class="w-full py-2 bg-red-600 text-white font-bold text-sm hover:bg-red-700 transition-colors">TUTUP KAMERA</button>
                        </div>
        
                        <!-- Scan Button -->
                        <button type="button" onclick="startScanner()" class="w-full py-3 bg-teal-50 text-teal-700 font-bold rounded-xl border border-teal-200 hover:bg-teal-100 hover:text-teal-800 transition-all duration-300 flex items-center justify-center gap-2 mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            <span>SCAN QR CODE SPK</span>
                        </button>
        
                        <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-black py-4 px-6 rounded-xl hover:from-orange-600 hover:to-orange-700 transform hover:-translate-y-1 hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-3 text-lg">
                            <span>CARI STATUS</span>
                            <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- TAB 2: Complaint Form -->
            <div x-show="tab === 'complaint'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="bg-white rounded-3xl shadow-xl p-8 border-t-8 border-rose-500 relative overflow-hidden">
                    
                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-rose-50 p-4 border border-rose-100">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-rose-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-rose-800">Terdapat kesalahan input</h3>
                                    <ul class="mt-2 text-sm text-rose-700 list-disc pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form class="space-y-6" action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- SPK Number -->
                            <div>
                                <label for="c_spk_number" class="block text-sm font-bold text-gray-500 mb-1 uppercase tracking-wider">Nomor SPK / Order ID</label>
                                <input id="c_spk_number" name="spk_number" type="text" autocomplete="off" required 
                                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-rose-100 focus:border-rose-500 text-gray-800 transition-all placeholder-gray-400"
                                    placeholder="Contoh: SPK-2023001"
                                    value="{{ old('spk_number') }}">
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label for="customer_phone" class="block text-sm font-bold text-gray-500 mb-1 uppercase tracking-wider">Nomor WhatsApp</label>
                                <input id="customer_phone" name="customer_phone" type="tel" autocomplete="off" required 
                                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-rose-100 focus:border-rose-500 text-gray-800 transition-all placeholder-gray-400"
                                    placeholder="Nomor saat order"
                                    value="{{ old('customer_phone') }}">
                            </div>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-bold text-gray-500 mb-1 uppercase tracking-wider">Kategori Masalah</label>
                            <select id="category" name="category" required 
                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-rose-100 focus:border-rose-500 text-gray-800 transition-all">
                                <option value="" disabled selected>Pilih Kategori...</option>
                                <option value="QUALITY" {{ old('category') == 'QUALITY' ? 'selected' : '' }}>Kualitas Pengerjaan (Kurang Bersih/Rapi/Kuat)</option>
                                <option value="DAMAGE" {{ old('category') == 'DAMAGE' ? 'selected' : '' }}>Kerusakan Barang (Sepatu Rusak/Luntur/Hilang)</option>
                                <option value="LATE" {{ old('category') == 'LATE' ? 'selected' : '' }}>Keterlambatan (Lewat Estimasi)</option>
                                <option value="SERVICE" {{ old('category') == 'SERVICE' ? 'selected' : '' }}>Pelayanan (Admin/Teknisi)</option>
                                <option value="OTHER" {{ old('category') == 'OTHER' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-bold text-gray-500 mb-1 uppercase tracking-wider">Detail Keluhan</label>
                            <textarea id="description" name="description" rows="3" required 
                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-rose-100 focus:border-rose-500 text-gray-800 transition-all placeholder-gray-400"
                                placeholder="Jelaskan masalahnya secara detail...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Photos -->
                        <div>
                            <label class="block text-sm font-bold text-gray-500 mb-1 uppercase tracking-wider">Foto Bukti (Opsional)</label>
                            <div class="flex justify-center rounded-xl border-2 border-dashed border-gray-300 px-6 py-4 hover:border-rose-400 hover:bg-rose-50 transition-all cursor-pointer bg-white" onclick="document.getElementById('photos').click()">
                                <div class="text-center">
                                    <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p id="photo-label" class="mt-1 text-xs text-gray-500">Klik untuk upload foto (Max 3)</p>
                                    <input id="photos" name="photos[]" type="file" class="hidden" multiple accept="image/*">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-rose-500 to-rose-600 text-white font-black py-4 px-6 rounded-xl hover:from-rose-600 hover:to-rose-700 transform hover:-translate-y-1 hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-3 text-lg">
                            <span>KIRIM KELUHAN</span>
                            <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
                <script>
                    let html5QrcodeScanner = null;

                    function startScanner() {
                        const scannerContainer = document.getElementById('scanner-container');
                        scannerContainer.classList.remove('hidden');

                        if (html5QrcodeScanner) { return; }

                        html5QrcodeScanner = new Html5Qrcode("reader");
                        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                        
                        html5QrcodeScanner.start({ facingMode: "environment" }, config, onScanSuccess)
                        .catch(err => {
                            console.error("Error starting scanner", err);
                            alert("Kamera tidak dapat diakses.");
                            stopScanner();
                        });
                    }

                    function stopScanner() {
                        const scannerContainer = document.getElementById('scanner-container');
                        scannerContainer.classList.add('hidden');

                        if (html5QrcodeScanner) {
                            html5QrcodeScanner.stop().then(() => {
                                html5QrcodeScanner.clear();
                                html5QrcodeScanner = null;
                            }).catch(err => console.error(err));
                        }
                    }

                    function onScanSuccess(decodedText, decodedResult) {
                        document.getElementById('spk_number').value = decodedText;
                        stopScanner();
                    }

                    // Photo Selection Feedback
                    document.getElementById('photos').addEventListener('change', function(e) {
                        const count = e.target.files.length;
                        const label = document.getElementById('photo-label');
                        if (count > 0) {
                            label.innerHTML = `<span class="text-teal-600 font-bold">${count} Foto Dipilih</span>`;
                        } else {
                            label.innerText = "Klik untuk upload foto (Max 3)";
                        }
                    });
                </script>
        
        <!-- Info Footer -->
        <div class="mt-8 text-center text-gray-400 text-xs font-medium tracking-wide">
            <p>&copy; 2026 SHOE WORKSHOP.</p>
        </div>
    </div>
</body>
</html>
