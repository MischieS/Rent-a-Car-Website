<?php
// Include database connection
require_once 'config/database.php';
require_once 'models/Booking.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if car_id is provided
if (!isset($_GET['car_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Car ID is required'
    ]);
    exit;
}

$car_id = $_GET['car_id'];

try {
    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Get unavailable dates directly from database
    $unavailable_dates = [];
    
    // Simple query to get all bookings for this car
    $query = "SELECT pickup_date, return_date FROM bookings WHERE car_id = ? AND status != 'cancelled'";
    $stmt = $db->prepare($query);
    $stmt->execute([$car_id]);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pickup_date = new DateTime($row['pickup_date']);
        $return_date = new DateTime($row['return_date']);
        
        // Add all dates between pickup and return to unavailable dates
        $current_date = clone $pickup_date;
        while ($current_date <= $return_date) {
            $unavailable_dates[] = $current_date->format('Y-m-d');
            $current_date->modify('+1 day');
        }
    }
    
    // Remove duplicates
    $unavailable_dates = array_unique($unavailable_dates);
    
    // Return success response with unavailable dates
    echo json_encode([
        'success' => true,
        'unavailable_dates' => array_values($unavailable_dates)
    ]);
    
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving unavailable dates: ' . $e->getMessage()
    ]);
}
?>
