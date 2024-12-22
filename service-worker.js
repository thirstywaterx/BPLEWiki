const CACHE_NAME = 'my-pwa-cache-v1';
const urlsToCache = [
  '/',
  '/index.html'
];

// 安装 Service Worker 并缓存资源
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
  self.skipWaiting();  // 使新安装的 Service Worker 立即接管控制权
});

// 更新缓存：每次成功的网络请求都会检查并更新缓存中的内容
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(cachedResponse => {
      // 检查缓存是否命中
      const fetchPromise = fetch(event.request).then(networkResponse => {
        // 成功从网络获取内容后更新缓存
        if (networkResponse && networkResponse.status === 200) {
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, networkResponse.clone());
          });
        }
        return networkResponse;
      }).catch(() => {
        // 如果网络请求失败，返回缓存内容（适用于离线情况）
        return cachedResponse;
      });

      // 如果缓存存在，优先返回缓存内容，同时更新缓存
      return cachedResponse || fetchPromise;
    })
  );
});

// 激活并清除旧缓存
self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (!cacheWhitelist.includes(cacheName)) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();  // 立即激活新的 Service Worker 并接管页面控制
});