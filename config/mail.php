<?php
/**
 * Mail Configuration
 * 
 * Loads SMTP settings from .env file
 * Provides secure email configuration for the application
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception('.env file not found. Please create one based on .env.example');
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
            }
        }
    }
}

// Load .env file from project root
$envPath = __DIR__ . '/../.env';
try {
    loadEnv($envPath);
} catch (Exception $e) {
    error_log('Mail Config Error: ' . $e->getMessage());
}

// Helper function to get environment variable
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// Mail configuration array
return [
    'smtp' => [
        'host'     => env('SMTP_HOST', 'smtp.gmail.com'),
        'port'     => env('SMTP_PORT', 587),
        'username' => env('SMTP_USERNAME', ''),
        'password' => env('SMTP_PASSWORD', ''),
        'encryption' => 'tls', // or 'ssl' for port 465
    ],
    'from' => [
        'email' => env('SMTP_FROM_EMAIL', ''),
        'name'  => env('SMTP_FROM_NAME', 'Website'),
    ],
    'admin' => [
        'email' => env('SMTP_ADMIN_EMAIL', ''),
    ],
    'app' => [
        'name' => env('APP_NAME', 'Coaching Management'),
        'url'  => env('APP_URL', 'https://yourwebsite.com'),
    ],
];
