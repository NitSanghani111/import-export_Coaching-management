<?php
/**
 * Syntax Checker for manage_blog.php
 */

echo "<h1>🔍 PHP Syntax Check</h1>";

$file = "manage_blog.php";
$output = [];
$returnVar = 0;

// Use PHP to check syntax
exec("php -l " . escapeshellarg($file), $output, $returnVar);

echo "<pre>";
foreach ($output as $line) {
    echo htmlspecialchars($line) . "\n";
}
echo "</pre>";

if ($returnVar === 0) {
    echo "<p style='color: green; font-size: 18px;'><strong>✅ No syntax errors found!</strong></p>";
} else {
    echo "<p style='color: red; font-size: 18px;'><strong>❌ Syntax errors detected!</strong></p>";
}

echo "<p><a href='manage_blog.php' style='margin-top: 20px; padding: 10px 20px; background: #FFC261; color: black; text-decoration: none; border-radius: 6px; font-weight: bold;'>Try Manage Blog</a></p>";
?>
