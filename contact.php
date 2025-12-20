<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Contact Us';

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($conn, $_POST['name'] ?? '');
    $email = clean_input($conn, $_POST['email'] ?? '');
    $phone = clean_input($conn, $_POST['phone'] ?? '');
    $subject = clean_input($conn, $_POST['subject'] ?? '');
    $message = clean_input($conn, $_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // In a real application, you would send an email or store in database
        // For now, we'll just show a success message
        $success = 'Thank you for contacting us! We will get back to you soon.';
        
        // Clear form fields
        $name = $email = $phone = $subject = $message = '';
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
            Contact Us
        </h1>
        <p class="text-xl text-gray-400 max-w-2xl mx-auto">
            Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Contact Form -->
        <div class="bg-dark-card border border-dark-border rounded-lg p-8">
            <h2 class="text-2xl font-bold mb-6">Send us a Message</h2>
            
            <?php if ($success): ?>
                <?php echo show_success($success); ?>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <?php echo show_error($error); ?>
            <?php endif; ?>
            
            <form method="POST" action="" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="<?php echo isset($name) ? escape_html($name) : ''; ?>"
                        required
                        class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                        placeholder="John Doe"
                    >
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo isset($email) ? escape_html($email) : ''; ?>"
                        required
                        class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                        placeholder="john@example.com"
                    >
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium mb-2">
                        Phone Number
                    </label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        value="<?php echo isset($phone) ? escape_html($phone) : ''; ?>"
                        class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                        placeholder="+1 (555) 123-4567"
                    >
                </div>
                
                <div>
                    <label for="subject" class="block text-sm font-medium mb-2">
                        Subject
                    </label>
                    <input 
                        type="text" 
                        id="subject" 
                        name="subject" 
                        value="<?php echo isset($subject) ? escape_html($subject) : ''; ?>"
                        class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition"
                        placeholder="How can we help?"
                    >
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium mb-2">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="message" 
                        name="message" 
                        rows="5" 
                        required
                        class="w-full bg-dark-bg border border-dark-border rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-blue-500 transition resize-none"
                        placeholder="Tell us what you're interested in..."
                    ><?php echo isset($message) ? escape_html($message) : ''; ?></textarea>
                </div>
                
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white px-6 py-3 rounded-lg font-semibold transition"
                >
                    Send Message
                </button>
            </form>
        </div>

        <!-- Contact Information -->
        <div class="space-y-8">
            <div class="bg-dark-card border border-dark-border rounded-lg p-8">
                <h2 class="text-2xl font-bold mb-6">Get in Touch</h2>
                
                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Email</h3>
                            <p class="text-gray-400">info@iecoaching.com</p>
                            <p class="text-gray-400">support@iecoaching.com</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Phone</h3>
                            <p class="text-gray-400">+1 (555) 123-4567</p>
                            <p class="text-gray-400">+1 (555) 765-4321</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Office Address</h3>
                            <p class="text-gray-400">123 Business Street</p>
                            <p class="text-gray-400">Suite 456, City Name</p>
                            <p class="text-gray-400">State, ZIP Code</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Business Hours</h3>
                            <p class="text-gray-400">Monday - Friday: 9:00 AM - 6:00 PM</p>
                            <p class="text-gray-400">Saturday: 10:00 AM - 4:00 PM</p>
                            <p class="text-gray-400">Sunday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Quick Links -->
            <div class="bg-dark-card border border-dark-border rounded-lg p-8">
                <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="/blog.php" class="text-blue-400 hover:text-blue-300 flex items-center transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Browse Our Blog
                        </a>
                    </li>
                    <li>
                        <a href="/events.php" class="text-blue-400 hover:text-blue-300 flex items-center transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            View Upcoming Events
                        </a>
                    </li>
                    <li>
                        <a href="/about.php" class="text-blue-400 hover:text-blue-300 flex items-center transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Learn More About Us
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
