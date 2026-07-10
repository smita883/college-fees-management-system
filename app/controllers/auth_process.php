<?php
/**
 * Authentication Process Handler
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../controllers/AuthController.php';

Auth::startSession();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $authCtrl = new AuthController();
    $result = $authCtrl->login($username, $password);

    if ($result['success']) {
        $_SESSION['message'] = 'Login successful!';
        $_SESSION['message_type'] = 'success';
        header('Location: ' . APP_URL . '/dashboard.php');
    } else {
        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . APP_URL . '/login.php');
    }
} else {
    header('Location: ' . APP_URL . '/login.php');
}

exit();

?>
