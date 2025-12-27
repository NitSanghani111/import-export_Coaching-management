<?php
session_start();

/* =========================
   AUTH CHECK
========================= */
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

/* =========================
   IDLE AUTO LOGOUT (30 min)
========================= */
$timeout = 1800; // 30 minutes

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Dashboard | Parth Coaching</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Sora:wght@400;600;700&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
        }

        .font-sora {
            font-family: 'Sora', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f0f1e 0%, #1a1a2e 50%, #0f0f1e 100%);
            background-attachment: fixed;
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(255, 194, 97, 0.15);
        }

        .gradient-accent {
            background: linear-gradient(135deg, #FFC261 0%, #FFB347 100%);
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }

        .sidebar-active {
            background: rgba(255, 194, 97, 0.15);
            border-left: 3px solid #FFC261;
        }

        .pulse-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .tooltip {
            position: relative;
        }

        .tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background: #1a1a2e;
            color: #FFC261;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            border: 1px solid rgba(255, 194, 97, 0.3);
            z-index: 10;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(255, 194, 97, 0.1) 0%, rgba(255, 179, 71, 0.05) 100%);
            border: 1px solid rgba(255, 194, 97, 0.2);
        }

        .action-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 194, 97, 0.02) 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="text-white antialiased">

    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR -->
        <aside class="w-72 bg-[#0a0a14] border-r border-white/10 flex flex-col hidden lg:flex fixed h-screen left-0 top-0">
            
            <!-- Logo Section -->
            <div class="p-8 border-b border-white/10">
                <div class="flex items-center gap-4 fade-in-up">
                    <div class="gradient-accent w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg shadow-lg">
                        PC
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Parth Coaching</p>
                        <p class="text-gray-400 text-xs">Management Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="dashboard.php"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition sidebar-active fade-in-up stagger-1"
                   style="background: rgba(255, 194, 97, 0.15); border-left: 3px solid #FFC261;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 16l4-4m0 0l4-4m-4 4L9 4m4 4l4-4" />
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="manage_blog.php"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition fade-in-up stagger-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2v-5.5a2 2 0 012-2H19" />
                    </svg>
                    <span class="font-medium">Blog Posts</span>
                </a>

                <a href="manage_workshop.php"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition fade-in-up stagger-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span class="font-medium">Workshops</span>
                </a>
            </nav>

            <!-- User Info & Logout -->
            <div class="border-t border-white/10 p-4 space-y-4 fade-in-up">
                <div class="flex items-center gap-3 px-4 py-2">
                    <div class="gradient-accent w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold">
                        <?= strtoupper(substr(htmlspecialchars($_SESSION["admin"]), 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate"><?= htmlspecialchars($_SESSION["admin"]) ?></p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                </div>
                <a href="logout.php"
                   class="gradient-accent text-black w-full py-2.5 rounded-lg font-semibold text-sm transition hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 lg:ml-72 flex flex-col h-screen overflow-auto">

            <!-- TOP BAR (Mobile & Desktop) -->
            <header class="sticky top-0 z-40 bg-[#0a0a14]/80 backdrop-blur-md border-b border-white/10 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold font-sora">Dashboard</h1>
                        <p class="text-sm text-gray-400 mt-1">Welcome back, <?= htmlspecialchars($_SESSION["admin"]) ?></p>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <button class="lg:hidden gradient-accent text-black px-4 py-2 rounded-lg font-semibold text-sm"
                            onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                        ☰ Menu
                    </button>

                    <!-- Time Display -->
                    <div class="hidden sm:block text-right">
                        <p id="currentTime" class="text-sm font-medium"></p>
                        <p id="currentDate" class="text-xs text-gray-400"></p>
                    </div>
                </div>
            </header>

            <!-- CONTENT AREA -->
            <section class="flex-1 p-6 md:p-8 space-y-8">

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="stat-card rounded-2xl p-6 fade-in-up stagger-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-gray-400 text-sm font-medium">Active Sessions</p>
                                <p class="text-3xl font-bold mt-2">1</p>
                                <p class="text-xs text-green-400 mt-1">✓ Currently logged in</p>
                            </div>
                            <div class="gradient-accent w-12 h-12 rounded-lg flex items-center justify-center text-xl">
                                👤
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rounded-2xl p-6 fade-in-up stagger-2">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-gray-400 text-sm font-medium">Quick Actions</p>
                                <p class="text-3xl font-bold mt-2">2</p>
                                <p class="text-xs text-blue-400 mt-1">Available sections</p>
                            </div>
                            <div class="gradient-accent w-12 h-12 rounded-lg flex items-center justify-center text-xl">
                                ⚡
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rounded-2xl p-6 fade-in-up stagger-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-gray-400 text-sm font-medium">System Status</p>
                                <p class="text-3xl font-bold mt-2">
                                    <span class="pulse-dot inline-block w-3 h-3 rounded-full gradient-accent mr-2"></span>
                                    Online
                                </p>
                                <p class="text-xs text-gray-400 mt-1">All systems running</p>
                            </div>
                            <div class="gradient-accent w-12 h-12 rounded-lg flex items-center justify-center text-xl">
                                🔒
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Cards -->
                <div>
                    <h2 class="text-xl font-bold font-sora mb-6">Quick Actions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Blog Card -->
                        <a href="manage_blog.php"
                           class="action-card rounded-2xl p-8 card-hover fade-in-up stagger-1 group">
                            <div class="flex items-start justify-between mb-6">
                                <div class="gradient-accent w-14 h-14 rounded-xl flex items-center justify-center text-2xl shadow-lg group-hover:shadow-xl transition">
                                    📰
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-[#FFC261] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold font-sora mb-2">Manage Blog</h3>
                            <p class="text-gray-400 text-sm leading-relaxed">Create, edit, and publish engaging blog posts to keep your audience informed.</p>
                            <div class="mt-6 pt-4 border-t border-white/10 flex items-center text-[#FFC261] text-sm font-medium">
                                Enter Section <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </div>
                        </a>

                        <!-- Workshop Card -->
                        <a href="manage_workshop.php"
                           class="action-card rounded-2xl p-8 card-hover fade-in-up stagger-2 group">
                            <div class="flex items-start justify-between mb-6">
                                <div class="gradient-accent w-14 h-14 rounded-xl flex items-center justify-center text-2xl shadow-lg group-hover:shadow-xl transition">
                                    🎓
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-[#FFC261] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold font-sora mb-2">Manage Workshops</h3>
                            <p class="text-gray-400 text-sm leading-relaxed">Schedule, organize, and manage workshops to engage with your students.</p>
                            <div class="mt-6 pt-4 border-t border-white/10 flex items-center text-[#FFC261] text-sm font-medium">
                                Enter Section <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </div>
                        </a>

                    </div>
                </div>

            </section>

        </main>

    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobileMenu" class="hidden fixed inset-0 bg-black/50 z-50 lg:hidden" onclick="this.classList.add('hidden')"></div>

    <script>
        // Update time and date
        function updateTime() {
            const now = new Date();
            const timeEl = document.getElementById('currentTime');
            const dateEl = document.getElementById('currentDate');
            
            if (timeEl) timeEl.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            if (dateEl) dateEl.textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
        
        updateTime();
        setInterval(updateTime, 1000);
    </script>

</body>
</html>
