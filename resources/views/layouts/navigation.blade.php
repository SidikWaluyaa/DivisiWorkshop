<nav x-data="{ open: false }" class="header-gradient">
    <!-- Primary Navigation Menu -->
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- Mobile Sidebar Hamburger (only on mobile) --}}
                <div class="flex items-center lg:hidden mr-2" x-data>
                    <button @click="$dispatch('toggle-mobile-menu')" 
                            class="p-2 rounded-lg text-white hover:bg-white/10 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Mobile Logo (shown only on small screens) -->
                <div class="shrink-0 flex items-center lg:hidden">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-12 w-auto" />
                    </a>
                </div>

                <!-- Page Heading (Desktop) -->
                @isset($header)
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <div class="header-text">
                        {{ $header }}
                    </div>
                </div>
                @endisset
            </div>

            <!-- Global Command/Search Bar -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 flex-1 justify-end max-w-xl">
                <form id="navbar-tracking-form" action="{{ route('tracking.track') }}" method="POST" class="w-full relative transform translate-y-1">
                    @csrf
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-teal-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input id="global-search" 
                               type="text" 
                               name="spk_number" 
                               class="block w-full pl-10 pr-12 py-2 border-none rounded-lg leading-5 bg-black/10 text-white placeholder-teal-100/70 focus:outline-none focus:bg-white/20 focus:text-white focus:placeholder-white/50 focus:ring-0 sm:text-sm shadow-inner transition-all duration-300" 
                               placeholder="Scan Barcode / Type SPK..." 
                               autocomplete="off">
                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center space-x-1">
                            <button type="button" onclick="startScanner()" class="p-1 text-teal-100 hover:text-white transition-colors rounded-full hover:bg-white/10" title="Scan with Camera">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                            <span class="text-teal-100 text-xs border border-teal-100/50 rounded px-1.5 py-0.5 pointer-events-none" title="Press '/' to focus">/</span>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Scanner Modal -->
            <div id="scanner-modal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="stopScanner()"></div>

                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4 text-center">Scan Barcode Sepatu</h3>
                            
                            <div class="rounded-lg overflow-hidden bg-black relative">
                                <div id="reader" style="width: 100%; height: 350px;"></div>
                                <!-- Overlay Guide -->
                                <div class="absolute inset-0 border-2 border-teal-500 opacity-50 pointer-events-none flex items-center justify-center">
                                    <div class="w-64 h-32 border-2 border-white/50 rounded-lg"></div>
                                </div>
                            </div>
                            
                            <div class="mt-4 text-center text-sm text-gray-500">
                                Arahkan kamera ke Barcode SPK
                            </div>
                            
                            <!-- Scan from File Option -->
                            <div class="mt-4 border-t pt-4 text-center">
                                <p class="text-xs text-gray-500 mb-2">Atau scan dari file gambar</p>
                                <input type="file" id="qr-input-file" accept="image/*" class="hidden" onchange="scanFromFile(this)">
                                <button type="button" onclick="document.getElementById('qr-input-file').click()" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Upload Gambar
                                </button>
                            </div>

                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="stopScanner()">Batal</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Modal -->
            <div id="result-modal" class="fixed inset-0 z-[110] hidden" role="dialog" aria-modal="true">
                <div class="absolute inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeResultModal()"></div>
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-2xl">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="text-xl font-bold leading-6 text-gray-900 mb-4 flex items-center">
                                <span class="bg-teal-100 text-teal-800 p-2 rounded-full mr-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                </span>
                                Hasil Pencarian
                            </h3>
                            <div id="result-content" class="space-y-4">
                                <!-- Dynamic Content -->
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" class="inline-flex w-full justify-center rounded-md bg-teal-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-500 sm:ml-3 sm:w-auto" onclick="closeResultModal()">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                let html5QrcodeScanner = null;

                function startScanner() {
                    const modal = document.getElementById('scanner-modal');
                    modal.classList.remove('hidden');
                    
                    if (html5QrcodeScanner === null) {
                        html5QrcodeScanner = new Html5Qrcode("reader");
                    }

                    const config = { 
                        fps: 15, 
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    };
                    
                    html5QrcodeScanner.start({ facingMode: "environment" }, config, onScanSuccess)
                    .catch(err => {
                        console.error("Scanner Error:", err);
                        let msg = "Gagal membuka kamera.";
                        
                        // Check for common SSL issue
                        if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                             msg += "\n\n⚠️ Browser memblokir kamera di website Non-HTTPS (Tidak Aman). Silahkan gunakan HTTPS atau Scan dari File Gambar.";
                        } else if (err.name === 'NotAllowedError' || err.message?.includes('Permission denied')) {
                            msg = "Akses kamera ditolak. Mohon izinkan akses kamera pada browser Anda.";
                        } else if (err.name === 'NotFoundError') {
                            msg = "Perangkat kamera tidak ditemukan.";
                        } else {
                            msg += "\nError: " + (err.message || err);
                        }
                        
                        alert(msg);
                        // Do not stop scanner immediately so user can try 'Upload Gambar'
                    });
                }

                function stopScanner() {
                    const modal = document.getElementById('scanner-modal');
                    modal.classList.add('hidden');
                    if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
                        html5QrcodeScanner.stop().catch(console.error);
                    }
                }

                async function scanFromFile(input) {
                    if (!input.files || input.files.length === 0) return;
                    
                    const imageFile = input.files[0];
                    
                    // Show Loading Feedback
                    const btn = input.nextElementSibling;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<span class="animate-pulse">⏳ Memproses...</span>';
                    btn.disabled = true;

                    try {
                        // Ensure scanner instance exists
                        if (html5QrcodeScanner === null) {
                             html5QrcodeScanner = new Html5Qrcode("reader"); 
                        }

                        // Stop camera stream if it's running to avoid conflict
                        if (html5QrcodeScanner.isScanning) {
                            await html5QrcodeScanner.stop();
                        }

                        // Scan the file
                        const decodedResult = await html5QrcodeScanner.scanFileV2(imageFile, true);
                        
                        console.log("File Scan Result:", decodedResult); // Debug logic
                        onScanSuccess(decodedResult.decodedText, decodedResult);

                    } catch (err) {
                        console.error("Scan File Error:", err);
                        alert("Gagal membaca QR Code dari gambar.\n\nTips:\n- Pastikan gambar jelas & tidak buram\n- Crop gambar agar fokus ke QR/Barcode\n- Gunakan format JPG/PNG");
                    } finally {
                        // Reset button UI
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        input.value = ''; // Reset input so same file can be selected again
                    }
                }

                function onScanSuccess(decodedText, decodedResult) {
                    const audio = new Audio('https://www.soundjay.com/buttons/beep-01a.mp3');
                    audio.play().catch(console.error);
                    stopScanner();
                    
                    // Put text in search bar
                    const input = document.getElementById('global-search');
                    input.value = decodedText;
                    
                    // Trigger Search
                    performSearch(decodedText);
                }

                function performSearch(query) {
                    // Show Loading or something if needed
                    fetch('{{ route("tracking.track") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ spk_number: query })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showResultModal(data);
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mencari data.');
                    });
                }

                function showResultModal(response) {
                    const modal = document.getElementById('result-modal');
                    const content = document.getElementById('result-content');
                    
                    let html = '';
                    response.data.forEach(order => {
                        html += `
                        <div class="border rounded-lg p-4 bg-gray-50 relative">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="text-xs text-gray-500 font-bold tracking-wider">SPK: ${order.spk_number}</div>
                                    <h4 class="font-bold text-lg text-teal-700">${order.customer_name}</h4>
                                </div>
                                <span class="px-2 py-1 text-xs font-bold rounded bg-blue-100 text-blue-800">${order.status}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-3">
                                <div><span class="block text-xs text-gray-400">Sepatu</span>${order.shoe_brand}</div>
                                <div><span class="block text-xs text-gray-400">Warna</span>${order.shoe_color}</div>
                                <div><span class="block text-xs text-gray-400">Masuk</span>${order.entry_date}</div>
                                <div><span class="block text-xs text-gray-400">Estimasi</span>${order.estimation_date}</div>
                            </div>
                            <div class="mt-3 pt-3 border-t text-right">
                                <a href="${order.detail_url}" class="text-teal-600 hover:text-teal-800 font-bold text-sm">Lihat Detail Lengkap &rarr;</a>
                            </div>
                        </div>`;
                    });

                    content.innerHTML = html;
                    modal.classList.remove('hidden');
                }

                function closeResultModal() {
                    document.getElementById('result-modal').classList.add('hidden');
                }

                document.addEventListener('keydown', function(e) {
                    if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                        document.getElementById('global-search').focus();
                    }
                    if (e.key === 'Escape') {
                         stopScanner();
                         closeResultModal();
                    }
                });

                // Override form submit to use AJAX
                const navbarTrackingForm = document.getElementById('navbar-tracking-form');
                if (navbarTrackingForm) {
                    navbarTrackingForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const query = document.getElementById('global-search').value;
                        if(query) performSearch(query);
                    });
                }
            </script>

            <!-- User Profile Dropdown (Simple & Clean) -->
            <div class="hidden sm:flex items-center ms-4">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition-all focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white font-bold text-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="text-white text-sm font-medium hidden md:block">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <div class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500">{{ Auth::user()->role }}</div>
                        </div>
                        
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="flex items-center gap-2 text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>


</nav>
