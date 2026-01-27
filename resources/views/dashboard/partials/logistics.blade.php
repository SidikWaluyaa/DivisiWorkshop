<section class="section-gradient rounded-3xl p-8 animate-fade-in-up">
    <!-- Section Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-[#FFC232] flex items-center justify-center shadow-lg shadow-[#FFC232]/30 section-icon-glow">
            <span class="text-2xl text-yellow-900">📍</span>
        </div>
        <div class="flex-1">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Live Operations</h2>
            <p class="text-sm text-gray-500 font-medium">Pantau posisi setiap sepatu di lantai produksi secara real-time</p>
        </div>
        <div class="hidden md:block flex-grow h-px section-divider"></div>
    </div>

    {{-- Live Workshop Flow --}}
    <div class="bg-white rounded-3xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 chart-card" x-data="{ activeLocation: null }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
            <h3 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-3">
                <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-orange-100 text-orange-600">📍</span>
                Live Workshop Flow
            </h3>
            <p class="text-gray-500 text-sm font-medium mt-1 ml-14">Pantau posisi setiap sepatu di lantai produksi secara real-time.</p>
        </div>
    </div>

    {{-- Location Triggers (Horizontal Scroll / Grid) --}}
    <div class="flex flex-wrap gap-3 mb-8">
        @foreach($locations as $location => $orders)
            @php
                $count = $orders->count();
                // Determine color based on location/status keywords
                $colorClass = 'gray'; // default
                $icon = '📍';

                if (str_contains($location, 'Penerimaan')) { $colorClass = 'blue'; $icon = '📥'; }
                elseif (str_contains($location, 'Preparation')) { $colorClass = 'cyan'; $icon = '🧼'; }
                elseif (str_contains($location, 'Sortir')) { $colorClass = 'indigo'; $icon = '📋'; }
                elseif (str_contains($location, 'Production')) { $colorClass = 'orange'; $icon = '🔨'; }
                elseif (str_contains($location, 'Jahit')) { $colorClass = 'orange'; $icon = '🧵'; }
                elseif (str_contains($location, 'Clean Up')) { $colorClass = 'teal'; $icon = '✨'; }
                elseif (str_contains($location, 'QC Akhir')) { $colorClass = 'green'; $icon = '✅'; }
                elseif (str_contains($location, 'Selesai')) { $colorClass = 'emerald'; $icon = '🛍️'; }
            @endphp
            
            <button 
                @click="activeLocation = activeLocation === '{{ $location }}' ? null : '{{ $location }}'"
                :class="activeLocation === '{{ $location }}' 
                    ? 'bg-{{ $colorClass }}-600 text-white shadow-lg shadow-{{ $colorClass }}-500/30 ring-2 ring-{{ $colorClass }}-400 ring-offset-2' 
                    : 'bg-white text-gray-600 border border-gray-200 hover:border-{{ $colorClass }}-400 hover:bg-{{ $colorClass }}-50'"
                class="group relative flex items-center gap-3 px-5 py-3 rounded-2xl transition-all duration-200 ease-out">
                
                <span class="text-xl">{{ $icon }}</span>
                <div class="text-left">
                    <div class="text-[10px] uppercase font-bold tracking-wider opacity-70 leading-none mb-1 group-hover:text-{{ $colorClass }}-600"
                         :class="activeLocation === '{{ $location }}' ? 'text-{{ $colorClass }}-100' : ''">
                        Lokasi
                    </div>
                    <div class="font-bold text-sm leading-none">{{ $location }}</div>
                </div>
                <span class="ml-2 flex items-center justify-center w-6 h-6 rounded-full text-xs font-black transition-colors"
                      :class="activeLocation === '{{ $location }}' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600 group-hover:bg-{{ $colorClass }}-100 group-hover:text-{{ $colorClass }}-700'">
                    {{ $count }}
                </span>
                
                {{-- Active Indicator --}}
                 <div x-show="activeLocation === '{{ $location }}'" 
                      class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-4 h-4 bg-{{ $colorClass }}-600 rotate-45 border-r border-b border-{{ $colorClass }}-400"></div>
            </button>
        @endforeach
    </div>
    
    {{-- Expanded Tables --}}
    @foreach($locations as $location => $orders)
        @if($orders->count() > 0)
            <div x-show="activeLocation === '{{ $location }}'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-collapse
                 class="mb-6 bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden relative z-10">
                
                {{-- Table Header --}}
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h4 class="font-black text-gray-800 flex items-center gap-2">
                        <span>📂</span> Detail: <span class="text-teal-600">{{ $location }}</span>
                    </h4>
                    <span class="text-xs font-bold text-gray-400 uppercase">{{ $orders->count() }} Sepatu dalam antrian</span>
                </div>
                
                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-white text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="text-left py-4 px-6 font-bold uppercase text-xs tracking-wider">No SPK</th>
                                <th class="text-left py-4 px-6 font-bold uppercase text-xs tracking-wider">Pelanggan</th>
                                <th class="text-left py-4 px-6 font-bold uppercase text-xs tracking-wider">Merek</th>
                                <th class="text-center py-4 px-6 font-bold uppercase text-xs tracking-wider">Tanggal Masuk</th>
                                <th class="text-center py-4 px-6 font-bold uppercase text-xs tracking-wider">Estimasi</th>
                                <th class="text-center py-4 px-6 font-bold uppercase text-xs tracking-wider">Status System</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($orders as $order)
                            <tr class="hover:bg-teal-50/50 transition-colors group">
                                <td class="py-4 px-6">
                                    <a href="{{ route('reception.show', $order->id) }}" class="font-mono text-sm font-bold text-teal-600 hover:text-teal-800 hover:underline">
                                        {{ $order->spk_number }}
                                    </a>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-gray-900">{{ $order->customer_name }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $order->shoe_brand ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="text-xs text-gray-500 font-medium font-mono">
                                        {{ \Carbon\Carbon::parse($order->entry_date)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @php
                                        $estDates = \Carbon\Carbon::parse($order->estimation_date);
                                        $isOverdue = $estDates->isPast() && $order->status !== 'SELESAI';
                                        $isToday = $estDates->isToday();
                                    @endphp
                                    <div class="flex items-center justify-center gap-1">
                                        @if($isOverdue)
                                            <span class="px-2 py-1 bg-red-50 text-red-600 rounded-md text-xs font-bold ring-1 ring-red-200">
                                                {{ $estDates->format('d/m') }}!
                                            </span>
                                        @elseif($isToday)
                                             <span class="px-2 py-1 bg-orange-50 text-orange-600 rounded-md text-xs font-bold ring-1 ring-orange-200">
                                                Hari Ini
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-xs font-mono">{{ $estDates->format('d/m') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="status-badge {{ in_array($order->status->value, ['PRODUCTION', 'ASSESSMENT', 'PREPARATION', 'SORTIR', 'QC']) ? 'orange' : 'teal' }} text-[10px]">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach
</div>
</section>
