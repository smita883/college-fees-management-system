<?php
/**
 * Course API Handler
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../controllers/CourseController.php';

Auth::startSession();
Auth::requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $courseCtrl = new CourseController();

    switch ($action) {
        case 'list':
            $departmentId = $_GET['department_id'] ?? null;
            $courses = $courseCtrl->getAllCourses($departmentId);
            echo json_encode(['success' => true, 'data' => $courses]);
            break;

        case 'get':
            $courseId = $_GET['id'] ?? 0;
            $course = $courseCtrl->getCourseById($courseId);
            echo json_encode(['success' => true, 'data' => $course]);
            break;

        case 'add':
            if ($method === 'POST') {
                Auth::requireRole([ROLE_ADMIN, ROLE_MANAGER]);
                $result = $courseCtrl->addCourse($_POST);
                echo json_encode($result);
            }
            break;

        case 'update':
            if ($method === 'POST') {
                Auth::requireRole([ROLE_ADMIN, ROLE_MANAGER]);
                $courseId = $_POST['course_id'] ?? 0;
                $result = $courseCtrl->updateCourse($courseId, $_POST);
                echo json_encode($result);
            }
            break;

        case 'delete':
            if ($method === 'POST') {
                Auth::requireRole([ROLE_ADMIN]);
                $courseId = $_POST['course_id'] ?? 0;
                $result = $courseCtrl->deleteCourse($courseId);
                echo json_encode($result);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>
