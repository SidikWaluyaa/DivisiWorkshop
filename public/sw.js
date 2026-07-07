/**
 * Service Worker — Manajemen ShoeWorkshop PWA
 * Strategy: Network First, Cache Fallback (Stale-While-Revalidate for assets)
 */

const CACHE_VERSION = 'msw-v1';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const DYNAMIC_CACHE = `${CACHE_VERSION}-dynamic`;

// Assets to pre-cache on install
const PRECACHE_URLS = [
    '/offline.html',
    '/images/logo.png',
    '/manifest.json',
];

// URL patterns that should use cache-first strategy (static assets)
const CACHE_FIRST_PATTERNS = [
    /\.(?:css|js|woff2?|ttf|eot|svg|png|jpe?g|gif|ico|webp)$/i,
    /^https:\/\/fonts\.(googleapis|bunny|gstatic)\.net\//,
    /^https:\/\/cdn\.jsdelivr\.net\//,
];

// URL patterns that should NEVER be cached
const NO_CACHE_PATTERNS = [
    /\/livewire\//,
    /\/_debugbar\//,
    /\/api\//,
    /\/login/,
    /\/logout/,
    /\/sanctum\//,
    /csrf-cookie/,
];

// Install: Pre-cache essential assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

// Activate: Clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => key !== STATIC_CACHE && key !== DYNAMIC_CACHE)
                    .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

// Fetch: Network first for pages, cache first for assets
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') return;

    // Skip no-cache patterns
    if (NO_CACHE_PATTERNS.some((pattern) => pattern.test(url.href))) return;

    // Cache-first for static assets
    if (CACHE_FIRST_PATTERNS.some((pattern) => pattern.test(url.href))) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Network-first for HTML pages (admin routes)
    if (request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(networkFirst(request));
        return;
    }

    // Default: Network first
    event.respondWith(networkFirst(request));
});

/**
 * Cache-first strategy: Try cache, fallback to network
 */
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('', { status: 408, statusText: 'Offline' });
    }
}

/**
 * Network-first strategy: Try network, fallback to cache
 */
async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;

        // Fallback to offline page for HTML requests
        if (request.headers.get('accept')?.includes('text/html')) {
            return caches.match('/offline.html');
        }

        return new Response('', { status: 408, statusText: 'Offline' });
    }
}
