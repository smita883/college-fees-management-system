<?php
/**
 * CFMS - Configuration File
 * Database and application settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'cfms_user');
define('DB_PASS', 'secure_password_here');
define('DB_NAME', 'cfms_db');
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_NAME', 'College Fee Management System');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'production'); // 'production' or 'development'
define('APP_DEBUG', false);
define('APP_URL', 'http://localhost/cfms');

// Security Settings
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_SECURE_COOKIE', false); // Set to true in production with HTTPS
define('SESSION_HTTP_ONLY', true);

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Currency
define('CURRENCY_SYMBOL', '₹');
define('CURRENCY_CODE', 'INR');

// Pagination
define('ITEMS_PER_PAGE', 25);

// File Upload Settings
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('UPLOAD_PATH', __DIR__ . '/../../uploads/');
define('ALLOWED_EXTENSIONS', array('pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'));

// Email Configuration (if needed)
define('MAIL_DRIVER', 'smtp');
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your_email@gmail.com');
define('MAIL_PASSWORD', 'your_app_password');
define('MAIL_FROM', 'noreply@college.edu');

// System Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_ACCOUNTS', 'accounts');
define('ROLE_RECEPTIONIST', 'receptionist');
define('ROLE_MANAGER', 'manager');
define('ROLE_STAFF', 'staff');

// Backup Settings
define('BACKUP_PATH', __DIR__ . '/../../backups/');
define('BACKUP_RETENTION_DAYS', 30);

// Error Reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../../logs/error.log');
}

?>
