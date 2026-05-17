<?php
/**
 * Database Test Script
 * Check connection and table structure
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../db.php";

echo "<h2>🔍 Database Connection Test</h2>";

// Test connection
if ($conn->connect_error) {
    echo "<p style='color: red;'><strong>❌ Connection Failed:</strong> " . htmlspecialchars($conn->connect_error) . "</p>";
    exit;
} else {
    echo "<p style='color: green;'><strong>✅ Connected to Database:</strong> " . htmlspecialchars($conn->real_escape_string($conn->db)) . "</p>";
}

// Test charset
echo "<p><strong>📝 Charset:</strong> " . htmlspecialchars($conn->character_set_name()) . "</p>";

// Check blog table structure
echo "<h3>📋 Blog Table Structure</h3>";
$result = $conn->query("DESCRIBE blog");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>❌ Error:</strong> " . htmlspecialchars($conn->error) . "</p>";
}

// Check blog_categories table
echo "<h3>📋 Blog Categories Table Structure</h3>";
$result = $conn->query("DESCRIBE blog_categories");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>❌ Error:</strong> " . htmlspecialchars($conn->error) . "</p>";
}

// Count blogs
echo "<h3>📊 Data Count</h3>";
$blogCount = $conn->query("SELECT COUNT(*) as count FROM blog");
if ($blogCount) {
    $row = $blogCount->fetch_assoc();
    echo "<p><strong>Total Blogs:</strong> " . htmlspecialchars($row['count']) . "</p>";
} else {
    echo "<p style='color: red;'><strong>❌ Error:</strong> " . htmlspecialchars($conn->error) . "</p>";
}

echo "<p><a href='manage_blog.php' style='color: blue;'>← Back to Manage Blog</a></p>";
?>
