<?php
/**
 * Contact Form Handler - PRODUCTION SAFE
 * 
 * Processes contact form submissions and sends email via MailService
 * ALWAYS returns valid JSON, even on fatal errors
 */

// Load JSON response handler FIRST (before any output)
require_once __DIR__ . '/../includes/JsonResponse.php';

// Initialize error handlers (set to true for debugging, false for production)
JsonResponse::init(true); // Change to false after fixing issues

// Validate request method
JsonResponse::requireMethod('POST');

// Validate required fields
JsonResponse::validatePost(['name', 'email', 'message']);

try {
    // Load MailService with safe error handling
    JsonResponse::requireFile(__DIR__ . '/../includes/MailService.php', 'MailService');
    
    // Get and sanitize POST data
    $data = [
        'name'    => trim($_POST['name'] ?? ''),
        'email'   => trim($_POST['email'] ?? ''),
        'phone'   => trim($_POST['phone'] ?? ''),
        'subject' => trim($_POST['subject'] ?? ''),
        'message' => trim($_POST['message'] ?? '')
    ];
    
    // Sanitize inputs
    $data['name']    = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
    $data['email']   = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $data['phone']   = htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8');
    $data['subject'] = htmlspecialchars($data['subject'], ENT_QUOTES, 'UTF-8');
    $data['message'] = htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8');
    
    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        JsonResponse::error('Invalid email address', 400);
    }
    
    // Validate message not empty
    if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
        JsonResponse::error('Please fill in all required fields', 400);
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
    
    // Send contact email
    try {
        $result = $mailService->sendContactEmail($data);
        
        if (!$result['success']) {
            // Email sending failed
            JsonResponse::error(
                $result['message'] ?? 'Failed to send message',
                500,
                ['email_error' => $result['message'] ?? 'Unknown error']
            );
        }
        
        // Success!
        JsonResponse::success(
            'Thank you for contacting us! We will get back to you soon.',
            ['sender' => $data['name']]
        );
        
    } catch (Exception $emailError) {
        JsonResponse::error(
            'Failed to send message',
            500,
            ['error' => $emailError->getMessage()]
        );
    }
    
} catch (Exception $e) {
    // Catch any unexpected errors
    JsonResponse::error(
        'Message sending failed. Please try again or contact us directly.',
        500,
        [
            'error' => $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    );
}
