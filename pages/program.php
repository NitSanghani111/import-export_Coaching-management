<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <link rel="icon" href="/img/design/Logo.svg" type="image/svg+xml" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex, nofollow">
  <title>Business Coaching Programs Rajkot | Scale Your Business Fast</title>
    <meta name="description" content="Scale your business fast with Business Coaching Programs Rajkot offering expert mentoring, proven systems, and strategies for sustainable growth.">

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700;800&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/main.css" />

  <script src="https://cdn.tailwindcss.com?v=3"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: '#0d0d0d',
            accent: '#FFBE49',
          },
          fontFamily: {
            serif: ['Playfair Display', 'Georgia', 'serif'],
            sans: ['Inter', 'system-ui', 'sans-serif'],
          },
        },
      },
    };
  </script>
  <style>
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      font-family: 'Playfair Display', Georgia, serif;
    }

    /* Scroll Animation Styles */
    .fade-in {
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }

    .fade-in.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .slide-in-left {
      opacity: 0;
      transform: translateX(-50px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }

    .slide-in-left.visible {
      opacity: 1;
      transform: translateX(0);
    }

    .slide-in-right {
      opacity: 0;
      transform: translateX(50px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }

    .slide-in-right.visible {
      opacity: 1;
      transform: translateX(0);
    }

    .scale-in {
      opacity: 0;
      transform: scale(0.9);
      transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .scale-in.visible {
      opacity: 1;
      transform: scale(1);
    }
  </style>
</head>

<body class="antialiased bg-black text-white overflow-x-hidden">

  <!-- Page Loader -->
  <div id="page-loader">
    <div class="loader-spinner"></div>
    <div class="loader-text">Loading</div>
  </div>

  <div id="navbar-container"></div>

  <main class="pt-16 md:pt-20" id="home">
    <!-- Hero Section -->
    <section
      class="relative  min-h-[calc(100vh-72px)] lg:min-h-[calc(100vh-88px)] flex items-center justify-center overflow-hidden">
      <!-- Background Image with Blur -->
      <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-cover bg-center  scale-110"
          style="background-image: url('../img/imgs/program_hero.jpeg');"></div>
      </div>


      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/60 z-0"></div>

      <!-- Decorative Circles -->
      <div class="absolute inset-0 z-10">
        <div class="absolute w-24 h-24 rounded-full bg-white/80 top-[10%] left-[5%]"></div>
        <div class="absolute w-48 h-48 rounded-full bg-white/20 top-[15%] right-[15%]"></div>
        <div class="absolute w-24 h-24 rounded-full bg-white/30 bottom-[20%] left-[10%]"></div>
        <div class="absolute w-28 h-28 rounded-full bg-white/40 top-[50%] right-[5%]"></div>
        <div class="absolute w-20 h-20 rounded-full bg-white/25 border-2 border-white/50 bottom-[35%] right-[25%]">
        </div>
      </div>

      <!-- Hero Content -->
      <div
        class="relative z-20 max-w-[90rem] w-full px-6 md:px-12 flex flex-col lg:flex-row items-center justify-between gap-12 -mt-12">
        <div class="flex-1 max-w-2xl text-center lg:text-left fade-in">
          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6 tracking-tight">
            Programs Built to Scale Your Business with Clarity
          </h1>
          <p class="text-lg sm:text-xl lg:text-[22px] leading-relaxed max-w-2xl text-white/90">
            Structured coaching programs built on clarity, systems, and predictable growth.
          </p>
        </div>
        <!-- Client Image Added -->
       <div class="flex-1 flex justify-center lg:justify-end fade-in">
  <div class="relative group">
    
    <!-- Glow -->
    <div class="absolute -inset-4 bg-accent/20 rounded-full blur-3xl opacity-30 group-hover:opacity-60 transition-opacity duration-700"></div>
    
    <!-- Gold ring wrapper -->
    <div class="relative rounded-full border-[6px] border-[#D4AF37] p-1">
      <img src="../img/imgs/tmp6.jpeg" alt="Empowered Client" 
        class="w-[200px] h-[200px] md:w-[320px] md:h-[320px] lg:w-[400px] lg:h-[400px] object-cover rounded-full drop-shadow-[0_20px_50px_rgba(0,0,0,0.4)]" />
    </div>

  </div>
</div>
    </section>

    <!-- Programs Section -->
    <section class="max-w-7xl mx-auto px-6 py-20 flex flex-col gap-20 relative">

      <h2 class="text-4xl sm:text-5xl font-serif font-bold text-white mb-20">
        Feature Program
      </h2>
      <!-- ================= DYNAMIC PROGRAM CARDS ================= -->
      <?php 
      require_once __DIR__ . '/../data/programs.php';
      $isRightAlign = false;
      foreach ($programs as $slug => $prog): 
      ?>
      <div class="relative inline-flex flex-col xl:flex-row
       items-start xl:items-center
       gap-6 md:gap-8 xl:gap-12
       <?= $isRightAlign ? 'ml-auto' : 'w-fit' ?>
       
       rounded-[32px] md:rounded-[48px] xl:rounded-[260px]

       bg-[radial-gradient(ellipse_210%_100%_at_<?= $isRightAlign ? '20%' : '60%' ?>_50%,rgba(40,40,40,0.85)_0%,rgba(0,0,0,0.95)_75%)]

       px-6 sm:px-8 md:px-12 xl:px-20
       py-6 sm:py-8 md:py-10 xl:py-12

       ring-1 ring-white/20
      shadow-[0_30px_80px_rgba(0,0,0,0.85),inset_0_0_0_1px_rgba(255,255,255,0.06)]
      overflow-hidden transform-gpu transition-transform duration-300 hover:scale-[1.03]">

        <?php if ($isRightAlign): ?>
        <!-- CONTENT (RIGHT ALIGN) -->
        <div
          class="flex flex-col justify-between text-center xl:text-left w-full xl:w-[380px] order-2 xl:order-1 pl-6 xl:pl-12 pr-4 xl:pr-8">
          <div>
            <h3 class="text-[#e8b961] text-3xl font-bold mb-5">
              <?= htmlspecialchars($prog['title']) ?>
            </h3>
            <p class="text-white/85 leading-[1.75] text-[17px] line-clamp-4">
              <?= htmlspecialchars($prog['subtitle']) ?>
            </p>
          </div>
          <a href="/program/<?= $slug ?>" class="mt-6 w-fit mx-auto xl:mx-0
                px-8 py-3 rounded-full
                bg-[#f5a742] text-black font-semibold text-[15px]
                hover:bg-[#f7b052] transition duration-300 inline-block text-center cursor-pointer">
            Know More
          </a>
        </div>

        <!-- IMAGE (RIGHT ALIGN) -->
        <div class="w-full sm:w-[320px] h-[300px] flex-shrink-0 order-1 xl:order-2">
          <img src="<?= htmlspecialchars($prog['hero_image']) ?>" alt="<?= htmlspecialchars($prog['title']) ?>"
            class="w-full h-full object-cover rounded-[18px]" />
        </div>

        <?php else: ?>
        <!-- IMAGE (LEFT ALIGN) -->
        <div class="w-full sm:w-[320px] h-[300px] flex-shrink-0">
          <img src="<?= htmlspecialchars($prog['hero_image']) ?>" alt="<?= htmlspecialchars($prog['title']) ?>"
            class="w-full h-full object-cover rounded-[18px]" />
        </div>

        <!-- CONTENT (LEFT ALIGN) -->
        <div class="flex flex-col justify-between text-center xl:text-left w-full xl:w-[380px] pl-4 xl:pl-8">
          <div>
            <h3 class="text-[#e8b961] text-3xl font-bold mb-5">
              <?= htmlspecialchars($prog['title']) ?>
            </h3>
            <p class="text-white/85 leading-[1.75] text-[17px] line-clamp-4">
              <?= htmlspecialchars($prog['subtitle']) ?>
            </p>
          </div>
          <a href="/program/<?= $slug ?>" class="mt-6 w-fit mx-auto xl:mx-0
                px-8 py-3 rounded-full
                bg-[#f5a742] text-black font-semibold text-[15px]
                hover:bg-[#f7b052] transition duration-300 inline-block text-center cursor-pointer">
            Know More
          </a>
        </div>
        <?php endif; ?>

      </div>
      <?php 
      $isRightAlign = !$isRightAlign;
      endforeach; 
      ?>




    </section>
    <!-- Bottom gold border accent -->




  </main>

  <div id="footer-container"></div>

  <script src="../assets/js/loader.js"></script>
  <script src="../assets/js/navbar.js"></script>
  <script>
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

    const initReveal = () => {
      const items = document.querySelectorAll('.reveal');
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add('revealed');
              observer.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.15 }
      );
      items.forEach((item) => observer.observe(item));
    };

    const initAnimations = () => {
      const fadeElements = document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right, .scale-in');
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add('visible');
              observer.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.1 }
      );
      fadeElements.forEach((el) => observer.observe(el));
    };

    document.addEventListener('DOMContentLoaded', async () => {
      const navbarRoot = await injectComponent('/components/Navbar.html', '#navbar-container');
      setTimeout(() => {
        if (typeof window.initNavbar === 'function') {
          window.initNavbar();
        }
      }, 150);
      await injectComponent('../components/footer.html', '#footer-container', 'footer');
      initReveal();
      initAnimations();
    });
  </script>
</body>

</html>