<template>
    <div v-if="!isWorkshopLayout">
        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- BOTTOM NAVIGATION BAR (Mobile Viewport Only)                   -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <nav class="pwa-bottom-nav" :class="{ 'pwa-bottom-nav--hidden': isHidden }">
            <template v-for="item in currentNavItems" :key="item.id">
                <!-- Action Button (e.g. Drawer Toggle) -->
                <button v-if="item.isAction"
                        type="button"
                        class="pwa-bottom-nav__item-mockup"
                        :class="isDrawerOpen ? 'opacity-100 scale-105' : 'opacity-70 hover:opacity-100'"
                        @click="handleAction(item)">
                    <span class="relative flex flex-col items-center">
                        <component :is="item.icon" class="h-6 w-6 text-white transition-transform duration-200" :class="{ 'rotate-90': isDrawerOpen }" />
                        <span class="text-[10px] font-black text-white/90 mt-1 uppercase tracking-tight">{{ item.label }}</span>
                    </span>
                </button>

                <!-- Navigation Link -->
                <a v-else
                   :href="item.href"
                   class="pwa-bottom-nav__item-mockup"
                   :class="isActive(item) ? 'opacity-100 font-black' : 'opacity-70 hover:opacity-100'"
                   @click="handleTap($event, item)">
                    <span class="relative flex flex-col items-center">
                        <div class="relative">
                            <component :is="item.icon" class="h-6 w-6 text-white" />
                            <span v-if="item.badge && item.badge > 0" 
                                  class="absolute -top-1.5 -right-2.5 bg-[#FFC232] text-slate-950 text-[9px] font-black px-1.5 py-0.2 rounded-full border border-white shadow-sm animate-pulse">
                                {{ item.badge > 99 ? '99+' : item.badge }}
                            </span>
                        </div>
                        <span class="text-[10px] font-bold text-white/90 mt-1 uppercase tracking-tight" :class="{ 'text-[#FFC232] font-black': isActive(item) }">
                            {{ item.label }}
                        </span>
                        <div v-if="isActive(item)" class="w-1.5 h-1.5 bg-[#FFC232] rounded-full mt-0.5"></div>
                    </span>
                </a>
            </template>
        </nav>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- SLIDE-UP GLASSMORPHISM DRAWER (CS Additional Features)         -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <transition name="drawer-fade">
            <div v-if="isDrawerOpen" 
                 class="fixed inset-0 z-[10000] bg-black/60 backdrop-blur-sm transition-opacity"
                 @click="isDrawerOpen = false"></div>
        </transition>

        <transition name="drawer-slide">
            <div v-if="isDrawerOpen" 
                 class="fixed bottom-0 left-0 right-0 z-[10001] bg-white dark:bg-slate-900 rounded-t-[2.5rem] p-6 pb-10 shadow-2xl border-t border-slate-100 dark:border-slate-800 text-slate-900 dark:text-white max-w-lg mx-auto">
                
                <!-- Drag Handle Bar -->
                <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-5 cursor-pointer" @click="isDrawerOpen = false"></div>

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-[#22B086] flex items-center justify-center font-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white">Menu Divisi CS</h3>
                            <p class="text-xs text-slate-400 font-medium">Akses cepat fitur & analisis CS</p>
                        </div>
                    </div>
                    <button @click="isDrawerOpen = false" 
                            class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Menu Grid -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    
                    <!-- 1. Forecasting -->
                    <a href="/cs/forecasting" 
                       class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 border border-slate-100 dark:border-slate-700/60 transition-all duration-200 group">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white mb-0.5">Forecasting</h4>
                        <p class="text-[10px] text-slate-400 font-medium leading-tight">Target & Proyeksi Revenue</p>
                    </a>

                    <!-- 2. After Photo Gallery -->
                    <a href="/cs/after-photos" 
                       class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 border border-slate-100 dark:border-slate-700/60 transition-all duration-200 group">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white mb-0.5">Galeri Foto CX</h4>
                        <p class="text-[10px] text-slate-400 font-medium leading-tight">Foto After Pengerjaan</p>
                    </a>

                    <!-- 3. KPI Leaderboard -->
                    <a href="/cs/kpi-leaderboard" 
                       class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 hover:bg-amber-50 dark:hover:bg-amber-950/30 border border-slate-100 dark:border-slate-700/60 transition-all duration-200 group">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white mb-0.5">KPI Leaderboard</h4>
                        <p class="text-[10px] text-slate-400 font-medium leading-tight">Peringkat & Hasil CS</p>
                    </a>

                    <!-- 4. Lost Leads -->
                    <a href="/cs/leads/lost" 
                       class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 hover:bg-rose-50 dark:hover:bg-rose-950/30 border border-slate-100 dark:border-slate-700/60 transition-all duration-200 group">
                        <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </div>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white mb-0.5">Lost Leads</h4>
                        <p class="text-[10px] text-slate-400 font-medium leading-tight">Analisis Lead Batal</p>
                    </a>

                </div>

                <!-- Profile Footer Action -->
                <a href="/profile" 
                   class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700/80 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Pengaturan Akun & Sandi</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

            </div>
        </transition>
    </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, defineComponent, h } from 'vue';

// ── SVG Icon Components ──────────────────────────────────────────────
const IconPipeline = defineComponent({
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2' })
        ]);
    }
});

const IconPending = defineComponent({
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' })
        ]);
    }
});

const IconSpk = defineComponent({
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
        ]);
    }
});

const IconCharts = defineComponent({
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' })
        ]);
    }
});

const IconMenu = defineComponent({
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M4 6h16M4 12h16m-7 6h7' })
        ]);
    }
});

const IconHome = defineComponent({
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
        ]);
    }
});

export default {
    name: 'BottomNav',
    setup() {
        const bouncingId = ref(null);
        const isHidden = ref(false);
        const isDrawerOpen = ref(false);
        const pendingSpkCount = ref(0);
        let lastScrollY = 0;
        let scrollTimeout = null;

        const currentPath = computed(() => window.location.pathname);
        
        // Detect if current path is under CS Module
        const isCsRoute = computed(() => {
            return currentPath.value.startsWith('/cs') || currentPath.value.includes('/cs/');
        });

        // Detect if on workshop layout (workshop already has its own Blade bottom nav)
        const isWorkshopLayout = computed(() => {
            return currentPath.value.startsWith('/workshop') || 
                   currentPath.value.startsWith('/manifest') ||
                   currentPath.value.startsWith('/production') ||
                   currentPath.value.startsWith('/sortir') ||
                   currentPath.value.startsWith('/preparation') ||
                   currentPath.value.startsWith('/qc') ||
                   currentPath.value.startsWith('/surat-jalan') ||
                   currentPath.value.startsWith('/revision');
        });

        // Dynamic Nav Items for CS Module
        const csNavItems = computed(() => [
            { id: 'pipeline', label: 'Pipeline', href: '/cs/dashboard', icon: IconPipeline, matchPatterns: ['/cs/dashboard', '/cs/leads'] },
            { id: 'pending', label: 'Pending', href: '/cs/pending-monitoring', icon: IconPending, badge: pendingSpkCount.value, matchPatterns: ['/cs/pending-monitoring'] },
            { id: 'spk', label: 'Data SPK', href: '/cs/spk-data', icon: IconSpk, matchPatterns: ['/cs/spk-data'] },
            { id: 'analytics', label: 'Analytics', href: '/cs/analytics', icon: IconCharts, matchPatterns: ['/cs/analytics', '/cs/kpi'] },
            { id: 'menu', label: 'Menu', isAction: true, icon: IconMenu, matchPatterns: [] },
        ]);

        // Fallback General Nav Items (for non-CS & non-Workshop)
        const generalNavItems = computed(() => [
            { id: 'home', label: 'Home', href: '/admin/customers', icon: IconHome, matchPatterns: ['/admin/customers', '/admin/orders'] },
            { id: 'cs-pipeline', label: 'CS Hub', href: '/cs/dashboard', icon: IconPipeline, matchPatterns: ['/cs'] },
            { id: 'analytics', label: 'Analytics', href: '/cs/analytics', icon: IconCharts, matchPatterns: ['/cs/analytics', '/admin/cs'] },
            { id: 'menu', label: 'Menu', isAction: true, icon: IconMenu, matchPatterns: [] },
        ]);

        const currentNavItems = computed(() => {
            if (isCsRoute.value) {
                return csNavItems.value;
            }
            return generalNavItems.value;
        });

        function isActive(item) {
            if (!item.matchPatterns || item.matchPatterns.length === 0) return false;
            return item.matchPatterns.some(pattern => currentPath.value.startsWith(pattern));
        }

        function handleTap(event, item) {
            bouncingId.value = item.id;
            setTimeout(() => { bouncingId.value = null; }, 300);
        }

        function handleAction(item) {
            if (item.id === 'menu') {
                isDrawerOpen.value = !isDrawerOpen.value;
            }
        }

        // Fetch Live Badge Counts
        async function fetchBadgeCounts() {
            try {
                const response = await fetch('/cs/api/badge-counts');
                if (response.ok) {
                    const json = await response.json();
                    if (json.status === 'success' && typeof json.pending_spk !== 'undefined') {
                        pendingSpkCount.value = json.pending_spk;
                    }
                }
            } catch (e) {
                // Silently ignore network failures on PWA
            }
        }

        // Hide bottom nav on scroll down, show on scroll up
        function handleScroll() {
            const currentScrollY = window.scrollY;
            isHidden.value = currentScrollY > lastScrollY && currentScrollY > 120 && !isDrawerOpen.value;
            lastScrollY = currentScrollY;

            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => { isHidden.value = false; }, 1200);
        }

        onMounted(() => {
            window.addEventListener('scroll', handleScroll, { passive: true });
            fetchBadgeCounts();
            // Polling badge counts every 45s
            setInterval(fetchBadgeCounts, 45000);
        });

        onUnmounted(() => {
            window.removeEventListener('scroll', handleScroll);
            clearTimeout(scrollTimeout);
        });

        return { 
            currentNavItems, 
            isActive, 
            handleTap, 
            handleAction, 
            bouncingId, 
            isHidden,
            isDrawerOpen,
            isWorkshopLayout
        };
    }
};
</script>

<style scoped>
/* Transitions for Drawer */
.drawer-fade-enter-active,
.drawer-fade-leave-active {
    transition: opacity 0.25s ease;
}
.drawer-fade-enter-from,
.drawer-fade-leave-to {
    opacity: 0;
}

.drawer-slide-enter-active,
.drawer-slide-leave-active {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.drawer-slide-enter-from,
.drawer-slide-leave-to {
    transform: translateY(100%);
}
</style>
