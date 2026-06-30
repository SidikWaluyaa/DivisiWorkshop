<x-app-layout>
    <div class="min-h-screen bg-gray-50/50">
        {{-- Elite Header --}}
        <div class="bg-white shadow-xl border-b border-gray-100 sticky top-0 z-30 backdrop-blur-md bg-white/90">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    {{-- Left: Icon & Title --}}
                    <div class="flex items-center gap-5">
                        <div class="p-3.5 bg-gradient-to-br from-rose-600 to-red-800 rounded-2xl shadow-rose-200/50 shadow-lg border border-rose-600/20 transform transition-transform hover:rotate-3 group">
                            <svg class="w-8 h-8 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 tracking-tight leading-none italic">Laporan Transaksi Batal</h1>
                            <p class="text-gray-500 text-xs mt-1.5 font-black uppercase tracking-widest italic opacity-70">Analitik Kerugian Keuangan & Operasional</p>
                        </div>
                    </div>
                    
                    {{-- Right: Back Action --}}
                    <div class="flex items-center gap-4">
                        <a href="{{ route('finance.index') }}" class="group relative inline-flex items-center gap-2.5 px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] italic shadow-xl transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali ke Transaksi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loss Intelligence Dashboard Section --}}
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {{-- Stat 1: Total Kerugian --}}
                <div class="bg-gray-900 rounded-[2rem] p-6 shadow-2xl shadow-rose-900/10 group hover:scale-[1.02] transition-all duration-500 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-32 h-32 bg-red-500/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-[10px] font-black text-red-400 uppercase tracking-widest italic">Total Kerugian Keuangan</div>
                            <div class="p-2 bg-white/10 rounded-xl text-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-white tracking-tighter italic tabular-nums">Rp {{ number_format($stats['total_lost'] ?? 0, 0, ',', '.') }}</div>
                        <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-1 italic">Estimasi nilai transaksi terhenti</div>
                    </div>
                </div>

                {{-- Stat 2: Total SPK Batal --}}
                <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-xl shadow-gray-100/50 group hover:border-red-200 transition-all duration-500">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic group-hover:text-red-600 transition-colors">Total SPK Batal</div>
                        <div class="p-2 bg-red-50 rounded-xl text-red-500 group-hover:bg-red-500 group-hover:text-white transition-all duration-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 tracking-tighter italic tabular-nums">{{ $stats['cancelled_count'] ?? 0 }} SPK</div>
                    <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-1 italic">Total unit dibatalkan admin</div>
                </div>

                {{-- Stat 3: Rata-rata Kerugian --}}
                <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-xl shadow-gray-100/50 group hover:border-red-200 transition-all duration-500">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic group-hover:text-red-600 transition-colors">Rata-rata Kerugian</div>
                        <div class="p-2 bg-red-50 rounded-xl text-red-500 group-hover:bg-red-500 group-hover:text-white transition-all duration-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 tracking-tighter italic tabular-nums">Rp {{ number_format($stats['average_lost'] ?? 0, 0, ',', '.') }}</div>
                    <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-1 italic">Nilai rata-rata kerugian per SPK</div>
                </div>

                {{-- Stat 4: Rasio Pembatalan --}}
                <div class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-xl shadow-gray-100/50 group hover:border-red-200 transition-all duration-500">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic group-hover:text-red-600 transition-colors">Rasio Pembatalan</div>
                        <div class="p-2 bg-red-50 rounded-xl text-red-500 group-hover:bg-red-500 group-hover:text-white transition-all duration-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 tracking-tighter italic tabular-nums">{{ number_format($stats['cancellation_rate'] ?? 0, 2) }}%</div>
                    <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-1 italic">Tingkat pembatalan seluruh order</div>
                </div>
            </div>

            {{-- Filter & Search Panel --}}
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xl shadow-gray-100/50 mb-8">
                <form action="{{ route('finance.cancelled') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                    {{-- Search keyword --}}
                    <div class="flex-1 w-full">
                        <label for="search" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Cari Transaksi Batal</label>
                        <div class="relative">
                            <input type="text" name="search" id="search" value="{{ $search }}"
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:border-red-500 focus:ring-red-500/20"
                                placeholder="Cari No SPK, Nama, Telepon, atau Alasan Batal...">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Date From --}}
                    <div class="w-full md:w-48">
                        <label for="date_from" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Tanggal Dari</label>
                        <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:border-red-500 focus:ring-red-500/20">
                    </div>

                    {{-- Date To --}}
                    <div class="w-full md:w-48">
                        <label for="date_to" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Tanggal Sampai</label>
                        <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:border-red-500 focus:ring-red-500/20">
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-2 w-full md:w-auto">
                        <button type="submit" class="flex-1 md:flex-none px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-xs font-black uppercase tracking-widest italic transition-all hover:shadow-lg">
                            Filter
                        </button>
                        @if($search || $dateFrom || $dateTo)
                            <a href="{{ route('finance.cancelled') }}" class="flex-1 md:flex-none px-6 py-3 bg-gray-100 hover:bg-gray-250 text-gray-500 rounded-xl text-xs font-black uppercase tracking-widest italic transition-all text-center">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Main Data List --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100">
                <div class="p-1">
                    @if($orders->isEmpty())
                        <div class="text-center py-36 relative overflow-hidden">
                            <div class="absolute -top-12 -left-12 w-64 h-64 bg-emerald-50 rounded-full blur-3xl opacity-50"></div>
                            <div class="relative z-10 flex flex-col items-center justify-center">
                                <div class="w-24 h-24 bg-white rounded-[2.5rem] shadow-2xl border border-gray-50 flex items-center justify-center text-4xl mb-6 group hover:scale-110 transition-transform duration-500">
                                    🛡️
                                </div>
                                <span class="font-black text-gray-900 text-2xl uppercase tracking-tighter italic">Operasional Aman</span>
                                <p class="text-gray-400 text-xs mt-3 max-w-xs font-black uppercase tracking-widest leading-loose italic opacity-60 text-center">Tidak ada transaksi batal atau kerugian keuangan terdeteksi pada filter saat ini.</p>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto overflow-hidden rounded-[2rem]">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-900/5 border-b border-gray-100">
                                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] italic">No SPK & Customer</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Detail Sepatu</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-right italic">Estimasi Kerugian</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-right italic">Tanggal Batal</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Alasan Pembatalan</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] text-center italic">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50/50">
                                    @foreach($orders as $order)
                                    <tr class="hover:bg-rose-50/20 transition-all duration-500 group relative">
                                        <td class="px-8 py-6 relative">
                                            <div class="absolute left-0 top-0 w-1 h-full bg-red-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                            <div class="flex flex-col gap-1">
                                                <div class="font-black text-gray-950 text-base leading-none tracking-tight group-hover:text-red-600 transition-colors">
                                                    {{ $order->spk_number }}
                                                </div>
                                                <div class="font-bold text-gray-600 uppercase tracking-tight text-[11px] leading-none italic mt-1">
                                                    {{ $order->customer_name }}
                                                </div>
                                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider italic">
                                                    {{ $order->customer_phone }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col">
                                                <div class="text-xs font-black text-gray-800 uppercase tracking-tight">
                                                    {{ $order->shoe_brand }} {{ $order->shoe_type }}
                                                </div>
                                                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                                    Warna: {{ $order->shoe_color ?? '-' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <div class="font-black text-red-600 text-base tracking-tight italic">
                                                Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <div class="text-xs font-bold text-gray-900 tracking-tight">{{ $order->updated_at ? $order->updated_at->format('d M Y') : '-' }}</div>
                                            <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5 italic">
                                                {{ $order->updated_at ? $order->updated_at->diffForHumans() : '' }}
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 max-w-xs">
                                            <div class="text-xs text-gray-500 font-medium line-clamp-2" title="{{ $order->reception_rejection_reason }}">
                                                {{ $order->reception_rejection_reason ?? 'Tidak dicantumkan alasan pembatalan.' }}
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-gray-300 rounded-xl text-[10px] font-black uppercase tracking-wider italic text-gray-500 transition-all hover:scale-105 active:scale-95 shadow-sm">
                                                    Detail SPK
                                                </a>
                                                @if(auth()->user()->role === 'admin')
                                                    <button type="button" 
                                                            onclick="restoreSpk('{{ $order->id }}', '{{ $order->spk_number }}')"
                                                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-emerald-100 text-emerald-600 hover:bg-emerald-50 rounded-xl text-[10px] font-black uppercase tracking-wider italic transition-all hover:scale-105 active:scale-95 shadow-sm">
                                                        Pulihkan
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($orders->hasPages())
                        <div class="mt-4 px-8 py-5 border-t border-gray-100 bg-gray-50/50">
                            {{ $orders->links() }}
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function restoreSpk(orderId, spkNumber) {
            if (typeof Swal === 'undefined') {
                if (!confirm(`Apakah Anda yakin ingin memulihkan SPK ${spkNumber} kembali ke status aktif sebelumnya?`)) {
                    return;
                }
            } else {
                const result = await Swal.fire({
                    title: 'Pulihkan SPK?',
                    text: `Apakah Anda yakin ingin memulihkan SPK ${spkNumber} kembali ke status aktif sebelum dibatalkan? SPK ini akan dilepas dari invoice lama (menjadi loose SPK) demi keamanan pembukuan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Pulihkan!',
                    cancelButtonText: 'Batal'
                });

                if (!result.isConfirmed) {
                    return;
                }
            }

            try {
                // Show loading if Swal exists
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memulihkan status SPK...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }

                const res = await fetch(`/admin/orders/${orderId}/restore`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await res.json();
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(data.message);
                    }
                    location.reload();
                } else {
                    throw new Error(data.message || 'Gagal memulihkan SPK');
                }
            } catch (e) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: e.message
                    });
                } else {
                    alert(e.message);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
