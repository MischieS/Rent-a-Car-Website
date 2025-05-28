<?php
// Include database and object files
require_once '../includes/session.php';
require_once '../config/database.php';
require_once '../models/Settings.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Settings object
$settings = new Settings($db);

// Get settings
$settings->getSettings();

// Process form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    // Set settings properties
    $settings->site_title = $_POST['site_title'];
    $settings->site_description = $_POST['site_description'];
    $settings->contact_email = $_POST['contact_email'];
    $settings->contact_phone = $_POST['contact_phone'];
    $settings->contact_address = $_POST['contact_address'];
    $settings->currency_symbol = $_POST['currency_symbol'];
    $settings->min_rental_period = $_POST['min_rental_period'];
    $settings->max_rental_period = $_POST['max_rental_period'];
    $settings->terms_conditions = $_POST['terms_conditions'];
    $settings->privacy_policy = $_POST['privacy_policy'];
    $settings->facebook_url = $_POST['facebook_url'];
    $settings->twitter_url = $_POST['twitter_url'];
    $settings->instagram_url = $_POST['instagram_url'];
    $settings->enable_reviews = isset($_POST['enable_reviews']) ? 1 : 0;
    $settings->maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
    
    // Update settings
    if ($settings->updateSettings()) {
        $success_message = "Settings updated successfully!";
    } else {
        $error_message = "Failed to update settings.";
    }
    
    // Refresh settings
    $settings->getSettings();
}

// Page title
$page_title = "System Settings";

// Include header
include_once 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-2 text-gray-800">System Settings</h1>
            <p class="mb-4">Configure your car rental system settings</p>
        </div>
    </div>

    <?php if (!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">System Settings</h6>
        </div>
        <div class="card-body">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">General</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Contact</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="booking-tab" data-bs-toggle="tab" data-bs-target="#booking" type="button" role="tab" aria-controls="booking" aria-selected="false">Booking</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="legal-tab" data-bs-toggle="tab" data-bs-target="#legal" type="button" role="tab" aria-controls="legal" aria-selected="false">Legal</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab" aria-controls="social" aria-selected="false">Social Media</button>
                    </li>
                </ul>
                
                <div class="tab-content p-4" id="settingsTabContent">
                    <!-- General Settings Tab -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="site_title" class="form-label">Site Title</label>
                                <input type="text" class="form-control" id="site_title" name="site_title" value="<?php echo htmlspecialchars($settings->site_title); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="currency_symbol" class="form-label">Currency Symbol</label>
                                <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="<?php echo htmlspecialchars($settings->currency_symbol); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="site_description" class="form-label">Site Description</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($settings->site_description); ?></textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable_reviews" name="enable_reviews" <?php echo $settings->enable_reviews ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="enable_reviews">Enable Customer Reviews</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" <?php echo $settings->maintenance_mode ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Settings Tab -->
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                        <div class="mb-3">
                            <label for="contact_email" class="form-label">Contact Email</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($settings->contact_email); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_phone" class="form-label">Contact Phone</label>
                            <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($settings->contact_phone); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_address" class="form-label">Contact Address</label>
                            <textarea class="form-control" id="contact_address" name="contact_address" rows="3"><?php echo htmlspecialchars($settings->contact_address); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Booking Settings Tab -->
                    <div class="tab-pane fade" id="booking" role="tabpanel" aria-labelledby="booking-tab">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="min_rental_period" class="form-label">Minimum Rental Period (days)</label>
                                <input type="number" class="form-control" id="min_rental_period" name="min_rental_period" value="<?php echo htmlspecialchars($settings->min_rental_period); ?>" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="max_rental_period" class="form-label">Maximum Rental Period (days)</label>
                                <input type="number" class="form-control" id="max_rental_period" name="max_rental_period" value="<?php echo htmlspecialchars($settings->max_rental_period); ?>" min="1" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Legal Settings Tab -->
                    <div class="tab-pane fade" id="legal" role="tabpanel" aria-labelledby="legal-tab">
                        <div class="mb-3">
                            <label for="terms_conditions" class="form-label">Terms and Conditions</label>
                            <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="10"><?php echo htmlspecialchars($settings->terms_conditions); ?></textarea>
                            <small class="form-text text-muted">HTML formatting is allowed.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="privacy_policy" class="form-label">Privacy Policy</label>
                            <textarea class="form-control" id="privacy_policy" name="privacy_policy" rows="10"><?php echo htmlspecialchars($settings->privacy_policy); ?></textarea>
                            <small class="form-text text-muted">HTML formatting is allowed.</small>
                        </div>
                    </div>
                    
                    <!-- Social Media Settings Tab -->
                    <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                        <div class="mb-3">
                            <label for="facebook_url" class="form-label">Facebook URL</label>
                            <input type="url" class="form-control" id="facebook_url" name="facebook_url" value="<?php echo htmlspecialchars($settings->facebook_url); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="twitter_url" class="form-label">Twitter URL</label>
                            <input type="url" class="form-control" id="twitter_url" name="twitter_url" value="<?php echo htmlspecialchars($settings->twitter_url); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="instagram_url" class="form-label">Instagram URL</label>
                            <input type="url" class="form-control" id="instagram_url" name="instagram_url" value="<?php echo htmlspecialchars($settings->instagram_url); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" name="update_settings" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Simple textarea enhancement script -->
<script>
    // Add basic formatting buttons for textareas if needed in the future
    // This is a placeholder for potential future enhancements
    document.addEventListener('DOMContentLoaded', function() {
        // No TinyMCE, just using standard textareas
    });
</script>

<?php
// Include footer
include_once 'includes/footer.php';
?>
