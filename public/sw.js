const CACHE = 'inventaris-v2';

self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE).then(c => c.addAll([
            '/images/logo-smk.png',
            '/manifest.json',
        ]))
    );
});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys => Promise.all(
            keys.filter(k => k !== CACHE).map(k => caches.delete(k))
        ))
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
