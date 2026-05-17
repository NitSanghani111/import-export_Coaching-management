<?php
session_start();
session_destroy();

// Helper function to get safe redirect URL (ensures HTTPS in production)
function getRedirectUrl($path = 'login.php') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = '/admin/';
    return $protocol . '://' . $host . $basePath . $path;
}

header("Location: " . getRedirectUrl('login.php'));
exit;

