<x-app-layout>
    <div x-data="{ activeTab: 'kanban' }" class="h-screen flex flex-col bg-gray-50 overflow-hidden">
        {{-- Header Toolbar --}}
        <div class="bg-white border-b px-6 py-3 flex justify-between items-center shadow-sm z-10">
            <div class="flex items-center gap-4">
                <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    CS Dashboard
                </h1>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">
                    {{ Auth::user()->name }} ({{ Auth::user()->cs_code ?? 'N/A' }})
                </span>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('newLeadModal').showModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold shadow-md transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Chat Masuk (New Lead)
                </button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="px-6 border-b bg-white flex space-x-6">
            <button @click="activeTab = 'kanban'" 
                    class="py-3 px-1 border-b-2 font-medium text-sm transition-colors"
                    :class="activeTab === 'kanban' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                Active Board
            </button>
            <button @click="activeTab = 'history'" 
                    class="py-3 px-1 border-b-2 font-medium text-sm transition-colors"
                    :class="activeTab === 'history' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                History / Selesai
            </button>
        </div>

        {{-- Kanban Board --}}
        <div x-show="activeTab === 'kanban'" class="flex-1 overflow-x-auto overflow-y-hidden p-6">
            <div class="flex gap-6 h-full min-w-[1200px]">
                
                {{-- Lane: NEW (Chat Masuk) --}}
                <div class="w-80 flex flex-col bg-gray-100 rounded-xl border border-gray-200 shadow-sm max-h-full">
                    <div class="p-3 border-b bg-gray-200/50 rounded-t-xl flex justify-between items-center sticky top-0">
                        <h3 class="font-bold text-gray-700 flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            Chat Masuk
                        </h3>
                        <span class="bg-gray-300 text-gray-700 px-2 py-0.5 rounded text-xs font-bold">{{ $lanes['NEW']->count() }}</span>
                    </div>
                    <div class="flex-1 overflow-y-auto p-3 space-y-3" id="lane-new">
                        @foreach($lanes['NEW'] as $lead)
                            <x-cs-lead-card :lead="$lead" />
                        @endforeach
                    </div>
                </div>

                {{-- Lane: KONSULTASI --}}
                <div class="w-80 flex flex-col bg-blue-50/50 rounded-xl border border-blue-100 shadow-sm max-h-full">
                    <div class="p-3 border-b bg-blue-100/50 rounded-t-xl flex justify-between items-center sticky top-0">
                        <h3 class="font-bold text-blue-800 flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            Konsultasi
                        </h3>
                        <span class="bg-blue-200 text-blue-800 px-2 py-0.5 rounded text-xs font-bold">{{ $lanes['KONSULTASI']->count() }}</span>
                    </div>
                    <div class="flex-1 overflow-y-auto p-3 space-y-3" id="lane-konsultasi">
                        @foreach($lanes['KONSULTASI'] as $lead)
                            <x-cs-lead-card :lead="$lead" />
                        @endforeach
                    </div>
                </div>

                {{-- Lane: INVEST (Greeting & Konsultasi) --}}
                <div class="w-80 flex flex-col bg-amber-50/50 rounded-xl border border-amber-100 shadow-sm max-h-full">
                    <div class="p-3 border-b bg-amber-100/50 rounded-t-xl flex justify-between items-center sticky top-0">
                        <h3 class="font-bold text-amber-800 flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            Invest (Follow Up)
                        </h3>
                        <span class="bg-amber-200 text-amber-800 px-2 py-0.5 rounded text-xs font-bold">{{ $lanes['INVEST_GREETING']->count() + $lanes['INVEST_KONSULTASI']->count() }}</span>
                    </div>
                    <div class="flex-1 overflow-y-auto p-3 space-y-3" id="lane-invest">
                        @foreach($lanes['INVEST_GREETING'] as $lead)
                            <x-cs-lead-card :lead="$lead" :isInvest="true" />
                        @endforeach
                        
                        @foreach($lanes['INVEST_KONSULTASI'] as $lead)
                            <x-cs-lead-card :lead="$lead" :isInvest="true" />
                        @endforeach
                    </div>
                </div>



            </div>
        </div>

        {{-- History Tab --}}
        <div x-show="activeTab === 'history'" class="flex-1 overflow-x-auto p-6" style="display: none;">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="p-4 border-b">
                    <h3 class="font-bold text-gray-700">Riwayat Penjualan (Selesai/SPK Created)</h3>
                </div>
                <table class="min-w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Tanggal Closing</th>
                            <th class="px-6 py-3">Nama Customer</th>
                            <th class="px-6 py-3">No. WhatsApp</th>
                            <th class="px-6 py-3">Alamat</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyLeads as $lead)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $lead->last_updated_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4">{{ $lead->customer_name ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $lead->customer_phone }}</td>
                                <td class="px-6 py-4 truncate max-w-xs" title="{{ $lead->customer_address }}">
                                    {{ Str::limit($lead->customer_address, 40) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">SPK Created</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">
                                    Belum ada data history.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t">
                    {{ $historyLeads->links() }}
                </div>
            </div>
        </div>

    </div>

    {{-- MODAL: New Lead --}}
    <dialog id="newLeadModal" class="modal rounded-xl shadow-2xl p-0 w-96 max-w-full backdrop:bg-black/50">
        <form method="POST" action="{{ route('cs.leads.store') }}" class="flex flex-col h-full">
            @csrf
            <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-white font-bold text-lg">Input Chat Masuk</h3>
                <button type="button" onclick="document.getElementById('newLeadModal').close()" class="text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
                    <input type="text" name="customer_phone" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="08..." required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Customer (Opsional)</label>
                    <input type="text" name="customer_name" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nama Panggilan...">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Catatan Awal</label>
                    <textarea name="notes" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tanya tentang..."></textarea>
                </div>
            </div>

            <div class="p-6 bg-gray-50 flex justify-end gap-3 rounded-b-xl border-t">
                <button type="button" onclick="document.getElementById('newLeadModal').close()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </dialog>

</x-app-layout>
