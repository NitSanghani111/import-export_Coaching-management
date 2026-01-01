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
    const node = pick ? doc.querySelector(pick) : doc.body;
    target.innerHTML = '';
    if (node) target.appendChild(node.cloneNode(true));
    return target;
  } catch (err) {
    console.error(err);
    target.innerHTML = '<div class="text-red-500">Unable to load component.</div>';
    return null;
  }
};

const initNavbar = (root) => {
  // Navbar now handles its own initialization internally
  // No need for external navbar management
  return;
};

const initReveal = () => {
  // Guard: avoid errors if not present
  if (typeof observeElements === 'function') {
    observeElements();
  }
};

// Init on DOM ready
document.addEventListener('DOMContentLoaded', async () => {
  const navbarRoot = await injectComponent('components/Navbar.html', '#navbar-container', 'header');
  
  // Call navbar initialization after injection
  if (typeof initializeNavbar === 'function') {
    initializeNavbar();
  }
  
  await injectComponent('components/fotter.html', '#footer-container', 'footer');
  initReveal();
});

// Reviews carousel controls (global for onclick)
let indexa = 0;
const track = document.getElementById("reviewTrack");
const total = track ? track.children.length : 0;

function nextReview() {
  if (!track) return;
  if (indexa < total - 1) indexa++;
  track.style.transform = `translateX(-${indexa * 100}%)`;
}

function prevReview() {
  if (!track) return;
  if (indexa > 0) indexa--;
  track.style.transform = `translateX(-${indexa * 100}%)`;
}

// Expose to global scope for inline onclick
window.nextReview = nextReview;
window.prevReview = prevReview;
