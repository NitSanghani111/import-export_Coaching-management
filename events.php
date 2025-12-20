<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Events';

// Fetch upcoming and past events
$upcoming_query = "SELECT * FROM events WHERE status = 'upcoming' AND event_date >= CURDATE() ORDER BY event_date ASC";
$upcoming_result = mysqli_query($conn, $upcoming_query);
$upcoming_events = [];
if ($upcoming_result) {
    while ($row = mysqli_fetch_assoc($upcoming_result)) {
        $upcoming_events[] = $row;
    }
}

$past_query = "SELECT * FROM events WHERE status = 'completed' OR event_date < CURDATE() ORDER BY event_date DESC LIMIT 6";
$past_result = mysqli_query($conn, $past_query);
$past_events = [];
if ($past_result) {
    while ($row = mysqli_fetch_assoc($past_result)) {
        $past_events[] = $row;
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
            Events & Workshops
        </h1>
        <p class="text-xl text-gray-400 max-w-2xl mx-auto">
            Join our workshops, seminars, and networking events to expand your knowledge and connect with industry experts.
        </p>
    </div>

    <!-- Upcoming Events -->
    <div class="mb-16">
        <h2 class="text-3xl font-bold mb-8">Upcoming Events</h2>
        
        <?php if (count($upcoming_events) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($upcoming_events as $event): ?>
            <div class="bg-dark-card border border-dark-border rounded-lg overflow-hidden hover:border-blue-500/50 transition">
                <?php if ($event['image']): ?>
                <div class="h-48 bg-gradient-to-br from-blue-500/20 to-purple-500/20 overflow-hidden">
                    <img src="/uploads/events/<?php echo escape_html($event['image']); ?>" alt="<?php echo escape_html($event['title']); ?>" class="w-full h-full object-cover">
                </div>
                <?php else: ?>
                <div class="h-48 bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <?php endif; ?>
                
                <div class="p-6">
                    <div class="inline-block bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-sm font-semibold mb-3">
                        Upcoming
                    </div>
                    <h3 class="text-2xl font-semibold mb-3"><?php echo escape_html($event['title']); ?></h3>
                    <p class="text-gray-400 mb-4"><?php echo escape_html($event['description']); ?></p>
                    
                    <div class="space-y-2 text-gray-400">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <?php echo format_date($event['event_date']); ?>
                            <?php if ($event['event_time']): ?>
                                at <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($event['location']): ?>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <?php echo escape_html($event['location']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <a href="/contact.php" class="inline-block mt-4 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white px-6 py-2 rounded transition">
                        Register Now
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center text-gray-400 py-12 bg-dark-card border border-dark-border rounded-lg">
            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-lg">No upcoming events at the moment. Check back soon!</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Past Events -->
    <?php if (count($past_events) > 0): ?>
    <div>
        <h2 class="text-3xl font-bold mb-8">Past Events</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($past_events as $event): ?>
            <div class="bg-dark-card border border-dark-border rounded-lg overflow-hidden opacity-75">
                <?php if ($event['image']): ?>
                <div class="h-40 bg-gradient-to-br from-blue-500/20 to-purple-500/20 overflow-hidden">
                    <img src="/uploads/events/<?php echo escape_html($event['image']); ?>" alt="<?php echo escape_html($event['title']); ?>" class="w-full h-full object-cover">
                </div>
                <?php else: ?>
                <div class="h-40 bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <?php endif; ?>
                
                <div class="p-4">
                    <div class="inline-block bg-gray-500/20 text-gray-400 px-3 py-1 rounded-full text-sm font-semibold mb-2">
                        Completed
                    </div>
                    <h3 class="text-lg font-semibold mb-2"><?php echo escape_html($event['title']); ?></h3>
                    <p class="text-sm text-gray-400 mb-2">
                        <?php echo escape_html(substr($event['description'], 0, 100)) . '...'; ?>
                    </p>
                    <div class="text-sm text-gray-500"><?php echo format_date($event['event_date']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
