// Component Loader - Loads Navbar and Footer into the page
(function() {
  'use strict';

  // Helper to extract body content from HTML string
  function extractBodyContent(htmlString) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(htmlString, 'text/html');
    const body = doc.body;
    return body.innerHTML;
  }

  // Load component with error handling
  async function loadComponent(url, containerId) {
    const container = document.getElementById(containerId);
    if (!container) {
      console.warn(`Container #${containerId} not found`);
      return false;
    }

    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`Failed to load ${url}: ${response.status}`);
      }
      
      const html = await response.text();
      const content = extractBodyContent(html);
      container.innerHTML = content;
      
      return true;
    } catch (error) {
      console.error(`Error loading component from ${url}:`, error);
      return false;
    }
  }

  // Load all components
  async function loadAllComponents() {
    const components = [
      { url: 'components/Navbar.html', id: 'navbar-container' },
      { url: 'components/fotter.html', id: 'footer-container' }
    ];

    try {
      // Load all components in parallel
      const results = await Promise.all(
        components.map(comp => loadComponent(comp.url, comp.id))
      );

      // Initialize navbar after it's loaded
      if (results[0] && typeof window.initNavbar === 'function') {
        // Small delay to ensure DOM is ready
        setTimeout(() => {
          window.initNavbar();
        }, 50);
      }

      return results.every(r => r);
    } catch (error) {
      console.error('Error loading components:', error);
      return false;
    }
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadAllComponents);
  } else {
    loadAllComponents();
  }
})();
