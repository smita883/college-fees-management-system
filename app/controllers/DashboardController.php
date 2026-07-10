<?php
/**
 * Dashboard Controller
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';

class DashboardController {
    private $db;

    public function __construct() {
        Auth::startSession();
        Auth::requireLogin();
        $this->db = $GLOBALS['db'];
    }

    /**
     * Get dashboard statistics
     */
    public function getStatistics() {
        $stats = [];

        // Total students
        $result = $this->db->query("SELECT COUNT(*) as total FROM students WHERE is_active = 1");
        $stats['total_students'] = $result->fetch_assoc()['total'];

        // Total courses
        $result = $this->db->query("SELECT COUNT(*) as total FROM courses WHERE is_active = 1");
        $stats['total_courses'] = $result->fetch_assoc()['total'];

        // Total departments
        $result = $this->db->query("SELECT COUNT(*) as total FROM departments WHERE is_active = 1");
        $stats['total_departments'] = $result->fetch_assoc()['total'];

        // Total users
        $result = $this->db->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
        $stats['total_users'] = $result->fetch_assoc()['total'];

        // Total fee collected
        $result = $this->db->query(
            "SELECT SUM(amount_paid) as total FROM fee_payments WHERE payment_status = 'Completed'"
        );
        $stats['total_fee_collected'] = $result->fetch_assoc()['total'] ?? 0;

        // Pending payments
        $result = $this->db->query(
            "SELECT COUNT(*) as total FROM fee_payments WHERE payment_status = 'Pending'"
        );
        $stats['pending_payments'] = $result->fetch_assoc()['total'];

        return $stats;
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities($limit = 10) {
        $stmt = $this->db->prepare(
            "SELECT a.log_id, a.user_id, a.action, a.module, a.description, a.created_at, u.username, u.first_name, u.last_name
             FROM activity_logs a
             LEFT JOIN users u ON a.user_id = u.user_id
             ORDER BY a.created_at DESC
             LIMIT ?"
        );
        
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get fee collection summary
     */
    public function getFeeCollectionSummary() {
        $result = $this->db->query(
            "SELECT 
                SUM(CASE WHEN payment_status = 'Completed' THEN amount_paid ELSE 0 END) as collected,
                SUM(CASE WHEN payment_status = 'Pending' THEN amount_paid ELSE 0 END) as pending,
                SUM(CASE WHEN payment_status = 'Failed' THEN amount_paid ELSE 0 END) as failed
             FROM fee_payments"
        );
        return $result->fetch_assoc();
    }
}

?>
