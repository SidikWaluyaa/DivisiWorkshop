<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK - {{ $order->spk_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;600;700&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif; 
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important; 
        }
        
        .font-display { font-family: 'Outfit', sans-serif; }

        @page { size: A4; margin: 0; }
        
        .page-container {
            width: 210mm; 
            min-height: 297mm; 
            background: white; 
            margin: 0 auto;
            position: relative;
            display: grid;
            grid-template-columns: 75mm 135mm; /* Sidebar + Main */
        }

        .sidebar {
            background: #22B086; /* Emerald Green */
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            min-height: 100%;
        }

        .main-content {
            padding: 20px 25px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .orange-bar {
            background: #FFC232; /* Official Orange */
            color: white;
            padding: 4px 12px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 11px;
            border-radius: 4px;
            letter-spacing: 0.05em;
        }

        @media print {
            body { background: white; margin: 0; padding: 0; }
            .page-container { 
                box-shadow: none; 
                width: 210mm; 
                height: auto; 
                min-height: 297mm;
            }
            .no-print { display: none; }
            .avoid-break { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8 flex justify-center min-h-screen print:p-0 print:h-auto">


    <!-- MAIN PAGE CONTAINER -->
    <div class="page-container shadow-2xl overflow-hidden">
        
        <!-- SIDEBAR (LEFT) -->
        <aside class="sidebar h-full shrink-0" style="background-color: #22B086;">
            {{-- Header Sidebar --}}
            <div class="flex items-center justify-between gap-3 mb-2">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" class="h-10 w-auto brightness-0 invert" onerror="this.style.display='none'">
                    <div>
                        <h1 class="font-display font-black text-xs leading-none">SHOE WORKSHOP</h1>
                        <p class="text-[10px] font-bold text-white/80 mt-0.5 tracking-tighter">Form <span class="text-white">SPK Customer</span></p>
                    </div>
                </div>
                {{-- QR Code --}}
                <div class="bg-white p-1 rounded-lg">
                    {!! $barcode !!}
                </div>
            </div>

            {{-- Main Photo --}}
            <div class="relative avoid-break">
                <div class="aspect-square bg-white/10 rounded-xl overflow-hidden border border-white/20 relative group">
                     @if($order->spk_cover_photo)
                        <img src="{{ asset('storage/' . $order->spk_cover_photo) }}" class="w-full h-full object-contain">
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-white/20">
                            <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-[8px] font-bold uppercase tracking-widest opacity-50">Tanpa Foto</span>
                        </div>
                    @endif
                    
                    {{-- Size Badge --}}
                    <div class="absolute top-2 right-2 bg-white text-teal-900 w-12 h-12 rounded-lg flex flex-col items-center justify-center shadow-lg">
                        <span class="text-[8px] font-bold uppercase leading-none text-gray-400">Size</span>
                        <span class="text-xl font-black font-display leading-none">{{ $order->shoe_size }}</span>
                    </div>
                </div>
            </div>

            {{-- Notes Section --}}
            <div class="mt-2 space-y-1 avoid-break">
                <p class="text-[10px] font-black text-white uppercase tracking-widest">Keterangan Besar :</p>
                <div class="bg-white/5 rounded-lg border border-white/10 p-3 flex-grow min-h-[120px]">
                        {{ $order->notes ?? $order->technician_notes ?? '' }}
                </div>
            </div>

            {{-- Workshop Control Grid (Vertical) --}}
            <div class="mt-auto space-y-3 avoid-break">
                {{-- ACC FOLLOW UP --}}
                <div class="bg-white/5 rounded-xl border border-white/10 overflow-hidden">
                    <div class="bg-white/10 px-3 py-1 flex items-center justify-center">
                        <span class="text-[9px] font-black tracking-widest uppercase" style="color: #FFC232;">ACC Follow Up</span>
                    </div>
                    <div class="p-3 space-y-3">
                        <div class="grid grid-cols-2 gap-2">
                             <div>
                                <p class="text-[8px] font-black text-white uppercase mb-1">Lolos QC:</p>
                                <div class="h-14 bg-white/5 rounded border border-white/5"></div>
                             </div>
                             <div>
                                <p class="text-[8px] font-black text-white uppercase mb-1">Verifikasi OTW:</p>
                                <div class="h-14 bg-white/5 rounded border border-white/5"></div>
                             </div>
                        </div>
                        <div class="grid grid-cols-1 gap-2 border-t border-white/5 pt-2">
                            <div class="flex justify-between items-center text-[9px]">
                                <span class="font-black text-white">Tanggal Selesai:</span>
                                <span class="w-20 border-b border-dotted border-white/60 h-4"></span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="font-black text-white text-[9px] uppercase">Follow up :</span>
                                <div class="h-8 border-b border-dotted border-white/60"></div>
                            </div>
                            <div class="flex justify-between items-end">
                                <span class="text-[8px] font-black text-white uppercase">Paraf QC</span>
                                <div class="w-10 h-10 border-2 border-white/20 rounded bg-white/5"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ACC QC --}}
                <div class="bg-white/5 rounded-xl border border-white/10 overflow-hidden">
                    <div class="px-3 py-1 flex items-center justify-center" style="background-color: rgba(255, 255, 255, 0.1);">
                        <span class="text-[9px] font-black tracking-widest uppercase" style="color: #FFC232;">ACC QC</span>
                    </div>
                    <div class="p-3 space-y-3">
                        <div>
                            <p class="text-[8px] font-black text-white uppercase mb-1">Revisi :</p>
                            <div class="h-24 bg-white/5 rounded border border-white/5"></div>
                        </div>
                        <div class="flex justify-between items-end gap-2">
                            <div class="flex-grow">
                                <p class="text-[8px] font-black text-white uppercase mb-1">Lolos QC :</p>
                                <div class="h-8 border-b border-dotted border-white/40"></div>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-[8px] font-black text-white uppercase mb-1">Paraf QC</span>
                                <div class="w-12 h-12 border-2 border-white/20 rounded bg-white/5"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Sidebar Branding --}}
            <div class="mt-4 pt-4 border-t border-white/20 relative overflow-hidden avoid-break">
                <div class="absolute -left-10 bottom-0 opacity-10 blur-xl w-32 h-32 bg-amber-400 rounded-full"></div>
                <div class="flex items-center gap-2 relative z-10">
                    <div class="text-xs font-black leading-none text-white">
                        #<span style="color: #FFC232;">living</span>with<br><span class="text-xl">PASSION</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN AREA (RIGHT) -->
        <main class="main-content">
            {{-- ORDER INFO BOX --}}
            <div class="grid grid-cols-2 gap-4 avoid-break">
                <div class="space-y-3">
                    <div class="bg-gray-50 rounded-lg p-2 px-3 border border-gray-100 flex items-center justify-between">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">Nomor SPK</span>
                        <span class="text-xs font-black font-mono tracking-tight" style="color: #22B086;">{{ $order->spk_number }}</span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2 px-3 border border-gray-100 flex items-center justify-between">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">Nama Customer</span>
                        <span class="text-xs font-black text-gray-800 tracking-tight">{{ $order->customer_name }}</span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 min-h-[70px]">
                        <p class="text-[9px] font-bold text-gray-400 uppercase mb-1">Alamat Lengkap</p>
                        <p class="text-[10px] font-bold text-gray-800 leading-tight">
                            {{ $order->customer_address }}
                            @if($order->customer)
                                <br><span class="text-gray-500 font-medium">
                                    {{ $order->customer->village ? $order->customer->village . ', ' : '' }}
                                    {{ $order->customer->district ? $order->customer->district . ', ' : '' }}
                                    {{ $order->customer->city ? $order->customer->city . ', ' : '' }}
                                    {{ $order->customer->province ? $order->customer->province : '' }}
                                </span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- ACCESSORIES TAGS --}}
                <div class="bg-white border rounded-lg p-4 space-y-3 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-full opacity-5 skew-x-12" style="background-color: #22B086;"></div>
                    
                    <p class="text-[10px] font-black uppercase tracking-widest mb-2 flex items-center gap-2" style="color: #22B086;">
                        <svg class="w-3 h-3" style="color: #FFC232;" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path></svg>
                        Accessories
                    </p>
                    
                    <div class="flex flex-wrap gap-2">
                         @php $acc = $order; @endphp
                         <div class="px-2 py-1 rounded border flex items-center gap-2" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                             <span class="text-[8px] font-black uppercase" style="color: #22B086; opacity: 0.6;">INS:</span>
                             <span class="text-[11px] font-black" style="color: #22B086;">@if(in_array($acc->accessories_insole, ['Simpan', 'S'])) S @elseif(in_array($acc->accessories_insole, ['Nempel', 'N'])) N @else T @endif</span>
                         </div>
                         <div class="px-2 py-1 rounded border flex items-center gap-2" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                             <span class="text-[8px] font-black uppercase" style="color: #22B086; opacity: 0.6;">TALI:</span>
                             <span class="text-[11px] font-black" style="color: #22B086;">@if(in_array($acc->accessories_tali, ['Simpan', 'S'])) S @elseif(in_array($acc->accessories_tali, ['Nempel', 'N'])) N @else T @endif</span>
                         </div>
                         <div class="px-2 py-1 rounded border flex items-center gap-2" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                             <span class="text-[8px] font-black uppercase" style="color: #22B086; opacity: 0.6;">BOX:</span>
                             <span class="text-[11px] font-black" style="color: #22B086;">@if(in_array($acc->accessories_box, ['Simpan', 'S'])) S @elseif(in_array($acc->accessories_box, ['Nempel', 'N'])) N @else T @endif</span>
                         </div>
                    </div>
                    
                    <div class="pt-2 border-t border-gray-100 flex items-center gap-2">
                         <span class="text-[8px] font-black uppercase shrink-0" style="color: #22B086; opacity: 0.6;">LAINNYA:</span>
                         <span class="text-[10px] font-bold text-gray-700 border-b border-dotted border-gray-300 flex-grow pb-1">
                             @if($order->accessories_other && $order->accessories_other != 'Tidak Ada')
                                {{ $order->accessories_other }}
                             @else
                                <span class="text-gray-200 tracking-tighter">...................................................</span>
                             @endif
                         </span>
                    </div>
                </div>
            </div>

            {{-- SERVICES LIST (ORANGE BARS) --}}
            <div class="flex-grow mt-2">
                 <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Jasa Pengerjaan :</p>
                 
                 <div class="space-y-6">
                      @foreach($order->workOrderServices as $service)
                      <div class="avoid-break group">
                          {{-- Orange Header Bar --}}
                          <div class="orange-bar flex justify-between items-center shadow-sm relative overflow-hidden group-hover:opacity-90 transition-opacity" style="background-color: #FFC232;">
                               <div class="relative z-10 flex items-center gap-2">
                                   <span class="w-1 h-3 bg-white/40 rounded-full"></span>
                                   {{ strtoupper($service->custom_service_name ?? $service->service->name ?? 'Service Name') }} - {{ strtoupper($service->category_name ?? ($service->service ? $service->service->category : 'S')) }}
                               </div>
                               <div class="text-[8px] font-black opacity-60">PROSES SPK</div>
                          </div>
                          
                          {{-- Detail Row --}}
                          <div class="mt-2 pl-4 border-l-2 border-gray-100 flex items-start justify-between gap-4">
                               {{-- Notes Detail --}}
                               <div class="flex-grow space-y-1">
                                   <div class="flex items-start gap-2">
                                       <span class="text-[9px] font-black text-teal-600 uppercase tracking-tighter shrink-0 pt-0.5">NB :</span>
                                       <p class="text-[10px] font-medium text-gray-500 leading-normal italic">
                                           @if(is_array($service->service_details))
                                               {{ implode(', ', array_map(function($k, $v) { return strtoupper($v); }, array_keys($service->service_details), $service->service_details)) }}
                                           @endif
                                           {{ $service->notes ?? '' }}
                                       </p>
                                   </div>
                               </div>

                               {{-- Checklist & Paraf --}}
                               <div class="flex items-center gap-4 shrink-0">
                                   <div class="flex flex-col items-center gap-1">
                                       <span class="text-[7px] font-black text-gray-400 uppercase">QC</span>
                                       <div class="w-5 h-5 rounded border-2 border-teal-500 bg-white"></div>
                                   </div>
                                   <div class="flex flex-col items-center gap-1">
                                       <span class="text-[7px] font-black text-gray-400 uppercase">Paraf</span>
                                       <div class="w-10 h-6 border-b-2 border-gray-200"></div>
                                   </div>
                               </div>
                          </div>
                      </div>
                      @endforeach
                 </div>
            </div>

            {{-- BOTTOM TRACKING BOXES --}}
            <div class="mt-auto pt-4 border-t-2 border-gray-50">
                 <div class="grid grid-cols-3 gap-3">
                      <div class="bg-gray-100/50 rounded-xl p-3 border border-gray-200/50 flex flex-col justify-between">
                          <p class="text-[9px] font-black text-center text-teal-900 uppercase mb-2">SPK Masuk :</p>
                          <div class="h-4 border-b border-dotted border-gray-300 mt-1"></div>
                      </div>
                      <div class="bg-teal-50 rounded-xl p-3 border border-teal-100 flex flex-col justify-between">
                          <p class="text-[9px] font-black text-center text-teal-900 uppercase mb-2">Estimasi Selesai :</p>
                          <div class="h-4 border-b border-dotted border-gray-300 mt-1"></div>
                      </div>
                      <div class="bg-gray-100/50 rounded-xl p-3 border border-gray-200/50 flex flex-col justify-between">
                          <p class="text-[9px] font-black text-center text-teal-900 uppercase mb-2">SPK Keluar :</p>
                          <div class="h-4 border-b border-dotted border-gray-300 mt-1"></div>
                      </div>
                 </div>

                 {{-- Revisi Jasa --}}
                 <div class="mt-3 bg-white border-2 border-gray-100 rounded-xl p-4 min-h-[100px] shadow-inner relative">
                      <div class="absolute top-2 left-4 text-[9px] font-black text-teal-900 uppercase tracking-widest opacity-40">Revisi Jasa</div>
                      <div class="mt-4 text-[10px] text-gray-300 italic">Tambahan biaya/jasa baru...</div>
                 </div>
            </div>

            {{-- FINAL FOOTER RIGHT --}}
            <div class="mt-4 flex justify-between items-center px-4 opacity-50">
                <div class="text-[10px] font-black uppercase" style="color: #22B086;">Shoe Workshop Premium</div>
                <div class="text-[10px] font-bold text-gray-400">#morethanrepair</div>
            </div>
        </main>

    </div>

    <script>
        window.onload = function() {
            // Auto Print
             window.print();
        }
    </script>
</body>
</html>
