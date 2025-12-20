<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? escape_html($page_title) . ' - ' : ''; ?>Import/Export Coaching</title>
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
<body class="bg-dark-bg text-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-dark-card border-b border-dark-border sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/index.php" class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                        IE Coaching
                    </a>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="/index.php" class="text-gray-300 hover:text-white transition <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-white font-semibold' : ''; ?>">Home</a>
                    <a href="/blog.php" class="text-gray-300 hover:text-white transition <?php echo basename($_SERVER['PHP_SELF']) == 'blog.php' || basename($_SERVER['PHP_SELF']) == 'blog-single.php' ? 'text-white font-semibold' : ''; ?>">Blog</a>
                    <a href="/events.php" class="text-gray-300 hover:text-white transition <?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'text-white font-semibold' : ''; ?>">Events</a>
                    <a href="/about.php" class="text-gray-300 hover:text-white transition <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'text-white font-semibold' : ''; ?>">About</a>
                    <a href="/contact.php" class="text-gray-300 hover:text-white transition <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'text-white font-semibold' : ''; ?>">Contact</a>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-300 hover:text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-dark-border">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="/index.php" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-dark-bg rounded transition">Home</a>
                <a href="/blog.php" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-dark-bg rounded transition">Blog</a>
                <a href="/events.php" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-dark-bg rounded transition">Events</a>
                <a href="/about.php" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-dark-bg rounded transition">About</a>
                <a href="/contact.php" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-dark-bg rounded transition">Contact</a>
            </div>
        </div>
    </nav>

    <main class="min-h-screen">
