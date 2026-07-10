<?php
/**
 * Course Controller
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Logger.php';

class CourseController {
    private $db;

    public function __construct() {
        Auth::startSession();
        Auth::requireLogin();
        $this->db = $GLOBALS['db'];
    }

    /**
     * Get all courses
     */
    public function getAllCourses($departmentId = null) {
        if ($departmentId !== null) {
            $stmt = $this->db->prepare(
                "SELECT c.*, d.department_name FROM courses c
                 LEFT JOIN departments d ON c.department_id = d.department_id
                 WHERE c.is_active = 1 AND c.department_id = ?
                 ORDER BY c.course_name ASC"
            );
            $stmt->bind_param('i', $departmentId);
            $stmt->execute();
        } else {
            $result = $this->db->query(
                "SELECT c.*, d.department_name FROM courses c
                 LEFT JOIN departments d ON c.department_id = d.department_id
                 WHERE c.is_active = 1
                 ORDER BY c.course_name ASC"
            );
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get course by ID
     */
    public function getCourseById($courseId) {
        $stmt = $this->db->prepare(
            "SELECT c.*, d.department_name FROM courses c
             LEFT JOIN departments d ON c.department_id = d.department_id
             WHERE c.course_id = ?"
        );
        
        $stmt->bind_param('i', $courseId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Add new course
     */
    public function addCourse($data) {
        // Sanitize input
        $data = array_map([Security::class, 'sanitizeInput'], $data);

        // Validate required fields
        $required = ['course_code', 'course_name', 'department_id'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'];
            }
        }

        // Check if course code already exists
        $checkStmt = $this->db->prepare("SELECT course_id FROM courses WHERE course_code = ?");
        $checkStmt->bind_param('s', $data['course_code']);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Course code already exists'];
        }

        // Insert course
        $stmt = $this->db->prepare(
            "INSERT INTO courses (course_code, course_name, department_id, duration_years, description)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'sssss',
            $data['course_code'],
            $data['course_name'],
            $data['department_id'],
            $data['duration_years'] ?? null,
            $data['description'] ?? null
        );

        if ($stmt->execute()) {
            $courseId = $this->db->lastInsertId();
            Logger::log('COURSE_CREATED', 'Course Management', $courseId, 'New course added: ' . $data['course_name'], '', '', 'Success');
            return ['success' => true, 'message' => 'Course added successfully', 'course_id' => $courseId];
        }

        return ['success' => false, 'message' => 'Failed to add course'];
    }

    /**
     * Update course
     */
    public function updateCourse($courseId, $data) {
        // Sanitize input
        $data = array_map([Security::class, 'sanitizeInput'], $data);

        $stmt = $this->db->prepare(
            "UPDATE courses SET course_name = ?, department_id = ?, duration_years = ?, description = ? WHERE course_id = ?"
        );

        $stmt->bind_param(
            'sssi',
            $data['course_name'] ?? '',
            $data['department_id'] ?? 0,
            $data['duration_years'] ?? null,
            $data['description'] ?? null,
            $courseId
        );

        if ($stmt->execute()) {
            Logger::log('COURSE_UPDATED', 'Course Management', $courseId, 'Course updated', '', '', 'Success');
            return ['success' => true, 'message' => 'Course updated successfully'];
        }

        return ['success' => false, 'message' => 'Failed to update course'];
    }

    /**
     * Delete course (soft delete)
     */
    public function deleteCourse($courseId) {
        $stmt = $this->db->prepare("UPDATE courses SET is_active = 0 WHERE course_id = ?");
        $stmt->bind_param('i', $courseId);

        if ($stmt->execute()) {
            Logger::log('COURSE_DELETED', 'Course Management', $courseId, 'Course deleted', '', '', 'Success');
            return ['success' => true, 'message' => 'Course deleted successfully'];
        }

        return ['success' => false, 'message' => 'Failed to delete course'];
    }

    /**
     * Get total courses
     */
    public function getTotalCourses() {
        $result = $this->db->query("SELECT COUNT(*) as total FROM courses WHERE is_active = 1");
        return $result->fetch_assoc()['total'];
    }
}

?>
