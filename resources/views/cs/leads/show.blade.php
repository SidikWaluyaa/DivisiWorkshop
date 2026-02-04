<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-lg" style="background-color: #22AF85">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-gray-900 leading-tight tracking-tight uppercase">
                        {{ $lead->customer_name ?? 'Guest' }}
                    </h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest leading-none {{ $lead->status_badge_class }}">
                            {{ $lead->status }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest leading-none border-2 {{ $lead->priority_badge_class }}">
                            {{ $lead->priority }}
                        </span>
                    </div>
                </div>
            </div>
            <a href="{{ route('cs.dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-500 px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition shadow-sm">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Left Column: Customer Info & Actions --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Customer Information --}}
                    <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
                        <div class="p-6 pb-0">
                            <h3 class="font-black text-gray-900 uppercase tracking-tighter text-xl">Profil Customer</h3>
                            <div class="w-12 h-1.5 bg-[#22AF85] rounded-full mt-2"></div>
                        </div>
                        <div class="p-4 space-y-3">
                            <div>
                                <label class="text-xs text-gray-500 font-semibold">Nama</label>
                                <p class="text-gray-900">{{ $lead->customer_name ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 font-semibold">Telepon</label>
                                <div class="flex items-center gap-2">
                                    <p class="text-gray-900">{{ $lead->customer_phone }}</p>
                                    <a href="{{ $lead->wa_greeting_link }}" target="_blank" class="text-green-600 hover:text-green-700 font-bold text-xs flex items-center gap-1 border border-green-200 bg-green-50 px-2 py-0.5 rounded">
                                        WhatsApp
                                    </a>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 font-semibold">Email</label>
                                <p class="text-gray-900">{{ $lead->customer_email ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 font-semibold">Alamat</label>
                                <p class="text-gray-900 text-sm">{{ $lead->customer_address ?? '-' }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-xs text-gray-500 font-semibold">Kota</label>
                                    <p class="text-gray-900 text-sm">{{ $lead->customer_city ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 font-semibold">Provinsi</label>
                                    <p class="text-gray-900 text-sm">{{ $lead->customer_province ?? '-' }}</p>
                                </div>
                            </div>
                            <hr>
                            <div>
                                <label class="text-xs text-gray-500 font-semibold">Sumber</label>
                                <p class="text-gray-900">📱 {{ $lead->source }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 font-semibold">Tipe Lead</label>
                                <p class="text-gray-900">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $lead->channel === 'ONLINE' ? 'bg-indigo-100 text-indigo-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ $lead->channel }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 font-semibold">CS Handler</label>
                                <p class="text-gray-900">{{ $lead->cs->name ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 font-semibold">First Contact</label>
                                <p class="text-gray-900 text-sm">{{ $lead->first_contact_at?->format('d M Y H:i') ?? '-' }}</p>
                            </div>
                            @if($lead->response_time_minutes)
                                <div>
                                    <label class="text-xs text-gray-500 font-semibold">Response Time</label>
                                    <p class="text-gray-900">⏱️ {{ $lead->response_time_formatted }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
                        <div class="p-6 pb-0">
                            <h3 class="font-black text-gray-900 uppercase tracking-tighter text-xl">Quick Actions</h3>
                            <div class="w-12 h-1.5 bg-[#FFC232] rounded-full mt-2"></div>
                        </div>
                        <div class="p-4 space-y-2">
                            @if($lead->status === 'GREETING')
                                <button onclick="moveToKonsultasi()" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg font-semibold">
                                    → Pindah ke Konsultasi
                                </button>
                            @endif

                            @if($lead->status === 'KONSULTASI')
                                <button onclick="openQuotationModal()" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg font-semibold">
                                    ➕ Buat Quotation
                                </button>
                                @if($lead->canMoveToClosing())
                                    <button onclick="moveToClosing()" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg font-semibold">
                                        → Pindah ke Closing
                                    </button>
                                @endif
                            @endif

                            @if($lead->status === 'CLOSING')
                                @if(!$lead->spk)
                                    <button onclick="openSpkModal()" class="w-full bg-purple-500 hover:bg-purple-600 text-white py-2 rounded-lg font-semibold">
                                        📄 Generate SPK
                                    </button>
                                @elseif($lead->spk->canBeHandedToWorkshop())
                                    <button onclick="openHandoverModal()" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg font-semibold">
                                        ✅ Serahkan ke Workshop
                                    </button>
                                @endif
                            @endif

                            <button onclick="openActivityModal()" class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg font-semibold">
                                📝 Log Aktivitas
                            </button>

                            <button onclick="openFollowUpModal()" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg font-semibold">
                                ⏰ Set Follow Up
                            </button>

                            <button onclick="markLost()" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg font-semibold">
                                ❌ Mark as LOST
                            </button>
                        </div>
                    </div>

                </div>

                {{-- Right Column: Timeline & Details --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Quotations Section --}}
                    @if(in_array($lead->status, ['KONSULTASI', 'CLOSING', 'CONVERTED']))
                        <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
                            <div class="p-6 pb-0 flex justify-between items-center">
                                <div>
                                    <h3 class="font-black text-gray-900 uppercase tracking-tighter text-xl">Quotations History</h3>
                                    <div class="w-12 h-1.5 bg-[#FFC232] rounded-full mt-2"></div>
                                </div>
                            </div>
                            <div class="p-4">
                                @forelse($lead->quotations as $quotation)
                                    <div class="border rounded-lg p-4 mb-3 {{ $quotation->status === 'ACCEPTED' ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h4 class="font-bold text-gray-900">{{ $quotation->quotation_number }}</h4>
                                                <p class="text-xs text-gray-500">Version {{ $quotation->version }} • {{ $quotation->created_at->format('d M Y') }}</p>
                                            </div>
                                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $quotation->status_badge_class }}">
                                                {{ $quotation->status }}
                                            </span>
                                        </div>
                                        
                                        
                                        {{-- Items --}}
                                        <div class="bg-gray-50 rounded p-3 mb-2">
                                            <p class="text-xs font-semibold text-gray-600 mb-2">📦 Data Barang ({{ count($quotation->quotationItems ?? []) }} item)</p>
                                            <div class="space-y-2">
                                                @foreach($quotation->quotationItems as $item)
                                                    <div class="bg-white rounded p-2 border border-gray-200">
                                                        <div class="flex items-start gap-2">
                                                            <span class="text-lg">{{ $item->category_icon }}</span>
                                                            <div class="flex-1">
                                                                <p class="font-semibold text-sm text-gray-800">Item #{{ $item->item_number }}: {{ $item->label }}</p>
                                                                <div class="text-xs text-gray-600 mt-1 space-y-0.5">
                                                                    @if($item->category)
                                                                        <p><span class="font-semibold">Kategori:</span> {{ $item->category }}</p>
                                                                    @endif
                                                                    @if($item->shoe_color)
                                                                        <p><span class="font-semibold">Warna:</span> {{ $item->shoe_color }}</p>
                                                                    @endif
                                                                    @if($item->condition_notes)
                                                                        <p><span class="font-semibold">Kondisi:</span> {{ $item->condition_notes }}</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Notes --}}
                                        @if($quotation->notes)
                                            <div class="text-sm bg-yellow-50 border border-yellow-200 rounded p-2">
                                                <p class="text-xs font-semibold text-yellow-800">Catatan:</p>
                                                <p class="text-xs text-yellow-700">{{ $quotation->notes }}</p>
                                            </div>
                                        @endif


                                        {{-- Actions --}}
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            @if($quotation->status === 'ACCEPTED')
                                                <button onclick="rejectQuotation({{ $quotation->id }})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider transition">
                                                    ❌ Batalkan / Tolak
                                                </button>
                                            @endif

                                            @if($quotation->status === 'SENT')
                                                <button onclick="rejectQuotation({{ $quotation->id }})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider transition">
                                                    ❌ Tolak
                                                </button>
                                            @endif

                                            <a href="{{ route('cs.quotations.export-pdf', $quotation->id) }}" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider transition border border-gray-200 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                Download PDF
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">Belum ada quotation</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    {{-- SPK Section --}}
                    @if($lead->spk)
                        <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
                            <div class="p-6 pb-0">
                                <h3 class="font-black text-gray-900 uppercase tracking-tighter text-xl">Service Production Key (SPK)</h3>
                                <div class="w-12 h-1.5 bg-[#22AF85] rounded-full mt-2"></div>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="text-xs text-gray-500 font-semibold">SPK Number</label>
                                        <p class="text-lg font-bold text-gray-900">{{ $lead->spk->spk_number }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 font-semibold">Status</label>
                                        <p><span class="px-2 py-1 rounded text-xs font-semibold {{ $lead->spk->status_badge_class }}">{{ $lead->spk->label }}</span></p>
                                    </div>
                                </div>

                                <div class="bg-gray-50 rounded p-3 mb-3">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-gray-600">Total Price:</span>
                                        <span class="font-bold">Rp {{ number_format($lead->spk->total_price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between mb-2">
                                        <span class="text-gray-600">DP Amount:</span>
                                        <span class="font-semibold text-yellow-600">Rp {{ number_format($lead->spk->dp_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Remaining:</span>
                                        <span class="font-semibold text-red-600">Rp {{ number_format($lead->spk->remaining_payment, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="text-xs text-gray-500 font-semibold">DP Status</label>
                                    <p><span class="px-2 py-1 rounded text-xs font-semibold {{ $lead->spk->dp_status_badge_class }}">{{ $lead->spk->dp_status }}</span></p>
                                </div>
                                @if($lead->spk->status === 'WAITING_DP')
                                    <button onclick="openDpModal()" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg font-semibold mb-2">
                                        💰 Konfirmasi DP Dibayar
                                    </button>
                                @elseif($lead->spk->status === 'WAITING_VERIFICATION')
                                    <div class="w-full bg-yellow-100 text-yellow-700 py-2 px-3 rounded-lg font-bold text-center mb-2 border-2 border-yellow-200">
                                        ⏳ Menunggu Verifikasi Finance
                                    </div>
                                @elseif($lead->spk->status === 'HANDED_TO_WORKSHOP')
                                    <div class="w-full bg-green-100 text-green-700 py-2 px-3 rounded-lg font-bold text-center mb-2 border-2 border-green-200">
                                        ✅ Sudah Diserahkan ke Workshop
                                    </div>
                                @elseif($lead->spk->canBeHandedToWorkshop())
                                    <button onclick="openHandoverModal()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold mb-2 flex items-center justify-center gap-2">
                                        🚚 Serahkan ke Workshop
                                    </button>
                                @endif

                                {{-- PDF Download --}}
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <a href="{{ route('cs.spk.export-pdf', $lead->spk->id) }}" target="_blank" class="flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg text-sm font-semibold transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Download PDF SPK (Customer Copy)
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Activity Timeline --}}
                    <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
                        <div class="p-6 pb-0">
                            <h3 class="font-black text-gray-900 uppercase tracking-tighter text-xl">Customer Journey</h3>
                            <div class="w-12 h-1.5 bg-gray-200 rounded-full mt-2"></div>
                        </div>
                        <div class="p-4 max-h-96 overflow-y-auto">
                            @forelse($lead->activities as $activity)
                                <div class="flex gap-3 mb-4 pb-4 border-b border-gray-200 last:border-0">
                                    <div class="text-2xl">{{ $activity->type_icon }}</div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="font-semibold text-gray-900">{{ $activity->user->name ?? 'System' }}</span>
                                            <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700">{!! $activity->formatted_content !!}</p>
                                        @if($activity->channel)
                                            <span class="text-xs text-gray-500">via {{ $activity->channel }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">Belum ada aktivitas</p>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- Modal: Log Activity --}}
    <div id="activityModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-xl bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Log Aktivitas</h3>
                <button onclick="closeActivityModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('cs.activities.store', $lead->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe *</label>
                        <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="CHAT">💬 Chat</option>
                            <option value="CALL">📞 Telepon</option>
                            <option value="EMAIL">📧 Email</option>
                            <option value="MEETING">🤝 Meeting</option>
                            <option value="NOTE">📝 Catatan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Channel</label>
                        <input type="text" name="channel" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="WhatsApp, Instagram, dll">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Isi Komunikasi *</label>
                        <textarea name="content" required rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Detail komunikasi..."></textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="closeActivityModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Create Quotation (Multi-Item Data Barang) --}}
    <div id="quotationModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-4xl shadow-2xl rounded-xl bg-white mb-10" 
             x-data="quotationManager()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Buat Quotation Baru</h3>
                <button onclick="closeQuotationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('cs.quotations.store', $lead->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-blue-800 font-semibold">📦 Input Data Barang</p>
                        <p class="text-xs text-blue-600">Anda bisa menambahkan beberapa barang sekaligus. Layanan akan dipilih saat Generate SPK.</p>
                    </div>

                    {{-- Items Container --}}
                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="border-2 border-gray-200 rounded-lg p-4 bg-white relative">
                                {{-- Item Header --}}
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-bold text-gray-800" x-text="'📦 Item #' + (index + 1)"></h4>
                                    <button type="button" @click="removeItem(index)" 
                                            x-show="items.length > 1"
                                            class="text-red-500 hover:text-red-700 font-semibold text-sm">
                                        🗑️ Hapus
                                    </button>
                                </div>

                                {{-- Item Data Form --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    {{-- Category --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori Barang *</label>
                                        <div class="flex gap-2">
                                            <select x-model="item.categoryOpt" @change="item.category = item.categoryOpt === 'Lainnya' ? '' : item.categoryOpt" 
                                                    class="w-1/2 px-2 py-2 border rounded-lg text-sm bg-white">
                                                <option value="">Pilih...</option>
                                                <option value="Sepatu">Sepatu</option>
                                                <option value="Tas">Tas</option>
                                                <option value="Dompet">Dompet</option>
                                                <option value="Topi">Topi</option>
                                                <option value="Lainnya">Lainnya...</option>
                                            </select>
                                            <input type="text" :name="'items[' + index + '][category]'" x-model="item.category" required
                                                   :readonly="item.categoryOpt !== 'Lainnya'" 
                                                   :class="item.categoryOpt !== 'Lainnya' ? 'bg-gray-100' : 'bg-white'"
                                                   placeholder="Input manual..." class="w-1/2 px-2 py-2 border rounded-lg text-sm">
                                        </div>
                                    </div>

                                    {{-- Type --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jenis Barang</label>
                                        <div class="flex gap-2">
                                            <select x-model="item.typeOpt" @change="item.shoe_type = item.typeOpt === 'Lainnya' ? '' : item.typeOpt"
                                                    class="w-1/2 px-2 py-2 border rounded-lg text-sm bg-white">
                                                <option value="">Pilih...</option>
                                                <option value="Casual">Casual</option>
                                                <option value="Sneakers">Sneakers</option>
                                                <option value="Outdoor">Outdoor</option>
                                                <option value="Sport">Sport</option>
                                                <option value="Lainnya">Lainnya...</option>
                                            </select>
                                            <input type="text" :name="'items[' + index + '][shoe_type]'" x-model="item.shoe_type"
                                                   :readonly="item.typeOpt !== 'Lainnya'"
                                                   :class="item.typeOpt !== 'Lainnya' ? 'bg-gray-100' : 'bg-white'"
                                                   placeholder="Input manual..." class="w-1/2 px-2 py-2 border rounded-lg text-sm">
                                        </div>
                                    </div>

                                    {{-- Brand --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Brand</label>
                                        <div class="flex gap-2">
                                            <select x-model="item.brandOpt" @change="item.shoe_brand = item.brandOpt === 'Lainnya' ? '' : item.brandOpt"
                                                    class="w-1/2 px-2 py-2 border rounded-lg text-sm bg-white">
                                                <option value="">Pilih...</option>
                                                <option value="Nike">Nike</option>
                                                <option value="Adidas">Adidas</option>
                                                <option value="Puma">Puma</option>
                                                <option value="New Balance">New Balance</option>
                                                <option value="Lainnya">Lainnya...</option>
                                            </select>
                                            <input type="text" :name="'items[' + index + '][shoe_brand]'" x-model="item.shoe_brand"
                                                   :readonly="item.brandOpt !== 'Lainnya'"
                                                   :class="item.brandOpt !== 'Lainnya' ? 'bg-gray-100' : 'bg-white'"
                                                   placeholder="Input manual..." class="w-1/2 px-2 py-2 border rounded-lg text-sm">
                                        </div>
                                    </div>

                                    {{-- Size --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Ukuran</label>
                                        <div class="flex gap-2">
                                            <select x-model="item.sizeOpt" @change="item.shoe_size = item.sizeOpt === 'Lainnya' ? '' : item.sizeOpt"
                                                    class="w-1/2 px-2 py-2 border rounded-lg text-sm bg-white">
                                                <option value="">Pilih...</option>
                                                <option value="40">40</option>
                                                <option value="41">41</option>
                                                <option value="42">42</option>
                                                <option value="43">43</option>
                                                <option value="Lainnya">Lainnya...</option>
                                            </select>
                                            <input type="text" :name="'items[' + index + '][shoe_size]'" x-model="item.shoe_size"
                                                   :readonly="item.sizeOpt !== 'Lainnya'"
                                                   :class="item.sizeOpt !== 'Lainnya' ? 'bg-gray-100' : 'bg-white'"
                                                   placeholder="Input manual..." class="w-1/2 px-2 py-2 border rounded-lg text-sm">
                                        </div>
                                    </div>

                                    {{-- Color --}}
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Warna</label>
                                        <input type="text" :name="'items[' + index + '][shoe_color]'" x-model="item.shoe_color"
                                               placeholder="Contoh: Merah, Hitam Putih..." class="w-full px-2 py-2 border rounded-lg text-sm">
                                    </div>

                                    {{-- Condition Notes --}}
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kondisi / Catatan Item</label>
                                        <textarea :name="'items[' + index + '][condition_notes]'" x-model="item.condition_notes" rows="2"
                                                  placeholder="Contoh: Kotor, sol lepas, warna pudar..." class="w-full px-2 py-2 border rounded-lg text-sm"></textarea>
                                    </div>

                                    {{-- Item Notes (Keterangan Besar SPK) --}}
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                                            📝 Catatan Khusus Item (Akan muncul di Keterangan SPK)
                                        </label>
                                        <textarea :name="'items[' + index + '][item_notes]'" x-model="item.item_notes" rows="2"
                                                  placeholder="Contoh: Midsole retak, perlu re-glue khusus..." class="w-full px-2 py-2 border border-blue-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Add Item Button --}}
                    <button type="button" @click="addItem()" 
                            class="w-full py-2 border-2 border-dashed border-blue-300 rounded-lg text-blue-600 hover:bg-blue-50 font-semibold">
                        ➕ Tambah Item Lain
                    </button>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Umum</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Catatan untuk quotation ini..."></textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="closeQuotationModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                        Buat Quotation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function quotationManager() {
            return {
                items: [{
                    categoryOpt: '',
                    category: '',
                    typeOpt: '',
                    shoe_type: '',
                    brandOpt: '',
                    shoe_brand: '',
                    sizeOpt: '',
                    shoe_size: '',
                    shoe_color: '',
                    condition_notes: '',
                    item_notes: ''
                }],
                addItem() {
                    this.items.push({
                        categoryOpt: '',
                        category: '',
                        typeOpt: '',
                        shoe_type: '',
                        brandOpt: '',
                        shoe_brand: '',
                        sizeOpt: '',
                        shoe_size: '',
                        shoe_color: '',
                        condition_notes: '',
                        item_notes: ''
                    });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                }
            }
        }
    </script>

    {{-- Modal: Generate SPK --}}
    <div id="spkModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-3xl shadow-2xl rounded-xl bg-white mb-10">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Generate SPK</h3>
                <button onclick="closeSpkModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('cs.spk.generate', $lead->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-indigo-700 mb-1">Preview No. SPK</label>
                                <div class="bg-white border-2 border-indigo-200 rounded-lg p-3 text-2xl font-mono font-black text-indigo-900 flex justify-between items-center shadow-inner">
                                    <span id="spkPreview">F-{{ date('ym-d') }}-XXXX-{{ strtoupper($lead->cs->cs_code ?? 'SW') }}</span>
                                    <span class="text-xs font-bold text-indigo-400 uppercase tracking-widest">Auto Generated</span>
                                </div>
                                <input type="hidden" name="spk_number" id="finalSpkNumber">
                                <p class="text-[10px] text-indigo-500 mt-2">Format: [Kode]-[YYMM]-[DD]-[Sequence]-[CS]</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 col-span-2">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Metode Pengiriman *</label>
                                        <select name="delivery_type" id="deliveryTypeSelect" required class="w-full px-3 py-2 border-2 border-yellow-200 rounded-lg text-sm bg-yellow-50 font-bold" onchange="updateSpkPreview()">
                                            <option value="Offline" data-code="F">Dateng Offline (F)</option>
                                            <option value="Online" data-code="N">Online / Ekspedisi (N)</option>
                                            <option value="Pickup" data-code="P">Pickup Kurir (P)</option>
                                            <option value="Ojol" data-code="O">Ojek Online (O)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Kode CS (Manual) *</label>
                                        <input type="text" name="manual_cs_code" id="manualCsInput" required maxlength="5" value="{{ $lead->cs->cs_code ?? '' }}" placeholder="Contoh: QA" class="w-full px-3 py-2 border-2 border-indigo-100 rounded-lg text-sm font-bold uppercase" oninput="updateSpkPreview()">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Prioritas *</label>
                                        <select name="priority" required class="w-full px-3 py-2 border rounded-lg text-sm">
                                            <option value="Reguler">Reguler</option>
                                            <option value="Prioritas">Prioritas</option>
                                            <option value="Urgent">Urgent</option>
                                            <option value="Express">Express</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Expected Delivery</label>
                                        <input type="date" name="expected_delivery_date" class="w-full px-3 py-2 border rounded-lg text-sm" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Data --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-bold text-gray-800 mb-3">Data Customer</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama *</label>
                                <input type="text" name="customer_name" value="{{ $lead->customer_name }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Telepon *</label>
                                <input type="text" name="customer_phone" value="{{ $lead->customer_phone }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                                <input type="email" name="customer_email" value="{{ $lead->customer_email }}" class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat *</label>
                                <textarea name="customer_address" required rows="2" class="w-full px-3 py-2 border rounded-lg text-sm">{{ $lead->customer_address }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Kota *</label>
                                <input type="text" name="customer_city" value="{{ $lead->customer_city }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Provinsi *</label>
                                <input type="text" name="customer_province" value="{{ $lead->customer_province }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Special Instructions --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <label class="block text-sm font-bold text-gray-800 mb-2">Instruksi Khusus (Opsional)</label>
                        <textarea name="special_instructions" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Catatan tambahan untuk tim produksi/workshop..."></textarea>
                    </div>

                    {{-- Multi-Item Service Selection --}}
                    @php
                        $acceptedQuotation = $lead->getAcceptedQuotation();
                    @endphp
                    @if($acceptedQuotation && count($acceptedQuotation->quotationItems ?? []) > 0)
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 border-2 border-purple-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-bold text-gray-800">📦 Pilih Layanan per Item ({{ count($acceptedQuotation->quotationItems ?? []) }} items)</h4>
                                <span class="text-xs bg-purple-600 text-white px-2 py-1 rounded-full font-semibold">Quotation #{{ $acceptedQuotation->quotation_number }}</span>
                            </div>

                            {{-- Items Loop --}}
                            <div class="space-y-3">
                                @foreach($acceptedQuotation->quotationItems as $quotationItem)
                                    <div class="bg-white rounded-lg border-2 border-gray-200 p-4 shadow-sm">
                                        {{-- Item Header --}}
                                        <div class="flex items-start gap-3 mb-3 pb-3 border-b">
                                            <span class="text-2xl">{{ $quotationItem->category_icon }}</span>
                                            <div class="flex-1">
                                                <h5 class="font-bold text-gray-900">Item #{{ $quotationItem->item_number }}: {{ $quotationItem->label }}</h5>
                                                <div class="text-xs text-gray-600 mt-1">
                                                    <span class="font-semibold">Kategori:</span> {{ $quotationItem->category }}
                                                    @if($quotationItem->shoe_color)
                                                        | <span class="font-semibold">Warna:</span> {{ $quotationItem->shoe_color }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>


                                        {{-- Service Selection with Search --}}
                                        <div class="space-y-2">
                                            <p class="text-xs font-semibold text-gray-700 mb-2">Pilih Layanan:</p>
                                            
                                            {{-- Search Input --}}
                                            <input type="text" 
                                                   id="search-services-{{ $quotationItem->id }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                                   placeholder="Cari dan pilih layanan..."
                                                   onkeyup="filterServices({{ $quotationItem->id }})">

                                            {{-- Services List --}}
                                            <div id="services-container-{{ $quotationItem->id }}" class="max-h-64 overflow-y-auto space-y-1 mt-2 border rounded-lg p-2 bg-gray-50">
                                                @foreach($services as $service)
                                                    <div class="service-wrapper-{{ $quotationItem->id }}-{{ $service->id }}">
                                                        <label for="service-{{ $quotationItem->id }}-{{ $service->id }}" 
                                                               class="service-item-{{ $quotationItem->id }} flex items-start justify-between p-3 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-300 cursor-pointer transition bg-white"
                                                               data-service-name="{{ strtolower($service->name) }}"
                                                               data-service-category="{{ strtolower($service->category ?? '') }}"
                                                               data-service-description="{{ strtolower($service->description ?? '') }}">
                                                            <div class="flex items-start gap-3 flex-1">
                                                                <input type="checkbox" 
                                                                       id="service-{{ $quotationItem->id }}-{{ $service->id }}"
                                                                       name="items[{{ $quotationItem->id }}][services][]" 
                                                                       value="{{ $service->id }}"
                                                                       data-item-id="{{ $quotationItem->id }}"
                                                                       data-service-id="{{ $service->id }}"
                                                                       data-price="{{ $service->price }}"
                                                                       data-name="{{ $service->name }}"
                                                                       data-category="{{ $service->category ?? '-' }}"
                                                                       data-description="{{ $service->description ?? '-' }}"
                                                                       onchange="toggleServiceDetail({{ $quotationItem->id }}, {{ $service->id }}); updateItemTotal({{ $quotationItem->id }}); updateSelectedServices({{ $quotationItem->id }});"
                                                                       class="service-checkbox mt-1 w-4 h-4 text-purple-600 rounded">
                                                                <div class="flex-1">
                                                                    <p class="text-sm font-semibold text-gray-800">{{ $service->name }}</p>
                                                                    <p class="text-xs text-gray-600 mt-0.5">📂 {{ $service->category ?? 'Tidak ada kategori' }}</p>
                                                                    <p class="text-xs text-gray-500 mt-1">{{ $service->description ?? 'Tidak ada detail jasa' }}</p>
                                                                </div>
                                                            </div>
                                                            <span class="text-sm font-bold text-purple-600 ml-3">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                                                        </label>
                                                        
                                                        {{-- Detail Input (Hidden by default) --}}
                                                        <div id="detail-{{ $quotationItem->id }}-{{ $service->id }}" class="hidden mt-2 ml-10 mr-3">
                                                            <label for="detail-input-{{ $quotationItem->id }}-{{ $service->id }}" class="text-xs font-semibold text-gray-700">Detail Jasa (opsional):</label>
                                                            <textarea id="detail-input-{{ $quotationItem->id }}-{{ $service->id }}"
                                                                      name="items[{{ $quotationItem->id }}][service_details][{{ $service->id }}]"
                                                                      oninput="updateSelectedServices({{ $quotationItem->id }})"
                                                                      class="w-full px-3 py-2 border border-purple-300 rounded-lg text-sm mt-1 bg-purple-50"
                                                                      rows="2"
                                                                      placeholder="Contoh: Jahit Sol menggunakan Pola kecil"></textarea>
                                                            <p class="text-xs text-gray-500 mt-1">💡 Tambahkan detail spesifik untuk layanan ini</p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Custom Service Section --}}
                                            <div class="mt-3 border-t pt-3">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-xs font-semibold text-gray-700">Layanan Custom:</span>
                                                    <button type="button" 
                                                            onclick="toggleCustomService({{ $quotationItem->id }})"
                                                            class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg font-semibold transition">
                                                        + Tambah Custom
                                                    </button>
                                                </div>
                                                
                                                <div id="custom-service-{{ $quotationItem->id }}" class="hidden space-y-2 mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                                    <div>
                                                        <label for="custom-name-{{ $quotationItem->id }}" class="text-xs font-semibold text-gray-700">Nama Layanan *</label>
                                                        <input type="text" 
                                                               id="custom-name-{{ $quotationItem->id }}"
                                                               class="w-full px-3 py-2 border rounded-lg text-sm mt-1"
                                                               placeholder="Contoh: Pasang Kancing Khusus">
                                                    </div>
                                                    <div>
                                                        <label for="custom-price-{{ $quotationItem->id }}" class="text-xs font-semibold text-gray-700">Harga *</label>
                                                        <input type="number" 
                                                               id="custom-price-{{ $quotationItem->id }}"
                                                               class="w-full px-3 py-2 border rounded-lg text-sm mt-1"
                                                               placeholder="50000">
                                                    </div>
                                                    <div>
                                                        <label for="custom-description-{{ $quotationItem->id }}" class="text-xs font-semibold text-gray-700">Detail Jasa (opsional)</label>
                                                        <textarea id="custom-description-{{ $quotationItem->id }}"
                                                                  class="w-full px-3 py-2 border rounded-lg text-sm mt-1"
                                                                  rows="2"
                                                                  placeholder="Detail layanan custom..."></textarea>
                                                    </div>
                                                    <button type="button"
                                                            onclick="addCustomService({{ $quotationItem->id }})"
                                                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                                                        Tambahkan ke Daftar
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Selected Services Summary --}}
                                            <div id="selected-summary-{{ $quotationItem->id }}" class="mt-3 hidden">
                                                <span class="text-xs font-semibold text-gray-700 mb-2 block">Layanan Terpilih:</span>
                                                <div id="selected-list-{{ $quotationItem->id }}" class="space-y-1"></div>
                                            </div>

                                            {{-- Item Subtotal --}}
                                            <div class="mt-3 pt-3 border-t flex justify-between items-center">
                                                <span class="text-sm font-semibold text-gray-700">Subtotal Item #{{ $quotationItem->item_number }}:</span>
                                                <span class="text-lg font-bold text-purple-600" id="item-subtotal-{{ $quotationItem->id }}">Rp 0</span>
                                            </div>
                                        </div>

                                        {{-- Hidden input for quotation_item_id --}}
                                        <input type="hidden" name="items[{{ $quotationItem->id }}][quotation_item_id]" value="{{ $quotationItem->id }}">
                                    </div>
                                @endforeach
                            </div>

                            {{-- Grand Total --}}
                            <div class="mt-4 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg p-4 text-white">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold">TOTAL SEMUA ITEMS:</span>
                                    <span class="text-2xl font-black" id="grand-total">Rp 0</span>
                                </div>
                                <p class="text-xs mt-1 opacity-80">Total dari {{ count($acceptedQuotation->quotationItems ?? []) }} item</p>
                            </div>
                        </div>

                        <script>
                            // Filter services based on search input
                            function filterServices(itemId) {
                                const searchInput = document.getElementById(`search-services-${itemId}`);
                                const filter = searchInput.value.toLowerCase();
                                const serviceItems = document.querySelectorAll(`.service-item-${itemId}`);
                                
                                serviceItems.forEach(item => {
                                    const name = item.dataset.serviceName || '';
                                    const category = item.dataset.serviceCategory || '';
                                    const description = item.dataset.serviceDescription || '';
                                    
                                    const matchesSearch = name.includes(filter) || 
                                                         category.includes(filter) || 
                                                         description.includes(filter);
                                    
                                    item.style.display = matchesSearch ? 'flex' : 'none';
                                });
                            }

                            // Toggle service detail input
                            function toggleServiceDetail(itemId, serviceId) {
                                const checkbox = document.getElementById(`service-${itemId}-${serviceId}`);
                                const detailDiv = document.getElementById(`detail-${itemId}-${serviceId}`);
                                
                                if (checkbox.checked) {
                                    detailDiv.classList.remove('hidden');
                                } else {
                                    detailDiv.classList.add('hidden');
                                }
                            }

                            // Toggle custom service form
                            function toggleCustomService(itemId) {
                                const customForm = document.getElementById(`custom-service-${itemId}`);
                                customForm.classList.toggle('hidden');
                            }

                            // Add custom service to list
                            function addCustomService(itemId) {
                                const nameInput = document.getElementById(`custom-name-${itemId}`);
                                const priceInput = document.getElementById(`custom-price-${itemId}`);
                                const descriptionInput = document.getElementById(`custom-description-${itemId}`);
                                
                                const name = nameInput.value.trim();
                                const price = parseFloat(priceInput.value) || 0;
                                const description = descriptionInput.value.trim() || 'Layanan custom';
                                
                                if (!name || price <= 0) {
                                    alert('Nama layanan dan harga harus diisi!');
                                    return;
                                }
                                
                                // Create custom service element
                                const container = document.getElementById(`services-container-${itemId}`);
                                const customId = `custom-${Date.now()}`;
                                
                                const serviceLabel = document.createElement('label');
                                serviceLabel.className = `service-item-${itemId} flex items-start justify-between p-3 border border-green-300 rounded-lg hover:bg-green-50 hover:border-green-400 cursor-pointer transition bg-green-50`;
                                serviceLabel.dataset.serviceName = name.toLowerCase();
                                serviceLabel.dataset.serviceCategory = 'custom';
                                serviceLabel.dataset.serviceDescription = description.toLowerCase();
                                
                                serviceLabel.innerHTML = `
                                    <div class="flex items-start gap-3 flex-1">
                                        <input type="checkbox" 
                                               name="items[${itemId}][custom_services][]" 
                                               value="${customId}"
                                               data-item-id="${itemId}"
                                               data-price="${price}"
                                               data-name="${name}"
                                               data-category="Custom"
                                               data-description="${description}"
                                               onchange="updateItemTotal(${itemId}); updateSelectedServices(${itemId});"
                                               class="service-checkbox mt-1 w-4 h-4 text-green-600 rounded"
                                               checked>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-800">${name} <span class="text-xs bg-green-600 text-white px-2 py-0.5 rounded">CUSTOM</span></p>
                                            <p class="text-xs text-gray-600 mt-0.5">📂 Custom</p>
                                            <p class="text-xs text-gray-500 mt-1">${description}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-green-600 ml-3">Rp ${price.toLocaleString('id-ID')}</span>
                                    
                                    <input type="hidden" name="items[${itemId}][custom_service_names][]" value="${name}">
                                    <input type="hidden" name="items[${itemId}][custom_service_prices][]" value="${price}">
                                    <input type="hidden" name="items[${itemId}][custom_service_descriptions][]" value="${description}">
                                `;
                                
                                container.appendChild(serviceLabel);
                                
                                // Clear form
                                nameInput.value = '';
                                priceInput.value = '';
                                descriptionInput.value = '';
                                
                                // Hide form
                                toggleCustomService(itemId);
                                
                                // Update totals
                                updateItemTotal(itemId);
                                updateSelectedServices(itemId);
                            }

                            // Update item subtotal
                            function updateItemTotal(itemId) {
                                let total = 0;
                                const checkboxes = document.querySelectorAll(`input[data-item-id="${itemId}"]:checked`);
                                
                                checkboxes.forEach(cb => {
                                    total += parseFloat(cb.dataset.price) || 0;
                                });
                                
                                document.getElementById(`item-subtotal-${itemId}`).textContent = 
                                    'Rp ' + total.toLocaleString('id-ID');
                                
                                updateGrandTotal();
                            }

                            // Update selected services summary
                            function updateSelectedServices(itemId) {
                                const checkboxes = document.querySelectorAll(`input[data-item-id="${itemId}"]:checked`);
                                const container = document.getElementById(`selected-summary-${itemId}`);
                                const list = document.getElementById(`selected-list-${itemId}`);
                                
                                list.innerHTML = '';
                                
                                if (checkboxes.length > 0) {
                                    container.classList.remove('hidden');
                                    
                                    checkboxes.forEach(cb => {
                                        const serviceId = cb.dataset.serviceId;
                                        const name = cb.dataset.name;
                                        const price = parseFloat(cb.dataset.price) || 0;
                                        const category = cb.dataset.category || 'Tidak ada kategori';
                                        
                                        // Get detail from textarea if it's a regular service, otherwise use dataset description (for custom)
                                        let description = '';
                                        if (serviceId) {
                                            const detailInput = document.getElementById(`detail-input-${itemId}-${serviceId}`);
                                            description = detailInput ? detailInput.value.trim() : '';
                                            if (!description) description = cb.dataset.description || 'Tidak ada detail jasa';
                                        } else {
                                            description = cb.dataset.description || 'Tidak ada detail jasa';
                                        }
                                        
                                        const card = document.createElement('div');
                                        card.className = 'bg-purple-50 border border-purple-200 rounded p-2 text-xs';
                                        card.innerHTML = `
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-800">${name}</p>
                                                    <p class="text-gray-600 mt-0.5">📂 ${category}</p>
                                                    <p class="text-gray-500 mt-1 italic">${description}</p>
                                                </div>
                                                <span class="font-bold text-purple-600 ml-2">Rp ${price.toLocaleString('id-ID')}</span>
                                            </div>
                                        `;
                                        list.appendChild(card);
                                    });
                                } else {
                                    container.classList.add('hidden');
                                }
                            }

                            // Update grand total
                            function updateGrandTotal() {
                                let grandTotal = 0;
                                const allCheckboxes = document.querySelectorAll('.service-checkbox:checked');
                                
                                allCheckboxes.forEach(cb => {
                                    grandTotal += parseFloat(cb.dataset.price) || 0;
                                });
                                
                                document.getElementById('grand-total').textContent = 
                                    'Rp ' + grandTotal.toLocaleString('id-ID');
                            }
                        </script>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-yellow-800">⚠️ Tidak ada quotation yang diterima atau tidak ada item dalam quotation.</p>
                        </div>
                    @endif
                    {{-- Promo Code Section --}}
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <label class="block text-sm font-bold text-gray-800 mb-2">🎁 Voucher / Promo Code</label>
                        <div class="flex gap-2">
                            <input type="text" name="promo_code" id="promo-code-input" class="flex-1 px-3 py-2 border rounded-lg text-sm uppercase font-mono" placeholder="CONTOH: HEMAT50K">
                            <button type="button" onclick="validatePromo()" id="btn-apply-promo" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition">
                                Apply
                            </button>
                        </div>
                        <div id="promo-status" class="mt-2 hidden">
                            <p class="text-xs font-semibold" id="promo-message"></p>
                        </div>
                    </div>

                    {{-- DP & Payment --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="font-bold text-gray-800 mb-3">Pembayaran</h4>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">DP Amount *</label>
                            <input type="number" name="dp_amount" id="dp-amount-input" value="0" min="0" required class="w-full px-3 py-2 border rounded-lg text-sm">
                            <p class="text-[10px] text-gray-500 mt-1">* Minimal 30% dari total. <span class="font-semibold" id="dp-suggestion">Saran: Rp 0</span></p>
                        </div>
                    </div>

                    <script>
                        // Update DP suggestion when grand total changes
                        document.addEventListener('DOMContentLoaded', function() {
                            const observer = new MutationObserver(function() {
                                const grandTotalText = document.getElementById('grand-total').textContent;
                                const grandTotal = parseFloat(grandTotalText.replace(/[^0-9]/g, ''));
                                const dpSuggestion = Math.ceil(grandTotal * 0.3);
                                
                                document.getElementById('dp-suggestion').textContent = 
                                    'Saran (30%): Rp ' + dpSuggestion.toLocaleString('id-ID');
                                document.getElementById('dp-amount-input').value = dpSuggestion;
                            });
                            
                            const grandTotalElement = document.getElementById('grand-total');
                            if (grandTotalElement) {
                                observer.observe(grandTotalElement, { childList: true, characterData: true, subtree: true });
                            }
                        });
                    </script>




                    {{-- Special Instructions --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Instruksi Khusus</label>
                        <textarea name="special_instructions" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Catatan khusus untuk workshop...">{{ $acceptedQuotation->notes ?? '' }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="closeSpkModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold">
                        Generate SPK
                    </button>
                </div>
            </form>
        </div>
    </div>



    {{-- Modal: Set Follow Up --}}
    <div id="followUpModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-xl bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Jadwalkan Follow Up</h3>
                <button onclick="closeFollowUpModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('cs.leads.set-follow-up', $lead->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal & Waktu *</label>
                        <input type="datetime-local" name="next_follow_up_at" required min="{{ date('Y-m-d\TH:i') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Follow Up</label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="Apa yang perlu di-follow up?"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="closeFollowUpModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold">
                        Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Handover to Workshop --}}
    <div id="handoverModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-2xl rounded-xl bg-white mb-20">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Serahkan ke GUDANG</h3>
                <button onclick="closeHandoverModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>


            @if($lead->spk && count($lead->spk->items ?? []) > 0)
            <form action="{{ route('cs.spk.hand-to-workshop', $lead->spk->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="text-sm text-blue-800 font-semibold mb-1">📦 Konfirmasi {{ count($lead->spk->items ?? []) }} Items</p>
                        <p class="text-xs text-blue-600">Sistem akan membuat {{ count($lead->spk->items ?? []) }} Work Order terpisah (1 per item) dengan nomor SPK Gudang sesuai kategori masing-masing.</p>
                    </div>

                    {{-- Items Loop --}}
                    <div class="space-y-3">
                        @foreach($lead->spk->items as $spkItem)
                            <div class="bg-white border-2 border-gray-200 rounded-lg p-4">
                                {{-- Item Header --}}
                                <div class="flex items-start gap-3 mb-3 pb-3 border-b">
                                    <span class="text-2xl">{{ $spkItem->category_icon }}</span>
                                    <div class="flex-1">
                                        <h5 class="font-bold text-gray-900">Item #{{ $spkItem->item_number }}: {{ $spkItem->label }}</h5>
                                        <div class="text-xs text-gray-600 mt-1">
                                            <span class="font-semibold">Kategori:</span> {{ $spkItem->category }}
                                            @if($spkItem->shoe_color)
                                                | <span class="font-semibold">Warna:</span> {{ $spkItem->shoe_color }}
                                            @endif
                                        </div>
                                        <div class="text-xs mt-2 space-y-1">
                                            <span class="font-semibold text-gray-700 block mb-1 underline">Layanan:</span>
                                            @foreach($spkItem->services as $service)
                                                <div class="flex flex-col gap-1 text-purple-700 bg-purple-50 px-2 py-1.5 rounded border border-purple-100">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-medium">• {{ $service['name'] }}</span>
                                                        @if(!empty($service['is_custom']))
                                                            <span class="bg-green-600 text-[10px] text-white px-1.5 py-0.5 rounded font-bold uppercase">Custom</span>
                                                        @endif
                                                    </div>
                                                    @if(!empty($service['manual_detail']))
                                                        <span class="text-[10px] text-purple-500 italic ml-3 border-l-2 border-purple-200 pl-2">{{ $service['manual_detail'] }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Subtotal</p>
                                        <p class="text-lg font-bold text-purple-600">Rp {{ number_format($spkItem->item_total_price, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                {{-- Item Type (Auto-selected based on category) --}}
                                <input type="hidden" name="items[{{ $spkItem->id }}][item_type]" value="{{ $spkItem->category_prefix }}">
                                <input type="hidden" name="items[{{ $spkItem->id }}][spk_item_id]" value="{{ $spkItem->id }}">

                                {{-- Physical Details (Pre-filled from SPK Item) --}}
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                    <h6 class="font-bold text-gray-800 mb-2 text-xs">Detail Fisik Barang</h6>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label for="brand-{{ $spkItem->id }}" class="block text-[10px] font-semibold text-gray-700 mb-1">Merk</label>
                                            <input type="text" id="brand-{{ $spkItem->id }}" name="items[{{ $spkItem->id }}][shoe_brand]" value="{{ $spkItem->shoe_brand }}" class="w-full px-2 py-1 border rounded text-xs" placeholder="Nike">
                                        </div>
                                        <div>
                                            <label for="type-{{ $spkItem->id }}" class="block text-[10px] font-semibold text-gray-700 mb-1">Tipe</label>
                                            <input type="text" id="type-{{ $spkItem->id }}" name="items[{{ $spkItem->id }}][shoe_type]" value="{{ $spkItem->shoe_type }}" class="w-full px-2 py-1 border rounded text-xs" placeholder="Air Jordan">
                                        </div>
                                        <div>
                                            <label for="color-{{ $spkItem->id }}" class="block text-[10px] font-semibold text-gray-700 mb-1">Warna</label>
                                            <input type="text" id="color-{{ $spkItem->id }}" name="items[{{ $spkItem->id }}][shoe_color]" value="{{ $spkItem->shoe_color }}" class="w-full px-2 py-1 border rounded text-xs" placeholder="Merah">
                                        </div>
                                        <div>
                                            <label for="size-{{ $spkItem->id }}" class="block text-[10px] font-semibold text-gray-700 mb-1">Ukuran</label>
                                            <input type="text" id="size-{{ $spkItem->id }}" name="items[{{ $spkItem->id }}][shoe_size]" value="{{ $spkItem->shoe_size }}" class="w-full px-2 py-1 border rounded text-xs" placeholder="42">
                                        </div>
                                    </div>
                                </div>

                                {{-- Photo Reference (Optional per item) --}}
                                <div class="mt-2">
                                    <label for="ref_photo-{{ $spkItem->id }}" class="block text-[10px] font-semibold text-gray-700 mb-1">Foto Referensi (Opsional)</label>
                                    <input type="file" id="ref_photo-{{ $spkItem->id }}" name="items[{{ $spkItem->id }}][ref_photo]" accept="image/*" class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Summary --}}
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg p-4 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-semibold">Total {{ count($lead->spk->items ?? []) }} Items</p>
                                <p class="text-xs opacity-80">Akan generate {{ count($lead->spk->items ?? []) }} Work Orders</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs">Grand Total</p>
                                <p class="text-2xl font-black">Rp {{ number_format($lead->spk->total_price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="closeHandoverModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                        ✅ Proses Handover ({{ count($lead->spk->items ?? []) }} WO)
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>

    <script>
        // Handover Modal
        function openHandoverModal() {
            document.getElementById('handoverModal').classList.remove('hidden');
        }
        function closeHandoverModal() {
            document.getElementById('handoverModal').classList.add('hidden');
        }

        // Follow Up Modal
        function openFollowUpModal() {
            document.getElementById('followUpModal').classList.remove('hidden');
        }
        function closeFollowUpModal() {
            document.getElementById('followUpModal').classList.add('hidden');
        }

        // Activity Modal
        function openActivityModal() {
            document.getElementById('activityModal').classList.remove('hidden');
        }
        function closeActivityModal() {
            document.getElementById('activityModal').classList.add('hidden');
        }

        // Quotation Modal
        function openQuotationModal() {
            document.getElementById('quotationModal').classList.remove('hidden');
        }
        function closeQuotationModal() {
            document.getElementById('quotationModal').classList.add('hidden');
        }

        function autoFillPrice(input) {
            const val = input.value;
            const options = document.getElementById('service-list').options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === val) {
                    const price = options[i].getAttribute('data-price');
                    const row = input.closest('.quotation-item');
                    row.querySelector('.service-price-input').value = price;
                    break;
                }
            }
        }

        let itemIndex = 1;
        function addQuotationItem() {
            const container = document.getElementById('quotation-items');
            const newItem = `
                <div class="quotation-item border rounded p-3 bg-gray-50">
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-5 relative">
                            <input type="text" name="items[${itemIndex}][service_name]" list="service-list" required class="service-name-input w-full px-2 py-1 border rounded text-sm" placeholder="Nama Service" onchange="autoFillPrice(this)">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="items[${itemIndex}][qty]" value="1" min="1" required class="w-full px-2 py-1 border rounded text-sm" placeholder="Qty">
                        </div>
                        <div class="col-span-4">
                            <input type="number" name="items[${itemIndex}][price]" required class="service-price-input w-full px-2 py-1 border rounded text-sm" placeholder="Harga">
                        </div>
                        <div class="col-span-1 flex items-center justify-center">
                            <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <input type="text" name="items[${itemIndex}][description]" class="w-full px-2 py-1 border rounded text-sm" placeholder="Deskripsi (opsional)">
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newItem);
            itemIndex++;
        }

        function removeItem(button) {
            const items = document.querySelectorAll('.quotation-item');
            if (items.length > 1) {
                button.closest('.quotation-item').remove();
            } else {
                alert('Minimal harus ada 1 item!');
            }
        }

        // Handover Modal
        function openHandoverModal() {
            document.getElementById('handoverModal').classList.remove('hidden');
        }
        function closeHandoverModal() {
            document.getElementById('handoverModal').classList.add('hidden');
        }

        // SPK Modal
        function openSpkModal() {
            document.getElementById('spkModal').classList.remove('hidden');
        }
        function closeSpkModal() {
            document.getElementById('spkModal').classList.add('hidden');
        }

        // Quick Actions
        function moveToKonsultasi() {
            const notes = prompt('Catatan untuk pindah ke Konsultasi:');
            if (notes !== null) {
                fetch("{{ route('cs.leads.move-konsultasi', $lead->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ notes: notes })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function moveToClosing() {
            if (confirm('Pindahkan lead ke Closing?')) {
                fetch("{{ route('cs.leads.move-closing', $lead->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function rejectQuotation(quotationId) {
            const reason = prompt('Alasan penolakan:');
            if (reason !== null && reason.trim() !== '') {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/cs/quotations/${quotationId}/reject`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
                
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'rejection_reason';
                reasonInput.value = reason;
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                form.appendChild(reasonInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function markDpPaid() {
            // Deprecated: replaced by openDpModal
        }



        function markLost() {
            const reason = prompt('Alasan lead LOST:');
            if (reason !== null && reason.trim() !== '') {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('cs.leads.mark-lost', $lead->id) }}";
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'lost_reason';
                reasonInput.value = reason;
                
                form.appendChild(csrfInput);
                form.appendChild(reasonInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // SPK Preview Logic
        function updateSpkPreview() {
            const deliverySelect = document.getElementById('deliveryTypeSelect');
            const deliveryCode = deliverySelect.options[deliverySelect.selectedIndex].getAttribute('data-code');
            const dateStr = "{{ date('ym-d') }}";
            const csCode = document.getElementById('manualCsInput').value.toUpperCase() || '??';
            
            const previewText = `${deliveryCode}-${dateStr}-XXXX-${csCode}`;
            document.getElementById('spkPreview').innerText = previewText;
        }

        // Promo Validation Logic
        async function validatePromo() {
            const codeInput = document.getElementById('promo-code-input');
            const code = codeInput.value.trim();
            const statusDiv = document.getElementById('promo-status');
            const messageP = document.getElementById('promo-message');
            const btnApply = document.getElementById('btn-apply-promo');

            if (!code) {
                alert('Masukkan kode promo!');
                return;
            }

            // Show loading
            btnApply.disabled = true;
            btnApply.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Checking...';
            statusDiv.classList.add('hidden');

            try {
                // Get grand total before discount
                const checkboxes = document.querySelectorAll('.service-checkbox:checked');
                let subtotal = 0;
                let serviceIds = [];
                checkboxes.forEach(cb => {
                    subtotal += parseFloat(cb.dataset.price) || 0;
                    if (cb.dataset.serviceId) {
                        serviceIds.push(cb.dataset.serviceId);
                    }
                });

                if (subtotal <= 0) {
                    alert('Pilih setidaknya satu layanan terlebih dahulu!');
                    btnApply.disabled = false;
                    btnApply.innerText = 'Apply';
                    return;
                }

                const response = await fetch('/api/cs/promos/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        code: code,
                        total_amount: subtotal,
                        service_ids: serviceIds
                    })
                });

                const result = await response.json();

                statusDiv.classList.remove('hidden');
                if (result.valid) {
                    messageP.className = 'text-xs font-semibold text-green-600';
                    messageP.innerHTML = `✅ Promo Berhasil! Diskon: <strong>Rp ${result.discount.toLocaleString('id-ID')}</strong>`;
                    codeInput.classList.add('border-green-500');
                    codeInput.classList.remove('border-red-500');
                    
                    // Update Grand Total with discount
                    const grandTotal = subtotal - result.discount;
                    document.getElementById('grand-total').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
                    
                    // Update DP Suggestion
                    const dpSuggestion = Math.ceil(grandTotal * 0.3);
                    document.getElementById('dp-suggestion').textContent = 'Saran (30%): Rp ' + dpSuggestion.toLocaleString('id-ID');
                    document.getElementById('dp-amount-input').value = dpSuggestion;

                } else {
                    messageP.className = 'text-xs font-semibold text-red-600';
                    messageP.innerText = '❌ ' + result.message;
                    codeInput.classList.add('border-red-500');
                    codeInput.classList.remove('border-green-500');
                    
                    // Reset Grand Total to original
                    document.getElementById('grand-total').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
                }
            } catch (error) {
                console.error('Error validating promo:', error);
                alert('Gagal memvalidasi promo. Silakan coba lagi.');
            } finally {
                btnApply.disabled = false;
                btnApply.innerText = 'Apply';
            }
        }

        // Initialize preview on load
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('deliveryTypeSelect')) {
                updateSpkPreview();
            }
        });
    </script>
</x-app-layout>
