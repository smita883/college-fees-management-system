<?php
/**
 * Student API Handler
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../controllers/StudentController.php';

Auth::startSession();
Auth::requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $studentCtrl = new StudentController();

    switch ($action) {
        case 'list':
            $page = $_GET['page'] ?? 1;
            $students = $studentCtrl->getAllStudents($page);
            echo json_encode(['success' => true, 'data' => $students]);
            break;

        case 'get':
            $studentId = $_GET['id'] ?? 0;
            $student = $studentCtrl->getStudentById($studentId);
            echo json_encode(['success' => true, 'data' => $student]);
            break;

        case 'add':
            if ($method === 'POST') {
                $result = $studentCtrl->addStudent($_POST);
                echo json_encode($result);
            }
            break;

        case 'update':
            if ($method === 'POST') {
                $studentId = $_POST['student_id'] ?? 0;
                $result = $studentCtrl->updateStudent($studentId, $_POST);
                echo json_encode($result);
            }
            break;

        case 'delete':
            if ($method === 'POST') {
                $studentId = $_POST['student_id'] ?? 0;
                $result = $studentCtrl->deleteStudent($studentId);
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
