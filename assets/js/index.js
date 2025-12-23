// Moved from index.html

// Blogs data
const blogs = [
  {
    title: "Business Strategies",
    desc: "Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim laborum.",
    image: "https://images.unsplash.com/photo-1521737604893-d14cc237f11d"
  },
  {
    title: "Sales System",
    desc: "Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
    image: "https://images.unsplash.com/photo-1522071820081-009f0129c71c"
  },
  {
    title: "Leadership Growth",
    desc: "Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim laborum.",
    image: "https://images.unsplash.com/photo-1507679799987-c73779587ccf"
  }
];

// Render blog cards
const carousel = document.getElementById("carousel");
if (carousel) {
  blogs.forEach(blog => {
    carousel.innerHTML += `
      <div class="min-w-[360px] max-w-[360px]">
        <img src="${blog.image}"
             class="w-full h-60 object-cover rounded-2xl mb-6" />

        <h3 class="text-2xl font-serif mb-3">
          ${blog.title}
        </h3>

        <p class="text-gray-300 leading-relaxed mb-6">
          ${blog.desc}
        </p>

        <div class="w-full h-[3px] bg-[#FFBE49] rounded-full"></div>
      </div>
    `;
  });
}

/* =========================
   CAROUSEL LOGIC
========================= */
let index = 0;
const cardWidth = 360 + 48; // card width + gap
const maxIndex = blogs.length - 2;

const nextBtn = document.getElementById("next");
const prevBtn = document.getElementById("prev");

if (nextBtn && carousel) {
  nextBtn.onclick = () => {
    if (index < maxIndex) {
      index++;
      carousel.style.transform = `translateX(-${index * cardWidth}px)`;
    }
  };
}

if (prevBtn && carousel) {
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
  if (!root) return;
  const header = root.querySelector('#site-header');
  const menuToggle = root.querySelector('#menu-toggle');
  const menuClose = root.querySelector('#menu-close');
  const mobileMenu = root.querySelector('#mobile-menu');
  const navLinks = root.querySelectorAll('.nav-link');
  const logo = root.querySelector('a[href="#home"]');

  const openMenu = () => {
    if (!mobileMenu) return;
    mobileMenu.classList.remove('pointer-events-none', 'translate-x-full', 'opacity-0');
    mobileMenu.classList.add('opacity-100', 'translate-x-0');
    document.body.classList.add('overflow-hidden');
    mobileMenu.setAttribute('aria-hidden', 'false');
  };

  const closeMenu = () => {
    if (!mobileMenu) return;
    mobileMenu.classList.add('pointer-events-none', 'translate-x-full', 'opacity-0');
    mobileMenu.classList.remove('opacity-100', 'translate-x-0');
    document.body.classList.remove('overflow-hidden');
    mobileMenu.setAttribute('aria-hidden', 'true');
  };

  menuToggle?.addEventListener('click', openMenu);
  menuClose?.addEventListener('click', closeMenu);
  mobileMenu?.addEventListener('click', (event) => {
    if (event.target === mobileMenu) closeMenu();
  });

  navLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
      const targetId = link.getAttribute('href');
      if (targetId && targetId.startsWith('#')) {
        event.preventDefault();
        const target = document.querySelector(targetId);
        if (target) target.scrollIntoView({ behavior: 'smooth' });
        closeMenu();
      }
    });
  });

  const setActiveLink = () => {
    const scrollPos = window.scrollY + 120;
    navLinks.forEach((link) => {
      const targetId = link.getAttribute('href');
      const section = targetId ? document.querySelector(targetId) : null;
      if (!section) return;
      const { offsetTop, offsetHeight } = section;
      const isActive = scrollPos >= offsetTop && scrollPos < offsetTop + offsetHeight;
      link.classList.toggle('text-white', isActive);
      link.classList.toggle('text-white/80', !isActive);
      link.classList.toggle('font-semibold', isActive);
    });
  };

  const handleScrollEffects = () => {
    const scrolled = window.scrollY > 50;
    header?.classList.toggle('shadow-lg', scrolled);
    header?.classList.toggle('bg-opacity-95', scrolled);
    setActiveLink();
  };

  logo?.addEventListener('click', (event) => {
    event.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  document.addEventListener('scroll', handleScrollEffects);
  window.addEventListener('load', setActiveLink);
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
  initNavbar(navbarRoot);
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
