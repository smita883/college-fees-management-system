<?php
/**
 * General Helper Functions
 */

class Helper {
    /**
     * Format currency
     */
    public static function formatCurrency($amount) {
        return CURRENCY_SYMBOL . number_format($amount, 2, '.', ',');
    }

    /**
     * Format date
     */
    public static function formatDate($date, $format = 'd-m-Y') {
        if (empty($date)) return '-';
        return date($format, strtotime($date));
    }

    /**
     * Format datetime
     */
    public static function formatDateTime($datetime, $format = 'd-m-Y H:i:s') {
        if (empty($datetime)) return '-';
        return date($format, strtotime($datetime));
    }

    /**
     * Get age from date of birth
     */
    public static function getAge($dob) {
        if (empty($dob)) return 0;
        $birthDate = new DateTime($dob);
        $today = new DateTime('today');
        return $birthDate->diff($today)->y;
    }

    /**
     * Generate random string
     */
    public static function generateRandomString($length = 10) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Get file size in human readable format
     */
    public static function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get full name
     */
    public static function getFullName($firstName, $lastName) {
        return trim($firstName . ' ' . $lastName);
    }

    /**
     * Redirect with message
     */
    public static function redirect($url, $message = '', $type = 'success') {
        if (!empty($message)) {
            $_SESSION['message'] = $message;
            $_SESSION['message_type'] = $type;
        }
        header('Location: ' . $url);
        exit();
    }

    /**
     * Get and clear message
     */
    public static function getMessage() {
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            $type = $_SESSION['message_type'] ?? 'info';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            return ['message' => $message, 'type' => $type];
        }
        return null;
    }
}

?>
