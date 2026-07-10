<?php
/**
 * Request Validation Middleware
 */

class RequestValidator {
    /**
     * Validate CSRF token
     */
    public static function validateCSRF() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            
            if (!Auth::verifyCSRFToken($token)) {
                http_response_code(403);
                die('CSRF token validation failed');
            }
        }
    }

    /**
     * Validate request method
     */
    public static function validateMethod($allowedMethods = ['POST', 'GET']) {
        if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
            http_response_code(405);
            die('Method not allowed');
        }
    }

    /**
     * Validate required fields
     */
    public static function validateRequired($fields, $data) {
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                return "$field is required";
            }
        }
        return null;
    }
}

?>
