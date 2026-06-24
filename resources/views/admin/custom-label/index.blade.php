<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Custom Shipping Label') }}
        </h2>
    </x-slot>

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        @media print {
            /* Hide ALL layout components */
            .sidebar-gradient, 
            .header-gradient, 
            .no-print, 
            nav, 
            aside, 
            header,
            [role="navigation"],
            .main-content > header {
                display: none !important;
            }
            
            /* Reset layout containers */
            body, 
            html, 
            .min-h-screen, 
            .main-content, 
            main, 
            .py-6, 
            .py-10, 
            .max-w-\[1400px\], 
            .flex-col, 
            .flex-1 {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                width: 100% !important;
                height: auto !important;
                position: static !important;
                transform: none !important;
                min-height: auto !important;
                border: none !important;
                box-shadow: none !important;
            }

            #printable-area {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 210mm !important;
                height: 148.5mm !important;
                transform: none !important;
                visibility: visible !important;
                box-shadow: none !important;
                z-index: 1000 !important;
            }
            
            .canvas {
                border-bottom: none !important;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        
        /* Custom scrollbar for preview */
        .preview-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .preview-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .preview-scroll::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
        
        /* Label specific styles */
        .canvas {
            width: 210mm;
            height: 148.5mm;
            position: relative;
            overflow: hidden;
            background-color: #ffffff;
            border-bottom: 2px dashed #e2e8f0;
            transform-origin: top left;
        }
        .marble-texture {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.3' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.02'/%3E%3C/svg%3E");
            z-index: 1;
        }
        
        [x-cloak] { display: none !important; }
    </style>

    <div class="py-10 bg-[#FBFBFB] dark:bg-gray-900 min-h-screen" x-data="{
        name: 'Nama Kustomer',
        address: 'Jl. Alamat Lengkap No.123, Kelurahan, Kecamatan, Kota, Provinsi',
        phone: '081234567890',
        district: 'Kecamatan',
        city: 'Kota',
        province: 'Provinsi',
        postalCode: '12345',
        maskPhone(value) {
            if (!value) return '';
            return value.toString().trim();
        },
        printLabel() {
            if (typeof window.printCustomLabel === 'function') {
                window.printCustomLabel();
            } else {
                window.print();
            }
        }
    }">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                
                {{-- Editor Section --}}
                <div class="w-full lg:w-1/3 space-y-6 no-print">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 p-8">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="p-3 bg-teal-50 dark:bg-teal-900/30 rounded-2xl">
                                <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Label Editor</h3>
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-widest mt-0.5">Input detail pengiriman</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nama Penerima</label>
                                <input type="text" x-model="name" 
                                    class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:text-white font-bold transition-all"
                                    placeholder="Masukkan nama penerima...">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Alamat Lengkap</label>
                                <textarea x-model="address" rows="3"
                                    class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:text-white font-bold transition-all"
                                    placeholder="Masukkan alamat lengkap..."></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Kecamatan</label>
                                    <input type="text" x-model="district" 
                                        class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:text-white font-bold transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Kota</label>
                                    <input type="text" x-model="city" 
                                        class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:text-white font-bold transition-all text-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Provinsi</label>
                                    <input type="text" x-model="province" 
                                        class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:text-white font-bold transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Kode Pos</label>
                                    <input type="text" x-model="postalCode" 
                                        class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:text-white font-bold transition-all text-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nomor WhatsApp</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-5 pointer-events-none">
                                        <span class="text-teal-500 font-bold">+62</span>
                                    </div>
                                    <input type="text" x-model="phone" 
                                        class="w-full pl-14 pr-5 py-4 bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:text-white font-bold transition-all"
                                        placeholder="81234567890">
                                </div>
                            </div>
                        </div>

                        <div class="mt-10">
                            <button @click="printLabel()" 
                                class="w-full bg-[#22AF85] hover:bg-[#1d9a74] text-white font-black py-5 rounded-2xl shadow-xl shadow-teal-500/20 transition-all active:scale-95 flex items-center justify-center gap-3 text-lg uppercase tracking-widest">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Cetak Label
                            </button>
                        </div>
                    </div>
                    
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 p-6 rounded-3xl border border-indigo-100 dark:border-indigo-800/50">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 bg-indigo-500 text-white rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-indigo-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-indigo-900 dark:text-indigo-100 uppercase text-xs tracking-wider">Tips Pencetakan</h4>
                                <p class="text-xs text-indigo-700/70 dark:text-indigo-300/70 mt-1 leading-relaxed font-medium">
                                    Gunakan browser Chrome untuk hasil terbaik. Pastikan 'Headers and Footers' dimatikan pada setelan cetak agar desain bersih.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Preview Section --}}
                <div class="flex-1">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 p-4 sticky top-8">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50 dark:border-gray-700 mb-6 no-print">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-[0.3em]">Live Preview</h3>
                            <div class="flex gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                        </div>
                        
                        <div class="preview-scroll overflow-auto bg-gray-100 dark:bg-gray-900 rounded-2xl p-8 flex justify-center min-h-[600px]">
                            <div id="printable-area" class="shadow-2xl scale-[0.6] xl:scale-[0.8] origin-top">
                                <div class="canvas">
                                    <div class="marble-texture"></div>

                                    {{-- Background Shapes --}}
                                    <div class="absolute -top-16 -left-16 w-80 h-72 bg-[#FFC232] rounded-[70px] rotate-[12deg] z-10 opacity-70 border-[8px] border-white shadow-sm"></div>
                                    <div class="absolute top-10 -left-20 w-72 h-64 bg-[#22B086] rounded-[60px] rotate-[-8deg] z-10 opacity-80 border-[8px] border-white shadow-sm"></div>
                                    <div class="absolute bottom-0 -right-16 w-[450px] h-40 bg-[#22B086] rounded-tl-[100px] z-10 border-[8px] border-white shadow-xl opacity-90"></div>

                                    {{-- Main Container --}}
                                    <div class="relative w-full h-full p-8 flex flex-col z-20" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                                        
                                        {{-- Header (Top Right) --}}
                                        <div class="flex justify-end mb-6">
                                            <div class="text-right">
                                                <h1 class="text-4xl font-[900] text-slate-800 leading-[0.8] tracking-tighter mb-1 uppercase">
                                                    Just<br>taking care<br>your shoes
                                                </h1>
                                                <div class="flex flex-col items-end gap-1.5 mt-2">
                                                    <div class="flex gap-1 h-1 rounded-full overflow-hidden w-16 bg-slate-100">
                                                        <div class="w-[60%] bg-[#22B086]"></div>
                                                        <div class="w-[20%] bg-[#FFC232]"></div>
                                                        <div class="w-[20%] bg-slate-800"></div>
                                                    </div>
                                                    <p class="text-slate-400 font-bold text-[7px] tracking-widest uppercase">
                                                        our pleasure can be a part of making your shoes better.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Shipping Card --}}
                                        <div class="relative flex-1 w-full bg-white border-[2px] border-[#FFC232] rounded-[20px] rounded-br-[70px] overflow-hidden flex flex-col shadow-xl">
                                            
                                            {{-- To Badge --}}
                                            <div class="absolute top-0 left-0 bg-[#22B086] text-white px-6 py-1.5 rounded-br-[25px] z-30 shadow-sm">
                                                <span class="font-black text-[9px] tracking-widest uppercase">To : our beloved customer</span>
                                            </div>

                                            {{-- Address Content --}}
                                            <div class="flex-1 px-10 pt-10 flex flex-col justify-center">
                                                <div class="mb-4">
                                                    <h2 class="text-5xl font-black text-slate-900 uppercase tracking-tighter leading-none mb-3" x-text="name || 'Nama Penerima'"></h2>
                                                    <div class="space-y-0.5 border-l-[4px] border-[#22B086] pl-5 py-0.5">
                                                        <p class="text-xl font-bold text-slate-700 uppercase tracking-tight" x-text="address || 'Alamat tidak tersedia'"></p>
                                                        <p class="text-sm font-semibold text-slate-400 uppercase tracking-widest leading-none">
                                                            <span x-text="district || '-'"></span> | <span x-text="city || '-'"></span>
                                                        </p>
                                                        <p class="text-sm font-semibold text-slate-400 uppercase tracking-widest leading-none">
                                                            <span x-text="province || '-'"></span> - <span x-text="postalCode || '-'"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-2 flex items-center gap-2">
                                                    <div class="w-5 h-5 bg-emerald-500 text-white rounded-md flex items-center justify-center shadow-sm border border-white">
                                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.27c1.12.44 2.33.68 3.58.68.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.24 2.45.68 3.58.11.41.02.83-.27 1.11l-2.2 2.2z"/></svg>
                                                    </div>
                                                    <span class="text-sm font-black text-emerald-600 tracking-tight font-mono">+62 <span x-text="maskPhone(phone) || '-'"></span></span>
                                                </div>
                                            </div>

                                            {{-- Footer Area --}}
                                            <div class="h-18 border-t-[2px] border-[#FFC232] flex bg-white overflow-hidden relative">
                                                {{-- Logo Section --}}
                                                <div class="w-[18%] flex items-center justify-center border-r-[2px] border-[#FFC232] px-2">
                                                    <div class="flex items-center gap-3 opacity-90 transition-all duration-300">
                                                        <div class="relative w-4 h-3 flex flex-col justify-center shrink-0">
                                                            <div class="absolute top-0 right-0 w-2.5 h-1 bg-[#FFC232] rounded-full shadow-sm"></div>
                                                            <div class="absolute bottom-0 left-0 w-2.5 h-1 bg-[#22B086] rounded-full shadow-sm"></div>
                                                        </div>
                                                        <div class="leading-[1] text-left uppercase shrink-0">
                                                            <span class="block text-[10px] font-[900] text-[#22B086] tracking-widest">Shoe</span>
                                                            <span class="block text-[10px] font-light text-[#22B086] tracking-widest mt-0.5">Workshop</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- Location Section --}}
                                                <div class="w-[40%] border-r-[2px] border-[#FFC232] px-4 py-2 flex flex-col justify-center gap-1">
                                                    <p class="font-black text-slate-900 text-[7px] uppercase tracking-[0.15em]">Lokasi Kami</p>
                                                    <div class="space-y-1">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-5 h-5 bg-emerald-50 text-[#22B086] rounded-full flex items-center justify-center shrink-0 border border-emerald-100/50">
                                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                                            </div>
                                                            <p class="text-slate-600 text-[7.5px] font-bold tracking-tight uppercase leading-[1.2]">
                                                                Jl. Kembar I No.41, Cigereleng, Kec. Regol,<br>Kota Bandung, Jawa Barat 40253
                                                            </p>
                                                        </div>

                                                        <div class="flex items-center gap-2">
                                                            <div class="w-5 h-5 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center shrink-0 border border-slate-100">
                                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                                                            </div>
                                                            <span class="text-[#22B086] text-[7.5px] font-[900] uppercase tracking-wide">WWW.SHOEWORKSHOP.ID</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Contact Section --}}
                                                <div class="w-[42%] px-4 py-2 flex flex-col justify-center gap-1 relative z-10">
                                                    <p class="font-black text-slate-900 text-[6px] uppercase tracking-[0.1em]">Hubungi Kami</p>
                                                    <div class="grid grid-cols-[1.1fr_1fr] gap-y-1 mt-0.5">
                                                        <div class="flex items-center gap-1.5">
                                                            <div class="w-5 h-5 bg-emerald-50 text-[#25D366] rounded-full flex items-center justify-center shrink-0 border border-emerald-100/50">
                                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                            </div>
                                                            <span class="text-emerald-700 text-[7.5px] font-bold">0895339939800</span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            <div class="w-5 h-5 bg-rose-50 text-[#E1306C] rounded-full flex items-center justify-center shrink-0 border border-rose-100/50">
                                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                                            </div>
                                                            <span class="text-slate-600 text-[7.5px] font-bold tracking-tight">Shoe_Workshop</span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            <div class="w-5 h-5 bg-slate-50 text-slate-900 rounded-full flex items-center justify-center shrink-0 border border-slate-100">
                                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.032 2.612.019 3.88.155.031 1.285.438 2.56 1.312 3.41s2.126 1.334 3.41 1.427v3.913c-1.248-.028-2.427-.402-3.412-1.133V14.91c0 1.245-.31 2.428-.888 3.412a6.402 6.402 0 0 1-2.422 2.422 6.402 6.402 0 0 1-3.412.888 6.402 6.402 0 0 1-3.412-.888 6.402 6.402 0 0 1-2.422-2.422 6.402 6.402 0 0 1-.888-3.412 6.402 6.402 0 0 1 .888-3.412 6.402 6.402 0 0 1 2.422-2.422 6.402 6.402 0 0 1 3.412-.888c.125 0 .248.01.37.03V8.12a2.44 2.44 0 0 0-.37-.03 2.502 2.502 0 0 0-2.5 2.5 2.502 2.502 0 0 0 2.5 2.5 2.502 2.502 0 0 0 2.5-2.5V0z"/></svg>
                                                            </div>
                                                            <span class="text-slate-600 text-[7.5px] font-bold tracking-tight">Shoe.Workshop</span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            <div class="w-5 h-5 bg-red-50 text-[#FF0000] rounded-full flex items-center justify-center shrink-0 border border-red-100/50">
                                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                                            </div>
                                                            <span class="text-slate-600 text-[7.5px] font-bold tracking-tight">Shoe Police</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.printCustomLabel = function() {
            const printableElement = document.querySelector('#printable-area .canvas');
            if (!printableElement) return;
            
            const printContents = printableElement.innerHTML;
            
            // Create or get the hidden iframe
            let iframe = document.getElementById('print-iframe');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'print-iframe';
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';
                document.body.appendChild(iframe);
            }
            
            const doc = iframe.contentWindow.document;
            doc.open();
            doc.write('<html><head><title>Cetak Label</title>');
            doc.write('<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">');
            doc.write('<script src="https://cdn.tailwindcss.com"><\/script>');
            doc.write('<style>');
            doc.write('@page { size: A4 portrait; margin: 0; }');
            doc.write('body { margin: 0; padding: 0; font-family: "Plus Jakarta Sans", sans-serif; }');
            doc.write('.canvas { width: 210mm; height: 148.5mm; position: relative; overflow: hidden; background-color: #ffffff; }');
            doc.write('.marble-texture { position: absolute; inset: 0; background-image: url("data:image/svg+xml,%3Csvg viewBox=\'0 0 400 400\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cfilter id=\'noiseFilter\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.3\' numOctaves=\'3\' stitchTiles=\'stitch\'/%3E%3C/filter%3E%3Crect width=\'100%25\' height=\'100%25\' filter=\'url(%23noiseFilter)\' opacity=\'0.02\'/%3E%3C/svg%3E"); z-index: 1; }');
            doc.write('* { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }');
            doc.write('</style></head><body>');
            doc.write('<div class="canvas">' + printContents + '</div>');
            doc.write('</body></html>');
            doc.close();
            
            // Wait for tailwind and fonts
            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }, 800);
        }
    </script>
    @endpush
</x-app-layout>
