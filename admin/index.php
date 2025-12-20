<?php
$page_title = 'Dashboard';
include __DIR__ . '/header.php';

// Get statistics
$blog_count_query = "SELECT COUNT(*) as total FROM blogs";
$blog_count_result = mysqli_query($conn, $blog_count_query);
$total_blogs = mysqli_fetch_assoc($blog_count_result)['total'];

$published_blog_query = "SELECT COUNT(*) as total FROM blogs WHERE status = 'published'";
$published_blog_result = mysqli_query($conn, $published_blog_query);
$published_blogs = mysqli_fetch_assoc($published_blog_result)['total'];

$event_count_query = "SELECT COUNT(*) as total FROM events";
$event_count_result = mysqli_query($conn, $event_count_query);
$total_events = mysqli_fetch_assoc($event_count_result)['total'];

$upcoming_event_query = "SELECT COUNT(*) as total FROM events WHERE status = 'upcoming' AND event_date >= CURDATE()";
$upcoming_event_result = mysqli_query($conn, $upcoming_event_query);
$upcoming_events = mysqli_fetch_assoc($upcoming_event_result)['total'];

// Get recent blogs
$recent_blogs_query = "SELECT * FROM blogs ORDER BY created_at DESC LIMIT 5";
$recent_blogs_result = mysqli_query($conn, $recent_blogs_query);
$recent_blogs = [];
if ($recent_blogs_result) {
    while ($row = mysqli_fetch_assoc($recent_blogs_result)) {
        $recent_blogs[] = $row;
    }
}

// Get recent events
$recent_events_query = "SELECT * FROM events ORDER BY created_at DESC LIMIT 5";
$recent_events_result = mysqli_query($conn, $recent_events_query);
$recent_events = [];
if ($recent_events_result) {
    while ($row = mysqli_fetch_assoc($recent_events_result)) {
        $recent_events[] = $row;
    }
}
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold mb-2">Welcome back, <?php echo escape_html($_SESSION['admin_username']); ?>!</h1>
    <p class="text-gray-400">Here's an overview of your website</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-dark-card border border-dark-border rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
        <div class="text-3xl font-bold mb-1"><?php echo $total_blogs; ?></div>
        <div class="text-gray-400 text-sm">Total Blog Posts</div>
    </div>

    <div class="bg-dark-card border border-dark-border rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <div class="text-3xl font-bold mb-1"><?php echo $published_blogs; ?></div>
        <div class="text-gray-400 text-sm">Published Blogs</div>
    </div>

    <div class="bg-dark-card border border-dark-border rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
        <div class="text-3xl font-bold mb-1"><?php echo $total_events; ?></div>
        <div class="text-gray-400 text-sm">Total Events</div>
    </div>

    <div class="bg-dark-card border border-dark-border rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-orange-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <div class="text-3xl font-bold mb-1"><?php echo $upcoming_events; ?></div>
        <div class="text-gray-400 text-sm">Upcoming Events</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-dark-card border border-dark-border rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
        <div class="space-y-3">
            <a href="/admin/blog-add.php" class="flex items-center justify-between p-3 bg-dark-bg rounded-lg hover:bg-gray-800 transition group">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Blog Post
                </span>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            <a href="/admin/event-add.php" class="flex items-center justify-between p-3 bg-dark-bg rounded-lg hover:bg-gray-800 transition group">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Event
                </span>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            <a href="/admin/blogs.php" class="flex items-center justify-between p-3 bg-dark-bg rounded-lg hover:bg-gray-800 transition group">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Manage Blogs
                </span>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            <a href="/admin/events.php" class="flex items-center justify-between p-3 bg-dark-bg rounded-lg hover:bg-gray-800 transition group">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Manage Events
                </span>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Recent Activity placeholder -->
    <div class="bg-dark-card border border-dark-border rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Recent Blogs</h2>
        <?php if (count($recent_blogs) > 0): ?>
        <div class="space-y-3">
            <?php foreach (array_slice($recent_blogs, 0, 5) as $blog): ?>
            <div class="flex items-center justify-between p-3 bg-dark-bg rounded-lg">
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium truncate"><?php echo escape_html($blog['title']); ?></div>
                    <div class="text-xs text-gray-400"><?php echo format_date($blog['created_at']); ?></div>
                </div>
                <span class="ml-2 px-2 py-1 text-xs rounded <?php echo $blog['status'] == 'published' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400'; ?>">
                    <?php echo ucfirst($blog['status']); ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-gray-400">No blogs yet</p>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Events -->
<div class="bg-dark-card border border-dark-border rounded-lg p-6">
    <h2 class="text-xl font-bold mb-4">Recent Events</h2>
    <?php if (count($recent_events) > 0): ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left border-b border-dark-border">
                    <th class="pb-3 text-sm font-semibold text-gray-400">Title</th>
                    <th class="pb-3 text-sm font-semibold text-gray-400">Date</th>
                    <th class="pb-3 text-sm font-semibold text-gray-400">Location</th>
                    <th class="pb-3 text-sm font-semibold text-gray-400">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($recent_events, 0, 5) as $event): ?>
                <tr class="border-b border-dark-border">
                    <td class="py-3"><?php echo escape_html($event['title']); ?></td>
                    <td class="py-3 text-gray-400"><?php echo format_date($event['event_date']); ?></td>
                    <td class="py-3 text-gray-400"><?php echo escape_html($event['location']); ?></td>
                    <td class="py-3">
                        <span class="px-2 py-1 text-xs rounded <?php 
                            echo $event['status'] == 'upcoming' ? 'bg-blue-500/20 text-blue-400' : 
                                ($event['status'] == 'completed' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'); 
                        ?>">
                            <?php echo ucfirst($event['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="text-gray-400">No events yet</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
