<?php
/**
 * Database Connection Handler
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $conn;
    private $error;

    private function __construct() {
        try {
            $this->conn = new mysqli(
                DB_HOST,
                DB_USER,
                DB_PASS,
                DB_NAME
            );

            // Check connection
            if ($this->conn->connect_error) {
                throw new Exception('Database Connection Failed: ' . $this->conn->connect_error);
            }

            // Set charset
            $this->conn->set_charset(DB_CHARSET);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            error_log($this->error);
        }
    }

    // Get singleton instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Get connection
    public function getConnection() {
        return $this->conn;
    }

    // Execute query
    public function query($sql) {
        $result = $this->conn->query($sql);
        if (!$result && APP_DEBUG) {
            throw new Exception('Query Error: ' . $this->conn->error);
        }
        return $result;
    }

    // Prepare statement
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    // Get last insert ID
    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    // Get affected rows
    public function affectedRows() {
        return $this->conn->affected_rows;
    }

    // Close connection
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Database instance
$db = Database::getInstance();

?>
