<?php
/**
 * Application Constants and Configuration
 */

// Application Details
define('APP_NAME', 'College Fee Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost/college-fees-management-system');
define('APP_TITLE', 'CFMS');

// Environment
define('DEBUG_MODE', getenv('DEBUG_MODE') !== false ? getenv('DEBUG_MODE') : false);
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: 'development');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour
define('SESSION_SECURE_COOKIE', false); // Set to true in production
define('SESSION_HTTP_ONLY', true);

// Password Configuration
define('PASSWORD_MIN_LENGTH', 8);

// Pagination
define('ITEMS_PER_PAGE', 20);

// Currency
define('CURRENCY_SYMBOL', '₹');
define('CURRENCY_CODE', 'INR');

// User Roles
define('ROLE_ADMIN', 'Admin');
define('ROLE_MANAGER', 'Manager');
define('ROLE_ACCOUNTS', 'Accounts');
define('ROLE_FACULTY', 'Faculty');
define('ROLE_STAFF', 'Staff');

// Timezone
define('TIMEZONE', 'Asia/Kolkata');

// File Upload
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_UPLOAD_TYPES', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png']);

// CORS
define('ALLOWED_ORIGINS', '*');

// Email Configuration (for future use)
define('MAIL_FROM', 'noreply@cfms.local');
define('MAIL_HOST', 'localhost');
define('MAIL_PORT', 587);

?>
