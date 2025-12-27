<?php
require_once __DIR__ . '/db.php';

// Fetch top 6 blogs
$sql = "SELECT b.*, GROUP_CONCAT(c.name SEPARATOR '|') AS categories
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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home page</title>
  <link rel="stylesheet" href="assets/css/main.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Home blog card separation: gold border, subtle shadow */
    .home-blog-card {
      border: 1px solid #FFBE49;
      border-radius: 16px;
      padding: 16px;
      background: rgba(12, 12, 12, 0.9);
      box-shadow: 0 8px 24px rgba(0,0,0,0.35);
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .home-blog-card:hover {
      border-color: #ffcc66; /* lighter gold on hover */
      box-shadow: 0 12px 28px rgba(0,0,0,0.4);
    }
    /* Panel behind carousel to separate from page background */
  
  </style>
</head>

<body class="bg-black text-white">


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
      <h1 class="text-4xl md:text-5xl xl:text-6xl font-serif leading-tight mb-6">
        PARTH JETHVA 
        <br />
         India’s Rising Business Growth Coach
      </h1>

      <p class="text-gray-300 max-w-xl leading-relaxed mb-10">
        Helping entrepreneurs scale with clarity, smart systems, and predictable
        growth — so their business runs smoothly, efficiently, and profitably
        without constant overwhelm.
      </p>

      <!-- CTA -->
      <button class="cta-btn inline-flex items-center gap-4 px-7 py-3 rounded-full shadow-lg">
        <span class="font-medium">Start Your Journey Here</span>
        <span class="cta-arrow w-10 h-10 rounded-full flex items-center justify-center text-lg">
          →
        </span>
      </button>
    </div>

    <!-- RIGHT IMAGE -->
    <div class="relative flex justify-center">
      <img
        src="img/imgs/client.jpeg"
        alt="Client"
        class="hero-img max-w-md w-full grayscale rounded"
      />
    </div>

  </div>
</section>

  <section class="max-w-7xl mx-auto px-6 py-28">

    <!-- Heading -->
    <h2 class="text-4xl md:text-5xl font-serif mb-20">
      My 3 Growth Pillars
    </h2>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 pillars-grid">

      <!-- CARD 1 -->
      <div class="card-base px-12 py-16 text-center">
        <div class="mb-8 text-gray-400">
         <img src="./img/design/Clarity.svg" alt="" class="w-18 h-18 mx-auto"/>
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
      <div class="card-base px-12 py-16 text-center">
        <div class="mb-8 text-gray-400">
          <img src="./img/design/Computer.svg" alt="" class="w-18 h-18 mx-auto"/>
        </div>

        <h3 class="text-3xl font-serif text-[#FFBE49] mb-6">
          Systems & Structure
        </h3>

        <p class="text-gray-300 text-lg leading-relaxed">
          Build a business that runs without daily chaos.
        </p>
      </div>

      <!-- CARD 3 -->
      <div class="card-base px-12 py-16 text-center">
        <div class="mb-8 text-gray-400">
          <img src="./img/design/Growth.svg" alt="" class="w-18 h-18 mx-auto"/>
        </div>

        <h3 class="text-3xl font-serif text-[#FFBE49] mb-6">
          Profit & Performance
        </h3>

        <p class="text-gray-300 text-lg leading-relaxed">
          Scale consistently with proven business frameworks.
        </p>
      </div>

    </div>
  </section>

<!-- Signature progrmams -->

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">

  <h2 class="text-3xl sm:text-4xl font-bold mb-10 sm:mb-16">
    Signature Programs
  </h2>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">

    <!-- Program Card -->
    <div class="card-shadow overflow-hidden flex flex-col h-full">

      <!-- Image -->
      <img
        src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d"
        class="w-full h-48 sm:h-56 object-cover rounded-t-[28px]"
      />

      <!-- Content -->
      <div class="px-7 py-6 flex-1 flex flex-col gap-3">
        <h3 class="text-xl sm:text-2xl font-serif">
          Lorem ipsum dolor sit
        </h3>

        <p class="text-gray-300 text-base leading-relaxed">
          Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
          ut aliquip ex ea commodo consequat.
        </p>
      </div>

      <!-- Button (flush to bottom) -->
      <button
        class="bg-[#FFBE49] text-black font-semibold
               py-3.5 sm:py-4 w-full rounded-b-[28px] leading-none">
        Get Started
      </button>

    </div>

     <div class="card-shadow overflow-hidden flex flex-col h-full">

      <!-- Image -->
      <img
        src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d"
        class="w-full h-48 sm:h-56 object-cover rounded-t-[28px]"
      />

      <!-- Content -->
      <div class="px-7 py-6 flex-1 flex flex-col gap-3">
        <h3 class="text-xl sm:text-2xl font-serif">
          Lorem ipsum dolor sit
        </h3>

        <p class="text-gray-300 text-base leading-relaxed">
          Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
          ut aliquip ex ea commodo consequat.
        </p>
      </div>

      <!-- Button (flush to bottom) -->
      <button
        class="bg-[#FFBE49] text-black font-semibold
               py-3.5 sm:py-4 w-full rounded-b-[28px] leading-none">
        Get Started
      </button>

    </div>
   <div class="card-shadow overflow-hidden flex flex-col h-full">

      <!-- Image -->
      <img
        src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d"
        class="w-full h-48 sm:h-56 object-cover rounded-t-[28px]"
      />

      <!-- Content -->
      <div class="px-7 py-6 flex-1 flex flex-col gap-3">
        <h3 class="text-xl sm:text-2xl font-serif">
          Lorem ipsum dolor sit
        </h3>

        <p class="text-gray-300 text-base leading-relaxed">
          Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
          ut aliquip ex ea commodo consequat.
        </p>
      </div>

      <!-- Button (flush to bottom) -->
      <button
        class="bg-[#FFBE49] text-black font-semibold
               py-3.5 sm:py-4 w-full rounded-b-[28px] leading-none">
        Get Started
      </button>

    </div>
  </div>
</section>
 

<!-- blog section -->

<section class="relative max-w-7xl mx-auto px-6 py-28">

  <!-- GLASS RING BACKGROUND -->
  <div class="glass-rings ring-left-1"></div>
  <div class="glass-rings ring-left-2"></div>
  <div class="glass-rings ring-center"></div>

  <div class="relative z-10 grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-12">

    <!-- LEFT PANEL -->
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

    <!-- CAROUSEL -->
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
</section>


<!-- the reulut  -->
 <section class="max-w-7xl mx-auto px-6 py-28">

  <!-- Title -->
  <h2 class="text-4xl md:text-5xl font-serif mb-20">
    The Results
  </h2>

  <!-- GRID -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-y-24 gap-x-10 place-items-center">

    <!-- ITEM 1 -->
    <div class="result-item text-center max-w-xs lg:col-span-2 lg:col-start-1">
      <p class="text-gray-300 mb-8">
        Entrepreneurs across 15+ industries have scaled using my methods
      </p>
      <div class="result-circle w-28 h-28 rounded-full flex items-center justify-center mx-auto">
        <img src="img/imgs/h1.jpeg" class="w-14 h-14 object-contain" />
      </div>
    </div>

    <!-- ITEM 2 -->
    <div class="result-item text-center max-w-xs lg:col-start-3 lg:col-span-2">
      <p class="text-gray-300 mb-8">
        More time freedom for the founder
      </p>
      <div class="result-circle w-28 h-28 rounded-full flex items-center justify-center mx-auto">
        <img src="img/imgs/h3.jpeg" class="w-14 h-14 object-contain" />
      </div>
    </div>

    <!-- ITEM 3 -->
    <div class="result-item text-center max-w-xs lg:col-span-2 lg:col-start-5">
      <p class="text-gray-300 mb-8">
        Increased business stability & growth momentum
      </p>
      <div class="result-circle w-28 h-28 rounded-full flex items-center justify-center mx-auto">
        <img src="img/imgs/h5.jpeg" class="w-14 h-14 object-contain" />
      </div>
    </div>

    <!-- ITEM 4 (BETWEEN 1 & 2) -->
    <div class="result-item text-center max-w-xs lg:col-span-2 lg:col-start-2">
      <div class="result-circle w-28 h-28 rounded-full flex items-center justify-center mx-auto mb-8">
        <img src="img/imgs/h2.jpeg" class="w-14 h-14 object-contain" />
      </div>
      <p class="text-gray-300">
        Stable & system-driven operations
      </p>
    </div>

    <!-- ITEM 5 (BETWEEN 2 & 3) -->
    <div class="result-item text-center max-w-xs lg:col-span-2 lg:col-start-4">
      <div class="result-circle w-28 h-28 rounded-full flex items-center justify-center mx-auto mb-8">
        <img src="img/imgs/h4.jpeg" class="w-14 h-14 object-contain" />
      </div>
      <p class="text-gray-300">
        Stronger, more accountable teams
      </p>
    </div>

  </div>
</section>


<section class="max-w-7xl mx-auto px-6 py-32">

  <!-- TITLE -->
  <h2 class="text-4xl md:text-5xl font-serif mb-14">
    Reviews
  </h2>

  <!-- OUTER FRAME -->
  <div class="relative rounded-[28px] border border-white/70 px-10 py-20 overflow-hidden">

    <!-- BACKGROUND RINGS -->
    <div class="glass-ring ring-lg left-10 top-20"></div>
    <div class="glass-ring ring-md right-32 top-16"></div>
    <div class="glass-ring ring-lg left-1/2 bottom-[-90px] -translate-x-1/2"></div>

    <!-- ARROWS -->
    <div class="absolute right-10 top-10 flex gap-4 z-20">
      <button class="arrow-btn" onclick="prevReview()">←</button>
      <button class="arrow-btn" onclick="nextReview()">→</button>
    </div>

    <!-- CAROUSEL VIEWPORT -->
    <div class="relative z-10 max-w-4xl mx-auto overflow-hidden">

      <!-- TRACK -->
      <div id="reviewTrack" class="flex transition-transform duration-500 ease-out">

        <!-- REVIEW 1 -->
        <div class="min-w-full bg-white text-black rounded-[22px] px-10 py-8 review-card">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-full bg-gray-300 flex items-center justify-center">
              👤
            </div>
            <div>
              <h3 class="text-2xl font-serif">Lorem ipsum dolor</h3>
              <p class="text-sm text-gray-500 flex gap-3">
                <span>1 Review</span>
                <span>📍 India</span>
              </p>
            </div>
          </div>

          <hr class="border-black/70 my-4" />

          <div class="flex justify-between items-center mb-4">
            <div class="text-yellow-500 text-lg">★★★★☆</div>
            <div class="text-sm text-gray-500">Date of review</div>
          </div>

          <h4 class="text-lg font-semibold mb-2">Brief Review</h4>
          <p class="text-gray-700 leading-relaxed">
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
          </p>
        </div>

        <!-- REVIEW 2 -->
        <div class="min-w-full bg-white text-black rounded-[22px] px-10 py-8 review-card">
          <div class="flex items-center gap-4 mb-3">
            <div class="w-14 h-14 rounded-full bg-gray-300 flex items-center justify-center">
              👤
            </div>
            <div>
              <h3 class="text-2xl font-serif">John Doe</h3>
              <p class="text-sm text-gray-500 flex gap-3">
                <span>3 Reviews</span>
                <span>📍 USA</span>
              </p>
            </div>
          </div>

          <hr class="border-black/70 my-4" />

          <div class="flex justify-between items-center mb-4">
            <div class="text-yellow-500 text-lg">★★★★★</div>
            <div class="text-sm text-gray-500">Date of review</div>
          </div>

          <h4 class="text-lg font-semibold mb-2">Excellent Experience</h4>
          <p class="text-gray-700 leading-relaxed">
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
          </p>
        </div>

      </div>
    </div>
  </div>
</section>





    <div id="footer-container"></div>
<script src="assets/js/index.js"></script>

</body>

</html>