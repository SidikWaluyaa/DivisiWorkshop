@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endpush

@push('styles')
    <style>
        /* Complete Flatpickr Overrides for ultra-premium Shoeworkshop aesthetics */
        .flatpickr-calendar {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(24px) !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            border-radius: 1.5rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.02) !important;
            padding: 12px 10px !important;
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif !important;
            width: 325px !important;
            box-sizing: border-box !important;
            animation: fpFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .flatpickr-calendar.arrowTop:before,
        .flatpickr-calendar.arrowTop:after {
            border-bottom-color: rgba(255, 255, 255, 0.98) !important;
        }
        .flatpickr-days, .dayContainer {
            width: 304px !important;
            min-width: 304px !important;
            max-width: 304px !important;
            box-sizing: border-box !important;
        }
        @keyframes fpFadeIn {
            from { opacity: 0; transform: scale(0.96) translateY(8px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .flatpickr-months {
            align-items: center !important;
            margin-bottom: 10px !important;
            padding: 0 4px !important;
        }
        .flatpickr-months .flatpickr-prev-month, 
        .flatpickr-months .flatpickr-next-month {
            top: 14px !important;
            padding: 6px 8px !important;
            border-radius: 10px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: #334155 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
        }
        .flatpickr-months .flatpickr-prev-month:hover, 
        .flatpickr-months .flatpickr-next-month:hover {
            background: #f1f5f9 !important;
            color: #22AF85 !important;
            border-color: #22AF85 !important;
            transform: scale(1.05);
        }
        .flatpickr-current-month {
            font-size: 14px !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            padding: 4px 0 0 0 !important;
        }
        .flatpickr-current-month select {
            font-weight: 800 !important;
            color: #0f172a !important;
        }
        .flatpickr-current-month .numInputWrapper {
            font-weight: 800 !important;
            color: #0f172a !important;
        }
        .flatpickr-weekdays {
            margin-bottom: 6px !important;
        }
        .flatpickr-weekday {
            font-weight: 800 !important;
            font-size: 10px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            color: #94a3b8 !important;
        }
        .flatpickr-day {
            border-radius: 10px !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            color: #1e293b !important;
            margin: 2px 0 !important;
            height: 38px !important;
            line-height: 38px !important;
            max-width: 42px !important;
            transition: all 0.15s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .flatpickr-day:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
        }
        .flatpickr-day.today {
            border: 2px solid #F59E0B !important;
            color: #0f172a !important;
        }
        .flatpickr-day.selected, 
        .flatpickr-day.startRange, 
        .flatpickr-day.endRange {
            background: linear-gradient(135deg, #22AF85 0%, #15803D 100%) !important;
            border-color: transparent !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(34, 175, 133, 0.35) !important;
            border-radius: 10px !important;
            font-weight: 800 !important;
        }
        .flatpickr-day.inRange {
            background: #E6F7F2 !important;
            border-color: #E6F7F2 !important;
            color: #15803D !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        .flatpickr-day.prevMonthDay, 
        .flatpickr-day.nextMonthDay {
            color: #cbd5e1 !important;
            opacity: 0.45 !important;
        }
    </style>
@endpush

<x-app-layout>
    <div class="min-h-screen bg-gray-50/50" x-data="{
        isRefundModalOpen: false,
        activeOrderId: null,
        activeSpkNumber: '',
        refundAmount: 0,
        refundNotes: '',
        paidSoFar: 0,
        isSaving: false,

        openRefundModal(orderId, spkNumber, currentRefund, currentNotes, paid) {
            this.activeOrderId = orderId;
            this.activeSpkNumber = spkNumber;
            this.refundAmount = Number(currentRefund) || 0;
            this.refundNotes = currentNotes || '';
            this.paidSoFar = Number(paid) || 0;
            this.isRefundModalOpen = true;
        },

        closeRefundModal() {
            this.isRefundModalOpen = false;
        },

        async submitRefund() {
            if (!this.activeOrderId) return;
            this.isSaving = true;

            try {
                const res = await fetch(`/finance/cancelled-orders/${this.activeOrderId}/update-refund`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({
                        refund_amount: this.refundAmount,
                        refund_notes: this.refundNotes
                    })
                });

                const data = await res.json();
                if (data.success) {
                    this.closeRefundModal();
                    if (typeof Swal !== 'undefined') {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        alert(data.message);
                    }
                    location.reload();
                } else {
                    throw new Error(data.message || 'Gagal memperbarui refund');
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
            } finally {
                this.isSaving = false;
            }
        }
    }">
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
                            <p class="text-gray-500 text-xs mt-1.5 font-black uppercase tracking-widest italic opacity-70">Analitik Kerugian Keuangan, Operasional & Refund</p>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
                {{-- Stat 1: Total Kerugian --}}
                <div class="bg-gray-900 rounded-[2rem] p-6 shadow-2xl shadow-rose-900/10 group hover:scale-[1.02] transition-all duration-500 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-32 h-32 bg-red-500/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-[10px] font-black text-red-400 uppercase tracking-widest italic">Total Kerugian Transaksi</div>
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

                {{-- Stat 3: Total Dana Refund --}}
                <div class="bg-white rounded-[2rem] p-6 border border-amber-100/80 shadow-xl shadow-amber-500/5 group hover:border-amber-300 transition-all duration-500 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-[10px] font-black text-amber-600 uppercase tracking-widest italic group-hover:text-amber-700 transition-colors">Total Dana Refund</div>
                            <div class="p-2 bg-amber-50 rounded-xl text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-all duration-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-amber-600 tracking-tighter italic tabular-nums">Rp {{ number_format($stats['total_refund'] ?? 0, 0, ',', '.') }}</div>
                        <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-1 italic">Total dana dikembalikan ke customer</div>
                    </div>
                </div>

                {{-- Stat 4: Rata-rata Kerugian --}}
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

                {{-- Stat 5: Rasio Pembatalan --}}
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
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xl shadow-gray-100/50 mb-8"
                 x-data="{
                    fp: null,
                    initPicker() {
                        const from = '{{ $dateFrom }}';
                        const to = '{{ $dateTo }}';
                        let defaultDates = [];
                        if (from && to) defaultDates = [from, to];
                        else if (from) defaultDates = [from];

                        if (typeof flatpickr !== 'undefined') {
                            this.fp = flatpickr(this.$refs.dateRangeInput, {
                                mode: 'range',
                                dateFormat: 'Y-m-d',
                                altInput: true,
                                altFormat: 'd M Y',
                                defaultDate: defaultDates,
                                locale: {
                                    rangeSeparator: ' s/d ',
                                    firstDayOfWeek: 1
                                },
                                onChange: (selectedDates, dateStr, instance) => {
                                    if (selectedDates.length === 2) {
                                        document.getElementById('hidden_date_from').value = instance.formatDate(selectedDates[0], 'Y-m-d');
                                        document.getElementById('hidden_date_to').value = instance.formatDate(selectedDates[1], 'Y-m-d');
                                    } else if (selectedDates.length === 1) {
                                        document.getElementById('hidden_date_from').value = instance.formatDate(selectedDates[0], 'Y-m-d');
                                        document.getElementById('hidden_date_to').value = '';
                                    } else {
                                        document.getElementById('hidden_date_from').value = '';
                                        document.getElementById('hidden_date_to').value = '';
                                    }
                                }
                            });
                        }
                    },
                    setPreset(type) {
                        if (!this.fp) return;
                        const today = new Date();
                        let start, end;
                        if (type === 'today') {
                            start = today;
                            end = today;
                        } else if (type === '7days') {
                            start = new Date();
                            start.setDate(today.getDate() - 6);
                            end = today;
                        } else if (type === 'this_month') {
                            start = new Date(today.getFullYear(), today.getMonth(), 1);
                            end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        } else if (type === 'clear') {
                            this.fp.clear();
                            document.getElementById('hidden_date_from').value = '';
                            document.getElementById('hidden_date_to').value = '';
                            return;
                        }
                        this.fp.setDate([start, end], true);
                    },
                    exportToExcel() {
                        const search = encodeURIComponent(document.getElementById('search')?.value || '');
                        const dateFrom = encodeURIComponent(document.getElementById('hidden_date_from')?.value || '');
                        const dateTo = encodeURIComponent(document.getElementById('hidden_date_to')?.value || '');
                        window.location.href = `{{ route('finance.cancelled.export') }}?search=${search}&date_from=${dateFrom}&date_to=${dateTo}`;
                    }
                 }"
                 x-init="initPicker()">
                <form action="{{ route('finance.cancelled') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-end gap-4">
                    {{-- Search keyword --}}
                    <div class="flex-1 w-full">
                        <label for="search" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 italic">Cari Transaksi Batal</label>
                        <div class="relative">
                            <input type="text" name="search" id="search" value="{{ $search }}"
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:border-emerald-500 focus:ring-emerald-500/20"
                                placeholder="Cari No SPK, Nama, Telepon, atau Alasan Batal...">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Unified Date Range Picker --}}
                    <div class="w-full lg:w-96">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest italic">Rentang Tanggal Batal</label>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="setPreset('today')" class="text-[9px] font-black uppercase text-gray-500 hover:text-emerald-700 bg-gray-100 hover:bg-emerald-50 px-2 py-0.5 rounded transition-all active:scale-95">Hari Ini</button>
                                <button type="button" @click="setPreset('7days')" class="text-[9px] font-black uppercase text-gray-500 hover:text-emerald-700 bg-gray-100 hover:bg-emerald-50 px-2 py-0.5 rounded transition-all active:scale-95">7 Hari</button>
                                <button type="button" @click="setPreset('this_month')" class="text-[9px] font-black uppercase text-gray-500 hover:text-emerald-700 bg-gray-100 hover:bg-emerald-50 px-2 py-0.5 rounded transition-all active:scale-95">Bulan Ini</button>
                            </div>
                        </div>
                        <div class="relative">
                            <input type="text" x-ref="dateRangeInput"
                                   class="w-full pl-11 pr-8 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:border-emerald-500 focus:ring-emerald-500/20 placeholder-gray-400"
                                   placeholder="Semua Tanggal Batal...">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <button type="button" @click="setPreset('clear')" x-show="fp && fp.selectedDates.length > 0" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <input type="hidden" name="date_from" id="hidden_date_from" value="{{ $dateFrom }}">
                        <input type="hidden" name="date_to" id="hidden_date_to" value="{{ $dateTo }}">
                    </div>

                    {{-- Buttons --}}
                    <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                        <button type="submit" class="flex-1 lg:flex-none px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-xs font-black uppercase tracking-widest italic transition-all hover:shadow-lg active:scale-95">
                            Filter
                        </button>
                        
                        {{-- Export Excel Button --}}
                        <button type="button" @click="exportToExcel()"
                                class="flex-1 lg:flex-none inline-flex items-center justify-center gap-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase tracking-widest italic shadow-lg shadow-emerald-600/20 transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Export Excel</span>
                        </button>

                        @if($search || $dateFrom || $dateTo)
                            <a href="{{ route('finance.cancelled') }}" class="flex-1 lg:flex-none px-4 py-3 bg-gray-100 hover:bg-gray-250 text-gray-500 rounded-xl text-xs font-black uppercase tracking-widest italic transition-all text-center active:scale-95">
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
                                        <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] italic">No SPK & Customer</th>
                                        <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] italic">Detail Sepatu</th>
                                        <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] text-right italic">Estimasi Kerugian</th>
                                        <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] text-right italic">Uang Masuk</th>
                                        <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] text-right italic">Nominal Refund</th>
                                        <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] text-right italic">Tanggal Batal</th>
                                        <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] italic">Alasan Pembatalan</th>
                                        <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] text-center italic">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50/50">
                                    @foreach($orders as $order)
                                    @php
                                        $paidSoFar = (float) ($order->invoice?->paid_amount ?? $order->payments->sum('amount_total'));
                                        $refundAmt = (float) ($order->refund_amount ?? 0);
                                    @endphp
                                    <tr class="hover:bg-rose-50/20 transition-all duration-500 group relative">
                                        {{-- 1. No SPK & Customer --}}
                                        <td class="px-6 py-6 relative">
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

                                        {{-- 2. Detail Sepatu --}}
                                        <td class="px-6 py-6">
                                            <div class="flex flex-col">
                                                <div class="text-xs font-black text-gray-800 uppercase tracking-tight">
                                                    {{ $order->shoe_brand }} {{ $order->shoe_type }}
                                                </div>
                                                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                                    Warna: {{ $order->shoe_color ?? '-' }}
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 3. Estimasi Kerugian --}}
                                        <td class="px-6 py-6 text-right">
                                            <div class="font-black text-red-600 text-sm tracking-tight italic">
                                                Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}
                                            </div>
                                        </td>

                                        {{-- 4. Uang Masuk dari Customer --}}
                                        <td class="px-6 py-6 text-right">
                                            @if($paidSoFar > 0)
                                                <div class="font-black text-emerald-600 text-sm tracking-tight italic tabular-nums">
                                                    Rp {{ number_format($paidSoFar, 0, ',', '.') }}
                                                </div>
                                                <div class="inline-flex items-center gap-1 text-[9px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md uppercase tracking-wider mt-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Terbayar
                                                </div>
                                            @else
                                                <div class="text-xs font-bold text-gray-400 italic tabular-nums">
                                                    Rp 0
                                                </div>
                                                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mt-1 italic">
                                                    Belum Bayar
                                                </div>
                                            @endif
                                        </td>

                                        {{-- 5. Nominal Refund --}}
                                        <td class="px-6 py-6 text-right">
                                            <div class="font-black text-sm tracking-tight italic tabular-nums {{ $refundAmt > 0 ? 'text-amber-600' : 'text-gray-400' }}">
                                                Rp {{ number_format($refundAmt, 0, ',', '.') }}
                                            </div>
                                            @if($order->refund_notes)
                                                <div class="text-[9px] font-semibold text-gray-500 italic max-w-[150px] ml-auto truncate mt-0.5" title="{{ $order->refund_notes }}">
                                                    {{ $order->refund_notes }}
                                                </div>
                                            @endif
                                            <button type="button" 
                                                    @click="openRefundModal($el.dataset.id, $el.dataset.spk, $el.dataset.refund, $el.dataset.notes, $el.dataset.paid)"
                                                    data-id="{{ $order->id }}"
                                                    data-spk="{{ $order->spk_number }}"
                                                    data-refund="{{ $refundAmt }}"
                                                    data-notes="{{ $order->refund_notes ?? '' }}"
                                                    data-paid="{{ $paidSoFar }}"
                                                    class="inline-flex items-center gap-1 mt-1.5 text-[10px] font-black uppercase tracking-wider text-amber-600 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200/60 px-2.5 py-1 rounded-lg transition-all active:scale-95 shadow-sm cursor-pointer">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                <span>Kelola Refund</span>
                                            </button>
                                        </td>

                                        {{-- 6. Tanggal Batal --}}
                                        <td class="px-6 py-6 text-right">
                                            <div class="text-xs font-bold text-gray-900 tracking-tight">{{ $order->updated_at ? $order->updated_at->format('d M Y') : '-' }}</div>
                                            <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5 italic">
                                                {{ $order->updated_at ? $order->updated_at->diffForHumans() : '' }}
                                            </div>
                                        </td>

                                        {{-- 7. Alasan Pembatalan --}}
                                        <td class="px-6 py-6 max-w-xs">
                                            <div class="text-xs text-gray-600 font-medium line-clamp-2" title="{{ $order->reception_rejection_reason }}">
                                                {{ $order->reception_rejection_reason ?? 'Tidak dicantumkan alasan pembatalan.' }}
                                            </div>
                                        </td>

                                        {{-- 8. Aksi --}}
                                        <td class="px-6 py-6 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                                   class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-gray-200 hover:border-gray-300 rounded-xl text-[10px] font-black uppercase tracking-wider italic text-gray-600 transition-all hover:scale-105 active:scale-95 shadow-sm">
                                                    Detail SPK
                                                </a>
                                                @if(auth()->user()->role === 'admin')
                                                    <button type="button" 
                                                            onclick="restoreSpk('{{ $order->id }}', '{{ $order->spk_number }}')"
                                                            class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-emerald-100 text-emerald-600 hover:bg-emerald-50 rounded-xl text-[10px] font-black uppercase tracking-wider italic transition-all hover:scale-105 active:scale-95 shadow-sm">
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

        {{-- Modal Quick-Edit Refund Finance --}}
        <div x-show="isRefundModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div x-show="isRefundModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeRefundModal()"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                     aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Dialog Panel --}}
                <div x-show="isRefundModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
                    
                    <form @submit.prevent="submitRefund()">
                        <div class="p-6">
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-lg">
                                        💸
                                    </div>
                                    <div>
                                        <h3 class="text-base font-black text-gray-900 italic leading-tight">
                                            Kelola Refund SPK
                                        </h3>
                                        <p class="text-[11px] font-bold text-gray-400 mt-0.5" x-text="activeSpkNumber"></p>
                                    </div>
                                </div>
                                <button type="button" @click="closeRefundModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="space-y-4 mt-4">
                                {{-- Ringkasan Pembayaran Customer --}}
                                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/60">
                                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                        Uang Masuk dari Customer
                                    </div>
                                    <div class="text-lg font-black text-emerald-600 italic tabular-nums" x-text="'Rp ' + Number(paidSoFar).toLocaleString('id-ID')">
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-0.5 font-medium">
                                        Acuan nominal uang yang sudah dibayarkan customer untuk SPK ini.
                                    </p>
                                </div>

                                {{-- Input Nominal Refund --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">
                                        Nominal Refund (Rp) <span class="text-amber-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-black text-gray-400">Rp</span>
                                        <input type="number" 
                                               x-model.number="refundAmount" 
                                               min="0" 
                                               step="1000"
                                               required
                                               class="w-full pl-12 pr-4 py-3 bg-gray-50 rounded-2xl border-gray-200 focus:border-amber-500 focus:ring-amber-500 font-black text-base text-gray-900"
                                               placeholder="0">
                                    </div>
                                    <p class="text-[10px] text-gray-400 font-medium mt-1">
                                        Ketik nominal uang yang dikembalikan ke customer (0 jika tidak ada pengembalian).
                                    </p>
                                </div>

                                {{-- Catatan Refund --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">
                                        Catatan Refund (Opsional)
                                    </label>
                                    <textarea x-model="refundNotes" 
                                              rows="2"
                                              class="w-full p-3.5 bg-gray-50 rounded-2xl border-gray-200 focus:border-amber-500 focus:ring-amber-500 text-xs font-medium text-gray-900"
                                              placeholder="Contoh: Ditransfer via BCA, potongan admin Rp 10.000, dll."></textarea>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                                <button type="button" 
                                        @click="closeRefundModal()" 
                                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl text-xs uppercase tracking-wider transition-all">
                                    Batal
                                </button>
                                <button type="submit" 
                                        :disabled="isSaving" 
                                        class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-xl text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                    <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Refund'"></span>
                                    <svg x-show="isSaving" class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>
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
