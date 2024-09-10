self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('offline-page').then((cache) => {
            return cache.addAll([
                '/offline.html',
                '/scripts/alpine.min.js',
                '/scripts/tailwind.min.js',
                '/images/favicon.ico',
                '/images/illustration/illustration_7.png',
                '/images/bg-login-pabrik.jpeg',
                '/images/bg-pabrik-1.jpeg',
                '/images/pkt-pattern.png',
            ]);
        }).catch((err) => {
            console.error(err);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
        .catch(() => {
            return caches.match('/offline.html');
        })
    );
});
