<?php
require_once '../config/database.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Check if the images column exists
$check_images = "SHOW COLUMNS FROM cars LIKE 'images'";
$stmt = $db->prepare($check_images);
$stmt->execute();
$images_exists = $stmt->rowCount() > 0;

// Add images column if it doesn't exist
if (!$images_exists) {
    $add_images = "ALTER TABLE cars ADD COLUMN images TEXT AFTER image";
    $stmt = $db->prepare($add_images);
    if ($stmt->execute()) {
        echo "<p>Added 'images' column to cars table.</p>";
    } else {
        echo "<p>Failed to add 'images' column to cars table.</p>";
    }
} else {
    echo "<p>'images' column already exists in cars table.</p>";
}

// Check if the availability column exists
$check_availability = "SHOW COLUMNS FROM cars LIKE 'availability'";
$stmt = $db->prepare($check_availability);
$stmt->execute();
$availability_exists = $stmt->rowCount() > 0;

// Add availability column if it doesn't exist
if (!$availability_exists) {
    $add_availability = "ALTER TABLE cars ADD COLUMN availability TINYINT(1) DEFAULT 1 AFTER price_per_day";
    $stmt = $db->prepare($add_availability);
    if ($stmt->execute()) {
        echo "<p>Added 'availability' column to cars table.</p>";
    } else {
        echo "<p>Failed to add 'availability' column to cars table.</p>";
    }
} else {
    echo "<p>'availability' column already exists in cars table.</p>";
}

// Check if the created_at column exists
$check_created_at = "SHOW COLUMNS FROM cars LIKE 'created_at'";
$stmt = $db->prepare($check_created_at);
$stmt->execute();
$created_at_exists = $stmt->rowCount() > 0;

// Add created_at column if it doesn't exist
if (!$created_at_exists) {
    $add_created_at = "ALTER TABLE cars ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    $stmt = $db->prepare($add_created_at);
    if ($stmt->execute()) {
        echo "<p>Added 'created_at' column to cars table.</p>";
    } else {
        echo "<p>Failed to add 'created_at' column to cars table.</p>";
    }
} else {
    echo "<p>'created_at' column already exists in cars table.</p>";
}

// Check if the updated_at column exists
$check_updated_at = "SHOW COLUMNS FROM cars LIKE 'updated_at'";
$stmt = $db->prepare($check_updated_at);
$stmt->execute();
$updated_at_exists = $stmt->rowCount() > 0;

// Add updated_at column if it doesn't exist
if (!$updated_at_exists) {
    $add_updated_at = "ALTER TABLE cars ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP";
    $stmt = $db->prepare($add_updated_at);
    if ($stmt->execute()) {
        echo "<p>Added 'updated_at' column to cars table.</p>";
    } else {
        echo "<p>Failed to add 'updated_at' column to cars table.</p>";
    }
} else {
    echo "<p>'updated_at' column already exists in cars table.</p>";
}

echo "<p>Car table update complete.</p>";
echo "<p><a href='car-add.php'>Go to Add Car page</a></p>";
?>
