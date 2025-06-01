// Service Worker for Chat Application
const CACHE_NAME = 'chat-app-cache-v3';
const OFFLINE_URL = '/offline';
const ASSETS = [
  '/',
  '/css/app.css',
  '/js/app.js',
  '/sounds/notification.mp3',
  '/image/Logowname.png',
  '/manifest.json',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
  'https://cdn.jsdelivr.net/npm/@emoji-mart/data',
  'https://cdn.jsdelivr.net/npm/@emoji-mart/js',
  'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js'
];

// Install Event - Cache essential resources
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Service Worker: Caching core assets');
        return cache.addAll(ASSETS).catch(err => {
          console.log('Failed to cache assets:', err);
        });
      })
      .then(() => self.skipWaiting())
  );
});

// Activate Event - Clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            console.log('Service Worker: Clearing old cache');
            return caches.delete(cache);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch Event - Network first with cache fallback
self.addEventListener('fetch', (event) => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') return;

  // Handle API requests differently
  if (event.request.url.includes('/api/') || 
      event.request.url.includes('/admin/chat')) {
    event.respondWith(
      fetch(event.request)
        .then(response => {
          // Clone the response to cache it
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseClone);
          });
          return response;
        })
        .catch(() => {
          // If network fails, try to get from cache
          return caches.match(event.request);
        })
    );
    return;
  }

  // For non-API requests: Cache first with network fallback
  event.respondWith(
    caches.match(event.request)
      .then((cachedResponse) => {
        return cachedResponse || fetch(event.request)
          .then(response => {
            // Don't cache large files or opaque responses
            if (!response || response.status !== 200 || response.type === 'opaque') {
              return response;
            }

            // Clone the response to cache it
            const responseToCache = response.clone();
            caches.open(CACHE_NAME)
              .then(cache => cache.put(event.request, responseToCache));

            return response;
          })
          .catch(() => {
            // If offline and not found in cache, show offline page
            if (event.request.headers.get('accept').includes('text/html')) {
              return caches.match(OFFLINE_URL);
            }
          });
      })
  );
});

// Background Sync for failed messages
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-messages') {
    console.log('Service Worker: Background sync for messages');
    event.waitUntil(
      self.registration.showNotification('Chat App', {
        body: 'Connection restored. Sending pending messages...',
        icon: '/image/Logowname.png'
      }).then(() => {
        return sendPendingMessages();
      })
    );
  }
});

// Push Notifications
self.addEventListener('push', (event) => {
  const data = event.data.json();
  const options = {
    body: data.body,
    icon: '/image/Logowname.png',
    badge: '/image/Logowname.png',
    vibrate: [200, 100, 200],
    data: {
      url: data.url
    }
  };

  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(
    clients.matchAll({ type: 'window' }).then(clientList => {
      for (const client of clientList) {
        if (client.url === event.notification.data.url && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(event.notification.data.url);
      }
    })
  );
});

// Function to send pending messages
function sendPendingMessages() {
  return clients.matchAll()
    .then(clients => {
      clients.forEach(client => {
        client.postMessage({ type: 'resendPendingMessages' });
      });
    })
    .catch(err => {
      console.log('Error sending pending messages:', err);
    });
}

// Periodically clean up old messages
self.addEventListener('periodicsync', (event) => {
  if (event.tag === 'cleanup-cache') {
    event.waitUntil(cleanupOldMessages());
  }
});

function cleanupOldMessages() {
  return caches.open(CACHE_NAME)
    .then(cache => {
      return cache.keys()
        .then(keys => {
          const weekAgo = Date.now() - (7 * 24 * 60 * 60 * 1000);
          
          const oldItems = keys.filter(request => {
            return request.url.includes('/api/chat') || 
                   request.url.includes('/admin/chat');
          });

          return Promise.all(
            oldItems.map(request => {
              return cache.match(request)
                .then(response => {
                  if (response) {
                    const date = new Date(response.headers.get('date'));
                    if (date < weekAgo) {
                      return cache.delete(request);
                    }
                  }
                });
            })
          );
        });
    });
}