<?php
/**
 * Workshop Registration Handler - PRODUCTION SAFE
 * 
 * Processes workshop registration and sends confirmation emails
 * ALWAYS returns valid JSON, even on fatal errors
 */

// Load JSON response handler FIRST (before any output)
require_once __DIR__ . '/../includes/JsonResponse.php';

// Initialize error handlers (set to true for debugging, false for production)
JsonResponse::init(true); // Change to false after fixing issues

// Validate request method
JsonResponse::requireMethod('POST');

// Validate required fields
JsonResponse::validatePost(['name', 'email', 'contact', 'workshop_title']);

try {
    // Load dependencies with safe error handling
    JsonResponse::requireFile(__DIR__ . '/../includes/MailService.php', 'MailService');
    
    // Try to load database (optional)
    $conn = null;
    if (file_exists(__DIR__ . '/../db.php')) {
        require_once __DIR__ . '/../db.php';
    }
    
    // Get and sanitize POST data
    $data = [
        'name'            => trim($_POST['name'] ?? ''),
        'email'           => trim($_POST['email'] ?? ''),
        'contact'         => trim($_POST['contact'] ?? ''),
        'city'            => trim($_POST['city'] ?? ''),
        'workshop_title'  => trim($_POST['workshop_title'] ?? ''),
        'workshop_id'     => trim($_POST['workshop_id'] ?? ''),
        'workshop_date'   => trim($_POST['workshop_date'] ?? 'TBA')
    ];
    
    // Sanitize inputs
    $data['name']           = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
    $data['email']          = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $data['contact']        = htmlspecialchars($data['contact'], ENT_QUOTES, 'UTF-8');
    $data['city']           = htmlspecialchars($data['city'], ENT_QUOTES, 'UTF-8');
    $data['workshop_title'] = htmlspecialchars($data['workshop_title'], ENT_QUOTES, 'UTF-8');
    $data['workshop_date']  = htmlspecialchars($data['workshop_date'], ENT_QUOTES, 'UTF-8');
    
    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        JsonResponse::error('Invalid email address', 400);
    }
    
    // Validate required fields are not empty after sanitization
    if (empty($data['name']) || empty($data['email']) || empty($data['workshop_title'])) {
        JsonResponse::error('Please fill in all required fields', 400);
    }
    
    // Save to database if connection exists
    $registrationId = null;
    if ($conn !== null && isset($conn)) {
        try {
            $registrationId = saveRegistrationToDatabase($conn, $data);
        } catch (Exception $dbError) {
            // Log but don't fail - database is optional
            error_log('Database save failed: ' . $dbError->getMessage());
        }
    }
    
    // Initialize MailService
    try {
        $mailService = new MailService();
    } catch (Exception $mailError) {
        // If mail service fails, provide helpful error
        $errorMessage = 'Email service unavailable.';
        $debugInfo = [
            'mail_error' => $mailError->getMessage(),
            'possible_causes' => [
                'Missing .env file on server',
                'Missing vendor/PHPMailer files',
                'Invalid SMTP credentials',
                'SMTP port blocked by hosting'
            ]
        ];
        
        JsonResponse::error($errorMessage, 500, $debugInfo);
    }
    
    // Send registration emails
    try {
        $emailResult = $mailService->sendWorkshopRegistration($data);
        
        if (!$emailResult['success']) {
            // Email sending failed
            JsonResponse::error(
                $emailResult['message'] ?? 'Failed to send confirmation email',
                500,
                ['email_error' => $emailResult['message'] ?? 'Unknown error']
            );
        }
        
        // Success! Return with registration details
        $successData = [
            'workshop' => $data['workshop_title'],
            'date' => $data['workshop_date'],
            'registration_id' => $registrationId
        ];
        
        JsonResponse::success(
            'Thank you! Your registration is confirmed. Check your email for workshop details.',
            $successData
        );
        
    } catch (Exception $emailError) {
        JsonResponse::error(
            'Failed to send confirmation email',
            500,
            ['error' => $emailError->getMessage()]
        );
    }
    
} catch (Exception $e) {
    // Catch any unexpected errors
    JsonResponse::error(
        'Registration failed. Please try again or contact support.',
        500,
        [
            'error' => $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    );
}

/**
 * Save registration to database
 */
function saveRegistrationToDatabase($conn, $data) {
    // Create table if not exists
    $createTableSQL = "CREATE TABLE IF NOT EXISTS workshop_registrations (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        contact VARCHAR(50) NOT NULL,
        city VARCHAR(100),
        workshop_title VARCHAR(500) NOT NULL,
        workshop_id INT(11),
        workshop_date VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_workshop_id (workshop_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (!$conn->query($createTableSQL)) {
        throw new Exception('Failed to create table: ' . $conn->error);
    }
    
    // Prepare insert statement
    $stmt = $conn->prepare(
        "INSERT INTO workshop_registrations (name, email, contact, city, workshop_title, workshop_id, workshop_date) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    
    if (!$stmt) {
        throw new Exception('Database prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param(
        "sssssis",
        $data['name'],
        $data['email'],
        $data['contact'],
        $data['city'],
        $data['workshop_title'],
        $data['workshop_id'],
        $data['workshop_date']
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Database insert failed: ' . $stmt->error);
    }
    
    $insertId = $stmt->insert_id;
    $stmt->close();
    
    return $insertId;
}
