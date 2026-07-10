<?php
/**
 * Application Bootstrap File
 */

// Define application root
define('APP_ROOT', dirname(__DIR__));
define('APP_PATH', APP_ROOT . '/app');

// Load configuration
require_once APP_PATH . '/config/constants.php';
require_once APP_PATH . '/config/database.php';

// Load middleware
require_once APP_PATH . '/middleware/ErrorHandler.php';
require_once APP_PATH . '/middleware/CORSMiddleware.php';
require_once APP_PATH . '/middleware/RequestValidator.php';
require_once APP_PATH . '/middleware/RateLimiter.php';

// Register error handler
ErrorHandler::register();

// Load helpers
require_once APP_PATH . '/helpers/Auth.php';
require_once APP_PATH . '/helpers/Security.php';
require_once APP_PATH . '/helpers/Helper.php';
require_once APP_PATH . '/helpers/Logger.php';

// Handle CORS
CORSMiddleware::handle();

// Initialize session
Auth::startSession();

// Set timezone
date_default_timezone_set(TIMEZONE ?? 'UTC');

?>
