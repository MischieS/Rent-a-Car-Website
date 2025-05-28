<?php
require_once '../config/database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Function to check if table exists
function tableExists($db, $tableName) {
    try {
        $result = $db->query("SELECT 1 FROM $tableName LIMIT 1");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// HTML header
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Duplicate Categories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Fix Duplicate Categories</h1>
        <div class="card">
            <div class="card-body">';

// Check if car_categories table exists
if (!tableExists($db, 'car_categories')) {
    echo '<div class="alert alert-danger">The car_categories table does not exist.</div>';
    echo '<a href="cars.php" class="btn btn-primary">Go to Cars Page</a>';
    echo '</div></div></div></body></html>';
    exit;
}

// Find duplicate categories
$query = "SELECT name, COUNT(*) as count FROM car_categories GROUP BY name HAVING COUNT(*) > 1";
$stmt = $db->prepare($query);
$stmt->execute();
$duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($duplicates) === 0) {
    echo '<div class="alert alert-success">No duplicate categories found.</div>';
} else {
    echo '<div class="alert alert-warning">Found ' . count($duplicates) . ' duplicate category names.</div>';
    echo '<ul class="list-group mb-3">';
    
    foreach ($duplicates as $duplicate) {
        echo '<li class="list-group-item">Category "' . htmlspecialchars($duplicate['name']) . '" appears ' . $duplicate['count'] . ' times</li>';
    }
    
    echo '</ul>';
    
    // Fix duplicates if requested
    if (isset($_GET['fix']) && $_GET['fix'] === 'true') {
        echo '<h4>Fixing duplicates...</h4>';
        
        foreach ($duplicates as $duplicate) {
            $categoryName = $duplicate['name'];
            
            // Get all IDs for this category name
            $query = "SELECT id FROM car_categories WHERE name = :name ORDER BY id ASC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $categoryName);
            $stmt->execute();
            $categoryIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Keep the first ID (lowest/oldest) and delete the rest
            $keepId = array_shift($categoryIds);
            
            echo '<p>Keeping category "' . htmlspecialchars($categoryName) . '" with ID ' . $keepId . '</p>';
            
            // Update any cars using the duplicate IDs to use the kept ID
            foreach ($categoryIds as $deleteId) {
                $query = "UPDATE cars SET category_id = :keepId WHERE category_id = :deleteId";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':keepId', $keepId);
                $stmt->bindParam(':deleteId', $deleteId);
                $stmt->execute();
                
                // Delete the duplicate category
                $query = "DELETE FROM car_categories WHERE id = :deleteId";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':deleteId', $deleteId);
                $stmt->execute();
                
                echo '<p>Deleted duplicate category ID ' . $deleteId . ' and updated associated cars</p>';
            }
        }
        
        echo '<div class="alert alert-success mt-3">All duplicate categories have been fixed.</div>';
    } else {
        echo '<a href="fix-duplicate-categories.php?fix=true" class="btn btn-danger mb-3">Fix Duplicate Categories</a>';
        echo '<p class="text-muted">This will keep one instance of each duplicate category and update all cars to use that category.</p>';
    }
}

// Link to go back
echo '<a href="car-add.php" class="btn btn-primary">Go to Add Car Page</a>';

// HTML footer
echo '</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
?>
