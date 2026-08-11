<div class="relative inline-block text-left" x-data="{ openDropdown: false }">
    {{-- Bell Icon in Header --}}
    <button @click="openDropdown = !openDropdown" type="button" 
            class="relative p-2 text-white/90 hover:text-white rounded-xl hover:bg-white/10 transition-all focus:outline-none flex items-center justify-center cursor-pointer" 
            title="Fitur Baru & Pengumuman Sistem">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-black text-white ring-2 ring-teal-700 animate-pulse">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Slide-down Dropdown Menu (0ms Alpine Instant Toggle) --}}
    <div x-show="openDropdown" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 transform -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 transform -translate-y-2"
         @click.away="openDropdown = false"
         class="absolute right-0 mt-3 w-80 sm:w-96 rounded-2xl bg-white shadow-2xl ring-1 ring-black/10 z-[9999] overflow-hidden border border-gray-100 divide-y divide-gray-100 text-left whitespace-normal"
         style="display: none; min-width: 320px; max-width: 380px;">
        
        <div class="px-4 py-3.5 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-lg">🚀</span>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-white">Apa Yang Baru</h3>
                    <p class="text-[10px] text-slate-300">Pembaruan & Fitur Sistem Workshop</p>
                </div>
            </div>
            @if(in_array(auth()->user()->role ?? '', ['admin', 'owner']) || str_contains(auth()->user()->access_rights ?? '', '"admin"'))
                <a href="{{ route('admin.announcements.index') }}" class="text-[10px] bg-teal-600 hover:bg-teal-500 text-white px-2.5 py-1 rounded-lg font-bold transition-all shadow-sm">
                    ⚙️ Kelola
                </a>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 scrollbar-thin scrollbar-thumb-gray-300">
            @forelse($announcements as $ann)
                @php $isRead = $ann->reads->isNotEmpty(); @endphp
                <div wire:click="openDetail({{ $ann->id }})" 
                     @click="openDropdown = false"
                     class="p-4 hover:bg-teal-50/60 cursor-pointer transition-colors relative {{ !$isRead ? 'bg-amber-50/40' : '' }}">
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $ann->category === 'FEATURE_UPDATE' ? 'bg-teal-100 text-teal-800' : ($ann->category === 'MAINTENANCE' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ $ann->version }} • {{ str_replace('_', ' ', $ann->category) }}
                        </span>
                        <span class="text-[9px] font-bold text-gray-400">
                            {{ $ann->published_at ? $ann->published_at->diffForHumans() : $ann->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <h4 class="text-xs font-bold text-gray-900 leading-snug flex items-start gap-1.5">
                        @if(!$isRead)
                            <span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0 mt-1 animate-ping"></span>
                        @endif
                        <span>{{ $ann->title }}</span>
                    </h4>
                    @if($ann->summary)
                        <p class="text-[11px] text-gray-600 line-clamp-2 mt-1 font-normal leading-relaxed">
                            {{ $ann->summary }}
                        </p>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xs font-bold">Belum ada pengumuman baru</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Toast Auto-Popup (Bottom Right) --}}
    @if($showToast && $selectedAnnouncement)
        <div class="fixed bottom-6 right-6 z-[9999] max-w-sm w-full bg-slate-900 text-white rounded-2xl shadow-2xl border border-teal-500/30 p-4 animate-in slide-in-from-bottom-5 duration-300 text-left whitespace-normal">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-teal-500/20 text-teal-400 border border-teal-500/30 flex items-center justify-center text-xl flex-shrink-0">
                        🎉
                    </div>
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-teal-400">Fitur Baru Tersedia ({{ $selectedAnnouncement['version'] ?? 'v1.0.0' }})</span>
                        <h4 class="text-xs font-bold text-white leading-tight mt-0.5 line-clamp-1">
                            {{ $selectedAnnouncement['title'] }}
                        </h4>
                    </div>
                </div>
                <button wire:click="closeToast" class="text-gray-400 hover:text-white transition-colors p-1">
                    ✕
                </button>
            </div>
            @if(!empty($selectedAnnouncement['summary']))
                <p class="text-[11px] text-gray-300 mt-2.5 line-clamp-2 leading-relaxed">
                    {{ $selectedAnnouncement['summary'] }}
                </p>
            @endif
            <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between">
                <button wire:click="openDetail({{ $selectedAnnouncement['id'] }})" class="text-xs font-black text-teal-400 hover:text-teal-300 transition-colors flex items-center gap-1">
                    <span>Baca Selengkapnya</span> &rarr;
                </button>
                <button wire:click="closeToast" class="text-[10px] font-bold text-gray-400 hover:text-gray-200">
                    Tutup
                </button>
            </div>
        </div>
    @endif

    {{-- Detail Modal "Apa Yang Baru / Release Notes" --}}
    @if($showModal && $selectedAnnouncement)
        <div class="fixed inset-0 z-[10000] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" wire:click="closeModal"></div>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <div class="bg-gradient-to-r from-slate-900 to-teal-950 p-6 text-white relative">
                        <button wire:click="closeModal" class="absolute top-4 right-4 text-white/60 hover:text-white text-lg">
                            ✕
                        </button>
                        <span class="px-2.5 py-1 rounded bg-teal-500/20 text-teal-300 text-[10px] font-black uppercase tracking-widest border border-teal-500/30">
                            {{ $selectedAnnouncement['version'] ?? 'v1.0.0' }} • {{ str_replace('_', ' ', $selectedAnnouncement['category'] ?? 'UPDATE') }}
                        </span>
                        <h2 class="text-xl font-black mt-3 leading-snug">
                            {{ $selectedAnnouncement['title'] }}
                        </h2>
                        <p class="text-xs text-slate-300 mt-1">
                            Dirilis pada: {{ !empty($selectedAnnouncement['published_at']) ? \Carbon\Carbon::parse($selectedAnnouncement['published_at'])->translatedFormat('d F Y H:i') : now()->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                        @if(!empty($selectedAnnouncement['summary']))
                            <div class="p-3.5 bg-teal-50/60 border border-teal-100 rounded-2xl text-xs font-semibold text-teal-900 leading-relaxed">
                                💡 {{ $selectedAnnouncement['summary'] }}
                            </div>
                        @endif

                        <div class="text-xs font-normal text-gray-700 leading-relaxed space-y-2 prose max-w-none">
                            {!! nl2br(e($selectedAnnouncement['description'] ?? '')) !!}
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end">
                        <button wire:click="closeModal" type="button" 
                                class="w-full sm:w-auto px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-black rounded-xl text-xs uppercase tracking-widest transition-all shadow-md">
                            👍 Saya Mengerti & Paham
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
