<x-app-layout>
<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
            <div>
                <a href="{{ route('manifest.index') }}" class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-[#22AF85] transition-colors mb-4 group">
                    <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar
                </a>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Detail <span class="text-[#22AF85]">Manifest</span></h1>
                <p class="text-sm text-gray-500 mt-1 font-black uppercase tracking-[0.2em]">{{ $manifest->manifest_number }}</p>
            </div>
            
            <div class="flex items-center space-x-4">
                @if($manifest->status === 'SENT')
                    <form action="{{ route('manifest.receive', $manifest->id) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa semua {{ $manifest->workOrders->count() }} pasang sepatu telah diterima secara fisik di Workshop Hijau?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-8 py-4 bg-[#FFC232] border border-transparent rounded-2xl font-black text-sm text-gray-900 uppercase tracking-[0.2em] hover:bg-[#e6af2e] shadow-lg shadow-yellow-200/50 transition-all active:scale-95">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Konfirmasi Terima
                        </button>
                    </form>
                @else
                    <div class="inline-flex items-center px-8 py-4 bg-[#22AF85]/10 text-[#22AF85] border border-[#22AF85]/20 rounded-2xl font-black text-sm uppercase tracking-[0.2em] shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        BARANG DITERIMA
                    </div>
                @endif

                <button onclick="window.print()" class="p-4 bg-white border border-gray-200 text-gray-400 rounded-2xl hover:text-gray-600 hover:border-gray-300 transition-all shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar Info (Web Only) -->
            <div class="lg:col-span-1 space-y-6 print:hidden">
                <!-- Status & Logistik -->
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 p-8">
                    <h2 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6 flex items-center">
                        <span class="w-1 h-3 bg-[#22AF85] rounded-full mr-2"></span>
                        Status Pengiriman
                    </h2>

                    <div class="space-y-8 relative before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
                        <div class="relative pl-12">
                            <div class="absolute left-0 w-10 h-10 rounded-full bg-[#22AF85] flex items-center justify-center text-white shadow-[0_0_15px_rgba(34,175,133,0.4)] z-10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                            </div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">DIKIRIM (GUDANG)</p>
                            <p class="text-sm font-black text-gray-900 mt-1">{{ $manifest->dispatcher->name }}</p>
                            <p class="text-[11px] text-gray-400 mt-0.5 font-bold uppercase tracking-tight">{{ $manifest->dispatched_at->format('d M Y • H:i') }}</p>
                        </div>

                        @if($manifest->status === 'RECEIVED')
                        <div class="relative pl-12">
                            <div class="absolute left-0 w-10 h-10 rounded-full bg-[#FFC232] flex items-center justify-center text-gray-900 shadow-[0_0_15px_rgba(255,194,50,0.4)] z-10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">DITERIMA (WORKSHOP)</p>
                            <p class="text-sm font-black text-gray-900 mt-1">{{ $manifest->receiver->name }}</p>
                            <p class="text-[11px] text-gray-400 mt-0.5 font-bold uppercase tracking-tight">{{ $manifest->received_at->format('d M Y • H:i') }}</p>
                        </div>
                        @else
                        <div class="relative pl-12">
                            <div class="absolute left-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-300 z-10 border-4 border-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic">MENUNGGU KONFIRMASI...</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($manifest->notes)
                <div class="bg-[#22AF85] rounded-3xl p-8 text-white shadow-xl shadow-[#22AF85]/20 relative overflow-hidden">
                    <svg class="absolute -right-8 -bottom-8 w-32 h-32 text-white/10" fill="currentColor" viewBox="0 0 20 20"><path d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"></path></svg>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-4 opacity-60">Catatan Dispatcher</h3>
                    <p class="text-sm font-bold leading-relaxed italic">"{{ $manifest->notes }}"</p>
                </div>
                @endif
            </div>

            <!-- Main Items List & Print Content -->
            <div class="lg:col-span-2 print:col-span-3">
                <!-- Status Pengiriman Box (Print Only) -->
                <div class="hidden print:block mb-6 p-4 border-2 border-black">
                    <table class="w-full text-xs text-black" style="border: none !important;">
                        <tr style="border: none !important;">
                            <td class="font-bold border-none py-1 px-2 text-left" style="width: 25%; border: none !important;">STATUS PENGIRIMAN</td>
                            <td class="border-none py-1 px-2 text-left" style="width: 37.5%; border: none !important;">
                                <strong class="uppercase">Dikirim (Gudang):</strong><br>
                                {{ $manifest->dispatcher->name }}<br>
                                <span class="italic text-[10px]">{{ $manifest->dispatched_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="border-none py-1 px-2 text-left" style="width: 37.5%; border: none !important;">
                                <strong class="uppercase">Diterima (Workshop):</strong><br>
                                {{ $manifest->receiver ? $manifest->receiver->name : 'MENUNGGU KONFIRMASI' }}<br>
                                <span class="italic text-[10px]">{{ $manifest->received_at ? $manifest->received_at->format('d/m/Y H:i') : '-' }}</span>
                            </td>
                        </tr>
                        @if($manifest->notes)
                        <tr style="border: none !important;">
                            <td class="font-bold border-none pt-2 px-2 text-left" style="border: none !important;">CATATAN:</td>
                            <td colspan="2" class="border-none pt-2 px-2 italic text-left" style="border: none !important;">"{{ $manifest->notes }}"</td>
                        </tr>
                        @endif
                    </table>
                </div>

                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden print:border-black print:rounded-none">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-white/50 backdrop-blur-sm print:bg-white print:border-black print:py-3 print:px-4">
                        <h2 class="text-lg font-bold text-gray-800 print:text-black print:text-sm print:font-extrabold">Daftar Barang Bawaan</h2>
                        <span class="text-xs font-black px-4 py-1.5 bg-gray-100 text-gray-500 rounded-lg uppercase tracking-widest print:bg-white print:text-black print:border print:border-black print:py-0.5 print:px-2 print:text-[10px]">{{ $manifest->workOrders->count() }} Pasang Sepatu</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 print:border-black">
                            <thead>
                                <tr class="bg-gray-50/50 print:bg-white print:border-black">
                                    <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] print:text-black print:font-black">Data Order</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] print:text-black print:font-black">Detail Item</th>
                                    <th class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] print:text-black print:font-black">Priority</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 print:divide-black">
                                @foreach($manifest->workOrders as $order)
                                <tr class="hover:bg-[#22AF85]/[0.02] transition-colors group print:border-black">
                                    <td class="px-8 py-6 whitespace-nowrap print:py-2 print:px-4">
                                        <p class="text-sm font-black text-[#22AF85] tracking-tight print:text-black print:font-bold">{{ $order->spk_number }}</p>
                                        <div class="text-[11px] text-gray-400 font-bold mt-0.5 uppercase tracking-tighter print:text-black print:font-normal">{{ $order->customer_name }}</div>
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap print:py-2 print:px-4">
                                        <div class="text-xs font-bold text-gray-700 tracking-tight group-hover:text-gray-900 transition-colors uppercase print:text-black print:font-bold">{{ $order->shoe_brand }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter mt-1 print:text-black print:font-normal">{{ $order->shoe_type }} • {{ $order->shoe_color }} • SZ {{ $order->shoe_size }}</div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-center print:py-2 print:px-4">
                                        @if($order->priority === 'Prioritas' || $order->priority === 'Urgent' || $order->priority === 'Express')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-red-50 text-red-600 text-[10px] font-black border border-red-100 uppercase italic print:border-black print:text-black print:bg-white print:not-italic print:font-black">
                                                {{ $order->priority }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-gray-50 text-gray-500 text-[10px] font-black border border-gray-100 uppercase tracking-tighter print:border-black print:text-black print:bg-white print:font-bold">
                                                {{ $order->priority }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8 bg-gray-50 border border-dashed border-gray-200 rounded-3xl p-8 text-center print:border-black print:bg-white print:rounded-none print:p-4 print:mt-4">
                    <div class="max-w-md mx-auto">
                        <svg class="w-12 h-12 text-gray-200 mx-auto mb-4 print:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-2 print:text-black print:font-bold print:text-xs">Pemeriksaan Barang</h4>
                        <p class="text-xs text-gray-400 leading-relaxed italic print:text-black print:not-italic print:font-medium print:text-[11px]">"Manifest ini adalah bukti tanda terima sah antara Gudang dan Workshop Hijau. Pastikan jumlah fisik cocok dengan jumlah di sistem sebelum konfirmasi."</p>
                    </div>
                </div>

                <!-- Print Signature Box (Guaranteed Side-by-Side Table Layout) -->
                <div class="hidden print:block mt-8 pt-4 border-t-2 border-black">
                    <table class="w-full text-center text-black" style="border: none !important;">
                        <tr style="border: none !important;">
                            <td style="width: 50%; border: none !important;" class="align-top text-center px-4">
                                <p class="text-xs font-bold uppercase tracking-wider text-black mb-1">DIKIRIM OLEH (GUDANG)</p>
                                <p class="text-[10px] text-black italic">{{ $manifest->dispatched_at->format('d/m/Y H:i') }}</p>
                                <div style="height: 60px;"></div>
                                <p class="text-xs font-bold text-black border-t border-black pt-1 inline-block px-6">( {{ $manifest->dispatcher->name }} )</p>
                            </td>
                            <td style="width: 50%; border: none !important;" class="align-top text-center px-4">
                                <p class="text-xs font-bold uppercase tracking-wider text-black mb-1">DITERIMA OLEH (WORKSHOP)</p>
                                <p class="text-[10px] text-black italic">{{ $manifest->received_at ? $manifest->received_at->format('d/m/Y H:i') : 'Tanggal & Jam: ....................' }}</p>
                                <div style="height: 60px;"></div>
                                <p class="text-xs font-bold text-black border-t border-black pt-1 inline-block px-6">( {{ $manifest->receiver ? $manifest->receiver->name : '........................................' }} )</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        /* Force All Colors to Solid Black & Clean White Background */
        *, *::before, *::after {
            color: #000000 !important;
            background: transparent !important;
            box-shadow: none !important;
            text-shadow: none !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html, body, 
        div.min-h-screen, 
        div.main-content, 
        main, 
        div.py-6, 
        div.py-12 { 
            overflow: visible !important; 
            height: auto !important; 
            min-height: 0 !important;
            display: block !important;
            float: none !important;
            position: static !important;
            padding: 0 !important;
            margin: 0 !important;
            background-color: #ffffff !important;
        }

        .max-w-7xl { 
            max-width: 100% !important; 
            padding: 0 !important; 
            width: 100% !important; 
            margin: 0 !important;
        }
        
        div.overflow-hidden, div.overflow-x-auto, .bg-white, .bg-gray-50 { 
            overflow: visible !important; 
            height: auto !important; 
            display: block !important;
            border: none !important;
            box-shadow: none !important;
            background: #ffffff !important;
        }

        /* High-Contrast Table Styling */
        table { 
            width: 100% !important; 
            border-collapse: collapse !important; 
            table-layout: auto !important;
        }

        /* Badges for High Contrast Printing */
        span.inline-flex {
            border: 1px solid #000000 !important;
            color: #000000 !important;
            background: #ffffff !important;
            font-weight: 900 !important;
            padding: 2px 8px !important;
            border-radius: 4px !important;
        }

        /* Hide Web Navigation, Buttons, Icons */
        header, aside, .sidebar-collapsed, .lg\:ml-64, 
        nav, button, a, form, .flex.items-center.space-x-4,
        svg.w-5.h-5, svg.w-6.h-6, .before\:absolute { 
            display: none !important; 
        }

        /* Header Info Layout */
        .mb-8 { 
            display: block !important;
            border-bottom: 2px solid #000000 !important;
            padding-bottom: 12px !important;
            margin-bottom: 16px !important;
        }

        h1 {
            font-size: 22px !important;
            font-weight: 900 !important;
            color: #000000 !important;
        }

        h1 span {
            color: #000000 !important;
        }
    }
</style>
</x-app-layout>
