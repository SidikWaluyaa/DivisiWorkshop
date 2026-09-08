<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <span class="font-bold tracking-wider text-teal-100 uppercase text-xs">Divisi CS</span>
            <span class="text-white/40">/</span>
            <span class="font-black text-white text-base tracking-wide">{{ __('CS Hub & Pipeline') }}</span>
        </div>
    </x-slot>

    <div x-data="csDashboard" @open-new-lead.window="leadModalOpen = true" class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Executive Glassmorphism Hero Header --}}
            <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-white via-white/95 to-emerald-50/40 p-6 sm:p-8 border border-white/80 shadow-[0_20px_50px_rgba(34,175,133,0.08)] mb-8 backdrop-blur-xl">
                {{-- Decorative background glow --}}
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-72 h-72 bg-amber-400/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    {{-- Left info --}}
                    <div class="flex items-start sm:items-center gap-4 sm:gap-5">
                        <div class="relative shrink-0">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-[#1b8c6a] via-[#22AF85] to-[#38d4a5] flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 ring-4 ring-white">
                                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500 border-2 border-white"></span>
                            </span>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider uppercase bg-emerald-100 text-emerald-800 border border-emerald-200/60 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Live Pipeline Hub
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold text-gray-500 bg-gray-100 border border-gray-200/60 inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                                CS Hub & Pipeline
                                <span class="text-xs font-bold text-gray-400 font-mono">v2.0</span>
                            </h1>
                            <p class="text-xs sm:text-sm text-gray-500 font-medium mt-0.5">
                                Monitoring konversi prospek pelanggan dari Greeting hingga Closing SPK secara real-time.
                            </p>
                        </div>
                    </div>

                    {{-- Right actions & Quick Filters --}}
                    <div class="flex flex-wrap items-center gap-3 sm:gap-4 lg:self-center">
                        {{-- Pipeline Quick Filter Pills --}}
                        <div class="inline-flex items-center p-1 bg-gray-100/90 rounded-2xl border border-gray-200/70 text-xs font-bold shadow-inner">
                            <a href="{{ route('cs.leads.konsultasi') }}" class="px-3 py-1.5 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-white transition flex items-center gap-1.5">
                                <span>💬 Konsultasi</span>
                                <span class="px-1.5 py-0.2 text-[10px] rounded-full bg-yellow-100 text-yellow-800 font-black">{{ $metrics['total_konsultasi'] }}</span>
                            </a>
                            <a href="{{ route('cs.leads.follow-up') }}" class="px-3 py-1.5 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-white transition flex items-center gap-1.5">
                                <span>🎯 Follow Up</span>
                                <span class="px-1.5 py-0.2 text-[10px] rounded-full bg-blue-100 text-blue-800 font-black">{{ $metrics['total_follow_up'] }}</span>
                            </a>
                            <a href="{{ route('cs.leads.closing') }}" class="px-3 py-1.5 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-white transition flex items-center gap-1.5">
                                <span>🏆 Closing</span>
                                <span class="px-1.5 py-0.2 text-[10px] rounded-full bg-emerald-100 text-emerald-800 font-black">{{ $metrics['total_closing'] }}</span>
                            </a>
                        </div>

                        {{-- Primary CTA Button --}}
                        <button @click="$dispatch('open-new-lead')" 
                                class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest text-white bg-gradient-to-r from-[#1ea87f] via-[#22AF85] to-[#25c494] shadow-[0_10px_25px_rgba(34,175,133,0.35)] hover:shadow-[0_15px_30px_rgba(34,175,133,0.5)] hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 group cursor-pointer">
                            <div class="w-5 h-5 rounded-lg bg-white/20 flex items-center justify-center group-hover:rotate-90 transition-transform duration-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <span>Tambah Lead Baru</span>
                        </button>
                    </div>
                </div>
            </div>
            
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
                        <div class="text-[10px] text-red-500 font-black uppercase tracking-widest mb-1">Hot Potential 🔥</div>
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

        @include('cs.leads.partials.create-modal')

    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('csDashboard', () => ({
                leadModalOpen: false,
                
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
