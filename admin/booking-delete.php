<?php
// Include necessary files
require_once '../includes/session.php';
require_once '../config/database.php';
require_once '../models/Booking.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = "Unauthorized access";
    header('Location: ../login.php');
    exit();
}

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Booking ID is required";
    header('Location: bookings.php');
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize booking object
$booking = new Booking($db);
$booking->id = $_GET['id'];

// Delete the booking
if ($booking->delete()) {
    $_SESSION['success'] = "Booking was deleted successfully";
} else {
    $_SESSION['error'] = "Unable to delete booking";
}

// Redirect back to bookings page
header('Location: bookings.php');
exit();
?>
