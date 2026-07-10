<?php
/**
 * Student Controller
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Logger.php';

class StudentController {
    private $db;

    public function __construct() {
        Auth::startSession();
        Auth::requireLogin();
        $this->db = $GLOBALS['db'];
    }

    /**
     * Get all students
     */
    public function getAllStudents($page = 1, $perPage = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare(
            "SELECT s.*, c.course_name, d.department_name
             FROM students s
             LEFT JOIN courses c ON s.course_id = c.course_id
             LEFT JOIN departments d ON s.department_id = d.department_id
             WHERE s.is_active = 1
             ORDER BY s.roll_number ASC
             LIMIT ? OFFSET ?"
        );
        
        $stmt->bind_param('ii', $perPage, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get student by ID
     */
    public function getStudentById($studentId) {
        $stmt = $this->db->prepare(
            "SELECT s.*, c.course_name, d.department_name
             FROM students s
             LEFT JOIN courses c ON s.course_id = c.course_id
             LEFT JOIN departments d ON s.department_id = d.department_id
             WHERE s.student_id = ?"
        );
        
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Add new student
     */
    public function addStudent($data) {
        // Sanitize input
        $data = array_map([Security::class, 'sanitizeInput'], $data);

        // Validate required fields
        $required = ['roll_number', 'first_name', 'last_name', 'course_id', 'department_id', 'admission_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => ucfirst($field) . ' is required'];
            }
        }

        // Check if roll number already exists
        $checkStmt = $this->db->prepare("SELECT student_id FROM students WHERE roll_number = ?");
        $checkStmt->bind_param('s', $data['roll_number']);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Roll number already exists'];
        }

        // Insert student
        $stmt = $this->db->prepare(
            "INSERT INTO students (roll_number, first_name, last_name, email, phone, date_of_birth, gender, 
             address, city, state, postal_code, course_id, department_id, admission_date, 
             father_name, father_phone, mother_name, mother_phone)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'ssssssssssssisssss',
            $data['roll_number'],
            $data['first_name'],
            $data['last_name'],
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['date_of_birth'] ?? null,
            $data['gender'] ?? null,
            $data['address'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['postal_code'] ?? null,
            $data['course_id'],
            $data['department_id'],
            $data['admission_date'],
            $data['father_name'] ?? null,
            $data['father_phone'] ?? null,
            $data['mother_name'] ?? null,
            $data['mother_phone'] ?? null
        );

        if ($stmt->execute()) {
            $studentId = $this->db->lastInsertId();
            Logger::log('STUDENT_CREATED', 'Student Management', $studentId, 'New student added: ' . $data['roll_number'], '', '', 'Success');
            return ['success' => true, 'message' => 'Student added successfully', 'student_id' => $studentId];
        }

        return ['success' => false, 'message' => 'Failed to add student'];
    }

    /**
     * Update student
     */
    public function updateStudent($studentId, $data) {
        // Sanitize input
        $data = array_map([Security::class, 'sanitizeInput'], $data);

        $stmt = $this->db->prepare(
            "UPDATE students SET first_name = ?, last_name = ?, email = ?, phone = ?, date_of_birth = ?,
             gender = ?, address = ?, city = ?, state = ?, postal_code = ?, father_name = ?, 
             father_phone = ?, mother_name = ?, mother_phone = ? WHERE student_id = ?"
        );

        $stmt->bind_param(
            'sssssssssssssi',
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['date_of_birth'] ?? null,
            $data['gender'] ?? null,
            $data['address'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['postal_code'] ?? null,
            $data['father_name'] ?? null,
            $data['father_phone'] ?? null,
            $data['mother_name'] ?? null,
            $data['mother_phone'] ?? null,
            $studentId
        );

        if ($stmt->execute()) {
            Logger::log('STUDENT_UPDATED', 'Student Management', $studentId, 'Student record updated', '', '', 'Success');
            return ['success' => true, 'message' => 'Student updated successfully'];
        }

        return ['success' => false, 'message' => 'Failed to update student'];
    }

    /**
     * Delete student (soft delete)
     */
    public function deleteStudent($studentId) {
        $stmt = $this->db->prepare("UPDATE students SET is_active = 0 WHERE student_id = ?");
        $stmt->bind_param('i', $studentId);

        if ($stmt->execute()) {
            Logger::log('STUDENT_DELETED', 'Student Management', $studentId, 'Student marked as inactive', '', '', 'Success');
            return ['success' => true, 'message' => 'Student deleted successfully'];
        }

        return ['success' => false, 'message' => 'Failed to delete student'];
    }

    /**
     * Get total student count
     */
    public function getTotalStudents() {
        $result = $this->db->query("SELECT COUNT(*) as total FROM students WHERE is_active = 1");
        return $result->fetch_assoc()['total'];
    }
}

?>
