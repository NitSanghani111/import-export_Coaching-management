<?php
/**
 * MIGRATION: Add meta_title and meta_description columns to blog table
 * This fixes the 500 error issue
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../db.php";

echo "<h1>✅ Database Migration: Add Meta Columns</h1>";

// Check connection
if ($conn->connect_error) {
    echo "<p style='color: red;'><strong>❌ Connection Failed:</strong> " . htmlspecialchars($conn->connect_error) . "</p>";
    exit;
}

echo "<p style='color: green;'><strong>✅ Connected to database</strong></p>";

// Get existing columns
$describe = $conn->query("DESCRIBE blog");
$existing_columns = [];
while ($row = $describe->fetch_assoc()) {
    $existing_columns[] = $row['Field'];
}

echo "<p><strong>Existing columns:</strong> " . implode(", ", $existing_columns) . "</p>";

$migrations = [
    [
        'column' => 'meta_title',
        'sql' => "ALTER TABLE blog ADD COLUMN meta_title VARCHAR(255) NULL DEFAULT NULL AFTER image"
    ],
    [
        'column' => 'meta_description',
        'sql' => "ALTER TABLE blog ADD COLUMN meta_description TEXT NULL DEFAULT NULL AFTER meta_title"
    ]
];

echo "<h2>🔄 Running Migrations</h2>";

foreach ($migrations as $migration) {
    $col = $migration['column'];
    
    if (in_array($col, $existing_columns)) {
        echo "<p style='color: blue;'>⏭️ Column '$col' already exists - skipping</p>";
    } else {
        echo "<p>Adding column '$col'...</p>";
        
        if ($conn->query($migration['sql'])) {
            echo "<p style='color: green;'><strong>✅ Successfully added '$col' column</strong></p>";
        } else {
            echo "<p style='color: red;'><strong>❌ Error adding '$col':</strong> " . htmlspecialchars($conn->error) . "</p>";
        }
    }
}

// Verify final structure
echo "<h2>✨ Final Blog Table Structure</h2>";
$describe = $conn->query("DESCRIBE blog");
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $describe->fetch_assoc()) {
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

echo "<h2>✅ Migration Complete!</h2>";
echo "<p style='color: green; font-size: 18px;'><strong>The 500 error should now be fixed!</strong></p>";

echo "<p><a href='manage_blog.php' style='display: inline-block; margin-top: 20px; padding: 12px 24px; background: #FFC261; color: black; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;'>Go to Manage Blog</a></p>";

$conn->close();
?>
