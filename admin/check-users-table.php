<?php
// Include database connection
require_once '../config/database.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Function to check if a column exists in a table
function columnExists($db, $table, $column) {
    $query = "SHOW COLUMNS FROM $table LIKE '$column'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

// Function to add a column to a table if it doesn't exist
function addColumnIfNotExists($db, $table, $column, $definition) {
    if (!columnExists($db, $table, $column)) {
        $query = "ALTER TABLE $table ADD COLUMN $column $definition";
        $stmt = $db->prepare($query);
        $result = $stmt->execute();
        return $result ? "Column '$column' added successfully." : "Failed to add column '$column'.";
    }
    return "Column '$column' already exists.";
}

// Check and update the users table structure
try {
    // Check if the users table exists
    $query = "SHOW TABLES LIKE 'users'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        echo "The users table does not exist. Please run the setup.php script first.";
        exit;
    }
    
    // Add missing columns
    echo "<h2>Checking users table structure...</h2>";
    
    echo "<p>" . addColumnIfNotExists($db, 'users', 'status', "ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active'") . "</p>";
    echo "<p>" . addColumnIfNotExists($db, 'users', 'profile_image', "VARCHAR(255) DEFAULT NULL") . "</p>";
    echo "<p>" . addColumnIfNotExists($db, 'users', 'remember_token', "VARCHAR(255) DEFAULT NULL") . "</p>";
    echo "<p>" . addColumnIfNotExists($db, 'users', 'created_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP") . "</p>";
    echo "<p>" . addColumnIfNotExists($db, 'users', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP") . "</p>";
    
    echo "<h2>Users table structure check completed.</h2>";
    echo "<p><a href='../register.php'>Go to Registration Page</a></p>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
