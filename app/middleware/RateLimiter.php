<?php
/**
 * Rate Limiting Middleware
 */

class RateLimiter {
    private static $redisClient = null;

    public static function init() {
        // If Redis is available, use it for rate limiting
        // Otherwise use session-based rate limiting
    }

    /**
     * Check rate limit
     */
    public static function checkLimit($identifier, $limit = 60, $window = 3600) {
        $key = 'rate_limit_' . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 0,
                'reset_time' => time() + $window
            ];
        }

        $current = $_SESSION[$key];

        // Reset if window has expired
        if (time() > $current['reset_time']) {
            $_SESSION[$key] = [
                'count' => 0,
                'reset_time' => time() + $window
            ];
            $current = $_SESSION[$key];
        }

        $_SESSION[$key]['count']++;

        if ($current['count'] > $limit) {
            return false;
        }

        return true;
    }
}

?>
