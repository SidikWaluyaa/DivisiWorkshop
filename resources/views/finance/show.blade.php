<x-app-layout>
    {{-- Print Stylesheet --}}
    <style>
        @media print {
            /* Hide unnecessary elements */
            .print\:hidden,
            nav,
            footer,
            button,
            .no-print {
                display: none !important;
            }
            
            /* Reset page margins */
            @page {
                margin: 1cm;
                size: A4;
            }
            
            body {
                background: white !important;
                color: black !important;
            }
            
            /* Header styling for print */
            .print-header {
                background: linear-gradient(to right, #14B8A6, #F97316) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 20px;
                margin-bottom: 20px;
                border-radius: 8px;
            }
            
            /* Card styling for print */
            .print-card {
                border: 2px solid #e5e7eb;
                padding: 15px;
                margin-bottom: 15px;
                page-break-inside: avoid;
                border-radius: 8px;
            }
            
            /* Timeline for print */
            .print-timeline-dot {
                width: 12px;
                height: 12px;
                background: #14B8A6;
                border-radius: 50%;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .print-timeline-line {
                width: 2px;
                background: #d1d5db;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Ensure colors print */
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Page breaks */
            .page-break-before {
                page-break-before: always;
            }
            
            .page-break-after {
                page-break-after: always;
            }
            
            .no-page-break {
                page-break-inside: avoid;
            }
        }
    </style>
    
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-teal-50/30 to-orange-50/20 pb-12">
        {{-- Premium Header --}}
        <div class="bg-gradient-to-r from-teal-600 via-teal-500 to-orange-500 shadow-2xl">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('finance.index') }}" class="p-2 bg-white/20 backdrop-blur-sm rounded-xl hover:bg-white/30 transition-all print:hidden">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-3xl font-black text-white">{{ $order->spk_number }}</h1>
                            <p class="text-teal-50 text-sm mt-1">Detail Pembayaran & Tagihan</p>
                        </div>
                    </div>
                    
                    {{-- Action Buttons & Status --}}
                    <div class="flex items-center gap-3">
                        {{-- Print Button --}}
                        <button onclick="window.print()" 
                                class="print:hidden inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl text-white font-bold text-sm transition-all shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Print Invoice
                        </button>
                        
                        {{-- Export Button --}}
                        <a href="{{ route('finance.export-payment-history', $order->id) }}" 
                           class="print:hidden inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl text-white font-bold text-sm transition-all shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export PDF
                        </a>
                        
                        {{-- Status Badges --}}
                        <div class="flex flex-col items-end gap-2">
                            <span class="px-4 py-2 bg-white/90 backdrop-blur-sm rounded-xl text-gray-800 font-bold text-sm shadow-lg">
                                {{ str_replace('_', ' ', $order->status->value) }}
                            </span>
                            @if($order->sisa_tagihan <= 0)
                                <span class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl text-white font-black text-sm shadow-lg flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    LUNAS
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 -mt-6">
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {{-- Total Tagihan --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-teal-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Tagihan</p>
                            <h3 class="text-3xl font-black text-gray-900 mt-2">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</h3>
                        </div>
                        <div class="p-4 bg-teal-50 rounded-2xl">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total Terbayar --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sudah Dibayar</p>
                            <h3 class="text-3xl font-black text-green-600 mt-2">Rp {{ number_format($order->total_paid, 0, ',', '.') }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $order->payments->count() }} transaksi</p>
                        </div>
                        <div class="p-4 bg-green-50 rounded-2xl">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Sisa Tagihan --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 {{ $order->sisa_tagihan > 0 ? 'border-red-500' : 'border-gray-300' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sisa Tagihan</p>
                            <h3 class="text-3xl font-black {{ $order->sisa_tagihan > 0 ? 'text-red-600' : 'text-gray-400' }} mt-2">
                                Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="p-4 {{ $order->sisa_tagihan > 0 ? 'bg-red-50' : 'bg-gray-50' }} rounded-2xl">
                            <svg class="w-8 h-8 {{ $order->sisa_tagihan > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Customer Info Card --}}
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
                <h3 class="text-lg font-black text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Informasi Customer & Pengiriman
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Pelanggan</p>
                            <p class="text-gray-900 font-black text-lg">{{ $order->customer_name }}</p>
                            <p class="text-xs text-gray-500 font-medium">{{ $order->customer_phone }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Item Sepatu</p>
                            <p class="text-gray-900 font-bold">{{ $order->shoe_brand }} {{ $order->shoe_size }}</p>
                            <p class="text-xs text-gray-500">{{ $order->shoe_color }}</p>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Alamat Lengkap Tujuan</p>
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                            <div class="text-gray-800 font-bold mb-2 leading-relaxed">
                                {{ $order->customer->address ?? ($order->customer_address ?? '-') }}
                            </div>
                            <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-[11px] uppercase tracking-wider font-semibold text-blue-600/80">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
                                    <span>Kel: {{ $order->customer->village ?? '-' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
                                    <span>Kec: {{ $order->customer->district ?? '-' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
                                    <span>Kota: {{ $order->customer->city ?? '-' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
                                    <span>Prov: {{ $order->customer->province ?? '-' }}</span>
                                </div>
                                <div class="col-span-2 pt-1 border-t border-blue-100/50 flex items-center gap-2 mt-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span>Kode Pos: {{ $order->customer->postal_code ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- LEFT: Bill Details --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
                            <h4 class="font-black text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Rincian Biaya
                            </h4>
                        </div>
                        <div class="p-6">
                            <table class="w-full">
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($order->workOrderServices as $detail)
                                        <tr>
                                            <td class="py-3 text-gray-700">
                                                {{ $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'Layanan Hapus') }}
                                            </td>
                                            <td class="py-3 text-right font-bold text-gray-900">Rp {{ number_format($detail->cost, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    @if($order->cost_oto + $order->cost_add_service > 0)
                                    <tr>
                                        <td class="py-3 text-gray-700">Biaya OTO / Tambahan</td>
                                        <td class="py-3 text-right font-bold text-gray-900">Rp {{ number_format($order->cost_oto + $order->cost_add_service, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="group">
                                        <td class="py-3 text-gray-700 flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span>Ongkos Kirim</span>
                                                <button onclick="editShipping()" class="p-1 text-teal-600 hover:bg-teal-50 rounded transition-colors opacity-0 group-hover:opacity-100 print:hidden" title="Edit Ongkir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="text-[10px] text-gray-400 font-medium uppercase tracking-tight" id="display-shipping-zone">
                                                {{ $order->shipping_zone ? ($order->shipping_zone . ' (' . ($order->shipping_type ?? 'Ekspedisi') . ')') : '' }}
                                            </div>
                                        </td>
                                        <td class="py-3 text-right font-bold text-gray-900" id="display-shipping">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                    </tr>
                                    @if($order->discount > 0)
                                    <tr>
                                        <td class="py-3 text-gray-700">Potongan / Diskon</td>
                                        <td class="py-3 text-right font-bold text-red-600">- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot class="border-t-2 border-gray-200">
                                    <tr>
                                        <td class="py-4 font-black text-gray-900 text-lg">TOTAL</td>
                                        <td class="py-4 text-right font-black text-teal-600 text-xl" id="display-total-transaksi">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            @if($order->status === \App\Enums\WorkOrderStatus::WAITING_PAYMENT)
                                <div class="mt-6 pt-6 border-t">
                                    <button onclick="confirmMove('{{ $order->id }}')" class="w-full bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-black py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all">
                                        🚀 Lanjut ke Workshop (Preparation)
                                    </button>
                                    <p class="text-xs text-center text-gray-500 mt-2">Klik jika DP sudah diterima atau order siap dikerjakan</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Payment Form & History --}}
                <div class="space-y-6">
                    {{-- Payment Form --}}
                    @if($order->sisa_tagihan > 0)
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-teal-200">
                        <div class="bg-gradient-to-r from-teal-500 to-teal-600 px-6 py-4">
                            <h4 class="font-black text-white flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Input Pembayaran Baru
                            </h4>
                        </div>
                        <form action="{{ route('finance.payment.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="p-6" onsubmit="return validatePayment(event)">
                            @csrf
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Tipe Pembayaran</label>
                                    <select name="payment_type" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm">
                                        <option value="BEFORE">DP / Awal</option>
                                        <option value="AFTER">Pelunasan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Metode Bayar</label>
                                    <select name="payment_method" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm">
                                        <option value="Cash">Cash</option>
                                        <option value="Transfer">Transfer</option>
                                        <option value="QRIS">QRIS</option>
                                        <option value="Debit">Debit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-700 mb-2">Jumlah Bayar *</label>
                                <input type="number" 
                                       name="amount_total" 
                                       id="payment_amount"
                                       max="{{ $order->sisa_tagihan }}"
                                       required 
                                       class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm"
                                       placeholder="Rp">
                                <p class="text-xs text-gray-500 mt-1">Maksimal: Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Bayar</label>
                                <input type="datetime-local" name="paid_at" value="{{ date('Y-m-d\TH:i') }}" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm">
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-700 mb-2">Upload Bukti Transfer</label>
                                <input type="file" 
                                       name="proof_image" 
                                       accept="image/jpeg,image/png,image/jpg"
                                       onchange="previewImage(event)"
                                       class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm">
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 5MB</p>
                                <div id="image_preview" class="mt-2 hidden">
                                    <img id="preview_img" src="" alt="Preview" class="w-full h-48 object-cover rounded-lg border-2 border-gray-200">
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-700 mb-2">Catatan (Opsional)</label>
                                <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm" placeholder="Catatan tambahan..."></textarea>
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white font-black py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all">
                                💰 Simpan Pembayaran
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-8 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black text-green-800 mb-2">Pembayaran Lunas!</h3>
                        <p class="text-green-600">Tidak ada tagihan tersisa untuk order ini.</p>
                    </div>
                    @endif

                    {{-- Payment History Timeline --}}
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-teal-50 via-teal-100/50 to-orange-50 px-6 py-4 border-b border-teal-200">
                            <h4 class="font-black text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                📅 Riwayat Pembayaran
                            </h4>
                        </div>
                        <div class="p-6">
                            @forelse($order->payments as $payment)
                                <div class="flex gap-4 {{ !$loop->last ? 'pb-6 mb-6' : '' }}">
                                    {{-- Timeline Connector --}}
                                    <div class="flex flex-col items-center">
                                        {{-- Dot --}}
                                        <div class="w-4 h-4 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 shadow-lg ring-4 ring-teal-100"></div>
                                        {{-- Line --}}
                                        @if(!$loop->last)
                                            <div class="w-0.5 flex-1 bg-gradient-to-b from-teal-300 to-orange-300 mt-2"></div>
                                        @endif
                                    </div>
                                    
                                    {{-- Payment Card --}}
                                    <div class="flex-1 bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h5 class="font-black text-gray-900 text-lg">
                                                        {{ $payment->type === 'BEFORE' ? '💵 DP / Pembayaran Awal' : '✅ Pelunasan' }}
                                                    </h5>
                                                    <span class="px-2 py-0.5 bg-teal-100 text-teal-700 text-[10px] font-bold rounded-full uppercase">
                                                        {{ $payment->payment_method }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ $payment->paid_at->format('d M Y, H:i') }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-black text-teal-600 text-2xl">Rp {{ number_format($payment->amount_total, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($payment->notes)
                                            <div class="bg-amber-50 border-l-4 border-amber-400 p-3 rounded-r-lg mb-3">
                                                <p class="text-sm text-amber-800 italic flex items-start gap-2">
                                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                    </svg>
                                                    "{{ $payment->notes }}"
                                                </p>
                                            </div>
                                        @endif
                                        
                                        <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                            <div class="flex items-center gap-3">
                                                @if($payment->proof_image)
                                                    <button onclick="showProofLightbox('{{ asset($payment->proof_image) }}')" 
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        📷 Lihat Bukti
                                                    </button>
                                                @else
                                                    <span class="text-xs text-gray-400 italic">Tanpa bukti transfer</span>
                                                @endif
                                            </div>
                                            <p class="text-[10px] text-gray-400 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                PIC: {{ $payment->pic->name ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">Belum ada pembayaran</p>
                                    <p class="text-sm text-gray-400 mt-1">Silakan input pembayaran pertama</p>
                                </div>
                            @endforelse
                        </div>
                        @if($order->payments->count() > 0)
                        <div class="bg-gradient-to-r from-teal-50 to-green-50 px-6 py-4 border-t border-teal-200 flex justify-between items-center">
                            <span class="font-black text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Total Terbayar
                            </span>
                            <span class="font-black text-teal-600 text-2xl">Rp {{ number_format($order->payments->sum('amount_total'), 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Ongkir --}}
    <div x-data="{ 
            show: false, 
            loading: false, 
            type: '{{ $order->shipping_type ?? 'Ekspedisi' }}',
            zone: '{{ $order->shipping_zone ?? 'Custom' }}',
            cost: {{ $order->shipping_cost ?? 0 }},
            zones: {
                'Self-Pickup': 0,
                'Zona 1: Dalam Kota': 15000,
                'Zona 2: Luar Kota': 25000,
                'Zona 3: Luar Provinsi': 45000,
                'Custom': {{ $order->shipping_cost ?? 0 }}
            },
            updateCost() {
                if (this.zone !== 'Custom') {
                    this.cost = this.zones[this.zone];
                }
            }
         }" 
         x-init="$watch('zone', value => updateCost())"
         x-show="show" 
         @open-shipping-modal.window="show = true"
         @close-shipping-modal.window="show = false"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="show = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-teal-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-black text-gray-900">Update Pengiriman</h3>
                            
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Jenis Kurir</label>
                                    <select x-model="type" class="w-full border-gray-300 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-sm">
                                        <option value="Ekspedisi">Ekspedisi (JNE, J&T, Sicepat)</option>
                                        <option value="Internal">Kurir Internal / Ojol</option>
                                        <option value="Self-Pickup">Ambil Sendiri / Toko</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Zona Pengiriman</label>
                                    <select x-model="zone" class="w-full border-gray-300 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-sm">
                                        <template x-for="(price, name) in zones">
                                            <option :value="name" x-text="name"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Nominal Ongkir (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                        <input type="number" x-model="cost" 
                                               class="w-full pl-12 border-gray-300 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-lg font-black"
                                               placeholder="0">
                                    </div>
                                    <p class="mt-1 text-[10px] text-gray-500 italic" x-show="zone !== 'Custom'">* Nominal otomatis berdasarkan zona</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" @click="saveShipping(cost, type, zone)" :disabled="loading"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-bold text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:w-auto sm:text-sm disabled:opacity-50">
                        <span x-show="!loading">Simpan Perubahan</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                    <button type="button" @click="show = false"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Lightbox Modal for Proof Images --}}
    <div x-data="{ showLightbox: false, lightboxImage: '' }" 
         x-show="showLightbox" 
         @click="showLightbox = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm"
         style="display: none;"
         x-transition>
        <div class="relative max-w-4xl max-h-screen p-4">
            <button @click="showLightbox = false" class="absolute top-2 right-2 p-2 bg-white/20 hover:bg-white/30 rounded-full text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <img :src="lightboxImage" class="max-w-full max-h-screen rounded-lg shadow-2xl" @click.stop>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Image Preview Function
        function previewImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar!',
                    text: 'Ukuran file maksimal 5MB',
                    confirmButtonColor: '#14B8A6'
                });
                event.target.value = '';
                return;
            }
            
            // Validate file type
            if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Tidak Valid!',
                    text: 'Hanya file JPG dan PNG yang diperbolehkan',
                    confirmButtonColor: '#14B8A6'
                });
                event.target.value = '';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview_img').src = e.target.result;
                document.getElementById('image_preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        // Payment Validation with SweetAlert
        function validatePayment(event) {
            event.preventDefault();
            
            const amount = parseInt(document.getElementById('payment_amount').value);
            const maxAmount = {{ $order->sisa_tagihan }};
            
            if (!amount || amount <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Jumlah Tidak Valid!',
                    text: 'Jumlah pembayaran harus lebih dari 0',
                    confirmButtonColor: '#14B8A6'
                });
                return false;
            }
            
            if (amount > maxAmount) {
                Swal.fire({
                    icon: 'error',
                    title: 'Melebihi Sisa Tagihan!',
                    html: `Jumlah pembayaran tidak boleh melebihi sisa tagihan<br><strong>Maksimal: Rp ${maxAmount.toLocaleString('id-ID')}</strong>`,
                    confirmButtonColor: '#14B8A6'
                });
                return false;
            }
            
            // Confirmation
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `Simpan pembayaran sebesar<br><strong class="text-2xl text-teal-600">Rp ${amount.toLocaleString('id-ID')}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#14B8A6',
                cancelButtonColor: '#6B7280',
                confirmButtonText: '✓ Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
            
            return false;
        }

        // Show Lightbox for Proof Image
        function showProofImage(imagePath) {
            Alpine.store('lightbox', {
                show: true,
                image: imagePath
            });
        }

        // Show Proof Lightbox (Alternative method using Alpine directly)
        function showProofLightbox(imagePath) {
            // Find the lightbox element and trigger it
            const lightboxDiv = document.querySelector('[x-data*="showLightbox"]');
            if (lightboxDiv) {
                Alpine.$data(lightboxDiv).showLightbox = true;
                Alpine.$data(lightboxDiv).lightboxImage = imagePath;
            }
        }

        // Confirm Move to Workshop
        function confirmMove(orderId) {
            Swal.fire({
                title: 'Pindahkan ke Workshop?',
                text: 'Order akan dipindahkan ke proses Preparation',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#14B8A6',
                cancelButtonColor: '#6B7280',
                confirmButtonText: '✓ Ya, Pindahkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/finance/${orderId}/update-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ action: 'move_to_prep' })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonColor: '#14B8A6'
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message,
                                confirmButtonColor: '#14B8A6'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memproses request',
                            confirmButtonColor: '#14B8A6'
                        });
                    });
                }
            });
        }

        function editShipping() {
            window.dispatchEvent(new CustomEvent('open-shipping-modal'));
        }

        function saveShipping(amount, type, zone) {
            // Access Alpine data from the modal element
            const modalEl = document.querySelector('[x-data*="show: false"]');
            const alpineData = Alpine.$data(modalEl);
            
            alpineData.loading = true;

            fetch("{{ route('finance.shipping.update', $order->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    shipping_cost: amount,
                    shipping_type: type,
                    shipping_zone: zone
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alpineData.show = false;
                    
                    // Update UI elements
                    document.getElementById('display-shipping').innerText = 'Rp ' + data.new_shipping;
                    document.getElementById('display-shipping-zone').innerText = zone + ' (' + type + ')';
                    document.getElementById('display-total-transaksi').innerText = 'Rp ' + data.new_total;
                    
                    // Update summary cards if they exist in the DOM
                    // Total Tagihan card
                    const tagihanH3 = document.querySelector('h3.text-3xl.font-black.text-gray-900.mt-2');
                    if(tagihanH3) tagihanH3.innerText = 'Rp ' + data.new_total;
                    
                    // Sisa Tagihan card
                    const sisaTagihanH3 = document.querySelector('h3.text-3xl.font-black.text-red-600.mt-2') || document.querySelector('h3.text-3xl.font-black.text-gray-400.mt-2');
                    if(sisaTagihanH3) sisaTagihanH3.innerText = 'Rp ' + data.new_sisa;

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            })
            .finally(() => {
                alpineData.loading = false;
            });
        }

        // Success/Error Messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session("error") }}',
                confirmButtonColor: '#14B8A6'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                html: '<ul class="text-left">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                confirmButtonColor: '#14B8A6'
            });
        @endif
    </script>
</x-app-layout>

