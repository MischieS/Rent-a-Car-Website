<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Include necessary files
require_once 'config/database.php';
require_once 'includes/session.php';

// Check if user is admin
if (!isAdmin()) {
    // Redirect to home page if not admin
    header('Location: index.php');
    exit();
}

// Redirect to admin dashboard
header('Location: admin/index.php');
exit();
?>
