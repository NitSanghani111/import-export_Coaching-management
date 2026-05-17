<?php
/**
 * Add Meta Title and Description Columns to Blog Table
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../db.php";

echo "<h2>🔧 Adding Meta Columns to Blog Table</h2>";

// Check if columns already exist
$result = $conn->query("DESCRIBE blog");
$columns = [];
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
}

if (in_array('meta_title', $columns) && in_array('meta_description', $columns)) {
    echo "<p style='color: green;'>✅ Columns already exist!</p>";
} else {
    echo "<p>Adding missing columns...</p>";
    
    // Add meta_title column
    if (!in_array('meta_title', $columns)) {
        $sql1 = "ALTER TABLE blog ADD COLUMN meta_title VARCHAR(255) NULL AFTER image";
        if ($conn->query($sql1)) {
            echo "<p style='color: green;'>✅ Added meta_title column</p>";
        } else {
            echo "<p style='color: red;'>❌ Error adding meta_title: " . htmlspecialchars($conn->error) . "</p>";
        }
    }
    
    // Add meta_description column
    if (!in_array('meta_description', $columns)) {
        $sql2 = "ALTER TABLE blog ADD COLUMN meta_description TEXT NULL AFTER meta_title";
        if ($conn->query($sql2)) {
            echo "<p style='color: green;'>✅ Added meta_description column</p>";
        } else {
            echo "<p style='color: red;'>❌ Error adding meta_description: " . htmlspecialchars($conn->error) . "</p>";
        }
    }
}

// Verify columns now exist
echo "<h3>📋 Updated Blog Table Structure</h3>";
$result = $conn->query("DESCRIBE blog");
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

echo "<p><a href='manage_blog.php' style='color: blue; font-size: 16px; font-weight: bold;'>✅ Done! Go to Manage Blog</a></p>";
?>
