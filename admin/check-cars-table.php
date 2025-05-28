<?php
// Start session
session_start();

// Include database connection
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Check cars table structure
try {
    $query = "DESCRIBE cars";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Cars Table Structure</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Check if images column exists
    $hasImagesColumn = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'images') {
            $hasImagesColumn = true;
            break;
        }
    }
    
    if (!$hasImagesColumn) {
        echo "<h3>Adding 'images' column to cars table</h3>";
        $alterQuery = "ALTER TABLE cars ADD COLUMN images TEXT NULL AFTER image";
        $db->exec($alterQuery);
        echo "Column 'images' added successfully.";
    } else {
        echo "<h3>'images' column already exists.</h3>";
    }
    
    // Check if seats column exists
    $hasSeatsColumn = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'seats') {
            $hasSeatsColumn = true;
            break;
        }
    }
    
    if (!$hasSeatsColumn) {
        echo "<h3>Adding 'seats' column to cars table</h3>";
        $alterQuery = "ALTER TABLE cars ADD COLUMN seats INT DEFAULT 5 AFTER fuel_type";
        $db->exec($alterQuery);
        echo "Column 'seats' added successfully.";
    } else {
        echo "<h3>'seats' column already exists.</h3>";
    }
    
    // Check if description column exists
    $hasDescriptionColumn = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'description') {
            $hasDescriptionColumn = true;
            break;
        }
    }
    
    if (!$hasDescriptionColumn) {
        echo "<h3>Adding 'description' column to cars table</h3>";
        $alterQuery = "ALTER TABLE cars ADD COLUMN description TEXT NULL AFTER images";
        $db->exec($alterQuery);
        echo "Column 'description' added successfully.";
    } else {
        echo "<h3>'description' column already exists.</h3>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
