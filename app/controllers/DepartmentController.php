<?php
/**
 * Department Controller
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Logger.php';

class DepartmentController {
    private $db;

    public function __construct() {
        Auth::startSession();
        Auth::requireLogin();
        $this->db = $GLOBALS['db'];
    }

    /**
     * Get all departments
     */
    public function getAllDepartments() {
        $result = $this->db->query(
            "SELECT * FROM departments WHERE is_active = 1 ORDER BY department_name ASC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get department by ID
     */
    public function getDepartmentById($departmentId) {
        $stmt = $this->db->prepare("SELECT * FROM departments WHERE department_id = ?");
        $stmt->bind_param('i', $departmentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Add new department
     */
    public function addDepartment($data) {
        // Sanitize input
        $data = array_map([Security::class, 'sanitizeInput'], $data);

        // Validate required fields
        if (empty($data['department_name'])) {
            return ['success' => false, 'message' => 'Department name is required'];
        }

        // Check if department already exists
        $checkStmt = $this->db->prepare("SELECT department_id FROM departments WHERE department_name = ?");
        $checkStmt->bind_param('s', $data['department_name']);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Department already exists'];
        }

        // Insert department
        $stmt = $this->db->prepare(
            "INSERT INTO departments (department_name, description, head_of_department, phone, email)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'sssss',
            $data['department_name'],
            $data['description'] ?? null,
            $data['head_of_department'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null
        );

        if ($stmt->execute()) {
            $departmentId = $this->db->lastInsertId();
            Logger::log('DEPARTMENT_CREATED', 'Department Management', $departmentId, 'New department added: ' . $data['department_name'], '', '', 'Success');
            return ['success' => true, 'message' => 'Department added successfully', 'department_id' => $departmentId];
        }

        return ['success' => false, 'message' => 'Failed to add department'];
    }

    /**
     * Update department
     */
    public function updateDepartment($departmentId, $data) {
        // Sanitize input
        $data = array_map([Security::class, 'sanitizeInput'], $data);

        $stmt = $this->db->prepare(
            "UPDATE departments SET department_name = ?, description = ?, head_of_department = ?, phone = ?, email = ? WHERE department_id = ?"
        );

        $stmt->bind_param(
            'sssssi',
            $data['department_name'] ?? '',
            $data['description'] ?? null,
            $data['head_of_department'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $departmentId
        );

        if ($stmt->execute()) {
            Logger::log('DEPARTMENT_UPDATED', 'Department Management', $departmentId, 'Department updated', '', '', 'Success');
            return ['success' => true, 'message' => 'Department updated successfully'];
        }

        return ['success' => false, 'message' => 'Failed to update department'];
    }

    /**
     * Delete department (soft delete)
     */
    public function deleteDepartment($departmentId) {
        $stmt = $this->db->prepare("UPDATE departments SET is_active = 0 WHERE department_id = ?");
        $stmt->bind_param('i', $departmentId);

        if ($stmt->execute()) {
            Logger::log('DEPARTMENT_DELETED', 'Department Management', $departmentId, 'Department deleted', '', '', 'Success');
            return ['success' => true, 'message' => 'Department deleted successfully'];
        }

        return ['success' => false, 'message' => 'Failed to delete department'];
    }

    /**
     * Get total departments
     */
    public function getTotalDepartments() {
        $result = $this->db->query("SELECT COUNT(*) as total FROM departments WHERE is_active = 1");
        return $result->fetch_assoc()['total'];
    }
}

?>
