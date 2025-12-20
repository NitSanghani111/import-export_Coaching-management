<?php
/**
 * Installation Test Script
 * Run this script once to verify your setup
 * Delete this file after successful installation
 */

// Test database connection
require_once __DIR__ . '/config/db.php';

echo "<!DOCTYPE html>
<html lang='en' class='dark'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Installation Test</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <style>body { background-color: #0a0e17; }</style>
</head>
<body class='bg-dark text-gray-100 p-8'>
    <div class='max-w-4xl mx-auto'>
        <h1 class='text-3xl font-bold mb-6'>Installation Test</h1>
        <div class='space-y-4'>";

// Test 1: Database Connection
echo "<div class='bg-gray-800 border border-gray-700 rounded p-4'>";
echo "<h2 class='text-xl font-semibold mb-2'>1. Database Connection</h2>";
if ($conn) {
    echo "<p class='text-green-400'>✓ Database connection successful!</p>";
    echo "<p class='text-sm text-gray-400'>Connected to: " . DB_NAME . "</p>";
} else {
    echo "<p class='text-red-400'>✗ Database connection failed!</p>";
    echo "<p class='text-sm text-gray-400'>Error: " . mysqli_connect_error() . "</p>";
}
echo "</div>";

// Test 2: Check if tables exist
echo "<div class='bg-gray-800 border border-gray-700 rounded p-4'>";
echo "<h2 class='text-xl font-semibold mb-2'>2. Database Tables</h2>";
$tables = ['admin', 'blogs', 'events'];
$all_tables_exist = true;
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "<p class='text-green-400'>✓ Table '$table' exists</p>";
    } else {
        echo "<p class='text-red-400'>✗ Table '$table' missing</p>";
        $all_tables_exist = false;
    }
}
echo "</div>";

// Test 3: Check uploads directory
echo "<div class='bg-gray-800 border border-gray-700 rounded p-4'>";
echo "<h2 class='text-xl font-semibold mb-2'>3. Upload Directories</h2>";
$dirs = ['uploads/blogs', 'uploads/events'];
foreach ($dirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir) && is_writable(__DIR__ . '/' . $dir)) {
        echo "<p class='text-green-400'>✓ Directory '$dir' exists and is writable</p>";
    } else {
        echo "<p class='text-yellow-400'>⚠ Directory '$dir' needs write permissions</p>";
        echo "<p class='text-sm text-gray-400'>Run: chmod 755 $dir</p>";
    }
}
echo "</div>";

// Test 4: PHP Version
echo "<div class='bg-gray-800 border border-gray-700 rounded p-4'>";
echo "<h2 class='text-xl font-semibold mb-2'>4. PHP Version</h2>";
$php_version = phpversion();
if (version_compare($php_version, '7.4.0', '>=')) {
    echo "<p class='text-green-400'>✓ PHP version: $php_version (OK)</p>";
} else {
    echo "<p class='text-yellow-400'>⚠ PHP version: $php_version (Recommended: 7.4+)</p>";
}
echo "</div>";

// Test 5: Check admin credentials
echo "<div class='bg-gray-800 border border-gray-700 rounded p-4'>";
echo "<h2 class='text-xl font-semibold mb-2'>5. Admin Account</h2>";
$admin_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM admin");
if ($admin_check) {
    $admin_row = mysqli_fetch_assoc($admin_check);
    if ($admin_row['count'] > 0) {
        echo "<p class='text-green-400'>✓ Admin account exists</p>";
        echo "<p class='text-sm text-gray-400'>Default login: admin / admin123</p>";
        echo "<p class='text-sm text-yellow-400'>⚠ Change password after first login!</p>";
    } else {
        echo "<p class='text-red-400'>✗ No admin account found</p>";
    }
}
echo "</div>";

// Summary
echo "<div class='bg-gradient-to-r from-blue-500/20 to-purple-500/20 border border-blue-500 rounded p-6 mt-6'>";
if ($conn && $all_tables_exist) {
    echo "<h2 class='text-2xl font-bold mb-2'>✓ Installation Successful!</h2>";
    echo "<p class='mb-4'>Your website is ready to use.</p>";
    echo "<div class='space-y-2'>";
    echo "<p><a href='/index.php' class='text-blue-400 hover:underline'>→ Visit Website</a></p>";
    echo "<p><a href='/admin/login.php' class='text-blue-400 hover:underline'>→ Admin Login</a></p>";
    echo "</div>";
    echo "<p class='text-sm text-gray-400 mt-4'>⚠ Delete this file (install-test.php) after testing!</p>";
} else {
    echo "<h2 class='text-2xl font-bold mb-2'>⚠ Installation Issues</h2>";
    echo "<p>Please fix the issues above and refresh this page.</p>";
    echo "<p class='text-sm text-gray-400 mt-4'>Make sure you've imported database.sql into your MySQL database.</p>";
}
echo "</div>";

echo "    </div>
    </div>
</body>
</html>";
?>
