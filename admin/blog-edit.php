<?php
$page_title = 'Edit Blog';
include __DIR__ . '/header.php';

$success = '';
$error = '';

// Get blog ID
$blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($blog_id == 0) {
    header('Location: /admin/blogs.php');
    exit();
}

// Fetch blog
$query = "SELECT * FROM blogs WHERE id = $blog_id LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: /admin/blogs.php');
    exit();
}

$blog = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = clean_input($conn, $_POST['title'] ?? '');
    $content = clean_input($conn, $_POST['content'] ?? '');
    $author = clean_input($conn, $_POST['author'] ?? 'Admin');
    $status = clean_input($conn, $_POST['status'] ?? 'draft');
    $slug = create_slug($title);
    
    // Validate
    if (empty($title) || empty($content)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Check if slug exists (excluding current blog)
        $slug_check = "SELECT id FROM blogs WHERE slug = '$slug' AND id != $blog_id LIMIT 1";
        $slug_result = mysqli_query($conn, $slug_check);
        if (mysqli_num_rows($slug_result) > 0) {
            $slug = $slug . '-' . time();
        }
        
        $image_filename = $blog['image'];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_image($_FILES['image'], __DIR__ . '/../uploads/blogs');
            if ($upload_result['success']) {
                // Delete old image
                if ($blog['image']) {
                    delete_file(__DIR__ . '/../uploads/blogs/' . $blog['image']);
                }
                $image_filename = $upload_result['filename'];
            } else {
                $error = $upload_result['error'];
            }
        }
        
        // Handle image removal
        if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1' && $blog['image']) {
            delete_file(__DIR__ . '/../uploads/blogs/' . $blog['image']);
            $image_filename = '';
        }
        
        if (empty($error)) {
            // Update blog
            $query = "UPDATE blogs SET title = '$title', slug = '$slug', content = '$content', image = '$image_filename', author = '$author', status = '$status' WHERE id = $blog_id";
            
            if (mysqli_query($conn, $query)) {
                header('Location: /admin/blogs.php?success=updated');
                exit();
            } else {
                $error = 'Failed to update blog post.';
            }
        }
    }
}
?>

<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="/admin/blogs.php" class="text-blue-400 hover:text-blue-300 mr-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-3xl font-bold">Edit Blog Post</h1>
    </div>
    <p class="text-gray-400 ml-9">Update your blog post</p>
</div>

<?php if ($error): ?>
    <?php echo show_error($error); ?>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data" class="bg-dark-card border border-dark-border rounded-lg p-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    required
                    class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                    placeholder="Enter blog title"
                    value="<?php echo isset($_POST['title']) ? escape_html($_POST['title']) : escape_html($blog['title']); ?>"
                >
            </div>

            <div>
                <label for="content" class="block text-sm font-medium mb-2">
                    Content <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="content" 
                    name="content" 
                    rows="15" 
                    required
                    class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition resize-none"
                    placeholder="Write your blog content here..."
                ><?php echo isset($_POST['content']) ? escape_html($_POST['content']) : escape_html($blog['content']); ?></textarea>
                <p class="text-sm text-gray-400 mt-2">Write your content in plain text. Line breaks will be preserved.</p>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div>
                <label for="author" class="block text-sm font-medium mb-2">Author</label>
                <input 
                    type="text" 
                    id="author" 
                    name="author" 
                    class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                    placeholder="Admin"
                    value="<?php echo isset($_POST['author']) ? escape_html($_POST['author']) : escape_html($blog['author']); ?>"
                >
            </div>

            <div>
                <label for="status" class="block text-sm font-medium mb-2">Status</label>
                <select 
                    id="status" 
                    name="status" 
                    class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                >
                    <?php $current_status = isset($_POST['status']) ? $_POST['status'] : $blog['status']; ?>
                    <option value="draft" <?php echo $current_status == 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo $current_status == 'published' ? 'selected' : ''; ?>>Published</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Current Image</label>
                <?php if ($blog['image']): ?>
                <div class="mb-3">
                    <img src="/uploads/blogs/<?php echo escape_html($blog['image']); ?>" alt="" class="w-full rounded border border-dark-border">
                    <label class="flex items-center mt-2 text-sm">
                        <input type="checkbox" name="remove_image" value="1" class="mr-2">
                        <span class="text-red-400">Remove current image</span>
                    </label>
                </div>
                <?php else: ?>
                <p class="text-gray-400 text-sm mb-3">No image uploaded</p>
                <?php endif; ?>
                
                <label for="image" class="block text-sm font-medium mb-2">Upload New Image</label>
                <input 
                    type="file" 
                    id="image" 
                    name="image" 
                    accept="image/*"
                    class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600"
                >
                <p class="text-sm text-gray-400 mt-2">Max size: 5MB. Supported: JPG, PNG, GIF, WebP</p>
            </div>

            <div class="pt-4 border-t border-dark-border space-y-3">
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white px-6 py-3 rounded-lg font-semibold transition"
                >
                    Update Blog Post
                </button>
                <a 
                    href="/admin/blogs.php" 
                    class="block w-full text-center bg-dark-bg border border-dark-border hover:border-gray-600 text-gray-300 px-6 py-3 rounded-lg font-semibold transition"
                >
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

<?php include __DIR__ . '/footer.php'; ?>
