<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Booking.php';
require_once '../includes/session.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Initialize booking object
$booking = new Booking($db);

// Get filter parameters
$car_id = isset($_GET['car_id']) && !empty($_GET['car_id']) ? $_GET['car_id'] : null;
$status = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : null;

try {
    // Get bookings for calendar
    $bookings = $booking->getBookingsForCalendar($car_id, $status);
    
    // Format bookings for FullCalendar
    $events = [];
    foreach ($bookings as $booking) {
        $events[] = [
            'id' => $booking['id'],
            'title' => $booking['car_name'] . ' - ' . $booking['user_name'],
            'start' => $booking['pickup_date'],
            'end' => $booking['return_date'],
            'status' => $booking['status'],
            'user_name' => $booking['user_name'],
            'user_email' => $booking['user_email'],
            'car_name' => $booking['car_name'],
            'car_image' => $booking['car_image'],
            'pickup_location_name' => $booking['pickup_location_name'],
            'return_location_name' => $booking['return_location_name'],
            'total_price' => $booking['total_price']
        ];
    }
    
    // Return bookings as JSON
    echo json_encode($events);
} catch (Exception $e) {
    // Log error
    error_log('Calendar data error: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to load booking data']);
}
?>
