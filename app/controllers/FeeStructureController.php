<?php
/**
 * Fee Structure Controller
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Logger.php';

class FeeStructureController {
    private $db;

    public function __construct() {
        Auth::startSession();
        Auth::requireLogin();
        $this->db = $GLOBALS['db'];
    }

    /**
     * Get all fee structures
     */
    public function getAllFeeStructures($academicYear = null) {
        if ($academicYear !== null) {
            $stmt = $this->db->prepare(
                "SELECT fs.*, c.course_name, d.department_name 
                 FROM fee_structure fs
                 LEFT JOIN courses c ON fs.course_id = c.course_id
                 LEFT JOIN departments d ON fs.department_id = d.department_id
                 WHERE fs.is_active = 1 AND fs.academic_year = ?
                 ORDER BY fs.fee_name ASC"
            );
            $stmt->bind_param('s', $academicYear);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        $result = $this->db->query(
            "SELECT fs.*, c.course_name, d.department_name 
             FROM fee_structure fs
             LEFT JOIN courses c ON fs.course_id = c.course_id
             LEFT JOIN departments d ON fs.department_id = d.department_id
             WHERE fs.is_active = 1
             ORDER BY fs.academic_year DESC, fs.fee_name ASC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get fee structure by ID
     */
    public function getFeeStructureById($feeId) {
        $stmt = $this->db->prepare(
            "SELECT fs.*, c.course_name, d.department_name 
             FROM fee_structure fs
             LEFT JOIN courses c ON fs.course_id = c.course_id
             LEFT JOIN departments d ON fs.department_id = d.department_id
             WHERE fs.fee_id = ?"
        );
        
        $stmt->bind_param('i', $feeId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Add new fee structure
     */
    public function addFeeStructure($data) {
        // Sanitize input
        $data = array_map([Security::class, 'sanitizeInput'], $data);

        // Validate required fields
        if (empty($data['fee_name']) || empty($data['amount'])) {
            return ['success' => false, 'message' => 'Fee name and amount are required'];
        }

        // Insert fee structure
        $stmt = $this->db->prepare(
            "INSERT INTO fee_structure (fee_name, course_id, department_id, academic_year, amount, description)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'ssssds',
            $data['fee_name'],
            $data['course_id'] ?? null,
            $data['department_id'] ?? null,
            $data['academic_year'] ?? null,
            $data['amount'],
            $data['description'] ?? null
        );

        if ($stmt->execute()) {
            $feeId = $this->db->lastInsertId();
            Logger::log('FEE_STRUCTURE_CREATED', 'Fee Structure', $feeId, 'New fee structure added: ' . $data['fee_name'], '', '', 'Success');
            return ['success' => true, 'message' => 'Fee structure added successfully', 'fee_id' => $feeId];
        }

        return ['success' => false, 'message' => 'Failed to add fee structure'];
    }

    /**
     * Update fee structure
     */
    public function updateFeeStructure($feeId, $data) {
        // Sanitize input
        $data = array_map([Security::class, 'sanitizeInput'], $data);

        $stmt = $this->db->prepare(
            "UPDATE fee_structure SET fee_name = ?, course_id = ?, department_id = ?, academic_year = ?, amount = ?, description = ? WHERE fee_id = ?"
        );

        $stmt->bind_param(
            'sssssi',
            $data['fee_name'] ?? '',
            $data['course_id'] ?? null,
            $data['department_id'] ?? null,
            $data['academic_year'] ?? null,
            $data['amount'] ?? 0,
            $data['description'] ?? null,
            $feeId
        );

        if ($stmt->execute()) {
            Logger::log('FEE_STRUCTURE_UPDATED', 'Fee Structure', $feeId, 'Fee structure updated', '', '', 'Success');
            return ['success' => true, 'message' => 'Fee structure updated successfully'];
        }

        return ['success' => false, 'message' => 'Failed to update fee structure'];
    }

    /**
     * Delete fee structure (soft delete)
     */
    public function deleteFeeStructure($feeId) {
        $stmt = $this->db->prepare("UPDATE fee_structure SET is_active = 0 WHERE fee_id = ?");
        $stmt->bind_param('i', $feeId);

        if ($stmt->execute()) {
            Logger::log('FEE_STRUCTURE_DELETED', 'Fee Structure', $feeId, 'Fee structure deleted', '', '', 'Success');
            return ['success' => true, 'message' => 'Fee structure deleted successfully'];
        }

        return ['success' => false, 'message' => 'Failed to delete fee structure'];
    }

    /**
     * Get fees for course
     */
    public function getFeesByCourseDepartment($courseId, $departmentId, $academicYear) {
        $stmt = $this->db->prepare(
            "SELECT * FROM fee_structure 
             WHERE is_active = 1 AND course_id = ? AND academic_year = ?"
        );
        
        $stmt->bind_param('is', $courseId, $academicYear);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

?>
