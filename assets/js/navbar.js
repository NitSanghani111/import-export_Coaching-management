// Navbar behavior controller
(() => {
  const state = { initialized: false };

  const setActivePageLink = () => {
    const navLinks = document.querySelectorAll('.nav-link');
    const currentPath = window.location.pathname.replace(/\/index\.(php|html?)$/, '/');
    const segments = currentPath.split('/').filter(Boolean);
    const currentPage = segments[0] || 'home';

    navLinks.forEach(link => {
      const linkPage = link.getAttribute('data-page') || '';
      const isHome = ['/', '', 'index.php', 'index.html'].includes(currentPath);
      const active = (linkPage === currentPage) || (linkPage === 'home' && isHome);
      link.classList.toggle('text-white', active);
      link.classList.toggle('text-white/80', !active);
      link.classList.toggle('text-white/90', !active);
      link.classList.toggle('active', active);
      link.style.fontWeight = active ? '700' : '400';
      link.style.color = active ? '#FFBE49' : '';
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
