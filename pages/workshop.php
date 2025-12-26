<?php
require_once __DIR__ . '/../db.php';
date_default_timezone_set('Asia/Kolkata');

// Detect available columns in workshops table
$workshopCols = [];
$colsRes = mysqli_query($conn, "SHOW COLUMNS FROM workshops");
if ($colsRes) {
    while ($c = mysqli_fetch_assoc($colsRes)) { 
        $workshopCols[] = $c['Field']; 
    }
}

// Determine which datetime column to use
$hasStartDatetime = in_array('start_datetime', $workshopCols, true);
$hasScheduledAt   = in_array('scheduled_at', $workshopCols, true);
$hasDatetime      = in_array('datetime', $workshopCols, true);
$datetimeField    = $hasStartDatetime ? 'start_datetime' : ($hasScheduledAt ? 'scheduled_at' : ($hasDatetime ? 'datetime' : ''));

// Also detect separate date/time columns (fallback)
$dateField = in_array('workshop_date', $workshopCols, true) ? 'workshop_date' : (in_array('date', $workshopCols, true) ? 'date' : (in_array('start_date', $workshopCols, true) ? 'start_date' : ''));
$timeField = in_array('workshop_time', $workshopCols, true) ? 'workshop_time' : (in_array('time', $workshopCols, true) ? 'time' : (in_array('start_time', $workshopCols, true) ? 'start_time' : ''));

// Determine which description column to use
$descField = in_array('short_description', $workshopCols, true) ? 'short_description' : (in_array('description', $workshopCols, true) ? 'description' : '');

// Determine which image column to use
$thumbField = in_array('thumbnail', $workshopCols, true) ? 'thumbnail' : (in_array('image', $workshopCols, true) ? 'image' : '');

// build datetime select: use native datetime column if present, otherwise create alias `start_dt` from date+time
$selectCols = ['id', 'title'];
if ($datetimeField) {
  $selectCols[] = $datetimeField;
  $datetimeSelect = $datetimeField; // column name (use in SQL expressions)
  $datetimeAlias = $datetimeField; // name present in result rows
  $datetimeExpr = $datetimeField; // expression to use in WHERE/ORDER
} elseif ($dateField) {
  // create an aliased datetime string from date + optional time
  if ($timeField) {
    $expr = "CONCAT_WS(' ', $dateField, IFNULL($timeField, '00:00:00'))";
    $selectCols[] = $expr . " AS start_dt";
  } else {
    $expr = "CONCAT($dateField, ' 00:00:00')";
    $selectCols[] = $expr . " AS start_dt";
  }
  $datetimeSelect = 'start_dt';
  $datetimeAlias = 'start_dt';
  $datetimeExpr = $expr; // use full expression in WHERE/ORDER (alias not available there)
} else {
  $datetimeSelect = '';
  $datetimeAlias = '';
  $datetimeExpr = '';
}

$selectCols[] = $descField ?: null;
$selectCols[] = $thumbField ?: null;
$selectCols = array_values(array_filter($selectCols, function($v){ return $v !== null && $v !== ''; }));

$selectStr = implode(', ', $selectCols);

// Fetch workshops
// If we have a native single datetime column, use SQL to split upcoming/past strictly.
$upcomingWorkshops = [];
$pastWorkshops = [];
if ($datetimeField) {
  $selectStrSql = implode(', ', $selectCols);
  $upcomingQuery = "SELECT $selectStrSql FROM workshops WHERE $datetimeField > NOW() ORDER BY $datetimeField ASC";
  $pastQuery = "SELECT $selectStrSql FROM workshops WHERE $datetimeField < NOW() ORDER BY $datetimeField DESC LIMIT 20";

  $upcomingResult = mysqli_query($conn, $upcomingQuery);
  if ($upcomingResult) {
    while ($row = mysqli_fetch_assoc($upcomingResult)) {
      $upcomingWorkshops[] = $row;
    }
  } else {
    error_log("Query error (upcoming sql): " . mysqli_error($conn) . " -- SQL: " . $upcomingQuery);
  }

  $pastResult = mysqli_query($conn, $pastQuery);
  if ($pastResult) {
    while ($row = mysqli_fetch_assoc($pastResult)) {
      $pastWorkshops[] = $row;
    }
  } else {
    error_log("Query error (past sql): " . mysqli_error($conn) . " -- SQL: " . $pastQuery);
  }

} else {
  // No native datetime column — fetch all rows and decide in PHP using strtotime on available fields
  $allQuery = "SELECT " . implode(', ', $selectCols) . " FROM workshops ORDER BY id DESC";
  $res = mysqli_query($conn, $allQuery);
  if (!$res) {
    error_log("Query error (all): " . mysqli_error($conn) . " -- SQL: " . $allQuery);
  } else {
    // helper to get timestamp from a row (try several fields/formats)
    $getTs = function($row) use ($datetimeField, $dateField, $timeField) {
      // try common names first
      $candidates = [];
      if (!empty($row['start_dt'])) $candidates[] = $row['start_dt'];
      if (!empty($row['start_datetime'])) $candidates[] = $row['start_datetime'];
      if (!empty($row['scheduled_at'])) $candidates[] = $row['scheduled_at'];
      if (!empty($row['datetime'])) $candidates[] = $row['datetime'];
      if (!empty($row['date_time'])) $candidates[] = $row['date_time'];
      // date+time fields
      if (!empty($dateField) && !empty($timeField) && !empty($row[$dateField]) && !empty($row[$timeField])) {
        $candidates[] = $row[$dateField] . ' ' . $row[$timeField];
      }
      if (!empty($dateField) && !empty($row[$dateField])) $candidates[] = $row[$dateField];
      if (!empty($timeField) && !empty($row[$timeField])) $candidates[] = $row[$timeField];

      foreach ($candidates as $s) {
        $ts = strtotime($s);
        if ($ts !== false && $ts > 0) return $ts;
      }
      return 0;
    };

    while ($row = mysqli_fetch_assoc($res)) {
      $ts = $getTs($row);
      if ($ts > time()) {
        $upcomingWorkshops[] = $row;
      } elseif ($ts > 0) {
        $pastWorkshops[] = $row;
      } else {
        // undated — treat as upcoming
        $upcomingWorkshops[] = $row;
      }
    }
    // sort upcoming by timestamp asc, past by timestamp desc (attempt)
    usort($upcomingWorkshops, function($a, $b) use ($getTs) { return $getTs($a) <=> $getTs($b); });
    usort($pastWorkshops, function($a, $b) use ($getTs) { return $getTs($b) <=> $getTs($a); });
  }
}

// Helper function to format date
function formatDate($datetimeStr) {
    if (!$datetimeStr) return 'Date TBA';
    $dt = new DateTime($datetimeStr);
    return $dt->format('d F Y');
}

// Helper function to get image
function getWorkshopImage($workshop, $thumbField, $altField = null) {
    if ($thumbField && !empty($workshop[$thumbField])) {
        return '../admin/workshops_img/' . htmlspecialchars($workshop[$thumbField]);
    }
    if ($altField && !empty($workshop[$altField])) {
        return '../admin/workshops_img/' . htmlspecialchars($workshop[$altField]);
    }
    return 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d';
}

// Helper function to get description
function getDescription($workshop, $descField) {
    if ($descField && !empty($workshop[$descField])) {
        return htmlspecialchars(substr($workshop[$descField], 0, 60));
    }
    return 'Workshop coming soon';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Workshops</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Inter:wght@300;400&display=swap" rel="stylesheet">

  <style>
    body {
      background:#000;
      color:#fff;
      font-family:'Inter',sans-serif;
    }
    .font-serif { font-family:'Playfair Display',serif; }

    /* hide scrollbar */
    .no-scrollbar::-webkit-scrollbar { display:none; }
    .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
  </style>
</head>

<body>
    <div id="navbar-container"></div>
<!-- ================= HERO ================= -->
<section class="relative h-[70vh] md:h-[85vh] w-full overflow-hidden">

  <!-- Image -->
  <img
    src="../img/imgs/workshophere.jpeg"
    class="absolute inset-0 w-full h-full object-cover"
    alt="Workshop hero"
  />

  <!-- Dark overlay -->
  <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-black/40 to-black/70"></div>

  <!-- Text -->
  <div class="relative z-10 flex items-center justify-center h-full text-center px-6">
    <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-tight max-w-5xl">
      From Chaos to Clarity: Business Systems Workshop
    </h1>
  </div>

  <!-- ================= SHADOW BAND ================= -->
  <svg
    class="absolute bottom-[-1px] w-full h-[180px]"
    viewBox="0 0 1440 180"
    preserveAspectRatio="none"
  >
    <defs>
      <linearGradient id="shadowFade" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="rgba(0,0,0,0.75)" />
        <stop offset="55%" stop-color="rgba(0,0,0,0.95)" />
        <stop offset="100%" stop-color="#000" />
      </linearGradient>
    </defs>
    <path
      d="
        M0,120
        C200,150 420,90 720,110
        C980,130 1200,70 1440,50
        L1440,180
        L0,180
        Z
      "
      fill="url(#shadowFade)"
    />
  </svg>

</section>

<!-- ================= BLACK CONTENT SECTION ================= -->
<?php if (!empty($pastWorkshops)): ?>
<section class="bg-black pt-24 pb-10 px-6 md:px-16 text-center">

  <h2 class="font-serif text-3xl md:text-4xl mb-4">
    Previously Hosted Workshops
  </h2>

  <p class="text-gray-400 text-sm max-w-2xl mx-auto leading-relaxed">
    Live workshops and webinars focused on clarity, execution, and scalable growth.
    Built for entrepreneurs who want real business results.
  </p>

</section>

<!-- ================= CAROUSEL SECTION ================= -->
<section class="relative bg-black pb-20 px-6 md:px-16">

  <!-- Right fade to hint more cards -->
  <div class="pointer-events-none absolute inset-y-6 right-0 w-28 bg-gradient-to-l from-black via-black/70 to-transparent"></div>

  <!-- Arrows -->
  <div class="flex justify-end gap-3 mb-4 pr-4 md:pr-10">
    <button type="button" id="prevBtn"
      class="w-12 h-12 rounded-full border border-white/30 flex items-center justify-center text-white
             hover:bg-white hover:text-black transition">
      &larr;
    </button>

    <button type="button" id="nextBtn"
      class="w-12 h-12 rounded-full border border-white/30 flex items-center justify-center text-white
             hover:bg-white hover:text-black transition">
      &rarr;
    </button>
  </div>

  <!-- Carousel -->
  <div
    id="carousel"
    class="flex gap-4 md:gap-6 overflow-x-auto no-scrollbar relative z-10 py-4 pr-12 md:pr-20"
  >

    <?php foreach ($pastWorkshops as $workshop): ?>
    <article data-card class="min-w-[240px] md:min-w-[280px] h-[340px] md:h-[380px] rounded-2xl overflow-hidden border border-white/10 relative shrink-0 transition duration-300 ease-out hover:scale-[1.04] hover:-translate-y-1 hover:border-white/60 hover:shadow-[0_16px_40px_rgba(0,0,0,0.45)]">
      <img
        src="<?php echo getWorkshopImage($workshop, $thumbField); ?>"
        class="absolute inset-0 w-full h-full object-cover"
        alt="<?php echo htmlspecialchars($workshop['title']); ?>"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent"></div>
      <div class="absolute bottom-6 left-6 right-6">
        <h3 class="font-serif text-lg leading-snug mb-3">
          <?php echo htmlspecialchars($workshop['title']); ?>
        </h3>
        <div class="flex items-center gap-2 text-xs text-gray-300">
          <span class="w-2 h-2 rounded-full bg-white"></span>
          <?php echo formatDate($workshop[$datetimeAlias] ?? null); ?>
        </div>
      </div>
    </article>
    <?php endforeach; ?>

  </div>

</section>
<?php endif; ?>


<!-- ================= UPCOMING WORKSHOPS ================= -->
<?php if (!empty($upcomingWorkshops)): ?>
<section class="bg-black py-28 px-6 md:px-16">

  <!-- Heading -->
  <h2 class="text-center font-serif text-3xl md:text-4xl mb-20">
    Upcoming Workshops
  </h2>

  <!-- Grid -->
  <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-16">

    <?php foreach ($upcomingWorkshops as $workshop): ?>
    <div class="relative h-[380px] rounded-2xl overflow-hidden
     shadow-[0_25px_60px_rgba(0,0,0,0.85)]
     transition duration-300 ease-out
     hover:scale-[1.03] hover:-translate-y-1
     hover:border hover:border-white/60
     hover:shadow-[0_18px_48px_rgba(0,0,0,0.55)]">

      <!-- Image -->
      <img
        src="<?php echo getWorkshopImage($workshop, $thumbField); ?>"
        class="absolute inset-0 w-full h-full object-cover opacity-75"
        alt="<?php echo htmlspecialchars($workshop['title']); ?>"
      />

      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/55"></div>

      <!-- CONTENT -->
      <div class="relative z-10 h-full p-6 flex flex-col justify-between">

        <!-- TOP (TITLE ONLY) -->
        <h3 class="font-serif text-xl">
          <?php echo htmlspecialchars($workshop['title']); ?>
        </h3>

        <!-- BOTTOM CONTENT -->
        <div>
          <span class="block text-sm text-gray-300 mb-2">
            <?php echo formatDate($workshop[$datetimeAlias] ?? null); ?>
          </span>

          <p class="text-sm text-gray-300 leading-relaxed mb-4">
            <?php echo getDescription($workshop, $descField); ?>...
          </p>

          <button
            type="button"
            class="open-register bg-white text-black text-xs px-4 py-2 rounded-md hover:bg-gray-200 transition"
            data-title="<?php echo htmlspecialchars($workshop['title']); ?>"
            data-id="<?php echo htmlspecialchars($workshop['id']); ?>">
            Register Now
          </button>
        </div>

      </div>
    </div>
    <?php endforeach; ?>

  </div>
</section>
<?php else: ?>
<section class="bg-black py-28 px-6 md:px-16">
    	
  <div class="text-center">
    <h2 class="text-center font-serif text-3xl md:text-4xl mb-8">
      No Upcoming Workshops
    </h2>
    <p class="text-gray-400 text-base">Check back soon for new workshops!</p>
  </div>
</section>
<?php endif; ?>

    <div id="footer-container"></div>

<!-- Register Modal (dark themed, accessible) -->
<div id="registerModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="bg-[#0b0b0b] text-white rounded-xl w-full max-w-4xl overflow-hidden shadow-2xl border border-white/10">
    <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
      <h3 id="modalTitle" class="font-serif text-lg">Register for Workshop</h3>
      <button id="modalClose" aria-label="Close register form" class="text-gray-300 hover:text-white text-2xl leading-none">&times;</button>
    </div>

    <form id="registerForm" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6" novalidate>
      <input type="hidden" name="workshop_id" id="workshop_id">
      <input type="hidden" name="workshop_title" id="workshop_title">

      <div class="flex flex-col gap-4">
        <label class="text-xs text-gray-300">Full name <span class="text-red-500">*</span></label>
        <input id="r_name" name="name" placeholder="Full name" class="p-3 bg-[#0f0f0f] border border-white/10 rounded text-white" required>

        <label class="text-xs text-gray-300">Email <span class="text-red-500">*</span></label>
        <input id="r_email" name="email" type="email" placeholder="you@example.com" class="p-3 bg-[#0f0f0f] border border-white/10 rounded text-white" required>

        <label class="text-xs text-gray-300">Contact number</label>
        <input id="r_contact" name="contact" placeholder="Phone or WhatsApp" class="p-3 bg-[#0f0f0f] border border-white/10 rounded text-white">

        <label class="text-xs text-gray-300">Who are you?</label>
        <input id="r_who" name="who" placeholder="Founder, Student, etc." class="p-3 bg-[#0f0f0f] border border-white/10 rounded text-white">
      </div>

      <div class="flex flex-col gap-4">
        <label class="text-xs text-gray-300">Tell us something about you</label>
        <textarea id="r_message" name="message" rows="8" placeholder="A short note" class="p-3 bg-[#0f0f0f] border border-white/10 rounded text-white w-full"></textarea>

        <div class="flex items-center justify-between mt-auto">
          <div id="formFeedback" class="text-sm text-red-400"></div>
          <div class="flex gap-3">
            <button type="button" id="modalCancel" class="px-4 py-2 rounded border border-white/10 text-white">Cancel</button>
            <button type="submit" id="modalSubmit" class="px-4 py-2 rounded bg-white text-black flex items-center gap-2">
              <span id="submitLabel">Send</span>
              <svg id="submitSpinner" class="w-4 h-4 hidden animate-spin" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" stroke-linecap="round" fill="none" stroke-dasharray="60" stroke-dashoffset="0"></circle></svg>
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ================= CAROUSEL SCRIPT ================= -->
<script>
  const carousel = document.getElementById('carousel');
  const nextBtn = document.getElementById('nextBtn');
  const prevBtn = document.getElementById('prevBtn');
  const firstCard = carousel ? carousel.querySelector('[data-card]') : null;

  const getStep = () => {
    if (!carousel || !firstCard) return 304; // fallback width + gap
    const styles = getComputedStyle(carousel);
    const gap = parseFloat(styles.columnGap || styles.gap || '0');
    return firstCard.getBoundingClientRect().width + gap;
  };

  let step = getStep();
  let animationFrame;
  let autoTimer;

  const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);

  const smoothScrollBy = (distance, duration = 1100) => {
    if (!carousel) return;
    if (animationFrame) cancelAnimationFrame(animationFrame);

    const start = carousel.scrollLeft;
    const target = start + distance;
    const startTime = performance.now();

    const tick = (now) => {
      const elapsed = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const eased = easeOutCubic(progress);
      carousel.scrollLeft = start + (target - start) * eased;
      if (progress < 1) {
        animationFrame = requestAnimationFrame(tick);
      }
    };

    animationFrame = requestAnimationFrame(tick);
  };

  const scrollByStep = (direction) => {
    stopAutoPlay();
    smoothScrollBy(step * direction);
    startAutoPlay();
  };

  if (nextBtn) nextBtn.addEventListener('click', () => scrollByStep(1));
  if (prevBtn) prevBtn.addEventListener('click', () => scrollByStep(-1));

  window.addEventListener('resize', () => {
    step = getStep();
  });

  const startAutoPlay = () => {
    stopAutoPlay();
    autoTimer = setInterval(() => {
      const max = carousel ? carousel.scrollWidth - carousel.clientWidth : 0;
      const nextLeft = carousel ? carousel.scrollLeft + step : 0;
      if (carousel && max > 0 && nextLeft >= max - 4) {
        carousel.scrollLeft = 0; // jump back silently to start for seamless loop
      }
      smoothScrollBy(step);
    }, 3200);
  };

  const stopAutoPlay = () => {
    if (autoTimer) {
      clearInterval(autoTimer);
      autoTimer = null;
    }
  };

  if (carousel) {
    carousel.addEventListener('mouseenter', stopAutoPlay);
    carousel.addEventListener('mouseleave', startAutoPlay);
    startAutoPlay();
  }

  // ================= NAVBAR SCRIPT =================
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
            observeElements(); // Initialize scroll animations
        };

        document.addEventListener('DOMContentLoaded', async () => {
            const navbarRoot = await injectComponent('../components/Navbar.html', '#navbar-container', 'header');
            initNavbar(navbarRoot);
            await injectComponent('../components/fotter.html', '#footer-container', 'footer');
            initReveal();
        });

        // ===== Register modal handling =====
        (function(){
          const openBtns = () => document.querySelectorAll('.open-register');
          const modal = document.getElementById('registerModal');
          const modalTitle = document.getElementById('modalTitle');
          const workshopId = document.getElementById('workshop_id');
          const workshopTitle = document.getElementById('workshop_title');
          const modalClose = document.getElementById('modalClose');
          const modalCancel = document.getElementById('modalCancel');
          const form = document.getElementById('registerForm');

          const openModal = (title, id) => {
            modalTitle.textContent = 'Register — ' + (title || 'Workshop');
            workshopId.value = id || '';
            workshopTitle.value = title || '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
          };

          const closeModal = () => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
          };

          document.addEventListener('click', (e) => {
            const t = e.target.closest && e.target.closest('.open-register');
            if (t) {
              const title = t.getAttribute('data-title');
              const id = t.getAttribute('data-id');
              openModal(title, id);
            }
          });

          modalClose?.addEventListener('click', closeModal);
          modalCancel?.addEventListener('click', closeModal);

          form?.addEventListener('submit', function(ev){
            ev.preventDefault();
            // simple validation
            const name = document.getElementById('r_name').value.trim();
            const email = document.getElementById('r_email').value.trim();
            const contact = document.getElementById('r_contact').value.trim();
            if (!name || !email) {
              alert('Please enter name and email');
              return;
            }

            // For now: static behavior — show success message
            form.innerHTML = '<div class="p-6 text-center">\n  <h4 class="text-lg font-semibold">Thanks — you are registered</h4>\n  <p class="text-sm text-gray-700 mt-2">We will contact you with the workshop details.</p>\n  <div class="mt-4">\n    <button id="closeAfter" class="px-4 py-2 bg-black text-white rounded">Close</button>\n  </div>\n</div>';

            const closeAfter = document.getElementById('closeAfter');
            closeAfter?.addEventListener('click', closeModal);
            console.log('Register (static):', {name, email, contact, id: workshopId.value, title: workshopTitle.value});
          });
        })();
</script>

</body>
</html>
