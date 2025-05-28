<?php
require_once '../config/database.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Check if car_categories table exists
$check_table = "SHOW TABLES LIKE 'car_categories'";
$stmt = $db->prepare($check_table);
$stmt->execute();
$table_exists = $stmt->rowCount() > 0;

// Create car_categories table if it doesn't exist
if (!$table_exists) {
    $create_table = "CREATE TABLE car_categories (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $stmt = $db->prepare($create_table);
    if ($stmt->execute()) {
        echo "<p>Created car_categories table.</p>";
    } else {
        echo "<p>Failed to create car_categories table.</p>";
        exit;
    }
}

// Default categories
$default_categories = [
    'Sedan',
    'SUV',
    'Hatchback',
    'Convertible',
    'Coupe',
    'Minivan',
    'Pickup',
    'Luxury',
    'Sports',
    'Electric'
];

// Add default categories if they don't exist
$categories_added = 0;
foreach ($default_categories as $category) {
    $check_category = "SELECT id FROM car_categories WHERE name = :name";
    $stmt = $db->prepare($check_category);
    $stmt->bindParam(':name', $category);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        $add_category = "INSERT INTO car_categories (name) VALUES (:name)";
        $stmt = $db->prepare($add_category);
        $stmt->bindParam(':name', $category);
        if ($stmt->execute()) {
            $categories_added++;
        }
    }
}

echo "<p>Added $categories_added new categories.</p>";

// Check if cars table has category_id column
$check_column = "SHOW COLUMNS FROM cars LIKE 'category_id'";
$stmt = $db->prepare($check_column);
$stmt->execute();
$column_exists = $stmt->rowCount() > 0;

// Add category_id column if it doesn't exist
if (!$column_exists) {
    $add_column = "ALTER TABLE cars ADD COLUMN category_id INT(11) AFTER id";
    $stmt = $db->prepare($add_column);
    if ($stmt->execute()) {
        echo "<p>Added category_id column to cars table.</p>";
        
        // Update existing cars to use a default category
        $default_category_id = 1; // Sedan
        $update_cars = "UPDATE cars SET category_id = :category_id WHERE category_id IS NULL";
        $stmt = $db->prepare($update_cars);
        $stmt->bindParam(':category_id', $default_category_id);
        if ($stmt->execute()) {
            echo "<p>Updated existing cars with default category.</p>";
        }
    } else {
        echo "<p>Failed to add category_id column to cars table.</p>";
    }
}

// Add foreign key constraint if it doesn't exist
$check_fk = "SELECT * FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'cars' 
            AND CONSTRAINT_NAME = 'fk_cars_category'";
$stmt = $db->prepare($check_fk);
$stmt->execute();
$fk_exists = $stmt->rowCount() > 0;

if (!$fk_exists) {
    // First make sure all cars have valid category_id
    $update_null = "UPDATE cars SET category_id = 1 WHERE category_id IS NULL";
    $stmt = $db->prepare($update_null);
    $stmt->execute();
    
    // Add foreign key constraint
    $add_fk = "ALTER TABLE cars 
              ADD CONSTRAINT fk_cars_category 
              FOREIGN KEY (category_id) 
              REFERENCES car_categories(id) 
              ON DELETE SET NULL";
    $stmt = $db->prepare($add_fk);
    if ($stmt->execute()) {
        echo "<p>Added foreign key constraint for category_id.</p>";
    } else {
        echo "<p>Failed to add foreign key constraint. This is not critical.</p>";
    }
}

echo "<p>Category setup complete.</p>";
echo "<p><a href='car-edit.php?id=" . (isset($_GET['id']) ? $_GET['id'] : '1') . "'>Return to Car Edit</a></p>";
echo "<p><a href='cars.php'>Go to Cars List</a></p>";
?>
