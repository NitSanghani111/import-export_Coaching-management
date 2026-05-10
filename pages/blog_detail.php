<?php
require_once __DIR__ . '/../db.php';

$slug = $_GET['slug'] ?? '';

// Fetch blog with categories
$sql = "SELECT b.*, GROUP_CONCAT(c.name SEPARATOR '|') AS categories
        FROM blog b
        LEFT JOIN blog_categories bc ON bc.blog_id = b.id
        LEFT JOIN categories c ON c.id = bc.category_id
        WHERE b.slug = ?
        GROUP BY b.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: blog.php");
    exit;
}

$blog = $result->fetch_assoc();
$categories = !empty($blog['categories']) ? explode('|', $blog['categories']) : [];
$image = !empty($blog['image']) ? '../admin/uploads/' . htmlspecialchars($blog['image']) : 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="icon" href="/img/design/Logo.svg" type="image/svg+xml" />
  <title><?= htmlspecialchars($blog['title']); ?> | Parth Coaching</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet" />
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-Q21ERFEHX8"></script>
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M7SP2QJL');</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-Q21ERFEHX8');
</script>
  <link rel="stylesheet" href="../assets/css/main.css" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            serif: ['Playfair Display', 'Georgia', 'serif'],
            sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif']
          }
        }
      }
    }
  </script>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      background: #000;
    
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      color: #fff;
    }
    
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Playfair Display', Georgia, serif;
    }

    .back-button {
      transition: all 0.24s ease;
      color: #f5e8c8;
    }

    .back-button:hover {
      transform: translateX(-6px);
      color: #ffefc2;
    }

    .feature-image {
      animation: fadeInUp 0.8s ease-out;
      border-radius: 12px;
      box-shadow: 0 18px 40px rgba(10,8,0,0.55);
      transition: transform 0.45s ease, box-shadow 0.35s ease;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .content-section {
      animation: fadeInUp 0.8s ease-out 0.2s backwards;
    }

    /* Article container polish */
    .article-container { max-width: 1400px; margin: 0 auto; background: linear-gradient(180deg, rgba(255,255,255,0.01), rgba(0,0,0,0.65)); border-radius: 14px; box-shadow: 0 22px 60px rgba(0,0,0,0.7); padding: 40px 48px; }
    @media (max-width: 768px) { .article-container { padding: 20px 16px; margin: 0 12px; border-radius: 10px; } }

    /* Prose Styling */
    .prose-content h2 {
      font-size: 2rem;
      font-weight: 700;
      margin-top: 2rem;
      margin-bottom: 1rem;
      color: #fff;
    }

    .prose-content p {
      line-height: 1.8;
      margin-bottom: 1.5rem;
      color: #e0e0e0;
    }

    .prose-content a {
      color: #f3d88b; /* warm golden links */
      text-decoration: underline;
      transition: color 0.24s ease, text-decoration-color 0.24s ease;
    }

    .prose-content a:hover {
      color: #ffefc2;
      text-decoration-color: rgba(255,239,194,0.6);
    }

    .category-badge {
      display: inline-block;
      background: linear-gradient(135deg, rgba(184,134,11,0.08), rgba(184,134,11,0.06));
      border: 1px solid rgba(184,134,11,0.14);
      padding: 0.45rem 0.9rem;
      border-radius: 999px;
      margin-right: 0.75rem;
      margin-bottom: 0.5rem;
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #f6efd6;
      transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.24s ease;
    }

    .category-badge:hover {
      background: linear-gradient(135deg, rgba(184,134,11,0.14), rgba(184,134,11,0.08));
      border-color: rgba(184,134,11,0.28);
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(184,134,11,0.06);
    }

    /* Related Posts */
    .related-card {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .related-card:hover {
      transform: translateY(-6px);
      border-color: rgba(184,134,11,0.28);
      background: rgba(255, 255, 255, 0.03);
      box-shadow: 0 14px 36px rgba(0,0,0,0.55);
    }

    .related-image {
      overflow: hidden;
      height: 200px;
    }

    .related-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.6s ease;
    }

    .related-card:hover .related-image img {
      transform: scale(1.08) translateZ(0);
      filter: saturate(1.06) contrast(1.03);
    }

    /* Share section amber theme */
    .share-box { background: linear-gradient(90deg, rgba(184,134,11,0.06), rgba(184,134,11,0.03)); border: 1px solid rgba(184,134,11,0.08); }
    .share-box a { color: #f3d88b; }
    .share-box a:hover { background: rgba(255,255,255,0.06); color: #fff; }

    /* Hide bottom share section on mobile, show only on desktop */
    @media (max-width: 980px) {
      .share-box {
        display: none;
      }
    }

    /* Layout for two-column article (meta + main) */
    .article-layout { display: grid; grid-template-columns: 300px 1fr; gap: 48px; align-items: start; }
    @media (max-width: 980px) { .article-layout { grid-template-columns: 1fr; gap: 24px; } .meta-column { order: 2; } .article-main { order: 1; } }
    .meta-column .meta-inner { position: sticky; top: 80px; }
    .meta-card { background: rgba(255,255,255,0.02); }
    .article-main .feature-image { border-radius: 10px; max-width: 100%; }
    .prose-content { margin-top: 18px; color: #e8e3da; font-size: 1.02rem; line-height: 1.9; max-width: 900px; }
    .prose-content blockquote { border-left: 4px solid rgba(184,134,11,0.2); padding-left: 16px; color: #f4ecd3; }
  </style>
</head>
<body>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M7SP2QJL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- Page Loader -->
    <div id="page-loader">
      <div class="loader-spinner"></div>
      <div class="loader-text">Loading</div>
    </div>

    <div id="navbar-container"></div>
  <!-- ================= BACK NAVIGATION ================= -->
  <div class="bg-gradient-to-r from-black via-black to-gray-900 border-b border-gray-800">
    <div class="max-w-[1400px] mx-auto px-8 md:px-12 py-6">
      <a href="/blog" class="back-button inline-flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Blogs
      </a>
    </div>
  </div>

  <!-- ================= ARTICLE CONTENT ================= -->
  <article class="bg-black ">
    <div class="article-container">

      <!-- Article Layout: meta column + main content -->
      <div class="content-section mb-12 article-layout">
        <!-- Meta Column -->
        <aside class="meta-column">
          <div class="meta-inner">
            <a href="/blog" class="back-button inline-flex items-center gap-2 text-gray-400 hover:text-white transition-colors mb-4">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              Back
            </a>

            <?php if (!empty($categories)): ?>
              <div class="mb-4">
                <?php foreach ($categories as $cat): ?>
                  <div style="display:inline-block;margin:0 6px 8px 0;"><span class="category-badge"><?= htmlspecialchars(trim($cat)); ?></span></div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <div class="meta-card bg-black/30 border border-gray-800 rounded-xl p-4 text-gray-200">
              <div class="text-sm text-gray-300">Published</div>
              <div class="mt-1 font-medium text-white">
                <time datetime="<?= htmlspecialchars($blog['created_at']); ?>"><?= date('M d, Y', strtotime($blog['created_at'])); ?></time>
              </div>
              <div class="mt-3 text-sm text-gray-300">Read time: <span class="text-white"><?= ceil(str_word_count($blog['description']) / 200); ?> min</span></div>

              <div class="mt-5 border-t border-gray-800 pt-4">
                <div class="text-xs text-gray-300 uppercase mb-2">Share</div>
                <div class="flex flex-col gap-2">
                  <a href="https://twitter.com/intent/tweet?text=<?= urlencode($blog['title']); ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-white/6 hover:bg-white/12 rounded-lg transition-all text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7"/></svg>
                    Twitter
                  </a>
                  <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($_SERVER['REQUEST_URI']); ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-white/6 hover:bg-white/12 rounded-lg transition-all text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a6 6 0 00-6 6v3H7v4h2v8h4v-8h3l1-4h-4V8a2 2 0 012-2h3z"/></svg>
                    Facebook
                  </a>
                  <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($_SERVER['REQUEST_URI']); ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-white/6 hover:bg-white/12 rounded-lg transition-all text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/></svg>
                    LinkedIn
                  </a>
                </div>
              </div>
            </div>
          </div>
        </aside>

        <!-- Main Article -->
        <main class="article-main">
          <!-- Title -->
          <h1 class="font-serif text-4xl md:text-5xl lg:text-4xl leading-tight tracking-tight mb-6 text-white">
            <?= htmlspecialchars($blog['title']); ?>
          </h1>

          <!-- Featured Image -->
          <div class="mb-8 md:rounded-xl overflow-hidden">
            <img 
              src="<?= $image; ?>" 
              alt="<?= htmlspecialchars($blog['title']); ?>" 
              class="feature-image w-full h-[400px] md:h-[500px] lg:h-[600px] object-cover"
            />
          </div>

          <!-- Article Body -->
          <div class="prose-content">
            <?= $blog['description']; ?>
          </div>
        </main>
      </div>

      <!-- Footer Divider -->
      <hr class="my-16 border-gray-800">

      <!-- Share Section -->
      <div class="content-section share-box border rounded-xl p-8">
        <h3 class="font-serif text-2xl mb-4">Share This Article</h3>
        <div class="flex flex-wrap gap-4">
          <a href="https://twitter.com/intent/tweet?text=<?= urlencode($blog['title']); ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white/6 hover:bg-white/12 rounded-lg transition-all">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7"/></svg>
            Twitter
          </a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($_SERVER['REQUEST_URI']); ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white/6 hover:bg-white/12 rounded-lg transition-all">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a6 6 0 00-6 6v3H7v4h2v8h4v-8h3l1-4h-4V8a2 2 0 012-2h3z"/></svg>
            Facebook
          </a>
          <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($_SERVER['REQUEST_URI']); ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white/6 hover:bg-white/12 rounded-lg transition-all">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/></svg>
            LinkedIn
          </a>
        </div>
      </div>

    </div>
  </article>
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

		document.addEventListener('DOMContentLoaded', async () => {
			const navbarRoot = await injectComponent('/components/Navbar.html', '#navbar-container');
			setTimeout(() => {
				if (typeof window.initNavbar === 'function') {
					window.initNavbar();
				}
			}, 150);
			await injectComponent('../components/footer.html', '#footer-container', 'footer');
			initReveal();
		});
	</script>
</body>
</html>
