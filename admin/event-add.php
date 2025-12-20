<?php
$page_title = 'Add New Event';
include __DIR__ . '/header.php';

$success = '';
$error = '';

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
        $image_filename = '';
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_image($_FILES['image'], __DIR__ . '/../uploads/events');
            if ($upload_result['success']) {
                $image_filename = $upload_result['filename'];
            } else {
                $error = $upload_result['error'];
            }
        }
        
        if (empty($error)) {
            // Insert event
            $query = "INSERT INTO events (title, description, image, event_date, event_time, location, status) VALUES ('$title', '$description', '$image_filename', '$event_date', '$event_time', '$location', '$status')";
            
            if (mysqli_query($conn, $query)) {
                header('Location: /admin/events.php?success=added');
                exit();
            } else {
                $error = 'Failed to add event.';
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
        <h1 class="text-3xl font-bold">Add New Event</h1>
    </div>
    <p class="text-gray-400 ml-9">Create a new event for your website</p>
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
                    value="<?php echo isset($_POST['title']) ? escape_html($_POST['title']) : ''; ?>"
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
                ><?php echo isset($_POST['description']) ? escape_html($_POST['description']) : ''; ?></textarea>
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
                        value="<?php echo isset($_POST['event_date']) ? escape_html($_POST['event_date']) : ''; ?>"
                    >
                </div>

                <div>
                    <label for="event_time" class="block text-sm font-medium mb-2">Event Time</label>
                    <input 
                        type="time" 
                        id="event_time" 
                        name="event_time" 
                        class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                        value="<?php echo isset($_POST['event_time']) ? escape_html($_POST['event_time']) : ''; ?>"
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
                    value="<?php echo isset($_POST['location']) ? escape_html($_POST['location']) : ''; ?>"
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
                    <option value="upcoming" <?php echo (isset($_POST['status']) && $_POST['status'] == 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="completed" <?php echo (isset($_POST['status']) && $_POST['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo (isset($_POST['status']) && $_POST['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium mb-2">Event Image</label>
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
                    Create Event
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
