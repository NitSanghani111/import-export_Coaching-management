<?php
$page_title = 'Add New Blog';
include __DIR__ . '/header.php';

$success = '';
$error = '';

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
        // Check if slug exists
        $slug_check = "SELECT id FROM blogs WHERE slug = '$slug' LIMIT 1";
        $slug_result = mysqli_query($conn, $slug_check);
        if (mysqli_num_rows($slug_result) > 0) {
            $slug = $slug . '-' . time();
        }
        
        $image_filename = '';
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_image($_FILES['image'], __DIR__ . '/../uploads/blogs');
            if ($upload_result['success']) {
                $image_filename = $upload_result['filename'];
            } else {
                $error = $upload_result['error'];
            }
        }
        
        if (empty($error)) {
            // Insert blog
            $query = "INSERT INTO blogs (title, slug, content, image, author, status) VALUES ('$title', '$slug', '$content', '$image_filename', '$author', '$status')";
            
            if (mysqli_query($conn, $query)) {
                header('Location: /admin/blogs.php?success=added');
                exit();
            } else {
                $error = 'Failed to add blog post.';
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
        <h1 class="text-3xl font-bold">Add New Blog Post</h1>
    </div>
    <p class="text-gray-400 ml-9">Create a new blog post for your website</p>
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
                    value="<?php echo isset($_POST['title']) ? escape_html($_POST['title']) : ''; ?>"
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
                ><?php echo isset($_POST['content']) ? escape_html($_POST['content']) : ''; ?></textarea>
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
                    value="<?php echo isset($_POST['author']) ? escape_html($_POST['author']) : 'Admin'; ?>"
                >
            </div>

            <div>
                <label for="status" class="block text-sm font-medium mb-2">Status</label>
                <select 
                    id="status" 
                    name="status" 
                    class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                >
                    <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo (isset($_POST['status']) && $_POST['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                </select>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium mb-2">Featured Image</label>
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
                    Publish Blog Post
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
