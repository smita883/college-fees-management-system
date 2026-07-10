<?php
/**
 * Department API Handler
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../controllers/DepartmentController.php';

Auth::startSession();
Auth::requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $deptCtrl = new DepartmentController();

    switch ($action) {
        case 'list':
            $departments = $deptCtrl->getAllDepartments();
            echo json_encode(['success' => true, 'data' => $departments]);
            break;

        case 'get':
            $departmentId = $_GET['id'] ?? 0;
            $department = $deptCtrl->getDepartmentById($departmentId);
            echo json_encode(['success' => true, 'data' => $department]);
            break;

        case 'add':
            if ($method === 'POST') {
                Auth::requireRole([ROLE_ADMIN]);
                $result = $deptCtrl->addDepartment($_POST);
                echo json_encode($result);
            }
            break;

        case 'update':
            if ($method === 'POST') {
                Auth::requireRole([ROLE_ADMIN]);
                $departmentId = $_POST['department_id'] ?? 0;
                $result = $deptCtrl->updateDepartment($departmentId, $_POST);
                echo json_encode($result);
            }
            break;

        case 'delete':
            if ($method === 'POST') {
                Auth::requireRole([ROLE_ADMIN]);
                $departmentId = $_POST['department_id'] ?? 0;
                $result = $deptCtrl->deleteDepartment($departmentId);
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
