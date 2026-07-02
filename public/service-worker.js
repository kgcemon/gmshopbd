const CACHE_NAME = 'Codzshop-cache-v2';
const ASSETS = [
    '/',
    '/assets/user/product.css',
    '/assets/user/header.css',
    '/assets/user/product.css',
    '/assets/user/loginModal1.js',
    '/icon.png',
];

self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(ASSETS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k=>k!==CACHE_NAME).map(k=>caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', e => {
    e.respondWith(
        caches.match(e.request).then(res => res || fetch(e.request))
    );
});
