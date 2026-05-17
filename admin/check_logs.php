<?php
/**
 * Check PHP error log location
 */

echo "<h1>🔍 Hostinger Error Log Info</h1>";

echo "<h2>📍 Error Log Location</h2>";
echo "<p><strong>error_log setting:</strong> " . ini_get('error_log') . "</p>";

echo "<h2>📋 Current Settings</h2>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>display_errors</td><td>" . ini_get('display_errors') . "</td></tr>";
echo "<tr><td>log_errors</td><td>" . ini_get('log_errors') . "</td></tr>";
echo "<tr><td>error_reporting</td><td>" . error_reporting() . "</td></tr>";
echo "<tr><td>memory_limit</td><td>" . ini_get('memory_limit') . "</td></tr>";
echo "<tr><td>max_execution_time</td><td>" . ini_get('max_execution_time') . "</td></tr>";
echo "</table>";

echo "<h2>💡 Instructions</h2>";
echo "<ol>";
echo "<li>Go to your Hostinger cPanel</li>";
echo "<li>Find <strong>Logs</strong> or <strong>Error Log</strong> section</li>";
echo "<li>Open the error log file</li>";
echo "<li>Look for errors from today's date with file path: <code>public_html/admin/manage_blog.php</code></li>";
echo "<li>Copy the error message and share it with me</li>";
echo "</ol>";

echo "<h2>📸 What to look for:</h2>";
echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
echo "[timestamp] PHP Parse Error in manage_blog.php line X\n";
echo "[timestamp] PHP Fatal error in manage_blog.php\n";
echo "[timestamp] PHP Warning in manage_blog.php\n";
echo "</pre>";

echo "<p><a href='manage_blog.php' style='font-size: 16px; color: blue;'>← Back to Manage Blog</a></p>";
?>
