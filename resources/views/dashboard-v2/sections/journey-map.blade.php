{{-- Customer Journey Pipeline --}}
<section class="relative bg-white rounded-3xl p-8 shadow-xl overflow-hidden border border-gray-100 animate-fade-in-up delay-200">
    <div class="absolute inset-0 bg-gradient-to-br from-[#22AF85]/3 via-transparent to-[#FFC232]/3"></div>

    <div class="relative z-10">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight">Customer Journey Pipeline</h2>
                    <p class="text-xs text-gray-400 font-medium">Alur real-time sepatu dari CS hingga Selesai</p>
                </div>
            </div>
            <a href="{{ route('workshop.dashboard-v2') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#22AF85]/5 text-[#22AF85] rounded-xl font-bold text-xs hover:bg-[#22AF85]/10 transition-all border border-[#22AF85]/10">
                Detail Workshop →
            </a>
        </div>

        {{-- Pipeline Flow --}}
        <div class="relative py-4">

            <div class="relative z-10 grid grid-cols-7 gap-3">
                @foreach($journey as $index => $node)
                <div class="flex flex-col items-center group cursor-default" x-data="{ open: false }">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl shadow-lg mb-3 relative border-2 transition-all hover:shadow-xl hover:scale-110 cursor-pointer outline-none focus:ring-2 focus:ring-offset-2"
                         @click.stop="open = !open"
                         style="background: {{ $node['color'] }}15; border-color: {{ $node['color'] }}40; --tw-ring-color: {{ $node['color'] }}60">
                        <span>{{ $node['icon'] }}</span>
                        <div id="journey-count-{{ $index }}" class="absolute -top-2.5 -right-2.5 min-w-[24px] h-[24px] rounded-full flex items-center justify-center text-[11px] font-black text-white shadow-lg px-1"
                             style="background: {{ $node['color'] }}; {{ $node['count'] == 0 ? 'display:none' : '' }}">
                            {{ $node['count'] }}
                        </div>
                        <div id="journey-zero-{{ $index }}" class="absolute -top-2 -right-2 w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold text-gray-400 bg-gray-100 border border-gray-200"
                             style="{{ $node['count'] > 0 ? 'display:none' : '' }}">
                            0
                        </div>

                        {{-- Journey Tooltip --}}
                        <div x-show="open" x-cloak x-transition @click.away="open = false" class="absolute z-[100] w-48 p-3 bg-white rounded-xl shadow-2xl border border-gray-100 left-1/2 -translate-x-1/2 bottom-full mb-4 whitespace-normal text-left">
                            <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-b border-r border-gray-100 rotate-45"></div>
                            <div class="relative">
                                <div class="text-[9px] font-black uppercase tracking-widest mb-1" style="color: {{ $node['color'] }}">Tahap: {{ $node['stage'] }}</div>
                                <div class="text-[11px] text-gray-600 leading-tight font-medium">
                                    @if($node['stage'] == 'CS Terima') Total leads aktif menunggu konsultasi.
                                    @elseif($node['stage'] == 'Gudang Masuk') Sepatu baru diterima di hub logistik.
                                    @elseif($node['stage'] == 'Assessment') Pengecekan kondisi oleh tim ahli.
                                    @elseif($node['stage'] == 'Preparation') Tahap cuci/pembersihan awal.
                                    @elseif($node['stage'] == 'Production') Sedang dalam proses reparasi utama.
                                    @elseif($node['stage'] == 'QC') Pengecekan kualitas akhir.
                                    @elseif($node['stage'] == 'Selesai') Menunggu pengambilan/pengiriman.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-gray-500 text-center leading-tight group-hover:text-gray-800 transition-colors">{{ $node['stage'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Summary --}}
        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
            <div class="text-sm text-gray-400 font-medium">
                Total di pipeline: <span id="journey-total" class="font-black text-[#22AF85] text-lg">{{ collect($journey)->sum('count') }}</span> sepatu
            </div>
            <div class="flex items-center gap-3">
                @foreach($journey as $node)
                @if($node['count'] > 0)
                <span class="text-[9px] font-bold text-gray-400">{{ $node['stage'] }}: <span class="text-gray-700">{{ $node['count'] }}</span></span>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
