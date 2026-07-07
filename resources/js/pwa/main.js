/**
 * PWA Vue.js Entry Point — Manajemen ShoeWorkshop
 * 
 * Mounts lightweight Vue micro-components onto existing Blade layout.
 * Only active on mobile viewports (< 1024px) for BottomNav.
 * OfflineBanner and InstallPrompt are active on all viewports.
 */
import { createApp, h } from 'vue';
import BottomNav from './BottomNav.vue';
import OfflineBanner from './OfflineBanner.vue';
import InstallPrompt from './InstallPrompt.vue';

// Root PWA component that composes all micro-components
const PwaApp = {
    name: 'PwaApp',
    components: { BottomNav, OfflineBanner, InstallPrompt },
    render() {
        return h('div', { id: 'pwa-root-inner' }, [
            h(OfflineBanner),
            h(InstallPrompt),
            h(BottomNav),
        ]);
    }
};

// Mount when DOM is ready
function initPwa() {
    const mountPoint = document.getElementById('pwa-mount');
    if (!mountPoint) return;

    const app = createApp(PwaApp);
    app.mount(mountPoint);
}

// Register Service Worker
function registerServiceWorker() {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then((registration) => {
                    console.log('[PWA] SW registered:', registration.scope);

                    // Check for updates periodically (every 60 minutes)
                    setInterval(() => {
                        registration.update();
                    }, 60 * 60 * 1000);
                })
                .catch((error) => {
                    console.warn('[PWA] SW registration failed:', error);
                });
        });
    }
}

// Initialize
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPwa);
} else {
    initPwa();
}

registerServiceWorker();
