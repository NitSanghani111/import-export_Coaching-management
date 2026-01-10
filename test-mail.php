<?php
/**
 * Quick Test Script for Email System
 * 
 * Run this file to verify your PHPMailer setup is working
 * Usage: php test-mail.php
 */

require_once __DIR__ . '/includes/MailService.php';

echo "======================================\n";
echo "Testing PHPMailer Configuration\n";
echo "======================================\n\n";

try {
    // Initialize MailService
    echo "1. Loading MailService...\n";
    $mailService = new MailService();
    echo "   ✓ MailService loaded successfully\n\n";
    
    // Test Contact Email
    echo "2. Testing Contact Form Email...\n";
    $contactData = [
        'name'    => 'Test User',
        'email'   => 'test@example.com',
        'phone'   => '+1234567890',
        'subject' => 'Test Email from PHPMailer',
        'message' => 'This is a test message to verify that the email system is working correctly.'
    ];
    
    $contactResult = $mailService->sendContactEmail($contactData);
    
    if ($contactResult['success']) {
        echo "   ✓ Contact email sent successfully\n";
        echo "   Message: " . $contactResult['message'] . "\n\n";
    } else {
        echo "   ✗ Contact email failed\n";
        echo "   Error: " . $contactResult['message'] . "\n\n";
    }
    
    // Test Workshop Registration Email
    echo "3. Testing Workshop Registration Email...\n";
    $workshopData = [
        'name'           => 'Test User',
        'email'          => 'test@example.com',
        'contact'        => '+1234567890',
        'who'            => 'Test Role',
        'message'        => 'This is a test registration message.',
        'workshop_id'    => '1',
        'workshop_title' => 'Test Workshop: Business Systems',
        'workshop_date'  => '25 December 2024'
    ];
    
    $workshopResult = $mailService->sendWorkshopRegistration($workshopData);
    
    if ($workshopResult['success']) {
        echo "   ✓ Workshop registration emails sent successfully\n";
        echo "   Message: " . $workshopResult['message'] . "\n\n";
    } else {
        echo "   ✗ Workshop registration failed\n";
        echo "   Error: " . $workshopResult['message'] . "\n\n";
    }
    
    echo "======================================\n";
    echo "Test Complete!\n";
    echo "======================================\n";
    
    if ($contactResult['success'] && $workshopResult['success']) {
        echo "\n✓ All tests passed! Your email system is working.\n";
        echo "Check your inbox for test emails.\n";
    } else {
        echo "\n✗ Some tests failed. Check the errors above.\n";
        echo "Common issues:\n";
        echo "  - Invalid SMTP credentials in .env\n";
        echo "  - Gmail App Password not configured\n";
        echo "  - Port 587 blocked by firewall\n";
        echo "  - PHPMailer not installed (run: composer install)\n";
    }
    
} catch (Exception $e) {
    echo "\n✗ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "  1. .env file exists and has correct values\n";
    echo "  2. PHPMailer is installed (run: composer install)\n";
    echo "  3. config/mail.php is accessible\n";
}

echo "\n";
