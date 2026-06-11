<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 w-full no-print">
            <h2 class="font-black text-xl text-white leading-tight flex items-center gap-4">
                <div class="p-2 bg-white/10 rounded-xl shadow-inner backdrop-blur-md border border-white/20">
                    <span class="text-xl">📊</span>
                </div>
                {{ $title }}
            </h2>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-5 py-2.5 bg-[#22AF85] hover:bg-[#1d9d76] active:scale-95 text-white text-xs font-black rounded-xl transition-all shadow-lg shadow-[#22AF85]/20 flex items-center gap-2 cursor-pointer border-none outline-none">
                    🖨️ CETAK LAPORAN
                </button>
                <a href="{{ route('storage.dashboard') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 active:scale-95 text-white text-xs font-black rounded-xl transition-all shadow-md flex items-center gap-2">
                    ⬅️ KEMBALI
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #print-area, #print-area * {
                visibility: visible;
            }
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                display: block !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <div class="py-12 bg-gray-50/50 min-h-screen no-print">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Filter Form --}}
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-gray-100">
                <form method="GET" action="{{ route('storage.dashboard.detail') }}" class="space-y-4">
                    <input type="hidden" name="type" value="{{ $type }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date_input" value="{{ $startDate }}" {{ $ignoreDate ? 'disabled' : '' }}
                                   class="block w-full border-gray-200 rounded-2xl text-xs font-bold focus:ring-[#22AF85] focus:border-[#22AF85] bg-gray-50/50 disabled:opacity-50">
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-2">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date_input" value="{{ $endDate }}" {{ $ignoreDate ? 'disabled' : '' }}
                                   class="block w-full border-gray-200 rounded-2xl text-xs font-bold focus:ring-[#22AF85] focus:border-[#22AF85] bg-gray-50/50 disabled:opacity-50">
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-2">Cari SPK / Customer</label>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nomor SPK atau nama..."
                                   class="block w-full border-gray-200 rounded-2xl text-xs font-bold focus:ring-[#22AF85] focus:border-[#22AF85] bg-gray-50/50">
                        </div>
                        
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 py-2.5 bg-[#22AF85] hover:bg-[#1d9d76] active:scale-95 text-white text-xs font-black rounded-2xl transition-all shadow-md text-center cursor-pointer border-none outline-none">
                                🔍 Terapkan Filter
                            </button>
                            <a href="{{ route('storage.dashboard.detail', ['type' => $type]) }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-black rounded-2xl transition-all text-center flex items-center justify-center">
                                🔄 Reset
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-2">
                        <input type="checkbox" name="ignore_date" id="ignore_date" value="1" {{ $ignoreDate ? 'checked' : '' }}
                               class="rounded border-gray-300 text-[#22AF85] focus:ring-[#22AF85] cursor-pointer"
                               onchange="document.getElementById('start_date_input').disabled = this.checked; document.getElementById('end_date_input').disabled = this.checked;">
                        <label for="ignore_date" class="text-[10px] font-black text-gray-500 uppercase tracking-wider cursor-pointer select-none">
                            Semua Waktu (Abaikan Filter Tanggal)
                        </label>
                    </div>
                </form>
            </div>

            {{-- Table Panel --}}
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 p-8">
                <div class="flex justify-between items-center pb-6 border-b border-gray-100 mb-6">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Daftar SPK Terdata</h3>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">
                            Menampilkan {{ count($items) }} rekaman data SPK
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-wider">
                                <th class="py-4 pr-4">No. SPK</th>
                                <th class="py-4 px-4">Customer</th>
                                <th class="py-4 px-4">Detail Sepatu</th>
                                <th class="py-4 px-4 text-center">Prioritas</th>
                                <th class="py-4 px-4 text-center">Status</th>
                                <th class="py-4 px-4 text-right">Tanggal</th>
                                @if($type === 'after_masuk')
                                    <th class="py-4 pl-4 text-right">Posisi Rak</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-xs text-gray-600 font-bold">
                            @forelse($items as $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 pr-4 font-mono text-gray-900 text-sm">{{ $item->spk_number }}</td>
                                    <td class="py-4 px-4">
                                        <div class="text-gray-900 font-black">{{ $item->customer_name }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium">{{ $item->customer_phone }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="text-gray-900">{{ $item->shoe_brand }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium">{{ $item->shoe_type }} {{ $item->shoe_color ? "({$item->shoe_color})" : "" }}</div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @php
                                            $priorityColor = match(strtolower($item->priority)) {
                                                'high' => 'bg-rose-50 text-rose-600 border-rose-100',
                                                'medium' => 'bg-amber-50 text-amber-600 border-amber-100',
                                                default => 'bg-slate-50 text-slate-600 border-slate-100'
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border {{ $priorityColor }}">{{ $item->priority }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-[9px] font-black uppercase">{{ $item->status->label() }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-right whitespace-nowrap text-gray-900">
                                        @if($type === 'sepatu_masuk')
                                            {{ $item->entry_date ? $item->entry_date->format('d M Y H:i') : '-' }}
                                        @else
                                            {{ $item->finished_date ? $item->finished_date->format('d M Y H:i') : '-' }}
                                        @endif
                                    </td>
                                    @if($type === 'after_masuk')
                                        <td class="py-4 pl-4 text-right">
                                            @if($item->storageAssignments->isNotEmpty())
                                                <span class="bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-lg text-emerald-600 font-black text-[10px] shadow-sm">
                                                    {{ implode(', ', $item->storageAssignments->pluck('rack_code')->toArray()) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 font-medium">-</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $type === 'after_masuk' ? 7 : 6 }}" class="py-12 text-center text-gray-400 italic">
                                        Tidak ada data SPK ditemukan untuk periode dan filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Invisible Print Area for Screen & Printable on Browser Print --}}
    <div id="print-area" class="hidden">
        <div style="font-family: Arial, sans-serif; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px;">
                <div>
                    <h2 style="margin: 0; font-size: 18px; font-weight: bold;">SHOESTUDIO WORKSHOP REPORT</h2>
                    <p style="margin: 3px 0 0 0; font-size: 10px; color: #555; font-weight: bold; text-transform: uppercase;">{{ $title }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0; font-size: 10px; font-weight: bold;">TANGGAL PRINT: {{ now()->format('d M Y, H:i') }}</p>
                    <p style="margin: 3px 0 0 0; font-size: 9px; color: #777;">PERIODE: {{ $ignoreDate ? 'SEMUA WAKTU' : \Carbon\Carbon::parse($startDate)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                    <p style="margin: 3px 0 0 0; font-size: 10px; color: #000; font-weight: bold;">TOTAL SPK: {{ count($items) }}</p>
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="border-bottom: 2px solid #000; font-size: 10px; text-transform: uppercase; font-weight: bold; text-align: left;">
                        <th style="padding: 8px 4px;">No. SPK</th>
                        <th style="padding: 8px 4px;">Customer</th>
                        <th style="padding: 8px 4px;">Detail Sepatu</th>
                        <th style="padding: 8px 4px; text-align: center;">Prioritas</th>
                        <th style="padding: 8px 4px; text-align: center;">Status</th>
                        <th style="padding: 8px 4px; text-align: right;">Tanggal</th>
                        @if($type === 'after_masuk')
                            <th style="padding: 8px 4px; text-align: right;">Rak</th>
                        @endif
                    </tr>
                </thead>
                <tbody style="font-size: 9px;">
                    @forelse($items as $index => $item)
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 8px 4px; font-family: monospace; font-weight: bold;">{{ $item->spk_number }}</td>
                            <td style="padding: 8px 4px;">
                                <strong>{{ $item->customer_name }}</strong><br>
                                <span style="color: #666; font-size: 8px;">{{ $item->customer_phone }}</span>
                            </td>
                            <td style="padding: 8px 4px;">
                                <strong>{{ $item->shoe_brand }}</strong><br>
                                <span style="color: #666; font-size: 8px;">{{ $item->shoe_type }} {{ $item->shoe_color ? "({$item->shoe_color})" : "" }}</span>
                            </td>
                            <td style="padding: 8px 4px; text-align: center; font-weight: bold; text-transform: uppercase;">{{ $item->priority }}</td>
                            <td style="padding: 8px 4px; text-align: center;">{{ $item->status->label() }}</td>
                            <td style="padding: 8px 4px; text-align: right;">
                                @if($type === 'sepatu_masuk')
                                    {{ $item->entry_date ? $item->entry_date->format('d M Y H:i') : '-' }}
                                @else
                                    {{ $item->finished_date ? $item->finished_date->format('d M Y H:i') : '-' }}
                                @endif
                            </td>
                            @if($type === 'after_masuk')
                                <td style="padding: 8px 4px; text-align: right; font-weight: bold;">
                                    {{ $item->storageAssignments->isNotEmpty() ? implode(', ', $item->storageAssignments->pluck('rack_code')->toArray()) : '-' }}
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $type === 'after_masuk' ? 7 : 6 }}" style="padding: 20px; text-align: center; color: #777; font-style: italic;">
                                Tidak ada data SPK ditemukan untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div style="margin-top: 40px; text-align: right; font-size: 9px; color: #666;">
                <p>Dilaporkan Oleh: Tim Control Center Gudang</p>
                <p style="margin-top: 5px; font-weight: bold;">Pusat Kendali Gudang Shoestudio</p>
            </div>
        </div>
    </div>
</x-app-layout>
