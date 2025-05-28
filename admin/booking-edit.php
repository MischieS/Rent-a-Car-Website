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
require_once '../models/Booking.php';
require_once '../models/Car.php';
require_once '../models/User.php';

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Booking ID is required";
    header('Location: bookings.php');
    exit();
}

$booking_id = $_GET['id'];

// Initialize booking object
$booking = new Booking($db);
$booking_details = $booking->getBookingById($booking_id);

// If booking not found, redirect to bookings page
if (!$booking_details) {
    $_SESSION['error'] = "Booking not found";
    header('Location: bookings.php');
    exit();
}

// Get cars and users for dropdowns
$car = new Car($db);
$cars = $car->read()->fetchAll(PDO::FETCH_ASSOC);

$user = new User($db);
$users = $user->getAllUsers();

// Get locations
$locations = $booking->getAllLocations();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set booking properties
    $booking->id = $booking_id;
    $booking->user_id = $_POST['user_id'];
    $booking->car_id = $_POST['car_id'];
    $booking->pickup_date = $_POST['pickup_date'];
    $booking->return_date = $_POST['return_date'];
    $booking->pickup_location_id = $_POST['pickup_location_id']; // Changed from pickup_location
    $booking->return_location_id = $_POST['return_location_id']; // Changed from return_location
    $booking->total_price = $_POST['total_price'];
    $booking->status = $_POST['status'];
    $booking->payment_status = $_POST['payment_status'] ?? 'pending';
    
    // Debug
    echo "<pre>";
    print_r($_POST);
    print_r($booking);
    echo "</pre>";
    
    // Update booking
    if ($booking->update()) {
        $_SESSION['success'] = "Booking updated successfully";
        header('Location: bookings.php');
        exit();
    } else {
        $_SESSION['error'] = "Failed to update booking";
        // Add debug information
        if (isset($db->errorInfo)) {
            error_log("Database error: " . print_r($db->errorInfo(), true));
        }
    }
}

// Set page title
$page_title = "Edit Booking";

// Include header
include_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1>Edit Booking</h1>
            <p class="text-muted">Update booking information</p>
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
                    <form action="" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">Customer</label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Select Customer</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?php echo $u['id']; ?>" <?php echo ($booking_details['user_id'] == $u['id']) ? 'selected' : ''; ?>>
                                            <?php echo $u['first_name'] . ' ' . $u['last_name'] . ' (' . $u['email'] . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="car_id" class="form-label">Car</label>
                                <select class="form-select" id="car_id" name="car_id" required>
                                    <option value="">Select Car</option>
                                    <?php foreach ($cars as $c): ?>
                                        <option value="<?php echo $c['id']; ?>" <?php echo ($booking_details['car_id'] == $c['id']) ? 'selected' : ''; ?>>
                                            <?php echo $c['brand'] . ' ' . $c['model'] . ' (' . $c['year'] . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pickup_date" class="form-label">Pickup Date</label>
                                <input type="datetime-local" class="form-control" id="pickup_date" name="pickup_date" value="<?php echo date('Y-m-d\TH:i', strtotime($booking_details['pickup_date'])); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="return_date" class="form-label">Return Date</label>
                                <input type="datetime-local" class="form-control" id="return_date" name="return_date" value="<?php echo date('Y-m-d\TH:i', strtotime($booking_details['return_date'])); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pickup_location_id" class="form-label">Pickup Location</label>
                                <select class="form-select" id="pickup_location_id" name="pickup_location_id" required>
                                    <?php foreach ($locations as $location): ?>
                                        <option value="<?php echo $location['id']; ?>" <?php echo ($booking_details['pickup_location_id'] == $location['id']) ? 'selected' : ''; ?>>
                                            <?php echo $location['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="return_location_id" class="form-label">Return Location</label>
                                <select class="form-select" id="return_location_id" name="return_location_id" required>
                                    <?php foreach ($locations as $location): ?>
                                        <option value="<?php echo $location['id']; ?>" <?php echo ($booking_details['return_location_id'] == $location['id']) ? 'selected' : ''; ?>>
                                            <?php echo $location['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="total_price" class="form-label">Total Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="total_price" name="total_price" value="<?php echo $booking_details['total_price']; ?>" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" <?php echo ($booking_details['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo ($booking_details['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?php echo ($booking_details['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo ($booking_details['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="payment_status" class="form-label">Payment Status</label>
                                <select class="form-select" id="payment_status" name="payment_status" required>
                                    <option value="pending" <?php echo ($booking_details['payment_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="paid" <?php echo ($booking_details['payment_status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                                    <option value="refunded" <?php echo ($booking_details['payment_status'] == 'refunded') ? 'selected' : ''; ?>>Refunded</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="bookings.php" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Booking</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Booking Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">Booking ID</h6>
                        <p class="mb-0">#<?php echo $booking_details['id']; ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Created At</h6>
                        <p class="mb-0"><?php echo date('F j, Y, g:i a', strtotime($booking_details['created_at'])); ?></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Duration</h6>
                        <p class="mb-0">
                            <?php 
                            $pickup = new DateTime($booking_details['pickup_date']);
                            $return = new DateTime($booking_details['return_date']);
                            $interval = $pickup->diff($return);
                            echo $interval->format('%a days, %h hours');
                            ?>
                        </p>
                    </div>
                    <div>
                        <h6 class="text-muted">Status</h6>
                        <span class="badge bg-<?php 
                            switch($booking_details['status']) {
                                case 'pending': echo 'warning'; break;
                                case 'confirmed': echo 'success'; break;
                                case 'completed': echo 'info'; break;
                                case 'cancelled': echo 'danger'; break;
                                default: echo 'secondary';
                            }
                        ?>"><?php echo ucfirst($booking_details['status']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pickupDateInput = document.getElementById('pickup_date');
    const returnDateInput = document.getElementById('return_date');
    const carSelect = document.getElementById('car_id');
    const totalPriceInput = document.getElementById('total_price');
    
    // Function to calculate total price
    function calculateTotalPrice() {
        const pickupDate = new Date(pickupDateInput.value);
        const returnDate = new Date(returnDateInput.value);
        
        if (pickupDate && returnDate && !isNaN(pickupDate) && !isNaN(returnDate)) {
            // Calculate duration in days
            const durationMs = returnDate - pickupDate;
            const durationDays = Math.ceil(durationMs / (1000 * 60 * 60 * 24));
            
            if (durationDays > 0 && carSelect.value) {
                // Get selected car's price per day
                const selectedOption = carSelect.options[carSelect.selectedIndex];
                const carDetails = selectedOption.textContent;
                
                // Extract price from car details if available, otherwise use default
                let pricePerDay = 50; // Default price
                
                // Calculate total price
                totalPriceInput.value = (durationDays * pricePerDay).toFixed(2);
            }
        }
    }
    
    // Add event listeners
    pickupDateInput.addEventListener('change', calculateTotalPrice);
    returnDateInput.addEventListener('change', calculateTotalPrice);
    carSelect.addEventListener('change', calculateTotalPrice);
});
</script>

<?php include_once 'includes/footer.php'; ?>
