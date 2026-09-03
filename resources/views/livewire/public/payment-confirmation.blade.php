<div class="w-full max-w-lg mx-auto px-4 py-5 sm:py-8" 
     x-data="{
         scanMode: 'file', // 'camera' or 'file'
         cameraActive: false,
         html5QrCode: null,
         rawAmount: @entangle('amount'),
         formattedAmount: '',
         isScanning: false,

         formatRupiah(val) {
             let numberString = val.replace(/[^,\d]/g, '').toString();
             let split = numberString.split(',');
             let sisa = split[0].length % 3;
             let rupiah = split[0].substr(0, sisa);
             let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

             if (ribuan) {
                 let separator = sisa ? '.' : '';
                 rupiah += separator + ribuan.join('.');
             }

             rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
             return rupiah ? 'Rp ' + rupiah : '';
         },

         onAmountInput(e) {
             let clean = e.target.value.replace(/[^0-9]/g, '');
             this.rawAmount = clean;
             this.formattedAmount = clean ? this.formatRupiah(clean) : '';
         },

         startCamera() {
             this.scanMode = 'camera';
             this.cameraActive = true;
             this.$nextTick(() => {
                 if (!this.html5QrCode) {
                     this.html5QrCode = new Html5Qrcode('qr-reader-portal');
                 }
                 const config = { fps: 10, qrbox: { width: 220, height: 220 } };
                 this.html5QrCode.start(
                     { facingMode: 'environment' },
                     config,
                     (decodedText) => {
                         this.stopCamera();
                         @this.loadInvoice(decodedText);
                     },
                     (errorMessage) => {
                         // Scanning frame error, keep silent
                     }
                 ).catch(err => {
                     console.error('Camera start error:', err);
                     alert('Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.');
                     this.cameraActive = false;
                 });
             });
         },

         stopCamera() {
             if (this.html5QrCode && this.cameraActive) {
                 this.html5QrCode.stop().then(() => {
                     this.cameraActive = false;
                 }).catch(err => console.error(err));
             } else {
                 this.cameraActive = false;
             }
         },

         scanQrFile(e) {
             const file = e.target.files[0];
             if (!file) return;
             this.isScanning = true;
             const html5QrCode = new Html5Qrcode('qr-file-processor');
             html5QrCode.scanFile(file, true)
                 .then(decodedText => {
                     this.isScanning = false;
                     @this.loadInvoice(decodedText);
                 })
                 .catch(err => {
                     this.isScanning = false;
                     console.error('File scan error:', err);
                     alert('QR Code tidak terdeteksi pada gambar. Pastikan gambar QR jelas dan tidak terpotong.');
                 });
         },

         proofPreview: null,
         isCompressing: false,
         originalSizeKb: null,
         compressedSizeKb: null,

         compressAndUploadProof(e) {
             const file = e.target.files[0];
             if (!file) return;

             // Instant local preview for immediate visual feedback
             this.proofPreview = URL.createObjectURL(file);
             this.isCompressing = true;
             this.originalSizeKb = (file.size / 1024).toFixed(0);

             const reader = new FileReader();
             reader.onload = (event) => {
                 const img = new Image();
                 img.onload = () => {
                     const canvas = document.createElement('canvas');
                     let width = img.width;
                     let height = img.height;
                     const maxDim = 1200;

                     if (width > height) {
                         if (width > maxDim) {
                             height = Math.round((height * maxDim) / width);
                             width = maxDim;
                         }
                     } else {
                         if (height > maxDim) {
                             width = Math.round((width * maxDim) / height);
                             height = maxDim;
                         }
                     }

                     canvas.width = width;
                     canvas.height = height;
                     const ctx = canvas.getContext('2d');
                     ctx.drawImage(img, 0, 0, width, height);

                     canvas.toBlob((blob) => {
                         if (!blob) {
                             this.isCompressing = false;
                             return;
                         }
                         this.compressedSizeKb = (blob.size / 1024).toFixed(0);
                         this.proofPreview = URL.createObjectURL(blob);

                         const compressedFile = new File([blob], 'proof_' + Date.now() + '.jpg', {
                             type: 'image/jpeg',
                             lastModified: Date.now()
                         });

                         @this.upload('proof_image', compressedFile, 
                             () => {
                                 this.isCompressing = false;
                             }, 
                             () => {
                                 this.isCompressing = false;
                                 alert('Gagal mengunggah foto bukti.');
                             }
                         );
                     }, 'image/jpeg', 0.75); // 75% Quality
                 };
                 img.src = event.target.result;
             };
             reader.readAsDataURL(file);
         },

         removeProof() {
             this.proofPreview = null;
             this.compressedSizeKb = null;
             this.originalSizeKb = null;
             const input = document.getElementById('proof_file_input');
             if (input) input.value = '';
             @this.set('proof_image', null);
         },

         copyText(text) {
             navigator.clipboard.writeText(text);
             Swal.fire({
                 toast: true,
                 position: 'top-end',
                 icon: 'success',
                 title: 'Nomor rekening ' + text + ' berhasil disalin!',
                 showConfirmButton: false,
                 timer: 2000,
                 background: '#FFFFFF',
                 color: '#0F172A',
                 customClass: {
                     popup: 'rounded-2xl shadow-xl border border-slate-100 text-xs font-bold'
                 }
             });
         }
     }"
     x-init="
         if (rawAmount) {
             formattedAmount = formatRupiah(rawAmount.toString());
         }
     ">

    {{-- Hidden element for file scanning --}}
    <div id="qr-file-processor" style="display: none;"></div>

    @if($isSubmitted)
        {{-- ======================================================== --}}
        {{-- 3. STATE SUKSES: BUKTI PEMBAYARAN TERKIRIM --}}
        {{-- ======================================================== --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/60 border border-slate-100 text-center space-y-6 animate-fade-in">
            <div class="w-16 h-16 bg-emerald-50 border-2 border-[#22AF85] text-[#22AF85] rounded-full flex items-center justify-center mx-auto text-3xl shadow-lg shadow-emerald-500/10 animate-bounce">
                ✓
            </div>
            
            <div class="space-y-1.5">
                <span class="inline-block px-3 py-1 bg-emerald-50 text-[#22AF85] text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-200">
                    Bukti Pembayaran Terkirim
                </span>
                <h2 class="text-xl sm:text-2xl font-black font-poppins text-slate-900">
                    Terima Kasih!
                </h2>
                <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                    Bukti pembayaran untuk Invoice <strong class="text-[#22AF85] font-mono">{{ $invoice?->invoice_number ?? '-' }}</strong> telah berhasil diunggah dan sedang dalam antrean verifikasi tim Finance.
                </p>
            </div>

            <div class="bg-[#F8FAFC] rounded-2xl p-4 border border-slate-200/80 text-left space-y-2.5 text-xs">
                <div class="flex justify-between items-center text-slate-500">
                    <span>Nominal Ditransfer:</span>
                    <strong class="text-[#22AF85] text-sm font-black font-mono">Rp {{ number_format(preg_replace('/[^0-9]/', '', (string)$amount), 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between items-center text-slate-500">
                    <span>Bank Tujuan:</span>
                    <span class="text-slate-800 font-black">{{ $payment_method }} (PT. Terang Garam Solusindo)</span>
                </div>
                <div class="flex justify-between items-center text-slate-500">
                    <span>Waktu Unggah:</span>
                    <span class="text-slate-700 font-medium">{{ now()->format('d M Y, H:i') }} WIB</span>
                </div>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row gap-3 justify-center">
                @if($invoice)
                    <a href="{{ url('/api/invoice_share_grouped.php?token=' . urlencode($invoice->invoice_number)) }}" target="_blank"
                       class="w-full sm:w-auto px-6 py-3.5 bg-[#FFC232] hover:bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-amber-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Lihat Lembar Invoice
                    </a>
                @endif
                <button type="button" wire:click="resetInvoice"
                        class="w-full sm:w-auto px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-2xl transition-all active:scale-95">
                    Kirim Bukti Lain
                </button>
            </div>
        </div>

    @elseif(!$invoice)
        {{-- ======================================================== --}}
        {{-- 1. STATE IDENTIFIKASI INVOICE: SCAN / UPLOAD QR --}}
        {{-- ======================================================== --}}
        <div class="space-y-4">
            {{-- Title Hero Mobile --}}
            <div class="text-center mb-5">
                <h1 class="text-xl sm:text-2xl font-black font-poppins text-slate-900 tracking-tight">
                    Konfirmasi Pembayaran
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Scan atau upload gambar QR Code yang ada di lembar Invoice Anda
                </p>
            </div>

            <div class="bg-white rounded-3xl p-5 sm:p-7 shadow-xl shadow-slate-200/60 border border-slate-100 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                    <div class="flex items-center gap-2">
                        <span class="flex h-6 w-6 rounded-full bg-[#22AF85] text-white items-center justify-center text-xs font-black">1</span>
                        <h2 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wider">Identifikasi Invoice</h2>
                    </div>
                    <span class="text-[10px] text-[#22AF85] font-bold">Langkah 1/2</span>
                </div>

                {{-- Tab Mode Switcher (Upload File vs Scan Kamera) --}}
                <div class="grid grid-cols-2 gap-2 p-1.5 bg-[#F8FAFC] rounded-2xl border border-slate-200/80">
                    <button type="button" 
                            @click="stopCamera(); scanMode = 'file';"
                            :class="scanMode === 'file' ? 'bg-[#FFC232] text-slate-950 font-black shadow-sm' : 'text-slate-500 hover:text-slate-800 font-bold'"
                            class="py-2.5 px-3 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Upload QR
                    </button>
                    <button type="button" 
                            @click="startCamera();"
                            :class="scanMode === 'camera' ? 'bg-[#FFC232] text-slate-950 font-black shadow-sm' : 'text-slate-500 hover:text-slate-800 font-bold'"
                            class="py-2.5 px-3 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Scan Kamera
                    </button>
                </div>

                {{-- Mode 1: Upload File Gambar QR --}}
                <div x-show="scanMode === 'file'" class="space-y-3">
                    <label class="relative flex flex-col items-center justify-center w-full h-44 sm:h-48 border-2 border-dashed border-emerald-300 hover:border-[#22AF85] rounded-2xl bg-emerald-50/30 hover:bg-emerald-50/60 cursor-pointer transition-all group overflow-hidden">
                        <div class="flex flex-col items-center justify-center p-4 text-center">
                            <div class="w-12 h-12 mb-2.5 rounded-2xl bg-white text-[#22AF85] shadow-md border border-emerald-100 group-hover:scale-110 flex items-center justify-center transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <p class="text-xs sm:text-sm font-black text-slate-800">
                                Pilih Gambar QR Code Invoice
                            </p>
                            <p class="text-[10px] text-slate-500 mt-1 max-w-xs">
                                Dari hasil download QR di Invoice halaman 2 (JPG/PNG)
                            </p>
                            <span x-show="isScanning" class="mt-2 text-xs font-black text-[#22AF85] animate-pulse">
                                Membaca QR Code...
                            </span>
                        </div>
                        <input type="file" accept="image/*" class="hidden" @change="scanQrFile($event)">
                    </label>
                </div>

                {{-- Mode 2: Scan Kamera Live --}}
                <div x-show="scanMode === 'camera'" class="space-y-3" style="display: none;">
                    <div class="relative bg-black rounded-2xl overflow-hidden border border-slate-800 min-h-[240px] flex items-center justify-center shadow-inner">
                        <div id="qr-reader-portal" class="w-full"></div>
                    </div>
                    <div class="flex justify-between items-center text-xs px-1">
                        <span class="text-slate-500 italic text-[11px]">Arahkan ke QR Code Invoice</span>
                        <button type="button" @click="stopCamera(); scanMode = 'file';" class="text-red-500 hover:underline font-bold text-xs">
                            ✕ Tutup Kamera
                        </button>
                    </div>
                </div>

                {{-- Fallback: Manual Token Input --}}
                <div class="pt-3 border-t border-slate-100">
                    <details class="text-xs text-slate-600 group">
                        <summary class="cursor-pointer hover:text-[#22AF85] font-bold transition-colors list-none flex items-center justify-between py-1">
                            <span>Atau input Nomor Invoice / SPK Manual</span>
                            <span class="text-[10px] text-slate-400 group-open:rotate-180 transition-transform">▼</span>
                        </summary>
                        <div class="mt-3 flex gap-2">
                            <input type="text" 
                                   wire:model.defer="token"
                                   placeholder="Contoh: INV-260829-014F"
                                   class="flex-1 bg-[#F8FAFC] border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 uppercase font-mono font-bold focus:border-[#22AF85] focus:ring-1 focus:ring-[#22AF85] outline-none">
                            <button type="button" 
                                    wire:click="loadInvoice($wire.token)"
                                    class="px-4 py-2.5 bg-[#FFC232] hover:bg-amber-400 text-slate-950 font-black text-xs uppercase rounded-xl transition-all shadow-sm active:scale-95 cursor-pointer">
                                Cari
                            </button>
                        </div>
                    </details>
                </div>
            </div>
        </div>

    @else
        {{-- ======================================================== --}}
        {{-- 2. STATE INVOICE TERMUAT: RINGKASAN & FORM PEMBAYARAN --}}
        {{-- ======================================================== --}}
        <div class="space-y-4">
            {{-- Header Card: Invoice Details --}}
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl shadow-slate-200/60 border border-slate-100 relative overflow-hidden space-y-4">
                {{-- Decorative Top Accent Bar --}}
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#22AF85] to-[#FFC232]"></div>

                <div class="flex items-start justify-between gap-3 pt-1">
                    <div>
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#22AF85] text-[10px] font-black uppercase tracking-wider border border-emerald-200">
                            {{ $invoice->status ?? 'Belum Bayar' }}
                        </span>
                        <h2 class="text-base sm:text-lg font-black font-poppins text-slate-900 uppercase tracking-tight mt-1">
                            {{ $invoice->invoice_number }}
                        </h2>
                        <p class="text-[11px] text-slate-400 font-mono">{{ $invoice->created_at?->format('d M Y, H:i') }} WIB</p>
                    </div>

                    <div class="text-right">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Pelanggan</span>
                        <span class="text-xs sm:text-sm font-black text-slate-900 block truncate max-w-[140px] sm:max-w-none">
                            {{ $invoice->customer->name ?? 'N/A' }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">{{ $invoice->customer->phone ?? '' }}</span>
                    </div>
                </div>

                {{-- Shoe Items Breakdown --}}
                <div class="space-y-1.5">
                    <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-wider block">Daftar Item & Layanan:</span>
                    <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                        @foreach($invoice->workOrders as $wo)
                            <div class="bg-[#F8FAFC] rounded-2xl p-2.5 border border-slate-200/80 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-black text-slate-900 block">{{ $wo->shoe_brand }} {{ $wo->shoe_type }}</span>
                                    <span class="text-[10px] text-slate-500 font-mono">{{ $wo->spk_number }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-bold text-[#22AF85] bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 block">
                                        {{ $wo->workOrderServices->pluck('service.name')->filter()->join(', ') ?: 'Reparasi/Treatment' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Financial Summary Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-3 border-t border-slate-100 text-xs">
                    <div class="bg-[#F8FAFC] p-2.5 rounded-2xl border border-slate-200/60">
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Total Tagihan</span>
                        <span class="font-black text-slate-900 text-xs sm:text-sm font-mono block">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="bg-[#F8FAFC] p-2.5 rounded-2xl border border-slate-200/60">
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Terbayar (DP)</span>
                        <span class="font-black text-[#22AF85] text-xs sm:text-sm font-mono block">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-span-2 sm:col-span-1 bg-amber-50 p-2.5 rounded-2xl border border-amber-200 text-left sm:text-right">
                        <span class="text-[9px] font-black text-amber-800 uppercase block">Sisa Tagihan</span>
                        <span class="font-black text-[#B45309] text-xs sm:text-sm font-mono block">
                            Rp {{ number_format(max(0, $invoice->total_amount - $invoice->paid_amount), 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Switch / Re-scan button --}}
                <div class="text-right pt-1">
                    <button type="button" wire:click="resetInvoice" class="text-[11px] font-bold text-[#22AF85] hover:underline transition-colors">
                        🔄 Ganti / Scan QR Lain
                    </button>
                </div>
            </div>

            {{-- Form Upload Bukti Pembayaran --}}
            <form wire:submit.prevent="submitPayment" class="bg-white rounded-3xl p-5 sm:p-7 shadow-xl shadow-slate-200/60 border border-slate-100 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                    <div class="flex items-center gap-2">
                        <span class="flex h-6 w-6 rounded-full bg-[#22AF85] text-white items-center justify-center text-xs font-black">2</span>
                        <h2 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wider">Isi Bukti Pembayaran</h2>
                    </div>
                    <span class="text-[10px] text-[#22AF85] font-bold">Langkah 2/2</span>
                </div>

                {{-- 1. Input Nominal Transfer (Manual) --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-black text-slate-800 uppercase tracking-wider">
                        Nominal Yang Ditransfer <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               x-model="formattedAmount"
                               @input="onAmountInput($event)"
                               placeholder="Ketik nominal transfer, cth: Rp 150.000"
                               class="w-full bg-[#F8FAFC] border border-slate-300 rounded-2xl py-3.5 px-4 text-slate-900 text-base font-black font-mono tracking-wide focus:border-[#22AF85] focus:ring-2 focus:ring-[#22AF85]/20 outline-none transition-all placeholder:font-sans placeholder:font-normal placeholder:text-slate-400">
                    </div>
                    @error('amount') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                    <p class="text-[10px] text-slate-400 italic">Ketik nominal sesuai yang Anda transfer di struk / mutasi.</p>
                </div>

                {{-- 2. Rekening Bank Tujuan Transfer --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-800 uppercase tracking-wider">
                        Pilih Rekening Tujuan Transfer <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        {{-- BCA --}}
                        <label class="relative flex items-start p-3.5 rounded-2xl border-2 cursor-pointer transition-all"
                               :class="$wire.payment_method === 'BCA' ? 'bg-emerald-50/40 border-[#22AF85] text-slate-900 shadow-sm' : 'bg-[#F8FAFC] border-slate-200 text-slate-500 hover:border-slate-300'">
                            <input type="radio" wire:model="payment_method" value="BCA" class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-black text-xs text-blue-800 bg-blue-100 px-2 py-0.5 rounded">BANK BCA</span>
                                    <button type="button" @click.stop="copyText('8100978521')" class="text-[10px] font-black text-slate-900 bg-[#FFC232] hover:bg-amber-400 px-2 py-0.5 rounded shadow-xs active:scale-90 transition-all">
                                        Salin Rek.
                                    </button>
                                </div>
                                <p class="text-sm font-black font-mono mt-2 text-slate-900">8100978521</p>
                                <p class="text-[9px] font-bold text-slate-500 uppercase mt-0.5">PT. Terang Garam Solusindo</p>
                            </div>
                        </label>

                        {{-- Mandiri --}}
                        <label class="relative flex items-start p-3.5 rounded-2xl border-2 cursor-pointer transition-all"
                               :class="$wire.payment_method === 'Mandiri' ? 'bg-emerald-50/40 border-[#22AF85] text-slate-900 shadow-sm' : 'bg-[#F8FAFC] border-slate-200 text-slate-500 hover:border-slate-300'">
                            <input type="radio" wire:model="payment_method" value="Mandiri" class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-black text-xs text-blue-900 bg-blue-100 px-2 py-0.5 rounded">MANDIRI</span>
                                    <button type="button" @click.stop="copyText('1300030119047')" class="text-[10px] font-black text-slate-900 bg-[#FFC232] hover:bg-amber-400 px-2 py-0.5 rounded shadow-xs active:scale-90 transition-all">
                                        Salin Rek.
                                    </button>
                                </div>
                                <p class="text-sm font-black font-mono mt-2 text-slate-900">1300030119047</p>
                                <p class="text-[9px] font-bold text-slate-500 uppercase mt-0.5">PT. Terang Garam Solusindo</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- 3. Upload Struk Bukti Transfer (Auto Compress) --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider">
                            Upload Foto Struk / Bukti Transfer <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[9px] font-bold text-[#22AF85] bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                            ⚡ Auto-Compress Cepat
                        </span>
                    </div>

                    <div class="relative">
                        <label for="proof_file_input" class="flex flex-col items-center justify-center w-full min-h-[150px] border-2 border-dashed border-slate-300 hover:border-[#22AF85] rounded-2xl bg-[#F8FAFC] cursor-pointer p-4 transition-all">
                            {{-- STATE 1: Compress & Upload Loading --}}
                            <div x-show="isCompressing" class="flex flex-col items-center justify-center space-y-2.5 py-6 text-center w-full" style="display: none;">
                                <div class="relative w-12 h-12 flex items-center justify-center">
                                    <div class="absolute inset-0 rounded-full border-2 border-emerald-200 border-t-[#22AF85] animate-spin"></div>
                                    <span class="text-sm">⚡</span>
                                </div>
                                <p class="text-xs font-black text-slate-800 tracking-tight">Mengompres &amp; Menyiapkan Foto...</p>
                                <p class="text-[10px] text-slate-400 max-w-xs">Otomatis menyesuaikan resolusi agar ringan &amp; super cepat</p>
                            </div>

                            {{-- STATE 2: Foto Berhasil Dipilih & Ditampilkan Review --}}
                            <div x-show="proofPreview && !isCompressing" class="w-full flex items-center gap-4" style="display: none;">
                                <img :src="proofPreview" class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-2xl border-2 border-[#22AF85] shadow-md flex-shrink-0">
                                <div class="flex-1 text-left space-y-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-[#22AF85] animate-ping"></span>
                                        <p class="text-xs font-black text-[#22AF85]">Foto Bukti Siap Dikirim ✓</p>
                                    </div>
                                    <div x-show="compressedSizeKb" class="inline-block text-[10px] font-bold text-slate-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md font-mono">
                                        Ukuran: <span x-text="compressedSizeKb"></span> KB <span class="text-slate-400 font-normal">(Dioptimasi)</span>
                                    </div>
                                    <div class="pt-1 flex items-center gap-3">
                                        <span class="text-[10px] font-bold text-slate-600 hover:text-slate-900 underline">Ganti Foto</span>
                                        <button type="button" @click.stop.prevent="removeProof()" class="text-[10px] font-bold text-rose-500 hover:text-rose-700">✕ Hapus</button>
                                    </div>
                                </div>
                            </div>

                            {{-- STATE 3: Belum Ada Foto --}}
                            <div x-show="!proofPreview && !isCompressing" class="text-center space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-white text-[#22AF85] flex items-center justify-center mx-auto border border-slate-200 shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-xs font-black text-slate-800">Pilih Foto atau Screenshot Struk</p>
                                <p class="text-[10px] text-slate-400">Otomatis dikompres agar hemat kuota &amp; upload instan</p>
                            </div>

                            <input type="file" id="proof_file_input" @change="compressAndUploadProof($event)" accept="image/*" class="hidden">
                        </label>
                    </div>
                    @error('proof_image') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>

                {{-- 4. Catatan Opsional --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Catatan Tambahan (Opsional)
                    </label>
                    <textarea wire:model.defer="notes" 
                              rows="2" 
                              placeholder="Misal: Pembayaran DP dari rekening a.n Budi Santoso"
                              class="w-full bg-[#F8FAFC] border border-slate-300 rounded-2xl p-3 text-xs text-slate-800 focus:border-[#22AF85] focus:ring-1 focus:ring-[#22AF85] outline-none placeholder:text-slate-400"></textarea>
                </div>

                {{-- Submit Button (Kuning Aksen #FFC232) --}}
                <div class="pt-2">
                    <button type="submit" 
                            :disabled="isCompressing"
                            wire:loading.attr="disabled"
                            wire:target="submitPayment"
                            class="w-full h-14 bg-[#FFC232] hover:bg-amber-400 disabled:opacity-70 disabled:cursor-not-allowed text-slate-950 font-black text-sm uppercase tracking-wider rounded-2xl shadow-lg shadow-amber-500/25 transition-all transform active:scale-[0.98] flex items-center justify-center cursor-pointer relative overflow-hidden">
                        
                        {{-- Default State --}}
                        <div wire:loading.remove wire:target="submitPayment" x-show="!isCompressing" class="flex items-center justify-center gap-2">
                            <span>Kirim Bukti Pembayaran</span>
                            <span class="text-base leading-none">➔</span>
                        </div>

                        {{-- Client-Side Uploading / Compressing State --}}
                        <div x-show="isCompressing" class="flex items-center justify-center gap-2.5" style="display: none;">
                            <div class="w-4 h-4 border-2 border-slate-950/30 border-t-slate-950 rounded-full animate-spin"></div>
                            <span>Memproses Foto Bukti...</span>
                        </div>

                        {{-- Server-Side Submitting State --}}
                        <div wire:loading.flex wire:target="submitPayment" class="items-center justify-center gap-2.5">
                            <div class="w-5 h-5 border-2 border-slate-950/30 border-t-slate-950 rounded-full animate-spin"></div>
                            <span>Mengirim Bukti Pembayaran...</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
