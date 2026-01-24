<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK - {{ $order->spk_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        
        @page { size: A4; margin: 0; }
        
        .page-container {
            width: 210mm; 
            height: 296mm; /* Reduced 1mm to prevent slight overflow */
            background: white; 
            position: relative;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            overflow: hidden; 
        }

        .content-area {
            padding: 0 20mm;
            flex-grow: 1;
            overflow: hidden; /* Critical: Truncate content if it pushes footer */
        }

        @media print {
            body { background: white; margin: 0; padding: 0; }
            .page-container { box-shadow: none; width: 210mm; height: 296mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8 flex justify-center items-center h-screen overflow-hidden print:p-0 print:h-auto print:overflow-visible">

    {{-- Print Controls --}}
    <div class="fixed top-4 right-4 no-print flex gap-2 z-50">
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white font-bold rounded shadow hover:bg-blue-700">Print SPK</button>
    </div>

    <!-- MAIN PAGE CONTAINER (Flex Column) -->
    <div class="page-container shadow-2xl">
        
        <!-- ROW 1: HEADER -->
        <header class="h-24 bg-teal-700 flex items-center justify-between px-[20mm] relative shrink-0">
            <div class="flex items-center gap-3">
                {{-- Logo with Fallback --}}
                <img src="{{ asset('images/logo-white.png') }}" class="h-10 w-auto" onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                <div class="text-white" style="display: none;"> {{-- Show this if img fails --}}
                    <h1 class="font-black text-xl leading-none">SHOE</h1>
                    <h1 class="font-light text-lg leading-none">WORKSHOP</h1>
                </div>
            </div>
            <div class="text-right text-white">
                <p class="text-xs font-medium opacity-80">Form</p>
                <h2 class="text-2xl font-bold">SPK Customer</h2>
            </div>
            <!-- Teal decorative line -->
            <div class="absolute bottom-0 left-0 w-full h-1.5 bg-teal-800"></div>
        </header>

        <!-- ROW 2: CONTENT (Flex Grow) -->
        <main class="content-area pt-6 flex flex-col gap-6">
            
            {{-- Top Info Grid --}}
            <div class="grid grid-cols-12 gap-6 shrink-0">
                {{-- Left: Photo --}}
                <div class="col-span-5">
                    <div class="aspect-[4/5] bg-gray-100 rounded-xl overflow-hidden border-2 border-gray-200 relative">
                         @if($order->photos->where('step', 'WAREHOUSE_BEFORE')->count() > 0)
                            <img src="{{ asset('storage/' . $order->photos->where('step', 'WAREHOUSE_BEFORE')->first()->file_path) }}" class="w-full h-full object-cover">
                        @elseif($order->photos->count() > 0)
                            <img src="{{ asset('storage/' . $order->photos->first()->file_path) }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-300 font-bold">No Photo</div>
                        @endif
                        
                        {{-- Size Badge --}}
                        <div class="absolute bottom-3 right-3 bg-white border-2 border-teal-600 rounded flex flex-col items-center justify-center w-10 h-10 shadow-lg">
                            <span class="text-[7px] font-bold text-gray-500 uppercase leading-none mt-0.5">Size</span>
                            <span class="text-lg font-black text-gray-900 leading-none">{{ $order->shoe_size }}</span>
                        </div>
                    </div>
                </div>

                {{-- Middle: Info --}}
                <div class="col-span-4 space-y-2">
                    <div class="bg-gray-50 px-2.5 py-2 rounded border border-gray-100">
                        <p class="text-[9px] font-bold text-gray-400 uppercase">Nomor SPK</p>
                        <p class="text-xs font-bold text-gray-900">{{ $order->spk_number }}</p>
                    </div>
                    <div class="bg-gray-50 px-2.5 py-2 rounded border border-gray-100">
                        <p class="text-[9px] font-bold text-gray-400 uppercase">Nama Customer</p>
                        <p class="text-xs font-bold text-gray-900">{{ $order->customer_name }}</p>
                    </div>
                    <div class="bg-gray-50 px-2.5 py-2 rounded border border-gray-100 min-h-[50px]">
                        <p class="text-[9px] font-bold text-gray-400 uppercase">Alamat Customer</p>
                        <p class="text-[10px] font-medium text-gray-900 leading-snug mt-1">
                            {{ $order->customer_address ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Right: Accessories (Mapped to Warehouse Reception Data) --}}
                <div class="col-span-3 pt-1">
                     @php $acc = $order; @endphp
                     <div class="space-y-2">
                        {{-- Insole (Simpan) --}}
                        <div class="flex items-center gap-2">
                            <div class="w-3.5 h-3.5 border border-gray-400 rounded-sm flex items-center justify-center {{ in_array($acc->accessories_insole, ['Simpan', 'S']) ? 'bg-teal-600 border-teal-600' : 'bg-white' }}">
                                @if(in_array($acc->accessories_insole, ['Simpan', 'S'])) <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> @endif
                            </div>
                            <span class="text-[9px] font-bold text-gray-600">Insole (Simpan)</span>
                        </div>
                        {{-- Insole (Nempel) --}}
                        <div class="flex items-center gap-2">
                            <div class="w-3.5 h-3.5 border border-gray-400 rounded-sm flex items-center justify-center {{ in_array($acc->accessories_insole, ['Nempel', 'N']) ? 'bg-teal-600 border-teal-600' : 'bg-white' }}">
                                @if(in_array($acc->accessories_insole, ['Nempel', 'N'])) <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> @endif
                            </div>
                            <span class="text-[9px] font-bold text-gray-600">Insole (Nempel)</span>
                        </div>
                        {{-- Tali (Simpan) --}}
                        <div class="flex items-center gap-2">
                            <div class="w-3.5 h-3.5 border border-gray-400 rounded-sm flex items-center justify-center {{ in_array($acc->accessories_tali, ['Simpan', 'S']) ? 'bg-teal-600 border-teal-600' : 'bg-white' }}">
                                @if(in_array($acc->accessories_tali, ['Simpan', 'S'])) <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> @endif
                            </div>
                            <span class="text-[9px] font-bold text-gray-600">Tali (Simpan)</span>
                        </div>
                        {{-- Box --}}
                        <div class="flex items-center gap-2">
                            <div class="w-3.5 h-3.5 border border-gray-400 rounded-sm flex items-center justify-center {{ in_array($acc->accessories_box, ['Simpan', 'S']) ? 'bg-teal-600 border-teal-600' : 'bg-white' }}">
                                @if(in_array($acc->accessories_box, ['Simpan', 'S'])) <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> @endif
                            </div>
                            <span class="text-[9px] font-bold text-gray-600">Box Sepatu</span>
                        </div>
                         {{-- Custom --}}
                         <div class="flex items-center gap-2">
                             <div class="w-3.5 h-3.5 border border-gray-400 rounded-sm flex items-center justify-center {{ ($acc->accessories_other && $acc->accessories_other != 'Tidak Ada') ? 'bg-teal-600 border-teal-600' : 'bg-white' }}">
                                @if($acc->accessories_other && $acc->accessories_other != 'Tidak Ada') <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> @endif
                             </div>
                             <span class="text-[9px] font-bold text-gray-600 border-b border-gray-300 w-16 pb-px leading-none truncate block h-3">
                                {{ ($acc->accessories_other && $acc->accessories_other != 'Tidak Ada') ? $acc->accessories_other : '' }}
                             </span>
                        </div>
                     </div>
                </div>
            </div>

            {{-- Services Grid --}}
            <div class="flex-grow">
                <h3 class="text-[10px] font-bold text-gray-500 uppercase mb-2">Jasa / Layanan</h3>
                <div class="space-y-3">
                    @foreach($order->workOrderServices as $service)
                    <div class="group relative">
                         <div class="bg-[#fdb913] text-gray-900 px-4 py-1.5 text-xs font-black uppercase tracking-wider rounded-t-lg shadow-sm">
                            {{ $service->custom_service_name ?? $service->service->name ?? 'Custom Service' }}
                         </div>
                         <div class="bg-white border-x border-b border-gray-200 p-3 rounded-b-lg flex gap-4 shadow-sm text-xs">
                             <div class="flex-1">
                                 <p class="text-[9px] font-bold text-gray-400 uppercase">Notes</p>
                                 <p class="font-medium text-gray-800 leading-snug">
                                    @if(is_array($service->service_details))
                                        {{ implode(', ', array_map(function($k, $v) { return "$k: $v"; }, array_keys($service->service_details), $service->service_details)) }}
                                    @else
                                        -
                                    @endif
                                 </p>
                             </div>
                             <div class="w-16 border-l border-gray-100 pl-4 flex flex-col items-center justify-center opacity-50">
                                 <div class="w-5 h-5 border-2 border-gray-300 rounded"></div>
                                 <span class="text-[8px] font-bold text-gray-400 mt-1">QC</span>
                             </div>
                         </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </main>

        <!-- ROW 3: FOOTER (Mt-Auto) -->
        <footer class="mt-auto shrink-0">
            <div class="px-[20mm] pb-4">
                 <div class="flex gap-4 items-stretch h-28">
                    {{-- 1. Notes --}}
                    <div class="w-[40%] flex flex-col">
                        <div class="bg-gray-50 rounded p-2 border border-gray-200 flex-1 relative">
                            <p class="text-[9px] font-bold text-gray-500 uppercase">Keterangan Besar :</p>
                            <p class="text-[10px] font-bold text-gray-900 leading-snug mt-1">
                                {{ $order->technician_notes ?? $order->notes ?? '-' }}
                            </p>
                            @if($order->priority == 'High')
                                <div class="absolute bottom-2 right-2 px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-black rounded uppercase">URGENT</div>
                            @endif
                        </div>
                    </div>
                    {{-- 2. Signatures --}}
                    <div class="w-[30%] flex gap-2">
                        <div class="flex-1 flex flex-col">
                            <div class="bg-[#009b77] text-white text-center py-0.5 font-bold text-[8px] uppercase rounded-t">ACC Follow Up</div>
                            <div class="border-x border-b border-gray-200 flex-1 bg-gray-50/30 rounded-b"></div>
                        </div>
                        <div class="flex-1 flex flex-col">
                            <div class="bg-[#009b77] text-white text-center py-0.5 font-bold text-[8px] uppercase rounded-t">ACC QC</div>
                            <div class="border-x border-b border-gray-200 flex-1 bg-gray-50/30 rounded-b"></div>
                        </div>
                    </div>
                    {{-- 3. Dates --}}
                    <div class="w-[30%] flex flex-col gap-2">
                        <div class="border border-gray-200 rounded p-1.5 space-y-1 bg-gray-50">
                            <div class="flex justify-between items-center text-[9px]">
                                <span class="font-bold text-gray-500">Masuk</span>
                                <span class="font-black text-gray-900">{{ $order->created_at->format('d/m/y') }}</span>
                            </div>
                             <div class="flex justify-between items-center text-[9px] border-t border-gray-200 pt-1">
                                <span class="font-bold text-gray-500">Est.</span>
                                <span class="font-black text-gray-900">{{ $order->estimation_date ? \Carbon\Carbon::parse($order->estimation_date)->format('d/m/y') : '-' }}</span>
                            </div>
                             <div class="flex justify-between items-center text-[9px] border-t border-gray-200 pt-1">
                                <span class="font-bold text-gray-500">Keluar</span>
                                <span class="font-black text-gray-900">__/__/__</span>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded p-1.5 flex-1 bg-gray-50">
                             <p class="text-[8px] font-bold text-gray-500 uppercase">Revisi :</p>
                        </div>
                    </div>
                 </div>
            </div>

            {{-- Footer Branding Bar --}}
            <div class="h-14 bg-[#009b77] flex items-center justify-between px-[20mm] text-white relative overflow-hidden">
                <div class="flex items-center gap-3 relative z-10">
                    <span class="text-3xl font-black opacity-30 -mt-1">#</span>
                    <div class="leading-[0.9] text-[9px] uppercase tracking-widest font-black flex flex-col justify-center">
                        <span class="block">Living</span>
                        <span class="block">With</span>
                        <span class="block text-[#fdb913]">Passion</span>
                    </div>
                </div>
                <div class="text-right relative z-10">
                    <h3 class="font-black text-lg tracking-tight leading-none mb-0.5">SHOE WORKSHOP</h3>
                    <p class="text-[7px] font-bold tracking-[0.3em] text-[#fdb913] uppercase leading-none">More Than Repair</p>
                </div>
            </div>
        </footer>

    </div>

    <script>
        window.onload = function() {
            // Optional: Check height?
             window.print();
        }
    </script>
</body>
</html>
