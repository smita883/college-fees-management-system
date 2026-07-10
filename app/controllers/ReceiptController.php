<?php
/**
 * Receipt Management Controller
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Logger.php';

class ReceiptController {
    private $db;

    public function __construct() {
        Auth::startSession();
        Auth::requireLogin();
        $this->db = $GLOBALS['db'];
    }

    /**
     * Generate receipt number
     */
    private function generateReceiptNumber() {
        $prefix = 'RCP';
        $date = date('Ymd');
        
        // Get last receipt for today
        $stmt = $this->db->prepare(
            "SELECT MAX(CAST(SUBSTRING(receipt_number, 12) AS UNSIGNED)) as last_num 
             FROM receipts WHERE receipt_number LIKE ?"
        );
        $pattern = $prefix . $date . '%';
        $stmt->bind_param('s', $pattern);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        $nextNum = ($result['last_num'] ?? 0) + 1;
        return $prefix . $date . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get all receipts
     */
    public function getAllReceipts($page = 1, $perPage = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare(
            "SELECT r.*, s.roll_number, s.first_name, s.last_name, u.username 
             FROM receipts r
             LEFT JOIN students s ON r.student_id = s.student_id
             LEFT JOIN users u ON r.issued_by = u.user_id
             ORDER BY r.receipt_date DESC
             LIMIT ? OFFSET ?"
        );
        
        $stmt->bind_param('ii', $perPage, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get receipt by ID
     */
    public function getReceiptById($receiptId) {
        $stmt = $this->db->prepare(
            "SELECT r.*, s.roll_number, s.first_name, s.last_name, s.email, s.phone, 
                    fp.amount_paid, fp.payment_method, fp.payment_date, fs.fee_name, u.first_name as issued_by_fname, u.last_name as issued_by_lname
             FROM receipts r
             LEFT JOIN students s ON r.student_id = s.student_id
             LEFT JOIN fee_payments fp ON r.payment_id = fp.payment_id
             LEFT JOIN fee_structure fs ON fp.fee_id = fs.fee_id
             LEFT JOIN users u ON r.issued_by = u.user_id
             WHERE r.receipt_id = ?"
        );
        
        $stmt->bind_param('i', $receiptId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Issue receipt
     */
    public function issueReceipt($paymentId) {
        // Check if receipt already exists for this payment
        $checkStmt = $this->db->prepare("SELECT receipt_id FROM receipts WHERE payment_id = ?");
        $checkStmt->bind_param('i', $paymentId);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Receipt already issued for this payment'];
        }

        // Get payment details
        $paymentStmt = $this->db->prepare(
            "SELECT fp.student_id, fp.amount_paid FROM fee_payments fp WHERE fp.payment_id = ?"
        );
        $paymentStmt->bind_param('i', $paymentId);
        $paymentStmt->execute();
        $payment = $paymentStmt->get_result()->fetch_assoc();

        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found'];
        }

        $receiptNumber = $this->generateReceiptNumber();
        $userId = Auth::getCurrentUserId();
        $receiptDate = date('Y-m-d');

        // Insert receipt
        $stmt = $this->db->prepare(
            "INSERT INTO receipts (receipt_number, payment_id, student_id, total_amount, receipt_date, issued_by, receipt_status)
             VALUES (?, ?, ?, ?, ?, ?, 'Issued')"
        );

        $stmt->bind_param(
            'siidsi',
            $receiptNumber,
            $paymentId,
            $payment['student_id'],
            $payment['amount_paid'],
            $receiptDate,
            $userId
        );

        if ($stmt->execute()) {
            $receiptId = $this->db->lastInsertId();
            Logger::log('RECEIPT_ISSUED', 'Receipt Management', $receiptId, 'Receipt issued: ' . $receiptNumber, '', '', 'Success');
            return ['success' => true, 'message' => 'Receipt issued successfully', 'receipt_id' => $receiptId, 'receipt_number' => $receiptNumber];
        }

        return ['success' => false, 'message' => 'Failed to issue receipt'];
    }

    /**
     * Get receipt by number
     */
    public function getReceiptByNumber($receiptNumber) {
        $stmt = $this->db->prepare(
            "SELECT r.*, s.roll_number, s.first_name, s.last_name, s.email, s.phone, 
                    fp.amount_paid, fp.payment_method, fp.payment_date, fs.fee_name
             FROM receipts r
             LEFT JOIN students s ON r.student_id = s.student_id
             LEFT JOIN fee_payments fp ON r.payment_id = fp.payment_id
             LEFT JOIN fee_structure fs ON fp.fee_id = fs.fee_id
             WHERE r.receipt_number = ?"
        );
        
        $stmt->bind_param('s', $receiptNumber);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Cancel receipt
     */
    public function cancelReceipt($receiptId, $reason = '') {
        $stmt = $this->db->prepare(
            "UPDATE receipts SET receipt_status = 'Cancelled', remarks = ? WHERE receipt_id = ?"
        );
        
        $stmt->bind_param('si', $reason, $receiptId);

        if ($stmt->execute()) {
            Logger::log('RECEIPT_CANCELLED', 'Receipt Management', $receiptId, 'Receipt cancelled: ' . $reason, '', '', 'Success');
            return ['success' => true, 'message' => 'Receipt cancelled successfully'];
        }

        return ['success' => false, 'message' => 'Failed to cancel receipt'];
    }

    /**
     * Get receipts for student
     */
    public function getStudentReceipts($studentId) {
        $stmt = $this->db->prepare(
            "SELECT r.*, fp.amount_paid, fs.fee_name, fp.payment_date
             FROM receipts r
             LEFT JOIN fee_payments fp ON r.payment_id = fp.payment_id
             LEFT JOIN fee_structure fs ON fp.fee_id = fs.fee_id
             WHERE r.student_id = ?
             ORDER BY r.receipt_date DESC"
        );
        
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

?>
