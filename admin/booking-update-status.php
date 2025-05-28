<?php
// Include session and database connection
include_once '../includes/session.php';
include_once '../config/database.php';
include_once '../models/Booking.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $booking_id = $_POST['booking_id'] ?? '';
    $status = $_POST['status'] ?? '';
    $payment_status = $_POST['payment_status'] ?? '';
    
    // Validate data
    if (empty($booking_id) || empty($status) || empty($payment_status)) {
        $_SESSION['error'] = "All fields are required";
        header('Location: booking-view.php?id=' . $booking_id);
        exit();
    }
    
    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Initialize booking object
    $booking = new Booking($db);
    
    // Update booking status
    $result1 = $booking->updateStatus($booking_id, $status);
    
    // Update payment status
    $result2 = $booking->updatePaymentStatus($booking_id, $payment_status);
    
    if ($result1 && $result2) {
        $_SESSION['success'] = "Booking updated successfully";
    } else {
        $_SESSION['error'] = "Failed to update booking";
    }
    
    // Redirect back to booking view
    header('Location: booking-view.php?id=' . $booking_id);
    exit();
} else {
    // If not POST request, redirect to bookings page
    header('Location: bookings.php');
    exit();
}
?>
