<template>
    <Transition name="slide-down">
        <div v-if="isOffline" class="pwa-offline-banner">
            <div class="pwa-offline-banner__dot"></div>
            <span class="pwa-offline-banner__text">Anda sedang offline — data tersimpan lokal</span>
            <button @click="dismiss" class="pwa-offline-banner__close" aria-label="Tutup">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </Transition>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';

export default {
    name: 'OfflineBanner',
    setup() {
        const isOffline = ref(!navigator.onLine);
        const dismissed = ref(false);

        function goOnline() {
            isOffline.value = false;
            dismissed.value = false;
        }

        function goOffline() {
            if (!dismissed.value) {
                isOffline.value = true;
            }
        }

        function dismiss() {
            isOffline.value = false;
            dismissed.value = true;
            // Reset dismiss after 30 seconds so it shows again if still offline
            setTimeout(() => { dismissed.value = false; }, 30000);
        }

        onMounted(() => {
            window.addEventListener('online', goOnline);
            window.addEventListener('offline', goOffline);
        });

        onUnmounted(() => {
            window.removeEventListener('online', goOnline);
            window.removeEventListener('offline', goOffline);
        });

        return { isOffline, dismiss };
    }
};
</script>
