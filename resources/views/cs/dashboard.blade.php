<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg" style="background-color: #22AF85">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight uppercase">
                        {{ __('CS Hub') }}
                    </h2>
                    <p class="text-xs font-bold text-gray-500 tracking-widest uppercase opacity-70">Sales Pipeline Monitoring</p>
                </div>
            </div>
            <button @click="$dispatch('open-new-lead')" class="text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl transition transform hover:scale-105" style="background-color: #22AF85">
                ➕ Lead Baru
            </button>
        </div>
    </x-slot>

    <div x-data="csDashboard" @open-new-lead.window="leadModalOpen = true" class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Premium Metrics Dashboard --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {{-- Metric: Today's Intake --}}
                <div class="bg-white rounded-[2rem] shadow-xl p-6 border border-gray-100 flex items-center justify-between group hover:border-[#22AF85] transition-all">
                    <div>
                        <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">New Leads Today</div>
                        <div class="text-3xl font-black text-gray-900 leading-none">{{ $metrics['new_leads_today'] }}</div>
                        <div class="mt-2 text-[10px] font-bold text-[#22AF85] uppercase tracking-tighter">Total Active: {{ $metrics['total_greeting'] + $metrics['total_konsultasi'] + $metrics['total_follow_up'] }}</div>
                    </div>
                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 group-hover:bg-[#22AF85]/10 group-hover:text-[#22AF85] transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </div>
                </div>

                {{-- Metric: Hot Leads --}}
                <div class="bg-white rounded-[2rem] shadow-xl p-6 border border-gray-100 flex items-center justify-between group hover:border-[#FFC232] transition-all">
                    <div>
                        <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 text-red-500">Hot Potential 🔥</div>
                        <div class="text-3xl font-black text-gray-900 leading-none">{{ $metrics['hot_leads'] }}</div>
                        <div class="mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Need follow up: {{ $metrics['needs_follow_up'] }}</div>
                    </div>
                    <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-500 shadow-sm animate-pulse">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                </div>

                {{-- Metric: Conversion --}}
                <div class="bg-white rounded-[2rem] shadow-xl p-6 border border-gray-100 flex items-center justify-between group hover:border-[#22AF85] transition-all">
                    <div>
                        <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Closing Today 🏆</div>
                        <div class="text-3xl font-black text-gray-900 leading-none">{{ $metrics['total_converted_today'] }}</div>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="text-[10px] font-black {{ $metrics['converted_trend'] >= 0 ? 'text-[#22AF85]' : 'text-red-500' }}">
                                {{ $metrics['converted_trend'] >= 0 ? '↑' : '↓' }} {{ abs(round($metrics['converted_trend'])) }}%
                            </span>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">vs Yesterday</span>
                        </div>
                    </div>
                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 group-hover:bg-[#22AF85]/10 group-hover:text-[#22AF85] transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>

                {{-- Metric: Rate --}}
                <div class="bg-white rounded-[2rem] shadow-xl p-6 border border-gray-100 flex items-center justify-between group transition-all">
                    <div>
                        <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Conversion Rate</div>
                        <div class="text-3xl font-black text-gray-900 leading-none">{{ $metrics['conversion_rate'] }}%</div>
                        <div class="mt-2 w-24 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-[#22AF85] rounded-full" style="width: {{ $metrics['conversion_rate'] }}%"></div>
                        </div>
                    </div>
                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                </div>
            </div>

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl relative">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Monitoring Pembayaran Workshop --}}
            @if(count($workshopPayments) > 0)
            <div class="mb-8 bg-white rounded-[2rem] shadow-lg overflow-hidden border border-orange-100 flex flex-col md:flex-row">
                <div class="bg-gradient-to-b from-orange-400 to-orange-600 p-6 flex flex-col justify-center text-white md:w-64">
                    <svg class="w-8 h-8 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <h3 class="font-black text-xl leading-tight mb-2">Penagihan Workshop</h3>
                    <p class="text-[10px] font-bold uppercase tracking-widest opacity-80">{{ count($workshopPayments) }} Unit Menunggu</p>
                </div>
                <div class="flex-1 p-4 overflow-x-auto max-h-[350px] overflow-y-auto relative">
                    <table class="w-full text-sm text-left">
                        <thead class="text-gray-400 uppercase text-[9px] font-black tracking-widest border-b border-gray-50 sticky top-0 bg-white z-10 shadow-sm">
                            <tr>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Tagihan</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($workshopPayments as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <div class="font-black text-gray-900">{{ $order->spk_number }}</div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase">{{ $order->customer_name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-tighter" style="background-color: #FFC232; color: white">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-black text-gray-900 text-base">Rp {{ number_format($order->total_amount_due, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button @click="openPaymentModal({ id: {{ $order->id }}, spk_number: '{{ $order->spk_number }}', total_amount_due: {{ $order->total_amount_due }} })" 
                                            class="text-white px-4 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg transition transform hover:scale-105" style="background-color: #FFC232">
                                        Bayar
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Utilities Bar --}}
            <div class="bg-white rounded-3xl shadow-sm p-4 mb-6 border border-gray-50 flex flex-wrap items-center justify-between gap-4">
                <form action="{{ route('cs.dashboard') }}" method="GET" class="flex-1 min-w-[300px] relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama/HP Customer..." class="w-full pl-12 pr-4 py-4 bg-gray-50/50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#22AF85] transition-all font-bold">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                </form>
                
                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <a href="{{ route('cs.leads.lost') }}" class="px-5 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-500 hover:bg-red-100 transition-all border border-red-100">🚫 Lost Leads ({{ $metrics['total_lost'] }})</a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'hot']) }}" class="px-5 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest {{ request('filter') == 'hot' ? 'bg-red-500 text-white shadow-lg' : 'bg-gray-50 text-gray-400 hover:bg-gray-100' }} transition-all">🔥 Hot Leads</a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'overdue']) }}" class="px-5 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest {{ request('filter') == 'overdue' ? 'bg-[#FFC232] text-white shadow-lg' : 'bg-gray-50 text-gray-400 hover:bg-gray-100' }} transition-all">⏰ Overdue</a>
                    <a href="{{ route('cs.dashboard') }}" class="px-5 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all">Reset</a>
                </div>
            </div>

            {{-- Kanban Board --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 pb-12">
                
                {{-- Column: GREETING --}}
                <div class="flex flex-col h-[calc(100vh-320px)]">
                    <div class="mb-5 flex items-center justify-between px-2">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-8 rounded-full shadow-sm" style="background-color: #22AF85"></div>
                            <div>
                                <h3 class="font-black text-gray-900 uppercase tracking-tighter text-xl">Greeting</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $greetingLeads->total() }} Candidates</p>
                            </div>
                        </div>
                    </div>
                    <div id="GREETING" class="kanban-column flex-1 bg-gray-100/40 rounded-[2.5rem] p-4 overflow-y-auto space-y-4" style="min-height: 400px;">
                        @foreach($greetingLeads as $lead)
                            @include('cs.dashboard.partials.lead-card', ['lead' => $lead])
                        @endforeach
                    </div>
                    <div class="mt-4 px-2">
                        {{ $greetingLeads->appends(request()->query())->links('vendor.pagination.simple-tailwind') }}
                    </div>
                </div>

                {{-- Column: KONSULTASI --}}
                <div class="flex flex-col h-[calc(100vh-320px)]">
                    <div class="mb-5 flex items-center justify-between px-2">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-8 rounded-full shadow-sm" style="background-color: #FFC232"></div>
                            <div>
                                <h3 class="font-black text-gray-900 uppercase tracking-tighter text-xl">Konsultasi</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $konsultasiLeads->total() }} Active Cases</p>
                            </div>
                        </div>
                    </div>
                    <div id="KONSULTASI" class="kanban-column flex-1 bg-gray-100/40 rounded-[2.5rem] p-4 overflow-y-auto space-y-4" style="min-height: 400px;">
                        @foreach($konsultasiLeads as $lead)
                            @include('cs.dashboard.partials.lead-card', ['lead' => $lead])
                        @endforeach
                    </div>
                    <div class="mt-4 px-2">
                        {{ $konsultasiLeads->appends(request()->query())->links('vendor.pagination.simple-tailwind') }}
                    </div>
                </div>

                {{-- Column: FOLLOW_UP --}}
                <div class="flex flex-col h-[calc(100vh-320px)]">
                    <div class="mb-5 flex items-center justify-between px-2">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-8 rounded-full shadow-sm" style="background-color: #F97316"></div>
                            <div>
                                <h3 class="font-black text-gray-900 uppercase tracking-tighter text-xl">Follow-up</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $followUpLeads->total() }} Warm Leads 🔥</p>
                            </div>
                        </div>
                    </div>
                    <div id="FOLLOW_UP" class="kanban-column flex-1 bg-orange-50/40 rounded-[2.5rem] p-4 overflow-y-auto space-y-4" style="min-height: 400px;">
                        @foreach($followUpLeads as $lead)
                            @include('cs.dashboard.partials.lead-card', ['lead' => $lead])
                        @endforeach
                    </div>
                    <div class="mt-4 px-2">
                        {{ $followUpLeads->appends(request()->query())->links('vendor.pagination.simple-tailwind') }}
                    </div>
                </div>

                {{-- Column: CLOSING --}}
                <div class="flex flex-col h-[calc(100vh-320px)]">
                    <div class="mb-5 flex items-center justify-between px-2">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-8 rounded-full shadow-sm" style="background-color: #22AF85"></div>
                            <div>
                                <h3 class="font-black text-gray-900 uppercase tracking-tighter text-xl">Closing</h3>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $closingLeads->total() }} Conversion Ready</p>
                            </div>
                        </div>
                    </div>
                    <div id="CLOSING" class="kanban-column flex-1 bg-gray-100/40 rounded-[2.5rem] p-4 overflow-y-auto space-y-4" style="min-height: 400px;">
                        @foreach($closingLeads as $lead)
                            @include('cs.dashboard.partials.lead-card', ['lead' => $lead])
                        @endforeach
                    </div>
                    <div class="mt-4 px-2">
                        {{ $closingLeads->appends(request()->query())->links('vendor.pagination.simple-tailwind') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal: New Lead --}}
        <div x-show="leadModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center"
             style="display: none;">
            
            <div @click.outside="leadModalOpen = false" class="relative mx-auto p-0 border w-full max-w-md shadow-2xl rounded-3xl bg-white overflow-hidden m-4">
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 flex justify-between items-center text-white">
                    <h3 class="text-xl font-black uppercase tracking-tight">Lead Baru</h3>
                    <button @click="leadModalOpen = false" class="hover:bg-white/20 p-1 rounded-xl transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('cs.leads.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Customer</label>
                        <input type="text" name="customer_name" class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#22AF85] font-bold" placeholder="Opsional">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">No. Telepon *</label>
                        <input type="text" name="customer_phone" required class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#22AF85] font-bold" placeholder="08xxx">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tipe Lead *</label>
                            <select name="channel" required class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#22AF85] font-bold">
                                <option value="ONLINE">Online (WhatsApp/Social)</option>
                                <option value="OFFLINE">Offline (Walk-in)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Sumber Lead *</label>
                            <select name="source" required class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#22AF85] font-bold">
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="Instagram">Instagram</option>
                                <option value="Website">Website</option>
                                <option value="Referral">Referral</option>
                                <option value="Walk-in">Walk-in</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Prioritas *</label>
                        <select name="priority" required class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#22AF85] font-bold">
                            <option value="WARM">Normal</option>
                            <option value="HOT">HOT 🔥</option>
                            <option value="COLD">Cold</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Catatan Awal</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-[#22AF85] font-bold" placeholder="Catatan awal..."></textarea>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" @click="leadModalOpen = false" class="flex-1 py-4 text-xs font-black uppercase tracking-widest text-gray-400 hover:bg-gray-50 rounded-2xl transition">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-[#22AF85] text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl hover:shadow-2xl transition transform hover:scale-[1.02]">Simpan Lead</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: Workshop Payment Confirmation --}}
        <div x-show="paymentModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full z-[60] flex items-center justify-center"
             style="display: none;">
             
            <div @click.outside="paymentModalOpen = false" class="relative mx-auto p-0 border w-full max-w-md shadow-2xl rounded-3xl bg-white overflow-hidden m-4">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 flex justify-between items-center text-white">
                    <div>
                        <h3 class="text-lg font-black uppercase tracking-tight">Konfirmasi Bayar</h3>
                        <p class="text-[10px] font-bold opacity-80 uppercase tracking-widest" x-text="'Order #' + paymentData.spk_number">Order #---</p>
                    </div>
                    <button @click="paymentModalOpen = false" class="hover:bg-white/20 p-1 rounded-xl transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form :action="paymentData.action" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 space-y-5">
                        <div class="bg-orange-50 rounded-3xl p-6 border border-orange-100 text-center">
                            <p class="text-[10px] text-orange-600 font-black uppercase tracking-widest mb-1">Total Tagihan</p>
                            <p class="text-4xl font-black text-orange-700" x-text="paymentData.formatted_amount">Rp 0</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 text-center">Metode Pembayaran</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach(['Transfer', 'Tunai', 'EDC', 'Lainnya'] as $method)
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="{{ $method }}" required class="peer hidden">
                                    <div class="peer-checked:bg-orange-500 peer-checked:text-white border-none bg-gray-50 rounded-2xl p-4 text-center transition font-black text-[10px] uppercase tracking-widest">
                                        {{ $method }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Bukti Bayar</label>
                            <input type="file" name="proof_image" id="proof_image" required accept="image/*" class="hidden" @change="previewPaymentImage">
                            <label for="proof_image" class="flex flex-col items-center justify-center w-full h-48 bg-gray-50 rounded-3xl cursor-pointer hover:bg-orange-50/50 overflow-hidden transition relative border-2 border-dashed border-gray-100">
                                <template x-if="!paymentProofPreview">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                </template>
                                <template x-if="paymentProofPreview">
                                    <img :src="paymentProofPreview" class="absolute inset-0 w-full h-full object-cover">
                                </template>
                            </label>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 flex gap-3">
                        <button type="button" @click="paymentModalOpen = false" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-orange-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-xl hover:shadow-2xl transition transform hover:scale-[1.02]">
                            Proses Bayar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('csDashboard', () => ({
                leadModalOpen: false,
                paymentModalOpen: false,
                paymentData: {
                    id: null,
                    spk_number: '',
                    amount: 0,
                    formatted_amount: 'Rp 0',
                    action: ''
                },
                paymentProofPreview: null,
                
                init() {
                    const columns = ['GREETING', 'KONSULTASI', 'FOLLOW_UP', 'CLOSING'];
                    columns.forEach(id => {
                        new Sortable(document.getElementById(id), {
                            group: 'kanban',
                            animation: 200,
                            draggable: '.lead-card',
                            ghostClass: 'opacity-50',
                            onEnd: (evt) => {
                                if (evt.from.id !== evt.to.id) {
                                    this.updateLeadStatus(evt.item.getAttribute('data-id'), evt.to.id);
                                }
                            }
                        });
                    });
                },

                openNewLeadModal() {
                    this.leadModalOpen = true;
                },

                openPaymentModal(order) {
                    this.paymentData = {
                        id: order.id,
                        spk_number: order.spk_number,
                        amount: order.total_amount_due,
                        formatted_amount: new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(order.total_amount_due),
                        action: `/cs/workshop-payment/${order.id}`
                    };
                    this.paymentModalOpen = true;
                    this.paymentProofPreview = null;
                    document.getElementById('proof_image').value = ''; // Reset file input
                },

                previewPaymentImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.paymentProofPreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                updateLeadStatus(id, status) {
                    fetch(`/cs/leads/${id}/update-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: status })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            location.reload();
                        }
                    });
                },

                goToDetail(id) {
                    window.location.href = `/cs/leads/${id}`;
                }
            }));
        });
    </script>
</x-app-layout>
