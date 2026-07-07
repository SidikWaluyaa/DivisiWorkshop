<template>
    <Transition name="fade-up">
        <div v-if="showPrompt" class="pwa-install-overlay" @click.self="dismissTemporary">
            <div class="pwa-install-modal">
                <div class="pwa-install-modal__icon">
                    <img src="/images/logo.png" alt="Manajemen SW" width="64" height="64">
                </div>
                <h3 class="pwa-install-modal__title">Install Manajemen SW</h3>
                <p class="pwa-install-modal__desc">
                    Akses lebih cepat langsung dari homescreen HP Anda. Tanpa perlu buka browser!
                </p>
                <div class="pwa-install-modal__features">
                    <div class="pwa-install-modal__feature">
                        <svg width="20" height="20" fill="none" stroke="#22B086" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Akses Instan</span>
                    </div>
                    <div class="pwa-install-modal__feature">
                        <svg width="20" height="20" fill="none" stroke="#22B086" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636a9 9 0 010 12.728M5.636 5.636a9 9 0 000 12.728" />
                        </svg>
                        <span>Mode Offline</span>
                    </div>
                    <div class="pwa-install-modal__feature">
                        <svg width="20" height="20" fill="none" stroke="#22B086" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>Seperti Aplikasi</span>
                    </div>
                </div>
                <div class="pwa-install-modal__actions">
                    <button @click="installApp" class="pwa-install-modal__btn pwa-install-modal__btn--primary">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Install Sekarang
                    </button>
                    <button @click="dismissPermanent" class="pwa-install-modal__btn pwa-install-modal__btn--secondary">
                        Nanti Saja
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script>
import { ref, onMounted } from 'vue';

export default {
    name: 'InstallPrompt',
    setup() {
        const showPrompt = ref(false);
        let deferredPrompt = null;

        onMounted(() => {
            // Check if already installed or user dismissed
            const isInstalled = window.matchMedia('(display-mode: standalone)').matches;
            const isDismissed = localStorage.getItem('pwa-install-dismissed');
            
            // Check temporary dismissal (throttle)
            const dismissUntil = localStorage.getItem('pwa-install-dismissed-until');
            const isTempDismissed = dismissUntil && Date.now() < parseInt(dismissUntil, 10);

            if (isInstalled || isDismissed || isTempDismissed) return;

            // Listen for the beforeinstallprompt event
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                // Show after a 10-second delay so user has time to see the page first
                setTimeout(() => { 
                    const stillDismissed = localStorage.getItem('pwa-install-dismissed');
                    const stillTempDismissed = localStorage.getItem('pwa-install-dismissed-until');
                    const isStillTempDismissed = stillTempDismissed && Date.now() < parseInt(stillTempDismissed, 10);
                    
                    if (!stillDismissed && !isStillTempDismissed) {
                        showPrompt.value = true; 
                    }
                }, 10000);
            });
        });

        async function installApp() {
            if (!deferredPrompt) return;

            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;

            if (outcome === 'accepted') {
                showPrompt.value = false;
            }

            deferredPrompt = null;
        }

        function dismissTemporary() {
            showPrompt.value = false;
            // Throttle showing the prompt again for 7 days
            const sevenDays = 7 * 24 * 60 * 60 * 1000;
            localStorage.setItem('pwa-install-dismissed-until', (Date.now() + sevenDays).toString());
        }

        function dismissPermanent() {
            showPrompt.value = false;
            localStorage.setItem('pwa-install-dismissed', Date.now().toString());
        }

        return { showPrompt, installApp, dismissTemporary, dismissPermanent };
    }
};
</script>
