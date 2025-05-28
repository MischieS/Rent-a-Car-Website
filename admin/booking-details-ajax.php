<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Booking.php';
require_once '../includes/session.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Initialize booking object
$booking = new Booking($db);

// Handle GET request for booking details
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
    
    // Get booking details
    $booking_details = $booking->getBookingById($booking_id);
    
    if ($booking_details) {
        echo json_encode([
            'success' => true,
            'booking' => $booking_details
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Booking not found'
        ]);
    }
}

// Handle POST request for booking actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : null;
    $action = isset($_POST['action']) ? $_POST['action'] : null;
    
    if (!$booking_id || !$action) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required parameters'
        ]);
        exit;
    }
    
    // Handle different actions
    switch ($action) {
        case 'cancel':
            if ($booking->updateStatus($booking_id, 'cancelled')) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Booking cancelled successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to cancel booking'
                ]);
            }
            break;
            
        case 'confirm':
            if ($booking->updateStatus($booking_id, 'confirmed')) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Booking confirmed successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to confirm booking'
                ]);
            }
            break;
            
        case 'complete':
            if ($booking->updateStatus($booking_id, 'completed')) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Booking marked as completed'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to complete booking'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            break;
    }
}
?>
