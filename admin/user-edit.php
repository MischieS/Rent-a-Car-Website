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
$user_details = $user->getUserById($user_id);

// If user not found, redirect to users page
if (!$user_details) {
    $_SESSION['error'] = "User not found";
    header('Location: users.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set user properties
    $user->id = $user_id;
    $user->first_name = $_POST['first_name'];
    $user->last_name = $_POST['last_name'];
    $user->email = $_POST['email'];
    $user->phone = $_POST['phone'];
    $user->address = $_POST['address'] ?? '';
    $user->city = $_POST['city'] ?? '';
    $user->state = $_POST['state'] ?? '';
    $user->zip_code = $_POST['zip_code'] ?? '';
    $user->license_number = $_POST['license_number'] ?? '';
    $user->license_expiry = $_POST['license_expiry'] ?? '';
    $user->role = $_POST['role'];
    $user->status = $_POST['status'];
    
    // Update password if provided
    if (!empty($_POST['password'])) {
        $user->password = $_POST['password'];
    }
    
    // Handle profile image upload if provided
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/profiles/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            $user->profile_image = 'uploads/profiles/' . $file_name;
        }
    } else {
        // Keep existing image
        $user->profile_image = $user_details['profile_image'];
    }
    
    // Update user
    if ($user->updateUser()) {
        $_SESSION['success'] = "User updated successfully";
        header('Location: users.php');
        exit();
    } else {
        $_SESSION['error'] = "Failed to update user";
    }
}

// Set page title
$page_title = "Edit User";

// Include header
include_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Edit User</h1>
            <p class="text-muted">Update user information</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="users.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Users
            </a>
        </div>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_details['first_name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_details['last_name']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_details['email']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user_details['phone'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user_details['address'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user_details['city'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($user_details['state'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="zip_code" class="form-label">ZIP Code</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($user_details['zip_code'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="license_number" class="form-label">License Number</label>
                                <input type="text" class="form-control" id="license_number" name="license_number" value="<?php echo htmlspecialchars($user_details['license_number'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="license_expiry" class="form-label">License Expiry</label>
                                <input type="date" class="form-control" id="license_expiry" name="license_expiry" value="<?php echo htmlspecialchars($user_details['license_expiry'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="user" <?php echo ($user_details['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                                    <option value="admin" <?php echo ($user_details['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" <?php echo ($user_details['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($user_details['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Leave empty to keep the current password</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                            <div class="form-text">Leave empty to keep the current image</div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="users.php" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">User Profile</h5>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($user_details['profile_image'])): ?>
                        <img src="../<?php echo $user_details['profile_image']; ?>" alt="Profile" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                            <span style="font-size: 60px;"><?php echo substr($user_details['first_name'], 0, 1); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <h5><?php echo htmlspecialchars($user_details['first_name'] . ' ' . $user_details['last_name']); ?></h5>
                    <p class="text-muted"><?php echo ucfirst($user_details['role']); ?></p>
                    
                    <div class="mt-3">
                        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                        <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($user_details['phone'] ?? 'N/A'); ?></p>
                        <p class="mb-1"><strong>Joined:</strong> <?php echo date('M d, Y', strtotime($user_details['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
