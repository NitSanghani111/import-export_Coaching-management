<?php
// Security and Helper Functions

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Escape output for HTML
function escape_html($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Clean input for database
function clean_input($conn, $data) {
    return mysqli_real_escape_string($conn, sanitize_input($data));
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirect to login if not authenticated
function require_login() {
    if (!is_logged_in()) {
        header('Location: /admin/login.php');
        exit();
    }
}

// Generate CSRF token
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Create URL-friendly slug
function create_slug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

// Format date
function format_date($date) {
    return date('F j, Y', strtotime($date));
}

// Format datetime
function format_datetime($datetime) {
    return date('F j, Y g:i A', strtotime($datetime));
}

// Upload image with validation
function upload_image($file, $upload_dir) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File size exceeds 5MB limit'];
    }
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WebP allowed'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . '/' . $filename;
    
    // Create directory if not exists
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'error' => 'Failed to upload file'];
    }
}

// Delete file
function delete_file($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

// Pagination helper
function get_pagination_data($total_records, $page, $per_page = 10) {
    $total_pages = ceil($total_records / $per_page);
    $page = max(1, min($page, $total_pages));
    $offset = ($page - 1) * $per_page;
    
    return [
        'page' => $page,
        'per_page' => $per_page,
        'offset' => $offset,
        'total_pages' => $total_pages,
        'total_records' => $total_records
    ];
}

// Display success message
function show_success($message) {
    return '<div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded mb-4">' . escape_html($message) . '</div>';
}

// Display error message
function show_error($message) {
    return '<div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded mb-4">' . escape_html($message) . '</div>';
}
?>
