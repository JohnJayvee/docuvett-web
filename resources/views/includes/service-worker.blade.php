var CACHE_NAME = 'v1';
var urlsToCache = [
    '/',
    '/404',
    '/login',
    '/favicon.ico',
    '{{ mix("/js/compiled/manifest.js") }}',
    '{{ mix("/js/compiled/vendor.js") }}',
    '{{ mix("/js/compiled/app.js") }}',
    '{{ mix("/styles/compiled/sass_part.css") }}',
    '/images/avatar-placeholder.png',
    '/images/compiled/header-image.jpg',
    '/images/compiled/Logo_Light.png',
    '/images/compiled/Logo_Dark.png',
    '/fonts/vendor/font-awesome/fontawesome-webfont.woff2',
    '/fonts/vendor/font-awesome/fontawesome-webfont.woff',
    '/fonts/vendor/font-awesome/fontawesome-webfont.ttf',
    '/fonts/vendor/element-ui/lib/theme-chalk/element-icons.woff',
    '/fonts/vendor/element-ui/lib/theme-chalk/element-icons.ttf',
];

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                if (response) {
                    return response;
                }
                return fetch(event.request).catch(function(error) {
                    // `fetch()` throws an exception when the server is unreachable but not
                    // for valid HTTP responses, even `4xx` or `5xx` range.
                    let url = event.request.url.split('?', 2);
                    return caches.match(url[0])
                        .then(function(response) {
                            if (response) {
                                return response;
                            }
                            return caches.open(CACHE_NAME).then(function(cache) {
                                return cache.match('/login');
                            });
                        });
                });
            })
    );
});