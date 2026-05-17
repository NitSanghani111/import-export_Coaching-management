<?php
// Output buffering to prevent header issues
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start session FIRST, before any includes
session_start();

// Now require database
require "../db.php";

// Flash message helper
$flash_success = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    return trim($string, '-');
}

// Helper function to get safe redirect URL (ensures HTTPS in production)
function getRedirectUrl($path = 'manage_blog.php') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = '/admin/';
    return $protocol . '://' . $host . $basePath . $path;
}

// Auth check
if (!isset($_SESSION['admin'])) {
    header("Location: " . getRedirectUrl('login.php'));
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("SELECT image FROM blog WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();
    
    if ($blog && !empty($blog['image'])) {
        $imagePath = "uploads/" . basename($blog['image']);
        if (file_exists($imagePath)) {
            @unlink($imagePath);
        }
    }
    
    $deleteStmt = $conn->prepare("DELETE FROM blog WHERE id = ?");
    $deleteStmt->bind_param("i", $id);
    $deleteStmt->execute();
    $_SESSION['flash_success'] = 'Blog post deleted successfully!';
    header("Location: " . getRedirectUrl('manage_blog.php'));
    ob_end_flush();
    exit;
}

// Handle form submission
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;
$edit_blog = null;
$error = null;
$success = null;
$categories = [];
$selectedCategories = [];

if ($edit_id) {
    $stmt = $conn->prepare("SELECT * FROM blog WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_blog = $result->fetch_assoc();
    // Fetch already-linked categories for pre-checking
    $catStmt = $conn->prepare("SELECT category_id FROM blog_categories WHERE blog_id = ?");
    $catStmt->bind_param("i", $edit_id);
    $catStmt->execute();
    $catResult = $catStmt->get_result();
    while ($row = $catResult->fetch_assoc()) {
        $selectedCategories[] = (int)$row['category_id'];
    }
}

// Fetch all categories for checkbox list
$catAll = $conn->query("SELECT * FROM categories ORDER BY name ASC");
if ($catAll) {
    $categories = $catAll->fetch_all(MYSQLI_ASSOC);
} else {
    $categories = [];
    error_log("Categories query failed: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $desc = isset($_POST['description']) ? trim($_POST['description']) : '';
    $meta_title = isset($_POST['meta_title']) ? trim($_POST['meta_title']) : '';
    $meta_description = isset($_POST['meta_description']) ? trim($_POST['meta_description']) : '';
    $postedCategories = isset($_POST['categories']) && is_array($_POST['categories'])
        ? array_map('intval', $_POST['categories'])
        : [];
    // Keep selection for re-render on validation error
    $selectedCategories = $postedCategories;
    
    // Check for POST size limit error
    if ($_SERVER['CONTENT_LENGTH'] > 52428800) { // 50MB limit check
        $error = "⚠️ File too large! Maximum upload size is 50MB. Please compress your image.";
    } elseif (empty($title) || empty($desc)) {
        $error = "Title and description are required.";
    } else {
        $slug = createSlug($title);
        $image_name = null;
        
        if (isset($_FILES["image"]) && $_FILES["image"]["size"] > 0) {
            $image = $_FILES["image"]["name"];
            $image_tmp = $_FILES["image"]["tmp_name"];
            $image_size = $_FILES["image"]["size"];
            $image_error = $_FILES["image"]["error"];
            
            // Check for upload errors
            if ($image_error === UPLOAD_ERR_INI_SIZE || $image_error === UPLOAD_ERR_FORM_SIZE) {
                $error = "⚠️ File exceeds upload limit. Max size: 50MB. Please use a smaller image.";
            } elseif ($image_error !== UPLOAD_ERR_OK && $image_error !== UPLOAD_ERR_NO_FILE) {
                $error = "❌ Upload error: " . ($image_error === UPLOAD_ERR_PARTIAL ? "Partial upload" : "Unknown error");
            } else {
                // Validate file type and size
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                
                if (!in_array($ext, $allowed)) {
                    $error = "❌ Only image files allowed (JPG, PNG, GIF, WebP).";
                } elseif ($image_size > 50 * 1024 * 1024) { // 50MB limit
                    $error = "❌ Image too large. Max 50MB.";
                } else {
                    $image_name = time() . '_' . preg_replace('/[^a-z0-9._-]/i', '', basename($image));
                    $path = "uploads/" . $image_name;
                    
                    if (!@move_uploaded_file($image_tmp, $path)) {
                        $error = "❌ Failed to save image. Check folder permissions.";
                    }
                }
            }
        }
        
        if (!isset($error)) {
            if ($edit_id) {
                // Update existing blog
                if ($image_name) {
                    // Delete old image if new one uploaded
                    if (!empty($edit_blog['image'])) {
                        $oldPath = "uploads/" . basename($edit_blog['image']);
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }
                    $stmt = $conn->prepare("UPDATE blog SET title=?, slug=?, description=?, image=?, meta_title=?, meta_description=? WHERE id=?");
                    if (!$stmt) {
                        error_log("Prepare failed (update with image): " . $conn->error);
                        $error = "Database error: " . $conn->error;
                    } else {
                        $stmt->bind_param("ssssssi", $title, $slug, $desc, $image_name, $meta_title, $meta_description, $edit_id);
                        if (!$stmt->execute()) {
                            error_log("Execute failed (update with image): " . $stmt->error);
                            $error = "Database error: " . $stmt->error;
                        } else {
                            $success_flag = true;
                        }
                    }
                } else {
                    $stmt = $conn->prepare("UPDATE blog SET title=?, slug=?, description=?, meta_title=?, meta_description=? WHERE id=?");
                    if (!$stmt) {
                        error_log("Prepare failed (update no image): " . $conn->error);
                        $error = "Database error: " . $conn->error;
                    } else {
                        $stmt->bind_param("sssssi", $title, $slug, $desc, $meta_title, $meta_description, $edit_id);
                        if (!$stmt->execute()) {
                            error_log("Execute failed (update no image): " . $stmt->error);
                            $error = "Database error: " . $stmt->error;
                        } else {
                            $success_flag = true;
                        }
                    }
                }
                
                if (isset($success_flag) && $success_flag === true) {
                    // Sync categories: clear and insert new selections
                    $conn->query("DELETE FROM blog_categories WHERE blog_id = " . intval($edit_id));
                    if (!empty($postedCategories)) {
                        $catIns = $conn->prepare("INSERT INTO blog_categories (blog_id, category_id) VALUES (?, ?)");
                        if ($catIns) {
                            foreach ($postedCategories as $cid) {
                                $cid = intval($cid);
                                if ($cid > 0) {
                                    $catIns->bind_param("ii", $edit_id, $cid);
                                    if (!$catIns->execute()) {
                                        error_log("Category insert failed: " . $catIns->error);
                                    }
                                }
                            }
                        }
                    }
                    $_SESSION['flash_success'] = 'Blog post updated successfully!';
                    header("Location: " . getRedirectUrl('manage_blog.php'));
                    ob_end_flush();
                    exit;
                }
            } else {
                // Create new blog
                if (!$image_name) {
                    $error = "Please upload an image.";
                } else {
                    $stmt = $conn->prepare("INSERT INTO blog (title, slug, description, image, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?)");
                    if (!$stmt) {
                        error_log("Prepare failed (insert): " . $conn->error);
                        $error = "Database error: " . $conn->error;
                    } else {
                        $stmt->bind_param("ssssss", $title, $slug, $desc, $image_name, $meta_title, $meta_description);
                        if (!$stmt->execute()) {
                            error_log("Execute failed (insert): " . $stmt->error);
                            $error = "Database error: " . $stmt->error;
                        } else {
                            $newId = $stmt->insert_id;
                            if (!empty($postedCategories)) {
                                $catIns = $conn->prepare("INSERT INTO blog_categories (blog_id, category_id) VALUES (?, ?)");
                                if ($catIns) {
                                    foreach ($postedCategories as $cid) {
                                        $cid = intval($cid);
                                        if ($cid > 0) {
                                            $catIns->bind_param("ii", $newId, $cid);
                                            if (!$catIns->execute()) {
                                                error_log("Category insert failed: " . $catIns->error);
                                            }
                                        }
                                    }
                                }
                            }
                            $_SESSION['flash_success'] = 'Blog post created successfully!';
                            header("Location: " . getRedirectUrl('manage_blog.php'));
                            ob_end_flush();
                            exit;
                        }
                    }
                }
            }
            if (isset($error) === false) {
                ob_end_flush();
                exit;
            }
        }
    }
}

// Fetch all blogs
$blogs_result = $conn->query("SELECT * FROM blog ORDER BY id DESC");
if (!$blogs_result) {
    error_log("Blog query failed: " . $conn->error);
    $blogs = [];
} else {
    $blogs = $blogs_result->fetch_all(MYSQLI_ASSOC) ?: [];
}
$total_blogs = count($blogs);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= $edit_id ? 'Edit' : 'Add' ?> Blog | Parth Coaching Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.0/classic/ckeditor.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Sora:wght@400;600;700&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
        }

        .font-sora {
            font-family: 'Sora', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f0f1e 0%, #1a1a2e 50%, #0f0f1e 100%);
            background-attachment: fixed;
        }

        .gradient-accent {
            background: linear-gradient(135deg, #FFC261 0%, #FFB347 100%);
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(255, 194, 97, 0.1);
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }

        .form-input {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        .form-input:focus {
            border-color: #FFC261 !important;
            box-shadow: 0 0 0 3px rgba(255, 194, 97, 0.1) !important;
        }

        .ck-editor__editable {
            background: rgba(255, 255, 255, 0.03) !important;
            color: white !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            min-height: 300px !important;
        }

        .ck.ck-editor__main > .ck-editor__editable {
            background: rgba(255, 255, 255, 0.03);
        }

        .action-btn {
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body class="text-white antialiased">

    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR -->
        <aside class="w-72 bg-[#0a0a14] border-r border-white/10 flex flex-col hidden lg:flex fixed h-screen left-0 top-0 overflow-y-auto">
            
            <!-- Logo Section -->
            <div class="p-8 border-b border-white/10 sticky top-0 bg-[#0a0a14]">
                <div class="flex items-center gap-4">
                    <div class="gradient-accent w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg shadow-lg">
                        PC
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Parth Coaching</p>
                        <p class="text-gray-400 text-xs">Admin Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="dashboard.php"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9" />
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="manage_blog.php"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition sidebar-active"
                   style="background: rgba(255, 194, 97, 0.15); border-left: 3px solid #FFC261;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2v-5.5a2 2 0 012-2H19" />
                    </svg>
                    <span class="font-medium">Blog Posts</span>
                </a>

                <a href="manage_workshop.php"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span class="font-medium">Workshops</span>
                </a>
            </nav>

            <!-- Logout -->
            <div class="border-t border-white/10 p-4 sticky bottom-0 bg-[#0a0a14]">
                <a href="logout.php"
                   class="gradient-accent text-black w-full py-2.5 rounded-lg font-semibold text-sm transition hover:shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 lg:ml-72 flex flex-col h-screen overflow-auto">

            <!-- TOP BAR -->
            <header class="sticky top-0 z-40 bg-[#0a0a14]/80 backdrop-blur-md border-b border-white/10 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold font-sora"><?= $edit_id ? 'Edit Blog Post' : 'Manage Blog' ?></h1>
                        <p class="text-sm text-gray-400 mt-1">Create, update, and manage your blog content</p>
                    </div>
                    <button class="lg:hidden gradient-accent text-black px-4 py-2 rounded-lg font-semibold text-sm"
                            onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                        ☰ Menu
                    </button>
                </div>
            </header>

            <!-- CONTENT AREA -->
            <section class="flex-1 p-6 md:p-8 space-y-8">

                <!-- Success/Error Messages -->
                <?php if (!empty($flash_success)): ?>
                <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 flex items-center gap-3 fade-in-up">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-green-400">
                        <?= htmlspecialchars($flash_success) ?>
                    </span>
                </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4 flex items-center gap-3 fade-in-up">
                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-red-400"><?= htmlspecialchars($error) ?></span>
                </div>
                <?php endif; ?>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-yellow-500/10 to-orange-500/5 border border-yellow-500/20 rounded-2xl p-6 fade-in-up stagger-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-gray-400 text-sm font-medium">Total Blog Posts</p>
                                <p class="text-3xl font-bold mt-2"><?= $total_blogs ?></p>
                            </div>
                            <div class="gradient-accent w-12 h-12 rounded-lg flex items-center justify-center text-xl">
                                📰
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-500/10 to-cyan-500/5 border border-blue-500/20 rounded-2xl p-6 fade-in-up stagger-2">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-gray-400 text-sm font-medium">Status</p>
                                <p class="text-3xl font-bold mt-2"><?= $edit_id ? 'Editing' : 'Creating' ?></p>
                            </div>
                            <div class="gradient-accent w-12 h-12 rounded-lg flex items-center justify-center text-xl">
                                ✏️
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/5 border border-purple-500/20 rounded-2xl p-6 fade-in-up stagger-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-gray-400 text-sm font-medium">Last Action</p>
                                <p class="text-lg font-bold mt-2" id="lastAction">Just now</p>
                            </div>
                            <div class="gradient-accent w-12 h-12 rounded-lg flex items-center justify-center text-xl">
                                🕐
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Grid: Form + Blog List -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Form Section (2 Columns) -->
                    <div class="lg:col-span-2 fade-in-up">
                        <div class="bg-[#111]/40 border border-white/10 rounded-2xl p-8">
                            <h2 class="text-xl font-bold font-sora mb-2"><?= $edit_id ? 'Edit Post' : 'Create New Post' ?></h2>
                            <p class="text-gray-400 text-sm mb-8">Fill in the details below to <?= $edit_id ? 'update your' : 'create a new' ?> blog post.</p>

                            <form method="POST" enctype="multipart/form-data" class="space-y-6" id="blogForm">
                                <!-- Title -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Title <span class="text-red-400">*</span></label>
                                    <input type="text" name="title" placeholder="Enter blog post title" required 
                                           class="w-full rounded-lg p-3 form-input" 
                                           value="<?= $edit_blog ? htmlspecialchars($edit_blog['title']) : '' ?>">
                                </div>

                                <!-- Image Upload with Validation -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">
                                        Featured Image <?php if (!$edit_id): ?><span class="text-red-400">*</span><?php endif; ?>
                                        <span class="text-xs text-gray-500 font-normal">(Max 50MB)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="image" id="imageInput" accept="image/*" 
                                               class="w-full rounded-lg p-3 form-input" 
                                               <?php if (!$edit_id): ?>required<?php endif; ?>>
                                        <p id="imageSizeWarning" class="text-xs text-red-400 mt-1 hidden">⚠️ File is too large. Max size: 50MB</p>
                                        <?php if ($edit_blog && !empty($edit_blog['image'])): ?>
                                        <p class="text-xs text-gray-400 mt-2">Current image: <?= htmlspecialchars($edit_blog['image']) ?></p>
                                        <img src="uploads/<?= htmlspecialchars($edit_blog['image']) ?>" alt="Current" class="w-full h-40 object-cover rounded-lg mt-3 border border-white/10">
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Description Editor -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Description <span class="text-red-400">*</span></label>
                                    <textarea name="description" id="editor" placeholder="Write your blog content here..." ><?= $edit_blog ? htmlspecialchars($edit_blog['description']) : '' ?></textarea>
                                </div>

                                <!-- Meta Title -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">
                                        Meta Title 
                                        <span class="text-xs text-gray-500 font-normal">(For SEO - up to 60 characters)</span>
                                    </label>
                                    <input type="text" name="meta_title" placeholder="Enter SEO meta title..." maxlength="255"
                                           class="w-full rounded-lg p-3 form-input" 
                                           value="<?= $edit_blog && !empty($edit_blog['meta_title']) ? htmlspecialchars($edit_blog['meta_title']) : '' ?>">
                                    <p class="text-xs text-gray-400 mt-1">This will appear in search engine results and browser tabs</p>
                                </div>

                                <!-- Meta Description -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">
                                        Meta Description
                                        <span class="text-xs text-gray-500 font-normal">(For SEO - up to 160 characters)</span>
                                    </label>
                                    <textarea name="meta_description" placeholder="Enter SEO meta description..." maxlength="500"
                                           class="w-full rounded-lg p-3 form-input resize-none" 
                                           rows="3"><?= $edit_blog && !empty($edit_blog['meta_description']) ? htmlspecialchars($edit_blog['meta_description']) : '' ?></textarea>
                                    <p class="text-xs text-gray-400 mt-1">Brief description shown under the title in search results</p>
                                </div>

                                <!-- Categories -->
                                <?php if (!empty($categories)): ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-3">Categories</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <?php foreach ($categories as $cat): ?>
                                        <?php $checked = in_array((int)$cat['id'], $selectedCategories, true) ? 'checked' : ''; ?>
                                        <label class="flex items-center gap-2 text-sm bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg px-3 py-2 transition">
                                            <input type="checkbox" name="categories[]" value="<?= (int)$cat['id'] ?>" class="form-checkbox text-yellow-400" <?= $checked ?>>
                                            <span><?= htmlspecialchars($cat['name']) ?></span>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Action Buttons -->
                                <div class="flex gap-4 pt-6 border-t border-white/10">
                                    <button type="submit" class="gradient-accent text-black px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition action-btn flex-1">
                                        <?= $edit_id ? '💾 Update Post' : '✨ Publish Post' ?>
                                    </button>
                                    <a href="manage_blog.php" class="bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-lg font-semibold transition action-btn">
                                        ✕ Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Blog List (1 Column) -->
                    <div class="lg:col-span-1 fade-in-up stagger-1">
                        <div class="bg-[#111]/40 border border-white/10 rounded-2xl p-6 h-full">
                            <h3 class="text-lg font-bold font-sora mb-4">Recent Posts</h3>
                            
                            <?php if (empty($blogs)): ?>
                            <p class="text-gray-400 text-sm text-center py-8">No blog posts yet.</p>
                            <?php else: ?>
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                <?php foreach (array_slice($blogs, 0, 5) as $blog): ?>
                                <div class="bg-white/5 hover:bg-white/10 border border-white/5 rounded-lg p-3 transition group">
                                    <p class="text-sm font-medium text-white group-hover:text-yellow-300 transition truncate">
                                        <?= htmlspecialchars($blog['title']) ?>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        ID: <?= $blog['id'] ?>
                                    </p>
                                    <div class="flex gap-2 mt-2">
                                        <a href="?edit=<?= $blog['id'] ?>" class="text-xs bg-blue-500/20 hover:bg-blue-500/40 text-blue-300 px-2 py-1 rounded transition flex-1 text-center">Edit</a>
                                        <button onclick="deleteBlog(<?= $blog['id'] ?>)" class="text-xs bg-red-500/20 hover:bg-red-500/40 text-red-300 px-2 py-1 rounded transition flex-1">Delete</button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <!-- All Blog Posts Table (if not editing) -->
                <?php if (!$edit_id && !empty($blogs)): ?>
                <div class="fade-in-up">
                    <h3 class="text-xl font-bold font-sora mb-6">All Blog Posts</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($blogs as $blog): ?>
                        <div class="bg-[#111]/40 border border-white/10 rounded-2xl overflow-hidden card-hover">
                            <!-- Image -->
                            <?php if (!empty($blog['image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="w-full h-40 object-cover">
                            <?php else: ?>
                            <div class="w-full h-40 bg-gradient-to-br from-yellow-500/10 to-orange-500/5 flex items-center justify-center text-3xl">📰</div>
                            <?php endif; ?>

                            <!-- Content -->
                            <div class="p-6">
                                <h4 class="text-lg font-bold font-sora mb-2 line-clamp-2">
                                    <?= htmlspecialchars($blog['title']) ?>
                                </h4>
                                <p class="text-sm text-gray-400 mb-4 line-clamp-2">
                                    <?= strip_tags(substr(htmlspecialchars($blog['description']), 0, 100)) ?>...
                                </p>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="?edit=<?= $blog['id'] ?>" class="gradient-accent text-black flex-1 py-2 rounded-lg font-semibold text-sm text-center hover:shadow-lg transition">
                                        Edit
                                    </a>
                                    <button onclick="deleteBlog(<?= $blog['id'] ?>)" class="bg-red-500/20 hover:bg-red-500/30 text-red-300 flex-1 py-2 rounded-lg font-semibold text-sm transition">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </section>

        </main>

    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-[#0a0a14] border border-white/10 rounded-2xl p-6 max-w-sm mx-4 fade-in-up">
            <h3 class="text-lg font-bold font-sora mb-2">Delete Blog Post?</h3>
            <p class="text-gray-400 text-sm mb-6">This action cannot be undone. The post and its image will be permanently deleted.</p>
            <div class="flex gap-3">
                <button onclick="confirmDelete()" class="gradient-accent text-black flex-1 py-2 rounded-lg font-semibold text-sm hover:shadow-lg transition">
                    Delete
                </button>
                <button onclick="closeModal()" class="bg-white/10 hover:bg-white/20 text-white flex-1 py-2 rounded-lg font-semibold text-sm transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB
        const imageInput = document.getElementById('imageInput');
        const imageSizeWarning = document.getElementById('imageSizeWarning');
        const blogForm = document.getElementById('blogForm');
        let deleteId = null;

        // File size validation
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (file.size > MAX_FILE_SIZE) {
                        imageSizeWarning.classList.remove('hidden');
                        blogForm.querySelector('button[type="submit"]').disabled = true;
                    } else {
                        imageSizeWarning.classList.add('hidden');
                        blogForm.querySelector('button[type="submit"]').disabled = false;
                    }
                }
            });
        }

        function deleteBlog(id) {
            deleteId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function confirmDelete() {
            if (deleteId) {
                window.location.href = `manage_blog.php?delete=${deleteId}`;
            }
        }

        function closeModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            deleteId = null;
        }

        // Initialize CKEditor
        ClassicEditor.create(document.querySelector('#editor'), {
            toolbar: {
                items: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
            }
        }).catch(error => {
            console.error(error);
        });
    </script>

</body>
</html>
