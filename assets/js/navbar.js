// Navbar behavior controller
(() => {
  const state = { initialized: false };

  const setActivePageLink = () => {
    const navLinks = document.querySelectorAll('.nav-link');
    const path = window.location.pathname.toLowerCase();
    const parts = path.split('/').filter(Boolean);
    const last = parts.length ? parts[parts.length - 1] : '';

    const deriveSlug = () => {
      // Handle clean URLs first
      if (path === '/' || path === '' || /\/index\.(php|html?)$/.test(path)) return 'home';
      if (parts.length === 1 && !last.includes('.')) return 'home';
      if (path.includes('/home')) return 'home';
      if (path.includes('/about')) return 'about';
      if (path.includes('/program')) return 'program';
      if (path.includes('/contact')) return 'contact';
      if (path.includes('/workshop')) return 'workshop';
      if (path.includes('/blog')) return 'blog';

      // Fallback to filename (about.html, blog.php, blog_detail.php)
      if (last) {
        const base = last.replace(/\.(php|html?)$/, '');
        if (base === 'index') return 'home';
        if (base.startsWith('blog')) return 'blog';
        return base;
      }
      return 'home';
    };

    const currentPage = deriveSlug();

    navLinks.forEach(link => {
      const linkPage = (link.getAttribute('data-page') || '').toLowerCase();
      const active = linkPage === currentPage || (linkPage === 'home' && currentPage === 'home');
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
