<?php
$page_title = 'Manage Events';
include __DIR__ . '/header.php';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get event image
    $query = "SELECT image FROM events WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $event = mysqli_fetch_assoc($result);
        
        // Delete image file
        if ($event['image']) {
            delete_file(__DIR__ . '/../uploads/events/' . $event['image']);
        }
        
        // Delete event
        $delete_query = "DELETE FROM events WHERE id = $id";
        mysqli_query($conn, $delete_query);
        
        header('Location: /admin/events.php?success=deleted');
        exit();
    }
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Count total events
$count_query = "SELECT COUNT(*) as total FROM events";
$count_result = mysqli_query($conn, $count_query);
$total_events = mysqli_fetch_assoc($count_result)['total'];
$pagination = get_pagination_data($total_events, $page, $per_page);

// Fetch events
$query = "SELECT * FROM events ORDER BY event_date DESC LIMIT $per_page OFFSET $offset";
$result = mysqli_query($conn, $query);
$events = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
}
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold mb-2">Manage Events</h1>
        <p class="text-gray-400">Create, edit, and manage your events</p>
    </div>
    <a href="/admin/event-add.php" class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white px-6 py-3 rounded-lg font-semibold transition">
        <span class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Event
        </span>
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <?php if ($_GET['success'] == 'added'): ?>
        <?php echo show_success('Event added successfully!'); ?>
    <?php elseif ($_GET['success'] == 'updated'): ?>
        <?php echo show_success('Event updated successfully!'); ?>
    <?php elseif ($_GET['success'] == 'deleted'): ?>
        <?php echo show_success('Event deleted successfully!'); ?>
    <?php endif; ?>
<?php endif; ?>

<div class="bg-dark-card border border-dark-border rounded-lg overflow-hidden">
    <?php if (count($events) > 0): ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-dark-bg">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                <?php foreach ($events as $event): ?>
                <tr class="hover:bg-dark-bg transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <?php if ($event['image']): ?>
                            <img src="/uploads/events/<?php echo escape_html($event['image']); ?>" alt="" class="w-12 h-12 rounded object-cover mr-3">
                            <?php endif; ?>
                            <div>
                                <div class="font-medium"><?php echo escape_html($event['title']); ?></div>
                                <div class="text-sm text-gray-400"><?php echo escape_html(substr($event['description'], 0, 50)) . '...'; ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400">
                        <div><?php echo format_date($event['event_date']); ?></div>
                        <?php if ($event['event_time']): ?>
                        <div class="text-sm"><?php echo date('g:i A', strtotime($event['event_time'])); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-gray-400"><?php echo escape_html($event['location']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded <?php 
                            echo $event['status'] == 'upcoming' ? 'bg-blue-500/20 text-blue-400' : 
                                ($event['status'] == 'completed' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'); 
                        ?>">
                            <?php echo ucfirst($event['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="/admin/event-edit.php?id=<?php echo $event['id']; ?>" class="text-green-400 hover:text-green-300 p-2" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <a href="/admin/events.php?delete=<?php echo $event['id']; ?>" onclick="return confirm('Are you sure you want to delete this event?')" class="text-red-400 hover:text-red-300 p-2" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="px-6 py-4 bg-dark-bg border-t border-dark-border flex justify-center gap-2">
        <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="bg-dark-card border border-dark-border hover:border-blue-500 px-4 py-2 rounded transition">
            Previous
        </a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <?php if ($i == $page): ?>
            <span class="bg-blue-500 text-white px-4 py-2 rounded"><?php echo $i; ?></span>
            <?php else: ?>
            <a href="?page=<?php echo $i; ?>" class="bg-dark-card border border-dark-border hover:border-blue-500 px-4 py-2 rounded transition">
                <?php echo $i; ?>
            </a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($page < $pagination['total_pages']): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="bg-dark-card border border-dark-border hover:border-blue-500 px-4 py-2 rounded transition">
            Next
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="p-12 text-center text-gray-400">
        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <p class="text-lg mb-4">No events yet</p>
        <a href="/admin/event-add.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded transition">
            Add Your First Event
        </a>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
