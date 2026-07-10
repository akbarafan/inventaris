const CACHE = 'inventaris-v1';

self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE).then(c => c.addAll([
            '/images/logo-smk.png',
            '/manifest.json',
        ]))
    );
});

self.addEventListener('fetch', e => {
    const url = new URL(e.request.url);
    if (url.pathname === '/manifest.json' || url.pathname.startsWith('/images/') || url.pathname.startsWith('/build/')) {
        e.respondWith(
            caches.match(e.request).then(r => r || fetch(e.request))
        );
        return;
    }
    e.respondWith(fetch(e.request));
});
