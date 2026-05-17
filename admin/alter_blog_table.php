<?php
/**
 * Database migration to add meta fields to blog table
 */

require_once "../db.php";

// Check if columns exist, if not add them
$stmt = $conn->query("SHOW COLUMNS FROM blog LIKE 'meta_title'");
$columnExists = $stmt && $stmt->num_rows > 0;

if (!$columnExists) {
    // Add meta_title column
    $sql1 = "ALTER TABLE blog ADD COLUMN meta_title VARCHAR(255) DEFAULT NULL AFTER description";
    if ($conn->query($sql1) === TRUE) {
        echo "✓ Added meta_title column\n";
    } else {
        echo "Error adding meta_title: " . $conn->error . "\n";
    }

    // Add meta_description column
    $sql2 = "ALTER TABLE blog ADD COLUMN meta_description VARCHAR(500) DEFAULT NULL AFTER meta_title";
    if ($conn->query($sql2) === TRUE) {
        echo "✓ Added meta_description column\n";
    } else {
        echo "Error adding meta_description: " . $conn->error . "\n";
    }
} else {
    echo "✓ Meta columns already exist\n";
}

$conn->close();
echo "\nMigration completed!\n";
?>
