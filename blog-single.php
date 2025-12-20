<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

// Get blog by slug
$slug = isset($_GET['slug']) ? clean_input($conn, $_GET['slug']) : '';

if (empty($slug)) {
    header('Location: /blog.php');
    exit();
}

$query = "SELECT * FROM blogs WHERE slug = '$slug' AND status = 'published' LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: /blog.php');
    exit();
}

$blog = mysqli_fetch_assoc($result);
$page_title = $blog['title'];

include __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Back Button -->
    <a href="/blog.php" class="inline-flex items-center text-blue-400 hover:text-blue-300 mb-8 transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Blog
    </a>

    <!-- Blog Article -->
    <article class="bg-dark-card border border-dark-border rounded-lg overflow-hidden">
        <?php if ($blog['image']): ?>
        <div class="h-96 bg-gradient-to-br from-blue-500/20 to-purple-500/20 overflow-hidden">
            <img src="/uploads/blogs/<?php echo escape_html($blog['image']); ?>" alt="<?php echo escape_html($blog['title']); ?>" class="w-full h-full object-cover">
        </div>
        <?php endif; ?>
        
        <div class="p-8">
            <!-- Meta Info -->
            <div class="flex items-center gap-4 text-sm text-gray-400 mb-6">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <?php echo format_date($blog['created_at']); ?>
                </span>
                <span>•</span>
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <?php echo escape_html($blog['author']); ?>
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-3xl md:text-4xl font-bold mb-6 bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                <?php echo escape_html($blog['title']); ?>
            </h1>

            <!-- Content -->
            <div class="prose prose-invert prose-lg max-w-none">
                <div class="text-gray-300 leading-relaxed whitespace-pre-line">
                    <?php echo nl2br(escape_html($blog['content'])); ?>
                </div>
            </div>
        </div>
    </article>

    <!-- Related Posts -->
    <?php
    // Fetch 3 other blog posts
    $related_query = "SELECT * FROM blogs WHERE slug != '$slug' AND status = 'published' ORDER BY created_at DESC LIMIT 3";
    $related_result = mysqli_query($conn, $related_query);
    $related_blogs = [];
    if ($related_result) {
        while ($row = mysqli_fetch_assoc($related_result)) {
            $related_blogs[] = $row;
        }
    }
    ?>

    <?php if (count($related_blogs) > 0): ?>
    <div class="mt-16">
        <h2 class="text-2xl font-bold mb-8">Related Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($related_blogs as $related): ?>
            <article class="bg-dark-card border border-dark-border rounded-lg overflow-hidden hover:border-blue-500/50 transition group">
                <?php if ($related['image']): ?>
                <div class="h-40 bg-gradient-to-br from-blue-500/20 to-purple-500/20 overflow-hidden">
                    <img src="/uploads/blogs/<?php echo escape_html($related['image']); ?>" alt="<?php echo escape_html($related['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                <?php else: ?>
                <div class="h-40 bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <?php endif; ?>
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-2 group-hover:text-blue-400 transition">
                        <a href="/blog-single.php?slug=<?php echo urlencode($related['slug']); ?>">
                            <?php echo escape_html($related['title']); ?>
                        </a>
                    </h3>
                    <p class="text-sm text-gray-400">
                        <?php echo escape_html(substr(strip_tags($related['content']), 0, 100)) . '...'; ?>
                    </p>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
