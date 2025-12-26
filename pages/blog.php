<?php
require_once __DIR__ . '/../db.php'; // ensure $conn is available

// Fetch all categories for filter
$categories = [];
$catRes = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");
if ($catRes) {
  while ($row = mysqli_fetch_assoc($catRes)) {
    $categories[] = $row;
  }
}

// Selected category (id) filter
$selectedCategoryId = isset($_GET['category']) ? intval($_GET['category']) : 0;

// Build blog query with category join and aggregated category list
$sql = "SELECT b.*, GROUP_CONCAT(c.name SEPARATOR '|') AS categories
    FROM blog b
    LEFT JOIN blog_categories bc ON bc.blog_id = b.id
    LEFT JOIN categories c ON c.id = bc.category_id";

$params = [];
$types = '';
if ($selectedCategoryId > 0) {
  $sql .= " WHERE bc.category_id = ?";
  $types .= 'i';
  $params[] = $selectedCategoryId;
}

$sql .= " GROUP BY b.id ORDER BY b.id DESC";

if (!empty($params)) {
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, $types, ...$params);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
} else {
  $result = mysqli_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Blogs | Parth Coaching</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            serif: ['"Playfair Display"', '"Cormorant Garamond"', 'Georgia', 'serif'],
            sans: ['"Inter"', 'system-ui', '-apple-system', 'sans-serif']
          }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet" />

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body { 
      background: #000;
      min-height: 100vh;
      font-family: 'Inter', sans-serif;
    }
    
    /* Blog Card Hover Effects - modern, subtle scale + border + shadow */
    .blog-card {
      position: relative;
      overflow: hidden;
      transition: transform 0.28s cubic-bezier(.2,.9,.2,1), box-shadow 0.28s ease, border-color 0.28s ease;
      display: flex;
      flex-direction: column;
      border: 1px solid rgba(255,255,255,0.04);
      border-radius: 0.75rem;
      will-change: transform;
      background: linear-gradient(180deg, rgba(255,255,255,0.01), rgba(0,0,0,0.15));
    }

    .blog-card:hover {
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 18px 40px rgba(11,8,0,0.45);
      border-color: rgba(184,134,11,0.45); /* golden border on hover */
    }

    .blog-card:hover .blog-image {
      transform: scale(1.06) translateZ(0);
    }

    .blog-image {
      transition: transform 0.5s cubic-bezier(.2,.9,.2,1);
      backface-visibility: hidden;
      transform-origin: center center;
    }

    .blog-overlay {
      transition: background 0.28s ease, opacity 0.28s ease;
    }
    
    /* Category Dropdown */
    .category-select {
      appearance: none;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.08);
      transition: all 0.2s ease;
      color: #f7edd0;
    }
    .category-select:hover { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.12); }
    .category-select:focus { outline: none; border-color: rgba(184,134,11,0.45); background: rgba(255,255,255,0.03); }

    /* Category pill styles */
    .category-pill { font-size: 0.65rem; text-transform: uppercase; letter-spacing: .11em; color: #f5eed6; background: rgba(184,134,11,0.08); border: 1px solid rgba(184,134,11,0.12); padding: 0.25rem 0.6rem; border-radius: 999px; }
    
    /* Wavy Section Divider */
    .wave-divider {
      position: absolute;
      bottom: -1px;
      left: 0;
      width: 100%;
      overflow: hidden;
      line-height: 0;
    }
    
    .wave-divider svg {
      position: relative;
      display: block;
      width: calc(100% + 1.3px);
      height: 80px;
    }
    
    .wave-divider .shape-fill {
      fill: #000;
    }

    /* Make the wave divider more pronounced by adding a subtle top shadow */
    .wave-divider { filter: drop-shadow(0 -6px 8px rgba(0,0,0,0.6)); }
  </style>
</head>
<body class="text-white antialiased">
    <div id="navbar-container"></div>
  <!-- ================= HERO SECTION ================= -->
  <section class="relative w-full h-[75vh] md:h-[85vh] overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0">
      <img 
        src="../img/imgs/bloggero.jpeg" 
        alt="Blogs Hero" 
        class="w-full h-full object-cover"
      />
      <!-- Dark Overlay Gradient (lighter to reveal wave divider) -->
      <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-black"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 h-full flex items-center justify-center">
      <div class="text-center px-6 md:px-10 max-w-5xl">
        <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl xl:text-8xl leading-tight tracking-tight text-white">
          Insights & Perspectives
        </h1>
        <p class="mt-6 text-lg md:text-xl text-gray-300 max-w-2xl mx-auto">
          Exploring business, growth, and strategy in the world of international trade
        </p>
      </div>
    </div>

    <!-- Wavy Bottom Divider -->
    <div class="wave-divider">
      <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
        <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
      </svg>
    </div>
  </section>

  <!-- ================= BLOGS SECTION ================= -->
  <section class="bg-black py-20 md:py-28">
    <div class="max-w-7xl mx-auto px-6 md:px-10 lg:px-16">
      
      <!-- Section Header -->
      <div class="mb-16">
        <h2 class="font-serif text-4xl md:text-5xl lg:text-6xl text-white mb-10">
          Blogs
        </h2>

        <!-- Category Filter as Cards -->
        <div class="flex flex-wrap gap-3 items-center">
          <a 
            href="blog.php"
            class="<?= $selectedCategoryId === 0 ? 'bg-amber-600 border-amber-600' : 'bg-gray-800 hover:bg-gray-700 border-gray-700' ?> border px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 inline-block"
          >
            All Categories
          </a>
          
          <?php foreach ($categories as $cat): ?>
            <a
              href="blog.php?category=<?= (int)$cat['id']; ?>"
              class="<?= $selectedCategoryId === (int)$cat['id'] ? 'bg-amber-600 border-amber-600' : 'bg-gray-800 hover:bg-gray-700 border-gray-700' ?> border px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 inline-block"
            >
              <?= htmlspecialchars($cat['name']); ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Blog Grid -->
      <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <?php while ($blog = mysqli_fetch_assoc($result)) : ?>
            <?php
              $title = htmlspecialchars($blog['title']);
              $slug = htmlspecialchars($blog['slug']);
              $img = !empty($blog['image']) ? '../admin/uploads/' . htmlspecialchars($blog['image']) : 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80';
              $cats = [];
              if (!empty($blog['categories'])) {
                  $cats = array_filter(explode('|', $blog['categories']));
              }
              // Get excerpt from description - first 120 characters
              $description = htmlspecialchars(strip_tags($blog['description']));
              $excerpt = strlen($description) > 120 ? substr($description, 0, 120) . '...' : $description;
            ?>
            
            <!-- Blog Card -->
            <a href="../blogs/<?= $slug; ?>" class="block blog-card group h-full">
              <div class="relative h-80 md:h-96 overflow-hidden rounded-lg">
                <!-- Image -->
                <img 
                  src="<?= $img; ?>" 
                  alt="<?= $title; ?>" 
                  class="blog-image absolute inset-0 w-full h-full object-cover"
                />
                
                <!-- Overlay Gradient -->
                <div class="blog-overlay absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
                
                <!-- Content -->
                <div class="absolute inset-0 flex flex-col justify-end p-6 md:p-8">
                  <!-- Categories -->
                  <?php if (!empty($cats)): ?>
                    <div class="flex flex-wrap gap-2 mb-4">
                      <?php foreach (array_slice($cats, 0, 2) as $c): ?>
                        <span class="category-pill">
                          <?= htmlspecialchars($c); ?>
                        </span>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                  
                  <!-- Title -->
                  <h3 class="font-serif text-2xl md:text-3xl text-white leading-tight">
                    <?= $title; ?>
                  </h3>
                </div>
              </div>
              
              <!-- Card Bottom - Description -->
              <div class="bg-gradient-to-b from-gray-900 to-black border border-t-0 border-gray-800 rounded-b-lg p-6 h-32 flex flex-col">
                <p class="text-gray-400 text-sm leading-relaxed flex-grow">
                  <?= $excerpt; ?>
                </p>
                <div class="mt-4 flex items-center gap-2 text-amber-400 group-hover:gap-3 transition-all">
                  <span class="text-sm font-medium">Read More</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </div>
              </div>
            </a>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-20">
          <p class="text-2xl font-serif text-gray-400 mb-4">No articles found</p>
          <p class="text-gray-500">
            <?php if ($selectedCategoryId > 0): ?>
              <a href="blog.php" class="underline hover:text-white transition-colors">View all articles</a>
            <?php else: ?>
              Check back soon for new content
            <?php endif; ?>
          </p>
        </div>
      <?php endif; ?>

    </div>
  </section>
  <div id="footer-container"></div>
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

        const initNavbar = (root) => {
            if (!root) return;
            const header = root.querySelector('#site-header');
            const menuToggle = root.querySelector('#menu-toggle');
            const menuClose = root.querySelector('#menu-close');
            const mobileMenu = root.querySelector('#mobile-menu');

            const openMenu = () => {
                if (!mobileMenu) return;
                mobileMenu.classList.remove('pointer-events-none', 'translate-x-full', 'opacity-0');
                mobileMenu.classList.add('opacity-100', 'translate-x-0');
                document.body.classList.add('overflow-hidden');
            };

            const closeMenu = () => {
                if (!mobileMenu) return;
                mobileMenu.classList.add('pointer-events-none', 'translate-x-full', 'opacity-0');
                mobileMenu.classList.remove('opacity-100', 'translate-x-0');
                document.body.classList.remove('overflow-hidden');
            };

            menuToggle?.addEventListener('click', openMenu);
            menuClose?.addEventListener('click', closeMenu);
            mobileMenu?.addEventListener('click', (event) => {
                if (event.target === mobileMenu) closeMenu();
            });

            const handleScrollEffects = () => {
                const scrolled = window.scrollY > 50;
                header?.classList.toggle('shadow-lg', scrolled);
                header?.classList.toggle('bg-opacity-95', scrolled);
            };

            document.addEventListener('scroll', handleScrollEffects);
        };

        document.addEventListener('DOMContentLoaded', async () => {
            const navbarRoot = await injectComponent('../components/Navbar.html', '#navbar-container', 'header');
            initNavbar(navbarRoot);
            await injectComponent('../components/fotter.html', '#footer-container', 'footer');
        });
  </script>

</body>
</html>