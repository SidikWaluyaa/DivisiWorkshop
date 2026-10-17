<x-app-layout>
    <style>
        :root {
            --primary-emerald: #059669;
            --emerald-glow: rgba(5, 150, 105, 0.15);
            --amber-warm: #F59E0B;
            --indigo-royal: #4F46E5;
        }
        
        .reception-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.05);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            border-radius: 1.25rem;
        }
        .reception-card:hover {
            box-shadow: 0 12px 30px -8px rgba(15, 23, 42, 0.08);
        }
        .badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 9999px;
            display: inline-block;
        }
    </style>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="p-3 bg-gradient-to-br from-emerald-500 to-teal-700 text-white rounded-2xl shadow-lg shadow-emerald-500/20 ring-1 ring-white/30 backdrop-blur-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="font-black text-2xl leading-tight tracking-tight text-white">
                            {{ __('Gudang Penerimaan') }}
                        </h2>
                        <span class="hidden md:inline-flex px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-400/20 text-emerald-200 border border-emerald-300/30 uppercase tracking-widest">
                            Inbound & QC Hub
                        </span>
                    </div>
                    <div class="text-[11px] font-bold text-emerald-100/80 uppercase tracking-widest mt-0.5">
                        Penerimaan Fisik Sepatu & Quality Control Awal
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 text-white text-xs font-bold backdrop-blur-md border border-white/20 shadow-sm">
                    <span class="badge-dot bg-emerald-400 animate-pulse"></span>
                    Sistem Workshop Aktif
                </span>
            </div>
        </div>
    </x-slot>

    <div id="reception-main-container" class="pb-12 bg-gray-50/50" x-data="{ 
        selectedItems: [],
        selectAllMode: false,
        totalRecords: {{ $orders->total() }},
        pageRecords: {{ $orders->count() }},
        
        updateSelection() {
            const isMobile = window.innerWidth < 1024;
            const selector = isMobile ? '.check-item-mobile:checked' : '.check-item-desktop:checked';
            const checkboxes = document.querySelectorAll(selector);
            this.selectedItems = Array.from(checkboxes).map(cb => cb.value);
            
            // If user manually unchecks, disable selectAllMode
            if (this.selectedItems.length < this.pageRecords) {
                this.selectAllMode = false;
            }
        },
        toggleAll(event) {
            const isMobile = window.innerWidth < 1024;
            const selector = isMobile ? '.check-item-mobile' : '.check-item-desktop';
            const checkboxes = document.querySelectorAll(selector);
            checkboxes.forEach(cb => cb.checked = event.target.checked);
            
            this.updateSelection();
            
            // Reset Select All Mode on toggle
            this.selectAllMode = false;
        },
        selectAllAcrossPages() {
            this.selectAllMode = true;
            // Visual check all checkboxes on current page too
            const isMobile = window.innerWidth < 1024;
            const selector = isMobile ? '.check-item-mobile' : '.check-item-desktop';
            document.querySelectorAll(selector).forEach(cb => cb.checked = true);
            this.updateSelection();
            // Force true again
            this.selectAllMode = true; 
        },
        clearSelection() {
            this.selectedItems = [];
            this.selectAllMode = false;
            const checkboxes = document.querySelectorAll('.check-item');
            checkboxes.forEach(cb => cb.checked = false);
        }
    }">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10 space-y-7">

            {{-- 1. COMPACT TOOLBAR & COLLAPSIBLE IMPORT SECTION --}}
            <div x-data="{ openTools: false }" class="reception-card p-4 sm:p-5">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                Pusat Alat & Import Gudang
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Alat Pendukung</span>
                            </h3>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">Import data Excel, unduh template SPK, atau buat order manual penerimaan</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                        <button type="button" @click="openTools = !openTools" 
                                class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs font-black transition-all border active:scale-95 shadow-sm whitespace-nowrap"
                                :class="openTools ? 'bg-emerald-600 text-white border-emerald-600 shadow-emerald-500/20' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200/80'">
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openTools }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <span x-text="openTools ? 'Tutup Panel Import' : '📥 Buka Import Excel'"></span>
                        </button>

                        <button type="button" onclick="openCreateOrderModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-xl text-xs font-black transition-all shadow-sm active:scale-95 whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Order Manual
                        </button>

                        <div class="h-6 w-px bg-slate-200 hidden md:block mx-0.5"></div>

                        <a href="{{ route('reception.template') }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition-all border border-slate-200 shadow-sm active:scale-95 whitespace-nowrap" title="Unduh Template Excel SPK">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Template
                        </a>

                        <a href="{{ route('reception.export') }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition-all border border-slate-200 shadow-sm active:scale-95 whitespace-nowrap" title="Export Seluruh Data Reception ke Excel">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export
                        </a>

                        @can('deleteReception', \App\Models\WorkOrder::class)
                            <a href="{{ route('reception.trash') }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-xs font-bold transition-all border border-rose-200/80 active:scale-95 whitespace-nowrap" title="Buka Tempat Sampah">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Sampah
                            </a>
                        @endcan
                    </div>
                </div>

                <div x-show="openTools" x-collapse x-cloak class="mt-5 pt-5 border-t border-slate-100">
                    <div class="bg-slate-50/80 rounded-2xl p-5 sm:p-6 border border-slate-200/80">
                        <form method="POST" action="{{ route('reception.import') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                            @csrf
                            
                            {{-- Sisi Kiri: Petunjuk Import Singkat & Rapih --}}
                            <div class="lg:col-span-5 space-y-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <h4 class="font-black text-slate-800 text-xs uppercase tracking-wider">Format File & Petunjuk Import</h4>
                                </div>
                                
                                <div class="bg-blue-50/90 border border-blue-200/80 rounded-xl p-3 flex items-start gap-2.5">
                                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-xs text-blue-800 font-medium leading-relaxed">
                                        Pastikan data mengikuti kolom template resmi. Anda dapat mengunduh panduan kolom via tombol <strong>Template</strong> di atas.
                                    </p>
                                </div>

                                <ul class="text-xs text-slate-600 space-y-1.5 font-medium ml-1">
                                    <li class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Gunakan file berekstensi <strong>.xlsx</strong> atau <strong>.xls</strong></span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>No. SPK dan No. WhatsApp wajib terisi</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Maksimal ukuran berkas <strong>10 MB</strong></span>
                                    </li>
                                </ul>
                            </div>

                            {{-- Sisi Kanan: Area Upload Interaktif & Tombol Import --}}
                            <div class="lg:col-span-7" x-data="{ fileName: null, fileSize: null }">
                                <label for="file" 
                                       class="relative flex flex-col items-center justify-center w-full h-36 border-2 border-dashed rounded-2xl cursor-pointer transition-all duration-300 group p-4 text-center"
                                       :class="fileName ? 'border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-500/20' : 'border-slate-300 hover:border-emerald-400 bg-white hover:bg-slate-50/80 shadow-sm'">
                                    
                                    {{-- Belum Ada File --}}
                                    <div class="flex flex-col items-center justify-center space-y-1.5" x-show="!fileName">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center transition-transform group-hover:scale-110">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-700">
                                                <span class="text-emerald-700 font-black underline">Pilih berkas Excel</span> atau tarik ke kotak ini
                                            </p>
                                            <p class="text-[10px] text-slate-400 font-medium">Format: XLSX, XLS (Maks. 10MB)</p>
                                        </div>
                                    </div>

                                    {{-- File Sudah Terpilih --}}
                                    <div class="flex flex-col items-center justify-center space-y-1.5" x-show="fileName" x-cloak>
                                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/30">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <div class="text-center">
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-emerald-200 rounded-lg shadow-sm">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span class="text-xs font-black text-slate-800 truncate max-w-[240px]" x-text="fileName"></span>
                                            </div>
                                            <p class="text-[10px] text-emerald-700 font-bold mt-1">Berkas siap di-import. Klik tombol di bawah untuk memproses.</p>
                                        </div>
                                    </div>

                                    <input id="file" name="file" type="file" class="hidden" required accept=".xlsx,.xls,.csv" 
                                           @change="if($event.target.files.length) { fileName = $event.target.files[0].name; }" />
                                </label>
                                <x-input-error class="mt-2 text-center" :messages="$errors->get('file')" />
                                
                                {{-- Tombol Eksekusi Import --}}
                                <div class="mt-3.5 flex items-center justify-end gap-3">
                                    <button type="button" x-show="fileName" @click="fileName = null; document.getElementById('file').value = '';" 
                                            class="px-3.5 py-2.5 text-xs font-bold text-slate-500 hover:text-rose-600 transition-colors">
                                        Batal Pilih
                                    </button>

                                    <button type="submit" 
                                            :disabled="!fileName"
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl font-black text-xs uppercase tracking-wider transition-all shadow-md shadow-emerald-600/25 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <span>Proses Import Database</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-transition class="mt-4 p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                            <span class="font-medium">Berhasil!</span> {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mt-4 p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                            <span class="font-medium">Error!</span> {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. INTEGRATED METRIC CARDS TAB NAVIGATION --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 lg:gap-6">
                {{-- TAB 1: SPK MASUK (PENDING CS) --}}
                <a href="{{ route('reception.index', ['tab' => 'pending']) }}" 
                   class="relative p-6 sm:p-7 rounded-2xl border transition-all duration-300 text-left group hover:-translate-y-0.5 {{ $activeTab === 'pending' ? 'bg-gradient-to-br from-amber-500/15 via-white to-amber-500/5 border-amber-400 shadow-xl shadow-amber-500/10 ring-2 ring-amber-400/30' : 'bg-white/95 hover:bg-white border-slate-200/90 hover:border-amber-300 shadow-sm hover:shadow-md' }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-105 {{ $activeTab === 'pending' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'bg-amber-100 text-amber-700' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        @if($pendingOrders->total() > 0)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-500 text-white animate-pulse shadow-sm shadow-amber-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                Menunggu Fisik
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                                Tidak ada antrian
                            </span>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-2.5 mb-1.5">
                        <span class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">{{ number_format($pendingOrders->total()) }}</span>
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Order SPK</span>
                    </div>
                    <div class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5 mt-2">
                        <span class="badge-dot bg-amber-500"></span>
                        SPK Masuk (Pending CS)
                    </div>
                    <div class="text-xs text-slate-500 mt-1 leading-relaxed">Barang diserahkan CS, menunggu diverifikasi fisik & masuk rak transit</div>
                </a>

                {{-- TAB 2: DITERIMA (WAREHOUSE) --}}
                <a href="{{ route('reception.index', ['tab' => 'received']) }}" 
                   class="relative p-6 sm:p-7 rounded-2xl border transition-all duration-300 text-left group hover:-translate-y-0.5 {{ $activeTab === 'received' ? 'bg-gradient-to-br from-emerald-500/15 via-white to-emerald-500/5 border-emerald-400 shadow-xl shadow-emerald-500/10 ring-2 ring-emerald-400/30' : 'bg-white/95 hover:bg-white border-slate-200/90 hover:border-emerald-300 shadow-sm hover:shadow-md' }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-105 {{ $activeTab === 'received' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-emerald-100 text-emerald-700' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                            Rak Inbound
                        </span>
                    </div>
                    <div class="flex items-baseline gap-2.5 mb-1.5">
                        <span class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">{{ number_format($orders->total()) }}</span>
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Pasang / Item</span>
                    </div>
                    <div class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5 mt-2">
                        <span class="badge-dot bg-emerald-500"></span>
                        Diterima (Warehouse)
                    </div>
                    <div class="text-xs text-slate-500 mt-1 leading-relaxed">Sepatu di rak transit, siap untuk QC fisik, aksesoris & foto kondisi</div>
                </a>

                {{-- TAB 3: SUDAH DIPROSES --}}
                <a href="{{ route('reception.index', ['tab' => 'processed']) }}" 
                   class="relative p-6 sm:p-7 rounded-2xl border transition-all duration-300 text-left group hover:-translate-y-0.5 {{ $activeTab === 'processed' ? 'bg-gradient-to-br from-indigo-500/15 via-white to-indigo-500/5 border-indigo-400 shadow-xl shadow-indigo-500/10 ring-2 ring-indigo-400/30' : 'bg-white/95 hover:bg-white border-slate-200/90 hover:border-indigo-300 shadow-sm hover:shadow-md' }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-105 {{ $activeTab === 'processed' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-indigo-100 text-indigo-700' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">
                            Assessment / Prep
                        </span>
                    </div>
                    <div class="flex items-baseline gap-2.5 mb-1.5">
                        <span class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">{{ number_format($processedOrders->total()) }}</span>
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Selesai QC</span>
                    </div>
                    <div class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5 mt-2">
                        <span class="badge-dot bg-indigo-500"></span>
                        Sudah Diproses
                    </div>
                    <div class="text-xs text-slate-500 mt-1 leading-relaxed">Sudah diteruskan ke penilaian teknis, persiapan, atau follow-up CX</div>
                </a>
            </div>

            <!-- Workspace Card for Reception Data -->
            <div class="reception-card overflow-hidden" x-data="{ 
                activeTab: '{{ $activeTab }}',
                pendingSpk: '{{ request('pending_spk', '') }}',
                pendingCustomer: '{{ request('pending_customer', '') }}',
                isValidSpkFormat(val) {
                    if (!val) return true;
                    val = val.trim();
                    return /^[S|T|H|A|L]-\d{4}-\d{2}-\d{4}-[A-Z]{2,4}$/i.test(val);
                },
                actionModalOpen: false,
                actionOrder: null,
                openQuickAction(order) {
                    this.actionOrder = order;
                    this.actionModalOpen = true;
                },
                closeQuickAction() {
                    this.actionModalOpen = false;
                }
            }" x-on:switch-tab.window="activeTab = $event.detail">
                <div class="px-6 sm:px-8 py-5 border-b border-slate-100 bg-white flex flex-col md:flex-row justify-between md:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full {{ $activeTab === 'pending' ? 'bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.5)]' : ($activeTab === 'received' ? 'bg-emerald-500 shadow-[0_0_10px_rgba(5,150,105,0.5)]' : 'bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)]') }}"></span>
                        <div>
                            <h3 class="text-base font-black text-slate-900 tracking-tight">
                                @if($activeTab === 'pending')
                                    Daftar SPK Masuk Dari Customer Service
                                @elseif($activeTab === 'received')
                                    Daftar Order Diterima Fisik (Di Rak Transit Gudang)
                                @else
                                    Riwayat Order Selesai Diproses Gudang
                                @endif
                            </h3>
                            <p class="text-xs text-slate-500 font-medium">
                                @if($activeTab === 'pending')
                                    Verifikasi kondisi barang, tentukan rak transit, dan klik Terima Barang.
                                @elseif($activeTab === 'received')
                                    Lakukan pemeriksaan fisik awal, catat kelengkapan aksesoris, dan unggah foto kondisi.
                                @else
                                    Daftar order yang telah dialihkan ke tahap teknis berikutnya.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                            <form action="{{ route('reception.index') }}" method="GET" class="flex items-center gap-2">
                                <select name="handler_id" onchange="this.form.submit()" class="text-xs font-bold border-slate-200 rounded-xl focus:ring-emerald-500 py-1.5 pr-8 bg-slate-50 text-slate-700">
                                    <option value="">Semua Handler Gudang</option>
                                    @foreach($handlers as $h)
                                        <option value="{{ $h->id }}" {{ request('handler_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endif

                        <span class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-black border border-slate-200">
                            Total: {{ $activeTab === 'pending' ? $pendingOrders->total() : ($activeTab === 'received' ? $orders->total() : $processedOrders->total()) }} Order
                        </span>
                        
                        <!-- Bulk Delete Button (Only for received tab) -->
                        @can('deleteReception', \App\Models\WorkOrder::class)
                            <button id="btn-bulk-delete" type="button" 
                                x-show="selectedItems.length > 0 && activeTab === 'received'" x-cloak
                                class="px-3.5 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 rounded-xl text-xs font-black transition-all flex items-center gap-1.5"
                                onclick="submitBulkDelete()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus Terpilih (<span id="count-selected">0</span>)
                            </button>
                        @endcan
                    </div>
                </div>

                {{-- Tab Content Wrapper --}}
                <div>
                    
                    {{-- ======================================================== --}}
                    {{-- TAB 1: PENDING SPK (From CS)                             --}}
                    {{-- ======================================================== --}}
                    <div x-show="activeTab === 'pending'" x-transition class="p-6 sm:p-8">
                        
                        {{-- SMART FILTER BAR (PENDING) --}}
                        <div x-data="{ showAdvanced: {{ (request('pending_date_from') || request('pending_date_to')) ? 'true' : 'false' }} }" 
                             class="mb-6 bg-slate-50/90 p-5 sm:p-6 rounded-2xl border border-slate-200/80">
                            
                            <form method="GET" action="{{ route('reception.index') }}">
                                <input type="hidden" name="tab" value="pending">

                                {{-- Main Search & Priority Quick Bar --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-center">
                                    {{-- SPK Mask Input --}}
                                    <div class="sm:col-span-1 lg:col-span-4">
                                        <div class="relative">
                                            <input type="text" name="pending_spk" x-model="pendingSpk"
                                                placeholder="Cari No. SPK (misal: S-2610...)" 
                                                title="Contoh: S-2501-31-0003-SW"
                                                @input="pendingSpk = formatSPKMask($event.target.value)"
                                                :class="{ 'border-rose-400 bg-rose-50/20 ring-1 ring-rose-400': pendingSpk && !isValidSpkFormat(pendingSpk) }"
                                                class="w-full pl-9 pr-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 shadow-sm transition-all placeholder:text-slate-400 placeholder:font-normal">
                                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        </div>
                                    </div>

                                    {{-- Customer / Phone Search --}}
                                    <div class="sm:col-span-1 lg:col-span-4">
                                        <div class="relative">
                                            <input type="text" name="pending_customer" value="{{ request('pending_customer') }}"
                                                placeholder="Nama Pelanggan / WhatsApp..." 
                                                class="w-full pl-9 pr-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 shadow-sm transition-all placeholder:text-slate-400">
                                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                    </div>

                                    {{-- Priority Dropdown --}}
                                    <div class="sm:col-span-1 lg:col-span-2">
                                        <select name="pending_priority" class="w-full py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-amber-500 shadow-sm">
                                            <option value="">Semua Prioritas</option>
                                            <option value="Prioritas" {{ request('pending_priority') == 'Prioritas' ? 'selected' : '' }}>⚡ Prioritas / OTO</option>
                                            <option value="Reguler" {{ request('pending_priority') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                                        </select>
                                    </div>

                                    {{-- Quick Action Buttons --}}
                                    <div class="sm:col-span-1 lg:col-span-2 flex items-center gap-2 justify-end">
                                        <button type="submit" class="flex-1 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-black shadow-md shadow-amber-500/20 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            Cari
                                        </button>
                                        <button type="button" @click="showAdvanced = !showAdvanced" 
                                                class="p-2.5 bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95 flex-shrink-0"
                                                :title="showAdvanced ? 'Tutup Filter Tanggal' : 'Buka Filter Tanggal'">
                                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': showAdvanced }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Advanced Collapsible Filter (Date & Shortcuts) --}}
                                <div x-show="showAdvanced" x-collapse x-cloak class="mt-4 pt-4 border-t border-slate-200/60">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end">
                                        <div class="md:col-span-3">
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Dari Tanggal Masuk</label>
                                            <input type="date" name="pending_date_from" value="{{ request('pending_date_from') }}" 
                                                   class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 shadow-sm">
                                        </div>

                                        <div class="md:col-span-3">
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Sampai Tanggal</label>
                                            <input type="date" name="pending_date_to" value="{{ request('pending_date_to') }}" 
                                                   class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 shadow-sm">
                                        </div>

                                        <div class="md:col-span-3 flex flex-wrap items-center gap-1.5 pb-0.5">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-1">Pintasan:</span>
                                            <button type="button" onclick="setPendingDateRange(14)" 
                                                    class="px-2.5 py-1.5 bg-white hover:bg-amber-50 border border-slate-200 text-amber-700 rounded-lg text-[10px] font-black uppercase transition-all shadow-sm">
                                                📅 2 Minggu
                                            </button>
                                            <button type="button" onclick="setPendingDateRange(30)" 
                                                    class="px-2.5 py-1.5 bg-white hover:bg-amber-50 border border-slate-200 text-amber-700 rounded-lg text-[10px] font-black uppercase transition-all shadow-sm">
                                                📅 1 Bulan
                                            </button>
                                        </div>

                                        <div class="md:col-span-3 flex items-center justify-end gap-2">
                                            <a href="{{ route('reception.index', ['tab' => 'pending']) }}" 
                                               class="px-3 py-2 bg-white text-slate-600 border border-slate-200 rounded-xl text-xs font-bold hover:bg-slate-100 transition-colors">
                                                Reset
                                            </a>
                                            <button type="button" onclick="downloadBulkPdf()" 
                                                    class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black shadow-sm flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                PDF
                                            </button>
                                            <button type="button" onclick="downloadBulkExcel()" 
                                                    class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black shadow-sm flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Excel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- EMPTY STATE --}}
                        @if($pendingOrders->isEmpty())
                            <div class="text-center py-16 px-4 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                                <div class="w-16 h-16 rounded-3xl bg-amber-100 text-amber-600 mx-auto flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-black text-slate-800">Tidak Ada SPK Masuk (Pending)</h3>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Semua order dari CS telah diverifikasi dan masuk ke rak penyimpanan gudang.</p>
                            </div>
                        @else
                            {{-- MOBILE VIEW: LUXURY CARDS --}}
                            <div class="block lg:hidden space-y-4">
                                @foreach($pendingOrders as $order)
                                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 transition-all hover:shadow-md">
                                    <div class="flex justify-between items-start mb-3 pb-3 border-b border-slate-100">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-black text-slate-900 text-sm tracking-tight">{{ $order->spk_number }}</span>
                                                <button type="button" onclick="navigator.clipboard.writeText('{{ $order->spk_number }}')" class="text-slate-400 hover:text-amber-600" title="Salin No. SPK">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                </button>
                                            </div>
                                            <div class="text-[11px] font-bold text-slate-400 mt-0.5">
                                                Dibuat: {{ $order->created_at->format('d M Y H:i') }}
                                            </div>
                                        </div>
                                        
                                        @php
                                            $parts = explode('-', $order->spk_number);
                                            $csCode = end($parts);
                                            if(strlen($csCode) > 3) $csCode = $order->creator->cs_code ?? 'XX';
                                        @endphp
                                        <div class="flex items-center gap-1.5">
                                            @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express', 'OTO']))
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 border border-rose-200">
                                                    ⚡ {{ $order->priority }}
                                                </span>
                                            @endif
                                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-black">
                                                CS: {{ $csCode }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    {{-- Customer Information --}}
                                    <div class="mb-3.5">
                                        <div class="text-xs font-black text-slate-800">{{ $order->customer_name }}</div>
                                        <div class="text-xs text-slate-500 font-mono mt-0.5 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            {{ $order->customer_phone }}
                                        </div>
                                    </div>

                                    {{-- Shoe Specs Box --}}
                                    <div class="bg-slate-50 p-3 rounded-xl mb-4 border border-slate-100 text-xs">
                                        <div class="font-bold text-slate-800 flex items-center justify-between">
                                            <span>{{ $order->shoe_brand ?: 'Belum diisi' }} (Size: {{ $order->shoe_size ?: '-' }})</span>
                                            <span class="text-[10px] font-black text-slate-400 uppercase">{{ $order->category ?: 'Sepatu' }}</span>
                                        </div>
                                        <div class="text-slate-500 text-[11px] mt-0.5">Warna: {{ $order->shoe_color ?: '-' }}</div>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex gap-2">
                                        <button type="button" onclick="confirmReceive('{{ $order->id }}', '{{ $order->spk_number }}')" 
                                            class="flex-1 py-3 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-slate-950 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-amber-400/20 flex justify-center items-center gap-2 active:scale-95">
                                            <span>Terima Barang</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                        </button>

                                        @can('deleteReception', \App\Models\WorkOrder::class)
                                            <form action="{{ route('reception.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus SPK Pending ini? Data akan dipindahkan ke Tempat Sampah.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3.5 py-3 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition-all border border-rose-200/60 flex items-center justify-center active:scale-95" title="Hapus SPK">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                    <form id="receive-mobile-{{ $order->id }}" action="{{ route('reception.receive', $order->id) }}" method="POST" class="hidden">
                                        @csrf
                                        <input type="hidden" name="rack_code" class="selected-rack-input">
                                    </form>
                                </div>
                                @endforeach
                            </div>

                            {{-- DESKTOP VIEW: ELEVATED INTERACTIVE TABLE --}}
                            <div class="hidden lg:block overflow-x-auto rounded-2xl border border-slate-200/80">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 text-slate-500 uppercase text-[10px] font-black tracking-wider border-b border-slate-200/80">
                                            <th class="px-5 py-4 w-[20%]">No. SPK & Waktu</th>
                                            <th class="px-5 py-4 w-[22%]">Pelanggan</th>
                                            <th class="px-5 py-4 w-[24%]">Spesifikasi Sepatu</th>
                                            <th class="px-5 py-4 w-[10%] text-center">CS Handler</th>
                                            <th class="px-5 py-4 w-[10%] text-center">Prioritas</th>
                                            <th class="px-5 py-4 w-[14%] min-w-[170px] text-center">Aksi Cepat</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white text-xs">
                                        @foreach($pendingOrders as $order)
                                        <tr class="hover:bg-amber-50/30 transition-colors group">
                                            {{-- SPK & Waktu --}}
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-mono font-black text-slate-900 tracking-tight text-sm">{{ $order->spk_number }}</span>
                                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $order->spk_number }}')" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-amber-600 transition-opacity" title="Salin No. SPK">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    </button>
                                                </div>
                                                <div class="text-[10px] font-bold text-slate-400 mt-0.5">
                                                    {{ $order->created_at->format('d M Y, H:i') }}
                                                </div>
                                            </td>

                                            {{-- Pelanggan --}}
                                            <td class="px-5 py-4">
                                                <div class="font-black text-slate-800">{{ $order->customer_name }}</div>
                                                <div class="text-[11px] text-slate-500 font-mono mt-0.5 flex items-center gap-1">
                                                    <span class="text-emerald-600 font-bold">WA:</span> {{ $order->customer_phone }}
                                                </div>
                                            </td>

                                            {{-- Sepatu --}}
                                            <td class="px-5 py-4">
                                                <div class="font-bold text-slate-800">{{ $order->shoe_brand ?: 'Item' }} <span class="text-slate-400 font-normal">({{ $order->shoe_size ?: '-' }})</span></div>
                                                <div class="text-[11px] text-slate-500 mt-0.5">
                                                    {{ $order->shoe_color ?: '-' }} • <span class="uppercase font-semibold text-slate-400">{{ $order->category ?: 'Sepatu' }}</span>
                                                </div>
                                            </td>

                                            {{-- CS Code --}}
                                            <td class="px-5 py-4 text-center">
                                                @php
                                                    $parts = explode('-', $order->spk_number);
                                                    $csCode = end($parts);
                                                    if(strlen($csCode) > 3) $csCode = $order->creator->cs_code ?? 'XX';
                                                @endphp
                                                <span class="inline-flex px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200/80 rounded-lg text-xs font-black">
                                                    {{ $csCode }}
                                                </span>
                                            </td>

                                            {{-- Prioritas --}}
                                            <td class="px-5 py-4 text-center">
                                                @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express', 'OTO']))
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                                        <span class="badge-dot bg-rose-500 animate-ping"></span>
                                                        {{ $order->priority }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                        Reguler
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Aksi --}}
                                            <td class="px-5 py-4 text-center">
                                                <div class="flex items-center justify-center gap-2.5">
                                                    <button type="button" onclick="confirmReceive('{{ $order->id }}', '{{ $order->spk_number }}')" 
                                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-slate-950 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm shadow-amber-400/20 active:scale-95">
                                                        <span>Terima</span>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                    </button>
                                                    
                                                    @can('deleteReception', \App\Models\WorkOrder::class)
                                                        <form action="{{ route('reception.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus SPK Pending ini? Data akan masuk ke Tempat Sampah.')" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors border border-rose-200/60" title="Hapus SPK">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                                <form id="receive-{{ $order->id }}" action="{{ route('reception.receive', $order->id) }}" method="POST" class="hidden">
                                                    @csrf
                                                    <input type="hidden" name="rack_code" class="selected-rack-input">
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination for Pending Orders -->
                            <div class="mt-5 pt-4 border-t border-slate-100">
                                {{ $pendingOrders->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- TAB 2: RECEIVED ORDERS (Existing) --}}
                    <div x-show="activeTab === 'received'" x-transition class="p-6 sm:p-8">
                        
                        {{-- SMART FILTER SECTION (TAB 2) --}}
                        <div class="mb-6 bg-slate-50/90 p-5 sm:p-6 rounded-2xl border border-slate-200/80">
                            <form method="GET" action="{{ route('reception.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end">
                                <input type="hidden" name="tab" value="received">
                                
                                {{-- Search Input --}}
                                <div class="md:col-span-4">
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Cari SPK / Pelanggan / WhatsApp</label>
                                    <div class="relative">
                                        <input type="text" name="search" value="{{ request('search') }}" 
                                               placeholder="Ketik pencarian..." 
                                               class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 text-xs font-medium text-slate-800 shadow-sm transition-all placeholder:text-slate-400">
                                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                </div>

                                {{-- Date From --}}
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Masuk Dari</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 text-xs text-slate-800 shadow-sm">
                                </div>

                                {{-- Date To --}}
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Sampai</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 text-xs text-slate-800 shadow-sm">
                                </div>

                                {{-- Priority Filter --}}
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Prioritas</label>
                                    <select name="priority" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 text-xs font-bold text-slate-700 shadow-sm">
                                        <option value="">Semua</option>
                                        <option value="Prioritas" {{ request('priority') == 'Prioritas' ? 'selected' : '' }}>⚡ Prioritas / OTO</option>
                                        <option value="Reguler" {{ request('priority') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                                    </select>
                                </div>

                                {{-- QC Status Filter --}}
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Status QC</label>
                                    <select name="qc_status" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 text-xs font-bold text-slate-700 shadow-sm">
                                        <option value="">Semua Status</option>
                                        <option value="pending" {{ request('qc_status') == 'pending' ? 'selected' : '' }}>Belum QC</option>
                                        <option value="lolos" {{ request('qc_status') == 'lolos' ? 'selected' : '' }}>Lolos QC</option>
                                        <option value="reject" {{ request('qc_status') == 'reject' ? 'selected' : '' }}>Gagal QC (Reject)</option>
                                    </select>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="md:col-span-12 flex items-center justify-end gap-2 pt-2 border-t border-slate-200/60 mt-1">
                                    <a href="{{ route('reception.index', ['tab' => 'received']) }}" 
                                       class="px-3.5 py-2 bg-white text-slate-600 rounded-xl hover:bg-slate-100 transition-colors text-xs font-bold border border-slate-200 shadow-sm flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Reset
                                    </a>
                                    <button type="submit" 
                                            class="px-5 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all text-xs font-black flex items-center gap-1.5 shadow-md shadow-emerald-600/20 active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        Terapkan Filter
                                    </button>
                                </div>
                            </form>
                        </div>

                <form id="bulk-delete-form" action="{{ route('reception.bulk-delete') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    {{-- Mobile Card View --}}
                    <div class="block lg:hidden space-y-4 px-4 sm:px-0 mb-4">
                        @forelse($orders as $order)
                        <div id="spk-{{ $order->spk_number }}" 
                             x-data="{ 
                                 isHighlighted: false,
                                 init() {
                                     const urlParams = new URLSearchParams(window.location.search);
                                     if (urlParams.get('highlight') === '{{ $order->spk_number }}') {
                                         this.isHighlighted = true;
                                         setTimeout(() => { this.$el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 500);
                                         setTimeout(() => { this.isHighlighted = false; }, 5000);
                                     }
                                 }
                             }"
                             :class="{ 'ring-4 ring-yellow-400 bg-yellow-50/50 scale-[1.01] shadow-2xl z-10' : isHighlighted }"
                             class="bg-white rounded-xl shadow-sm border border-gray-200 relative overflow-hidden transition-all duration-500">
                            
                            <!-- Marker Label if highlighted -->
                            <div x-show="isHighlighted" style="display: none;" class="absolute top-2 left-1/2 -translate-x-1/2 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-[10px] font-black tracking-wider shadow-md z-20 animate-bounce">
                                TARGET PENCARIAN
                            </div>
                            {{-- Selection & Priority header --}}
                            <div class="p-3 border-b border-gray-100 flex justify-between items-start bg-gray-50/50">
                                <div class="flex items-start gap-3">
                                    <div class="pt-1">
                                        <input type="checkbox" name="ids[]" value="{{ $order->id }}" class="check-item check-item-mobile w-5 h-5 rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50" @change="updateSelection()">
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm leading-tight mb-1">{{ $order->created_at->format('d M Y') }}</div>
                                        <div class="flex flex-wrap gap-1">
                                            @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express', 'OTO']))
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 border border-red-200 uppercase">PRIORITAS</span>
                                            @else
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200 uppercase">REGULER</span>
                                            @endif
                                            
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-700 border border-teal-200">DITERIMA</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-black text-teal-600 font-mono text-sm tracking-tight">{{ $order->spk_number }}</div>
                                    <div class="text-[10px] text-gray-500">Est: {{ $order->estimation_date ? $order->estimation_date->format('d M') : '-' }}</div>
                                </div>
                            </div>

                            {{-- Main Content --}}
                            <div class="p-4 space-y-3">
                                {{-- Customer --}}
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-gray-900">{{ $order->customer_name }}</h4>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="text-xs text-green-600 hover:underline flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.6 1.967.3 3.945 1.511 6.085l-1.615 5.9 5.908-1.616zM18.312 14.5c-.266-.133-1.574-.776-1.817-.866-.234-.088-.352-.108-.501.121-.148.229-.588.751-.722.906-.134.156-.269.176-.534.043-.267-.133-1.127-.415-2.147-1.324-.795-.71-1.332-1.585-1.488-1.852-.155-.267-.016-.411.117-.544.119-.119.267-.311.4-.466.134-.155.177-.267.267-.445.089-.177.045-.333-.022-.467-.067-.133-.602-1.448-.824-1.983-.215-.515-.434-.445-.595-.453-.155-.008-.333-.008-.511-.008-.178 0-.467.067-.711.333-.244.267-.933.911-.933 2.222s.955 2.578 1.088 2.756c.133.178 1.881 2.871 4.557 4.026 2.676 1.155 2.676.769 3.167.724.488-.044 1.574-.643 1.797-1.264.221-.621.221-1.153.155-1.264-.067-.111-.244-.178-.511-.311zm-4.433 1.458z"/></svg>
                                            {{ $order->customer_phone }}
                                        </a>
                                    </div>
                                    {{-- Email Action --}}
                                    @if($order->customer_email)
                                        <button type="button" onclick="sendEmailNotification('{{ $order->id }}')" class="text-blue-500 hover:text-blue-700 bg-blue-50 p-1.5 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        </button>
                                    @endif
                                </div>

                                {{-- Items --}}
                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 bg-orange-100 rounded-lg text-orange-600 shadow-sm border border-orange-200">
                                            @php
                                                $cat = strtolower($order->category ?? '');
                                            @endphp
                                            @if(str_contains($cat, 'tas') || str_contains($cat, 'bag') || str_contains($cat, 'dompet'))
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                            @elseif(str_contains($cat, 'topi') || str_contains($cat, 'head') || str_contains($cat, 'helm'))
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582"></path></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            @if($order->shoe_brand || $order->shoe_size)
                                                <div class="flex flex-col gap-0.5">
                                                    <div class="flex items-center gap-1.5">
                                                        <div class="font-black text-gray-900 uppercase tracking-tight text-sm">{{ $order->shoe_brand ?? 'NO BRAND' }}</div>
                                                        <span class="px-1.5 py-0.5 bg-green-50 text-green-600 text-[9px] font-extrabold rounded border border-green-200 uppercase">CS OK</span>
                                                    </div>
                                                    <div class="text-[10px] text-gray-500 font-bold">
                                                        {{ strtoupper($order->category ?? 'Item') }} | {{ $order->shoe_color ?? '-' }} | SIZE: {{ $order->shoe_size ?? '-' }}
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center gap-2 mt-2">
                                                    <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '{{ $order->shoe_brand }}', '{{ $order->shoe_size }}', '{{ $order->shoe_color }}', '{{ $order->category }}')" class="text-[10px] text-teal-600 font-bold hover:text-teal-800 flex items-center gap-1 bg-white border border-teal-200 px-2 py-1 rounded shadow-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        Ubah Detail
                                                    </button>
                                                </div>
                                            @else
                                                <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '', '', '', '')" class="flex items-center gap-1 text-xs text-orange-600 hover:text-orange-800 transition-colors border border-dashed border-orange-300 px-2 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 font-bold">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                    Lengkapi Barang
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                         @php
                                            $qcStatus = $order->warehouse_qc_status;
                                            if (!$qcStatus && !is_null($order->reception_qc_passed)) {
                                                $qcStatus = $order->reception_qc_passed ? 'lolos' : 'reject';
                                            }
                                            $tali = $order->accessories_tali ?? ($order->accessories_data['tali'] ?? null);
                                            $insole = $order->accessories_insole ?? ($order->accessories_data['insole'] ?? null);
                                            $box = $order->accessories_box ?? ($order->accessories_data['box'] ?? null);
                                        @endphp
                                        
                                        <div class="flex flex-col gap-1 items-end">
                                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Handler</div>
                                        <div class="flex items-center gap-1">
                                            <div class="w-4 h-4 rounded-full bg-teal-100 flex items-center justify-center text-[8px] text-teal-600 font-bold">
                                                {{ $order->warehouseQcBy ? substr($order->warehouseQcBy->name, 0, 1) : '?' }}
                                            </div>
                                            <span class="text-xs font-semibold text-gray-700">{{ $order->warehouseQcBy->name ?? 'Belum Ada' }}</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 items-center">
                                            @if($qcStatus == 'lolos')
                                                 <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-1.5 py-0.5 rounded border border-teal-100">QC OK</span>
                                            @elseif($qcStatus == 'reject')
                                                 <span class="text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100">QC REJECT</span>
                                            @endif
                                            
                                            <div class="flex gap-1">
                                                <span class="text-[10px] px-1 rounded border {{ in_array($tali, ['Simpan','S','Nempel','N']) ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-gray-50 text-gray-400 border-gray-100' }}">Tali</span>
                                                <span class="text-[10px] px-1 rounded border {{ in_array($insole, ['Simpan','S','Nempel','N']) ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-gray-50 text-gray-400 border-gray-100' }}">Insol</span>
                                                <span class="text-[10px] px-1 rounded border {{ in_array($box, ['Simpan','S','Nempel','N']) ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-gray-50 text-gray-400 border-gray-100' }}">Box</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Actions --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('reception.show', $order->id) }}" class="col-span-2 flex items-center justify-center py-2 bg-gradient-to-r from-teal-500 to-teal-600 text-white rounded-lg font-bold text-sm shadow">
                                        Proses Order
                                    </a>
                                     <a href="{{ route('reception.print-spk', $order->id) }}" target="_blank" class="flex items-center justify-center py-2 bg-white text-gray-700 border border-gray-200 rounded-lg font-medium text-xs">
                                        Print SPK
                                    </a>
                                    <button type="button" onclick="openEditOrderModal({{ json_encode($order) }})" class="flex items-center justify-center py-2 bg-white text-teal-700 border border-teal-200 rounded-lg font-medium text-xs">
                                        Edit
                                    </button>
                                    <button type="button" x-data @click="$dispatch('open-photo-modal-{{ $order->id }}')" class="flex items-center justify-center py-2 bg-white text-gray-700 border border-gray-200 rounded-lg font-medium text-xs">
                                        Foto
                                    </button>
                                     <form action="{{ route('reception.skip-assessment', $order->id) }}" method="POST" class="col-span-2" onsubmit="return confirm('Kirim ke Antrian Manifest (Skip Assessment)?')">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center justify-center py-2 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg font-bold text-xs hover:bg-indigo-100 transition-colors">
                                            Siap Kirim 🚚
                                        </button>
                                    </form>
                                    @if(auth()->user()->email === 'admin@workshop.com')
                                        <form action="{{ route('reception.revert', $order->id) }}" method="POST" class="col-span-2" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan status SPK ini ke PENDING (Belum Diterima)?')">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center justify-center py-2 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg font-bold text-xs hover:bg-rose-100 transition-colors">
                                                Batal Terima ↩️
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="text-center py-8 text-gray-500 bg-white rounded-xl border border-gray-100">
                                Belum ada data.
                            </div>
                        @endforelse
                    </div>

                    <div class="hidden lg:block overflow-x-auto rounded-2xl border border-slate-200/80">
                        <table class="min-w-full w-full text-sm text-left text-gray-500">

                            <thead class="text-xs text-slate-700 uppercase bg-slate-50/90 border-b border-slate-200">
                                <tr>
                                    <th scope="col" class="px-5 py-4 w-[1%]">
                                        <input type="checkbox" @change="toggleAll($event)" :checked="selectedItems.length > 0 && selectedItems.length === document.querySelectorAll('.check-item').length" class="rounded-lg border-slate-300 text-emerald-600 shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200">
                                    </th>
                                    <th scope="col" class="px-5 py-4 font-black text-slate-700 tracking-wider">Order & Pelanggan</th>
                                    <th scope="col" class="px-5 py-4 font-black text-slate-700 tracking-wider">Spesifikasi Barang</th>
                                    <th scope="col" class="px-5 py-4 font-black text-slate-700 tracking-wider">Status QC & Aksesoris</th>
                                    <th scope="col" class="px-5 py-4 font-black text-slate-700 tracking-wider text-center">Petugas / CS</th>
                                    <th scope="col" class="px-5 py-4 font-black text-slate-700 tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($orders as $order)
                                    <tr id="spk-desktop-{{ $order->spk_number }}" 
                                        x-data="{ 
                                            isHighlighted: false,
                                            init() {
                                                const urlParams = new URLSearchParams(window.location.search);
                                                if (urlParams.get('highlight') === '{{ $order->spk_number }}') {
                                                    this.isHighlighted = true;
                                                    setTimeout(() => { this.$el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 500);
                                                    setTimeout(() => { this.isHighlighted = false; }, 5000);
                                                }
                                            }
                                        }"
                                        :class="{ 'bg-amber-100/60 border-l-4 border-amber-400 shadow-lg relative z-10' : isHighlighted, 'bg-white hover:bg-slate-50/80' : !isHighlighted }"
                                        class="transition-all duration-300">
                                        
                                        {{-- Checkbox --}}
                                        <td class="px-5 py-4 align-top">
                                            <input type="checkbox" name="ids[]" value="{{ $order->id }}" class="check-item check-item-desktop rounded-lg border-slate-300 text-emerald-600 shadow-sm focus:border-emerald-300 focus:ring focus:ring-emerald-200" @change="updateSelection()">
                                        </td>

                                        {{-- 1. Order & Pelanggan --}}
                                        <td class="px-5 py-4 align-top">
                                            <div class="flex flex-col gap-1.5">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-mono font-black text-emerald-700 text-sm tracking-tight hover:text-emerald-800 flex items-center gap-1 group">
                                                        {{ $order->spk_number }}
                                                        <button type="button" @click="navigator.clipboard.writeText('{{ $order->spk_number }}'); $dispatch('notify', 'No. SPK disalin')" class="text-slate-400 hover:text-emerald-600 opacity-60 hover:opacity-100 transition-opacity" title="Salin No. SPK">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                        </button>
                                                    </span>
                                                    
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <span class="badge-dot bg-emerald-500"></span>
                                                        Diterima
                                                    </span>

                                                    @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express', 'OTO']))
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                                            <span class="badge-dot bg-rose-500 animate-ping"></span>
                                                            {{ $order->priority }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                            Reguler
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="font-black text-slate-900 text-sm leading-tight">
                                                    {{ $order->customer_name }}
                                                </div>

                                                <div class="flex items-center gap-3 text-xs flex-wrap">
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="text-emerald-700 hover:text-emerald-800 font-medium flex items-center gap-1 hover:underline">
                                                        <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.6 1.967.3 3.945 1.511 6.085l-1.615 5.9 5.908-1.616zM18.312 14.5c-.266-.133-1.574-.776-1.817-.866-.234-.088-.352-.108-.501.121-.148.229-.588.751-.722.906-.134.156-.269.176-.534.043-.267-.133-1.127-.415-2.147-1.324-.795-.71-1.332-1.585-1.488-1.852-.155-.267-.016-.411.117-.544.119-.119.267-.311.4-.466.134-.155.177-.267.267-.445.089-.177.045-.333-.022-.467-.067-.133-.602-1.448-.824-1.983-.215-.515-.434-.445-.595-.453-.155-.008-.333-.008-.511-.008-.178 0-.467.067-.711.333-.244.267-.933.911-.933 2.222s.955 2.578 1.088 2.756c.133.178 1.881 2.871 4.557 4.026 2.676 1.155 2.676.769 3.167.724.488-.044 1.574-.643 1.797-1.264.221-.621.221-1.153.155-1.264-.067-.111-.244-.178-.511-.311zm-4.433 1.458z"/></svg>
                                                        {{ $order->customer_phone }}
                                                    </a>
                                                    
                                                    <span class="text-slate-300">•</span>
                                                    
                                                    <span class="text-slate-500 font-medium">
                                                        Masuk: <strong class="text-slate-700">{{ $order->entry_date ? $order->entry_date->format('d/m/Y') : '-' }}</strong>
                                                    </span>
                                                    
                                                    @if($order->estimation_date)
                                                        <span class="text-slate-300">•</span>
                                                        <span class="text-slate-500 font-medium">
                                                            Est: <strong class="text-slate-700">{{ $order->estimation_date->format('d/m/Y') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="email-container-{{ $order->id }} pt-0.5">
                                                    @if($order->customer_email)
                                                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                            <span class="truncate max-w-[160px]" title="{{ $order->customer_email }}">{{ $order->customer_email }}</span>
                                                            <button type="button" onclick="openEditEmailModal('{{ $order->id }}', '{{ $order->customer_email }}')" class="text-slate-400 hover:text-emerald-600 transition-colors" title="Edit Email">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <button type="button" onclick="openEditEmailModal('{{ $order->id }}', '')" class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400 hover:text-emerald-600 transition-colors">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                            Tambah Email
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                                           {{-- 2. Spesifikasi Barang --}}
                                        <td class="px-5 py-4 align-top">
                                            <div class="flex items-start gap-3">
                                                <div class="p-2.5 bg-amber-50 rounded-xl text-amber-600 border border-amber-200/60 shadow-sm flex-shrink-0">
                                                    @php
                                                        $cat = strtolower($order->category ?? '');
                                                    @endphp
                                                    @if(str_contains($cat, 'tas') || str_contains($cat, 'bag') || str_contains($cat, 'dompet'))
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                                    @elseif(str_contains($cat, 'topi') || str_contains($cat, 'head') || str_contains($cat, 'helm'))
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582"/></svg>
                                                    @else
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                    @endif
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    @if($order->shoe_brand || $order->shoe_size)
                                                        <div class="flex flex-col gap-1">
                                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                                <span class="font-black text-slate-900 uppercase tracking-tight text-sm truncate">{{ $order->shoe_brand ?? 'NO BRAND' }}</span>
                                                                <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 text-[9px] font-black rounded border border-emerald-200 uppercase">CS OK</span>
                                                            </div>
                                                            <div class="text-xs text-slate-500 font-medium">
                                                                {{ strtoupper($order->category ?? 'Sepatu') }} • {{ $order->shoe_color ?? '-' }} • Size: <strong class="text-slate-800">{{ $order->shoe_size ?? '-' }}</strong>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mt-2">
                                                            <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '{{ $order->shoe_brand }}', '{{ $order->shoe_size }}', '{{ $order->shoe_color }}', '{{ $order->category }}')" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50/70 hover:bg-emerald-100/70 border border-emerald-200/80 px-2.5 py-1 rounded-lg transition-colors">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                                Ubah Detail
                                                            </button>
                                                        </div>
                                                    @else
                                                        <button type="button" onclick="openEditShoeModal('{{ $order->id }}', '', '', '', '')" class="inline-flex items-center gap-1.5 text-xs text-amber-700 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 border border-dashed border-amber-300 px-3 py-1.5 rounded-xl font-bold transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                            Lengkapi Barang
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 3. Status QC & Aksesoris --}}
                                        <td class="px-5 py-4 align-top">
                                            <div class="space-y-2.5">
                                                @php
                                                    $qcStatus = $order->warehouse_qc_status;
                                                    if (!$qcStatus && !is_null($order->reception_qc_passed)) {
                                                        $qcStatus = $order->reception_qc_passed ? 'lolos' : 'reject';
                                                    }
                                                    $qcNotes = $order->warehouse_qc_notes ?? $order->reception_rejection_reason;
                                                    
                                                    $tali = $order->accessories_tali ?? ($order->accessories_data['tali'] ?? null);
                                                    $insole = $order->accessories_insole ?? ($order->accessories_data['insole'] ?? null);
                                                    $box = $order->accessories_box ?? ($order->accessories_data['box'] ?? null);
                                                    $hasAcc = $tali || $insole || $box || !empty($order->accessories_data['lainnya']) || $order->accessories_other;
                                                @endphp

                                                {{-- QC Badge --}}
                                                <div>
                                                    @if($qcStatus === 'lolos')
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                            QC PASSED
                                                        </span>
                                                    @elseif($qcStatus === 'reject')
                                                        @if($order->status != \App\Enums\WorkOrderStatus::HOLD_FOR_CX->value)
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-black bg-blue-50 text-blue-700 border border-blue-200 shadow-sm" title="Fisik reject disetujui customer">
                                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                ACC CUSTOMER
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-black bg-rose-50 text-rose-700 border border-rose-200 shadow-sm" title="Alasan: {{ $qcNotes }}">
                                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                REJECTED
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Belum QC Fisik
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- Aksesoris Pills --}}
                                                @if($hasAcc)
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        @php
                                                            $isSimpan = fn($val) => in_array(strtolower($val ?? ''), ['simpan', 's', 'disimpan']);
                                                            $isNempel = fn($val) => in_array(strtolower($val ?? ''), ['nempel', 'n', 'menempel']);
                                                        @endphp

                                                        {{-- Tali --}}
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $isSimpan($tali) ? 'bg-blue-50 text-blue-700 border-blue-200' : ($isNempel($tali) ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-50 text-slate-400 border-slate-200') }}" title="Tali Sepatu: {{ $tali ?? 'Tidak Ada' }}">
                                                            <span>Tali:</span>
                                                            <strong class="uppercase">{{ $isSimpan($tali) ? 'Simpan' : ($isNempel($tali) ? 'Nempel' : 'Tidak Ada') }}</strong>
                                                        </span>

                                                        {{-- Insole --}}
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $isSimpan($insole) ? 'bg-blue-50 text-blue-700 border-blue-200' : ($isNempel($insole) ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-50 text-slate-400 border-slate-200') }}" title="Insole: {{ $insole ?? 'Tidak Ada' }}">
                                                            <span>Insole:</span>
                                                            <strong class="uppercase">{{ $isSimpan($insole) ? 'Simpan' : ($isNempel($insole) ? 'Nempel' : 'Tidak Ada') }}</strong>
                                                        </span>

                                                        {{-- Box --}}
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $isSimpan($box) ? 'bg-blue-50 text-blue-700 border-blue-200' : ($isNempel($box) ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-50 text-slate-400 border-slate-200') }}" title="Box: {{ $box ?? 'Tidak Ada' }}">
                                                            <span>Box:</span>
                                                            <strong class="uppercase">{{ $isSimpan($box) ? 'Simpan' : ($isNempel($box) ? 'Nempel' : 'Tidak Ada') }}</strong>
                                                        </span>

                                                        @if(!empty($order->accessories_data['lainnya']) || $order->accessories_other)
                                                            <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-medium bg-amber-50 text-amber-800 border border-amber-200 italic" title="{{ $order->accessories_other ?? $order->accessories_data['lainnya'] }}">
                                                                + {{ Str::limit($order->accessories_other ?? $order->accessories_data['lainnya'], 15) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-[11px] text-slate-400 italic">Aksesoris belum dicatat</div>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 4. Petugas / CS --}}
                                        <td class="px-5 py-4 align-top text-center">
                                            @php
                                                $parts = explode('-', $order->spk_number);
                                                $csCode = end($parts);
                                                if(strlen($csCode) > 3) $csCode = $order->creator->cs_code ?? 'XX';
                                                $creatorName = $order->creator->name ?? 'CS Online';
                                            @endphp
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="inline-flex px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200/80 rounded-xl text-xs font-black shadow-sm" title="CS: {{ $creatorName }}">
                                                    {{ $csCode }}
                                                </span>
                                                <span class="text-[10px] text-slate-400 font-medium truncate max-w-[90px]" title="{{ $creatorName }}">
                                                    {{ $creatorName }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- 5. Aksi (Pro Max Action Hub) --}}
                                        <td class="px-5 py-4 align-top text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                {{-- Primary Action: Proses (Form QC) --}}
                                                <a href="{{ route('reception.show', $order->id) }}" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm shadow-emerald-600/20 active:scale-95">
                                                    <span>Proses QC</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                </a>

                                                {{-- Secondary Action: Print SPK --}}
                                                <a href="{{ route('reception.print-spk', $order->id) }}" target="_blank"
                                                   class="p-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl transition-all shadow-sm active:scale-95" 
                                                   title="Print SPK">
                                                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                </a>

                                                {{-- Action Modal Trigger (Titik Tiga / Menu Aksi Lengkap) --}}
                                                <button type="button" 
                                                        @click="openQuickAction({{ json_encode([
                                                            'id' => $order->id,
                                                            'spk_number' => $order->spk_number,
                                                            'customer_name' => $order->customer_name,
                                                            'customer_phone' => $order->customer_phone,
                                                            'customer_email' => $order->customer_email,
                                                            'shoe_brand' => $order->shoe_brand ?? 'NO BRAND',
                                                            'shoe_size' => $order->shoe_size ?? '-',
                                                            'category' => strtoupper($order->category ?? 'Sepatu'),
                                                            'has_customer_email' => !empty($order->customer_email),
                                                            'manifest_url' => route('reception.skip-assessment', $order->id),
                                                            'print_tag_url' => route('reception.print-tag', $order->id),
                                                            'revert_url' => route('reception.revert', $order->id),
                                                            'destroy_url' => route('reception.destroy', $order->id),
                                                            'order_data' => $order
                                                        ]) }})"
                                                        class="p-1.5 bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-xl transition-all shadow-sm active:scale-95" 
                                                        title="Buka Menu Aksi Lengkap">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 bg-slate-50/50">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                                </div>
                                                <p class="font-bold text-slate-700 text-sm">Belum ada barang diterima di rak transit.</p>
                                                <p class="text-xs text-slate-400 mt-0.5">Silahkan verifikasi dan terima SPK dari Tab "SPK Masuk (Pending CS)".</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                {{-- Floating Bulk Action Bar --}}
                <div x-show="selectedItems.length > 0" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-y-full opacity-0 scale-95"
                     x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="translate-y-0 opacity-100 scale-100"
                     x-transition:leave-end="translate-y-full opacity-0 scale-95"
                     class="fixed bottom-6 inset-x-0 z-[9999] flex flex-col items-center gap-2 px-4">
                    
                    {{-- Select All Across Pages Banner --}}
                    <div x-show="!selectAllMode && selectedItems.length === pageRecords && totalRecords > pageRecords" 
                         class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2 animate-bounce">
                        <span>Anda memilih {{ $orders->count() }} data di halaman ini.</span>
                        <button @click="selectAllAcrossPages()" class="underline font-bold hover:text-blue-100">
                            Pilih seluruh <span x-text="totalRecords"></span> data?
                        </button>
                    </div>

                    {{-- Main Action Bar --}}
                    <div class="bg-white/90 backdrop-blur-md border border-gray-200 shadow-2xl rounded-2xl p-4 w-full max-w-2xl flex flex-col sm:flex-row items-center justify-between gap-4 ring-1 ring-black/5">
                        <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-start">
                            <div class="flex items-center gap-2 bg-teal-100 px-3 py-1.5 rounded-lg text-teal-700">
                                <span class="text-xs font-bold uppercase tracking-wider">Terpilih</span>
                                <span class="bg-teal-600 text-white px-2 py-0.5 rounded-md font-bold text-sm" x-text="selectAllMode ? totalRecords : selectedItems.length"></span>
                            </div>
                            <button @click="clearSelection()" type="button" class="text-xs font-bold text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">
                                Batal
                            </button>
                        </div>

                        <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            {{-- Bulk Delete --}}
                            <button type="button" 
                                    onclick="submitBulkDelete()" 
                                    class="flex-1 sm:flex-none bg-red-100 hover:bg-red-200 text-red-600 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest shadow-sm hover:shadow transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus
                            </button>

                            {{-- Bulk Prep --}}
                            <button type="button" 
                                    onclick="bulkDirectToPrep()" 
                                    class="flex-1 sm:flex-none bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:shadow-indigo-200 transition-all flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                To Manifest
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Order Actions (Photos & Forms) --}}
                @foreach($orders as $order)
                    @include('reception.partials.order-actions', ['order' => $order])
                @endforeach
                
                {{-- Pagination Info and Links --}}
                @if($orders->total() > 0)
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-xs text-gray-500">
                        Menampilkan <span class="font-semibold text-gray-700">{{ $orders->firstItem() }}</span> 
                        sampai <span class="font-semibold text-gray-700">{{ $orders->lastItem() }}</span> 
                        dari <span class="font-semibold text-gray-700">{{ $orders->total() }}</span> data
                    </div>
                    
                    <div class="flex justify-center">
                        {{ $orders->links() }}
                    </div>
                </div>
                @endif
                    </div> {{-- End Tab 2: Received --}}

                    {{-- TAB 3: PROCESSED ORDERS --}}
                    <div x-show="activeTab === 'processed'" x-transition class="p-6 sm:p-8" x-data="{
                        search: '',
                        priorityFilter: '',
                        qcFilter: '',
                        isVisible(orderSpk, orderName, orderPhone, orderBrand, orderPriority, orderQc) {
                            const searchLower = this.search.toLowerCase();
                            const matchesSearch = orderSpk.toLowerCase().includes(searchLower) ||
                                                orderName.toLowerCase().includes(searchLower) ||
                                                orderPhone.toLowerCase().includes(searchLower) ||
                                                orderBrand.toLowerCase().includes(searchLower);
                            
                            const matchesPriority = this.priorityFilter === '' || orderPriority === this.priorityFilter;
                            
                            // Map QC status for easier filtering if needed, or exact match
                            // orderQc value is likely 'lolos' or something else
                            const matchesQc = this.qcFilter === '' || orderQc === this.qcFilter;

                            return matchesSearch && matchesPriority && matchesQc;
                        }
                    }">
                        {{-- Toolbar Filter & Search --}}
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6">
                            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                                {{-- Search Bar --}}
                                <div class="w-full md:w-96 relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-teal-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" x-model="search" placeholder="Cari SPK, Nama, atau Brand..." 
                                        class="pl-10 w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm transition-all focus:bg-white"
                                    >
                                </div>

                                {{-- Filters --}}
                                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                        </div>
                                        <select x-model="priorityFilter" class="pl-9 pr-8 py-2.5 w-full sm:w-40 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 cursor-pointer hover:bg-white transition-colors">
                                            <option value="">Semua Prioritas</option>
                                            <option value="Reguler">Reguler</option>
                                            <option value="Prioritas">Prioritas</option>
                                            <option value="Urgent">Urgent</option>
                                            <option value="Express">Express</option>
                                        </select>
                                    </div>

                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <select x-model="qcFilter" class="pl-9 pr-8 py-2.5 w-full sm:w-40 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 cursor-pointer hover:bg-white transition-colors">
                                            <option value="">Semua Status QC</option>
                                            <option value="lolos">Lolos QC</option>
                                            <option value="reject">Tidak Lolos</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($processedOrders->isEmpty())
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada Order yang Diproses</h3>
                                <p class="mt-1 text-sm text-gray-500">Order yang sudah dilakukan QC Gudang akan muncul di sini.</p>
                            </div>
                        @else
                    {{-- Mobile Card View Processed --}}
                    <div class="block lg:hidden space-y-4 mb-4">
                            @foreach($processedOrders as $order)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4" 
                                x-show="isVisible('{{ $order->spk_number }}', '{{ addslashes($order->customer_name) }}', '{{ $order->customer_phone }}', '{{ addslashes($order->shoe_brand) }}', '{{ $order->priority }}', '{{ $order->warehouse_qc_status }}')">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <div class="font-bold text-teal-600 text-sm">{{ $order->spk_number }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $order->warehouse_qc_at ? $order->warehouse_qc_at->format('d M Y H:i') : '-' }}</div>
                                    </div>
                                    <div>
                                        @if($order->status === \App\Enums\WorkOrderStatus::WAITING_PAYMENT->value)
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[9px] font-bold uppercase">Finance</span>
                                        @elseif($order->status === \App\Enums\WorkOrderStatus::CX_FOLLOWUP->value)
                                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-[9px] font-bold uppercase">Follow Up</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-[9px] font-bold uppercase">{{ $order->status }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h4 class="font-bold text-gray-900">{{ $order->customer_name }}</h4>
                                    <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                                </div>

                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 mb-3 flex gap-3 items-center">
                                    <div class="p-2 bg-white rounded border border-gray-200 shadow-sm text-center min-w-[40px]">
                                        @php
                                            $cat = strtolower($order->category ?? '');
                                        @endphp
                                        @if(str_contains($cat, 'tas') || str_contains($cat, 'bag') || str_contains($cat, 'dompet'))
                                            <span class="text-lg">👜</span>
                                        @elseif(str_contains($cat, 'topi') || str_contains($cat, 'head') || str_contains($cat, 'helm'))
                                            <span class="text-lg">🧢</span>
                                        @else
                                            <span class="text-lg">👟</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 grid grid-cols-2 gap-2">
                                        <div>
                                            <span class="text-[10px] text-gray-400 block uppercase font-bold">Item & Size</span>
                                            <span class="text-xs font-black text-gray-800">{{ $order->shoe_brand ?? '-' }} ({{ $order->shoe_size ?? '-' }})</span>
                                            @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express', 'OTO']))
                                                <span class="block text-[8px] text-red-600 font-bold uppercase mt-0.5">{{ $order->priority }}</span>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] text-gray-400 block uppercase font-bold">QC Status</span>
                                            @if($order->warehouse_qc_status === 'lolos')
                                                <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-[9px] font-bold uppercase border border-green-200">Lolos</span>
                                            @else
                                                <span class="inline-block px-2 py-0.5 bg-red-100 text-red-700 rounded text-[9px] font-bold uppercase border border-red-200">Reject</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" 
                                            onclick='openDetailModal(@json($order), @json($order->workOrderServices), @json($order->accessories_data))'
                                            class="flex items-center justify-center py-2 bg-gray-100 text-gray-700 rounded-lg font-bold text-xs border border-gray-200 hover:bg-gray-200">
                                        Lihat Detail
                                    </button>
                                    <a href="{{ route('reception.print-spk', $order->id) }}" target="_blank"
                                        class="flex items-center justify-center py-2 bg-[#22AF85]/10 text-[#22AF85] rounded-lg font-bold text-xs border border-[#22AF85]/20 hover:bg-[#22AF85]/20">
                                        Print SPK
                                    </a>
                                </div>
                            </div>
                            @endforeach
                    </div>

                    <div class="hidden lg:block overflow-x-auto rounded-2xl border border-slate-200/80">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                        <tr>
                                            <th class="px-4 py-3">SPK / Tgl Proses</th>
                                            <th class="px-4 py-3">Customer</th>
                                            <th class="px-4 py-3">Info Item</th>
                                            <th class="px-4 py-3">Status QC</th>
                                            <th class="px-4 py-3">Posisi Order</th>
                                            <th class="px-4 py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($processedOrders as $order)
                                        <tr class="hover:bg-gray-50 transition-colors"
   x-show="isVisible('{{ $order->spk_number }}', '{{ addslashes($order->customer_name) }}', '{{ $order->customer_phone }}', '{{ addslashes($order->shoe_brand) }}', '{{ $order->priority }}', '{{ $order->warehouse_qc_status }}')">
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-[#22AF85]">{{ $order->spk_number }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $order->warehouse_qc_at ? $order->warehouse_qc_at->format('d M Y H:i') : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $order->customer_name }}</div>
                                                <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="p-1.5 bg-gray-50 rounded border border-gray-100 text-gray-400">
                                                        @php
                                                            $cat = strtolower($order->category ?? '');
                                                        @endphp
                                                        @if(str_contains($cat, 'tas') || str_contains($cat, 'bag') || str_contains($cat, 'dompet'))
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                                        @elseif(str_contains($cat, 'topi') || str_contains($cat, 'head') || str_contains($cat, 'helm'))
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582"></path></svg>
                                                        @else
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <span class="font-bold text-gray-800 text-sm">{{ $order->shoe_brand ?? '-' }} - {{ $order->shoe_color ?? '-' }}</span>
                                                            @if(in_array($order->priority, ['Prioritas', 'Urgent', 'Express', 'OTO']))
                                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-600 border border-red-200 uppercase tracking-wide">
                                                                    {{ $order->priority }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-xs text-gray-600 mb-1.5 font-medium">{{ strtoupper($order->category ?? 'Item') }} | Size: {{ $order->shoe_size ?? '-' }}</div>
                                                        
                                                        <div class="inline-flex items-center gap-1 text-[#22AF85] text-[10px] font-bold cursor-pointer hover:underline" onclick='openDetailModal(@json($order), @json($order->workOrderServices), @json($order->accessories_data))'>
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ $order->workOrderServices->count() }} Layanan
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($order->warehouse_qc_status === 'lolos')
                                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase border border-green-200">Lolos QC</span>
                                                @else
                                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase border border-red-200">Tidak Lolos</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($order->status === \App\Enums\WorkOrderStatus::WAITING_PAYMENT->value)
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase border border-blue-200">Finance (Pembayaran)</span>
                                                @elseif($order->status === \App\Enums\WorkOrderStatus::CX_FOLLOWUP->value)
                                                    <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-[10px] font-bold uppercase border border-orange-200">CX (Follow Up)</span>
                                                @else
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-[10px] font-bold uppercase border border-gray-200">{{ $order->status }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button type="button" 
                                                            onclick='openDetailModal(@json($order), @json($order->workOrderServices), @json($order->accessories_data))'
                                                            class="inline-block px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-xs font-bold transition-all border border-gray-200">
                                                        Lihat Detail
                                                    </button>
                                                    <a href="{{ route('reception.print-spk', $order->id) }}" target="_blank"
                                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#22AF85]/10 text-[#22AF85] rounded border border-[#22AF85]/20 hover:bg-[#22AF85]/20 transition-all text-xs font-bold uppercase tracking-wider">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                        Print SPK
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination for Processed Orders -->
                            <div class="mt-4 border-t border-gray-100 pt-4 px-2">
                                {{ $processedOrders->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ======================================================== --}}
                {{-- QUICK ACTION MODAL (PRO MAX SAAS STANDARD)               --}}
                {{-- ======================================================== --}}
                <div x-show="actionModalOpen" 
                     x-cloak
                     class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
                     role="dialog" 
                     aria-modal="true">
                    
                    {{-- Backdrop Blur --}}
                    <div x-show="actionModalOpen"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click="closeQuickAction()"
                         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

                    {{-- Modal Content Card --}}
                    <div x-show="actionModalOpen"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                         @keydown.escape.window="closeQuickAction()"
                         class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all z-10 my-8">
                        
                        {{-- Header Modal --}}
                        <div class="px-6 py-5 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-black text-sm text-emerald-400" x-text="actionOrder?.spk_number"></span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-400/20 text-emerald-200 border border-emerald-300/30">Diterima</span>
                                    </div>
                                    <h4 class="font-bold text-white text-base leading-tight mt-0.5 truncate max-w-[280px]" x-text="actionOrder?.customer_name"></h4>
                                </div>
                            </div>
                            <button type="button" @click="closeQuickAction()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Sub-header Context --}}
                        <div class="px-6 py-2.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-slate-700 uppercase" x-text="actionOrder?.shoe_brand"></span>
                                <span>•</span>
                                <span x-text="actionOrder?.category"></span>
                                <span>•</span>
                                <span>Size: <strong class="text-slate-800" x-text="actionOrder?.shoe_size"></strong></span>
                            </div>
                            <div class="text-[11px] text-slate-400" x-text="actionOrder?.customer_phone"></div>
                        </div>

                        {{-- Action Grid --}}
                        <div class="p-6 space-y-2.5 max-h-[60vh] overflow-y-auto">
                            {{-- 1. Dokumentasi Foto --}}
                            <button type="button" 
                                    @click="closeQuickAction(); $dispatch('open-photo-modal-' + actionOrder.id)" 
                                    class="w-full flex items-center gap-3.5 p-3 rounded-2xl border border-slate-200/80 hover:border-emerald-500 hover:bg-emerald-50/40 transition-all text-left group">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-xs text-slate-800 group-hover:text-emerald-900">Dokumentasi Foto Fisik</div>
                                    <div class="text-[11px] text-slate-400">Unggah foto kondisi awal sebelum masuk ke pencucian/reparasi</div>
                                </div>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            {{-- 2. Print Tag Sepatu --}}
                            <a :href="actionOrder?.print_tag_url" target="_blank"
                               class="w-full flex items-center gap-3.5 p-3 rounded-2xl border border-slate-200/80 hover:border-blue-500 hover:bg-blue-50/40 transition-all text-left group">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-xs text-slate-800 group-hover:text-blue-900">Cetak Tag Sepatu (Label Rak)</div>
                                    <div class="text-[11px] text-slate-400">Cetak barcode gantung untuk dipasangkan pada fisik sepatu</div>
                                </div>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>

                            {{-- 3. Edit Data Order --}}
                            <button type="button" 
                                    @click="closeQuickAction(); openEditOrderModal(actionOrder.order_data)" 
                                    class="w-full flex items-center gap-3.5 p-3 rounded-2xl border border-slate-200/80 hover:border-amber-500 hover:bg-amber-50/40 transition-all text-left group">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-xs text-slate-800 group-hover:text-amber-900">Koreksi Data Order</div>
                                    <div class="text-[11px] text-slate-400">Ubah identitas pelanggan, nomor kontak WhatsApp, atau detail SPK</div>
                                </div>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-amber-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            {{-- 4. Kirim ke Manifest (Skip Assessment) --}}
                            <form :action="actionOrder?.manifest_url" method="POST" onsubmit="return confirm('Kirim ke Antrian Manifest (Skip Assessment)?')">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3.5 p-3 rounded-2xl border border-slate-200/80 hover:border-indigo-500 hover:bg-indigo-50/40 transition-all text-left group">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                                        <span class="text-lg">🚚</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-xs text-slate-800 group-hover:text-indigo-900">Kirim ke Manifest (Siap Antar)</div>
                                        <div class="text-[11px] text-slate-400">Lewati assessment teknis dan langsung siapkan pengiriman</div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </form>

                            {{-- 5. Kirim Nota Digital via Email (Conditional) --}}
                            <template x-if="actionOrder?.has_customer_email">
                                <button type="button" 
                                        @click="closeQuickAction(); sendEmailNotification(actionOrder.id)" 
                                        class="w-full flex items-center gap-3.5 p-3 rounded-2xl border border-slate-200/80 hover:border-sky-500 hover:bg-sky-50/40 transition-all text-left group">
                                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-xs text-slate-800 group-hover:text-sky-900">Kirim Nota via Email</div>
                                        <div class="text-[11px] text-slate-400" x-text="'Kirim salinan nota digital ke ' + actionOrder?.customer_email"></div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-sky-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </template>

                            {{-- 6. Batal Terima (Revert to Pending) - Admin/Owner Only --}}
                            @if(auth()->user()->email === 'admin@workshop.com' || auth()->user()->isAdmin() || auth()->user()->isOwner())
                                <form :action="actionOrder?.revert_url" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan status SPK ini ke PENDING (Belum Diterima)?')">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3.5 p-3 rounded-2xl border border-amber-200/80 bg-amber-50/40 hover:bg-amber-100/50 hover:border-amber-300 transition-all text-left group">
                                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center group-hover:scale-105 transition-transform">
                                            <span class="text-base">↩️</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-xs text-amber-900">Batal Terima (Kembalikan ke CS)</div>
                                            <div class="text-[11px] text-amber-700/80">Batalkan penerimaan fisik dan kembalikan order ke antrian pending</div>
                                        </div>
                                        <svg class="w-4 h-4 text-amber-400 group-hover:text-amber-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </form>
                            @endif

                            {{-- 7. Hapus Order (Delete) --}}
                            @can('deleteReception', \App\Models\WorkOrder::class)
                                <form :action="actionOrder?.destroy_url" method="POST" onsubmit="return confirm('Hapus data order ini secara permanen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full flex items-center gap-3.5 p-3 rounded-2xl border border-rose-200/80 bg-rose-50/30 hover:bg-rose-100/50 hover:border-rose-300 transition-all text-left group">
                                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-xs text-rose-700">Hapus Data SPK</div>
                                            <div class="text-[11px] text-rose-500/80">Hapus pesanan ini secara permanen dari basis data workshop</div>
                                        </div>
                                        <svg class="w-4 h-4 text-rose-400 group-hover:text-rose-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </form>
                            @endcan
                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">Klik di luar atau tombol tutup untuk keluar</span>
                            <button type="button" @click="closeQuickAction()" class="px-4 py-1.5 bg-slate-200/80 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold transition-colors">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
        @include('reception.partials.modals')
    @endpush

    @push('scripts')
        @include('reception.partials.scripts')
        <script>
            function formatSPKMask(val) {
                // Bersihkan karakter non-alphanumeric
                let raw = val.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
                let formatted = '';
                
                if (raw.length > 0) formatted += raw.charAt(0); // Tipe (S/T/H/A/L)
                if (raw.length > 1) formatted += '-' + raw.substring(1, Math.min(raw.length, 5)); // YYMM
                if (raw.length > 5) formatted += '-' + raw.substring(5, Math.min(raw.length, 7)); // DD
                if (raw.length > 7) formatted += '-' + raw.substring(7, Math.min(raw.length, 11)); // Seq
                if (raw.length > 11) formatted += '-' + raw.substring(11, Math.min(raw.length, 15)); // CS Code
                
                return formatted;
            }
        </script>
    @endpush
    
</x-app-layout>
