/**
 * Souly Digital Citadel - Sovereign Service Worker (PWA Engine)
 * Version: 1.0.0
 * Architecture: Non-blocking, Stale-While-Revalidate for Static Assets, Network-First for Dynamics
 */

const CACHE_NAME = 'souly-citadel-v1.0.0';
const STATIC_PRECACHE = [
    '/',
    '/services',
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/icon-192.svg',
    '/icons/icon-512.svg',
    '/icons/apple-touch-icon.png'
];

// 1. Install Event: Pre-cache Essential Citadel Shell Assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_PRECACHE).catch((err) => {
                console.warn('[Souly PWA] Pre-cache non-critical item failed:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate Event: Clean Stale Caches & Claim Immediate Control
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch Strategy: Hybrid Resilience
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Bypass non-GET requests and external tracking/APIs
    if (request.method !== 'GET') {
        return;
    }

    // Dynamic Routes (Admin, Portal, Auth, AI API): Network-First
    if (url.pathname.startsWith('/admin') || 
        url.pathname.startsWith('/portal') || 
        url.pathname.startsWith('/login') || 
        url.pathname.startsWith('/register') || 
        url.pathname.startsWith('/api')) {
        
        event.respondWith(
            fetch(request).catch(async () => {
                const cached = await caches.match(request);
                if (cached) return cached;
                return caches.match('/');
            })
        );
        return;
    }

    // Static Assets (CDN Fonts, CSS, Images, Icons, JS): Cache-First / Stale-While-Revalidate
    if (request.destination === 'style' || 
        request.destination === 'script' || 
        request.destination === 'image' || 
        request.destination === 'font' ||
        url.pathname.startsWith('/icons/')) {

        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) {
                    // Update cache in background
                    fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            caches.open(CACHE_NAME).then((cache) => cache.put(request, networkResponse));
                        }
                    }).catch(() => {/* Ignore background fetch failures */});
                    return cachedResponse;
                }

                return fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // Default HTML Navigation: Network-First with Cache Fallback
    event.respondWith(
        fetch(request).then((response) => {
            if (response && response.status === 200) {
                const clone = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
            }
            return response;
        }).catch(() => {
            return caches.match(request).then((cached) => {
                return cached || caches.match('/');
            });
        })
    );
});
