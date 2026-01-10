<?php
/**
 * JsonResponse - Production-Safe JSON API Response Handler
 * 
 * Ensures ALWAYS valid JSON output, even during fatal errors
 * Catches all errors and converts them to structured JSON responses
 */

class JsonResponse {
    
    private static $initialized = false;
    private static $debugMode = false;
    
    /**
     * Initialize error handlers to catch ALL errors
     * Call this at the very start of your API file
     */
    public static function init($debugMode = false) {
        if (self::$initialized) {
            return;
        }
        
        self::$debugMode = $debugMode;
        self::$initialized = true;
        
        // Start output buffering to catch any accidental output
        ob_start();
        
        // Set error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        
        // Set custom error handler
        set_error_handler([__CLASS__, 'errorHandler']);
        
        // Set custom exception handler
        set_exception_handler([__CLASS__, 'exceptionHandler']);
        
        // Set shutdown function to catch fatal errors
        register_shutdown_function([__CLASS__, 'shutdownHandler']);
        
        // Set JSON headers immediately
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');
        }
    }
    
    /**
     * Send success response
     */
    public static function success($message, $data = []) {
        self::send([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], 200);
    }
    
    /**
     * Send error response
     */
    public static function error($message, $code = 400, $debugInfo = []) {
        $response = [
            'success' => false,
            'message' => $message,
            'error_code' => $code
        ];
        
        // Include debug info only if debug mode enabled
        if (self::$debugMode && !empty($debugInfo)) {
            $response['debug'] = $debugInfo;
        }
        
        self::send($response, $code);
    }
    
    /**
     * Send JSON response and exit
     */
    private static function send($data, $httpCode = 200) {
        // Clear any previous output
        if (ob_get_length()) {
            ob_clean();
        }
        
        // Set HTTP response code
        http_response_code($httpCode);
        
        // Ensure headers are set
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8');
        }
        
        // Output JSON
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Handle PHP errors
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline) {
        $errorTypes = [
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];
        
        $errorType = $errorTypes[$errno] ?? 'Unknown Error';
        
        // Log the error
        error_log("[$errorType] $errstr in $errfile on line $errline");
        
        // For non-fatal errors, just log and continue
        if (!in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
            return true;
        }
        
        // For fatal errors, send JSON error response
        self::error(
            'A server error occurred. Please try again later.',
            500,
            [
                'type' => $errorType,
                'message' => $errstr,
                'file' => basename($errfile),
                'line' => $errline
            ]
        );
        
        return true;
    }
    
    /**
     * Handle uncaught exceptions
     */
    public static function exceptionHandler($exception) {
        // Log the exception
        error_log('Uncaught Exception: ' . $exception->getMessage());
        error_log('File: ' . $exception->getFile() . ' Line: ' . $exception->getLine());
        error_log('Trace: ' . $exception->getTraceAsString());
        
        // Send JSON error response
        self::error(
            'An unexpected error occurred. Please try again later.',
            500,
            [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => basename($exception->getFile()),
                'line' => $exception->getLine()
            ]
        );
    }
    
    /**
     * Handle fatal errors on shutdown
     */
    public static function shutdownHandler() {
        $error = error_get_last();
        
        // Check if it was a fatal error
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            // Clear any buffered output
            if (ob_get_length()) {
                ob_clean();
            }
            
            // Log the fatal error
            error_log("Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}");
            
            // Send JSON error response
            self::error(
                'A critical server error occurred. Please contact support.',
                500,
                [
                    'type' => 'Fatal Error',
                    'message' => $error['message'],
                    'file' => basename($error['file']),
                    'line' => $error['line']
                ]
            );
        }
    }
    
    /**
     * Validate required POST fields
     */
    public static function validatePost($requiredFields) {
        $missing = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            self::error(
                'Missing required fields: ' . implode(', ', $missing),
                400,
                ['missing_fields' => $missing]
            );
        }
        
        return true;
    }
    
    /**
     * Check if request method is allowed
     */
    public static function requireMethod($method = 'POST') {
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
            self::error(
                "Invalid request method. Only $method is allowed.",
                405,
                ['received' => $_SERVER['REQUEST_METHOD'], 'expected' => $method]
            );
        }
    }
    
    /**
     * Safe file require - throws descriptive error if file missing
     */
    public static function requireFile($filePath, $description = 'Required file') {
        if (!file_exists($filePath)) {
            throw new Exception("$description not found: " . basename($filePath));
        }
        
        require_once $filePath;
        return true;
    }
}
