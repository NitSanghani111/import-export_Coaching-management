<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/db.php';

// Fetch top 6 blogs with optimized query
$sql = "SELECT b.id, b.title, b.image, b.created_at,
        GROUP_CONCAT(c.name SEPARATOR '|') AS categories
        FROM blog b
        LEFT JOIN blog_categories bc ON bc.blog_id = b.id
        LEFT JOIN categories c ON c.id = bc.category_id
        GROUP BY b.id
        ORDER BY b.created_at DESC
        LIMIT 6";

$result = mysqli_query($conn, $sql);
$blogs = [];
if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $blogs[] = $row;
  }
  mysqli_free_result($result);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Home page</title>

  <!-- Preconnect to external domains for faster loading -->
  <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Preload critical CSS -->
  <link rel="preload" href="assets/css/main.css" as="style">

  <!-- CSS (defer non-critical) -->
  <link rel="stylesheet" href="assets/css/main.css" />

  <!-- Tailwind (defer to prevent blocking) -->
  <link rel="preload" as="script" href="https://cdn.tailwindcss.com">
  <script src="https://cdn.tailwindcss.com" defer></script>

  <!-- Google Fonts with font-display swap for faster rendering -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
  <noscript>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet">
  </noscript>
  <style>
    /* Home blog card separation: gold border, subtle shadow */
    .home-blog-card {
      border: 1px solid #FFBE49;
      border-radius: 16px;
      padding: 16px;
      background: rgba(12, 12, 12, 0.9);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .home-blog-card:hover {
      border-color: #ffcc66;
      /* lighter gold on hover */
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.4);
    }

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

    /* Panel behind carousel to separate from page background */
  </style>
</head>

<body class="bg-black text-white">

  <!-- Page Loader with Logo -->
  <div id="page-loader">
   
    <div class="loader-spinner"></div>
    <div class="loader-text">Loading</div>
  </div>

  <div id="navbar-container"></div>
  <!-- //hero section  -->

  <section class="relative min-h-screen flex items-center px-8 lg:px-24 hero-section">

    <!-- BACKGROUND RINGS -->
    <div class="hero-bg">
      <div class="ring r1"></div>
      <div class="ring r2"></div>
      <div class="ring r3"></div>
    </div>

    <!-- CONTENT -->
    <div class="relative z-10 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center hero-grid">

      <!-- LEFT TEXT -->
      <div>
        <h1 class="text-3xl md:text-4xl xl:text-4xl font-serif leading-tight mb-4">
          PARTH JETHVA
          <br />
          Startup & Business Growth Coach
        </h1>
        <h2 class="lg:text-3xl">Struggling with clarity or growth?
        </h2>
        <p class="text-gray-300 max-w-xl leading-relaxed mb-10 pt-5">
          Helping Entrepreneurs scale with clarity, systems, and predictable growth.
        </p>

        <!-- CTA -->
        <a href="./contact"> <button class="cta-btn inline-flex items-center gap-4 px-7 py-3 rounded-full shadow-lg">
            <span class="font-medium">Start Your Journey Here</span>
            <span class="cta-arrow w-10 h-10 rounded-full flex items-center justify-center text-lg">
              →
            </span>
          </button>
        </a>
      </div>

      <!-- RIGHT IMAGE -->
      <div class="relative flex justify-center">
        <img
          src="img/imgs/client.jpeg"
          alt="Client"
          class="hero-img max-w-md w-full grayscale rounded" />
      </div>

    </div>
  </section>

  <section class="max-w-7xl mx-auto px-6 py-28 relative">
    <!-- Subtle background -->
    <div class="absolute inset-0 opacity-5" style="background: radial-gradient(circle at 20% 50%, #FFBE49 0%, transparent 50%), radial-gradient(circle at 80% 50%, #FFBE49 0%, transparent 50%);"></div>
    
    <div class="relative z-10">
    <!-- Heading -->
    <h2 class="text-4xl md:text-5xl font-serif mb-20">
      My 3 Growth Pillars
    </h2>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 pillars-grid">

      <!-- CARD 1 -->
      <div class="card-base px-12 py-16 text-center transition-all duration-300 hover:scale-105 hover:shadow-[0_0_30px_rgba(255,190,73,0.2)] cursor-pointer">
        <div class="mb-8 text-gray-400">
          <img src="./img/design/Clarity.svg" alt="" class="w-18 h-18 mx-auto" />
          <p class="text-xs tracking-widest mt-2 text-gray-500">CLARITY</p>
        </div>

        <h3 class="text-3xl font-serif text-[#FFBE49] mb-6">
          Clarity & Direction
        </h3>

        <p class="text-gray-300 text-lg leading-relaxed">
          Stop guessing. Start executing with confidence.
        </p>
      </div>

      <!-- CARD 2 -->
      <div class="card-base px-12 py-16 text-center transition-all duration-300 hover:scale-105 hover:shadow-[0_0_30px_rgba(255,190,73,0.2)] cursor-pointer">
        <div class="mb-8 text-gray-400">
          <img src="./img/design/Computer.svg" alt="" class="w-18 h-18 mx-auto" />
        </div>

        <h3 class="text-3xl font-serif text-[#FFBE49] mb-6">
          Systems & Structure
        </h3>

        <p class="text-gray-300 text-lg leading-relaxed">
          Build a business that runs without daily chaos.
        </p>
      </div>

      <!-- CARD 3 -->
      <div class="card-base px-12 py-16 text-center transition-all duration-300 hover:scale-105 hover:shadow-[0_0_30px_rgba(255,190,73,0.2)] cursor-pointer">
        <div class="mb-8 text-gray-400">
          <img src="./img/design/Growth.svg" alt="" class="w-18 h-18 mx-auto" />
        </div>

        <h3 class="text-3xl font-serif text-[#FFBE49] mb-6">
          Profit & Performance
        </h3>

        <p class="text-gray-300 text-lg leading-relaxed">
          Scale consistently with proven business frameworks.
        </p>
      </div>

    </div>
    </div>
    <!-- Bottom gold border accent -->
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-60"></div>
  </section>

  <!-- Signature progrmams -->

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 relative">
  <!-- Top gold border accent -->
  <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-60"></div>

  <div class="relative z-10">
  <!-- Section Title -->
  <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif mb-14">
    Our Programs
  </h2>

  <!-- Programs Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 lg:gap-14">

    <!-- Program Card -->
    <div class="group rounded-[28px] overflow-hidden
                bg-[#0C0C0C] border border-white/10
                transition-all duration-500
                hover:border-[#FFBE49]/40 hover:-translate-y-2">

      <!-- Image -->
      <div class="relative overflow-hidden">
        <img
          src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d"
          class="w-full h-52 sm:h-60 object-cover
                 transition-transform duration-700 group-hover:scale-105"
          alt="Startup Success Blueprint" />
      </div>

      <!-- Content -->
      <div class="px-7 py-7 flex flex-col gap-4">

        <h3 class="text-xl sm:text-2xl font-serif leading-snug">
          Startup Success Blueprint
        </h3>

        <p class="text-gray-400 text-sm sm:text-base leading-relaxed">
          A practical, step-by-step system to build a business that survives,
          stabilises and scales.
        </p>

        <!-- Divider -->
        <div class="h-px w-full bg-white/10 my-2"></div>

        <!-- Key Points -->
        <ul class="text-gray-400 text-sm space-y-2 list-disc list-inside">
          <li>Validate your idea & market the right way</li>
          <li>Fix your business model & money leaks</li>
          <li>Build simple systems for sales & operations</li>
          <li>Create a 6–12 month growth roadmap</li>
        </ul>

        <!-- CTA -->
        <a href="#"
           class="mt-6 inline-flex items-center gap-2
                  text-[#FFBE49] font-medium
                  hover:gap-3 transition-all">
          Know more about this program
          <span>→</span>
        </a>

      </div>
    </div>

    <!-- Duplicate cards (structure stays same) -->
    <div class="group rounded-[28px] overflow-hidden
                bg-[#0C0C0C] border border-white/10
                transition-all duration-500
                hover:border-[#FFBE49]/40 hover:-translate-y-2">

      <div class="relative overflow-hidden">
        <img
          src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d"
          class="w-full h-52 sm:h-60 object-cover
                 transition-transform duration-700 group-hover:scale-105" />
      </div>

      <div class="px-7 py-7 flex flex-col gap-4">
        <h3 class="text-xl sm:text-2xl font-serif">
          Leadership & Team Systems
        </h3>

        <p class="text-gray-400 text-sm sm:text-base">
          Build accountable teams and systems that don’t depend on you daily.
        </p>

        <div class="h-px w-full bg-white/10 my-2"></div>

        <ul class="text-gray-400 text-sm space-y-2 list-disc list-inside">
          <li>Hiring clarity & role design</li>
          <li>Delegation & accountability</li>
          <li>Simple SOPs that scale</li>
        </ul>

        <a href="#"
           class="mt-6 inline-flex items-center gap-2
                  text-[#FFBE49] font-medium hover:gap-3 transition-all">
          Know more about this program →
        </a>
      </div>
    </div>

    <div class="group rounded-[28px] overflow-hidden
                bg-[#0C0C0C] border border-white/10
                transition-all duration-500
                hover:border-[#FFBE49]/40 hover:-translate-y-2">

      <div class="relative overflow-hidden">
        <img
          src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d"
          class="w-full h-52 sm:h-60 object-cover
                 transition-transform duration-700 group-hover:scale-105" />
      </div>

      <div class="px-7 py-7 flex flex-col gap-4">
        <h3 class="text-xl sm:text-2xl font-serif">
          Growth & Scale Roadmap
        </h3>

        <p class="text-gray-400 text-sm sm:text-base">
          Structured plans to scale revenue without burning out.
        </p>

        <div class="h-px w-full bg-white/10 my-2"></div>

        <ul class="text-gray-400 text-sm space-y-2 list-disc list-inside">
          <li>Channel clarity</li>
          <li>Predictable growth levers</li>
          <li>Execution roadmap</li>
        </ul>

        <a href="#"
           class="mt-6 inline-flex items-center gap-2
                  text-[#FFBE49] font-medium hover:gap-3 transition-all">
          Know more about this program →
        </a>
      </div>
    </div>

  </div>
  </div>
  <!-- Bottom gold border accent -->
  <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-60"></div>
</section>



  <!-- blog section -->

  <!-- <section class="relative max-w-7xl mx-auto px-6 py-28">

   
    <div class="glass-rings ring-left-1"></div>
    <div class="glass-rings ring-left-2"></div>
    <div class="glass-rings ring-center"></div>

    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-12">

      <div>
        <h2 class="text-5xl font-serif text-[#FFBE49] mb-4">
          Blogs
        </h2>

        <a href="/blog"
          class="inline-flex items-center gap-2 border-b border-white pb-1 mb-14 hover:text-[#FFBE49] transition-colors">
          All Blogs →
        </a>

        <div class="flex gap-6">
          <button id="prev" class="arrow-btns">←</button>
          <button id="next" class="arrow-btns">→</button>
        </div>
      </div>


      <div class="overflow-hidden home-blog-panel">
        <div id="carousel"
          class="flex gap-12 transition-transform duration-500 ease-out">

          <?php if (!empty($blogs)): ?>
            <?php foreach ($blogs as $blog): ?>
              <?php
              $title = htmlspecialchars($blog['title']);
              $slug = htmlspecialchars($blog['slug']);
              $image = !empty($blog['image']) ? 'admin/uploads/' . htmlspecialchars($blog['image']) : 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80';
              $description = htmlspecialchars(strip_tags($blog['description']));
              $excerpt = strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
              ?>

              <a href="/blogs/<?= $slug; ?>" class="min-w-[360px] max-w-[360px] block home-blog-card">
                <img src="<?= $image; ?>"
                  alt="<?= $title; ?>"
                  class="w-full h-60 object-cover rounded-2xl mb-6" />

                <h3 class="text-2xl font-serif mb-3">
                  <?= $title; ?>
                </h3>

                <p class="text-gray-300 leading-relaxed mb-6">
                  <?= $excerpt; ?>
                </p>

                <div class="w-full h-[3px] bg-[#FFBE49] rounded-full"></div>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-gray-400 text-center py-10">
              No blogs available yet.
            </div>
          <?php endif; ?>

        </div>
      </div>

    </div>
  </section> -->
  <!-- i help you  with -->
  <section class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 overflow-hidden">
    <!-- Bottom gold border accent -->
    <!-- Top gold border accent -->
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-60"></div>
    
    <!-- Decorative Background Elements -->
    <div class="absolute top-10 left-0 w-64 h-64 bg-[#FFBE49] opacity-5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 right-0 w-96 h-96 bg-[#FFBE49] opacity-5 rounded-full blur-3xl"></div>

    <!-- Section Header -->
    <div class="relative z-10 text-center mb-16 sm:mb-20">

      <h2 class="text-4xl sm:text-5xl lg:text-6xl font-serif mb-6 sm:mb-8 animate-fade-in">
        I Help You With
      </h2>

      <p class="text-gray-400 text-base sm:text-lg leading-relaxed max-w-3xl mx-auto px-4">
        No theory. No guesswork.<br class="hidden sm:block">
        Only practical, real-world frameworks that help you build,
        fix, and scale your business with clarity.
      </p>
    </div>

    <!-- Cards Grid -->
    <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">

      <!-- Card 1 -->
      <div class="group relative rounded-[28px] bg-gradient-to-br from-black/90 to-black/60 border border-white/10 
                px-6 sm:px-8 py-8 sm:py-10 transition-all duration-500 ease-out
                hover:border-[#FFBE49]/60 hover:shadow-[0_0_40px_rgba(255,190,73,0.2)]
                hover:-translate-y-2 hover:scale-[1.02] cursor-pointer">

        <!-- Number Badge -->
        <div class="absolute -top-4 -right-4 w-12 h-12 rounded-full bg-[#FFBE49] text-black font-bold 
                  flex items-center justify-center text-lg shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
          01
        </div>

        <!-- Glowing Top Border -->
        <div class="absolute top-0 left-1/4 right-1/4 h-[2px] bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

        <h3 class="text-xl sm:text-2xl font-semibold mb-4 group-hover:text-[#FFBE49] transition-colors duration-300">
          Business Strategy
        </h3>

        <p class="text-gray-400 text-sm sm:text-base leading-relaxed group-hover:text-gray-300 transition-colors duration-300">
          Clear direction, positioning, and decision-making
          frameworks tailored to your stage.
        </p>

        <!-- Hover Accent Line -->
        <div class="mt-6 h-1 w-0 bg-[#FFBE49] rounded-full group-hover:w-full transition-all duration-500"></div>
      </div>

      <!-- Card 2 -->
      <div class="group relative rounded-[28px] bg-gradient-to-br from-black/90 to-black/60 border border-white/10 
                px-6 sm:px-8 py-8 sm:py-10 transition-all duration-500 ease-out
                hover:border-[#FFBE49]/60 hover:shadow-[0_0_40px_rgba(255,190,73,0.2)]
                hover:-translate-y-2 hover:scale-[1.02] cursor-pointer">

        <div class="absolute -top-4 -right-4 w-12 h-12 rounded-full bg-[#FFBE49] text-black font-bold 
                  flex items-center justify-center text-lg shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
          02
        </div>

        <div class="absolute top-0 left-1/4 right-1/4 h-[2px] bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

        <h3 class="text-xl sm:text-2xl font-semibold mb-4 group-hover:text-[#FFBE49] transition-colors duration-300">
          Sales Systems
        </h3>

        <p class="text-gray-400 text-sm sm:text-base leading-relaxed group-hover:text-gray-300 transition-colors duration-300">
          Simple, repeatable sales processes that
          work without chaos or pressure.
        </p>

        <div class="mt-6 h-1 w-0 bg-[#FFBE49] rounded-full group-hover:w-full transition-all duration-500"></div>
      </div>

      <!-- Card 3 -->
      <div class="group relative rounded-[28px] bg-gradient-to-br from-black/90 to-black/60 border border-white/10 
                px-6 sm:px-8 py-8 sm:py-10 transition-all duration-500 ease-out
                hover:border-[#FFBE49]/60 hover:shadow-[0_0_40px_rgba(255,190,73,0.2)]
                hover:-translate-y-2 hover:scale-[1.02] cursor-pointer">

        <div class="absolute -top-4 -right-4 w-12 h-12 rounded-full bg-[#FFBE49] text-black font-bold 
                  flex items-center justify-center text-lg shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
          03
        </div>

        <div class="absolute top-0 left-1/4 right-1/4 h-[2px] bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

        <h3 class="text-xl sm:text-2xl font-semibold mb-4 group-hover:text-[#FFBE49] transition-colors duration-300">
          Marketing Roadmap
        </h3>

        <p class="text-gray-400 text-sm sm:text-base leading-relaxed group-hover:text-gray-300 transition-colors duration-300">
          Focused marketing plans built on clarity,
          not random posting or trends.
        </p>

        <div class="mt-6 h-1 w-0 bg-[#FFBE49] rounded-full group-hover:w-full transition-all duration-500"></div>
      </div>

      <!-- Card 4 -->
      <div class="group relative rounded-[28px] bg-gradient-to-br from-black/90 to-black/60 border border-white/10 
                px-6 sm:px-8 py-8 sm:py-10 transition-all duration-500 ease-out
                hover:border-[#FFBE49]/60 hover:shadow-[0_0_40px_rgba(255,190,73,0.2)]
                hover:-translate-y-2 hover:scale-[1.02] cursor-pointer">

        <div class="absolute -top-4 -right-4 w-12 h-12 rounded-full bg-[#FFBE49] text-black font-bold 
                  flex items-center justify-center text-lg shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
          04
        </div>

        <div class="absolute top-0 left-1/4 right-1/4 h-[2px] bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

        <h3 class="text-xl sm:text-2xl font-semibold mb-4 group-hover:text-[#FFBE49] transition-colors duration-300">
          Team Building
        </h3>

        <p class="text-gray-400 text-sm sm:text-base leading-relaxed group-hover:text-gray-300 transition-colors duration-300">
          Hiring, structure, and delegation systems
          that reduce dependency on you.
        </p>

        <div class="mt-6 h-1 w-0 bg-[#FFBE49] rounded-full group-hover:w-full transition-all duration-500"></div>
      </div>

      <!-- Card 5 -->
      <div class="group relative rounded-[28px] bg-gradient-to-br from-black/90 to-black/60 border border-white/10 
                px-6 sm:px-8 py-8 sm:py-10 transition-all duration-500 ease-out
                hover:border-[#FFBE49]/60 hover:shadow-[0_0_40px_rgba(255,190,73,0.2)]
                hover:-translate-y-2 hover:scale-[1.02] cursor-pointer">

        <div class="absolute -top-4 -right-4 w-12 h-12 rounded-full bg-[#FFBE49] text-black font-bold 
                  flex items-center justify-center text-lg shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
          05
        </div>

        <div class="absolute top-0 left-1/4 right-1/4 h-[2px] bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

        <h3 class="text-xl sm:text-2xl font-semibold mb-4 group-hover:text-[#FFBE49] transition-colors duration-300">
          Automation andProcesses
        </h3>

        <p class="text-gray-400 text-sm sm:text-base leading-relaxed group-hover:text-gray-300 transition-colors duration-300">
          Clean workflows and tools that
          save time and prevent burnout.
        </p>

        <div class="mt-6 h-1 w-0 bg-[#FFBE49] rounded-full group-hover:w-full transition-all duration-500"></div>
      </div>

      <!-- Card 6 -->
      <div class="group relative rounded-[28px] bg-gradient-to-br from-black/90 to-black/60 border border-white/10 
                px-6 sm:px-8 py-8 sm:py-10 transition-all duration-500 ease-out
                hover:border-[#FFBE49]/60 hover:shadow-[0_0_40px_rgba(255,190,73,0.2)]
                hover:-translate-y-2 hover:scale-[1.02] cursor-pointer">

        <div class="absolute -top-4 -right-4 w-12 h-12 rounded-full bg-[#FFBE49] text-black font-bold 
                  flex items-center justify-center text-lg shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
          06
        </div>

        <div class="absolute top-0 left-1/4 right-1/4 h-[2px] bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

        <h3 class="text-xl sm:text-2xl font-semibold mb-4 group-hover:text-[#FFBE49] transition-colors duration-300">
          Scalable Business Models
        </h3>

        <p class="text-gray-400 text-sm sm:text-base leading-relaxed group-hover:text-gray-300 transition-colors duration-300">
          Design models that grow revenue
          without growing complexity.
        </p>

        <div class="mt-6 h-1 w-0 bg-[#FFBE49] rounded-full group-hover:w-full transition-all duration-500"></div>
      </div>

    </div>

    <!-- Bottom CTA -->

  </section>

  <!-- the results -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 relative overflow-hidden">
    <!-- Top gold border accent -->
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-60"></div>

    <!-- Decorative corners -->
    <div class="absolute top-10 left-10 w-24 h-24 border-l-2 border-t-2 border-[#FFBE49] opacity-20"></div>
    <div class="absolute top-10 right-10 w-24 h-24 border-r-2 border-t-2 border-[#FFBE49] opacity-20"></div>

    <!-- Title Section -->
    <div class="text-center mb-16 sm:mb-20 relative z-10">
      <h2 class="text-4xl sm:text-5xl lg:text-6xl font-serif mb-6">
        The Results
      </h2>
      <p class="text-xl sm:text-2xl lg:text-3xl text-gray-300 max-w-4xl mx-auto leading-relaxed">
        Entrepreneurs across 15+ industries have scaled using my methods
      </p>
      
      <!-- Gold underline accent -->
      <div class="w-32 h-1 bg-[#FFBE49] mx-auto mt-8"></div>
    </div>

    <!-- Results Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10 relative z-10">

      <!-- Result Card 1 -->
      <div class="group relative">
        <div class="h-full border-2 border-white/10 rounded-2xl p-8 bg-black/40 backdrop-blur-sm
                    transition-all duration-500 ease-out
                    hover:border-[#FFBE49] hover:shadow-[0_0_30px_rgba(255,190,73,0.15)]
                    hover:-translate-y-2 cursor-pointer">
          
          <!-- Number Badge -->
          <div class="absolute -top-4 -right-4 w-12 h-12 bg-[#FFBE49] text-black font-bold text-xl
                      flex items-center justify-center rounded-full shadow-lg
                      group-hover:scale-110 transition-transform duration-300">
            01
          </div>

          <!-- Icon Container -->
          <div class="relative w-32 h-32 mx-auto mb-8 flex items-center justify-center">
            <!-- Outer glow ring -->
            <div class="absolute inset-0 border-2 border-[#FFBE49] rounded-full opacity-30
                        group-hover:opacity-60 transition-all duration-300 group-hover:scale-110"></div>
            <!-- Middle ring -->
            <div class="absolute inset-3 border-2 border-[#FFBE49] rounded-full opacity-20
                        group-hover:opacity-40 transition-all duration-300 group-hover:scale-105"></div>
            <!-- Background circle for icon -->
            <div class="absolute inset-6 bg-[#ffff] bg-opacity-10 rounded-full
                        group-hover:bg-opacity-20 transition-all duration-300"></div>
            <!-- Icon -->
            <img src="img/imgs/h1.png" class="relative w-20 h-20 object-contain z-10
                      group-hover:scale-125 transition-all duration-300 drop-shadow-lg
                      brightness-110" alt="Revenue Icon" />
          </div>

          <!-- Title -->
          <h3 class="text-xl sm:text-2xl font-semibold text-center mb-4 text-white
                     group-hover:text-[#FFBE49] transition-colors duration-300">
            Higher & Predictable Revenue
          </h3>

          <!-- Description -->
          <p class="text-gray-400 text-center text-sm sm:text-base leading-relaxed
                    group-hover:text-gray-300 transition-colors duration-300">
            Build sustainable revenue streams with proven strategies that deliver consistent growth
          </p>

          <!-- Bottom accent line -->
          <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-1 bg-[#FFBE49]
                      group-hover:w-3/4 transition-all duration-500 rounded-full"></div>
        </div>
      </div>

      <!-- Result Card 2 -->
      <div class="group relative">
        <div class="h-full border-2 border-white/10 rounded-2xl p-8 bg-black/40 backdrop-blur-sm
                    transition-all duration-500 ease-out
                    hover:border-[#FFBE49] hover:shadow-[0_0_30px_rgba(255,190,73,0.15)]
                    hover:-translate-y-2 cursor-pointer">
          
          <div class="absolute -top-4 -right-4 w-12 h-12 bg-[#FFBE49] text-black font-bold text-xl
                      flex items-center justify-center rounded-full shadow-lg
                      group-hover:scale-110 transition-transform duration-300">
            02
          </div>

          <div class="relative w-32 h-32 mx-auto mb-8 flex items-center justify-center">
            <div class="absolute inset-0 border-2 border-[#FFBE49] rounded-full opacity-30
                        group-hover:opacity-60 transition-all duration-300 group-hover:scale-110"></div>
            <div class="absolute inset-3 border-2 border-[#FFBE49] rounded-full opacity-20
                        group-hover:opacity-40 transition-all duration-300 group-hover:scale-105"></div>
            <div class="absolute inset-6 bg-[#ffff] bg-opacity-10 rounded-full
                        group-hover:bg-opacity-20 transition-all duration-300"></div>
            <img src="img/imgs/h2.png" class="relative w-20 h-20 object-contain z-10
                      group-hover:scale-125 transition-all duration-300 drop-shadow-lg
                      brightness-110" alt="Time Freedom Icon" />
          </div>

          <h3 class="text-xl sm:text-2xl font-semibold text-center mb-4 text-white
                     group-hover:text-[#FFBE49] transition-colors duration-300">
            More Time Freedom
          </h3>

          <p class="text-gray-400 text-center text-sm sm:text-base leading-relaxed
                    group-hover:text-gray-300 transition-colors duration-300">
            Reclaim your time and focus on strategic growth while your business runs smoothly
          </p>

          <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-1 bg-[#FFBE49]
                      group-hover:w-3/4 transition-all duration-500 rounded-full"></div>
        </div>
      </div>

      <!-- Result Card 3 -->
      <div class="group relative">
        <div class="h-full border-2 border-white/10 rounded-2xl p-8 bg-black/40 backdrop-blur-sm
                    transition-all duration-500 ease-out
                    hover:border-[#FFBE49] hover:shadow-[0_0_30px_rgba(255,190,73,0.15)]
                    hover:-translate-y-2 cursor-pointer">
          
          <div class="absolute -top-4 -right-4 w-12 h-12 bg-[#FFBE49] text-black font-bold text-xl
                      flex items-center justify-center rounded-full shadow-lg
                      group-hover:scale-110 transition-transform duration-300">
            03
          </div>

          <div class="relative w-32 h-32 mx-auto mb-8 flex items-center justify-center">
            <div class="absolute inset-0 border-2 border-[#FFBE49] rounded-full opacity-30
                        group-hover:opacity-60 transition-all duration-300 group-hover:scale-110"></div>
            <div class="absolute inset-3 border-2 border-[#FFBE49] rounded-full opacity-20
                        group-hover:opacity-40 transition-all duration-300 group-hover:scale-105"></div>
            <div class="absolute inset-6 bg-[#ffff] bg-opacity-10 rounded-full
                        group-hover:bg-opacity-20 transition-all duration-300"></div>
            <img src="img/imgs/h3.png" class="relative w-20 h-20 object-contain z-10
                      group-hover:scale-125 transition-all duration-300 drop-shadow-lg
                      brightness-110" alt="Team Icon" />
          </div>

          <h3 class="text-xl sm:text-2xl font-semibold text-center mb-4 text-white
                     group-hover:text-[#FFBE49] transition-colors duration-300">
            Stronger, Accountable Teams
          </h3>

          <p class="text-gray-400 text-center text-sm sm:text-base leading-relaxed
                    group-hover:text-gray-300 transition-colors duration-300">
            Build high-performing teams that take ownership and drive results independently
          </p>

          <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-1 bg-[#FFBE49]
                      group-hover:w-3/4 transition-all duration-500 rounded-full"></div>
        </div>
      </div>

      <!-- Centered wrapper for cards 4 & 5 -->
      <div class="sm:col-span-2 lg:col-span-3 flex flex-wrap justify-center gap-8 lg:gap-10">
        
        <!-- Result Card 4 -->
        <div class="group relative w-full sm:w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.67rem)]">
          <div class="h-full border-2 border-white/10 rounded-2xl p-8 bg-black/40 backdrop-blur-sm
                      transition-all duration-500 ease-out
                      hover:border-[#FFBE49] hover:shadow-[0_0_30px_rgba(255,190,73,0.15)]
                      hover:-translate-y-2 cursor-pointer">
            
            <div class="absolute -top-4 -right-4 w-12 h-12 bg-[#FFBE49] text-black font-bold text-xl
                        flex items-center justify-center rounded-full shadow-lg
                        group-hover:scale-110 transition-transform duration-300">
              04
            </div>

            <div class="relative w-32 h-32 mx-auto mb-8 flex items-center justify-center">
              <div class="absolute inset-0 border-2 border-[#FFBE49] rounded-full opacity-30
                          group-hover:opacity-60 transition-all duration-300 group-hover:scale-110"></div>
              <div class="absolute inset-3 border-2 border-[#FFBE49] rounded-full opacity-20
                          group-hover:opacity-40 transition-all duration-300 group-hover:scale-105"></div>
              <div class="absolute inset-6 bg-[#ffff] bg-opacity-10 rounded-full
                          group-hover:bg-opacity-20 transition-all duration-300"></div>
              <img src="img/imgs/h4.png" class="relative w-20 h-20 object-contain z-10
                        group-hover:scale-125 transition-all duration-300 drop-shadow-lg
                        brightness-110" alt="Growth Icon" />
            </div>

            <h3 class="text-xl sm:text-2xl font-semibold text-center mb-4 text-white
                       group-hover:text-[#FFBE49] transition-colors duration-300">
              Business Stability & Momentum
            </h3>

            <p class="text-gray-400 text-center text-sm sm:text-base leading-relaxed
                      group-hover:text-gray-300 transition-colors duration-300">
              Create sustainable growth momentum with solid foundations and clear direction
            </p>

            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-1 bg-[#FFBE49]
                        group-hover:w-3/4 transition-all duration-500 rounded-full"></div>
          </div>
        </div>

        <!-- Result Card 5 -->
        <div class="group relative w-full sm:w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.67rem)]">
          <div class="h-full border-2 border-white/10 rounded-2xl p-8 bg-black/40 backdrop-blur-sm
                      transition-all duration-500 ease-out
                      hover:border-[#FFBE49] hover:shadow-[0_0_30px_rgba(255,190,73,0.15)]
                      hover:-translate-y-2 cursor-pointer">
            
            <div class="absolute -top-4 -right-4 w-12 h-12 bg-[#FFBE49] text-black font-bold text-xl
                        flex items-center justify-center rounded-full shadow-lg
                        group-hover:scale-110 transition-transform duration-300">
              05
            </div>

            <div class="relative w-32 h-32 mx-auto mb-8 flex items-center justify-center">
              <div class="absolute inset-0 border-2 border-[#FFBE49] rounded-full opacity-30
                          group-hover:opacity-60 transition-all duration-300 group-hover:scale-110"></div>
              <div class="absolute inset-3 border-2 border-[#FFBE49] rounded-full opacity-20
                          group-hover:opacity-40 transition-all duration-300 group-hover:scale-105"></div>
              <div class="absolute inset-6 bg-[#ffff] bg-opacity-10 rounded-full
                          group-hover:bg-opacity-20 transition-all duration-300"></div>
              <img src="img/imgs/h5.png" class="relative w-20 h-20 object-contain z-10
                        group-hover:scale-125 transition-all duration-300 drop-shadow-lg
                        brightness-110" alt="Systems Icon" />
            </div>

            <h3 class="text-xl sm:text-2xl font-semibold text-center mb-4 text-white
                       group-hover:text-[#FFBE49] transition-colors duration-300">
              System-Driven Operations
            </h3>

            <p class="text-gray-400 text-center text-sm sm:text-base leading-relaxed
                      group-hover:text-gray-300 transition-colors duration-300">
              Implement scalable systems that ensure consistency and eliminate daily chaos
            </p>

            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-1 bg-[#FFBE49]
                        group-hover:w-3/4 transition-all duration-500 rounded-full"></div>
          </div>
        </div>

      </div>

    </div>

    <!-- Bottom decorative corners -->
    <div class="absolute bottom-10 left-10 w-24 h-24 border-l-2 border-b-2 border-[#FFBE49] opacity-20"></div>
    <div class="absolute bottom-10 right-10 w-24 h-24 border-r-2 border-b-2 border-[#FFBE49] opacity-20"></div>

    <!-- Bottom gold border accent -->
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-60"></div>
  </section>


  <section class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-24 md:py-32 relative">    <!-- Top gold border accent -->
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-[#FFBE49] to-transparent opacity-60"></div>
    <!-- TITLE -->
    <h2 class="text-3xl sm:text-4xl md:text-5xl font-serif mb-6 sm:mb-12 md:mb-14">
      Testimonials
    </h2>

    <!-- OUTER FRAME -->
    <div class="testimonial-frame relative rounded-3xl sm:rounded-[28px] border border-white/70 px-3 sm:px-6 md:px-10 py-8 sm:py-16 md:py-20 overflow-hidden">

      <!-- BACKGROUND RINGS -->
      <div class="glass-ring ring-lg hidden sm:block left-10 top-20"></div>
      <div class="glass-ring ring-md hidden md:block right-32 top-16"></div>
      <div class="glass-ring ring-lg hidden sm:block left-1/2 bottom-[-90px] -translate-x-1/2"></div>

      <!-- ARROWS -->
      <div class="testimonial-arrows absolute right-3 sm:right-6 md:right-10 top-3 sm:top-6 md:top-10 flex gap-2 sm:gap-3 md:gap-4 z-20">
        <button class="arrow-btn z-50 shadow-md bg-black/70" onclick="prevReview()">←</button>
        <button class="arrow-btn z-50 shadow-md bg-black/70" onclick="nextReview()">→</button>
      </div>

      <!-- CAROUSEL VIEWPORT -->
      <div class="relative z-10 max-w-4xl mx-auto overflow-hidden">

        <!-- TRACK -->
        <div id="reviewTrack" class="flex gap-4 sm:gap-6 transition-transform duration-1000 ease-out">

          <!-- REVIEW 1 -->
          <div class="max-w-[100%] bg-white text-black rounded-2xl sm:rounded-[22px] px-4 sm:px-6 md:px-10 py-4 sm:py-7 md:py-8 review-card flex-shrink-0">
            <div class="flex items-center gap-2 sm:gap-4 mb-2 sm:mb-3">
              <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-gray-300 flex items-center justify-center flex-shrink-0 text-sm sm:text-base">
                👤
              </div>
              <div class="min-w-0">
                <h3 class="text-lg sm:text-2xl font-serif leading-tight">
                  Rakesh Patel
                </h3>

                <p class="text-xs sm:text-sm text-gray-500 flex flex-wrap gap-1.5 sm:gap-3">
                  <span>1 Review</span>
                  <span>📍 Ahmedabad, India</span>
                </p>

              </div>
            </div>

            <hr class="border-black/70 my-3 sm:my-6 md:my-10" />

            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-1 sm:gap-0 mb-2 sm:mb-4">
              <div class="text-yellow-500 text-sm sm:text-lg">★★★★★</div>
              <div class="text-xs sm:text-sm text-gray-500">March 2024</div>

            </div>

            <h4 class="text-sm sm:text-lg font-semibold mb-1.5 sm:mb-2">
              Clear direction and practical guidance
            </h4>

            <p class="text-xs sm:text-base text-gray-700 leading-snug sm:leading-relaxed">
              I was stuck with ideas but no clear execution plan. Parth helped me
              structure my business, fix my pricing, and focus on the right things.
              The guidance was practical, simple, and immediately useful.
            </p>

          </div>

          <!-- REVIEW 2 -->
          <div class="max-w-[100%] bg-white text-black rounded-2xl sm:rounded-[22px] px-4 sm:px-6 md:px-10 py-4 sm:py-7 md:py-8 review-card flex-shrink-0">
            <div class="flex items-center gap-2 sm:gap-4 mb-2 sm:mb-3">
              <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-gray-300 flex items-center justify-center flex-shrink-0 text-sm sm:text-base">
                👤
              </div>
              <div class="min-w-0">
                <h3 class="text-lg sm:text-2xl font-serif leading-tight">
                  Neha Shah
                </h3>

                <p class="text-xs sm:text-sm text-gray-500 flex flex-wrap gap-1.5 sm:gap-3">
                  <span>2 Reviews</span>
                  <span>📍 Mumbai, India</span>
                </p>

              </div>
            </div>

            <hr class="border-black/70 my-3 sm:my-6 md:my-10" />

            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-1 sm:gap-0 mb-2 sm:mb-4">
              <div class="text-yellow-500 text-sm sm:text-lg">★★★★☆</div>
              <div class="text-xs sm:text-sm text-gray-500">January 2024</div>

            </div>

            <h4 class="text-sm sm:text-lg font-semibold mb-1.5 sm:mb-2">
              Simple systems that actually work
            </h4>

            <p class="text-xs sm:text-base text-gray-700 leading-snug sm:leading-relaxed">
              What I liked most was the clarity. No over-complicated theories.
              Just clean systems for sales and operations that I could implement
              immediately in my business.
            </p>

          </div>

          <!-- review 3 -->
          <div class="max-w-[100%] bg-white text-black rounded-2xl sm:rounded-[22px] px-4 sm:px-6 md:px-10 py-4 sm:py-7 md:py-8 review-card flex-shrink-0">
            <div class="flex items-center gap-2 sm:gap-4 mb-2 sm:mb-3">
              <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-gray-300 flex items-center justify-center flex-shrink-0 text-sm sm:text-base">
                👤
              </div>
              <div class="min-w-0">
                <h3 class="text-lg sm:text-2xl font-serif leading-tight">
                  Aman Verma
                </h3>

                <p class="text-xs sm:text-sm text-gray-500 flex flex-wrap gap-1.5 sm:gap-3">
                  <span>1 Review</span>
                  <span>📍 Bengaluru, India</span>
                </p>

              </div>
            </div>

            <hr class="border-black/70 my-3 sm:my-6 md:my-10" />

            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-1 sm:gap-0 mb-2 sm:mb-4">
              <div class="text-yellow-500 text-sm sm:text-lg">★★★★★</div>
              <div class="text-xs sm:text-sm text-gray-500">March 2024</div>

            </div>

            <h4 class="text-sm sm:text-lg font-semibold mb-1.5 sm:mb-2">
              Exactly what a founder needs
            </h4>

            <p class="text-xs sm:text-base text-gray-700 leading-snug sm:leading-relaxed">
              Straightforward advice, no fluff. Helped me understand where my
              business was leaking money and how to fix it step by step.
            </p>

          </div>


        </div>
      </div>
    </div>
  </section>




  <div id="footer-container"></div>
  <script src="assets/js/loader.js" defer></script>
  <script src="assets/js/components-loader.js"></script>
  <script src="assets/js/navbar.js" defer></script>
  <script src="assets/js/index.js" defer></script>

</body>

</html>