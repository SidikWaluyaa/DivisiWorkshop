<template>
    <nav class="pwa-bottom-nav" :class="{ 'pwa-bottom-nav--hidden': isHidden }">
        <a v-for="item in navItems" 
           :key="item.id"
           :href="item.href"
           class="pwa-bottom-nav__item-mockup"
           :class="isActive(item) ? 'opacity-100' : 'opacity-50'"
           @click="handleTap($event, item)">
            <span class="relative">
                <component :is="item.icon" class="h-6 w-6 text-white" />
                <span v-if="item.badge" class="absolute -top-2 -right-2 bg-yellow-500 text-[8px] font-bold px-1 rounded-full text-white">
                    {{ item.badge }}
                </span>
            </span>
        </a>
    </nav>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, defineComponent, h } from 'vue';

// SVG Icon Components from Mockup
const IconHome = defineComponent({
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
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

const IconCustomers = defineComponent({
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
        ]);
    }
});

const IconSettings = defineComponent({
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }),
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' })
        ]);
    }
});

export default {
    name: 'BottomNav',
    setup() {
        const bouncingId = ref(null);
        const isHidden = ref(false);
        let lastScrollY = 0;
        let scrollTimeout = null;

        const navItems = [
            { id: 'home', label: 'Home', href: '/admin/customers', icon: IconHome, matchPatterns: ['/admin/customers', '/admin/orders'] },
            { id: 'charts', label: 'Charts', href: '/admin/cs/analytics', icon: IconCharts, matchPatterns: ['/admin/cs/analytics', '/admin/cs/dashboard'] },
            { id: 'customers', label: 'Customers', href: '/admin/cs/leads/konsultasi', icon: IconCustomers, badge: 16, matchPatterns: ['/admin/cs/leads/konsultasi'] },
            { id: 'settings', label: 'Settings', href: '/profile', icon: IconSettings, matchPatterns: ['/profile'] },
        ];

        const currentPath = computed(() => window.location.pathname);

        function isActive(item) {
            return item.matchPatterns.some(pattern => currentPath.value.startsWith(pattern));
        }

        function handleTap(event, item) {
            bouncingId.value = item.id;
            setTimeout(() => { bouncingId.value = null; }, 300);
        }

        // Hide bottom nav on scroll down, show on scroll up
        function handleScroll() {
            const currentScrollY = window.scrollY;
            isHidden.value = currentScrollY > lastScrollY && currentScrollY > 100;
            lastScrollY = currentScrollY;

            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => { isHidden.value = false; }, 1500);
        }

        onMounted(() => {
            window.addEventListener('scroll', handleScroll, { passive: true });
        });

        onUnmounted(() => {
            window.removeEventListener('scroll', handleScroll);
            clearTimeout(scrollTimeout);
        });

        return { navItems, isActive, handleTap, bouncingId, isHidden };
    }
};
</script>
