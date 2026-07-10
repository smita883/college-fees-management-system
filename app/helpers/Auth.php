<?php
/**
 * Authentication Helper
 */

class Auth {
    private static $db = null;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        self::$db = $GLOBALS['db'];
    }

    /**
     * Start secure session
     */
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => SESSION_TIMEOUT,
                'path' => '/',
                'secure' => SESSION_SECURE_COOKIE,
                'httponly' => SESSION_HTTP_ONLY,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get current user
     */
    public static function getCurrentUser() {
        self::startSession();
        if (!self::isLoggedIn()) {
            return null;
        }
        return $_SESSION['user'] ?? null;
    }

    /**
     * Get current user ID
     */
    public static function getCurrentUserId() {
        self::startSession();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user role
     */
    public static function getCurrentUserRole() {
        self::startSession();
        return $_SESSION['role'] ?? null;
    }

    /**
     * Check user permission
     */
    public static function hasRole($role) {
        $userRole = self::getCurrentUserRole();
        if (is_array($role)) {
            return in_array($userRole, $role);
        }
        return $userRole === $role;
    }

    /**
     * Redirect to login if not authenticated
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . APP_URL . '/login.php');
            exit();
        }
    }

    /**
     * Require specific role
     */
    public static function requireRole($role) {
        self::requireLogin();
        if (!self::hasRole($role)) {
            header('Location: ' . APP_URL . '/unauthorized.php');
            exit();
        }
    }

    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        self::startSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        self::startSession();
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Hash password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => 12
        ]);
    }

    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Logout user
     */
    public static function logout() {
        self::startSession();
        session_destroy();
        setcookie('PHPSESSID', '', time() - 3600, '/');
    }
}

?>
