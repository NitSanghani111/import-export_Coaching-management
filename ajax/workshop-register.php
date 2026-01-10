<?php
/**
 * Workshop Registration Handler
 * 
 * Processes workshop registration and sends confirmation emails
 * Sends two emails: admin notification + user confirmation
 * Returns JSON response for AJAX calls
 */

// Enable error reporting for debugging (disable in production after fixing)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to user
ini_set('log_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST is allowed.'
    ]);
    exit;
}

// Try-catch for loading dependencies
try {
    // Load MailService
    if (!file_exists(__DIR__ . '/../includes/MailService.php')) {
        throw new Exception('MailService.php not found. Check file path.');
    }
    require_once __DIR__ . '/../includes/MailService.php';
    
    // Optionally, load database connection to save registration
    if (file_exists(__DIR__ . '/../db.php')) {
        require_once __DIR__ . '/../db.php';
    } else {
        $conn = null; // Database optional
        error_log('Workshop Registration: db.php not found, skipping database save');
    }
} catch (Exception $e) {
    error_log('Workshop Registration - File Load Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'System configuration error. Please contact support.',
        'debug' => $e->getMessage() // Remove this line in production
    ]);
    exit;
}

try {
    // Get POST data
    $data = [
        'name'            => trim($_POST['name'] ?? ''),
        'email'           => trim($_POST['email'] ?? ''),
        'contact'         => trim($_POST['contact'] ?? ''),
        'who'             => trim($_POST['who'] ?? ''),
        'message'         => trim($_POST['message'] ?? ''),
        'workshop_id'     => trim($_POST['workshop_id'] ?? ''),
        'workshop_title'  => trim($_POST['workshop_title'] ?? ''),
        'workshop_date'   => trim($_POST['workshop_date'] ?? '')
    ];
    
    // Basic sanitization
    foreach ($data as $key => $value) {
        $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    // Additional email validation
    $data['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    
    // Save registration to database (optional but recommended)
    if (isset($conn) && $conn) {
        $saved = saveRegistrationToDatabase($conn, $data);
        
        if (!$saved) {
            error_log('Failed to save workshop registration to database: ' . json_encode($data));
            // Continue anyway - we'll still send emails
        }
    } else {
        error_log('Workshop Registration: Database connection not available, skipping save');
    }
    
    // Initialize MailService
    try {
        $mailService = new MailService();
    } catch (Exception $e) {
        error_log('Workshop Registration - MailService Init Error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Email system error. Please contact support.',
            'debug' => 'MailService Error: ' . $e->getMessage() // Remove in production
        ]);
        exit;
    }
    
    // Send workshop registration emails (admin + user)
    $result = $mailService->sendWorkshopRegistration($data);
    
    // Log if failed
    if (!$result['success']) {
        error_log('Workshop registration email failed: ' . json_encode($data));
    }
    
    // Return JSON response
    echo json_encode($result);
    
} catch (Exception $e) {
    // Log error with full trace
    error_log('Workshop Registration Error: ' . $e->getMessage());
    error_log('Stack Trace: ' . $e->getTraceAsString());
    
    // Return error response with debug info
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again later.',
        'debug' => $e->getMessage(), // Remove in production
        'file' => $e->getFile(), // Remove in production
        'line' => $e->getLine() // Remove in production
    ]);
}
}

/**
 * Save registration to database
 * 
 * @param mysqli $conn Database connection
 * @param array $data Registration data
 * @return bool Success status
 */
function saveRegistrationToDatabase($conn, $data) {
    try {
        // Check if workshop_registrations table exists
        $tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'workshop_registrations'");
        
        if (mysqli_num_rows($tableCheck) == 0) {
            // Create table if it doesn't exist
            $createTable = "
                CREATE TABLE IF NOT EXISTS workshop_registrations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    workshop_id INT,
                    workshop_title VARCHAR(255),
                    workshop_date VARCHAR(100),
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    contact VARCHAR(50),
                    who_are_you VARCHAR(255),
                    message TEXT,
                    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_workshop_id (workshop_id),
                    INDEX idx_email (email),
                    INDEX idx_registered_at (registered_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            
            if (!mysqli_query($conn, $createTable)) {
                error_log('Failed to create workshop_registrations table: ' . mysqli_error($conn));
                return false;
            }
        }
        
        // Prepare statement to prevent SQL injection
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO workshop_registrations 
            (workshop_id, workshop_title, workshop_date, name, email, contact, who_are_you, message) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        if (!$stmt) {
            error_log('Failed to prepare statement: ' . mysqli_error($conn));
            return false;
        }
        
        // Bind parameters
        mysqli_stmt_bind_param($stmt, 'isssssss',
            $data['workshop_id'],
            $data['workshop_title'],
            $data['workshop_date'],
            $data['name'],
            $data['email'],
            $data['contact'],
            $data['who'],
            $data['message']
        );
        
        // Execute
        $result = mysqli_stmt_execute($stmt);
        
        if (!$result) {
            error_log('Failed to execute statement: ' . mysqli_stmt_error($stmt));
            return false;
        }
        
        mysqli_stmt_close($stmt);
        return true;
        
    } catch (Exception $e) {
        error_log('Database save error: ' . $e->getMessage());
        return false;
    }
}
