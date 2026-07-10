<?php
/**
 * Receipt API Handler
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../controllers/ReceiptController.php';

Auth::startSession();
Auth::requireLogin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $receiptCtrl = new ReceiptController();

    switch ($action) {
        case 'list':
            $page = $_GET['page'] ?? 1;
            $receipts = $receiptCtrl->getAllReceipts($page);
            echo json_encode(['success' => true, 'data' => $receipts]);
            break;

        case 'get':
            $receiptId = $_GET['id'] ?? 0;
            $receipt = $receiptCtrl->getReceiptById($receiptId);
            echo json_encode(['success' => true, 'data' => $receipt]);
            break;

        case 'issue':
            if ($method === 'POST') {
                $paymentId = $_POST['payment_id'] ?? 0;
                $result = $receiptCtrl->issueReceipt($paymentId);
                echo json_encode($result);
            }
            break;

        case 'cancel':
            if ($method === 'POST') {
                $receiptId = $_POST['receipt_id'] ?? 0;
                $reason = $_POST['reason'] ?? '';
                $result = $receiptCtrl->cancelReceipt($receiptId, $reason);
                echo json_encode($result);
            }
            break;

        case 'get_by_number':
            $receiptNumber = $_GET['number'] ?? '';
            $receipt = $receiptCtrl->getReceiptByNumber($receiptNumber);
            echo json_encode(['success' => true, 'data' => $receipt]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>
