@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-8 bg-teal-500 rounded-full"></div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">KPI</h1>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1 ml-4">
                Analisis Durasi Pengerjaan SPK per Tahapan Utama
            </p>
        </div>
        
        {{-- Export Excel Button --}}
        <a href="{{ route('admin.kpi.export', request()->all()) }}" 
           class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-2xl text-xs font-black shadow-lg shadow-teal-500/20 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Ekspor Excel
        </a>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl p-6 mb-8">
        <form method="GET" action="{{ route('admin.kpi.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            
            {{-- Search Input --}}
            <div class="space-y-1.5 col-span-1 md:col-span-2">
                <label class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest ml-1">Pencarian SPK / Customer</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik No. SPK atau Nama Pelanggan..."
                           class="w-full bg-gray-50 dark:bg-gray-750 border border-gray-200 dark:border-gray-650 rounded-2xl pl-11 pr-4 py-3.5 text-sm text-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all font-semibold">
                </div>
            </div>

            {{-- Start Date Input --}}
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest ml-1">Tanggal Mulai SPK</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full bg-gray-50 dark:bg-gray-750 border border-gray-200 dark:border-gray-650 rounded-2xl px-4 py-3 text-sm text-gray-805 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all font-bold">
            </div>

            {{-- End Date Input --}}
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest ml-1">Tanggal Selesai SPK</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full bg-gray-50 dark:bg-gray-750 border border-gray-200 dark:border-gray-650 rounded-2xl px-4 py-3 text-sm text-gray-805 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all font-bold">
            </div>

            {{-- Submit and Reset Buttons --}}
            <div class="col-span-1 md:col-span-4 flex justify-end gap-3 pt-2">
                @if($search || $startDate || $endDate)
                    <a href="{{ route('admin.kpi.index') }}" 
                       class="px-6 py-3.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-700 dark:text-white rounded-2xl text-xs font-black transition-all uppercase tracking-widest">
                        Reset Filter
                    </a>
                @endif
                <button type="submit" 
                        class="px-10 py-3.5 bg-teal-600 hover:bg-teal-500 text-white rounded-2xl text-xs font-black shadow-md shadow-teal-600/10 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest">
                    Cari & Saring
                </button>
            </div>
        </form>
    </div>

    {{-- Data Table Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-gray-750 border-b border-gray-150 dark:border-gray-700 text-slate-400 dark:text-slate-500 text-[10px] font-black uppercase tracking-widest">
                        <th class="px-6 py-5 align-middle">SPK & Pelanggan</th>
                        <th class="px-6 py-5 align-middle text-center">Status</th>
                        
                        {{-- PREP Stage Header --}}
                        <th class="px-6 py-5 align-middle border-l border-gray-200 dark:border-gray-700 bg-teal-500/5 text-center">
                            <span class="text-teal-600">PREPARATION</span>
                        </th>
                        
                        {{-- SORTIR Stage Header --}}
                        <th class="px-6 py-5 align-middle border-l border-gray-200 dark:border-gray-700 bg-amber-500/5 text-center">
                            <span class="text-amber-600">SORTIR</span>
                        </th>
                        
                        {{-- PRODUCTION Stage Header --}}
                        <th class="px-6 py-5 align-middle border-l border-gray-200 dark:border-gray-700 bg-blue-500/5 text-center">
                            <span class="text-blue-600">PRODUCTION</span>
                        </th>
                        
                        {{-- QC Stage Header --}}
                        <th class="px-6 py-5 align-middle border-l border-gray-200 dark:border-gray-700 bg-purple-500/5 text-center">
                            <span class="text-purple-600">QC</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-gray-750/30 transition-colors">
                            
                            {{-- SPK & Customer --}}
                            <td class="px-6 py-4.5">
                                <a href="{{ route('admin.orders.show', $order->id) }}" target="_blank" 
                                   class="font-mono text-xs font-black text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/30 px-2.5 py-1 rounded-md border border-teal-100 dark:border-teal-900/40 hover:scale-105 inline-block transition-all mb-1">
                                    {{ $order->spk_number }}
                                </a>
                                <div class="text-xs font-bold text-gray-800 dark:text-white leading-tight">
                                    {{ $order->customer_name }}
                                </div>
                            </td>
                            
                            {{-- Current Status --}}
                            <td class="px-6 py-4.5 text-center align-middle">
                                @php
                                    $statusLabel = $order->status ? $order->status->label() : '-';
                                    $statusColor = 'gray';
                                    if ($order->status) {
                                        $statusColor = match($order->status) {
                                            \App\Enums\WorkOrderStatus::PREPARATION => 'teal',
                                            \App\Enums\WorkOrderStatus::SORTIR => 'amber',
                                            \App\Enums\WorkOrderStatus::PRODUCTION => 'blue',
                                            \App\Enums\WorkOrderStatus::QC => 'purple',
                                            \App\Enums\WorkOrderStatus::SELESAI => 'green',
                                            \App\Enums\WorkOrderStatus::BATAL => 'red',
                                            default => 'gray'
                                        };
                                    }
                                @endphp
                                <span class="inline-flex items-center text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider bg-{{ $statusColor }}-50 text-{{ $statusColor }}-700 dark:bg-{{ $statusColor }}-955/30 dark:text-{{ $statusColor }}-400 border border-{{ $statusColor }}-200/50 dark:border-{{ $statusColor }}-900/40">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            {{-- PREPARATION KPI --}}
                            <td class="px-6 py-4.5 border-l border-gray-100 dark:border-gray-700 bg-teal-500/[0.01]">
                                <div class="text-[10px] space-y-0.5 text-gray-500 dark:text-gray-400">
                                    <div class="flex justify-between gap-4"><span class="font-medium text-gray-400">Masuk:</span> <span class="font-bold text-gray-700 dark:text-gray-200">{{ $order->kpi_data['PREPARATION']['enter_at'] }}</span></div>
                                    <div class="flex justify-between gap-4"><span class="font-medium text-gray-400">Keluar:</span> <span class="font-bold text-gray-700 dark:text-gray-200">{{ $order->kpi_data['PREPARATION']['exit_at'] }}</span></div>
                                    <div class="flex justify-between gap-4 pt-1 border-t border-dashed border-gray-150 dark:border-gray-750">
                                        <span class="font-black text-teal-600 uppercase text-[9px] tracking-widest">Durasi:</span>
                                        <span class="font-black text-teal-700 dark:text-teal-400 text-xs">{{ $order->kpi_data['PREPARATION']['duration'] }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- SORTIR KPI --}}
                            <td class="px-6 py-4.5 border-l border-gray-100 dark:border-gray-700 bg-amber-500/[0.01]">
                                <div class="text-[10px] space-y-0.5 text-gray-500 dark:text-gray-400">
                                    <div class="flex justify-between gap-4"><span class="font-medium text-gray-400">Masuk:</span> <span class="font-bold text-gray-700 dark:text-gray-200">{{ $order->kpi_data['SORTIR']['enter_at'] }}</span></div>
                                    <div class="flex justify-between gap-4"><span class="font-medium text-gray-400">Keluar:</span> <span class="font-bold text-gray-700 dark:text-gray-200">{{ $order->kpi_data['SORTIR']['exit_at'] }}</span></div>
                                    <div class="flex justify-between gap-4 pt-1 border-t border-dashed border-gray-150 dark:border-gray-750">
                                        <span class="font-black text-amber-600 uppercase text-[9px] tracking-widest">Durasi:</span>
                                        <span class="font-black text-amber-700 dark:text-amber-400 text-xs">{{ $order->kpi_data['SORTIR']['duration'] }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- PRODUCTION KPI --}}
                            <td class="px-6 py-4.5 border-l border-gray-100 dark:border-gray-700 bg-blue-500/[0.01]">
                                <div class="text-[10px] space-y-0.5 text-gray-500 dark:text-gray-400">
                                    <div class="flex justify-between gap-4"><span class="font-medium text-gray-400">Masuk:</span> <span class="font-bold text-gray-700 dark:text-gray-200">{{ $order->kpi_data['PRODUCTION']['enter_at'] }}</span></div>
                                    <div class="flex justify-between gap-4"><span class="font-medium text-gray-400">Keluar:</span> <span class="font-bold text-gray-700 dark:text-gray-200">{{ $order->kpi_data['PRODUCTION']['exit_at'] }}</span></div>
                                    <div class="flex justify-between gap-4 pt-1 border-t border-dashed border-gray-150 dark:border-gray-750">
                                        <span class="font-black text-blue-600 uppercase text-[9px] tracking-widest">Durasi:</span>
                                        <span class="font-black text-blue-700 dark:text-blue-400 text-xs">{{ $order->kpi_data['PRODUCTION']['duration'] }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- QC KPI --}}
                            <td class="px-6 py-4.5 border-l border-gray-100 dark:border-gray-700 bg-purple-500/[0.01]">
                                <div class="text-[10px] space-y-0.5 text-gray-500 dark:text-gray-400">
                                    <div class="flex justify-between gap-4"><span class="font-medium text-gray-400">Masuk:</span> <span class="font-bold text-gray-700 dark:text-gray-200">{{ $order->kpi_data['QC']['enter_at'] }}</span></div>
                                    <div class="flex justify-between gap-4"><span class="font-medium text-gray-400">Keluar:</span> <span class="font-bold text-gray-700 dark:text-gray-200">{{ $order->kpi_data['QC']['exit_at'] }}</span></div>
                                    <div class="flex justify-between gap-4 pt-1 border-t border-dashed border-gray-150 dark:border-gray-750">
                                        <span class="font-black text-purple-600 uppercase text-[9px] tracking-widest">Durasi:</span>
                                        <span class="font-black text-purple-700 dark:text-purple-400 text-xs">{{ $order->kpi_data['QC']['duration'] }}</span>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500 text-xs font-semibold italic">
                                Belum ada data SPK yang sesuai dengan filter atau pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
            <div class="px-6 py-5 bg-slate-50 dark:bg-gray-750 border-t border-gray-150 dark:border-gray-700">
                {{ $orders->appends(request()->all())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
