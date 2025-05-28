<?php
// Create this file in your admin folder to debug upload issues
echo "<h2>Upload Debug Information</h2>";

// Check PHP upload settings
echo "<h3>PHP Upload Settings:</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "<br>";

// Check upload directory
$upload_dir = '../uploads/cars/';
echo "<h3>Upload Directory Info:</h3>";
echo "Upload directory: " . realpath($upload_dir) . "<br>";
echo "Directory exists: " . (is_dir($upload_dir) ? 'Yes' : 'No') . "<br>";
echo "Directory writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "<br>";

// Create directory if it doesn't exist
if (!is_dir($upload_dir)) {
    if (mkdir($upload_dir, 0777, true)) {
        echo "Created upload directory<br>";
    } else {
        echo "Failed to create upload directory<br>";
    }
}

// Test file creation
$test_file = $upload_dir . 'test.txt';
if (file_put_contents($test_file, 'test')) {
    echo "Can write files: Yes<br>";
    unlink($test_file);
} else {
    echo "Can write files: No<br>";
}

// Check database connection
try {
    require_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    echo "<h3>Database Connection:</h3>";
    echo "Database connected: Yes<br>";
    
    // Check if cars table exists
    $stmt = $db->query("SHOW TABLES LIKE 'cars'");
    echo "Cars table exists: " . ($stmt->rowCount() > 0 ? 'Yes' : 'No') . "<br>";
    
} catch (Exception $e) {
    echo "<h3>Database Connection:</h3>";
    echo "Database error: " . $e->getMessage() . "<br>";
}

// Check if car ID 6 exists
if (isset($db)) {
    try {
        $stmt = $db->prepare("SELECT * FROM cars WHERE id = 6");
        $stmt->execute();
        $car = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<h3>Car ID 6:</h3>";
        echo $car ? "Car exists: Yes<br>" : "Car exists: No<br>";
        if ($car) {
            echo "Car data: " . print_r($car, true) . "<br>";
        }
    } catch (Exception $e) {
        echo "Error checking car: " . $e->getMessage() . "<br>";
    }
}
?>
