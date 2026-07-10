<?php
/**
 * CORS Middleware
 */

class CORSMiddleware {
    public static function handle() {
        header('Access-Control-Allow-Origin: ' . (ALLOWED_ORIGINS ?? '*'));
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Credentials: true');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
}

?>
