<div x-data="{ 
    showFilters: false,
    search: '{{ request('search') }}',
    workStatus: '{{ request('work_status', 'all') }}',
    priority: '{{ request('priority', 'all') }}',
    technician: '{{ request('technician', 'all') }}',
    
    applyFilters() {
        const params = new URLSearchParams(window.location.search);
        
        // Update search param
        if (this.search) {
            params.set('search', this.search);
        } else {
            params.delete('search');
        }
        
        // Update work_status param
        if (this.workStatus && this.workStatus !== 'all') {
            params.set('work_status', this.workStatus);
        } else {
            params.delete('work_status');
        }
        
        // Update priority param
        if (this.priority && this.priority !== 'all') {
            params.set('priority', this.priority);
        } else {
            params.delete('priority');
        }
        
        // Update technician param
        if (this.technician && this.technician !== 'all') {
            params.set('technician', this.technician);
        } else {
            params.delete('technician');
        }
        
        // Keep the current tab
        const currentTab = new URLSearchParams(window.location.search).get('tab');
        if (currentTab) {
            params.set('tab', currentTab);
        }
        
        // Redirect with new params
        window.location.search = params.toString();
    },
    
    clearFilters() {
        const params = new URLSearchParams();
        const currentTab = new URLSearchParams(window.location.search).get('tab');
        if (currentTab) {
            params.set('tab', currentTab);
        }
        window.location.search = params.toString();
    },
    
    getActiveFilterCount() {
        let count = 0;
        if (this.search) count++;
        if (this.workStatus && this.workStatus !== 'all') count++;
        if (this.priority && this.priority !== 'all') count++;
        if (this.technician && this.technician !== 'all') count++;
        return count;
    }
}" class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
    
    {{-- Compact Filter Bar --}}
    <div class="flex flex-col md:flex-row gap-3">
        {{-- Search Input --}}
        <div class="flex-1">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       x-model="search"
                       placeholder="Cari SPK, Customer, Brand, HP..."
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm"
                       @keyup.enter="applyFilters()">
            </div>
        </div>

        {{-- Status Filter --}}
        <div class="w-full md:w-48">
            <select x-model="workStatus"
                    {{ request('tab') === 'all' ? 'disabled' : '' }}
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm {{ request('tab') === 'all' ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                <option value="all">{{ request('tab') === 'all' ? '📊 N/A (Tab All)' : '📊 Semua Status' }}</option>
                <option value="not_started">⏸️ Belum Start</option>
                <option value="in_progress">⚙️ Sedang Dikerjakan</option>
                <option value="completed">✅ Selesai</option>
            </select>
        </div>

        {{-- Toggle Advanced Filters --}}
        <button @click="showFilters = !showFilters"
                type="button"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
            </svg>
            <span x-text="showFilters ? 'Sembunyikan Filter' : 'Filter Lanjutan'">Filter Lanjutan</span>
        </button>

        {{-- Apply/Clear Buttons --}}
        <div class="flex gap-2">
            <button @click="applyFilters()"
                    type="button"
                    class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white rounded-lg text-sm font-semibold transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Cari
            </button>
            <button @click="clearFilters()"
                    type="button"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-semibold transition-colors">
                Reset
            </button>
        </div>
    </div>

    {{-- Advanced Filters (Collapsible) --}}
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-3"
         style="display: none;">
        
        {{-- Priority Filter --}}
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">⚡ Prioritas</label>
            <select x-model="priority"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm">
                <option value="all">Semua</option>
                <option value="urgent">Prioritas/Urgent</option>
                <option value="regular">Regular</option>
            </select>
        </div>

        {{-- Technician Filter (if provided) --}}
        @if(isset($technicians) && $technicians->count() > 0)
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">👤 Teknisi</label>
            <select x-model="technician"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm">
                <option value="all">Semua Teknisi</option>
                @foreach($technicians as $tech)
                    <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Active Filters Count --}}
        <div class="flex items-end">
            <div class="text-xs text-gray-500">
                <span class="font-semibold">Filter Aktif:</span>
                <span class="ml-1 px-2 py-1 bg-teal-100 text-teal-700 rounded-full font-bold"
                      x-text="getActiveFilterCount()">0</span>
            </div>
        </div>
    </div>
</div>
