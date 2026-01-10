<?php
/**
 * Contact Form Handler
 * 
 * Processes contact form submissions and sends email via MailService
 * Returns JSON response for AJAX calls
 */

// Enable error reporting for debugging
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

// Load MailService with error handling
try {
    if (!file_exists(__DIR__ . '/../includes/MailService.php')) {
        throw new Exception('MailService.php not found');
    }
    require_once __DIR__ . '/../includes/MailService.php';
} catch (Exception $e) {
    error_log('Contact Form - File Load Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'System configuration error. Please contact support.',
        'debug' => $e->getMessage() // Remove in production
    ]);
    exit;
}

try {
    // Get POST data
    $data = [
        'name'    => trim($_POST['name'] ?? ''),
        'email'   => trim($_POST['email'] ?? ''),
        'phone'   => trim($_POST['phone'] ?? ''),
        'subject' => trim($_POST['subject'] ?? ''),
        'message' => trim($_POST['message'] ?? '')
    ];
    
    // Basic sanitization
    $data['name']    = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
    $data['email']   = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $data['phone']   = htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8');
    $data['subject'] = htmlspecialchars($data['subject'], ENT_QUOTES, 'UTF-8');
    $data['message'] = htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8');
    
    // Initialize MailService with error handling
    try {
        $mailService = new MailService();
    } catch (Exception $initError) {
        error_log('MailService Initialization Error: ' . $initError->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Email service configuration error. Please contact support.',
            'debug' => 'MailService init failed: ' . $initError->getMessage()
        ]);
        exit;
    }
    
    // Send contact email
    $result = $mailService->sendContactEmail($data);
    
    // Log if failed (optional)
    if (!$result['success']) {
        error_log('Contact form submission failed: ' . json_encode($data));
    }
    
    // Return JSON response
    echo json_encode($result);
    
} catch (Exception $e) {
    // Log detailed error for debugging
    error_log('Contact Form Error: ' . $e->getMessage());
    error_log('Error File: ' . $e->getFile() . ' on line ' . $e->getLine());
    error_log('Stack Trace: ' . $e->getTraceAsString());
    
    // Return error response with debug info
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again later.',
        'debug' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
