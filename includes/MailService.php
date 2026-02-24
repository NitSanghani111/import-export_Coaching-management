<?php
/**
 * MailService Class
 * 
 * Reusable PHPMailer service for sending emails
 * Handles SMTP configuration, email sending, and error handling
 */

// Load PHPMailer classes manually (no Composer required)
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    // Try Composer autoloader first
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require __DIR__ . '/../vendor/autoload.php';
    }
    // Try vendor/src structure (manual download)
    elseif (file_exists(__DIR__ . '/../vendor/src/Exception.php')) {
        require __DIR__ . '/../vendor/src/Exception.php';
        require __DIR__ . '/../vendor/src/PHPMailer.php';
        require __DIR__ . '/../vendor/src/SMTP.php';
    }
    // Try root vendor structure
    elseif (file_exists(__DIR__ . '/../vendor/Exception.php')) {
        require __DIR__ . '/../vendor/Exception.php';
        require __DIR__ . '/../vendor/PHPMailer.php';
        require __DIR__ . '/../vendor/SMTP.php';
    }
    // Try alternative vendor structure
    elseif (file_exists(__DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php')) {
        require __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
        require __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
    } else {
        throw new Exception('PHPMailer not found. Please download it from https://github.com/PHPMailer/PHPMailer/releases');
    }
}

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService {
    
    private $config;
    private $mailer;
    private $errors = [];
    
    /**
     * Constructor - Initialize mail service with configuration
     */
    public function __construct() {
        // Load mail configuration
        $this->config = require __DIR__ . '/../config/mail.php';
        
        // Validate configuration
        if (empty($this->config['smtp']['username']) || empty($this->config['smtp']['password'])) {
            throw new Exception('SMTP credentials not configured. Please check your .env file.');
        }
        
        // Initialize PHPMailer
        $this->mailer = new PHPMailer(true);
        $this->setupSMTP();
    }
    
    /**
     * Setup SMTP configuration
     */
    private function setupSMTP() {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host       = $this->config['smtp']['host'];
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = $this->config['smtp']['username'];
            $this->mailer->Password   = $this->config['smtp']['password'];
            $this->mailer->SMTPSecure = $this->config['smtp']['encryption'];
            $this->mailer->Port       = $this->config['smtp']['port'];
            
            // Character set
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';
            
            // Set default FROM
            $this->mailer->setFrom(
                $this->config['from']['email'], 
                $this->config['from']['name']
            );
            
            // Disable debug output (enable for troubleshooting)
            // $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
            
        } catch (Exception $e) {
            throw new Exception('SMTP Setup Failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Send Contact Form Email
     * 
     * @param array $data Contact form data
     * @return array Response with success/error
     */
    public function sendContactEmail($data) {
        try {
            // Validate inputs
            $validation = $this->validateContactData($data);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['message']
                ];
            }
            
            // Reset mailer for new email
            $this->mailer->clearAddresses();
            $this->mailer->clearReplyTos();
            $this->mailer->clearAttachments();
            
            // Recipients
            $this->mailer->addAddress($this->config['admin']['email']);
            
            // Set Reply-To to user's email
            $this->mailer->addReplyTo($data['email'], $data['name']);
            
            // Email subject
            $this->mailer->Subject = 'Contact Form: ' . $data['subject'] . ' - from ' . $data['name'];
            
            // Email body (HTML)
            $this->mailer->isHTML(true);
            $this->mailer->Body = $this->getContactEmailHTML($data);
            $this->mailer->AltBody = $this->getContactEmailText($data);
            
            // Send email
            $this->mailer->send();
            
            return [
                'success' => true,
                'message' => 'Your message has been sent successfully! We will get back to you soon.'
            ];
            
        } catch (Exception $e) {
            error_log('Contact Email Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send message. Please try again later.',
                'debug_error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send Workshop Registration Emails
     * Sends two emails: one to admin, one to user
     * 
     * @param array $data Workshop registration data
     * @return array Response with success/error
     */
    public function sendWorkshopRegistration($data) {
        try {
            // Validate inputs
            $validation = $this->validateWorkshopData($data);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['message']
                ];
            }
            
            // Send admin notification
            $adminSent = $this->sendWorkshopAdminNotification($data);
            
            // Send user confirmation (only if user email is different from admin)
            $userSent = true;
            if (strtolower($data['email']) !== strtolower($this->config['admin']['email'])) {
                $userSent = $this->sendWorkshopUserConfirmation($data);
            }
            
            if ($adminSent && $userSent) {
                return [
                    'success' => true,
                    'message' => 'Registration successful! Check your email for confirmation.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Registration received but email notification failed. We will contact you soon.'
                ];
            }
            
        } catch (Exception $e) {
            error_log('Workshop Registration Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ];
        }
    }
    
    /**
     * Send workshop notification to admin
     */
    private function sendWorkshopAdminNotification($data) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearReplyTos();
            $this->mailer->clearAttachments();
            
            $this->mailer->addAddress($this->config['admin']['email']);
            $this->mailer->addReplyTo($data['email'], $data['name']);
            
            $this->mailer->Subject = 'New Workshop Registration: ' . $data['workshop_title'];
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $this->getWorkshopAdminEmailHTML($data);
            $this->mailer->AltBody = $this->getWorkshopAdminEmailText($data);
            
            $this->mailer->send();
            return true;
            
        } catch (Exception $e) {
            error_log('Admin Workshop Email Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send workshop confirmation to user
     */
    private function sendWorkshopUserConfirmation($data) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearReplyTos();
            $this->mailer->clearAttachments();
            
            $this->mailer->addAddress($data['email'], $data['name']);
            
            $this->mailer->Subject = 'Workshop Registration Confirmed: ' . $data['workshop_title'];
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $this->getWorkshopUserEmailHTML($data);
            $this->mailer->AltBody = $this->getWorkshopUserEmailText($data);
            
            $this->mailer->send();
            return true;
            
        } catch (Exception $e) {
            error_log('User Workshop Email Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate contact form data
     */
    private function validateContactData($data) {
        $required = ['name', 'email', 'subject', 'message'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return [
                    'valid' => false,
                    'message' => ucfirst($field) . ' is required.'
                ];
            }
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'message' => 'Invalid email address.'
            ];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Validate workshop registration data
     */
    private function validateWorkshopData($data) {
        $required = ['name', 'email', 'workshop_title'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return [
                    'valid' => false,
                    'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.'
                ];
            }
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'message' => 'Invalid email address.'
            ];
        }
        
        return ['valid' => true];
    }
    
    /**
     * HTML template for contact email
     */
    private function getContactEmailHTML($data) {
        $name = htmlspecialchars($data['name']);
        $email = htmlspecialchars($data['email']);
        $phone = htmlspecialchars($data['phone'] ?? 'Not provided');
        $subject = htmlspecialchars($data['subject']);
        $message = nl2br(htmlspecialchars($data['message']));
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
                .header { background: #000; color: #fff; padding: 20px; text-align: center; }
                .content { background: #fff; padding: 30px; margin-top: 20px; }
                .field { margin-bottom: 20px; }
                .label { font-weight: bold; color: #555; }
                .value { margin-top: 5px; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>New Contact Form Submission</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='label'>From:</div>
                        <div class='value'>{$name}</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Email:</div>
                        <div class='value'>{$email}</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Phone:</div>
                        <div class='value'>{$phone}</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Subject:</div>
                        <div class='value'>{$subject}</div>
                    </div>
                    < class='field'>
                   >Message:
                        <div class='value'>{$message}</div>
                    </div>
                </div>
                <div class='footer'>
                    <p>This email was sent from the contact form on your website.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Plain text template for contact email
     */
    private function getContactEmailText($data) {
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'] ?? 'Not provided';
        $subject = $data['subject'];
        $message = $data['message'];
        
        return "
NEW CONTACT FORM SUBMISSION

From: {$name}
Email: {$email}
Phone: {$phone}
Subject: {$subject}

Message:
{$message}

---
This email was sent from the contact form on your website.
        ";
    }
    
    /**
     * HTML template for workshop admin notification
     */
    private function getWorkshopAdminEmailHTML($data) {
        $name = htmlspecialchars($data['name']);
        $email = htmlspecialchars($data['email']);
        $contact = htmlspecialchars($data['contact'] ?? 'Not provided');
        $who = htmlspecialchars($data['who'] ?? 'Not provided');
        $message = nl2br(htmlspecialchars($data['message'] ?? 'No message provided'));
        $workshop = htmlspecialchars($data['workshop_title']);
        $workshopDate = htmlspecialchars($data['workshop_date'] ?? 'TBA');
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f4f4; }
                .header { background: #000; color: #fff; padding: 25px; text-align: center; }
                .content { background: #fff; padding: 30px; margin-top: 20px; border-left: 4px solid #000; }
                .workshop-info { background: #f9f9f9; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #555; display: inline-block; width: 120px; }
                .value { display: inline-block; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🎓 New Workshop Registration</h2>
                </div>
                <div class='content'>
                    <div class='workshop-info'>
                        <h3 style='margin-top:0;'>Workshop: {$workshop}</h3>
                        <p style='margin:0; color:#666;'>Date: {$workshopDate}</p>
                    </div>
                    
                    <h4>Participant Details:</h4>
                    <div class='field'>
                        <span class='label'>Name:</span>
                        <span class='value'>{$name}</span>
                    </div>
                    <div class='field'>
                        <span class='label'>Email:</span>
                        <span class='value'>{$email}</span>
                    </div>
                    <div class='field'>
                        <span class='label'>Contact:</span>
                        <span class='value'>{$contact}</span>
                    </div>
                    <div class='field'>
                        <span class='label'>Who are they:</span>
                        <span class='value'>{$who}</span>
                    </div>
                    <div class='field'>
                        <span class='label'>Message:</span>
                        <div style='margin-top:10px; padding:10px; background:#f9f9f9; border-radius:5px;'>
                            {$message}
                        </div>
                    </div>
                </div>
                <div class='footer'>
                    <p>Workshop registration received at " . date('Y-m-d H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Plain text template for workshop admin notification
     */
    private function getWorkshopAdminEmailText($data) {
        $name = $data['name'];
        $email = $data['email'];
        $contact = $data['contact'] ?? 'Not provided';
        $who = $data['who'] ?? 'Not provided';
        $message = $data['message'] ?? 'No message provided';
        $workshop = $data['workshop_title'];
        $workshopDate = $data['workshop_date'] ?? 'TBA';
        
        return "
NEW WORKSHOP REGISTRATION

Workshop: {$workshop}
Date: {$workshopDate}

PARTICIPANT DETAILS:
Name: {$name}
Email: {$email}
Contact: {$contact}
Who are they: {$who}

Message:
{$message}

---
Registration received at " . date('Y-m-d H:i:s') . "
        ";
    }
    
    /**
     * HTML template for workshop user confirmation
     */
    private function getWorkshopUserEmailHTML($data) {
        $name = htmlspecialchars($data['name']);
        $workshop = htmlspecialchars($data['workshop_title']);
        $workshopDate = htmlspecialchars($data['workshop_date'] ?? 'TBA');
        $appName = htmlspecialchars($this->config['app']['name']);
        $appUrl = htmlspecialchars($this->config['app']['url']);
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f4f4; }
                .header { background: #000; color: #fff; padding: 30px; text-align: center; }
                .content { background: #fff; padding: 40px; margin-top: 20px; }
                .workshop-box { background: #f9f9f9; padding: 20px; margin: 20px 0; border-left: 4px solid #000; }
                .button { display: inline-block; padding: 12px 30px; background: #000; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #888; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>✅ Registration Confirmed!</h2>
                </div>
                <div class='content'>
                    <p>Hi {$name},</p>
                    <p>Thank you for registering for our workshop! We're excited to have you join us.</p>
                    
                    <div class='workshop-box'>
                        <h3 style='margin-top:0;'>{$workshop}</h3>
                        <p style='margin:0; font-size:14px; color:#666;'>
                            📅 <strong>Date:</strong> {$workshopDate}
                        </p>
                    </div>
                    
                    <p>You will receive more details about the workshop (including time, location/link, and agenda) via email 24-48 hours before the event.</p>
                    
                    <p><strong>What to expect:</strong></p>
                    <ul>
                        <li>In-depth learning and practical insights</li>
                        <li>Interactive sessions and Q&A</li>
                        <li>Networking opportunities</li>
                    </ul>
                    
                    <p>If you have any questions, feel free to reply to this email.</p>
                    
                    <p style='margin-top:30px;'>Looking forward to seeing you!</p>
                    <p><strong>{$appName} Team</strong></p>
                </div>
                <div class='footer'>
                    <p>{$appName}</p>
                    <p><a href='{$appUrl}' style='color:#888;'>{$appUrl}</a></p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Plain text template for workshop user confirmation
     */
    private function getWorkshopUserEmailText($data) {
        $name = $data['name'];
        $workshop = $data['workshop_title'];
        $workshopDate = $data['workshop_date'] ?? 'TBA';
        $appName = $this->config['app']['name'];
        $appUrl = $this->config['app']['url'];
        
        return "
REGISTRATION CONFIRMED!

Hi {$name},

Thank you for registering for our workshop! We're excited to have you join us.

Workshop: {$workshop}
Date: {$workshopDate}

You will receive more details about the workshop (including time, location/link, and agenda) via email 24-48 hours before the event.

What to expect:
- In-depth learning and practical insights
- Interactive sessions and Q&A
- Networking opportunities

If you have any questions, feel free to reply to this email.

Looking forward to seeing you!

{$appName} Team

---
{$appName}
{$appUrl}
        ";
    }
    
    /**
     * Get all errors
     */
    public function getErrors() {
        return $this->errors;
    }
}
