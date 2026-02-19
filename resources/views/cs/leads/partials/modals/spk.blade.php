{{-- Modal: Generate SPK --}}
<div id="spkModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
    <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-4xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white mb-20 overflow-hidden">
        {{-- Modal Header --}}
        <div class="bg-gray-50/80 px-10 py-8 flex justify-between items-center border-b border-gray-100">
            <div>
                <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Generate SPK</h3>
                <div class="flex items-center gap-2 mt-2">
                    <div class="w-12 h-1.5 bg-[#22AF85] rounded-full"></div>
                    <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Production Order Command</p>
                </div>
            </div>
            <button onclick="closeSpkModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 hover:border-gray-900 transition-all duration-300 group">
                <svg class="w-6 h-6 group-rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-10">
            <form action="{{ route('cs.spk.generate', $lead->id) }}" method="POST" class="space-y-10">
                @csrf
                {{-- SPK Preview Section --}}
                <div class="p-8 bg-gray-900 rounded-[2rem] shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-[#22AF85]/10 rounded-full blur-3xl -mr-32 -mt-32 transition-all group-hover:bg-[#22AF85]/20"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                        <div class="flex-1 text-center md:text-left">
                            <label class="block text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em] mb-4">Preview Draft Nomor SPK</label>
                            <div id="spkPreview" class="text-4xl md:text-5xl font-mono font-black text-white tracking-tighter truncate selection:bg-[#22AF85]">
                                F-{{ date('ym-d') }}-XXXX-{{ strtoupper($lead->cs->cs_code ?? '??') }}
                            </div>
                            <input type="hidden" name="spk_number" id="finalSpkNumber">
                            <div class="flex items-center gap-4 mt-6">
                                <span class="px-3 py-1 bg-[#22AF85] text-white rounded-lg text-[9px] font-black uppercase tracking-widest">Auto Generated</span>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">[KODE]-[YYMM]-[DD]-[SEQ]-[CS]</p>
                            </div>
                        </div>
                        
                        <div class="w-full md:w-auto grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2">Metode Kirim</label>
                                <select name="delivery_type" id="deliveryTypeSelect" required onchange="updateSpkPreview()"
                                        class="w-full px-5 py-3 rounded-xl border-2 border-gray-800 bg-gray-800 text-white text-xs font-bold focus:border-[#22AF85] focus:ring-0 transition-all">
                                    <option value="Offline" data-code="F">Offline (F)</option>
                                    <option value="Online" data-code="N">Online (N)</option>
                                    <option value="Pickup" data-code="P">Pickup (P)</option>
                                    <option value="Ojol" data-code="O">Ojol (O)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2">Kode CS</label>
                                <input type="text" name="manual_cs_code" id="manualCsInput" required maxlength="5" value="{{ $lead->cs->cs_code ?? '' }}" 
                                       oninput="updateSpkPreview()"
                                       class="w-full px-5 py-3 rounded-xl border-2 border-gray-800 bg-gray-800 text-white text-xs font-bold focus:border-[#22AF85] focus:ring-0 transition-all uppercase">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    {{-- Logistics & Priority --}}
                    <div class="space-y-8">
                        <div>
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">01</span>
                                Logistics & Scheduling
                            </h4>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Prioritas *</label>
                                    <select name="priority" required class="w-full px-6 py-4 rounded-2xl border-2 border-gray-50 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-gray-50/30">
                                        <option value="Reguler">Reguler</option>
                                        <option value="Prioritas">Prioritas</option>
                                        <option value="Urgent">Urgent</option>
                                        <option value="Express">Express</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Expected Delivery</label>
                                    <input type="date" name="expected_delivery_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           class="w-full px-6 py-4 rounded-2xl border-2 border-gray-50 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-gray-50/30">
                                </div>
                            </div>
                        </div>

                        {{-- Special Instructions --}}
                        <div>
                            <label class="block text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                Instruksi Khusus (Optional)
                            </label>
                            <textarea name="special_instructions" rows="4" 
                                      class="w-full px-8 py-6 rounded-3xl border-2 border-[#22AF85]/10 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-[#22AF85]/5 placeholder-[#22AF85]/30 transition-all"
                                      placeholder="Catatan pengerjaan atau request khusus customer...">{{ $lead->getAcceptedQuotation()->notes ?? '' }}</textarea>
                        </div>
                    </div>

                    {{-- Customer Data Verification --}}
                    <div>
                        <h4 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">02</span>
                            Data Shipment Verification
                        </h4>
                        <div class="p-8 bg-gray-50/50 rounded-[2.5rem] border-2 border-gray-100 space-y-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nama Penerima</label>
                                    <input type="text" name="customer_name" value="{{ $lead->customer_name }}" required
                                           class="w-full px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-black text-gray-900 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Telepon</label>
                                    <input type="text" name="customer_phone" value="{{ $lead->customer_phone }}" required
                                           class="w-full px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-black text-gray-900 bg-white shadow-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Alamat Lengkap</label>
                                <textarea name="customer_address" rows="2" required
                                          class="w-full px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm">{{ $lead->customer_address }}</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Kota</label>
                                    <input type="text" name="customer_city" value="{{ $lead->customer_city }}" required
                                           class="w-full px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Provinsi</label>
                                    <input type="text" name="customer_province" value="{{ $lead->customer_province }}" required
                                           class="w-full px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Multi-Item Service Mapping --}}
                @php
                    $acceptedQuotation = $lead->getAcceptedQuotation();
                @endphp
                @if($acceptedQuotation && count($acceptedQuotation->quotationItems ?? []) > 0)
                    <div class="pt-10 border-t border-gray-100">
                        <h4 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em] mb-8 flex items-center justify-between">
                            <span class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-[#22AF85] flex items-center justify-center text-white shadow-lg shadow-green-100 text-[10px]">03</span>
                                Service Configuration per Item
                            </span>
                            <span class="px-4 py-1.5 bg-[#22AF85]/10 text-[#22AF85] rounded-full text-[9px] font-black uppercase tracking-widest border border-[#22AF85]/20">
                                Active Quotation: #{{ $acceptedQuotation->quotation_number }}
                            </span>
                        </h4>

                        <div class="space-y-6">
                            @foreach($acceptedQuotation->quotationItems as $quotationItem)
                                <div class="bg-gray-50/50 rounded-[2.5rem] p-8 border-2 border-gray-100 transition-all duration-500 hover:border-[#22AF85]/20 hover:bg-white hover:shadow-2xl hover:shadow-gray-200/50 group">
                                    <div class="flex flex-col md:flex-row gap-8">
                                        {{-- Item Brief --}}
                                        <div class="md:w-1/3 space-y-4">
                                            <div class="flex items-start gap-4">
                                                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm border border-gray-100 group-hover:scale-110 transition-transform">
                                                    {{ $quotationItem->category_icon }}
                                                </div>
                                                <div>
                                                    <h5 class="text-lg font-black text-gray-900 leading-tight">Item #{{ $quotationItem->item_number }}</h5>
                                                    <p class="text-xs text-[#22AF85] font-black uppercase tracking-widest mt-1">{{ $quotationItem->category }}</p>
                                                </div>
                                            </div>
                                            <div class="p-5 bg-white rounded-2xl border border-gray-100 space-y-2 shadow-sm">
                                                <div class="flex justify-between text-[10px] font-bold">
                                                    <span class="text-gray-400 uppercase tracking-widest">Brand</span>
                                                    <span class="text-gray-900 font-black">{{ $quotationItem->shoe_brand ?: '-' }}</span>
                                                </div>
                                                <div class="flex justify-between text-[10px] font-bold">
                                                    <span class="text-gray-400 uppercase tracking-widest">Color</span>
                                                    <span class="text-gray-900 font-black">{{ $quotationItem->shoe_color ?: '-' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Service Selection --}}
                                        <div class="flex-1 space-y-6">
                                            <div class="relative">
                                                <input type="text" id="search-services-{{ $quotationItem->id }}" onkeyup="filterServices({{ $quotationItem->id }})"
                                                       placeholder="Cari layanan workshop..."
                                                       class="w-full px-12 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white placeholder-gray-300 transition-all shadow-sm">
                                                <svg class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                            </div>

                                            <div id="services-container-{{ $quotationItem->id }}" class="max-h-80 overflow-y-auto space-y-3 pr-2 custom-scrollbar">
                                                @foreach($services as $service)
                                                    <div class="service-wrapper-{{ $quotationItem->id }}-{{ $service->id }}">
                                                        <label class="service-item-{{ $quotationItem->id }} flex items-center justify-between p-5 bg-white border-2 border-gray-50 rounded-[1.5rem] hover:border-[#22AF85]/30 cursor-pointer transition-all active:scale-[0.98] shadow-sm select-none"
                                                               data-service-name="{{ strtolower($service->name) }}"
                                                               data-service-category="{{ strtolower($service->category ?? '') }}">
                                                            <div class="flex items-center gap-5">
                                                                <input type="checkbox" name="items[{{ $quotationItem->id }}][services][]" value="{{ $service->id }}"
                                                                       id="service-{{ $quotationItem->id }}-{{ $service->id }}"
                                                                       data-item-id="{{ $quotationItem->id }}" data-price="{{ $service->price }}" 
                                                                       data-name="{{ $service->name }}" data-category="{{ $service->category ?: '-' }}"
                                                                       onchange="toggleServiceDetail({{ $quotationItem->id }}, {{ $service->id }}); updateItemTotal({{ $quotationItem->id }}); updateSelectedServices({{ $quotationItem->id }});"
                                                                       class="service-checkbox w-6 h-6 rounded-lg border-2 border-gray-100 text-[#22AF85] focus:ring-0 transition-all">
                                                                <div>
                                                                    <p class="text-sm font-black text-gray-900">{{ $service->name }}</p>
                                                                    <p class="text-[9px] text-[#22AF85] font-black uppercase tracking-widest mt-1">{{ $service->category ?: 'General' }}</p>
                                                                </div>
                                                            </div>
                                                            <span class="text-sm font-black text-gray-900">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                                                        </label>
                                                        
                                                        <div id="detail-{{ $quotationItem->id }}-{{ $service->id }}" class="hidden mt-3 mx-4 p-6 bg-[#22AF85]/5 border-2 border-dashed border-[#22AF85]/20 rounded-[1.5rem]">
                                                            <label class="block text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em] mb-3">Detail Instruksi Jasa</label>
                                                            <textarea name="items[{{ $quotationItem->id }}][service_details][{{ $service->id }}]"
                                                                      id="detail-input-{{ $quotationItem->id }}-{{ $service->id }}"
                                                                      oninput="updateSelectedServices({{ $quotationItem->id }})"
                                                                      rows="2" class="w-full px-5 py-4 rounded-xl border-0 focus:ring-2 focus:ring-[#22AF85] text-xs font-bold text-gray-900 bg-white shadow-inner"
                                                                      placeholder="Contoh: Jahit Pola V / Recolor warna navy pekat..."></textarea>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Custom Service Engine --}}
                                            <div class="p-6 bg-gray-900 rounded-[2rem] border-2 border-gray-800 shadow-xl">
                                                <div class="flex justify-between items-center mb-4">
                                                    <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em]">Custom Workshop Service</span>
                                                    <button type="button" onclick="toggleCustomService({{ $quotationItem->id }})"
                                                            class="px-4 py-2 bg-[#22AF85] text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:opacity-90 transition shadow-lg shadow-green-900/20">
                                                        + Add New Service
                                                    </button>
                                                </div>
                                                
                                                <div id="custom-service-{{ $quotationItem->id }}" class="hidden space-y-4 pt-4 border-t border-gray-800">
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <input type="text" id="custom-name-{{ $quotationItem->id }}" placeholder="Nama Layanan"
                                                               class="px-5 py-3 rounded-xl border-0 bg-gray-800 text-white text-xs font-bold focus:ring-2 focus:ring-[#22AF85]">
                                                        <input type="number" id="custom-price-{{ $quotationItem->id }}" placeholder="Harga (IDR)"
                                                               class="px-5 py-3 rounded-xl border-0 bg-gray-800 text-white text-xs font-bold focus:ring-2 focus:ring-[#22AF85]">
                                                    </div>
                                                    <div>
                                                        <select id="custom-category-{{ $quotationItem->id }}"
                                                                class="w-full px-5 py-3 rounded-xl border-0 bg-gray-800 text-white text-xs font-bold focus:ring-2 focus:ring-[#22AF85]">
                                                            <option value="" disabled selected>Pilih Kategori Workshop</option>
                                                            @foreach($services->pluck('category')->unique()->filter() as $category)
                                                                <option value="{{ $category }}">{{ $category }}</option>
                                                            @endforeach
                                                            <option value="General">Lainnya / General</option>
                                                        </select>
                                                    </div>
                                                    <textarea id="custom-description-{{ $quotationItem->id }}" placeholder="Detail teknis..."
                                                              class="w-full px-5 py-3 rounded-xl border-0 bg-gray-800 text-white text-xs font-bold focus:ring-2 focus:ring-[#22AF85]" rows="2"></textarea>
                                                    <button type="button" onclick="addCustomService({{ $quotationItem->id }})"
                                                            class="w-full py-4 bg-[#FFC232] text-gray-900 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition">
                                                        Append to Task List
                                                    </button>
                                                </div>

                                                <div id="selected-summary-{{ $quotationItem->id }}" class="mt-6 space-y-2 hidden">
                                                    <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3">Service Pipeline:</p>
                                                    <div id="selected-list-{{ $quotationItem->id }}" class="space-y-2"></div>
                                                </div>

                                                <div class="mt-8 pt-6 border-t border-gray-800 flex justify-between items-center">
                                                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Subtotal Item #{{ $quotationItem->item_number }}</span>
                                                    <span class="text-2xl font-black text-white" id="item-subtotal-{{ $quotationItem->id }}">Rp 0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="items[{{ $quotationItem->id }}][quotation_item_id]" value="{{ $quotationItem->id }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Financial Finalization --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 pt-10 border-t border-gray-100">
                    {{-- Promo & Voucher --}}
                    <div class="p-8 bg-gray-50 rounded-[2.5rem] border-2 border-gray-100">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Redeem Boutique Voucher</label>
                        <div class="flex gap-4">
                            <input type="text" name="promo_code" id="promo-code-input" 
                                   class="flex-1 px-6 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-mono font-black text-gray-900 bg-white shadow-sm transition-all uppercase" 
                                   placeholder="COUPON CODE">
                            <button type="button" onclick="validatePromo()" id="btn-apply-promo" 
                                    class="px-10 py-4 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-gray-200">
                                Apply
                            </button>
                        </div>
                        <div id="promo-status" class="mt-4 hidden">
                            <p class="text-xs font-bold" id="promo-message"></p>
                        </div>
                    </div>

                    {{-- Final Pricing & DP --}}
                    <div class="space-y-6">
                        <div class="p-8 bg-gray-900 rounded-[2.5rem] shadow-2xl text-white">
                            <div class="flex justify-between items-center mb-6">
                                <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-widest">Grand Total Amount</span>
                                <span class="text-4xl font-black tracking-tighter" id="grand-total">Rp 0</span>
                            </div>
                            <div class="space-y-4 pt-6 border-t border-gray-800">
                                <div class="flex justify-between items-center">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Commitment Fee (DP) *</label>
                                    <div class="relative w-48">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-black text-gray-500">Rp</span>
                                        <input type="number" name="dp_amount" id="dp-amount-input" value="0" min="0" required
                                               class="w-full pl-12 pr-4 py-3 rounded-xl border-0 bg-gray-800 text-sm font-black text-white focus:ring-2 focus:ring-[#22AF85]">
                                    </div>
                                </div>
                                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest text-right">* Minimum 30% Commitment: <span class="text-[#22AF85]" id="dp-suggestion">Rp 0</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-6 pt-10">
                    <button type="button" onclick="closeSpkModal()" 
                            class="flex-1 px-10 py-6 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-xs rounded-[2rem] transition-all duration-300">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-[2] px-10 py-6 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-[0.2em] text-xs rounded-[2rem] shadow-[0_20px_40px_rgba(255,194,50,0.3)] transition-all duration-300 transform hover:-translate-y-2">
                        Generate Production Order (SPK)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
