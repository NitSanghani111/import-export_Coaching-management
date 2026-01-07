
// Professional Page Loading Animation Handler
document.addEventListener('DOMContentLoaded', function() {
  // Fade out the loader as soon as critical content is ready
  const loader = document.getElementById('page-loader');
  if (!loader) return;

  // Minimum display time for smooth UX (avoid flash)
  const minDisplayTime = 800;
  const startTime = performance.now();

  requestAnimationFrame(() => {
    const elapsed = performance.now() - startTime;
    const delay = Math.max(0, minDisplayTime - elapsed);
    
    setTimeout(() => {
      loader.classList.add('fade-out');
      setTimeout(() => {
        loader.style.display = 'none';
        loader.remove();
      }, 500);
    }, delay);
  });
});

// Show loader on page navigation for better UX
window.addEventListener('beforeunload', function() {
  const loader = document.getElementById('page-loader');
  if (loader && loader.style.display === 'none') {
    loader.style.display = 'flex';
    loader.classList.remove('fade-out');
  }
});

// Optimize image loading - lazy load images
if ('IntersectionObserver' in window) {
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        if (img.dataset.src) {
          img.src = img.dataset.src;
          img.classList.add('loaded');
        }
        observer.unobserve(img);
      }
    });
  });

  // Observe all lazy-loadable images
  document.querySelectorAll('img[data-src]').forEach(img => imageObserver.observe(img));
}

// Prefetch critical resources for faster navigation
function prefetchResources() {
  const links = [
    'components/Navbar.html',
    'components/fotter.html',
    'assets/css/main.css'
  ];
  
  links.forEach(link => {
    const prefetchLink = document.createElement('link');
    prefetchLink.rel = 'prefetch';
    prefetchLink.href = link;
    document.head.appendChild(prefetchLink);
  });
}

// Call prefetch on page load
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', prefetchResources);
} else {
  prefetchResources();
}

// Cache API calls for better performance
const cache = new Map();

function getCachedData(key) {
  if (cache.has(key)) {
    const cached = cache.get(key);
    if (Date.now() - cached.timestamp < 5 * 60 * 1000) { // 5 min cache
      return cached.data;
    }
    cache.delete(key);
  }
  return null;
}

function setCachedData(key, data) {
  cache.set(key, { data, timestamp: Date.now() });
}

// Performance optimization: defer non-critical scripts
document.addEventListener('DOMContentLoaded', () => {
  // Load tracking or analytics scripts with low priority
  const nonCriticalScripts = document.querySelectorAll('script[data-defer]');
  nonCriticalScripts.forEach(script => {
    setTimeout(() => {
      const newScript = document.createElement('script');
      newScript.src = script.dataset.src;
      newScript.async = true;
      document.body.appendChild(newScript);
    }, 3000); // Load after 3 seconds
  });
});
