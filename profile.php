<?php
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'includes/session.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$user->id = getCurrentUserId();
$user->getUserById($user->id);

$upload_dir = "uploads/profile_images/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$message = '';

// Handle profile update
if ($_POST && isset($_POST["update_profile"])) {
    $user->first_name = $_POST["first_name"];
    $user->last_name = $_POST["last_name"];
    $user->phone = $_POST["phone"];
    $user->address = $_POST["address"];
    $user->city = $_POST["city"];
    $user->state = $_POST["state"];
    $user->zip_code = $_POST["zip_code"];
    $user->license_number = $_POST["license_number"];
    $user->license_expiry = $_POST["license_expiry"];

    if ($user->updateProfile()) {
        $message = '<div class="alert alert-success">Profile updated successfully!</div>';
        $_SESSION['user_name'] = $user->first_name . ' ' . $user->last_name;
    } else {
        $message = '<div class="alert alert-danger">Failed to update profile.</div>';
    }
}

// Handle profile image upload
if ($_POST && isset($_POST["upload_profile_image"])) {
    if ($_FILES["profile_image"]["size"] > 0) {
        $file_name = time() . '_' . $_FILES["profile_image"]["name"];
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $user->profile_image = $target_file;
            if ($user->updateProfile()) {
                $message = '<div class="alert alert-success">Profile image uploaded successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Failed to update profile image.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Failed to upload image.</div>';
        }
    }
}

$page_title = "My Profile";
include_once 'assets/includes/header.php';
?>

<body>
    <?php include('assets/includes/header.php') ?>

    <div class="page-header">
        <div class="container">
            <h1>My Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">My Profile</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="profile-section">
        <div class="container">
            <?= $message ?>
            
            <div class="row">
                <div class="col-lg-3 col-md-4 mb-4">
                    <div class="profile-sidebar">
                        <div class="profile-user">
                            <form method="post" enctype="multipart/form-data">
                                <div class="profile-image-container">
                                    <?php if ($user->profile_image && file_exists($user->profile_image)): ?>
                                        <img src="<?= $user->profile_image ?>" alt="Profile" class="profile-image">
                                    <?php else: ?>
                                        <span class="profile-avatar"><?= substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1) ?></span>
                                    <?php endif; ?>
                                    <div class="profile-image-upload">
                                        <i class="fas fa-camera"></i> Change Photo
                                        <input type="file" name="profile_image" accept="image/*" onchange="this.form.submit()">
                                    </div>
                                </div>
                                <input type="hidden" name="upload_profile_image" value="1">
                            </form>
                            
                            <h4><?= $user->first_name . ' ' . $user->last_name ?></h4>
                            <p><?= $user->email ?></p>
                        </div>
                        
                        <div class="profile-menu">
                            <ul>
                                <li class="active">
                                    <a href="#profile-info"><i class="fas fa-user"></i> Personal Information</a>
                                </li>
                                <li>
                                    <a href="change-password.php"><i class="fas fa-lock"></i> Change Password</a>
                                </li>
                                <li>
                                    <a href="my-bookings.php"><i class="fas fa-calendar-check"></i> My Bookings</a>
                                </li>
                                <li>
                                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9 col-md-8">
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <h3>Personal Information</h3>
                        </div>
                        <div class="profile-card-body">
                            <form method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="first_name" value="<?= $user->first_name ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="last_name" value="<?= $user->last_name ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" value="<?= $user->email ?>" readonly>
                                            <small class="text-muted">Email cannot be changed</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" name="phone" value="<?= $user->phone ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" name="address" value="<?= $user->address ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="city" value="<?= $user->city ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">State</label>
                                            <input type="text" class="form-control" name="state" value="<?= $user->state ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Zip Code</label>
                                            <input type="text" class="form-control" name="zip_code" value="<?= $user->zip_code ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Driver's License Number</label>
                                            <input type="text" class="form-control" name="license_number" value="<?= $user->license_number ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">License Expiry Date</label>
                                            <input type="date" class="form-control" name="license_expiry" value="<?= $user->license_expiry ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group text-end">
                                    <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include('assets/includes/footer.php') ?>
</body>
</html>

<style>
.profile-image-container {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 15px;
    border-radius: 50%;
    overflow: hidden;
    background-color: #007bff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-avatar {
    color: white;
    font-size: 24px;
    font-weight: bold;
}

.profile-image-upload {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.7);
    padding: 5px;
    text-align: center;
    color: white;
    font-size: 12px;
    cursor: pointer;
}

.profile-image-upload input[type="file"] {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    cursor: pointer;
}
</style>
