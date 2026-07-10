<?php
/**
 * Logger Class for Activity Logging
 */

class Logger {
    private static $db = null;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        self::$db = $GLOBALS['db'];
    }

    /**
     * Log activity
     */
    public static function log($action, $module, $recordId = null, $description = '', $oldValue = '', $newValue = '', $status = 'Success') {
        require_once __DIR__ . '/../config/database.php';
        $db = $GLOBALS['db'];
        
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $ipAddress = Security::getUserIP();
        $userAgent = Security::getUserAgent();
        
        $stmt = $db->prepare(
            "INSERT INTO activity_logs (user_id, action, module, record_id, description, old_value, new_value, ip_address, user_agent, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->bind_param(
            'issisissss',
            $userId,
            $action,
            $module,
            $recordId,
            $description,
            $oldValue,
            $newValue,
            $ipAddress,
            $userAgent,
            $status
        );
        
        return $stmt->execute();
    }
}

?>
