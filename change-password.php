<?php
// Include necessary files
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'includes/session.php';

// Redirect if not logged in
requireLogin();

// Initialize variables
$success = $error = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Create user object
    $user = new User($db);
    $user->id = getCurrentUserId();
    $user->getUserById($user->id);
    
    // Verify current password
    if (password_verify($current_password, $user->password)) {
        // Change password
        if ($user->changePassword($new_password)) {
            $success = "Password changed successfully!";
        } else {
            $error = "Failed to change password. Please try again.";
        }
    } else {
        $error = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Change Password - DREAMS RENT</title>
    <?php include('assets/includes/header_link.php') ?>
</head>
<body>
    <div class="main-wrapper">
        <!-- Header -->
        <?php include('assets/includes/header.php') ?>
        <!-- /Header -->

        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1>Change Password</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="profile.php">My Profile</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Change Password</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Change Password Section -->
        <section class="section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Change Your Password</h3>
                                
                                <?php if (!empty($success)): ?>
                                <div class="alert alert-success"><?php echo $success; ?></div>
                                <?php endif; ?>
                                
                                <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endif; ?>
                                
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <div class="form-group mb-3">
                                        <label for="current_password">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="new_password">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="confirm_password">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    
                                    <div class="text-end">
                                        <a href="profile.php" class="btn btn-secondary me-2">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Change Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Change Password Section -->

        <!-- Footer -->
        <?php include('assets/includes/footer.php') ?>
        <!-- /Footer -->
    </div>

    <?php include('assets/includes/footer_link.php') ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirm password validation
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(event) {
            if (newPasswordInput.value !== confirmPasswordInput.value) {
                event.preventDefault();
                alert('New passwords do not match!');
            }
        });
    });
    </script>
</body>
</html>
