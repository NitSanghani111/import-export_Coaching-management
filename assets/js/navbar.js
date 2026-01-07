// Simple Navbar Controller
(() => {
  const state = { initialized: false };

const getCurrentPage = () => {
	const path = window.location.pathname.toLowerCase();
	const filename = path.split('/').pop();

	// Check filename or path segments
	if (path === '/' || path.endsWith('/') || filename === '' || filename === 'index.php') return 'home';
	if (path.includes('about') || filename === 'about.html') return 'about';
	if (path.includes('program') || filename === 'program.html') return 'program';
	if (path.includes('contact') || filename === 'contact.html') return 'contact';
	if (path.includes('workshop') || filename === 'workshop.php') return 'workshop';
	if (path.includes('blog') || filename.includes('blog')) return 'blog';

	return 'home';
};

  const setActivePageLink = () => {
    const currentPage = getCurrentPage();
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
      const page = link.getAttribute('data-page');
      if (!page) return;
      const isActive = page.toLowerCase() === currentPage;
      
      if (isActive) {
        link.classList.add('active');
        link.style.color = '#FFBE49';
        link.style.fontWeight = '700';
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
    const mobileMenu = document.getElementById('mobile-menu');
    const navLinks = document.querySelectorAll('.nav-link');

    if (!header || !menuToggle || !mobileMenu) return false;
    if (state.initialized) return true;

    const isMobileView = () => window.matchMedia('(max-width: 767px)').matches;

  const setToggleState = (isOpen) => {
	menuToggle.classList.toggle('open', isOpen);
	menuToggle.setAttribute(
		'aria-label',
		isOpen ? 'Close navigation menu' : 'Open navigation menu'
	);
};


const openMenu = () => {
	mobileMenu.classList.remove('pointer-events-none', 'opacity-0', 'scale-95');
	mobileMenu.classList.add('opacity-100', 'scale-100');

	document.body.classList.add('menu-open');
	setToggleState(true);   // 🔥 THIS LINE MAKES X ICON APPEAR
};

const closeMenu = () => {
	mobileMenu.classList.add('pointer-events-none', 'opacity-0', 'scale-95');
	mobileMenu.classList.remove('opacity-100', 'scale-100');

	document.body.classList.remove('menu-open');
	setToggleState(false);  // 🔥 THIS LINE RESTORES HAMBURGER
};


menuToggle.addEventListener('click', (e) => {
	e.preventDefault();
	e.stopPropagation();

	const isClosed = mobileMenu.classList.contains('opacity-0');

	if (isClosed) {
		openMenu();
	} else {
		closeMenu();
	}
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

    // Close the mobile drawer if the viewport is no longer mobile sized
    window.addEventListener('resize', () => {
      if (!isMobileView()) closeMenu();
    });

    document.addEventListener('visibilitychange', () => {
      if (!document.hidden) setActivePageLink();
    });

    state.initialized = true;
    return true;
  };

  window.initNavbar = () => {

  	const ok = bindNavbar();
	if (!ok) {
		console.error('Navbar elements missing on this page');
	}
  };
})();
