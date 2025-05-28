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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize user object
    $user = new User($db);
    
    // Set user properties
    $user->first_name = $_POST['first_name'];
    $user->last_name = $_POST['last_name'];
    $user->email = $_POST['email'];
    $user->phone = $_POST['phone'] ?? '';
    $user->password = $_POST['password'];
    $user->role = $_POST['role'];
    $user->status = $_POST['status'] ?? 'active';
    
    // Create user
    if ($user->createUser()) {
        $_SESSION['success'] = "User created successfully";
    } else {
        $_SESSION['error'] = "Failed to create user. Email may already exist.";
    }
}

// Redirect to users page
header('Location: users.php');
exit();
?>
