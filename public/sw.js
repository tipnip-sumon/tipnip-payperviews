const CACHE_NAME = 'payperviews-v1.0.0';
const urlsToCache = [
  '/',
  '/videos',
  '/login',
  '/register',
  '/assets/css/app.css',
  '/assets/js/app.js',
  '/assets/images/logo/payperviews-icon.svg',
  '/assets/images/logo/payperviews-icon-192.png',
  '/assets/images/logo/payperviews-icon-512.png',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
  'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap'
];

// Install Service Worker
self.addEventListener('install', function(event) {
  console.log('PayPerViews Service Worker: Installing...');
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        console.log('PayPerViews Service Worker: Caching app shell');
        return cache.addAll(urlsToCache);
      })
      .catch(function(error) {
        console.log('PayPerViews Service Worker: Cache failed', error);
      })
  );
});

// Fetch event
self.addEventListener('fetch', function(event) {
  // Skip cross-origin requests
  if (!event.request.url.startsWith(self.location.origin)) {
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then(function(response) {
        // Return cached version or fetch from network
        return response || fetch(event.request)
          .then(function(fetchResponse) {
            // Check if we received a valid response
            if (!fetchResponse || fetchResponse.status !== 200 || fetchResponse.type !== 'basic') {
              return fetchResponse;
            }

            // Clone the response
            const responseToCache = fetchResponse.clone();

            caches.open(CACHE_NAME)
              .then(function(cache) {
                // Don't cache POST requests or requests with query parameters
                if (event.request.method === 'GET' && !event.request.url.includes('?')) {
                  cache.put(event.request, responseToCache);
                }
              });

            return fetchResponse;
          })
          .catch(function() {
            // Return offline page for navigation requests
            if (event.request.mode === 'navigate') {
              return caches.match('/offline.html');
            }
          });
      })
  );
});

// Activate Service Worker
self.addEventListener('activate', function(event) {
  console.log('PayPerViews Service Worker: Activating...');
  
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.map(function(cacheName) {
          if (cacheName !== CACHE_NAME) {
            console.log('PayPerViews Service Worker: Deleting old cache', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Background sync for offline actions
self.addEventListener('sync', function(event) {
  if (event.tag === 'newsletter-subscription') {
    event.waitUntil(syncNewsletterSubscription());
  }
  
  if (event.tag === 'video-view') {
    event.waitUntil(syncVideoViews());
  }
});

// Push notifications
self.addEventListener('push', function(event) {
  const options = {
    body: event.data ? event.data.text() : 'New videos available to earn money!',
    icon: '/assets/images/logo/payperviews-icon-192.png',
    badge: '/assets/images/logo/payperviews-icon-96.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: '1'
    },
    actions: [
      {
        action: 'explore',
        title: 'Watch Videos',
        icon: '/assets/images/icons/play-icon.png'
      },
      {
        action: 'close',
        title: 'Close',
        icon: '/assets/images/icons/close-icon.png'
      }
    ]
  };

  event.waitUntil(
    self.registration.showNotification('PayPerViews', options)
  );
});

// Notification click handler
self.addEventListener('notificationclick', function(event) {
  event.notification.close();

  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/videos')
    );
  } else if (event.action === 'close') {
    // Just close the notification
  } else {
    // Default action - open the app
    event.waitUntil(
      clients.openWindow('/')
    );
  }
});

// Background sync functions
async function syncNewsletterSubscription() {
  try {
    const subscriptions = await getStoredSubscriptions();
    
    for (const subscription of subscriptions) {
      const response = await fetch('/api/newsletter/subscribe', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(subscription)
      });
      
      if (response.ok) {
        await removeStoredSubscription(subscription.id);
      }
    }
  } catch (error) {
    console.log('Background sync failed for newsletter subscriptions:', error);
  }
}

async function syncVideoViews() {
  try {
    const views = await getStoredVideoViews();
    
    for (const view of views) {
      const response = await fetch('/api/videos/record-view', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(view)
      });
      
      if (response.ok) {
        await removeStoredVideoView(view.id);
      }
    }
  } catch (error) {
    console.log('Background sync failed for video views:', error);
  }
}

// Storage helpers for offline functionality
async function getStoredSubscriptions() {
  const cache = await caches.open('payperviews-offline-data');
  const response = await cache.match('newsletter-subscriptions');
  return response ? await response.json() : [];
}

async function removeStoredSubscription(id) {
  const subscriptions = await getStoredSubscriptions();
  const filtered = subscriptions.filter(sub => sub.id !== id);
  
  const cache = await caches.open('payperviews-offline-data');
  await cache.put('newsletter-subscriptions', new Response(JSON.stringify(filtered)));
}

async function getStoredVideoViews() {
  const cache = await caches.open('payperviews-offline-data');
  const response = await cache.match('video-views');
  return response ? await response.json() : [];
}

async function removeStoredVideoView(id) {
  const views = await getStoredVideoViews();
  const filtered = views.filter(view => view.id !== id);
  
  const cache = await caches.open('payperviews-offline-data');
  await cache.put('video-views', new Response(JSON.stringify(filtered)));
}

// Share Target API
self.addEventListener('message', function(event) {
  if (event.data && event.data.type === 'SHARE_TARGET') {
    event.waitUntil(
      clients.openWindow('/videos?shared=true')
    );
  }
});

console.log('PayPerViews Service Worker: Loaded successfully');
