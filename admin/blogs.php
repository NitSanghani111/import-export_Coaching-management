<?php
$page_title = 'Manage Blogs';
include __DIR__ . '/header.php';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get blog image
    $query = "SELECT image FROM blogs WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $blog = mysqli_fetch_assoc($result);
        
        // Delete image file
        if ($blog['image']) {
            delete_file(__DIR__ . '/../uploads/blogs/' . $blog['image']);
        }
        
        // Delete blog
        $delete_query = "DELETE FROM blogs WHERE id = $id";
        mysqli_query($conn, $delete_query);
        
        header('Location: /admin/blogs.php?success=deleted');
        exit();
    }
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Count total blogs
$count_query = "SELECT COUNT(*) as total FROM blogs";
$count_result = mysqli_query($conn, $count_query);
$total_blogs = mysqli_fetch_assoc($count_result)['total'];
$pagination = get_pagination_data($total_blogs, $page, $per_page);

// Fetch blogs
$query = "SELECT * FROM blogs ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$result = mysqli_query($conn, $query);
$blogs = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $blogs[] = $row;
    }
}
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold mb-2">Manage Blogs</h1>
        <p class="text-gray-400">Create, edit, and manage your blog posts</p>
    </div>
    <a href="/admin/blog-add.php" class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white px-6 py-3 rounded-lg font-semibold transition">
        <span class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Blog
        </span>
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <?php if ($_GET['success'] == 'added'): ?>
        <?php echo show_success('Blog post added successfully!'); ?>
    <?php elseif ($_GET['success'] == 'updated'): ?>
        <?php echo show_success('Blog post updated successfully!'); ?>
    <?php elseif ($_GET['success'] == 'deleted'): ?>
        <?php echo show_success('Blog post deleted successfully!'); ?>
    <?php endif; ?>
<?php endif; ?>

<div class="bg-dark-card border border-dark-border rounded-lg overflow-hidden">
    <?php if (count($blogs) > 0): ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-dark-bg">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                <?php foreach ($blogs as $blog): ?>
                <tr class="hover:bg-dark-bg transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <?php if ($blog['image']): ?>
                            <img src="/uploads/blogs/<?php echo escape_html($blog['image']); ?>" alt="" class="w-12 h-12 rounded object-cover mr-3">
                            <?php endif; ?>
                            <div>
                                <div class="font-medium"><?php echo escape_html($blog['title']); ?></div>
                                <div class="text-sm text-gray-400"><?php echo escape_html(substr($blog['slug'], 0, 40)); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400"><?php echo escape_html($blog['author']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded <?php echo $blog['status'] == 'published' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400'; ?>">
                            <?php echo ucfirst($blog['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400"><?php echo format_date($blog['created_at']); ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="/blog-single.php?slug=<?php echo urlencode($blog['slug']); ?>" target="_blank" class="text-blue-400 hover:text-blue-300 p-2" title="View">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="/admin/blog-edit.php?id=<?php echo $blog['id']; ?>" class="text-green-400 hover:text-green-300 p-2" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <a href="/admin/blogs.php?delete=<?php echo $blog['id']; ?>" onclick="return confirm('Are you sure you want to delete this blog post?')" class="text-red-400 hover:text-red-300 p-2" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="px-6 py-4 bg-dark-bg border-t border-dark-border flex justify-center gap-2">
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
    <div class="p-12 text-center text-gray-400">
        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-lg mb-4">No blog posts yet</p>
        <a href="/admin/blog-add.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded transition">
            Add Your First Blog
        </a>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
