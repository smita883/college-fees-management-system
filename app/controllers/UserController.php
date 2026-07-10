<?php
/**
 * User Management Controller
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Logger.php';

class UserController {
    private $db;

    public function __construct() {
        Auth::startSession();
        Auth::requireLogin();
        Auth::requireRole([ROLE_ADMIN]);
        $this->db = $GLOBALS['db'];
    }

    /**
     * Get all users
     */
    public function getAllUsers($page = 1, $perPage = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare(
            "SELECT u.*, d.department_name FROM users u
             LEFT JOIN departments d ON u.department_id = d.department_id
             ORDER BY u.created_at DESC
             LIMIT ? OFFSET ?"
        );
        
        $stmt->bind_param('ii', $perPage, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get user by ID
     */
    public function getUserById($userId) {
        $stmt = $this->db->prepare(
            "SELECT u.*, d.department_name FROM users u
             LEFT JOIN departments d ON u.department_id = d.department_id
             WHERE u.user_id = ?"
        );
        
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update user
     */
    public function updateUser($userId, $data) {
        // Sanitize input
        $data = array_map([Security::class, 'sanitizeInput'], $data);

        $stmt = $this->db->prepare(
            "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, role = ?, department_id = ? WHERE user_id = ?"
        );

        $stmt->bind_param(
            'sssssi',
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['role'] ?? ROLE_STAFF,
            $data['department_id'] ?? null,
            $userId
        );

        if ($stmt->execute()) {
            Logger::log('USER_UPDATED', 'User Management', $userId, 'User details updated', '', '', 'Success');
            return ['success' => true, 'message' => 'User updated successfully'];
        }

        return ['success' => false, 'message' => 'Failed to update user'];
    }

    /**
     * Deactivate user
     */
    public function deactivateUser($userId) {
        if ($userId == Auth::getCurrentUserId()) {
            return ['success' => false, 'message' => 'Cannot deactivate your own account'];
        }

        $stmt = $this->db->prepare("UPDATE users SET is_active = 0 WHERE user_id = ?");
        $stmt->bind_param('i', $userId);

        if ($stmt->execute()) {
            Logger::log('USER_DEACTIVATED', 'User Management', $userId, 'User deactivated', '', '', 'Success');
            return ['success' => true, 'message' => 'User deactivated successfully'];
        }

        return ['success' => false, 'message' => 'Failed to deactivate user'];
    }

    /**
     * Activate user
     */
    public function activateUser($userId) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = 1 WHERE user_id = ?");
        $stmt->bind_param('i', $userId);

        if ($stmt->execute()) {
            Logger::log('USER_ACTIVATED', 'User Management', $userId, 'User activated', '', '', 'Success');
            return ['success' => true, 'message' => 'User activated successfully'];
        }

        return ['success' => false, 'message' => 'Failed to activate user'];
    }

    /**
     * Reset user password (Admin only)
     */
    public function resetPassword($userId, $newPassword) {
        // Validate password
        if (!Security::validatePasswordStrength($newPassword)) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters with uppercase, number, and special character'];
        }

        $passwordHash = Auth::hashPassword($newPassword);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $stmt->bind_param('si', $passwordHash, $userId);

        if ($stmt->execute()) {
            Logger::log('PASSWORD_RESET', 'User Management', $userId, 'Password reset by admin', '', '', 'Success');
            return ['success' => true, 'message' => 'Password reset successfully'];
        }

        return ['success' => false, 'message' => 'Failed to reset password'];
    }

    /**
     * Get total users
     */
    public function getTotalUsers() {
        $result = $this->db->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
        return $result->fetch_assoc()['total'];
    }
}

?>
