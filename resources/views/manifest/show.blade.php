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
            <!-- Sidebar Info -->
            <div class="lg:col-span-1 space-y-6">
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

            <!-- Main Items List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-white/50 backdrop-blur-sm">
                        <h2 class="text-lg font-bold text-gray-800">Daftar Barang Bawaan</h2>
                        <span class="text-xs font-black px-4 py-1.5 bg-gray-100 text-gray-500 rounded-lg uppercase tracking-widest">{{ $manifest->workOrders->count() }} Pasang Sepatu</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Data Order</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Detail Item</th>
                                    <th class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Priority</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($manifest->workOrders as $order)
                                <tr class="hover:bg-[#22AF85]/[0.02] transition-colors group">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <p class="text-sm font-black text-[#22AF85] tracking-tight">{{ $order->spk_number }}</p>
                                        <div class="text-[11px] text-gray-400 font-bold mt-0.5 uppercase tracking-tighter">{{ $order->customer_name }}</div>
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        <div class="text-xs font-bold text-gray-700 tracking-tight group-hover:text-gray-900 transition-colors uppercase">{{ $order->shoe_brand }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter mt-1">{{ $order->shoe_type }} • {{ $order->shoe_color }} • SZ {{ $order->shoe_size }}</div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-center">
                                        @if($order->priority === 'Prioritas' || $order->priority === 'Urgent' || $order->priority === 'Express')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-red-50 text-red-600 text-[10px] font-black border border-red-100 uppercase italic">
                                                {{ $order->priority }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-gray-50 text-gray-500 text-[10px] font-black border border-gray-100 uppercase tracking-tighter">
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

                <div class="mt-8 bg-gray-50 border border-dashed border-gray-200 rounded-3xl p-8 text-center">
                    <div class="max-w-md mx-auto">
                        <svg class="w-12 h-12 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-2">Pemeriksaan Barang</h4>
                        <p class="text-xs text-gray-400 leading-relaxed italic">"Manifest ini adalah bukti tanda terima sah antara Gudang dan Workshop Hijau. Pastikan jumlah fisik cocok dengan jumlah di sistem sebelum konfirmasi."</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        header, aside, nav, .bg-gray-50\/50, button, a[href*="index"], .py-12 { background: white !important; padding: 0 !important; }
        .max-w-7xl { max-width: 100% !important; padding: 0 !important; }
        .grid { display: block !important; }
        .shadow-xl, .shadow-lg, .shadow-sm { box-shadow: none !important; border: 1px solid #eee !important; }
        .rounded-3xl, .rounded-2xl { border-radius: 8px !important; }
        .lg\:col-span-1, .lg\:col-span-2 { width: 100% !important; margin-bottom: 2rem !important; }
        .mb-8 { margin-bottom: 1rem !important; }
        a, button, .flex.items-center.space-x-4 { display: none !important; }
    }
</style>
</x-app-layout>
