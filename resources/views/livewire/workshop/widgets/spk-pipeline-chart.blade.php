<div>
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-gray-50 to-orange-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-teal-500 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-black text-gray-800 tracking-tight">Status Pipeline</h3>
                        {{-- Info Tooltip --}}
                        <div x-data="{ open: false }" class="relative inline-block">
                            <button @mouseenter="open = true" @mouseleave="open = false" class="text-orange-300 hover:text-orange-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak x-transition class="absolute z-50 w-80 max-w-none p-5 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-orange-100 left-0 mt-3 whitespace-normal text-left">
                                <div class="absolute -top-1.5 left-4 w-3 h-3 bg-white border-t border-l border-orange-100 rotate-45"></div>
                                <div class="relative">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-1 h-4 bg-orange-500 rounded-full"></div>
                                        <div class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Maksud</div>
                                    </div>
                                    <div class="text-[13px] text-gray-700 leading-relaxed mb-4 pl-3 font-medium">Melihat distribusi beban kerja di setiap tahapan (Station) secara real-time untuk menghindari bottleneck.</div>
                                    
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-1 h-4 bg-gray-400 rounded-full"></div>
                                        <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sumber Data</div>
                                    </div>
                                    <div class="text-[12px] text-gray-500 leading-relaxed italic pl-3">Status terkini dari seluruh SPK yang aktif di workshop.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Active Station Distribution</p>
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
                              this.chart.data.datasets[0].data = newData.data;
                              this.chart.data.datasets[0].backgroundColor = newData.colors;
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
                          type: 'doughnut',
                          data: {
                              labels: this.$wire.chartData.labels,
                              datasets: [{
                                  data: this.$wire.chartData.data,
                                  backgroundColor: this.$wire.chartData.colors,
                                  borderWidth: 3,
                                  borderColor: '#ffffff',
                                  hoverOffset: 8
                              }]
                          },
                          options: {
                              responsive: true,
                              maintainAspectRatio: false,
                              cutout: '65%',
                              onClick: (e, elements) => {
                                  // Click detected
                              },
                              plugins: {
                                  legend: {
                                      position: 'bottom',
                                      labels: { padding: 15, usePointStyle: true, pointStyleWidth: 10, font: { size: 11, weight: 'bold' } }
                                  }
                              }
                          }
                      });
                  }
             }">
            <div class="relative" style="height: 280px">
                <canvas x-ref="canvas"></canvas>

                {{-- Loading Overlay --}}
                <div wire:loading class="absolute inset-0 bg-white/50 backdrop-blur-[1px] flex items-center justify-center z-10 transition-all duration-300">
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-8 h-8 border-4 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-xs font-bold text-orange-700 uppercase tracking-widest">Updating</span>
                    </div>
                </div>
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="text-center">
                        <div class="text-4xl font-black bg-gradient-to-r from-teal-600 to-orange-600 bg-clip-text text-transparent" x-text="$wire.chartData.total || 0">
                        </div>
                        <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total SPK</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
