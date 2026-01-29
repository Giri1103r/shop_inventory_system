self.addEventListener('install', function (event) {
    console.log('SW installing');
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    console.log('SW activated');
    self.clients.claim();
});

self.addEventListener('fetch', function (event) {
    event.respondWith(
        fetch(event.request).catch(() => {
            return new Response('Offline');
        })
    );
});
