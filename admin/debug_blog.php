<?php
/**
 * Debug Blog Issues - Simulates form submission
 */

// Show ALL errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start session
session_start();
$_SESSION['admin'] = true; // Mock admin session

require "../db.php";

echo "<h1>🔍 Blog Debug Test</h1>";

// Test 1: Check database connection
echo "<h2>1️⃣ Database Connection</h2>";
if ($conn->connect_error) {
    echo "<p style='color: red;'><strong>❌ Failed:</strong> " . htmlspecialchars($conn->connect_error) . "</p>";
    exit;
} else {
    echo "<p style='color: green;'><strong>✅ Connected</strong></p>";
}

// Test 2: Check blog table structure
echo "<h2>2️⃣ Blog Table Columns</h2>";
$result = $conn->query("DESCRIBE blog");
if (!$result) {
    echo "<p style='color: red;'><strong>❌ Error:</strong> " . htmlspecialchars($conn->error) . "</p>";
} else {
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[$row['Field']] = $row['Type'];
    }
    echo "<p><strong>Columns found:</strong></p>";
    echo "<ul>";
    foreach ($columns as $col => $type) {
        echo "<li>$col ($type)</li>";
    }
    echo "</ul>";
    
    // Check for required columns
    $required = ['title', 'slug', 'description', 'image', 'meta_title', 'meta_description'];
    $missing = array_diff($required, array_keys($columns));
    if (!empty($missing)) {
        echo "<p style='color: red;'><strong>❌ Missing columns:</strong> " . implode(', ', $missing) . "</p>";
    } else {
        echo "<p style='color: green;'><strong>✅ All required columns exist</strong></p>";
    }
}

// Test 3: Try INSERT statement
echo "<h2>3️⃣ Test INSERT Query</h2>";
$title = "Test Blog " . date('Y-m-d H:i:s');
$slug = "test-blog-" . time();
$desc = "This is a test description";
$image = "test_image_" . time() . ".jpg";
$meta_title = "Test Meta Title";
$meta_desc = "Test meta description";

echo "<p><strong>Attempting INSERT:</strong></p>";
echo "<pre>";
echo "Title: $title\n";
echo "Slug: $slug\n";
echo "Description: $desc\n";
echo "Image: $image\n";
echo "Meta Title: $meta_title\n";
echo "Meta Description: $meta_desc\n";
echo "</pre>";

$stmt = $conn->prepare("INSERT INTO blog (title, slug, description, image, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo "<p style='color: red;'><strong>❌ Prepare failed:</strong> " . htmlspecialchars($conn->error) . "</p>";
} else {
    echo "<p style='color: green;'><strong>✅ Prepare succeeded</strong></p>";
    
    $stmt->bind_param("ssssss", $title, $slug, $desc, $image, $meta_title, $meta_desc);
    
    if (!$stmt->execute()) {
        echo "<p style='color: red;'><strong>❌ Execute failed:</strong> " . htmlspecialchars($stmt->error) . "</p>";
    } else {
        echo "<p style='color: green;'><strong>✅ Execute succeeded</strong></p>";
        echo "<p><strong>Last Insert ID:</strong> " . $stmt->insert_id . "</p>";
    }
    
    $stmt->close();
}

// Test 4: Check existing blogs
echo "<h2>4️⃣ Fetch Existing Blogs</h2>";
$blogs_result = $conn->query("SELECT * FROM blog LIMIT 5");
if (!$blogs_result) {
    echo "<p style='color: red;'><strong>❌ Query failed:</strong> " . htmlspecialchars($conn->error) . "</p>";
} else {
    $blogs = $blogs_result->fetch_all(MYSQLI_ASSOC);
    echo "<p style='color: green;'><strong>✅ Query succeeded - Found " . count($blogs) . " blogs</strong></p>";
    if (!empty($blogs)) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Slug</th><th>Image</th><th>Meta Title</th></tr>";
        foreach ($blogs as $blog) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($blog['id']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($blog['title'], 0, 30)) . "</td>";
            echo "<td>" . htmlspecialchars($blog['slug']) . "</td>";
            echo "<td>" . htmlspecialchars($blog['image'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($blog['meta_title'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Test 5: Check categories table
echo "<h2>5️⃣ Categories Table</h2>";
$cat_result = $conn->query("SELECT * FROM categories LIMIT 3");
if (!$cat_result) {
    echo "<p style='color: orange;'><strong>⚠️ Query failed:</strong> " . htmlspecialchars($conn->error) . "</p>";
    echo "<p>Categories table may not exist (this is optional)</p>";
} else {
    $categories = $cat_result->fetch_all(MYSQLI_ASSOC);
    echo "<p style='color: green;'><strong>✅ Found " . count($categories) . " categories</strong></p>";
}

echo "<hr>";
echo "<p><a href='manage_blog.php' style='font-size: 16px; color: blue;'>← Back to Manage Blog</a></p>";
?>
