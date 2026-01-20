<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK - {{ $order->spk_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { 
                size: A4 portrait; 
                margin: 1cm; 
            }
            body { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
            }
            .no-print { display: none !important; }
        }
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 11px; 
            color: #000; 
        }
        .teal-bg { background-color: #14B8A6; }
        .orange-bg { background-color: #F97316; }
        .yellow-bg { background-color: #FCD34D; }
    </style>
</head>
<body class="bg-gray-100 p-4 print:p-0 print:bg-white">

    {{-- Print Controls --}}
    <div class="no-print mb-4 flex gap-4 max-w-[21cm] mx-auto">
        <button onclick="window.print()" class="px-4 py-2 bg-teal-600 text-white font-bold rounded hover:bg-teal-700">🖨️ Cetak SPK</button>
        <button onclick="window.close()" class="px-4 py-2 bg-gray-500 text-white font-bold rounded hover:bg-gray-600">Tutup</button>
    </div>

    {{-- A4 Container --}}
    <div class="max-w-[21cm] mx-auto bg-white shadow-lg print:shadow-none">
        
        {{-- HEADER WITH LOGO --}}
        <div class="flex items-stretch border-b-4 border-black">
            {{-- Logo Section (Teal) --}}
            <div class="w-1/4 teal-bg p-4 flex items-center justify-center">
                <div class="text-center">
                    <img src="{{ asset('images/logo-email.png') }}" class="h-20 mx-auto mb-2" alt="Logo">
                    <div class="text-white font-bold text-xs">Shoe Workshop</div>
                </div>
            </div>
            
            {{-- Title Section --}}
            <div class="flex-1 flex flex-col justify-center items-center p-4 bg-white">
                <h1 class="text-3xl font-black tracking-wide">Form</h1>
                <h2 class="text-2xl font-black text-teal-600">SPK Customer</h2>
            </div>
            
            {{-- SPK Info & Accessories --}}
            <div class="w-1/3 p-3 bg-gray-50 border-l-2 border-black">
                <div class="mb-3">
                    <label class="text-[9px] font-bold text-gray-500 uppercase">Nomor SPK</label>
                    <div class="font-mono font-black text-lg">{{ $order->spk_number }}</div>
                </div>
                
                {{-- Accessories Checklist --}}
                <div class="border border-gray-300 p-2 bg-white">
                    <div class="grid grid-cols-2 gap-1 text-[9px]">
                        <div class="flex items-center gap-1">
                            <input type="checkbox" {{ in_array($order->accessories_insole, ['Simpan', 'Nempel']) ? 'checked' : '' }} disabled class="w-3 h-3">
                            <span>Insole Tempel</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <input type="checkbox" {{ $order->accessories_tali == 'Tidak Ada' ? 'checked' : '' }} disabled class="w-3 h-3">
                            <span>Tali Tidak Ada</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <input type="checkbox" disabled class="w-3 h-3">
                            <span>Cuci Kering</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <input type="checkbox" {{ $order->accessories_other ? 'checked' : '' }} disabled class="w-3 h-3">
                            <span>Asesoris</span>
                        </div>
                        <div class="flex items-center gap-1 col-span-2">
                            <input type="checkbox" {{ $order->accessories_box == 'Tidak Ada' ? 'checked' : '' }} disabled class="w-3 h-3">
                            <span>Box Sepatu Tidak Ada</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Info Bar --}}
        <div class="flex border-b-2 border-black">
            <div class="flex-1 p-3 border-r-2 border-black">
                <label class="text-[9px] font-bold text-gray-500 uppercase">Nama Customer</label>
                <div class="font-bold text-base">{{ $order->customer_name }}</div>
            </div>
            <div class="w-1/3 p-3 bg-gray-50">
                <label class="text-[9px] font-bold text-gray-500 uppercase">Alamat Customer</label>
                <div class="text-xs">{{ $order->customer_address ?? '-' }}</div>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="flex">
            
            {{-- LEFT COLUMN: Photo & Info --}}
            <div class="w-1/3 border-r-2 border-black">
                
                {{-- Photo Before --}}
                <div class="aspect-square bg-gray-100 border-b-2 border-black relative overflow-hidden flex items-center justify-center">
                    @php
                        // Get first public photo from Assessment (not Reception)
                        $assessmentPhoto = $order->photos
                            ->where('is_public', 1)
                            ->filter(function($photo) {
                                return !in_array($photo->step, ['RECEPTION', 'WAREHOUSE_BEFORE', 'REFERENCE']);
                            })
                            ->sortByDesc('created_at')
                            ->first();
                        
                        $displayPhoto = $assessmentPhoto ?? $order->photos->first();
                    @endphp

                    @if($displayPhoto)
                        <img src="{{ asset('storage/' . $displayPhoto->file_path) }}" class="object-cover w-full h-full" alt="Before">
                    @else
                        <div class="text-gray-400 font-bold text-center p-4">
                            <svg class="w-16 h-16 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                            TIDAK ADA<br>FOTO BEFORE
                        </div>
                    @endif
                    
                    <div class="absolute bottom-2 left-2 right-2 teal-bg text-white text-center py-1 rounded font-bold text-xs">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        {{ $order->entry_date ? $order->entry_date->format('d-M-Y') : 'N/A' }}
                    </div>
                </div>

                {{-- Shoe Info --}}
                <div class="p-3 space-y-2">
                    <div class="border-2 border-black p-2 bg-white">
                        <div class="text-[9px] font-bold text-gray-500 uppercase">Jasa</div>
                        <div class="font-black text-xl uppercase">{{ $order->workOrderServices->first()->category_name ?? 'GENERAL' }}</div>
                    </div>

                    <div class="border-2 border-black p-3 bg-black text-white text-center">
                        <div class="text-[9px] font-bold text-gray-300 uppercase mb-1">Size</div>
                        <div class="text-4xl font-black">{{ $order->shoe_size }}</div>
                    </div>
                </div>

                {{-- Notes Section --}}
                <div class="p-3">
                    <div class="border-2 border-black p-2">
                        {{-- Technician Instructions (Priority) --}}
                        <div class="font-bold text-xs mb-1 bg-gray-200 px-2 py-1 uppercase border-b border-gray-400">⚠️ Instruksi Teknisi :</div>
                        <div class="text-xs font-bold min-h-[40px] p-1 mb-2">
                            {{ $order->technician_notes ?? '-' }}
                        </div>

                        {{-- CS Notes (Secondary) --}}
                         <div class="font-bold text-[10px] text-gray-500 mb-1 border-t border-gray-300 pt-1">Catatan CS (Customer):</div>
                        <div class="text-[10px] text-gray-500 italic">
                            {{ $order->notes ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Services --}}
            <div class="flex-1 p-4">
                
                @php
                    // Group services by category
                    $groupedServices = $order->workOrderServices->groupBy('category_name');
                @endphp

                @foreach($groupedServices as $category => $services)
                    <div class="mb-4">
                        {{-- Category Header --}}
                        <div class="yellow-bg border-2 border-black px-3 py-1 font-black uppercase text-sm mb-2">
                            {{ $category }}
                        </div>

                        {{-- Services in this category --}}
                        @foreach($services as $detail)
                            <div class="mb-3 border border-gray-300 bg-white">
                                <div class="flex items-start justify-between p-2 border-b border-gray-200">
                                    <div class="flex-1">
                                        <div class="font-bold text-sm">{{ $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan Custom') }}</div>
                                    </div>
                                    <div class="flex items-center gap-2 ml-2">
                                        <label class="flex items-center gap-1 text-xs">
                                            <input type="checkbox" class="w-4 h-4 border-2 border-black">
                                            <span class="font-bold">Proses</span>
                                        </label>
                                    </div>
                                </div>
                                
                                {{-- Notes Area --}}
                                <div class="p-2 bg-gray-50">
                                    <div class="text-[9px] font-bold text-gray-600 mb-1">NB :</div>
                                    <div class="border-b border-gray-300 min-h-[20px] text-xs">
                                        @if($loop->parent->first && $loop->first && $order->notes)
                                            {{ Str::limit($order->notes, 100) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                @if($order->workOrderServices->isEmpty())
                    <div class="text-center text-gray-400 py-8">
                        <p class="font-bold">Belum ada layanan yang dipilih</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- FOOTER SECTION --}}
        <div class="border-t-4 border-black">
            <div class="flex">
                {{-- ACC Follow Up --}}
                <div class="w-1/2 border-r-2 border-black p-3">
                    <div class="font-bold text-xs uppercase mb-2 text-center">ACC Follow Up</div>
                    <div class="text-[9px] space-y-1">
                        <div>Lulus QC / Input Masuk : _______________</div>
                        <div>Revisi : _______________</div>
                    </div>
                    <div class="mt-4 border-t border-gray-300 pt-2 text-center">
                        <div class="h-12"></div>
                        <div class="text-[9px] text-gray-500">Paraf QC : _______________</div>
                    </div>
                </div>

                {{-- ACC QC --}}
                <div class="w-1/2 p-3">
                    <div class="font-bold text-xs uppercase mb-2 text-center">ACC QC</div>
                    <div class="text-[9px] space-y-1">
                        <div>Lulus QC : _______________</div>
                        <div>Verifikasi OTW Workshop : _______________</div>
                    </div>
                    <div class="mt-4 border-t border-gray-300 pt-2 text-center">
                        <div class="h-12"></div>
                        <div class="text-[9px] text-gray-500">Paraf QC : _______________</div>
                    </div>
                </div>
            </div>

            {{-- Dates Section --}}
            <div class="flex border-t-2 border-black text-xs">
                <div class="flex-1 p-2 border-r border-black text-center">
                    <div class="font-bold text-[9px] text-gray-500 uppercase">SPK Masuk :</div>
                    <div class="font-bold">{{ $order->entry_date ? $order->entry_date->format('d F Y') : '-' }}</div>
                </div>
                <div class="flex-1 p-2 border-r border-black text-center">
                    <div class="font-bold text-[9px] text-gray-500 uppercase">Estimasi Selesai :</div>
                    <div class="font-bold">{{ $order->estimation_date ? $order->estimation_date->format('d F Y') : '-' }}</div>
                </div>
                <div class="flex-1 p-2 text-center">
                    <div class="font-bold text-[9px] text-gray-500 uppercase">SPK Keluar :</div>
                    <div class="font-bold">_______________</div>
                </div>
            </div>

            {{-- Revisi Jasa --}}
            <div class="border-t-2 border-black p-2 bg-gray-50">
                <div class="font-bold text-xs mb-1">Revisi Jasa</div>
                <div class="grid grid-cols-3 gap-2 text-[9px]">
                    <div class="border-b border-gray-300 pb-1">_____________________</div>
                    <div class="border-b border-gray-300 pb-1">_____________________</div>
                    <div class="border-b border-gray-300 pb-1">_____________________</div>
                </div>
            </div>

            {{-- Brand Footer --}}
            <div class="flex items-center justify-between p-3 teal-bg text-white">
                <div class="flex items-center gap-2">
                    <div class="text-2xl font-black">#</div>
                    <div>
                        <div class="text-xs font-bold leading-tight">living</div>
                        <div class="text-xs font-bold leading-tight">with</div>
                        <div class="text-xs font-bold leading-tight">passion</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xl font-black">Shoe Workshop</div>
                    <div class="text-[9px]">#moretherepair</div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Optional: Auto print on load
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
