<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
} else if (session_status() === PHP_SESSION_NONE) {
    // If headers are already sent, we need to use output buffering for future pages
    ob_start();
    session_start();
    ob_end_clean();
}

// Include database connection
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

// Check if user is logged in
function isLoggedIn() {
    // Check if user is logged in via session
    if (isset($_SESSION['user_id'])) {
        return true;
    }
    
    // Check if user is logged in via remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        
        // Get database connection
        $database = new Database();
        $db = $database->getConnection();
        
        // Create user object
        $user = new User($db);
        
        // Attempt to login with token
        if ($user->loginWithToken($token)) {
            // Set session variables
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->first_name . ' ' . $user->last_name;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_role'] = $user->role;
            
            return true;
        }
        
        // Invalid token, clear cookie
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    return false;
}

// Check if user is admin
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

// Set user session
function setUserSession($user) {
    $_SESSION['user_id'] = $user->id;
    $_SESSION['user_name'] = $user->first_name . ' ' . $user->last_name;
    $_SESSION['user_email'] = $user->email;
    $_SESSION['user_role'] = $user->role;
}

// Clear user session
function clearUserSession() {
    // Clear remember me cookie if exists
    if (isset($_COOKIE['remember_token'])) {
        // Get database connection
        $database = new Database();
        $db = $database->getConnection();
        
        // Create user object
        $user = new User($db);
        $user->id = $_SESSION['user_id'];
        
        // Clear token in database
        $user->clearRememberToken();
        
        // Clear cookie
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    // Clear session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_role']);
    
    // Destroy session
    session_destroy();
}

// Get current user ID
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Get current user name
function getCurrentUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
}

// Get current user email
function getCurrentUserEmail() {
    return isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
}

// Get current user role
function getCurrentUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}
?>
