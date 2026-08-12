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
    
    <div wire:ignore.self wire:key="prep-bulk-action-bar" class="bg-white/95 backdrop-blur-md border border-gray-200 shadow-2xl rounded-2xl p-4 w-full max-w-4xl flex flex-col md:flex-row items-center justify-between gap-4 ring-1 ring-black/5" x-data="{ taskType: 'washing' }">
        
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 bg-gray-100 px-3 py-1.5 rounded-lg">
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Terpilih</span>
                <span class="bg-gray-800 text-white px-2 py-0.5 rounded-md font-bold text-sm" x-text="selectedItems.length"></span>
            </div>
            <button @click="selectedItems = []; selectAll = false" class="text-[10px] font-bold text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors uppercase tracking-widest">
                Batal
            </button>
        </div>

        <div class="h-8 w-px bg-gray-200 hidden md:block"></div>

        <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 scrollbar-hide justify-start">
            @if($activeTab !== 'review')
                {{-- Dropdown Teknisi Cuci --}}
                <div class="relative shrink-0">
                    <select id="bulk-tech-washing" style="color: #111827 !important; background-color: #ffffff !important;" class="appearance-none bg-white border border-gray-300 text-gray-900 text-[10px] rounded-lg block w-36 pl-2.5 pr-7 py-2.5 font-bold uppercase shadow-sm cursor-pointer hover:border-blue-300">
                        <option value="" style="color: #111827 !important; background-color: #ffffff !important;">-- TEKNISI CUCI --</option>
                        @foreach($techs['washing'] ?? [] as $t)
                            <option value="{{ $t->id }}" style="color: #111827 !important; background-color: #ffffff !important;">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                {{-- Dropdown Teknisi Sol --}}
                <div class="relative shrink-0">
                    <select id="bulk-tech-sol" style="color: #111827 !important; background-color: #ffffff !important;" class="appearance-none bg-white border border-gray-300 text-gray-900 text-[10px] rounded-lg block w-36 pl-2.5 pr-7 py-2.5 font-bold uppercase shadow-sm cursor-pointer hover:border-blue-300">
                        <option value="" style="color: #111827 !important; background-color: #ffffff !important;">-- TEKNISI SOL --</option>
                        @foreach($techs['sol'] ?? [] as $t)
                            <option value="{{ $t->id }}" style="color: #111827 !important; background-color: #ffffff !important;">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                {{-- Dropdown Teknisi Upper --}}
                <div class="relative shrink-0">
                    <select id="bulk-tech-upper" style="color: #111827 !important; background-color: #ffffff !important;" class="appearance-none bg-white border border-gray-300 text-gray-900 text-[10px] rounded-lg block w-36 pl-2.5 pr-7 py-2.5 font-bold uppercase shadow-sm cursor-pointer hover:border-blue-300">
                        <option value="" style="color: #111827 !important; background-color: #ffffff !important;">-- TEKNISI UPPER --</option>
                        @foreach($techs['upper'] ?? [] as $t)
                            <option value="{{ $t->id }}" style="color: #111827 !important; background-color: #ffffff !important;">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <button type="button" @click="window.submitBulkAssign()" 
                        class="bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white px-3 py-2.5 rounded-lg text-[10px] font-black shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-1.5 active:scale-95 uppercase tracking-widest shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Assign &amp; Mulai
                </button>

                <button type="button" @click="window.submitBulkFinish()" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2.5 rounded-lg text-[10px] font-black shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-1.5 active:scale-95 uppercase tracking-widest shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Selesai Tugas
                </button>

                <button type="button" @click="window.confirmBulkCompleteAll()" 
                        class="bg-teal-600 hover:bg-teal-700 text-white px-3.5 py-2.5 rounded-lg text-[10px] font-black shadow hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-1.5 active:scale-95 uppercase tracking-widest shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Selesaikan Semua Prep
                </button>
            @else
                <button type="button" @click="confirmBulkAction('approve')" 
                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-xl text-xs font-black shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95 uppercase tracking-widest shrink-0 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Approve &amp; Sortir Terpilih
                </button>
            @endif
        </div>
    </div>
</div>
