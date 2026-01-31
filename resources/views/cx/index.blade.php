<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('CX Issue Resolution Center') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Navigation / Filter --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div class="flex space-x-4">
                    <a href="{{ route('cx.index') }}" class="px-4 py-2 bg-teal-600 text-white rounded-lg shadow font-medium text-sm">
                        ⚠️ Butuh Follow Up ({{ $orders->total() }})
                    </a>
                    <a href="{{ route('cx.cancelled') }}" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 rounded-lg shadow font-medium text-sm">
                        🚫 Kolam Cancel
                    </a>
                    <a href="{{ route('complaints.index') }}" class="px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 rounded-lg shadow font-medium text-sm">
                        📢 Data Komplain
                    </a>
                </div>

                @if(in_array(auth()->user()->role, ['admin', 'owner']))
                    <form action="{{ route('cx.index') }}" method="GET" class="flex items-center gap-2">
                        <select name="handler_id" onchange="this.form.submit()" class="text-xs border-gray-300 rounded-lg focus:ring-teal-500 py-1.5 pr-8">
                            <option value="">Semua Handler CX</option>
                            @php
                                // Compat fix: Use LIKE instead of whereJsonContains for hosting support
                                $cxHandlers = \App\Models\User::where('access_rights', 'LIKE', '%"cx"%')->get();
                            @endphp
                            @foreach($cxHandlers as $h)
                                <option value="{{ $h->id }}" {{ request('handler_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Mobile Card View --}}
                    <div class="block lg:hidden grid grid-cols-1 divide-y divide-gray-100 mb-4">
                        @forelse($orders as $order)
                             @php
                                // Determine Issue Source
                                $openIssue = $order->cxIssues->where('status', 'OPEN')->first();
                                $issueSource = $openIssue ? $openIssue->type : ($order->status == \App\Enums\WorkOrderStatus::HOLD_FOR_CX ? 'RECEPTION_REJECT' : 'UNKNOWN');
                                $reporter = $openIssue ? $openIssue->reporter->name : 'Gudang/Admin';
                                $desc = $openIssue ? $openIssue->description : ($order->reception_rejection_reason ?? 'Tidak ada keterangan');
                                $photos = $openIssue && $openIssue->photos ? $openIssue->photos : [];
                            @endphp
                            <div class="p-4 bg-white hover:bg-gray-50 transition-colors border-b border-gray-100">
                                {{-- Header --}}
                                <div class="flex justify-between items-start mb-2">
                                     <div>
                                         <span class="font-mono bg-amber-50 text-amber-700 px-2 py-1 rounded text-xs font-bold border border-amber-100">
                                            {{ $order->spk_number }}
                                        </span>
                                        <div class="text-[10px] text-gray-500 mt-1">{{ $order->entry_date->format('d M Y') }}</div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <div class="font-bold text-gray-900 text-sm">{{ $order->customer_name }}</div>
                                        <div class="flex items-center gap-1 mt-1">
                                            <span class="text-[10px] text-gray-400">Handler:</span>
                                            <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-1.5 py-0.5 rounded">{{ $order->cxHandler->name ?? 'Unassigned' }}</span>
                                        </div>
                                    </div>
                                </div>
                    
                                {{-- Contact --}}
                                <div class="mb-3">
                                     <a href="https://wa.me/{{ preg_replace('/^0/', '62', $order->customer_phone) }}?text=Halo%20Kak%20{{ $order->customer_name }},%20kami%20dari%20Workshop...%20ada%20kendala%20di%20sepatu%20{{ $order->spk_number }}..." 
                                       target="_blank"
                                       class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-bold hover:bg-green-200">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        Chat WA ({{ $order->customer_phone }})
                                    </a>
                                </div>
                    
                                {{-- Issue Details --}}
                                <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                     <div class="flex items-center gap-2 mb-2">
                                        <span class="text-[10px] uppercase font-bold tracking-wider text-gray-500">Pelapor: {{ $reporter }}</span>
                                        @if($openIssue && $openIssue->category)
                                            <span class="text-[10px] uppercase font-bold tracking-wider text-teal-600 border border-teal-200 px-1 rounded">{{ $openIssue->category }}</span>
                                        @endif
                                    </div>
                                     <div class="bg-red-50 p-2 rounded border border-red-100 text-xs text-gray-800">
                                        "{{ $desc }}"
                                    </div>
                                     @if(count($photos) > 0)
                                        <div class="flex gap-2 mt-2 overflow-x-auto pb-1">
                                            @foreach($photos as $photoUrl)
                                                <a href="{{ asset($photoUrl) }}" target="_blank" class="block w-10 h-10 rounded border hover:opacity-75 flex-shrink-0">
                                                    <img src="{{ asset($photoUrl) }}" class="w-full h-full object-cover rounded">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                    
                                {{-- Actions --}}
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <button onclick="openActionModal('{{ $order->id }}', 'lanjut')" class="w-full text-white bg-teal-600 hover:bg-teal-700 font-bold rounded px-2 py-2 shadow">
                                        ✅ Lanjut
                                    </button>
                                    <button onclick="openActionModal('{{ $order->id }}', 'tambah_jasa')" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-bold rounded px-2 py-2 shadow">
                                        ➕ Tambah Jasa
                                    </button>
                                    <button onclick="openActionModal('{{ $order->id }}', 'komplain')" class="w-full text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-semibold rounded px-2 py-2">
                                        ⚠️ Komplain
                                    </button>
                                    <button onclick="openActionModal('{{ $order->id }}', 'cancel')" class="w-full text-red-600 bg-white border border-red-200 hover:bg-red-50 font-semibold rounded px-2 py-2">
                                        ❌ Cancel
                                    </button>
                                </div>
                            </div>
                        @empty
                             <div class="text-center p-6 text-gray-500 italic text-sm">Tidak ada issue yang perlu penanganan.</div>
                        @endforelse
                    </div>

                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3">Order Info</th>
                                    <th class="px-6 py-3">Customer</th>
                                    <th class="px-6 py-3 w-1/3">Detail Kendala (Issue)</th>
                                    <th class="px-6 py-3 text-center">Resolusi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($orders as $order)
                                    @php
                                        // Determine Issue Source
                                        $openIssue = $order->cxIssues->where('status', 'OPEN')->first();
                                        $issueSource = $openIssue ? $openIssue->type : ($order->status == \App\Enums\WorkOrderStatus::HOLD_FOR_CX ? 'RECEPTION_REJECT' : 'UNKNOWN');
                                        $reporter = $openIssue ? $openIssue->reporter->name : 'Gudang/Admin';
                                        $desc = $openIssue ? $openIssue->description : ($order->reception_rejection_reason ?? 'Tidak ada keterangan');
                                        $photos = $openIssue && $openIssue->photos ? $openIssue->photos : [];
                                    @endphp
                                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 align-top">
                                            <div class="font-bold text-gray-900">{{ $order->entry_date->format('d M Y') }}</div>
                                            <div class="font-mono bg-amber-50 text-amber-700 px-2 py-1 rounded inline-block mt-1 text-xs font-bold border border-amber-100">
                                                {{ $order->spk_number }}
                                            </div>
                                            <div class="mt-2 text-xs">
                                                <span class="px-2 py-0.5 rounded-full bg-gray-200 text-gray-600 font-semibold">
                                                    {{ $order->previous_status ? 'Pre: ' . str_replace('_', ' ', ($order->previous_status instanceof \App\Enums\WorkOrderStatus ? $order->previous_status->value : $order->previous_status)) : 'QC Reject' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <div class="font-bold text-gray-800">{{ $order->customer_name }}</div>
                                            <div class="text-xs text-gray-500 mb-1">{{ $order->customer_phone }}</div>
                                            <div class="text-xs font-medium text-gray-700">
                                                {{ $order->shoe_brand }} {{ $order->shoe_color }}
                                            </div>

                                            <div class="flex items-center gap-1.5 mt-2 bg-gray-50 p-1.5 rounded-lg border border-gray-100 w-fit">
                                                <div class="w-5 h-5 rounded-full bg-teal-100 flex items-center justify-center text-[8px] text-teal-600 font-bold border border-teal-200">
                                                    {{ $order->cxHandler ? substr($order->cxHandler->name, 0, 1) : '?' }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-[9px] text-gray-400 leading-none">Handler CX</span>
                                                    <span class="text-[10px] font-bold text-gray-700">{{ $order->cxHandler->name ?? 'Unassigned' }}</span>
                                                </div>
                                            </div>
                                            
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $order->customer_phone) }}?text=Halo%20Kak%20{{ $order->customer_name }},%20kami%20dari%20Workshop...%20ada%20kendala%20di%20sepatu%20{{ $order->spk_number }}..." 
                                               target="_blank"
                                               class="inline-flex items-center gap-1 text-[10px] bg-green-100 text-green-700 px-2 py-1 rounded mt-2 hover:bg-green-200 font-bold">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                Chat WA
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <div class="flex items-start gap-2">
                                                <div class="flex-1">
                                                    <span class="text-[10px] uppercase font-bold tracking-wider text-gray-500">Pelapor: {{ $reporter }}</span>
                                                    @if($openIssue && $openIssue->category)
                                                        <span class="ml-2 text-[10px] uppercase font-bold tracking-wider text-teal-600 border border-teal-200 px-1 rounded">{{ $openIssue->category }}</span>
                                                    @endif
                                                    
                                                    <div class="bg-red-50 p-3 rounded-lg border border-red-100 mt-1 text-sm text-gray-800">
                                                        "{{ $desc }}"
                                                    </div>

                                                    {{-- Photos --}}
                                                    @if(count($photos) > 0)
                                                        <div class="flex gap-2 mt-2">
                                                            @foreach($photos as $photoUrl)
                                                                <a href="{{ asset($photoUrl) }}" target="_blank" class="block w-12 h-12 rounded border hover:opacity-75">
                                                                    <img src="{{ asset($photoUrl) }}" class="w-full h-full object-cover rounded">
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-middle">
                                            <div class="grid grid-cols-1 gap-2">
                                                <button onclick="openActionModal('{{ $order->id }}', 'lanjut')" class="w-full text-white bg-teal-600 hover:bg-teal-700 font-bold rounded text-xs px-3 py-2 shadow transition-all">
                                                    ✅ Lanjut (Resume)
                                                </button>
                                                <button onclick="openActionModal('{{ $order->id }}', 'tambah_jasa')" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-bold rounded text-xs px-3 py-2 shadow transition-all">
                                                    ➕ Tambah Jasa
                                                </button>
                                                <button onclick="openActionModal('{{ $order->id }}', 'komplain')" class="w-full text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-semibold rounded text-xs px-3 py-2 transition-all">
                                                    ⚠️ Komplain
                                                </button>
                                                <button onclick="openActionModal('{{ $order->id }}', 'cancel')" class="w-full text-red-600 bg-white border border-red-200 hover:bg-red-50 font-semibold rounded text-xs px-3 py-2 transition-all">
                                                    ❌ Cancel Order
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="p-4 bg-green-50 rounded-full mb-3">
                                                    <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <p class="text-base font-medium">Bagus! Tidak ada issue yang perlu penanganan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4 px-4 pb-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Universal Action Modal --}}
    <div id="actionModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 transition-opacity">
        <div class="relative top-20 mx-auto p-0 border w-full max-w-md shadow-2xl rounded-xl bg-white transform transition-all">
            <div class="bg-gray-50 px-6 py-4 rounded-t-xl border-b flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Konfirmasi Aksi</h3>
                <button onclick="closeActionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="actionForm" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="action" id="modalActionInput">
                <input type="hidden" name="issue_id" id="modalIssueInput">
                
                <div class="mb-4">
                    <div id="modalDescription" class="text-sm text-gray-600 mb-4 bg-blue-50 p-3 rounded-lg border border-blue-100 flex gap-2">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span id="modalDescText">Deskripsi aksi akan muncul disini.</span>
                    </div>

                    {{-- Add Service Inputs (Hidden by default) --}}
                    <div id="addServiceInputs" class="hidden space-y-5 mb-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                         
                         {{-- Custom Toggle --}}
                         <div class="flex items-center gap-2 mb-2">
                            <input type="checkbox" id="isCustomToggle" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                            <label for="isCustomToggle" class="text-xs font-bold text-gray-700">Input Nama & Harga Manual (Custom)</label>
                         </div>

                         <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Layanan Basis</label>
                            <select name="service_id" id="serviceSelect" class="w-full text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 shadow-sm">
                                <option value="">-- Pilih Jasa --</option>
                                @foreach($services->groupBy('category') as $category => $items)
                                    <optgroup label="{{ $category }}">
                                        @foreach($items as $service)
                                            <option value="{{ $service->id }}" data-name="{{ $service->name }}" data-price="{{ $service->price }}">
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-500 mt-1">*Pilih salah satu layanan sebagai kategori dasar.</p>
                        </div>

                        <div id="customServiceInput" class="hidden">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Layanan Custom</label>
                            <input type="text" name="custom_name" id="customNameField" class="w-full text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" placeholder="Contoh: Repaint + Gliter Custom">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Harga (Rp)</label>
                            <input type="number" name="cost" id="serviceCost" class="w-full text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 bg-gray-100" placeholder="0" readonly>
                        </div>
                    </div>

                    <label class="block text-sm font-bold text-gray-700 mb-2">Detail Jasa / Instruksi Pengerjaan (Wajib)</label>
                    <textarea name="notes" required rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500" placeholder="Jelaskan detail request customer, warna, bagian yang dikerjakan, dll..."></textarea>
                </div>

                <div class="flex gap-3 justify-end mt-6">
                    <button type="button" onclick="closeActionModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">Batal</button>
                    <button type="submit" id="modalSubmitBtn" class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-bold hover:bg-teal-700 shadow flex items-center gap-2">
                        <span>Konfirmasi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
    function openActionModal(orderId, action) {
        const form = document.getElementById('actionForm');
        const title = document.getElementById('modalTitle');
        const desc = document.getElementById('modalDescText');
        const actionInput = document.getElementById('modalActionInput');
        const btn = document.getElementById('modalSubmitBtn');
        const serviceInputs = document.getElementById('addServiceInputs');
        
        // Reset Inputs
        document.getElementById('serviceSelect').value = '';
        document.getElementById('serviceCost').value = '';
        document.getElementById('customNameField').value = '';
        
        // Reset Custom Toggle
        const toggle = document.getElementById('isCustomToggle');
        toggle.checked = false;
        toggle.dispatchEvent(new Event('change')); // Trigger logic reset

        serviceInputs.classList.add('hidden');

        form.action = '/cx/' + orderId + '/process';
        actionInput.value = action;
        document.getElementById('actionModal').classList.remove('hidden');

        // Dynamic Content
        switch(action) {
            case 'lanjut':
                title.textContent = 'Lanjutkan Order (Resume)';
                desc.textContent = 'Order akan dilanjutkan ke tahap Assessment (Teknisi). Pastikan customer sudah setuju dengan kondisi tersebut.';
                btn.className = "px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-bold hover:bg-teal-700 shadow flex items-center gap-2";
                break;
            case 'tambah_jasa':
                title.textContent = 'Tambah Jasa (CX Input)';
                desc.textContent = 'Silakan input layanan tambahan yang disepakati customer. Order akan diteruskan ke Sortir untuk cek material.';
                btn.className = "px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow flex items-center gap-2";
                serviceInputs.classList.remove('hidden');
                break;
            case 'komplain':
                title.textContent = 'Buat Komplain (Complaint)';
                desc.textContent = 'Order akan ditransfer ke modul Komplain untuk penanganan lebih lanjut.';
                btn.className = "px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-bold hover:bg-amber-600 shadow flex items-center gap-2";
                break;
            case 'cancel':
                title.textContent = 'Batalkan Order (Cancel)';
                desc.textContent = 'PERINGATAN: Order akan dibatalkan permanen dan masuk ke Kolam Cancel.';
                btn.className = "px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 shadow flex items-center gap-2";
                break;
        }
    }

    function closeActionModal() {
        document.getElementById('actionModal').classList.add('hidden');
    }


    // Service Select Logic & Custom Toggle
    const serviceSelect = document.getElementById('serviceSelect');
    const customToggle = document.getElementById('isCustomToggle');
    const customInputDiv = document.getElementById('customServiceInput');
    const customNameField = document.getElementById('customNameField');
    const costInput = document.getElementById('serviceCost');

    function updateServiceFields() {
        const selected = serviceSelect.options[serviceSelect.selectedIndex];
        const isCustom = customToggle.checked;
        const name = selected.dataset.name || '';
        const price = selected.dataset.price || '';

        if (isCustom) {
            // Custom Mode: Enable edits, Show Name Field
            customInputDiv.classList.remove('hidden');
            costInput.readOnly = false;
            costInput.classList.remove('bg-gray-100');
            
            // Auto-fill if empty
            if (!customNameField.value && name) {
                customNameField.value = name;
            }
            if (!costInput.value && price) {
                costInput.value = price;
            }
        } else {
            // Standard Mode: Read-only, Hide Name Field
            customInputDiv.classList.add('hidden');
            costInput.readOnly = true;
            costInput.classList.add('bg-gray-100');
            
            // Force strict values from select
            costInput.value = price;
            customNameField.value = ''; // Clear custom name to prevent sending it
        }
    }

    serviceSelect.addEventListener('change', updateServiceFields);
    customToggle.addEventListener('change', updateServiceFields);
    </script>
</x-app-layout>
