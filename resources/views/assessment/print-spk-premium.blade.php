@php
    $servicesCount = $order->workOrderServices->count();
    $mainGap = $servicesCount >= 8 ? '5px' : '10px';
    $mainPadding = $servicesCount >= 8 ? '10px 14px' : '16px 20px';
    $sidebarPadding = $servicesCount >= 8 ? '10px' : '16px';
    $sidebarGap = $servicesCount >= 8 ? '6px' : '10px';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
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

        @page { 
            size: A4; 
            margin: 0; 
        }
        
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
            background: {{ $order->fast_track_status === 'yes' ? '#ea580c' : '#22B086' }}; /* Orange or Emerald Green */
            color: white;
            padding: {{ $sidebarPadding }};
            display: flex;
            flex-direction: column;
            gap: {{ $sidebarGap }};
            min-height: 100%;
        }

        .main-content {
            padding: {{ $mainPadding }};
            display: flex;
            flex-direction: column;
            gap: {{ $mainGap }};
        }

        .orange-bar {
            background: #FFC232; /* Official Orange */
            color: #1e293b; /* Dark Slate for high contrast on yellow/orange */
            padding: 4px 10px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 10.5px;
            border-radius: 5px;
            letter-spacing: 0.05em;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .page-container-full {
            width: 210mm; 
            min-height: 297mm; 
            background: white; 
            margin: 10px auto;
            position: relative;
            box-sizing: border-box;
        }
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .page-container { 
                box-shadow: none; 
                width: 210mm; 
                height: 297mm; 
            }
            .page-container-full {
                box-shadow: none;
                width: 210mm;
                height: 297mm;
                margin: 0;
            }
            .no-print { display: none; }
            .avoid-break { page-break-inside: avoid; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8 flex justify-center min-h-screen print:p-0 print:h-auto">


    <!-- MAIN PAGE CONTAINER -->
    <div class="page-container shadow-2xl overflow-hidden">
        
        <!-- SIDEBAR (LEFT) -->
        <aside class="sidebar h-full shrink-0" style="background-color: {{ $order->fast_track_status === 'yes' ? '#ea580c' : '#22B086' }};">
            {{-- Header Sidebar --}}
            <div class="flex items-center justify-between gap-3 mb-2">
                <img src="{{ asset('images/logo.png') }}" class="h-10 w-auto brightness-0 invert" onerror="this.style.display='none'">
                <div class="text-right">
                    <h1 class="font-display font-black text-xs leading-none">SHOE WORKSHOP</h1>
                    <p class="text-[10px] font-bold text-white/80 mt-0.5 tracking-tighter">Form <span class="text-white">SPK Customer</span></p>
                </div>
            </div>

            @if($order->fast_track_status === 'yes')
                <div class="w-full bg-white/20 text-white border border-white/30 rounded-lg py-1.5 px-3 text-center text-[10px] font-black uppercase tracking-widest shadow-sm animate-pulse">
                    🚀 FAST TRACK SERVICE 🚀
                </div>
            @endif

            {{-- Main Photo (Single Cover Photo) --}}
            <div class="relative avoid-break">
                <div class="aspect-square bg-white/10 rounded-xl overflow-hidden border border-white/20 relative group">
                     @if($order->spk_cover_photo)
                         @php
                             // Find if cover photo has print settings
                             $coverPhotoRecord = $order->photos->firstWhere('is_spk_cover', true);
                             $coverSett = ['zoom' => 1.0, 'x' => 0, 'y' => 0, 'rotate' => 0];
                             if ($coverPhotoRecord && $coverPhotoRecord->print_settings) {
                                 $coverSett = is_array($coverPhotoRecord->print_settings) 
                                     ? $coverPhotoRecord->print_settings 
                                     : (json_decode($coverPhotoRecord->print_settings, true) ?? $coverSett);
                             }
                         @endphp
                         <img src="{{ $order->spk_cover_photo_url }}" 
                              style="transform: scale({{ $coverSett['zoom'] ?? 1.0 }}) translate({{ $coverSett['x'] ?? 0 }}%, {{ $coverSett['y'] ?? 0 }}%) rotate({{ $coverSett['rotate'] ?? 0 }}deg); transform-origin: center; object-fit: cover;"
                              class="w-full h-full">
                     @else
                        <div class="flex flex-col items-center justify-center h-full text-white/20 bg-slate-900 rounded-xl">
                            <svg class="w-12 h-12 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-[8px] font-bold uppercase tracking-widest opacity-50">Tanpa Foto</span>
                        </div>
                     @endif
                     
                     {{-- Size Badge --}}
                     <div class="absolute top-2 right-2 bg-white text-teal-900 w-12 h-12 rounded-lg flex flex-col items-center justify-center shadow-lg z-20">
                         <span class="text-[8px] font-bold uppercase leading-none text-gray-400">Size</span>
                         <span class="text-xl font-black font-display leading-none">{{ $order->shoe_size }}</span>
                     </div>
                </div>
            </div>

            @php
                // Get photos that are marked for print and are not the cover photo
                $printedPhotos = $order->photos->filter(function($p) {
                    return $p->is_printed && !$p->is_spk_cover;
                })->take(1); // Take up to 1 to prevent overflow on A4 height
                $photoCount = $printedPhotos->count();
            @endphp
            @if($photoCount > 0)
                <div class="mt-2 flex flex-col gap-2 avoid-break">
                    @foreach($printedPhotos as $photo)
                        @php 
                            $sett = is_array($photo->print_settings) 
                                ? $photo->print_settings 
                                : (json_decode($photo->print_settings, true) ?? ['zoom' => 1.0, 'x' => 0, 'y' => 0, 'rotate' => 0]); 
                        @endphp
                        <div class="aspect-square bg-white/10 rounded-xl overflow-hidden border border-white/20 relative">
                            <img src="{{ $photo->photo_url }}" 
                                 style="transform: scale({{ $sett['zoom'] ?? 1.0 }}) translate({{ $sett['x'] ?? 0 }}%, {{ $sett['y'] ?? 0 }}%) rotate({{ $sett['rotate'] ?? 0 }}deg); transform-origin: center; object-fit: cover;"
                                 class="w-full h-full">
                        </div>
                    @endforeach
                </div>
            @endif


            {{-- Notes Section --}}
            <div class="mt-1 space-y-1 avoid-break">
                <p class="text-[9px] font-black text-white uppercase tracking-widest">Keterangan Besar :</p>
                <div class="bg-white/5 rounded-lg border border-white/10 p-2.5 flex-grow min-h-[90px] text-xs leading-tight text-white opacity-95">
                        @php
                            $rawNotes = $order->notes ?? '';
                            // Bersihkan pola "XX HK - Bergaransi -" atau "XX HK - Bergaransi" dari teks manual
                            $cleanNotes = preg_replace('/\d+\s*HK\s*-\s*(Bergaransi|Non-Garansi|Garansi|Non Garansi)\s*(-\s*)?/i', '', $rawNotes);
                            $cleanNotes = trim($cleanNotes, ' -');
                            $isFastTrack = ($order->fast_track_status === 'yes');
                        @endphp
                        <div class="flex flex-col gap-2 items-start">
                            <span class="inline-block px-2.5 py-1 rounded bg-white text-[11px] font-black shadow-sm {{ $isFastTrack ? 'text-[#ea580c]' : 'text-[#22B086]' }}">
                                {{ $order->hk_days ?? 0 }} HK - {{ $order->is_warranty ? 'Bergaransi' : 'Non-Garansi' }}
                            </span>
                            @if($cleanNotes)
                                <span class="font-bold block mt-1 text-white/95 leading-normal">{{ $cleanNotes }}</span>
                            @endif
                        </div>
                </div>
            </div>

            {{-- CATATAN GUDANG (Prominent) --}}
            <div class="mt-2 avoid-break">
                <div class="bg-white/5 rounded-xl border border-white/10 overflow-hidden">
                    <div class="bg-white/10 px-3 py-1 flex items-center justify-center">
                        <span class="text-[9px] font-black tracking-widest uppercase" style="color: #FFC232;">Catatan Gudang</span>
                    </div>
                    <div class="p-3">
                        <div class="text-[10px] font-black text-white leading-snug uppercase">
                            @if($order->technician_notes)
                                <div class="space-y-1">
                                    @foreach(explode("\n", $order->technician_notes) as $line)
                                        @if(trim($line))
                                            <div class="flex items-start gap-2">
                                                <span style="color: #FFC232;">•</span>
                                                <span>{{ trim($line) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center opacity-50">- Belum ada catatan -</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- LEVEL PRIORITAS (SOLID & TERANG) --}}
            @if($order->priority && in_array(strtoupper($order->priority), ['PRIORITAS', 'EXPRESS', 'URGENT']))
            <div class="mt-2 avoid-break">
                @php
                    $isExpress = in_array(strtoupper($order->priority), ['EXPRESS', 'URGENT']);
                @endphp
                <div class="rounded-xl border shadow-sm overflow-hidden {{ $isExpress ? 'bg-red-600 border-red-500 text-white' : 'bg-[#FFC232] border-[#FFE27C] text-slate-900' }}">
                    <div class="px-3 py-1.5 flex items-center justify-center font-bold {{ $isExpress ? 'bg-red-700/60' : 'bg-[#e0a81c]' }}">
                        <span class="text-[9px] font-black tracking-widest uppercase">
                            Prioritas SPK
                        </span>
                    </div>
                    <div class="p-3 text-center">
                        <span class="text-sm font-black tracking-widest uppercase {{ $isExpress ? 'text-white animate-pulse' : 'text-slate-900' }}">
                            {{ strtoupper($order->priority) }}
                        </span>
                    </div>
                </div>
            </div>
            @endif



            {{-- Empty space to push branding to bottom --}}
            <div class="flex-grow"></div>

            {{-- Footer Sidebar Branding --}}
            <div class="mt-auto pt-3 border-t border-white/20 relative overflow-hidden avoid-break">
                <div class="absolute -left-10 bottom-0 opacity-10 blur-xl w-32 h-32 bg-amber-400 rounded-full"></div>
                <div class="flex items-center gap-2 relative z-10">
                    <div class="text-xs font-black leading-none text-white font-sans">
                        #<span style="color: #FFC232;">living</span>with<br><span class="text-xl">PASSION</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN AREA (RIGHT) -->
        <main class="main-content">
            {{-- ORDER INFO BOX --}}
            <div class="grid grid-cols-2 gap-4 avoid-break">
                <div class="space-y-2">
                    <div class="bg-gray-50 rounded-lg p-2 px-3 border border-gray-150 flex flex-col justify-center">
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Nomor SPK</span>
                        <span class="text-xl font-extrabold font-mono tracking-tight" style="color: {{ $order->fast_track_status === 'yes' ? '#ea580c' : '#22B086' }};">{{ $order->spk_number }}</span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2 px-3 border border-gray-100 flex flex-col justify-center">
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Nama Customer</span>
                        <span class="text-xs font-black text-gray-900 tracking-tight leading-tight">{{ $order->customer_name }}</span>
                    </div>
                    @if($order->csLead)
                    <div class="bg-gray-50 rounded-lg p-2 px-4 border border-gray-100 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-600 uppercase tracking-tight">Order Channel</span>
                        <div class="px-3 py-1 rounded-md border font-black text-[10px] tracking-widest {{ $order->csLead->channel === 'ONLINE' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                            {{ $order->csLead->channel }}
                        </div>
                    </div>
                    @endif

                    <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-100 min-h-[60px]">
                        <p class="text-[9px] font-bold text-gray-500 uppercase mb-0.5 tracking-tight">Alamat Lengkap</p>
                        <p class="text-[10px] font-semibold text-gray-700 leading-tight">
                            {{ $order->customer_address }}
                            @if($order->customer)
                                <br><span class="text-gray-400 font-medium text-[9px]">
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
             @php
                 $servicesCount = $order->workOrderServices->count();
                 
                 // Default (Small count: <= 4)
                 $barPaddingStyle = 'padding: 4px 10px; font-size: 10.5px;';
                 $indicatorHeight = 'h-3.5';
                 $nbLabelStyle = 'font-size: 8px;';
                 $nbTextStyle = 'font-size: 9px;';
                 $gapClass = 'space-y-2';
                 $qcLabelStyle = 'font-size: 6.5px;';
                 $qcBoxStyle = 'width: 16px; height: 16px;';
                 $parafLineStyle = 'width: 32px; height: 16px;';
                 
                 // Medium count: 5 - 7
                 if ($servicesCount >= 5 && $servicesCount <= 7) {
                     $barPaddingStyle = 'padding: 3px 8px; font-size: 9.5px;';
                     $indicatorHeight = 'h-3';
                     $nbLabelStyle = 'font-size: 7.5px;';
                     $nbTextStyle = 'font-size: 8.5px;';
                     $gapClass = 'space-y-1.5';
                     $qcLabelStyle = 'font-size: 6px;';
                     $qcBoxStyle = 'width: 14px; height: 14px;';
                     $parafLineStyle = 'width: 28px; height: 14px;';
                 }
                 // Large count: 8+
                 elseif ($servicesCount >= 8) {
                     $barPaddingStyle = 'padding: 2px 6px; font-size: 8.5px;';
                     $indicatorHeight = 'h-2.5';
                     $nbLabelStyle = 'font-size: 7px;';
                     $nbTextStyle = 'font-size: 7.5px;';
                     $gapClass = 'space-y-1';
                     $qcLabelStyle = 'font-size: 5.5px;';
                     $qcBoxStyle = 'width: 12px; height: 12px;';
                     $parafLineStyle = 'width: 24px; height: 12px;';
                 }
             @endphp

             <div class="flex-grow mt-0">
                   <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] mb-1.5 ml-1">Jasa Pengerjaan :</p>
                  
                  <div class="{{ $servicesCount >= 8 ? 'grid grid-cols-2 gap-x-4 gap-y-1.5' : $gapClass }}">
                       @foreach($order->workOrderServices as $service)
                       <div class="avoid-break group">
                            {{-- Orange Header Bar --}}
                            <div class="orange-bar shadow-sm flex items-center justify-between" style="{{ $barPaddingStyle }}">
                                 <div class="flex items-center gap-1.5 min-w-0 flex-grow pr-3">
                                     <span class="w-1 {{ $indicatorHeight }} bg-slate-900/20 rounded-full shrink-0"></span>
                                     <div class="flex-grow min-w-0 leading-tight">
                                         <span class="font-black whitespace-normal break-words" style="font-size: {{ $servicesCount >= 8 ? '8.5px' : ($servicesCount >= 5 ? '9.5px' : '10.5px') }};">
                                             {{ $loop->iteration }}. {{ strtoupper($service->custom_service_name ?? $service->service->name ?? 'Service Name') }}
                                         </span>
                                     </div>
                                 </div>
                                 <div class="flex items-center shrink-0 gap-1.5">
                                     @if(!empty($service->service_details['is_cx_additional']) && $service->service_details['is_cx_additional'])
                                         <span class="text-amber-800 bg-amber-100/90 border border-amber-300 rounded px-1.5 py-0.5 font-bold uppercase tracking-wider shrink-0" style="font-size: {{ $servicesCount >= 8 ? '7px' : ($servicesCount >= 5 ? '8px' : '9px') }};">
                                             JASA TAMBAHAN
                                         </span>
                                     @endif
                                     <span class="px-1.5 py-0.5 rounded bg-slate-900/10 font-bold uppercase tracking-wide border border-slate-900/15 shrink-0" style="font-size: {{ $servicesCount >= 8 ? '7px' : ($servicesCount >= 5 ? '8px' : '9px') }};">
                                         {{ strtoupper($service->category_name ?? ($service->service ? $service->service->category : 'S')) }}
                                     </span>
                                     @if($servicesCount < 8 && (empty($service->service_details['is_cx_additional']) || !$service->service_details['is_cx_additional']))
                                         <span class="text-[8px] font-black opacity-40 tracking-tighter shrink-0 ml-1">PROSES WORKSHOP</span>
                                     @endif
                                 </div>
                             </div>
                           
                           {{-- Detail Row --}}
                           <div class="mt-1 pl-3 border-l border-gray-200 flex items-start justify-between gap-4">
                                {{-- Notes Detail --}}
                                <div class="flex-grow space-y-1">
                                    <div class="flex items-start gap-1.5">
                                        <span class="text-teal-600 font-black uppercase tracking-tighter shrink-0 pt-0.5" style="{{ $nbLabelStyle }}">NB :</span>
                                        <div class="text-gray-700 font-bold leading-tight" style="{{ $nbTextStyle }}">
                                            @if(is_array($service->service_details))
                                                @foreach($service->service_details as $key => $val)
                                                    @if($key !== 'is_cx_additional')
                                                        @if(is_array($val))
                                                            @foreach($val as $line)
                                                                <div style="margin-bottom: 1px;">• {{ strtoupper($line) }}</div>
                                                            @endforeach
                                                        @else
                                                            {{ strtoupper($val) }}
                                                        @endif
                                                        @if(!$loop->last && !is_array($val)), @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if(!empty($service->notes))
                                                <div class="mt-1 text-gray-500 italic">{{ $service->notes }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
 
                                {{-- Checklist & Paraf --}}
                                <div class="flex items-center gap-3 shrink-0">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span class="font-black text-gray-400 uppercase" style="{{ $qcLabelStyle }}">QC</span>
                                        <div class="rounded border border-teal-500 bg-white" style="{{ $qcBoxStyle }}"></div>
                                    </div>
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span class="font-black text-gray-400 uppercase" style="{{ $qcLabelStyle }}">Paraf</span>
                                        <div class="border-b border-gray-200" style="{{ $parafLineStyle }}"></div>
                                    </div>
                                </div>
                           </div>
                       </div>
                       @endforeach
                  </div>
             </div>

             {{-- BOTTOM TRACKING BOXES --}}
             <div class="mt-auto {{ $servicesCount >= 8 ? 'pt-1.5' : 'pt-3' }} border-t border-gray-100">
                  <div class="grid grid-cols-3 gap-3">
                       <div class="bg-gray-100/50 rounded-xl {{ $servicesCount >= 8 ? 'p-1.5' : 'p-2.5' }} border border-gray-200/50 flex flex-col justify-between">
                           <p class="text-[8px] font-black text-center text-teal-900 uppercase mb-1">SPK Masuk :</p>
                           <div class="h-4 border-b border-dotted border-gray-300 mt-1"></div>
                       </div>
                       <div class="bg-teal-50 rounded-xl {{ $servicesCount >= 8 ? 'p-1.5' : 'p-2.5' }} border border-teal-100 flex flex-col justify-between">
                           <p class="text-[8px] font-black text-center text-teal-900 uppercase mb-1">Estimasi Selesai :</p>
                           @if($order->invoice && $order->invoice->estimasi_selesai)
                               <div class="text-[11px] font-black text-center text-teal-955 mt-1 uppercase tracking-tight">
                                   {{ \Carbon\Carbon::parse($order->invoice->estimasi_selesai)->translatedFormat('d M Y') }}
                               </div>
                           @else
                               <div class="h-4 border-b border-dotted border-gray-300 mt-1"></div>
                           @endif
                       </div>
                       <div class="bg-gray-100/50 rounded-xl {{ $servicesCount >= 8 ? 'p-1.5' : 'p-2.5' }} border border-gray-200/50 flex flex-col justify-between">
                           <p class="text-[8px] font-black text-center text-teal-900 uppercase mb-1">SPK Keluar :</p>
                           <div class="h-4 border-b border-dotted border-gray-300 mt-1"></div>
                       </div>
                  </div>
 
                  {{-- Note --}}
                  <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm relative {{ $servicesCount >= 8 ? 'min-h-[50px] mt-1' : 'min-h-[100px] mt-2' }}">
                       <div class="absolute top-1 left-3 text-[8px] font-black text-teal-900 uppercase tracking-widest opacity-50">Note / Catatan Tambahan</div>
                       <div class="mt-2 text-[10px] text-gray-700 leading-relaxed font-medium">
                           @if($order->notes)
                               {{ $order->notes }}
                           @else
                               {{-- Dotted lines for manual writing --}}
                               <div class="{{ $servicesCount >= 8 ? 'space-y-2 pt-1' : 'space-y-4 pt-2' }} opacity-30">
                                   <div class="border-b border-dashed border-gray-350"></div>
                                   <div class="border-b border-dashed border-gray-350"></div>
                                   @if($servicesCount < 8)
                                       <div class="border-b border-dashed border-gray-350"></div>
                                   @endif
                               </div>
                           @endif
                       </div>
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
