@props([
    'activeTab',
    'techs'
])

<div x-show="selectedItems.length > 0" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="translate-y-full opacity-0 scale-95"
     x-transition:enter-end="translate-y-0 opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-y-0 opacity-100 scale-100"
     x-transition:leave-end="translate-y-full opacity-0 scale-95"
     class="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4"
     style="display: none;">
    
    <div wire:ignore.self wire:key="prep-bulk-action-bar" 
         class="bg-slate-900/95 text-white backdrop-blur-xl border border-slate-700/80 shadow-2xl rounded-3xl p-4 w-full max-w-5xl flex flex-wrap items-center justify-between gap-3 ring-1 ring-white/10">
        
        {{-- Selected Counter & Cancel Button --}}
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 px-3.5 py-1.5 rounded-2xl">
                <span class="text-[11px] font-bold uppercase tracking-wider">Terpilih</span>
                <span class="bg-emerald-500 text-slate-950 px-2 py-0.5 rounded-full font-black text-xs" x-text="selectedItems.length"></span>
            </div>
            <button type="button" @click="selectedItems = []; selectAll = false" 
                    class="text-xs font-bold text-slate-400 hover:text-red-400 hover:bg-white/10 px-3 py-1.5 rounded-xl transition-all uppercase tracking-wider">
                ✕ Batal
            </button>
        </div>

        {{-- Action Buttons & Selectors (Flex-wrap layout - ZERO horizontal scroll) --}}
        <div class="flex flex-wrap items-center gap-2.5">
            @if($activeTab !== 'review')
                {{-- 1. Auto Assign Button (Balanced Round Robin) --}}
                <button type="button" wire:click="autoAssignSelectedPrep" 
                        class="bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-600 hover:from-indigo-600 hover:to-pink-700 text-white px-4 py-2 rounded-xl text-xs font-black shadow-lg transition-all flex items-center gap-1.5 active:scale-95 uppercase tracking-wider cursor-pointer"
                        title="Bagi SPK terpilih ke teknisi cuci secara adil & merata">
                    <span>🤖 Auto Assign</span>
                </button>

                <div class="h-6 w-px bg-slate-700 mx-1 hidden sm:block"></div>

                {{-- 2. Dropdown Teknisi Cuci --}}
                <div class="relative">
                    <select id="bulk-tech-washing" 
                            class="appearance-none bg-slate-800 border border-slate-600 text-white text-xs rounded-xl block pl-3 pr-8 py-2 font-bold uppercase shadow-inner cursor-pointer focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                        <option value="">-- TEKNISI CUCI --</option>
                        @foreach($techs['washing'] ?? [] as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                {{-- 3. Assign & Mulai Button --}}
                <button type="button" @click="window.submitBulkAssign()" 
                        class="bg-[#FFC232] hover:bg-amber-400 text-slate-950 px-4 py-2 rounded-xl text-xs font-black shadow-lg transition-all flex items-center gap-1.5 active:scale-95 uppercase tracking-wider cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Assign &amp; Mulai
                </button>

                {{-- 4. Selesaikan Semua Prep Button --}}
                <button type="button" @click="window.confirmBulkCompleteAll()" 
                        class="bg-[#22AF85] hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-black shadow-lg transition-all flex items-center gap-1.5 active:scale-95 uppercase tracking-wider cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Selesaikan Semua
                </button>
            @else
                <button type="button" @click="confirmBulkAction('approve')" 
                        class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-6 py-2.5 rounded-xl text-xs font-black shadow-lg transition-all flex items-center gap-2 active:scale-95 uppercase tracking-wider cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Approve &amp; Transfer ke Sortir
                </button>
            @endif
        </div>
    </div>
</div>
