<?php
require_once '../config/database.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Add images column to cars table if it doesn't exist
$check_column = "SHOW COLUMNS FROM cars LIKE 'images'";
$stmt = $db->prepare($check_column);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    // Column doesn't exist, add it
    $alter_table = "ALTER TABLE cars ADD COLUMN images TEXT AFTER image";
    $stmt = $db->prepare($alter_table);
    
    if ($stmt->execute()) {
        echo "Successfully added 'images' column to cars table.<br>";
    } else {
        echo "Failed to add 'images' column to cars table.<br>";
    }
} else {
    echo "'images' column already exists in cars table.<br>";
}

// Check if color and seats columns exist and are still needed
$check_color = "SHOW COLUMNS FROM cars LIKE 'color'";
$stmt = $db->prepare($check_color);
$stmt->execute();
$color_exists = $stmt->rowCount() > 0;

$check_seats = "SHOW COLUMNS FROM cars LIKE 'seats'";
$stmt = $db->prepare($check_seats);
$stmt->execute();
$seats_exists = $stmt->rowCount() > 0;

echo "<br>Color column exists: " . ($color_exists ? "Yes" : "No");
echo "<br>Seats column exists: " . ($seats_exists ? "Yes" : "No");

// Check if remember_token column exists in users table
$query = "SHOW COLUMNS FROM users LIKE 'remember_token'";
$stmt = $db->prepare($query);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    // Add remember_token column
    $query = "ALTER TABLE users ADD COLUMN remember_token VARCHAR(255) NULL";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute()) {
        echo "Remember token column added to users table.<br>";
    } else {
        echo "Error adding remember token column to users table.<br>";
    }
} else {
    echo "Remember token column already exists in users table.<br>";
}

echo "<br><a href='../admin/index.php'>Back to Admin Dashboard</a>";
?>
