<?php
$page_title = 'Edit Event';
include __DIR__ . '/header.php';

$success = '';
$error = '';

// Get event ID
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($event_id == 0) {
    header('Location: /admin/events.php');
    exit();
}

// Fetch event
$query = "SELECT * FROM events WHERE id = $event_id LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: /admin/events.php');
    exit();
}

$event = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = clean_input($conn, $_POST['title'] ?? '');
    $description = clean_input($conn, $_POST['description'] ?? '');
    $event_date = clean_input($conn, $_POST['event_date'] ?? '');
    $event_time = clean_input($conn, $_POST['event_time'] ?? '');
    $location = clean_input($conn, $_POST['location'] ?? '');
    $status = clean_input($conn, $_POST['status'] ?? 'upcoming');
    
    // Validate
    if (empty($title) || empty($description) || empty($event_date)) {
        $error = 'Please fill in all required fields.';
    } else {
        $image_filename = $event['image'];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_image($_FILES['image'], __DIR__ . '/../uploads/events');
            if ($upload_result['success']) {
                // Delete old image
                if ($event['image']) {
                    delete_file(__DIR__ . '/../uploads/events/' . $event['image']);
                }
                $image_filename = $upload_result['filename'];
            } else {
                $error = $upload_result['error'];
            }
        }
        
        // Handle image removal
        if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1' && $event['image']) {
            delete_file(__DIR__ . '/../uploads/events/' . $event['image']);
            $image_filename = '';
        }
        
        if (empty($error)) {
            // Update event
            $query = "UPDATE events SET title = '$title', description = '$description', image = '$image_filename', event_date = '$event_date', event_time = '$event_time', location = '$location', status = '$status' WHERE id = $event_id";
            
            if (mysqli_query($conn, $query)) {
                header('Location: /admin/events.php?success=updated');
                exit();
            } else {
                $error = 'Failed to update event.';
            }
        }
    }
}
?>

<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="/admin/events.php" class="text-blue-400 hover:text-blue-300 mr-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-3xl font-bold">Edit Event</h1>
    </div>
    <p class="text-gray-400 ml-9">Update event details</p>
</div>

<?php if ($error): ?>
    <?php echo show_error($error); ?>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data" class="bg-dark-card border border-dark-border rounded-lg p-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium mb-2">
                    Event Title <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    required
                    class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                    placeholder="Enter event title"
                    value="<?php echo isset($_POST['title']) ? escape_html($_POST['title']) : escape_html($event['title']); ?>"
                >
            </div>

            <div>
                <label for="description" class="block text-sm font-medium mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="8" 
                    required
                    class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition resize-none"
                    placeholder="Enter event description..."
                ><?php echo isset($_POST['description']) ? escape_html($_POST['description']) : escape_html($event['description']); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="event_date" class="block text-sm font-medium mb-2">
                        Event Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="event_date" 
                        name="event_date" 
                        required
                        class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                        value="<?php echo isset($_POST['event_date']) ? escape_html($_POST['event_date']) : escape_html($event['event_date']); ?>"
                    >
                </div>

                <div>
                    <label for="event_time" class="block text-sm font-medium mb-2">Event Time</label>
                    <input 
                        type="time" 
                        id="event_time" 
                        name="event_time" 
                        class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                        value="<?php echo isset($_POST['event_time']) ? escape_html($_POST['event_time']) : escape_html($event['event_time']); ?>"
                    >
                </div>
            </div>

            <div>
                <label for="location" class="block text-sm font-medium mb-2">Location</label>
                <input 
                    type="text" 
                    id="location" 
                    name="location" 
                    class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                    placeholder="e.g., Online Webinar, Convention Center, Mumbai"
                    value="<?php echo isset($_POST['location']) ? escape_html($_POST['location']) : escape_html($event['location']); ?>"
                >
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div>
                <label for="status" class="block text-sm font-medium mb-2">Status</label>
                <select 
                    id="status" 
                    name="status" 
                    class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                >
                    <?php $current_status = isset($_POST['status']) ? $_POST['status'] : $event['status']; ?>
                    <option value="upcoming" <?php echo $current_status == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="completed" <?php echo $current_status == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $current_status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Current Image</label>
                <?php if ($event['image']): ?>
                <div class="mb-3">
                    <img src="/uploads/events/<?php echo escape_html($event['image']); ?>" alt="" class="w-full rounded border border-dark-border">
                    <label class="flex items-center mt-2 text-sm">
                        <input type="checkbox" name="remove_image" value="1" class="mr-2">
                        <span class="text-red-400">Remove current image</span>
                    </label>
                </div>
                <?php else: ?>
                <p class="text-gray-400 text-sm mb-3">No image uploaded</p>
                <?php endif; ?>
                
                <label for="image" class="block text-sm font-medium mb-2">Upload New Image</label>
                <input 
                    type="file" 
                    id="image" 
                    name="image" 
                    accept="image/*"
                    class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600"
                >
                <p class="text-sm text-gray-400 mt-2">Max size: 5MB. Supported: JPG, PNG, GIF, WebP</p>
            </div>

            <div class="pt-4 border-t border-dark-border space-y-3">
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white px-6 py-3 rounded-lg font-semibold transition"
                >
                    Update Event
                </button>
                <a 
                    href="/admin/events.php" 
                    class="block w-full text-center bg-dark-bg border border-dark-border hover:border-gray-600 text-gray-300 px-6 py-3 rounded-lg font-semibold transition"
                >
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

<?php include __DIR__ . '/footer.php'; ?>
