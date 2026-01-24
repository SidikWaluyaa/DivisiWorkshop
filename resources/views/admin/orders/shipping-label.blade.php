<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Label - {{ $order->customer_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 20cm 16cm;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            margin: 0;
            padding: 0;
            width: 20cm;
            height: 16cm;
            max-width: 20cm;
            max-height: 16cm;
            background-color: #ffffff;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            overflow: hidden !important;
            color: #1a1a1a;
        }
        .canvas {
            width: 20cm;
            height: 16cm;
            position: relative;
            overflow: hidden;
            background-color: #ffffff;
        }
        .marble-texture {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.3' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.02'/%3E%3C/svg%3E");
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="canvas">
        <div class="marble-texture"></div>

        {{-- Background Shapes (Smaller and lighter) --}}
        <div class="absolute -top-12 -left-12 w-64 h-56 bg-[#FFC232] rounded-[60px] rotate-[15deg] z-10 opacity-60 border-[6px] border-white shadow-sm"></div>
        <div class="absolute top-8 -left-16 w-56 h-48 bg-[#22B086] rounded-[50px] rotate-[-5deg] z-10 opacity-70 border-[6px] border-white shadow-sm"></div>
        <div class="absolute -bottom-12 -right-12 w-[350px] h-48 bg-[#22B086] rounded-tl-[80px] z-10 border-[6px] border-white shadow-xl opacity-80"></div>

        {{-- Main Container --}}
        <div class="relative w-full h-full p-8 flex flex-col z-20">
            
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

            {{-- Shipping Card (COMPACT INTERNALS) --}}
            <div class="relative flex-1 w-full bg-white border-[2px] border-[#FFC232] rounded-[20px] rounded-br-[70px] overflow-hidden flex flex-col shadow-xl">
                
                {{-- To Badge --}}
                <div class="absolute top-0 left-0 bg-[#22B086] text-white px-6 py-1.5 rounded-br-[25px] z-30 shadow-sm">
                    <span class="font-black text-[9px] tracking-widest uppercase">To : our beloved customer</span>
                </div>

                {{-- Address Content (Scaled down by ~30%) --}}
                <div class="flex-1 px-10 pt-10 flex flex-col justify-center">
                    <div class="mb-4">
                        <h2 class="text-5xl font-black text-slate-900 uppercase tracking-tighter leading-none mb-3">{{ $order->customer_name }}</h2>
                        <div class="space-y-0.5 border-l-[4px] border-[#22B086] pl-5 py-0.5">
                            <p class="text-xl font-bold text-slate-700 uppercase tracking-tight">
                                {{ $order->customer_address ?? $order->customer?->address ?? 'Alamat tidak tersedia' }}
                            </p>
                            <p class="text-sm font-semibold text-slate-400 uppercase tracking-widest leading-none">
                                {{ $order->customer?->district ?? $order->customer_district ?? '-' }} | {{ $order->customer?->city ?? $order->customer_city ?? '-' }}
                            </p>
                            <p class="text-sm font-semibold text-slate-400 uppercase tracking-widest leading-none">
                                {{ $order->customer?->province ?? $order->customer_province ?? '-' }} - {{ $order->customer?->postal_code ?? $order->customer_postal_code ?? '-' }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-2 flex items-center gap-3">
                        <div class="w-8 h-8 bg-emerald-500 text-white rounded-lg flex items-center justify-center shadow-md border border-white">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.27c1.12.44 2.33.68 3.58.68.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.24 2.45.68 3.58.11.41.02.83-.27 1.11l-2.2 2.2z"/></svg>
                        </div>
                        <span class="text-3xl font-[1000] text-emerald-600 tracking-tighter font-mono">{{ $order->customer_phone ?? $order->customer?->phone ?? '-' }}</span>
                    </div>
                </div>

                {{-- Footer Area --}}
                <div class="h-24 border-t-[2px] border-[#FFC232] flex bg-white">
                    {{-- Logo Section --}}
                    <div class="w-[28%] flex items-center justify-center border-r-[2px] border-[#FFC232] px-4">
                        <div class="flex items-center gap-2">
                            <div class="relative w-8 h-6 flex flex-col justify-center shrink-0">
                                <div class="absolute top-0 right-0 w-6 h-2.5 bg-[#FFC232] rounded-full shadow-sm"></div>
                                <div class="absolute bottom-0 left-0 w-6 h-2.5 bg-[#22B086] rounded-full shadow-sm"></div>
                            </div>
                            <div class="leading-[0.85] text-left uppercase shrink-0">
                                <span class="block text-xl font-extrabold text-[#22B086]">Shoe</span>
                                <span class="block text-xl font-light text-[#22B086]">Workshop</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Location Section --}}
                    <div class="w-[32%] border-r-[2px] border-[#FFC232] px-6 py-4 flex items-center gap-4">
                        <div class="flex flex-col gap-1.5 text-slate-800 shrink-0">
                             <svg class="w-4 h-4 text-[#22B086]" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
                             <svg class="w-4 h-4 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        </div>
                        <div class="leading-tight">
                            <p class="font-black text-slate-800 text-[9px] uppercase tracking-tighter">Location</p>
                            <p class="text-slate-500 text-[8.5px] font-bold tracking-tight leading-none mb-0.5">Jl. Kembar I No. 41, Bandung</p>
                            <p class="text-[#22B086] text-[8.5px] font-[1000] mt-0.5 border-b border-[#22B086] w-fit">WWW.SHOEWORKSHOP.ID</p>
                        </div>
                    </div>

                    {{-- Socials Section --}}
                    <div class="flex-1 flex px-5 relative group overflow-hidden">
                        <div class="flex divide-x-2 divide-slate-100 w-full relative z-10 py-3">
                            <div class="pr-4 flex-1 flex flex-col justify-center">
                                <p class="font-black text-slate-800 text-[8px] uppercase mb-1.5 leading-none tracking-tighter">Stay Updated</p>
                                <div class="space-y-1 text-[8px] text-slate-600 font-bold uppercase">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3 h-3 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        <span>08877234545</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3 h-3 text-[#E1306C]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                        <span class="lowercase">shoe_workshop</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3 h-3 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                                        <span class="lowercase">shoe.workshop</span>
                                    </div>
                                </div>
                            </div>
                            <div class="pl-4 flex-1 flex flex-col justify-center">
                                <p class="font-black text-slate-800 text-[8px] uppercase mb-1.5 leading-none tracking-tighter">Our Media</p>
                                <div class="space-y-1 text-[8px] text-slate-600 font-bold uppercase">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3 h-3 text-[#FF0000]" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                        <span>Shoe Police</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3 h-3 text-[#E1306C]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                        <span class="lowercase">shoepolice_</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3 h-3 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                                        <span class="lowercase">shoepolice_</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Yellow Pillar --}}
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-8 h-14 bg-[#FFC232] rounded-l-full translate-x-4 z-0 opacity-80"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = () => {
             setTimeout(() => {
                 window.print();
             }, 800);
        };
    </script>
</body>
</html>
