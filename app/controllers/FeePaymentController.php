<?php
/**
 * Fee Payment (Fee Collection) Controller
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Logger.php';

class FeePaymentController {
    private $db;

    public function __construct() {
        Auth::startSession();
        Auth::requireLogin();
        $this->db = $GLOBALS['db'];
    }

    /**
     * Get all fee payments
     */
    public function getAllPayments($page = 1, $perPage = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT fp.*, s.roll_number, s.first_name, s.last_name, fs.fee_name, u.username 
                  FROM fee_payments fp
                  LEFT JOIN students s ON fp.student_id = s.student_id
                  LEFT JOIN fee_structure fs ON fp.fee_id = fs.fee_id
                  LEFT JOIN users u ON fp.created_by = u.user_id
                  WHERE 1=1";
        $types = '';
        $params = [];

        if (!empty($filters['payment_status'])) {
            $query .= " AND fp.payment_status = ?";
            $types .= 's';
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['student_id'])) {
            $query .= " AND fp.student_id = ?";
            $types .= 'i';
            $params[] = $filters['student_id'];
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query .= " AND fp.payment_date BETWEEN ? AND ?";
            $types .= 'ss';
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
        }

        $query .= " ORDER BY fp.payment_date DESC LIMIT ? OFFSET ?";
        $types .= 'ii';
        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get payment by ID
     */
    public function getPaymentById($paymentId) {
        $stmt = $this->db->prepare(
            "SELECT fp.*, s.roll_number, s.first_name, s.last_name, fs.fee_name, u.username 
             FROM fee_payments fp
             LEFT JOIN students s ON fp.student_id = s.student_id
             LEFT JOIN fee_structure fs ON fp.fee_id = fs.fee_id
             LEFT JOIN users u ON fp.created_by = u.user_id
             WHERE fp.payment_id = ?"
        );
        
        $stmt->bind_param('i', $paymentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Record fee payment
     */
    public function recordPayment($data) {
        // Sanitize input
        $data = array_map([Security::class, 'sanitizeInput'], $data);

        // Validate required fields
        $required = ['student_id', 'fee_id', 'amount_paid', 'payment_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'];
            }
        }

        $userId = Auth::getCurrentUserId();

        // Insert payment
        $stmt = $this->db->prepare(
            "INSERT INTO fee_payments (student_id, fee_id, amount_paid, payment_date, payment_method, transaction_id, reference_number, paid_by, payment_status, remarks, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'iidssssssi',
            $data['student_id'],
            $data['fee_id'],
            $data['amount_paid'],
            $data['payment_date'],
            $data['payment_method'] ?? 'Cash',
            $data['transaction_id'] ?? null,
            $data['reference_number'] ?? null,
            $data['paid_by'] ?? null,
            $data['payment_status'] ?? 'Completed',
            $data['remarks'] ?? null,
            $userId
        );

        if ($stmt->execute()) {
            $paymentId = $this->db->lastInsertId();
            Logger::log('PAYMENT_RECORDED', 'Fee Collection', $paymentId, 'Fee payment recorded for student ID: ' . $data['student_id'], '', '', 'Success');
            return ['success' => true, 'message' => 'Payment recorded successfully', 'payment_id' => $paymentId];
        }

        return ['success' => false, 'message' => 'Failed to record payment'];
    }

    /**
     * Update payment
     */
    public function updatePayment($paymentId, $data) {
        // Sanitize input
        $data = array_map([Security::class, 'sanitizeInput'], $data);

        $stmt = $this->db->prepare(
            "UPDATE fee_payments SET amount_paid = ?, payment_date = ?, payment_method = ?, transaction_id = ?, reference_number = ?, paid_by = ?, payment_status = ?, remarks = ? WHERE payment_id = ?"
        );

        $stmt->bind_param(
            'dssssssi',
            $data['amount_paid'] ?? 0,
            $data['payment_date'] ?? null,
            $data['payment_method'] ?? 'Cash',
            $data['transaction_id'] ?? null,
            $data['reference_number'] ?? null,
            $data['paid_by'] ?? null,
            $data['payment_status'] ?? 'Completed',
            $data['remarks'] ?? null,
            $paymentId
        );

        if ($stmt->execute()) {
            Logger::log('PAYMENT_UPDATED', 'Fee Collection', $paymentId, 'Payment updated', '', '', 'Success');
            return ['success' => true, 'message' => 'Payment updated successfully'];
        }

        return ['success' => false, 'message' => 'Failed to update payment'];
    }

    /**
     * Get student payment history
     */
    public function getStudentPaymentHistory($studentId) {
        $stmt = $this->db->prepare(
            "SELECT fp.*, fs.fee_name, fs.amount 
             FROM fee_payments fp
             LEFT JOIN fee_structure fs ON fp.fee_id = fs.fee_id
             WHERE fp.student_id = ?
             ORDER BY fp.payment_date DESC"
        );
        
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get payment summary by status
     */
    public function getPaymentSummary() {
        $result = $this->db->query(
            "SELECT 
                payment_status,
                COUNT(*) as count,
                SUM(amount_paid) as total_amount
             FROM fee_payments
             GROUP BY payment_status"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get total fees collected
     */
    public function getTotalCollected() {
        $result = $this->db->query(
            "SELECT SUM(amount_paid) as total FROM fee_payments WHERE payment_status = 'Completed'"
        );
        return $result->fetch_assoc()['total'] ?? 0;
    }
}

?>
