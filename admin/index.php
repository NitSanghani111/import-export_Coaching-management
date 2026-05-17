<?php
session_start();

// Helper function to get safe redirect URL (ensures HTTPS in production)
function getRedirectUrl($path = 'dashboard.php') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = '/admin/';
    return $protocol . '://' . $host . $basePath . $path;
}

if (isset($_SESSION["admin"])) {
    header("Location: " . getRedirectUrl('dashboard.php'));
} else {
    header("Location: " . getRedirectUrl('login.php'));
}
exit;
