<?php
// Include session and database connection
include_once '../includes/session.php';
include_once '../config/database.php';
include_once '../models/Booking.php';
include_once '../models/Car.php';
include_once '../models/User.php';
include_once '../models/Location.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize booking object
$booking = new Booking($db);

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: bookings.php');
    exit();
}

$booking_id = $_GET['id'];
$booking_details = $booking->getBookingById($booking_id);

// If booking not found, redirect to bookings page
if (!$booking_details) {
    $_SESSION['error'] = "Booking not found";
    header('Location: bookings.php');
    exit();
}

// Debug booking details
error_log("Booking details: " . print_r($booking_details, true));

// Initialize car and user objects
$car = new Car($db);
$car->id = $booking_details['car_id'];
$car_details = $car->readOne();

// Debug car details
error_log("Car details: " . print_r($car_details, true));

// Try to get car details directly from the database if readOne() failed
if (!$car_details) {
    try {
        $query = "SELECT * FROM cars WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$booking_details['car_id']]);
        $car_details = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Direct car query result: " . print_r($car_details, true));
    } catch (PDOException $e) {
        error_log("Error fetching car details: " . $e->getMessage());
    }
}

// If car details still not found, try to get from booking
if (!$car_details && isset($booking_details['car_brand']) && isset($booking_details['car_model'])) {
    $car_details = [
        'brand' => $booking_details['car_brand'],
        'model' => $booking_details['car_model'],
        'year' => $booking_details['car_year'] ?? 'N/A',
        'price_per_day' => $booking_details['price_per_day'] ?? 0,
        'category' => $booking_details['car_category'] ?? 'Standard',
        'image' => $booking_details['car_image'] ?? ''
    ];
}

// If car details still not found, create a default array
if (!$car_details) {
    $car_details = [
        'brand' => 'Vehicle',
        'model' => 'Information',
        'year' => 'Unavailable',
        'price_per_day' => 0,
        'category' => 'Standard',
        'image' => ''
    ];
}

// Ensure all required keys exist in car_details
$required_car_keys = ['brand', 'model', 'year', 'price_per_day', 'category', 'image'];
foreach ($required_car_keys as $key) {
    if (!isset($car_details[$key])) {
        $car_details[$key] = ($key == 'price_per_day') ? 0 : 'Standard';
    }
}

// Initialize user object
$user = new User($db);
$user->id = $booking_details['user_id'];
$user_details = $user->getUserById($user->id);

// Debug user details
error_log("User details: " . print_r($user_details, true));

// Create default user details array
$default_user = [
    'first_name' => 'Unknown',
    'last_name' => 'User',
    'email' => 'N/A',
    'phone' => 'N/A',
    'address' => 'N/A'
];

// If user details not found, try to get from booking
if (!is_array($user_details) && isset($booking_details['user_name'])) {
    // Parse user name into first and last name
    $name_parts = explode(' ', $booking_details['user_name'], 2);
    $first_name = $name_parts[0];
    $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
    
    $user_details = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $booking_details['user_email'] ?? 'N/A',
        'phone' => $booking_details['user_phone'] ?? 'N/A',
        'address' => 'N/A'
    ];
}

// If user details still not found, use default
if (!is_array($user_details)) {
    $user_details = $default_user;
}

// Get location information
$location = new Location($db);
$pickup_location = $booking_details['pickup_location'] ?? 'N/A';
$return_location = $booking_details['return_location'] ?? 'N/A';

// Include header
include_once 'includes/header.php';
?>

<div class="admin-page-title">
    <h1>Booking Details</h1>
    <p class="text-muted">View and manage booking information</p>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <!-- Left empty for alignment -->
    </div>
    <div class="col-md-4 text-end">
        <a href="bookings.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Bookings
        </a>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row">
    <!-- Booking Information -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Booking #<?php echo $booking_details['id']; ?></h5>
                <span class="badge bg-<?php 
                    echo ($booking_details['status'] == 'confirmed') ? 'success' : 
                        (($booking_details['status'] == 'pending') ? 'warning' : 
                        (($booking_details['status'] == 'cancelled') ? 'danger' : 'info')); 
                ?> p-2">
                    <?php echo ucfirst($booking_details['status']); ?>
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Booking Information</h6>
                        <p><strong>Booking Date:</strong> <?php echo date('F d, Y', strtotime($booking_details['created_at'] ?? date('Y-m-d'))); ?></p>
                        <p><strong>Pickup Date:</strong> <?php echo date('F d, Y', strtotime($booking_details['pickup_date'])); ?></p>
                        <p><strong>Return Date:</strong> <?php echo date('F d, Y', strtotime($booking_details['return_date'])); ?></p>
                        <p><strong>Pickup Location:</strong> <?php echo $pickup_location; ?></p>
                        <p><strong>Return Location:</strong> <?php echo $return_location; ?></p>
                        <p><strong>Total Price:</strong> $<?php echo number_format($booking_details['total_price'], 2); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Customer Information</h6>
                        <p><strong>Name:</strong> <?php echo $user_details['first_name'] . ' ' . $user_details['last_name']; ?></p>
                        <p><strong>Email:</strong> <?php echo $user_details['email']; ?></p>
                        <p><strong>Phone:</strong> <?php echo $user_details['phone']; ?></p>
                        <p><strong>Address:</strong> <?php echo $user_details['address']; ?></p>
                    </div>
                </div>
                
                <h6 class="text-muted mb-3 mt-4">Vehicle Information</h6>
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <?php if (!empty($car_details['image'])): ?>
                            <img src="../<?php echo $car_details['image']; ?>" alt="<?php echo $car_details['brand'] . ' ' . $car_details['model']; ?>" class="img-fluid rounded">
                        <?php else: ?>
                            <div class="bg-light rounded text-center py-5">
                                <i class="fas fa-car fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-9">
                        <h5><?php echo $car_details['brand'] . ' ' . $car_details['model'] . ' (' . $car_details['year'] . ')'; ?></h5>
                        <p class="mb-1"><strong>Daily Rate:</strong> $<?php echo number_format($car_details['price_per_day'], 2); ?></p>
                        <p class="mb-0"><strong>Category:</strong> <?php echo $car_details['category'] ?? 'Standard'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions Sidebar -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Booking Actions</h5>
            </div>
            <div class="card-body">
                <form action="booking-update-status.php" method="post">
                    <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Update Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="pending" <?php echo ($booking_details['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo ($booking_details['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="completed" <?php echo ($booking_details['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo ($booking_details['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" id="payment_status" name="payment_status">
                            <option value="pending" <?php echo ($booking_details['payment_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo ($booking_details['payment_status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                            <option value="refunded" <?php echo ($booking_details['payment_status'] == 'refunded') ? 'selected' : ''; ?>>Refunded</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-save me-1"></i> Update Booking
                    </button>
                </form>
                
                <hr>
                
                <a href="booking-edit.php?id=<?php echo $booking_id; ?>" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fas fa-edit me-1"></i> Edit Booking
                </a>
                
                <a href="booking-delete.php?id=<?php echo $booking_id; ?>" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this booking?');">
                    <i class="fas fa-trash-alt me-1"></i> Delete Booking
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Booking Timeline</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div>
                            <i class="fas fa-plus-circle text-success me-2"></i>
                            <span>Booking Created</span>
                            <p class="text-muted small mb-0"><?php echo date('F d, Y h:i A', strtotime($booking_details['created_at'] ?? date('Y-m-d H:i:s'))); ?></p>
                        </div>
                    </li>
                    <?php if ($booking_details['status'] != 'pending'): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div>
                            <i class="fas fa-check-circle text-primary me-2"></i>
                            <span>Status Updated to <?php echo ucfirst($booking_details['status']); ?></span>
                            <p class="text-muted small mb-0"><?php echo date('F d, Y h:i A', strtotime($booking_details['updated_at'] ?? date('Y-m-d H:i:s'))); ?></p>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if ($booking_details['payment_status'] == 'paid'): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div>
                            <i class="fas fa-dollar-sign text-success me-2"></i>
                            <span>Payment Received</span>
                            <p class="text-muted small mb-0"><?php echo date('F d, Y h:i A', strtotime($booking_details['updated_at'] ?? date('Y-m-d H:i:s'))); ?></p>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
