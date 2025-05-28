<?php
// Start session
session_start();

// Include database connection
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Debug information
error_log("Car delete script started");

// Check if car ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Car ID is required";
    error_log("Car ID not provided");
    header('Location: cars.php');
    exit();
}

$car_id = $_GET['id'];
error_log("Attempting to delete car ID: " . $car_id);

// Extremely simple deletion - direct SQL query
try {
    // Direct database deletion with minimal code
    $query = "DELETE FROM cars WHERE id = ?";
    $stmt = $db->prepare($query);
    $result = $stmt->execute([$car_id]);
    
    if ($result) {
        $_SESSION['success'] = "Car deleted successfully";
        error_log("Car deleted successfully: " . $car_id);
    } else {
        $error = $stmt->errorInfo();
        $_SESSION['error'] = "Failed to delete car. Error: " . $error[2];
        error_log("Failed to delete car: " . print_r($error, true));
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    error_log("Exception during car deletion: " . $e->getMessage());
}

// Redirect back to cars page
header('Location: cars.php');
exit();
?>
