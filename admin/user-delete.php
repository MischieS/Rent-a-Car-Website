<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Include models
require_once '../models/User.php';

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "User ID is required";
    header('Location: users.php');
    exit();
}

$user_id = $_GET['id'];

// Initialize user object
$user = new User($db);

// Delete user
if ($user->deleteUser($user_id)) {
    $_SESSION['success'] = "User deleted successfully";
} else {
    $_SESSION['error'] = "Failed to delete user";
}

// Redirect to users page
header('Location: users.php');
exit();
?>
