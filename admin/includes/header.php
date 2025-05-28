<?php
// Check if $db is not defined, create a database connection
if (!isset($db) || $db === null) {
  require_once(__DIR__ . '/../../config/database.php');
  $database = new Database();
  $db = $database->getConnection();
}

// Get admin user data
if (isset($_SESSION['user_id'])) {
  $admin_query = "SELECT * FROM users WHERE id = :user_id";
  $admin_stmt = $db->prepare($admin_query);
  $admin_stmt->bindParam(':user_id', $_SESSION['user_id']);
  $admin_stmt->execute();
  $admin_data = $admin_stmt->fetch(PDO::FETCH_ASSOC);
  
  // Store email in session for navbar
  $_SESSION['user_email'] = $admin_data['email'];
} else {
  $admin_data = [
      'first_name' => 'Admin',
      'profile_image' => ''
  ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - RENT A CAR</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Admin CSS -->
  <link rel="stylesheet" href="assets/css/admin.css">
  
  <!-- Page specific CSS -->
  <?php if (basename($_SERVER['PHP_SELF']) == 'car-add.php' || basename($_SERVER['PHP_SELF']) == 'car-edit.php'): ?>
  <link rel="stylesheet" href="assets/css/car-form.css">
  <?php endif; ?>
</head>
<body>

<!-- Top Navigation Bar -->
<?php include('includes/main-nav.php'); ?>

<!-- Main Content Container -->
<div class="container-fluid py-4 mt-5">
