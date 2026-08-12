<x-app-layout>
<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <a href="{{ route('manifest.show', $manifest->id) }}" class="inline-flex items-center text-sm font-bold text-slate-400 hover:text-teal-500 transition-colors mb-3 group">
                    <svg class="w-4 h-4 mr-1.5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Batal & Kembali ke Detail
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Serah Terima <span class="text-teal-500">Inbound Manifest</span>
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    Tinjau kondisi fisik SPK dan distribusikan Teknisi Prep (Cuci) sebelum memulai pengerjaan.
                </p>
            </div>
            
            <div class="px-5 py-3 bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center gap-3">
                <span class="text-xl animate-pulse">⚠️</span>
                <div class="text-xs font-bold uppercase tracking-wider">
                    Total: <span class="font-black text-sm">{{ $manifest->workOrders->count() }}</span> Pasang Sepatu
                </div>
            </div>
        </div>

        {{-- Main Form Grid --}}
        <form action="{{ route('manifest.receive', $manifest->id) }}" method="POST" onsubmit="return confirm('Konfirmasi serah terima fisik {{ $manifest->workOrders->count() }} SPK dan simpan pembagian teknisi?')">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Left: Item List --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden">
                        <div class="px-6 py-5 bg-slate-50/50 dark:bg-slate-850/50 border-b border-slate-150 dark:border-slate-800 flex justify-between items-center">
                            <h2 class="text-base font-black text-slate-800 dark:text-slate-200">Daftar Sepatu Dalam Batch</h2>
                            <span class="text-[10px] font-black px-2.5 py-1 bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg uppercase tracking-widest">
                                Manifest Items
                            </span>
                        </div>

                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($manifest->workOrders as $order)
                            <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:bg-slate-50/[0.02] transition-colors">
                                
                                {{-- Item Details --}}
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 bg-teal-500/10 text-teal-600 dark:text-teal-400 text-xs font-black rounded-lg border border-teal-500/20">
                                            {{ $order->spk_number }}
                                        </span>
                                        @if($order->priority === 'Prioritas' || $order->priority === 'Urgent' || $order->priority === 'Express')
                                            <span class="px-2 py-0.5 bg-rose-500/10 text-rose-500 text-[10px] font-black rounded-md border border-rose-500/20 uppercase animate-pulse">
                                                🔥 {{ $order->priority }}
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 truncate uppercase">
                                        {{ $order->shoe_brand }} <span class="text-slate-400 dark:text-slate-500 font-bold">•</span> {{ $order->shoe_type }}
                                    </h4>
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-400 dark:text-slate-500 font-semibold">
                                        <span>Customer: <strong class="text-slate-600 dark:text-slate-300">{{ $order->customer_name }}</strong></span>
                                        <span>•</span>
                                        <span>Warna: <strong class="text-slate-600 dark:text-slate-300">{{ $order->shoe_color }}</strong></span>
                                        <span>•</span>
                                        <span>Size: <strong class="text-slate-600 dark:text-slate-300">{{ $order->shoe_size }}</strong></span>
                                    </div>
                                </div>

                                {{-- Technician Assignment Dropdowns --}}
                                <div class="w-full md:w-80 shrink-0 space-y-4">
                                    {{-- 1. Washing (Always required) --}}
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">
                                            Teknisi Prep/Cuci
                                        </label>
                                        <select name="prep_washing_by[{{ $order->id }}]" class="w-full text-xs font-bold rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white py-2.5 px-3 shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                                            @foreach($candidates_washing as $tech)
                                                @php 
                                                    $isRecommended = $order->recommended_prep_washing_by === $tech->id;
                                                    $workload = \App\Models\WorkOrder::where('prep_washing_by', $tech->id)->whereNull('prep_washing_completed_at')->count();
                                                @endphp
                                                <option value="{{ $tech->id }}" {{ $isRecommended ? 'selected' : '' }}>
                                                    {{ $tech->name }} (W: {{ $workload }}){{ $isRecommended ? ' ★ Rekomendasi' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- 2. Bongkar Sol (Only if needs_prep_sol) --}}
                                    @if($order->needs_prep_sol)
                                    <div>
                                        <label class="block text-[10px] font-black text-orange-500 uppercase tracking-widest mb-1.5">
                                            Teknisi Bongkar Sol
                                        </label>
                                        <select name="prep_sol_by[{{ $order->id }}]" class="w-full text-xs font-bold rounded-xl border-orange-200 dark:border-orange-900/40 dark:bg-slate-950 dark:text-white py-2.5 px-3 shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                                            @foreach($candidates_sol as $tech)
                                                @php 
                                                    $isRecommended = $order->recommended_prep_sol_by === $tech->id;
                                                    $workload = \App\Models\WorkOrder::where('prep_sol_by', $tech->id)->whereNull('prep_sol_completed_at')->count();
                                                @endphp
                                                <option value="{{ $tech->id }}" {{ $isRecommended ? 'selected' : '' }}>
                                                    {{ $tech->name }} (W: {{ $workload }}){{ $isRecommended ? ' ★ Rekomendasi' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    {{-- 3. Bongkar Upper (Only if needs_prep_upper) --}}
                                    @if($order->needs_prep_upper)
                                    <div>
                                        <label class="block text-[10px] font-black text-purple-500 uppercase tracking-widest mb-1.5">
                                            Teknisi Bongkar Upper
                                        </label>
                                        <select name="prep_upper_by[{{ $order->id }}]" class="w-full text-xs font-bold rounded-xl border-purple-200 dark:border-purple-900/40 dark:bg-slate-950 dark:text-white py-2.5 px-3 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                                            @foreach($candidates_upper as $tech)
                                                @php 
                                                    $isRecommended = $order->recommended_prep_upper_by === $tech->id;
                                                    $workload = \App\Models\WorkOrder::where('prep_upper_by', $tech->id)->whereNull('prep_upper_completed_at')->count();
                                                @endphp
                                                <option value="{{ $tech->id }}" {{ $isRecommended ? 'selected' : '' }}>
                                                    {{ $tech->name }} (W: {{ $workload }}){{ $isRecommended ? ' ★ Rekomendasi' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Right: Sidebar info & actions --}}
                <div class="space-y-6">
                    
                    {{-- Manifest Info Card --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-800 p-6 space-y-5">
                        <h3 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] flex items-center">
                            <span class="w-1.5 h-3 bg-teal-500 rounded-full mr-2"></span>
                            Ringkasan Batch Inbound
                        </h3>
                        
                        <div class="space-y-4 text-xs font-semibold">
                            <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400">No. Manifest</span>
                                <span class="text-slate-800 dark:text-slate-200 font-black">{{ $manifest->manifest_number }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400">Pengirim (Dispatcher)</span>
                                <span class="text-slate-800 dark:text-slate-200 font-bold">{{ $manifest->dispatcher->name }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400">Tanggal Pengiriman</span>
                                <span class="text-slate-800 dark:text-slate-200 font-bold">{{ $manifest->dispatched_at->format('d M Y - H:i') }}</span>
                            </div>
                            @if($manifest->notes)
                            <div class="pt-2">
                                <span class="text-slate-400 block mb-1">Catatan Pengirim:</span>
                                <p class="text-slate-600 dark:text-slate-300 italic bg-slate-50 dark:bg-slate-950 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                                    "{{ $manifest->notes }}"
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions Card --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-800 p-6 space-y-4">
                        <button type="submit" class="w-full flex items-center justify-center py-4 bg-[#FFC232] hover:bg-[#e6af2e] text-slate-950 font-black text-sm uppercase tracking-[0.2em] rounded-2xl shadow-lg shadow-yellow-300/40 dark:shadow-none transition-all hover:-translate-y-0.5 active:translate-y-0">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Terima & Mulai Prep
                        </button>
                        
                        <a href="{{ route('manifest.show', $manifest->id) }}" class="w-full flex items-center justify-center py-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-350 font-bold text-xs uppercase tracking-widest rounded-2xl transition-all">
                            Batal
                        </a>
                    </div>

                </div>

            </div>
        </form>

    </div>
</div>
</x-app-layout>
