<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Check if user is logged in
require_login();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? escape_html($page_title) . ' - ' : ''; ?>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            bg: '#0a0e17',
                            card: '#141824',
                            border: '#1f2937',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0a0e17;
        }
    </style>
</head>
<body class="bg-dark-bg text-gray-100">
    <!-- Admin Navigation -->
    <nav class="bg-dark-card border-b border-dark-border sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/admin/index.php" class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                        Admin Panel
                    </a>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="/admin/index.php" class="text-gray-300 hover:text-white transition <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-white font-semibold' : ''; ?>">Dashboard</a>
                    <a href="/admin/blogs.php" class="text-gray-300 hover:text-white transition <?php echo strpos(basename($_SERVER['PHP_SELF']), 'blog') !== false ? 'text-white font-semibold' : ''; ?>">Blogs</a>
                    <a href="/admin/events.php" class="text-gray-300 hover:text-white transition <?php echo strpos(basename($_SERVER['PHP_SELF']), 'event') !== false ? 'text-white font-semibold' : ''; ?>">Events</a>
                    <a href="/index.php" target="_blank" class="text-gray-300 hover:text-white transition">View Site</a>
                    <a href="/admin/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
