<?php
/**
 * Database Setup Script - Run this once for initial setup
 */

echo "=== College Fee Management System - Setup ===";
echo "\n\n";

if (!file_exists(__DIR__ . '/../app/config/database.php')) {
    echo "ERROR: Database configuration file not found.\n";
    exit(1);
}

require_once __DIR__ . '/../app/config/database.php';

try {
    // Test database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "✓ Database connection successful\n";
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    if ($conn->query($sql) === TRUE) {
        echo "✓ Database created/verified\n";
    } else {
        die("Error creating database: " . $conn->error);
    }
    
    // Use the database
    $conn->select_db(DB_NAME);
    
    // Read and execute schema
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            if (!$conn->query($statement)) {
                echo "Warning: " . $conn->error . "\n";
            }
        }
    }
    
    echo "✓ Database schema created\n";
    echo "✓ Sample data inserted\n";
    echo "\n";
    echo "=== Setup Complete ===";
    echo "\n\n";
    echo "Default Admin Credentials:\n";
    echo "Username: admin\n";
    echo "Password: Admin@123\n";
    echo "\n";
    echo "Please change the password immediately after first login.\n";
    
    $conn->close();
    
} catch (Exception $e) {
    die("Setup Error: " . $e->getMessage());
}

?>
