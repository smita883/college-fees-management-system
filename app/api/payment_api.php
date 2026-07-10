<?php
/**
 * Fee Payment API Handler
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../controllers/FeePaymentController.php';

Auth::startSession();
Auth::requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $paymentCtrl = new FeePaymentController();

    switch ($action) {
        case 'list':
            $page = $_GET['page'] ?? 1;
            $filters = [
                'payment_status' => $_GET['status'] ?? '',
                'student_id' => $_GET['student_id'] ?? '',
                'start_date' => $_GET['start_date'] ?? '',
                'end_date' => $_GET['end_date'] ?? ''
            ];
            $payments = $paymentCtrl->getAllPayments($page, ITEMS_PER_PAGE, $filters);
            echo json_encode(['success' => true, 'data' => $payments]);
            break;

        case 'summary':
            $summary = $paymentCtrl->getPaymentSummary();
            echo json_encode(['success' => true, 'data' => $summary]);
            break;

        case 'record':
            if ($method === 'POST') {
                $result = $paymentCtrl->recordPayment($_POST);
                echo json_encode($result);
            }
            break;

        case 'history':
            $studentId = $_GET['student_id'] ?? 0;
            $history = $paymentCtrl->getStudentPaymentHistory($studentId);
            echo json_encode(['success' => true, 'data' => $history]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>
