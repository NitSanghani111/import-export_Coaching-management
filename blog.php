<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Blog';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

// Count total blogs
$count_query = "SELECT COUNT(*) as total FROM blogs WHERE status = 'published'";
$count_result = mysqli_query($conn, $count_query);
$total_blogs = 0;
if ($count_result) {
    $count_row = mysqli_fetch_assoc($count_result);
    $total_blogs = $count_row['total'];
}

$pagination = get_pagination_data($total_blogs, $page, $per_page);

// Fetch blogs with pagination
$query = "SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$result = mysqli_query($conn, $query);
$blogs = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $blogs[] = $row;
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
            Our Blog
        </h1>
        <p class="text-xl text-gray-400 max-w-2xl mx-auto">
            Stay updated with the latest insights, tips, and news about import/export business.
        </p>
    </div>

    <!-- Blog Grid -->
    <?php if (count($blogs) > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
        <?php foreach ($blogs as $blog): ?>
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
                <div class="flex items-center gap-4 text-sm text-gray-400 mb-3">
                    <span><?php echo format_date($blog['created_at']); ?></span>
                    <span>•</span>
                    <span><?php echo escape_html($blog['author']); ?></span>
                </div>
                <h3 class="text-xl font-semibold mb-2 group-hover:text-blue-400 transition">
                    <a href="/blog-single.php?slug=<?php echo urlencode($blog['slug']); ?>">
                        <?php echo escape_html($blog['title']); ?>
                    </a>
                </h3>
                <p class="text-gray-400 mb-4">
                    <?php echo escape_html(substr(strip_tags($blog['content']), 0, 150)) . '...'; ?>
                </p>
                <a href="/blog-single.php?slug=<?php echo urlencode($blog['slug']); ?>" class="text-blue-400 hover:text-blue-300 font-semibold">
                    Read More →
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="flex justify-center gap-2">
        <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="bg-dark-card border border-dark-border hover:border-blue-500 px-4 py-2 rounded transition">
            Previous
        </a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <?php if ($i == $page): ?>
            <span class="bg-blue-500 text-white px-4 py-2 rounded"><?php echo $i; ?></span>
            <?php else: ?>
            <a href="?page=<?php echo $i; ?>" class="bg-dark-card border border-dark-border hover:border-blue-500 px-4 py-2 rounded transition">
                <?php echo $i; ?>
            </a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($page < $pagination['total_pages']): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="bg-dark-card border border-dark-border hover:border-blue-500 px-4 py-2 rounded transition">
            Next
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="text-center text-gray-400 py-12 bg-dark-card border border-dark-border rounded-lg">
        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-lg">No blog posts available yet. Check back soon!</p>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
