<?php
/**
 * Contact Form Handler
 * 
 * Processes contact form submissions and sends email via MailService
 * Returns JSON response for AJAX calls
 */

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

// Load MailService
require_once __DIR__ . '/../includes/MailService.php';

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
    
    // Initialize MailService
    $mailService = new MailService();
    
    // Send contact email
    $result = $mailService->sendContactEmail($data);
    
    // Log if failed (optional)
    if (!$result['success']) {
        error_log('Contact form submission failed: ' . json_encode($data));
    }
    
    // Return JSON response
    echo json_encode($result);
    
} catch (Exception $e) {
    // Log error
    error_log('Contact Form Error: ' . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again later.'
    ]);
}
