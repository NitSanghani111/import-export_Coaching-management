<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Destroy session and logout
session_start();
session_destroy();

// Redirect to login
header('Location: /admin/login.php');
exit();
?>
