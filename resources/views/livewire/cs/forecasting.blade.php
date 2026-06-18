<div class="py-12 bg-slate-900 min-h-screen text-slate-100 font-sans"
     x-data="{
        monthlyData: @entangle('monthlyData'),
        chartInstance: null,
        renderChart() {
            const ctx = document.getElementById('comparisonChart');
            if (!ctx) return;
            
            if (this.chartInstance) {
                this.chartInstance.destroy();
            }

            const labels = this.monthlyData.map(m => m.month_name);
            const onlineDirect = this.monthlyData.map(m => m.closing_online);
            const followup = this.monthlyData.map(m => m.closing_followup);
            const offline = this.monthlyData.map(m => m.closing_offline);
            const tidakKirim = this.monthlyData.map(m => m.closing_tidak_kirim);

            this.chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Online Direct',
                            data: onlineDirect,
                            backgroundColor: 'rgba(20, 184, 166, 0.75)', // Teal
                            borderColor: 'rgba(20, 184, 166, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                        },
                        {
                            label: 'Follow Up',
                            data: followup,
                            backgroundColor: 'rgba(249, 115, 22, 0.75)', // Orange
                            borderColor: 'rgba(249, 115, 22, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                        },
                        {
                            label: 'Offline',
                            data: offline,
                            backgroundColor: 'rgba(99, 102, 241, 0.75)', // Indigo
                            borderColor: 'rgba(99, 102, 241, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                        },
                        {
                            label: 'Tidak Kirim (Sepatu)',
                            data: tidakKirim,
                            backgroundColor: 'rgba(239, 68, 68, 0.75)', // Red
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#e2e8f0',
                                font: {
                                    weight: 'bold',
                                    family: 'Inter, sans-serif'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#f8fafc',
                            bodyColor: '#cbd5e1',
                            borderColor: '#334155',
                            borderWidth: 1,
                            padding: 12
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(51, 65, 85, 0.2)' },
                            ticks: { color: '#94a3b8', font: { weight: 'bold' } }
                        },
                        y: {
                            grid: { color: 'rgba(51, 65, 85, 0.2)' },
                            ticks: { color: '#94a3b8', font: { weight: 'bold' } }
                        }
                    }
                }
            });
        }
     }"
     x-init="
        if (typeof Chart === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = () => renderChart();
            document.head.appendChild(script);
        } else {
            renderChart();
        }
        $watch('monthlyData', () => renderChart());
     ">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        {{-- Header --}}
        <div class="relative overflow-hidden rounded-[2.5rem] border border-slate-800 bg-slate-900/50 backdrop-blur-xl p-8 shadow-2xl">
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl"></div>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative z-10">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-teal-400 via-teal-300 to-indigo-400">
                        Perbandingan & Laporan Bulanan CS
                    </h1>
                    <p class="text-slate-400 text-sm mt-2 font-medium max-w-xl">
                        Pilih tahun di bawah untuk melihat laporan bulanan lengkap (Januari - Desember) dan perbandingan metrik CS secara otomatis.
                    </p>
                </div>
            </div>

            {{-- Copyable API Endpoint Card --}}
            <div class="mt-8 p-5 rounded-3xl bg-slate-950/60 border border-slate-800/80 backdrop-blur-md flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
                <div class="flex flex-col gap-1.5 flex-grow">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 text-[9px] font-black tracking-widest text-teal-400 bg-teal-500/10 rounded border border-teal-500/20 uppercase">GET</span>
                        <span class="text-[10px] font-black tracking-widest text-slate-500 uppercase">API ENDPOINT URL</span>
                    </div>
                    <code class="text-xs text-teal-300/95 font-mono select-all break-all pr-4">
                        {{ url('/cs/forecasting/api-data') }}?year={{ $selectedYear }}
                    </code>
                </div>
                <button onclick="navigator.clipboard.writeText('{{ url('/cs/forecasting/api-data') }}?year={{ $selectedYear }}'); alert('API Link copied to clipboard!')" 
                        class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-400 hover:to-teal-500 text-slate-950 text-xs font-bold rounded-2xl transition-all shadow-lg hover:shadow-teal-500/15 active:scale-95 flex items-center gap-2 whitespace-nowrap self-start md:self-auto">
                    <span>📋 Copy API Link</span>
                </button>
            </div>
        </div>

        {{-- Year Selector Filter --}}
        <div class="flex flex-col md:flex-row gap-4 items-center bg-slate-950/40 p-6 rounded-3xl border border-slate-800/80 backdrop-blur-md">
            <div class="relative w-full md:w-72">
                <label class="text-[10px] uppercase font-black tracking-widest text-slate-500 absolute -top-2.5 left-3 bg-slate-900 px-2 z-10">Pilih Tahun Laporan</label>
                <select wire:model.live="selectedYear" class="w-full bg-slate-900 border-slate-800 rounded-2xl text-xs font-bold text-slate-300 focus:ring-teal-500 focus:border-teal-500 py-3 px-4 cursor-pointer">
                    @php
                        $currentYear = date('Y');
                    @endphp
                    @for ($y = $currentYear - 2; $y <= $currentYear + 1; $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        {{-- Data Presentation Layout --}}
        <div class="space-y-8">
            
            {{-- Dynamic Table Columns --}}
            <div class="rounded-[2.5rem] border border-slate-800 bg-slate-900/50 backdrop-blur-md overflow-hidden shadow-2xl">
                <div class="p-8 border-b border-slate-800 flex justify-between items-center">
                    <h3 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-teal-500 rounded-full"></span>
                        Tabel Laporan Bulanan
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-400">
                        <thead class="text-[10px] text-slate-500 uppercase tracking-widest bg-slate-950/40 border-b border-slate-800/80">
                            <tr>
                                <th class="px-8 py-5 min-w-[200px]">Metrik</th>
                                @foreach($monthlyData as $data)
                                    <th class="px-6 py-5 text-center font-bold text-teal-400 bg-teal-500/5 min-w-[130px]">{{ $data['month_name'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-medium">
                            
                            {{-- Closing Online --}}
                            <tr class="hover:bg-slate-800/20 transition-all border-l-4 border-teal-500 bg-teal-500/[0.02]">
                                <td class="px-8 py-4.5 font-bold text-white min-w-[280px]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                                        <span>closing online</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-4.5 text-center font-extrabold text-teal-300 bg-teal-500/5 min-w-[130px]">{{ number_format($data['closing_online']) }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">├ % closing online</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Online / Total Closing)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['closing_online_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-500 italic pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span>└ closing ol/hari</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Online / Hari Aktif)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-500 bg-teal-500/5 min-w-[130px]">{{ number_format($data['closing_online_per_day'], 2) }}</td>
                                @endforeach
                            </tr>

                            {{-- Closing Follow Up --}}
                            <tr class="hover:bg-slate-800/20 transition-all border-l-4 border-orange-500 bg-orange-500/[0.02]">
                                <td class="px-8 py-4.5 font-bold text-white min-w-[280px]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                                        <span>closing follow up</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-4.5 text-center font-extrabold text-teal-300 bg-teal-500/5 min-w-[130px]">{{ number_format($data['closing_followup']) }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">├ % closing follow up</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Follow Up / Total Closing)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['closing_followup_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-500 italic pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span>└ closing fu/hari</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Follow Up / Hari Aktif)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-500 bg-teal-500/5 min-w-[130px]">{{ number_format($data['closing_followup_per_day'], 2) }}</td>
                                @endforeach
                            </tr>

                            {{-- Closing Offline --}}
                            <tr class="hover:bg-slate-800/20 transition-all border-l-4 border-indigo-500 bg-indigo-500/[0.02]">
                                <td class="px-8 py-4.5 font-bold text-white min-w-[280px]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                        <span>closing offline</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-4.5 text-center font-extrabold text-teal-300 bg-teal-500/5 min-w-[130px]">{{ number_format($data['closing_offline']) }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">├ % closing offline</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Offline / Total Closing)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['closing_offline_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-500 italic pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span>└ closing off/hari</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Offline / Hari Aktif)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-500 bg-teal-500/5 min-w-[130px]">{{ number_format($data['closing_offline_per_day'], 2) }}</td>
                                @endforeach
                            </tr>

                            {{-- Closing Tidak Kirim --}}
                            <tr class="hover:bg-slate-800/20 transition-all border-l-4 border-red-500 bg-red-500/[0.02]">
                                <td class="px-8 py-4.5 font-bold text-white min-w-[280px]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                        <span>closing tidak kirim</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-4.5 text-center font-extrabold text-teal-300 bg-teal-500/5 min-w-[130px]">{{ number_format($data['closing_tidak_kirim']) }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">├ % closing tidak kirim</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Tidak Kirim / (Online + FU))</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['closing_tidak_kirim_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-500 italic pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span>└ closing tidak kirim/hari</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Tidak Kirim / Hari Aktif)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-500 bg-teal-500/5 min-w-[130px]">{{ number_format($data['closing_tidak_kirim_per_day'], 2) }}</td>
                                @endforeach
                            </tr>

                            {{-- Total Closing --}}
                            <tr class="bg-slate-950/20 border-t-2 border-slate-800 border-l-4 border-emerald-500">
                                <td class="px-8 py-5 font-black text-white text-base min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span>total closing</span>
                                        <span class="text-[9px] text-slate-500 font-mono font-medium mt-0.5">(Online + FU + Offline)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-5 text-center font-black text-teal-400 text-lg bg-teal-500/10 min-w-[130px]">{{ number_format($data['total_closing']) }}</td>
                                @endforeach
                            </tr>

                            {{-- Tahap Barang Section --}}
                            <tr class="bg-slate-950/40 text-[10px] text-slate-400 uppercase tracking-widest border-t-2 border-b border-slate-800/80 border-l-4 border-slate-700">
                                <td class="px-8 py-3.5 font-bold" colspan="{{ count($monthlyData) + 1 }}">Tahap Barang</td>
                            </tr>
                            <tr class="hover:bg-slate-800/20 transition-all border-l-4 border-teal-500 bg-teal-500/[0.02]">
                                <td class="px-8 py-4 font-bold text-white min-w-[280px]">sepatu masuk online & fu</td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-4 text-center font-extrabold text-teal-300 bg-teal-500/5 min-w-[130px]">{{ number_format($data['sepatu_masuk_online'] ?? 0) }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">└ % sepatu online</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Sepatu Online / Total Sepatu Masuk)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['sepatu_online_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/20 transition-all border-l-4 border-indigo-500 bg-indigo-500/[0.02]">
                                <td class="px-8 py-4 font-bold text-white min-w-[280px]">sepatu masuk offline</td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-4 text-center font-extrabold text-teal-300 bg-teal-500/5 min-w-[130px]">{{ number_format($data['sepatu_masuk_offline'] ?? 0) }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-b border-slate-800 border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">└ % sepatu offline</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Sepatu Offline / Total Sepatu Masuk)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['sepatu_offline_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>

                            {{-- Tahap Uang Section --}}
                            <tr class="bg-slate-950/40 text-[10px] text-slate-400 uppercase tracking-widest border-t-2 border-b border-slate-800/80 border-l-4 border-slate-700">
                                <td class="px-8 py-3.5 font-bold" colspan="{{ count($monthlyData) + 1 }}">Tahap Uang</td>
                            </tr>
                            
                            {{-- Omset Total --}}
                            <tr class="hover:bg-slate-800/20 transition-all border-l-4 border-emerald-500 bg-emerald-500/[0.02]">
                                <td class="px-8 py-4 font-bold text-white min-w-[280px]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        <span>omset total</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-4 text-center font-extrabold text-emerald-300 bg-emerald-500/5 min-w-[130px]">Rp {{ number_format($data['omset_total'] ?? 0, 0, ',', '.') }}</td>
                                @endforeach
                            </tr>

                            {{-- Terbayar --}}
                            <tr class="hover:bg-slate-800/20 transition-all border-l-4 border-teal-500 bg-teal-500/[0.02]">
                                <td class="px-8 py-4 font-bold text-white min-w-[280px]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                                        <span>terbayar</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-4 text-center font-extrabold text-teal-300 bg-teal-500/5 min-w-[130px]">Rp {{ number_format($data['terbayar'] ?? 0, 0, ',', '.') }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">├ % terbayar</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Terbayar / Omset Total)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['terbayar_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>
                            
                            {{-- DP --}}
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">├ total DP</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Pembayaran DP Awal)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-slate-300 font-bold bg-teal-500/5 min-w-[130px]">Rp {{ number_format($data['total_dp'] ?? 0, 0, ',', '.') }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">├ % DP</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Total DP / Omset Total)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['dp_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>

                            {{-- Lunas Awal --}}
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">├ total lunas awal</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Pembayaran 100% Upfront)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-slate-300 font-bold bg-teal-500/5 min-w-[130px]">Rp {{ number_format($data['total_lunas_awal'] ?? 0, 0, ',', '.') }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">├ % lunas awal</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Total Lunas Awal / Omset Total)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['lunas_awal_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>

                            {{-- Pelunasan --}}
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">├ total pelunasan</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Pembayaran Akhir Pelunasan)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-slate-300 font-bold bg-teal-500/5 min-w-[130px]">Rp {{ number_format($data['total_pelunasan'] ?? 0, 0, ',', '.') }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">└ % pelunasan</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Total Pelunasan / Omset Total)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['pelunasan_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>

                            {{-- Tambah Jasa --}}
                            <tr class="hover:bg-slate-800/20 transition-all border-l-4 border-orange-500 bg-orange-500/[0.02]">
                                <td class="px-8 py-4 font-bold text-white min-w-[280px]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                                        <span>tambah jasa</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-4 text-center font-extrabold text-teal-300 bg-teal-500/5 min-w-[130px]">Rp {{ number_format($data['tambah_jasa'] ?? 0, 0, ',', '.') }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">└ % tambah jasa</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Tambah Jasa / Omset Total)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['tambah_jasa_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>

                            {{-- OTO --}}
                            <tr class="hover:bg-slate-800/20 transition-all border-l-4 border-indigo-500 bg-indigo-500/[0.02]">
                                <td class="px-8 py-4 font-bold text-white min-w-[280px]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                        <span>OTO</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-4 text-center font-extrabold text-teal-300 bg-teal-500/5 min-w-[130px]">Rp {{ number_format($data['oto'] ?? 0, 0, ',', '.') }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">└ % OTO</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(OTO / Omset Total)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['oto_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>

                            {{-- Ongkir --}}
                            <tr class="hover:bg-slate-800/20 transition-all border-l-4 border-blue-500 bg-blue-500/[0.02]">
                                <td class="px-8 py-4 font-bold text-white min-w-[280px]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                        <span>ongkir</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-4 text-center font-extrabold text-teal-300 bg-teal-500/5 min-w-[130px]">Rp {{ number_format($data['ongkir'] ?? 0, 0, ',', '.') }}</td>
                                @endforeach
                            </tr>
                            <tr class="hover:bg-slate-800/10 transition-all text-xs border-b border-slate-800 border-l-4 border-slate-800">
                                <td class="px-8 py-3 text-slate-400 pl-14 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-bold">└ % Ongkir</span>
                                        <span class="text-[9px] text-slate-500 font-mono mt-0.5">(Ongkir / Omset Total)</span>
                                    </div>
                                </td>
                                @foreach($monthlyData as $data)
                                    <td class="px-6 py-3 text-center text-teal-400 font-bold bg-teal-500/5 min-w-[130px]">{{ number_format($data['ongkir_pct'] ?? 0, 2) }}%</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="px-8 py-6 bg-slate-950/30 border-t border-slate-800 text-[10px] text-slate-500 font-bold uppercase tracking-wider flex justify-between items-center flex-wrap gap-4">
                    <span>* closing tidak kirim: SPK Sepatu berstatus SPK Pending (diambil dari tabel work_orders).</span>
                    <span>* total closing balance: Online + FU + Offline.</span>
                </div>
            </div>

            {{-- Chart Area --}}
            <div class="rounded-[2.5rem] border border-slate-800 bg-slate-900/50 backdrop-blur-md p-8 shadow-2xl">
                <h3 class="text-base font-black text-white uppercase tracking-tight flex items-center gap-3 mb-6">
                    <span class="w-1.5 h-6 bg-teal-500 rounded-full"></span>
                    Grafik Tren Bulanan
                </h3>
                <div class="h-80 relative" wire:ignore>
                    <canvas id="comparisonChart"></canvas>
                </div>
            </div>

        </div>

    </div>
</div>
