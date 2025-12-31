// Page Loading Animation Handler
document.addEventListener('DOMContentLoaded', function() {
  // Fade out the loader after page has fully loaded
  const loader = document.getElementById('page-loader');
  
  // Use a small delay to ensure smooth transition
  setTimeout(() => {
    if (loader) {
      loader.classList.add('fade-out');
      // Remove loader from DOM after animation completes
      setTimeout(() => {
        loader.style.display = 'none';
      }, 800);
    }
  }, 500);
});

// Show loader on page unload for better UX during navigation
window.addEventListener('beforeunload', function() {
  const loader = document.getElementById('page-loader');
  if (loader) {
    loader.classList.remove('fade-out');
    loader.style.display = 'flex';
  }
});
