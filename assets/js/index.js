// Moved from index.html

/* =========================
   CAROUSEL LOGIC (Now handles server-rendered blogs)
========================= */
const carousel = document.getElementById("carousel");
let index = 0;
const cardWidth = 360 + 48; // card width + gap

// Count actual blog cards in the DOM
const blogCount = carousel ? carousel.children.length : 0;
const maxIndex = Math.max(0, blogCount - 2);

const nextBtn = document.getElementById("next");
const prevBtn = document.getElementById("prev");

if (nextBtn && carousel && blogCount > 0) {
  nextBtn.onclick = () => {
    if (index < maxIndex) {
      index++;
      carousel.style.transform = `translateX(-${index * cardWidth}px)`;
    }
  };
}

if (prevBtn && carousel && blogCount > 0) {
  prevBtn.onclick = () => {
    if (index > 0) {
      index--;
      carousel.style.transform = `translateX(-${index * cardWidth}px)`;
    }
  };
}

// Component injection helpers
const injectComponent = async (src, selector, pick) => {
  const target = document.querySelector(selector);
  if (!target) return null;
  try {
    const res = await fetch(src);
    if (!res.ok) throw new Error(`Failed to load ${src}: ${res.status}`);
    const html = await res.text();
    const doc = new DOMParser().parseFromString(html, 'text/html');
    
    // For navbar, inject both header and mobile menu
    if (pick === 'header') {
      const header = doc.querySelector('header');
      const mobileMenu = doc.querySelector('#mobile-menu');
      target.innerHTML = '';
      if (header) target.appendChild(header.cloneNode(true));
      if (mobileMenu) target.appendChild(mobileMenu.cloneNode(true));
    } else {
      const node = pick ? doc.querySelector(pick) : doc.body;
      target.innerHTML = '';
      if (node) target.appendChild(node.cloneNode(true));
    }
    return target;
  } catch (err) {
    console.error(err);
    target.innerHTML = '<div class="text-red-500">Unable to load component.</div>';
    return null;
  }
};

const initReveal = () => {
  // Guard: avoid errors if not present
  if (typeof observeElements === 'function') {
    observeElements();
  }
};

// Init on DOM ready - Load components in parallel for speed
document.addEventListener('DOMContentLoaded', async () => {
  // Load navbar and footer in parallel (not sequential)
  const [navbarRoot] = await Promise.all([
    injectComponent('components/Navbar.html', '#navbar-container', 'header'),
    injectComponent('components/fotter.html', '#footer-container', 'footer')
  ]);
  
  // Initialize navbar immediately
  if (typeof window.initNavbar === 'function') {
    window.initNavbar();
  }
  
  initReveal();
});

// Reviews carousel controls (global for onclick)
let indexa = 0;

function nextReview() {
  const track = document.getElementById("reviewTrack");
  if (!track) return;
  const total = track.children.length;
  if (indexa < total - 1) {
    indexa++;
    // Account for gap-6 (24px) between cards
    const cardWidth = track.children[0].offsetWidth;
    const gap = 24; // gap-6 = 24px
    track.style.transform = `translateX(-${indexa * (cardWidth + gap)}px)`;
  }
}

function prevReview() {
  const track = document.getElementById("reviewTrack");
  if (!track) return;
  if (indexa > 0) {
    indexa--;
    // Account for gap-6 (24px) between cards
    const cardWidth = track.children[0].offsetWidth;
    const gap = 24; // gap-6 = 24px
    track.style.transform = `translateX(-${indexa * (cardWidth + gap)}px)`;
  }
}

// Expose to global scope for inline onclick
window.nextReview = nextReview;
window.prevReview = prevReview;
