<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Home';

// Fetch latest 3 blog posts
$query = "SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT 3";
$result = mysqli_query($conn, $query);
$latest_blogs = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $latest_blogs[] = $row;
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-500/10"></div>
    <div class="max-w-7xl mx-auto relative">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                Master Import/Export Business
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto">
                Transform your trading career with expert coaching, practical insights, and proven strategies for international business success.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/contact.php" class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white px-8 py-3 rounded-lg font-semibold transition">
                    Get Started
                </a>
                <a href="/about.php" class="bg-dark-card hover:bg-gray-800 text-white border border-dark-border px-8 py-3 rounded-lg font-semibold transition">
                    Learn More
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 px-4 sm:px-6 lg:px-8 bg-dark-card/50">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12">Why Choose Us</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-dark-card border border-dark-border p-6 rounded-lg">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Expert Coaching</h3>
                <p class="text-gray-400">Learn from experienced professionals with decades of international trade experience.</p>
            </div>
            <div class="bg-dark-card border border-dark-border p-6 rounded-lg">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Global Network</h3>
                <p class="text-gray-400">Connect with international buyers, sellers, and industry experts worldwide.</p>
            </div>
            <div class="bg-dark-card border border-dark-border p-6 rounded-lg">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Proven Methods</h3>
                <p class="text-gray-400">Apply tested strategies and best practices for successful import/export operations.</p>
            </div>
        </div>
    </div>
</section>

<!-- Latest Blog Posts -->
<section class="py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold">Latest Insights</h2>
            <a href="/blog.php" class="text-blue-400 hover:text-blue-300 font-semibold">View All →</a>
        </div>
        
        <?php if (count($latest_blogs) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($latest_blogs as $blog): ?>
            <article class="bg-dark-card border border-dark-border rounded-lg overflow-hidden hover:border-blue-500/50 transition group">
                <?php if ($blog['image']): ?>
                <div class="h-48 bg-gradient-to-br from-blue-500/20 to-purple-500/20 overflow-hidden">
                    <img src="/uploads/blogs/<?php echo escape_html($blog['image']); ?>" alt="<?php echo escape_html($blog['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                <?php else: ?>
                <div class="h-48 bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <?php endif; ?>
                <div class="p-6">
                    <div class="text-sm text-gray-400 mb-2"><?php echo format_date($blog['created_at']); ?></div>
                    <h3 class="text-xl font-semibold mb-2 group-hover:text-blue-400 transition">
                        <a href="/blog-single.php?slug=<?php echo urlencode($blog['slug']); ?>">
                            <?php echo escape_html($blog['title']); ?>
                        </a>
                    </h3>
                    <p class="text-gray-400 mb-4 line-clamp-3">
                        <?php echo escape_html(substr(strip_tags($blog['content']), 0, 150)) . '...'; ?>
                    </p>
                    <a href="/blog-single.php?slug=<?php echo urlencode($blog['slug']); ?>" class="text-blue-400 hover:text-blue-300 font-semibold">
                        Read More →
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center text-gray-400 py-12">
            <p>No blog posts available yet.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-500/10 to-purple-500/10">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Start Your Journey?</h2>
        <p class="text-xl text-gray-300 mb-8">
            Join hundreds of successful traders who have transformed their business with our coaching programs.
        </p>
        <a href="/contact.php" class="inline-block bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white px-8 py-3 rounded-lg font-semibold transition">
            Contact Us Today
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
