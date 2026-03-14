<div>
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-orange-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-gray-800 tracking-tight">Arus Masuk vs Selesai</h3>
                            {{-- Info Tooltip --}}
                            <div x-data="{ open: false }" class="relative inline-block">
                                <button @click.stop="open = !open" class="text-gray-300 hover:text-teal-500 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-50 w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-gray-100 left-0 mt-3 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-4 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-teal-500 rounded-full"></div>
                                            <div class="text-[10px] font-black text-teal-600 uppercase tracking-widest">Maksud</div>
                                        </div>
                                        <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Memantau kecepatan masuknya order baru dibandingkan dengan kecepatan tim menyelesaikan order untuk mengukur efisiensi output.</div>
                                        
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                            <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                        </div>
                                        <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Perbandingan tanggal masuk vs tanggal selesai SPK pada periode tertentu.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Inflow vs Completion Trends</p>
                    </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-teal-500 shadow-[0_0_8px_rgba(20,184,166,0.4)]"></div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider leading-none">Total Selesai</span>
                            <span class="text-xl font-black text-teal-600 leading-tight" x-text="$wire.chartData.totalCompletions || 0"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 border-l border-gray-100 pl-6">
                        <div class="w-3 h-3 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.4)]"></div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider leading-none">Total Masuk</span>
                            <span class="text-xl font-black text-orange-600 leading-tight" x-text="$wire.chartData.totalEntries || 0"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 border-l border-gray-100 pl-6">
                        <div :class="$wire.chartData.ratio >= 100 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]' : 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.4)]'" 
                             class="w-3 h-3 rounded-full transition-colors duration-500"></div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider leading-none">Performance Index</span>
                            <div class="flex items-baseline gap-1">
                                <span :class="$wire.chartData.ratio >= 100 ? 'text-emerald-600' : 'text-amber-600'" 
                                      class="text-xl font-black leading-tight transition-colors duration-500" 
                                      x-text="($wire.chartData.ratio || 0) + '%'"></span>
                                <span class="text-[10px] font-bold text-gray-400" x-show="$wire.chartData.ratio >= 100">Efficiency</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6" wire:ignore
             x-data="{
                 chart: null,
                  init() {
                      this.createChart();

                      this.$wire.on('refreshChart', () => {
                          this.$nextTick(() => {
                              if (!this.chart) {
                                  this.createChart();
                                  return;
                              }
                              let newData = this.$wire.chartData;
                              if (!newData || !newData.labels) return;
                              this.chart.data.labels = newData.labels;
                              this.chart.data.datasets[0].data = newData.completions;
                              this.chart.data.datasets[1].data = newData.entries;
                              this.chart.update('none');
                          });
                      });
                  },
                  createChart() {
                      let canvas = this.$refs.canvas;
                      if (!canvas) return;
                      let ctx = canvas.getContext('2d');
                      if (!ctx) return;
                      canvas.style.cursor = 'pointer';

                      if (this.chart) {
                          this.chart.destroy();
                      }

                      this.chart = new Chart(ctx, {
                          type: 'line',
                          data: {
                              labels: this.$wire.chartData.labels,
                              datasets: [
                                  {
                                      label: 'Order Selesai',
                                      data: this.$wire.chartData.completions,
                                      borderColor: '#14b8a6',
                                      backgroundColor: 'rgba(20, 184, 166, 0.1)',
                                      fill: true,
                                      tension: 0.4,
                                      borderWidth: 2.5,
                                      pointRadius: 4,
                                      pointBackgroundColor: '#14b8a6',
                                      pointStyle: 'rectRounded',
                                      pointHoverRadius: 6
                                  },
                                  {
                                      label: 'Order Masuk',
                                      data: this.$wire.chartData.entries,
                                      borderColor: '#f97316',
                                      backgroundColor: 'rgba(249, 115, 22, 0.05)',
                                      fill: true,
                                      tension: 0.4,
                                      borderWidth: 2,
                                      borderDash: [5, 5],
                                      pointRadius: 3,
                                      pointBackgroundColor: '#f97316',
                                      pointStyle: 'rectRounded',
                                      pointHoverRadius: 5
                                  }
                              ]
                          },
                          options: {
                              responsive: true,
                              maintainAspectRatio: false,
                              interaction: { intersect: false, mode: 'index' },
                              onClick: (e, elements) => {
                                  // Click detected
                              },
                              scales: {
                                  y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,0.04)' } },
                                  x: { ticks: { font: { size: 10 }, maxRotation: 45 }, grid: { display: false } }
                              },
                              plugins: {
                                  legend: {
                                      display: false // We use our own custom header summary
                                  }
                              }
                          }
                      });
                  }
             }">
             <div style="height: 280px; position:relative;">
                <canvas x-ref="canvas"></canvas>
                
                {{-- Loading Overlay --}}
                <div wire:loading class="absolute inset-0 bg-white/50 backdrop-blur-[1px] flex items-center justify-center z-10 transition-all duration-300">
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-8 h-8 border-4 border-teal-500 border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-xs font-bold text-teal-700 uppercase tracking-widest">Updating</span>
                    </div>
                </div>
             </div>
        </div>
    </div>
</div>
