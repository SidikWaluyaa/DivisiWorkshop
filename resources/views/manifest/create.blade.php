<x-app-layout>
<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('manifest.index') }}" class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-[#22AF85] transition-colors mb-4 group">
                <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar
            </a>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Buat <span class="text-[#22AF85]">Pengiriman Baru</span></h1>
            <p class="text-sm text-gray-500 mt-1 font-medium italic">Pilih batch sepatu yang siap dikirim ke Workshop Hijau</p>
        </div>

        <form action="{{ route('manifest.store') }}" method="POST" id="manifestForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Selection Area -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-white/50 backdrop-blur-sm">
                            <h2 class="text-lg font-bold text-gray-800">Antrian Siap Kirim</h2>
                            <div class="flex items-center space-x-4">
                                <span class="text-[10px] font-black px-2 py-1 bg-[#22AF85]/5 text-[#22AF85] rounded uppercase tracking-widest">READY TO DISPATCH</span>
                                <div class="flex items-center text-[11px] font-bold text-gray-400">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-[#22AF85] focus:ring-[#22AF85] w-4 h-4 mr-2 transition-all cursor-pointer">
                                    <label for="selectAll" class="cursor-pointer uppercase tracking-tighter">Pilih Semua</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="divide-y divide-gray-50 max-h-[600px] overflow-y-auto custom-scrollbar">
                            @forelse($orders as $order)
                            <div class="px-8 py-6 hover:bg-[#22AF85]/[0.02] transition-colors flex items-center group cursor-pointer" onclick="document.getElementById('check-{{ $order->id }}').click();">
                                <div class="mr-6">
                                    <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" id="check-{{ $order->id }}" class="order-checkbox rounded border-gray-300 text-[#22AF85] focus:ring-[#22AF85] w-5 h-5 transition-all cursor-pointer" onclick="event.stopPropagation();">
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-black text-gray-900 tracking-tight group-hover:text-[#22AF85] transition-colors">{{ $order->spk_number }}</p>
                                            <div class="text-[11px] text-gray-400 font-bold mt-0.5 uppercase tracking-tighter">{{ $order->customer_name }} • {{ $order->shoe_brand }}</div>
                                        </div>
                                        <div class="text-right">
                                            @if($order->priority === 'Prioritas' || $order->priority === 'Urgent' || $order->priority === 'Express')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-red-50 text-red-600 text-[10px] font-black border border-red-100 uppercase italic">
                                                    {{ $order->priority }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-gray-50 text-gray-500 text-[10px] font-black border border-gray-100 uppercase tracking-tighter">
                                                    {{ $order->priority }}
                                                </span>
                                            @endif
                                            <p class="text-[10px] text-gray-400 mt-1 font-bold">LUNAS: {{ $order->total_paid >= $order->total_transaksi ? 'YES' : 'DP' }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-[10px] text-gray-400 uppercase font-bold tracking-widest bg-gray-50 inline-block px-2 py-0.5 rounded">
                                        {{ $order->shoe_type }} • {{ $order->shoe_color }} • SZ {{ $order->shoe_size }}
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="px-8 py-20 text-center">
                                <div class="bg-gray-50/50 inline-flex p-8 rounded-full mb-4">
                                    <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900">Antrian Kosong</h3>
                                <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">Tidak ada item baru yang dipindahkan dari Pool Finance. Pastikan pembayaran sudah dikonfirmasi.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Action Panel -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 p-8 sticky top-8">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <span class="w-1.5 h-6 bg-[#22AF85] rounded-full mr-3"></span>
                            Shipment Config
                        </h2>

                        <div class="space-y-6">
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-[#22AF85]/10 text-[#22AF85] flex items-center justify-center font-bold text-xs mr-3">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Gudang Logistik Dispatcher</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Catatan Pengiriman</label>
                                <textarea name="notes" rows="4" class="w-full rounded-2xl border-gray-200 shadow-sm focus:border-[#22AF85] focus:ring-[#22AF85] transition-all text-sm placeholder:text-gray-300" placeholder="Contoh: Titip ke Driver Pak Andi, 2 Karung batch pagi..."></textarea>
                            </div>

                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Selected</span>
                                    <span id="selectedCount" class="text-xl font-black text-[#22AF85]">0</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1 mt-3 overflow-hidden">
                                    <div id="selectionProgress" class="bg-[#22AF85] h-1 rounded-full transition-all duration-300 shadow-[0_0_8px_rgba(34,175,133,0.5)]" style="width: 0%"></div>
                                </div>
                            </div>

                            <button type="submit" id="submitBtn" disabled class="w-full py-4 bg-[#FFC232] border border-transparent rounded-2xl font-black text-sm text-gray-900 uppercase tracking-[0.2em] hover:bg-[#e6af2e] focus:outline-none focus:ring-2 focus:ring-[#FFC232] focus:ring-offset-2 transition-all shadow-lg shadow-yellow-200/50 active:scale-95 disabled:bg-gray-100 disabled:text-gray-400 disabled:shadow-none disabled:cursor-not-allowed">
                                Generate Manifest
                            </button>
                            
                            <p class="text-[10px] text-gray-400 text-center font-bold tracking-tight px-4 leading-relaxed uppercase tracking-tighter">
                                <i class="fas fa-info-circle mr-1 text-[#22AF85]"></i> Item otomatis berubah menjadi <span class="text-[#22AF85]">OTW WORKSHOP</span> setelah manifest dibuat.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f9fafb; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.order-checkbox');
        const selectAll = document.getElementById('selectAll');
        const selectedCount = document.getElementById('selectedCount');
        const submitBtn = document.getElementById('submitBtn');
        const progress = document.getElementById('selectionProgress');
        const totalItems = checkboxes.length;

        function updateSummary() {
            const checked = document.querySelectorAll('.order-checkbox:checked').length;
            selectedCount.textContent = checked;
            submitBtn.disabled = checked === 0;
            
            const percentage = totalItems === 0 ? 0 : (checked / totalItems) * 100;
            progress.style.width = percentage + '%';
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSummary);
        });

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
                updateSummary();
            });
        }
    });
</script>
@endpush
</x-app-layout>
