/**
 * Font Caching Service Worker
 * Caches Google Fonts for offline usage and faster loading
 */

const CACHE_NAME = 'fonts-cache-v1';
const FONT_CACHE_NAME = 'google-fonts-v1';

// Google Fonts URLs to cache
const FONT_URLS = [
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
    'https://fonts.gstatic.com/s/inter/v19/UcC73FwrK3iLTeHuS_nVMrMxCp50SjIa1ZL7.woff2',
    'https://fonts.gstatic.com/s/inter/v19/UcC73FwrK3iLTeHuS_fVMrMxCp50SjIa1ZL7W6T7GlUNUw.woff2',
    'https://fonts.gstatic.com/s/inter/v19/UcC73FwrK3iLTcHuS_fVMrMxCp50SjIa1ZL7W6T7GlUNTw.woff2',
    'https://fonts.gstatic.com/s/inter/v19/UcC73FwrK3iLTeHuS_fVMrMxCp50SjIa1ZL7W6T7GlUNOw.woff2'
];

// Cache URLs for different font resources
const CACHE_URLS = [
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap'
];

// Install Service Worker
self.addEventListener('install', event => {
    // console.log('Font Service Worker installing...');
    
    event.waitUntil(
        caches.open(FONT_CACHE_NAME).then(cache => {
            // console.log('Caching font resources...');
            return cache.addAll(CACHE_URLS);
        })
    );
    
    // Skip waiting to activate immediately
    self.skipWaiting();
});

// Activate Service Worker
self.addEventListener('activate', (event) => {
    // console.log('Font Service Worker activating...');
    
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== FONT_CACHE_NAME && cacheName.startsWith('fonts-cache')) {
                        // console.log('Deleting old font cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    
    // Take control immediately
    self.clients.claim();
});

// Fetch Event Handler
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);
    
    // Handle Google Fonts requests
    if (url.hostname === 'fonts.googleapis.com' || url.hostname === 'fonts.gstatic.com') {
        event.respondWith(handleFontRequest(request));
    }
});

async function handleFontRequest(request) {
    try {
        // Don't cache HEAD requests - they cause cache errors
        if (request.method === 'HEAD') {
            // console.log('HEAD request detected, not caching:', request.url);
            try {
                return await fetch(request, {
                    mode: 'no-cors',
                    credentials: 'omit'
                });
            } catch (error) {
                // console.log('HEAD request failed, returning minimal response');
                return new Response('', { 
                    status: 200, 
                    statusText: 'OK',
                    headers: { 'Content-Type': 'text/plain' }
                });
            }
        }
        
        // Try cache first for GET requests
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            // console.log('Serving font from cache:', request.url);
            return cachedResponse;
        }
        
        // Try network with CORS handling
        let networkResponse;
        try {
            networkResponse = await fetch(request, {
                cache: 'default',
                mode: 'cors' // Explicitly set CORS mode
            });
        } catch (corsError) {
            // console.log('CORS failed, trying no-cors mode:', request.url);
            try {
                networkResponse = await fetch(request, {
                    cache: 'default',
                    mode: 'no-cors' // Fallback to no-cors
                });
            } catch (noCorsError) {
                throw new Error(`Both CORS and no-cors failed: ${corsError.message}`);
            }
        }
        
        if (networkResponse.ok || networkResponse.type === 'opaque') {
            // Only cache GET requests to avoid cache errors
            if (request.method === 'GET') {
                try {
                    const cache = await caches.open(FONT_CACHE_NAME);
                    await cache.put(request, networkResponse.clone());
                    // console.log('Font cached from network:', request.url);
                } catch (cacheError) {
                    console.warn('Failed to cache font response:', cacheError);
                    // Continue without caching
                }
            }
            return networkResponse;
        }
        
        throw new Error(`Network response not ok: ${networkResponse.status}`);
        
    } catch (error) {
        console.warn('Font request failed:', request.url, error);
        
        // Try to return a fallback response for CSS requests
        if (request.url.includes('fonts.googleapis.com')) {
            return new Response(generateFallbackCSS(), {
                headers: {
                    'Content-Type': 'text/css',
                    'Cache-Control': 'max-age=86400'
                }
            });
        }
        
        // For font files, let the browser handle fallback
        throw error;
    }
}

function generateFallbackCSS() {
    return `
/* Fallback font stack when Google Fonts fail */
@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-weight: 300;
  src: local('Segoe UI Light'), local('Roboto Light'), local('Helvetica Neue Light');
}

@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-weight: 400;
  src: local('Segoe UI'), local('Roboto'), local('Helvetica Neue'), local('Arial');
}

@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-weight: 500;
  src: local('Segoe UI Semibold'), local('Roboto Medium'), local('Helvetica Neue Medium');
}

@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-weight: 600;
  src: local('Segoe UI Bold'), local('Roboto Bold'), local('Helvetica Neue Bold');
}

@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-weight: 700;
  src: local('Segoe UI Bold'), local('Roboto Bold'), local('Helvetica Neue Bold'), local('Arial Bold');
}
`;
}

// Background sync for font updates
self.addEventListener('sync', (event) => {
    if (event.tag === 'font-update') {
        event.waitUntil(updateFontCache());
    }
});

async function updateFontCache() {
    try {
        const cache = await caches.open(FONT_CACHE_NAME);
        const requests = await cache.keys();
        
        // Update cached fonts
        const updatePromises = requests.map(async (request) => {
            try {
                const response = await fetch(request, { cache: 'no-cache' });
                if (response.ok) {
                    await cache.put(request, response);
                }
            } catch (error) {
                console.warn('Failed to update cached font:', request.url);
            }
        });
        
        await Promise.all(updatePromises);
        // console.log('Font cache updated');
        
    } catch (error) {
        console.error('Font cache update failed:', error);
    }
}

// Error handling
self.addEventListener('error', (event) => {
    console.error('Font Service Worker error:', event.error);
});

// Enhanced error handling for promises
self.addEventListener('unhandledrejection', function(event) {
    // console.log('Font Service Worker unhandled rejection:', event.reason);
    
    // Prevent default behavior that logs the error
    event.preventDefault();
    
    // Only log non-CORS related errors to reduce console noise
    if (event.reason && 
        !event.reason.message?.includes('CORS') && 
        !event.reason.message?.includes('Cache') &&
        !event.reason.message?.includes('HEAD')) {
        console.warn('SW Promise rejection (filtered):', event.reason);
    }
});
