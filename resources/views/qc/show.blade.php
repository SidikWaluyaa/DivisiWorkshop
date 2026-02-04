<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    {{ __('Inspection Details') }}
                </h2>
                <div class="text-xs font-medium opacity-90 flex items-center gap-2">
                    <span class="bg-white/20 px-2 py-0.5 rounded text-white font-mono">
                        {{ $order->spk_number }}
                    </span>
                    <span>{{ $order->customer_name }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- LEFT COLUMN: INFO SEPATU -->
                <div class="space-y-6">
                    <div class="dashboard-card overflow-hidden">
                        <div class="dashboard-card-header bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                            <h3 class="dashboard-card-title text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Shoe Information
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Merek & Artikel</label>
                                <div class="font-bold text-gray-800 text-lg">{{ $order->shoe_brand }}</div>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Warna & Ukuran</label>
                                <div class="text-gray-700 flex items-center gap-2">
                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs font-bold">{{ $order->shoe_color }}</span>
                                    <span class="text-gray-300">|</span>
                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs font-bold">{{ $order->shoe_size ?? '-' }}</span>
                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs font-bold">{{ $order->shoe_size ?? '-' }}</span>
                                </div>
                            </div>

                            {{-- TECHNICIAN INSTRUCTION / ALERT --}}
                            @if($order->technician_notes)
                                <div class="p-3 bg-amber-50 border-l-4 border-amber-500 rounded-r text-sm text-amber-900 font-medium">
                                    <span class="block font-bold text-amber-600 uppercase text-[10px] tracking-wide mb-1">⚠️ Instruksi Khusus Teknisi:</span>
                                    {{ $order->technician_notes }}
                                </div>
                            @endif

                            {{-- CS NOTES (Readonly) --}}
                            @if($order->notes)
                                <div>
                                    <label class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-1 block">💬 Request / Keluhan Customer (CS)</label>
                                    <div class="text-xs text-blue-900 italic border-l-4 border-blue-200 p-2 rounded bg-blue-50 leading-relaxed">
                                        "{{ $order->notes }}"
                                    </div>
                                </div>
                            @endif

                            {{-- CX FOLLOW UP HISTORY --}}
                            @php
                                $resolvedIssue = $order->cxIssues->where('status', 'RESOLVED')->last();
                            @endphp
                            @if($resolvedIssue)
                                <div class="p-3 bg-purple-50 border-l-4 border-purple-500 rounded-r text-sm text-purple-900 font-medium">
                                    <span class="block font-bold text-purple-600 uppercase text-[10px] tracking-wide mb-1">⚠️ Riwayat Follow Up CX:</span>
                                    <div class="italic">"{{ $resolvedIssue->resolution_notes ?? $resolvedIssue->description ?? '-' }}"</div>
                                    <div class="mt-1 text-[9px] text-purple-500 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Done by {{ $resolvedIssue->resolver->name ?? 'System' }} • {{ $resolvedIssue->updated_at->format('d/M H:i') }}
                                    </div>
                                </div>
                            @endif

                            <div class="pt-4 border-t border-gray-100">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 block">Layanan yang Dikerjakan</label>
                                <div class="space-y-3">
                                    @foreach($order->workOrderServices as $detail)
                                        <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm hover:border-teal-500/30 transition-all group">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex flex-col">
                                                    <span class="text-[9px] font-black bg-teal-50 text-teal-600 px-2 py-0.5 rounded uppercase tracking-widest mb-1 self-start">
                                                        {{ $detail->category_name ?? $detail->service->category ?? 'General' }}
                                                    </span>
                                                    <span class="text-xs font-black text-gray-800 uppercase tracking-tight group-hover:text-teal-600 transition-colors">
                                                        {{ $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan') }}
                                                    </span>
                                                </div>
                                                <span class="text-[10px] font-black text-teal-600">
                                                    Rp {{ number_format($detail->cost, 0, ',', '.') }}
                                                </span>
                                            </div>

                                            @if($detail->service && $detail->service->description)
                                                <p class="text-[10px] text-gray-400 font-medium mb-2 leading-relaxed italic">
                                                    {{ $detail->service->description }}
                                                </p>
                                            @endif

                                            @if(isset($detail->service_details['manual_detail']) && !empty($detail->service_details['manual_detail']))
                                                <div class="mb-3 p-2 bg-yellow-50 border border-yellow-100 rounded-lg text-[10px] text-yellow-800 font-bold italic">
                                                    "{{ $detail->service_details['manual_detail'] }}"
                                                </div>
                                            @endif

                                            <div class="pt-2 border-t border-gray-50 flex items-center justify-between">
                                                <div class="text-[9px] text-gray-500 font-black flex items-center gap-1.5 uppercase tracking-wide">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-teal-500"></div>
                                                    PIC: {{ $detail->technician ? $detail->technician->name : ($detail->technician_id ? \App\Models\User::find($detail->technician_id)->name : '-') }}
                                                </div>
                                                <div class="flex gap-1">
                                                    @if(!empty($detail->service_details) && is_array($detail->service_details))
                                                        @foreach($detail->service_details as $key => $val)
                                                            @if($key !== 'manual_detail' && !empty($val))
                                                                <span class="text-[8px] font-bold text-gray-400 bg-gray-50 px-1 py-0.5 rounded lowercase">
                                                                    #{{ is_array($val) ? implode(', ', $val) : $val }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: QC CHECKLIST -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="dashboard-card overflow-hidden">
                        <div class="dashboard-card-header bg-gradient-to-r from-teal-700 to-teal-800 border-b border-teal-600">
                            <h3 class="dashboard-card-title text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                Inspection Checklist
                            </h3>
                        </div>
                        
                        <div class="p-6 space-y-8">
                            <!-- 1. Jahit Sol -->
                            <div class="relative pl-8 border-l-2 {{ $subtasks['jahit']['done'] ? 'border-green-500' : 'border-gray-200' }}">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full {{ $subtasks['jahit']['done'] ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                <h4 class="text-sm font-bold text-gray-800 mb-2">1. Pengecekan Jahitan Sol (Jika ada)</h4>
                                
                                @if($subtasks['jahit']['done'])
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="font-bold text-green-700">PASSED</span>
                                        </div>
                                        <div class="text-right text-xs text-gray-500">
                                            <div>Verified at {{ $subtasks['jahit']['end']->format('H:i') }}</div>
                                            <div>Duration: {{ $subtasks['jahit']['duration'] }} m</div>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('qc.update', $order->id) }}" method="POST" class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        @csrf
                                        <input type="hidden" name="type" value="jahit">
                                        <div class="flex gap-4 items-end">
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-500 mb-1">Inspector Name</label>
                                                <select name="worker_id" class="block w-full text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                                                    <option value="">-- Select Inspector --</option>
                                                    @foreach($techJahit as $tech)
                                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow transition-colors">
                                                MARK PASS
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                            
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Temuan Awal (Before)</span>
                                    <x-photo-uploader :order="$order" step="QC_JAHIT_BEFORE" />
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Bukti Pengecekan (After)</span>
                                    <x-photo-uploader :order="$order" step="QC_JAHIT_AFTER" />
                                </div>
                            </div>
                        </div>

                            <!-- 2. Clean Up -->
                            <div class="relative pl-8 border-l-2 {{ $subtasks['clean_up']['done'] ? 'border-green-500' : 'border-gray-200' }}">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full {{ $subtasks['clean_up']['done'] ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                <h4 class="text-sm font-bold text-gray-800 mb-2">2. Kebersihan / Clean Up Detail</h4>
                                
                                @if($subtasks['clean_up']['done'])
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="font-bold text-green-700">PASSED</span>
                                        </div>
                                        <div class="text-right text-xs text-gray-500">
                                            <div>Verified at {{ $subtasks['clean_up']['end']->format('H:i') }}</div>
                                            <div>Duration: {{ $subtasks['clean_up']['duration'] }} m</div>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('qc.update', $order->id) }}" method="POST" class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        @csrf
                                        <input type="hidden" name="type" value="clean_up">
                                        <div class="flex gap-4 items-end">
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-500 mb-1">Inspector Name</label>
                                                <select name="worker_id" class="block w-full text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                                                    <option value="">-- Select Inspector --</option>
                                                    @foreach($techCleanup as $tech)
                                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow transition-colors">
                                                MARK PASS
                                            </button>
                                        </div>
                                    </form>
                                @endif
                                
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Temuan Awal (Before)</span>
                                        <x-photo-uploader :order="$order" step="QC_CLEANUP_BEFORE" />
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Bukti Pengecekan (After)</span>
                                        <x-photo-uploader :order="$order" step="QC_CLEANUP_AFTER" />
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Final Check -->
                            <div class="relative pl-8 border-l-2 {{ $subtasks['final']['done'] ? 'border-green-500' : 'border-gray-200' }}">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full {{ $subtasks['final']['done'] ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                <h4 class="text-sm font-bold text-gray-800 mb-2">3. QC Akhir (Keseluruhan)</h4>
                                
                                @if($subtasks['final']['done'])
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="font-bold text-green-700">PASSED</span>
                                        </div>
                                        <div class="text-right text-xs text-gray-500">
                                            <div>Verified at {{ $subtasks['final']['end']->format('H:i') }}</div>
                                            <div>Duration: {{ $subtasks['final']['duration'] }} m</div>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('qc.update', $order->id) }}" method="POST" class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        @csrf
                                        <input type="hidden" name="type" value="final">
                                        <div class="flex gap-4 items-end">
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-500 mb-1">Final Inspector PIC</label>
                                                <select name="worker_id" class="block w-full text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500" required>
                                                    <option value="">-- Select Final PIC --</option>
                                                    @foreach($techFinal as $tech)
                                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow transition-colors">
                                                MARK PASS
                                            </button>
                                        </div>
                                    </form>
                                @endif
                                
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Temuan Awal (Before)</span>
                                        <x-photo-uploader :order="$order" step="QC_FINAL_BEFORE" />
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="text-xs font-bold text-gray-500 uppercase block mb-2">Bukti Pengecekan (After)</span>
                                        <x-photo-uploader :order="$order" step="QC_FINAL_AFTER" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DECISION AREA -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- REJECT CARD -->
                        <div class="dashboard-card border-red-200 shadow-none border-2">
                            <div class="p-4 bg-red-50 border-b border-red-100">
                                <h3 class="font-bold text-red-800 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Found Issues?
                                </h3>
                            </div>
                            <div class="p-4">
                                <form action="{{ route('qc.fail', $order->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Select Services to Reject:</label>
                                        <div class="space-y-2 bg-white p-3 rounded border border-gray-200 max-h-40 overflow-y-auto">
                                            @foreach($order->workOrderServices as $detail)
                                                <label class="flex items-center p-2 hover:bg-red-50 rounded cursor-pointer transition-colors">
                                                    <input type="checkbox" name="rejected_services[]" value="{{ $detail->id }}" class="rounded text-red-600 focus:ring-red-500 border-gray-300">
                                                    <div class="ml-2 text-sm text-gray-700">
                                                        <span class="font-medium">{{ $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan') }}</span>
                                                        <span class="text-xs text-gray-400 block">by {{ $detail->technician ? $detail->technician->name : ($detail->technician_id ? \App\Models\User::find($detail->technician_id)->name : '-') }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Reason / Notes:</label>
                                        <input type="text" name="note" class="w-full text-sm border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="e.g. Lem kurang rapi di bagian heel" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Evidence Photo (Optional):</label>
                                        <input type="file" name="evidence_photo" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100" accept="image/*">
                                    </div>

                                    <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-bold text-sm shadow hover:shadow-md transition-all">
                                        ⛔ REJECT & RETURN TO PRODUCTION
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- CX FOLLOW UP CARD -->
                        <div class="dashboard-card border-amber-200 shadow-none border-2">
                            <div class="p-4 bg-amber-50 border-b border-amber-100">
                                <h3 class="font-bold text-amber-800 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Need CX Followup?
                                </h3>
                            </div>
                            <div class="p-4">
                                <form action="{{ route('cx-issues.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="work_order_id" value="{{ $order->id }}">
                                    
                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Category:</label>
                                        <select name="category" class="w-full text-sm border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500" required>
                                            <option value="">-- Select Category --</option>
                                            <option value="Teknis">Kendala Teknis (Konsultasi Customer)</option>
                                            <option value="Material">Masalah Material (Stok Habis/Beda)</option>
                                            <option value="Estimasi">Estimasi Waktu Meleset</option>
                                            <option value="Tambahan">Saran Tambah Jasa (Upsell)</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Description:</label>
                                        <textarea name="description" rows="2" class="w-full text-sm border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500" placeholder="Kirim pesan ke CX..." required></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Evidence Photo (Required):</label>
                                        <input type="file" name="photos[]" multiple class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100" accept="image/*" required>
                                    </div>

                                    <button class="w-full bg-amber-500 hover:bg-amber-600 text-white py-2 rounded-lg font-bold text-sm shadow hover:shadow-md transition-all">
                                        ⚠️ REPORT TO CX (PAUSE PROCESS)
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- APPROVE CARD -->
                        <div class="flex items-end justify-end">
                            @php
                                $allDone = $subtasks['jahit']['done'] && $subtasks['clean_up']['done'] && $subtasks['final']['done'];
                            @endphp
                            
                            @if($allDone)
                                <div class="w-full">
                                    <form action="{{ route('qc.pass', $order->id) }}" method="POST">
                                        @csrf
                                        <button class="w-full py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl shadow-lg transform hover:-translate-y-1 transition-all flex flex-col items-center justify-center gap-1">
                                            <span class="font-black text-xl tracking-wide">APPROVE & FINISH</span>
                                            <span class="text-xs font-medium opacity-90">All checks passed. Move to Finish Station.</span>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="w-full text-center p-6 bg-gray-100 rounded-xl border border-gray-200 border-dashed text-gray-400">
                                    <span class="block font-bold text-sm mb-1">Approval Locked</span>
                                    <span class="text-xs">Complete all checklist items to approve.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
