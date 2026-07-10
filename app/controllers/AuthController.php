<?php
/**
 * Authentication Controller
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Logger.php';

class AuthController {
    private $db;

    public function __construct() {
        Auth::startSession();
        $this->db = $GLOBALS['db'];
    }

    /**
     * Handle login
     */
    public function login($username, $password) {
        // Validate input
        $username = Security::sanitizeInput($username);
        $password = Security::sanitizeInput($password);

        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Username and password required'];
        }

        // Get user from database
        $stmt = $this->db->prepare(
            "SELECT user_id, username, email, first_name, last_name, password_hash, role, is_active 
             FROM users WHERE username = ? AND is_active = 1 LIMIT 1"
        );
        
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            Logger::log('LOGIN_FAILED', 'Authentication', null, 'Invalid username', '', '', 'Failed');
            return ['success' => false, 'message' => 'Invalid username or password'];
        }

        $user = $result->fetch_assoc();

        // Verify password
        if (!Auth::verifyPassword($password, $user['password_hash'])) {
            Logger::log('LOGIN_FAILED', 'Authentication', null, 'Invalid password for user: ' . $username, '', '', 'Failed');
            return ['success' => false, 'message' => 'Invalid username or password'];
        }

        // Update last login
        $updateStmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
        $updateStmt->bind_param('i', $user['user_id']);
        $updateStmt->execute();

        // Set session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user'] = [
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'role' => $user['role'],
            'full_name' => $user['first_name'] . ' ' . $user['last_name']
        ];

        Logger::log('LOGIN_SUCCESS', 'Authentication', $user['user_id'], 'User logged in', '', '', 'Success');

        return ['success' => true, 'message' => 'Login successful', 'user' => $user['role']];
    }

    /**
     * Handle logout
     */
    public function logout() {
        $userId = Auth::getCurrentUserId();
        if ($userId) {
            Logger::log('LOGOUT', 'Authentication', $userId, 'User logged out', '', '', 'Success');
        }
        Auth::logout();
        return ['success' => true, 'message' => 'Logout successful'];
    }

    /**
     * Register user (Admin only)
     */
    public function register($username, $email, $password, $firstName, $lastName, $phone, $role, $departmentId = null) {
        // Validate input
        $username = Security::sanitizeInput($username);
        $email = Security::sanitizeInput($email);
        $firstName = Security::sanitizeInput($firstName);
        $lastName = Security::sanitizeInput($lastName);
        $phone = Security::sanitizeInput($phone);

        // Validation checks
        if (empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if (!Security::validateEmail($email)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        if (!Security::validatePasswordStrength($password)) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters with uppercase, number, and special character'];
        }

        if (!empty($phone) && !Security::validatePhone($phone)) {
            return ['success' => false, 'message' => 'Invalid phone number'];
        }

        // Check if user already exists
        $checkStmt = $this->db->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $checkStmt->bind_param('ss', $username, $email);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }

        // Hash password
        $passwordHash = Auth::hashPassword($password);

        // Insert user
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password_hash, first_name, last_name, phone, role, department_id, is_active) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)"
        );
        
        $stmt->bind_param(
            'ssssssssi',
            $username,
            $email,
            $passwordHash,
            $firstName,
            $lastName,
            $phone,
            $role,
            $departmentId
        );

        if ($stmt->execute()) {
            $userId = $this->db->lastInsertId();
            Logger::log('USER_CREATED', 'User Management', $userId, 'New user registered: ' . $username, '', '', 'Success');
            return ['success' => true, 'message' => 'User registered successfully', 'user_id' => $userId];
        } else {
            Logger::log('USER_CREATION_FAILED', 'User Management', null, 'Failed to register user: ' . $username, '', '', 'Failed');
            return ['success' => false, 'message' => 'Failed to register user'];
        }
    }

    /**
     * Change password
     */
    public function changePassword($userId, $oldPassword, $newPassword, $confirmPassword) {
        // Validate input
        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if ($newPassword !== $confirmPassword) {
            return ['success' => false, 'message' => 'New passwords do not match'];
        }

        if (!Security::validatePasswordStrength($newPassword)) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters with uppercase, number, and special character'];
        }

        // Get user
        $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $user = $result->fetch_assoc();

        // Verify old password
        if (!Auth::verifyPassword($oldPassword, $user['password_hash'])) {
            Logger::log('PASSWORD_CHANGE_FAILED', 'Authentication', $userId, 'Invalid old password', '', '', 'Failed');
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        // Update password
        $newHash = Auth::hashPassword($newPassword);
        $updateStmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $updateStmt->bind_param('si', $newHash, $userId);

        if ($updateStmt->execute()) {
            Logger::log('PASSWORD_CHANGED', 'Authentication', $userId, 'Password changed successfully', '', '', 'Success');
            return ['success' => true, 'message' => 'Password changed successfully'];
        }

        return ['success' => false, 'message' => 'Failed to change password'];
    }
}

?>
