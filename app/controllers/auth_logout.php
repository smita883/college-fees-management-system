<?php
/**
 * Logout Handler
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../controllers/AuthController.php';

Auth::startSession();
$authCtrl = new AuthController();
$authCtrl->logout();

$_SESSION['message'] = 'You have been logged out successfully';
$_SESSION['message_type'] = 'success';
header('Location: ' . APP_URL . '/login.php');
exit();

?>
