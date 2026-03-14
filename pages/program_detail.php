<?php
// Initialize routing and component includes
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// Load the structured program data
require_once __DIR__ . '/../data/programs.php';

// Check if slug exists returning 404 otherwise
if (!array_key_exists($slug, $programs)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Program not found</h1>";
    exit;
}

$program = $programs[$slug];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($program['title']) ?> | Parth Jethva</title>
    <link rel="icon" href="/img/design/Logo.svg" type="image/svg+xml" />

    <!-- Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/main.css" />
    <script src="https://cdn.tailwindcss.com?v=3"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: '#0d0d0d', accent: '#FFBE49' },
                    fontFamily: {
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background-color: #000; color: #fff; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Playfair Display', Georgia, serif; }
        
        /* Animations */
        .fade-in { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .fade-in.visible { opacity: 1; transform: translateY(0); }
        .slide-in-left { opacity: 0; transform: translateX(-50px); transition: all 0.8s ease-out; }
        .slide-in-left.visible { opacity: 1; transform: translateX(0); }
        .slide-in-right { opacity: 0; transform: translateX(50px); transition: all 0.8s ease-out; }
        .slide-in-right.visible { opacity: 1; transform: translateX(0); }
        
        /* Interactive List Item */
        .hover-target:hover .hover-bullet { background-color: #FFBE49; transform: scale(1.2); }
    </style>
</head>

<body class="antialiased overflow-x-hidden">
    <!-- Navbar Container -->
    <div id="navbar-container"></div>

    <main class="pt-16 md:pt-20">
        <?php include __DIR__ . '/../components/program/hero.php'; ?>
        <?php include __DIR__ . '/../components/program/overview.php'; ?>
        <?php include __DIR__ . '/../components/program/checklist.php'; ?>
        <?php include __DIR__ . '/../components/program/curriculum.php'; ?>
        <?php include __DIR__ . '/../components/program/deliverables.php'; ?>
        <?php include __DIR__ . '/../components/program/faq.php'; ?>
        <?php include __DIR__ . '/../components/program/cta.php'; ?>
    </main>

    <!-- Footer Container -->
    <div id="footer-container"></div>

    <script src="/assets/js/loader.js"></script>
    <script src="/assets/js/navbar.js"></script>
    <script>
        // Setup Intersection Observer for scroll animations
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right').forEach(el => observer.observe(el));
        });

        // Load Header/Footer
        const injectComponent = async (src, selector, pick) => {
            const target = document.querySelector(selector);
            if (!target) return;
            try {
                const res = await fetch(src);
                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const node = pick ? doc.querySelector(pick) : doc.body;
                target.innerHTML = '';
                if (node) target.appendChild(node.cloneNode(true));
            } catch (err) { console.error(err); }
        };

        document.addEventListener('DOMContentLoaded', async () => {
            await injectComponent('/components/Navbar.html', '#navbar-container');
            if (typeof window.initNavbar === 'function') setTimeout(window.initNavbar, 150);
            await injectComponent('/components/footer.html', '#footer-container', 'footer');
        });
        
        // FAQ Accordion logic
        function toggleFAQ(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('.icon');
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
                icon.textContent = '+';
                button.classList.remove('text-accent');
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
                icon.textContent = '−';
                button.classList.add('text-accent');
            }
        }
    </script>
</body>
</html>
