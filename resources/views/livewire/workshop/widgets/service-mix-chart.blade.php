<div>
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-gray-800 tracking-tight">Layanan Terpopuler</h3>
                            {{-- Info Tooltip --}}
                            <div x-data="{ open: false }" class="relative inline-block">
                                <button @click.stop="open = !open" class="text-orange-300 hover:text-orange-600 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-50 w-80 max-w-none p-5 bg-white rounded-2xl shadow-2xl border border-gray-100 left-0 mt-3 whitespace-normal text-left">
                                    <div class="absolute -top-1.5 left-4 w-3 h-3 bg-white border-t border-l border-gray-100 rotate-45"></div>
                                    <div class="relative">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-orange-500 rounded-full"></div>
                                            <div class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Maksud</div>
                                        </div>
                                        <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Menganalisis kategori layanan yang paling banyak menghasilkan pendapatan untuk optimalisasi strategi bisnis.</div>
                                        
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                            <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                        </div>
                                        <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Data harga layanan dari SPK yang telah selesai (Finished status).</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Revenue by service category</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6" wire:ignore
             x-data="{
                 chart: null,
                 gradientColors: ['#14b8a6', '#f97316', '#6366f1', '#8b5cf6', '#ec4899', '#22c55e', '#f59e0b', '#ef4444'],
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
                              this.chart.data.datasets[0].data = newData.revenue;
                              this.chart.data.datasets[0].backgroundColor = newData.labels.map((_, i) => this.gradientColors[i % this.gradientColors.length] + '33');
                              this.chart.data.datasets[0].borderColor = newData.labels.map((_, i) => this.gradientColors[i % this.gradientColors.length]);
                              this.chart.update('none');
                          });
                      });
                  },
                  createChart() {
                      if (!this.$wire.chartData || !this.$wire.chartData.labels || this.$wire.chartData.labels.length === 0) return;

                      let canvas = this.$refs.canvas;
                      if (!canvas) return;
                      let ctx = canvas.getContext('2d');
                      if (!ctx) return;
                      canvas.style.cursor = 'pointer';

                      if (this.chart) {
                          this.chart.destroy();
                      }

                      this.chart = new Chart(ctx, {
                          type: 'bar',
                          data: {
                              labels: this.$wire.chartData.labels,
                              datasets: [{
                                  label: 'Revenue (Rp)',
                                  data: this.$wire.chartData.revenue,
                                  backgroundColor: this.$wire.chartData.labels.map((_, i) => this.gradientColors[i % this.gradientColors.length] + '33'),
                                  borderColor: this.$wire.chartData.labels.map((_, i) => this.gradientColors[i % this.gradientColors.length]),
                                  borderWidth: 2,
                                  borderRadius: 8,
                                  borderSkipped: false,
                              }]
                          },
                          options: {
                              indexAxis: 'y',
                              responsive: true,
                              maintainAspectRatio: false,
                              scales: {
                                  x: { ticks: { callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k', font: { size: 10 } }, grid: { color: 'rgba(0,0,0,0.04)' } },
                                  y: { ticks: { font: { size: 11, weight: 'bold' } }, grid: { display: false } }
                              },
                              plugins: {
                                  legend: { display: false },
                                  tooltip: { 
                                      enabled: true,
                                      callbacks: { label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) } 
                                  }
                              },
                              onClick: (e, elements) => {
                                  // Click detected
                              }
                          }
                      });
                  }
             }">
             
            <div x-show="$wire.chartData && $wire.chartData.labels && $wire.chartData.labels.length > 0" class="relative" style="height: 380px">
                 <canvas x-ref="canvas"></canvas>

                 {{-- Loading Overlay --}}
                 <div wire:loading class="absolute inset-0 bg-white/50 backdrop-blur-[1px] flex items-center justify-center z-10 transition-all duration-300">
                     <div class="flex flex-col items-center gap-2">
                         <div class="w-8 h-8 border-4 border-teal-500 border-t-transparent rounded-full animate-spin"></div>
                         <span class="text-xs font-bold text-teal-700 uppercase tracking-widest">Updating</span>
                     </div>
                 </div>
             </div>
            
            <div x-show="!$wire.chartData || !$wire.chartData.labels || $wire.chartData.labels.length === 0" class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm font-medium">Belum ada data layanan</p>
            </div>
        </div>
    </div>
</div>
