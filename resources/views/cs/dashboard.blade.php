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
