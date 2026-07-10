<?php
/**
 * Fee Structure API Handler
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../controllers/FeeStructureController.php';

Auth::startSession();
Auth::requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $feeCtrl = new FeeStructureController();

    switch ($action) {
        case 'list':
            $academicYear = $_GET['academic_year'] ?? null;
            $fees = $feeCtrl->getAllFeeStructures($academicYear);
            echo json_encode(['success' => true, 'data' => $fees]);
            break;

        case 'get':
            $feeId = $_GET['id'] ?? 0;
            $fee = $feeCtrl->getFeeStructureById($feeId);
            echo json_encode(['success' => true, 'data' => $fee]);
            break;

        case 'add':
            if ($method === 'POST') {
                Auth::requireRole([ROLE_ADMIN, ROLE_ACCOUNTS]);
                $result = $feeCtrl->addFeeStructure($_POST);
                echo json_encode($result);
            }
            break;

        case 'update':
            if ($method === 'POST') {
                Auth::requireRole([ROLE_ADMIN, ROLE_ACCOUNTS]);
                $feeId = $_POST['fee_id'] ?? 0;
                $result = $feeCtrl->updateFeeStructure($feeId, $_POST);
                echo json_encode($result);
            }
            break;

        case 'delete':
            if ($method === 'POST') {
                Auth::requireRole([ROLE_ADMIN]);
                $feeId = $_POST['fee_id'] ?? 0;
                $result = $feeCtrl->deleteFeeStructure($feeId);
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
