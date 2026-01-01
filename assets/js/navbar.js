// Simple Navbar Controller
(() => {
  const state = { initialized: false };

  const getCurrentPage = () => {
    const path = window.location.pathname.toLowerCase();
    console.log('📍 Current path:', path);
    
    if (path === '/' || path === '' || path === '/index.php' || path.includes('index.php')) return 'home';
    if (path.includes('/about') || path.includes('about.html')) return 'about';
    if (path.includes('/program') || path.includes('program.html')) return 'program';
    if (path.includes('/contact') || path.includes('contact.html')) return 'contact';
    if (path.includes('/workshop') || path.includes('workshop.php')) return 'workshop';
    if (path.includes('/blog')) return 'blog';
    return 'home';
  };

  const setActivePageLink = () => {
    const currentPage = getCurrentPage();
    console.log('🏠 Current page:', currentPage);
    const navLinks = document.querySelectorAll('.nav-link');
    console.log('🔗 Found nav links:', navLinks.length);
    
    navLinks.forEach(link => {
      const page = link.getAttribute('data-page').toLowerCase();
      const isActive = page === currentPage;
      
      if (isActive) {
        link.classList.add('active');
        link.style.color = '#FFBE49';
        link.style.fontWeight = '700';
        console.log('✅ Active:', page);
      } else {
        link.classList.remove('active');
        link.style.color = '';
        link.style.fontWeight = '400';
      }
    });
  };

  const bindNavbar = () => {
    const header = document.getElementById('site-header');
    const menuToggle = document.getElementById('menu-toggle');
    const menuClose = document.getElementById('menu-close');
    const mobileMenu = document.getElementById('mobile-menu');
    const navLinks = document.querySelectorAll('.nav-link');

    if (!header || !menuToggle || !menuClose || !mobileMenu) return false;
    if (state.initialized) return true;

    const openMenu = () => {
      mobileMenu.classList.remove('pointer-events-none', 'translate-x-full', 'opacity-0');
      mobileMenu.classList.add('opacity-100', 'translate-x-0');
      document.body.classList.add('overflow-hidden');
      mobileMenu.setAttribute('aria-hidden', 'false');
    };

    const closeMenu = () => {
      mobileMenu.classList.add('pointer-events-none', 'translate-x-full', 'opacity-0');
      mobileMenu.classList.remove('opacity-100', 'translate-x-0');
      document.body.classList.remove('overflow-hidden');
      mobileMenu.setAttribute('aria-hidden', 'true');
    };

    menuToggle.addEventListener('click', e => {
      e.stopPropagation();
      openMenu();
    });

    menuClose.addEventListener('click', e => {
      e.stopPropagation();
      closeMenu();
    });

    mobileMenu.addEventListener('click', event => {
      if (event.target === mobileMenu) closeMenu();
    });

    navLinks.forEach(link => {
      link.addEventListener('click', () => setTimeout(closeMenu, 80));
    });

    document.addEventListener('keydown', event => {
      if (event.key === 'Escape') closeMenu();
    });

    const handleScrollEffects = () => {
      const scrolled = window.scrollY > 50;
      header.classList.toggle('shadow-lg', scrolled);
      header.classList.toggle('bg-opacity-95', scrolled);
    };

    document.addEventListener('scroll', handleScrollEffects);
    handleScrollEffects();
    setActivePageLink();

    document.addEventListener('visibilitychange', () => {
      if (!document.hidden) setActivePageLink();
    });

    state.initialized = true;
    return true;
  };

  window.initNavbar = () => {
    const success = bindNavbar();
    if (!success) {
      // Retry after a tick if navbar not yet injected
      setTimeout(bindNavbar, 50);
    }
  };
})();
