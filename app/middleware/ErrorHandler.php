<?php
/**
 * Error Handler Middleware
 */

class ErrorHandler {
    public static function handleError($errno, $errstr, $errfile, $errline) {
        $errorTypes = [
            E_ERROR => 'Fatal Error',
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
        ];

        $errorType = $errorTypes[$errno] ?? 'Unknown Error';
        
        $message = "[$errorType] $errstr in $errfile on line $errline";
        
        // Log error
        error_log($message);
        
        // Display error in development
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            echo "<pre><strong>Error:</strong> $message</pre>";
        }
        
        return true;
    }

    public static function handleException($exception) {
        $message = "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
        error_log($message);
        
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            echo "<pre>$message</pre>";
        } else {
            http_response_code(500);
            echo "An error occurred. Please contact the administrator.";
        }
    }

    public static function register() {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
    }
}

?>
