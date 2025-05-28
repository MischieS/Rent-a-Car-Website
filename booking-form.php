<?php
// Include database connection
require_once 'config/database.php';
require_once 'models/Car.php';
require_once 'models/Booking.php';
require_once 'models/Location.php';
include('includes/session.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with return URL
    $redirect_url = urlencode("booking-form.php?" . http_build_query($_GET));
    header("Location: login.php?redirect=" . $redirect_url);
    exit;
}

// Check if required parameters are provided
if (!isset($_GET['car_id']) || !isset($_GET['pickup_date']) || !isset($_GET['return_date'])) {
    header("Location: booking.php");
    exit;
}

// Get parameters
$car_id = $_GET['car_id'];
$pickup_date = $_GET['pickup_date'];
$return_date = $_GET['return_date'];
$pickup_location_id = isset($_GET['pickup_location']) ? $_GET['pickup_location'] : '';
$return_location_id = isset($_GET['return_location']) ? $_GET['return_location'] : '';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$car = new Car($db);
$booking = new Booking($db);
$location = new Location($db);

// Get car details
$car->id = $car_id;
$car->readOne();
$car_details = [
    'brand' => $car->brand,
    'model' => $car->model,
    'year' => $car->year,
    'transmission' => $car->transmission,
    'fuel_type' => $car->fuel_type,
    'price_per_day' => $car->price_per_day,
    'image' => $car->image
];

// Get locations from database
$locations = $location->getAllLocations();

// Calculate rental days and total price
$pickup = new DateTime($pickup_date);
$return = new DateTime($return_date);
$interval = $pickup->diff($return);
$days = $interval->days > 0 ? $interval->days : 1;
$total_price = $car->price_per_day * $days;

// Process form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $postal_code = trim($_POST['postal_code']);
    $country = trim($_POST['country']);
    $pickup_location = $_POST['pickup_location'];
    $return_location = $_POST['return_location'];
    $special_requests = trim($_POST['special_requests']);
    
    // Basic validation
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($pickup_location)) $errors[] = "Pickup location is required";
    if (empty($return_location)) $errors[] = "Return location is required";
    
    // If no errors, create booking
    if (empty($errors)) {
        // Set booking properties
        $booking->user_id = $_SESSION['user_id'];
        $booking->car_id = $car_id;
        $booking->pickup_date = $pickup_date;
        $booking->return_date = $return_date;
        $booking->pickup_location_id = $pickup_location;
        $booking->return_location_id = $return_location;
        $booking->total_price = $total_price;
        $booking->status = 'pending';
        $booking->payment_status = 'pending';
        $booking->special_requests = $special_requests;
        
        // Create booking
        if ($booking->create()) {
            $booking_id = $db->lastInsertId();
            
            // Update user information if needed
            // This would be implemented in a User model
            
            // Redirect to confirmation page
            header("Location: booking-confirmation.php?id=" . $booking_id);
            exit;
        } else {
            $errors[] = "Failed to create booking. Please try again.";
        }
    }
}

// Get user details if logged in
$user_details = [];
if (isset($_SESSION['user_id'])) {
    // This would be implemented in a User model
    // For now, we'll use dummy data
    $user_details = [
        'first_name' => isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '',
        'last_name' => isset($_SESSION['last_name']) ? $_SESSION['last_name'] : '',
        'email' => isset($_SESSION['email']) ? $_SESSION['email'] : '',
        'phone' => isset($_SESSION['phone']) ? $_SESSION['phone'] : '',
        'address' => isset($_SESSION['address']) ? $_SESSION['address'] : '',
        'city' => isset($_SESSION['city']) ? $_SESSION['city'] : '',
        'postal_code' => isset($_SESSION['postal_code']) ? $_SESSION['postal_code'] : '',
        'country' => isset($_SESSION['country']) ? $_SESSION['country'] : ''
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Booking Form - DREAMS RENT</title>
    <?php include('assets/includes/header_link.php') ?>
    <style>
        .booking-form-section {
            padding: 60px 0;
            background-color: #f8f9fa;
        }
        
        .booking-form-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }
        
        .booking-summary {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            position: sticky;
            top: 20px;
        }
        
        .booking-summary-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .car-image {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .car-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .car-details {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .car-detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .booking-dates {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .date-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .price-summary {
            margin-bottom: 20px;
        }
        
        .price-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .price-total {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: 600;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .required-field::after {
            content: "*";
            color: #dc3545;
            margin-left: 4px;
        }
        
        @media (max-width: 991px) {
            .booking-summary {
                margin-top: 30px;
                position: static;
            }
        }
    </style>
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
                        <h1>Complete Your Booking</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="booking.php">Cars</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Booking Form</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Booking Form Section -->
        <section class="booking-form-section">
            <div class="container">
                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mb-4">
                    <h5 class="alert-heading">Please fix the following errors:</h5>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Booking Form -->
                    <div class="col-lg-8">
                        <div class="booking-form-container">
                            <h2 class="mb-4">Booking Information</h2>
                            
                            <form method="post" action="">
                                <!-- Personal Information -->
                                <div class="form-section">
                                    <h3 class="form-section-title">Personal Information</h3>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name" class="form-label required-field">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo isset($user_details['first_name']) ? $user_details['first_name'] : ''; ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="last_name" class="form-label required-field">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo isset($user_details['last_name']) ? $user_details['last_name'] : ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label required-field">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($user_details['email']) ? $user_details['email'] : ''; ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label required-field">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo isset($user_details['phone']) ? $user_details['phone'] : ''; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Address Information -->
                                <div class="form-section">
                                    <h3 class="form-section-title">Address Information</h3>
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" value="<?php echo isset($user_details['address']) ? $user_details['address'] : ''; ?>">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" class="form-control" id="city" name="city" value="<?php echo isset($user_details['city']) ? $user_details['city'] : ''; ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="postal_code" class="form-label">Postal Code</label>
                                            <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo isset($user_details['postal_code']) ? $user_details['postal_code'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" class="form-control" id="country" name="country" value="<?php echo isset($user_details['country']) ? $user_details['country'] : ''; ?>">
                                    </div>
                                </div>
                                
                                <!-- Booking Details -->
                                <div class="form-section">
                                    <h3 class="form-section-title">Booking Details</h3>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="pickup_location" class="form-label required-field">Pickup Location</label>
                                            <select class="form-select" id="pickup_location" name="pickup_location" required>
                                                <option value="">Select pickup location</option>
                                                <?php foreach ($locations as $loc): ?>
                                                <option value="<?php echo $loc['id']; ?>" <?php echo ($pickup_location_id == $loc['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $loc['name']; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="return_location" class="form-label required-field">Return Location</label>
                                            <select class="form-select" id="return_location" name="return_location" required>
                                                <option value="">Select return location</option>
                                                <?php foreach ($locations as $loc): ?>
                                                <option value="<?php echo $loc['id']; ?>" <?php echo ($return_location_id == $loc['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $loc['name']; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="special_requests" class="form-label">Special Requests</label>
                                        <textarea class="form-control" id="special_requests" name="special_requests" rows="3" placeholder="Any special requests or requirements?"></textarea>
                                    </div>
                                </div>
                                
                                <!-- Terms and Conditions -->
                                <div class="form-section">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">Complete Booking</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /Booking Form -->
                    
                    <!-- Booking Summary -->
                    <div class="col-lg-4">
                        <div class="booking-summary">
                            <h3 class="booking-summary-title">Booking Summary</h3>
                            
                            <!-- Car Details -->
                            <div class="car-details">
                                <img src="<?php echo !empty($car->image) ? $car->image : 'assets/img/cars/default.png'; ?>" alt="<?php echo $car->brand . ' ' . $car->model; ?>" class="car-image">
                                <h4 class="car-name"><?php echo $car->brand . ' ' . $car->model; ?></h4>
                                <div class="car-detail-item">
                                    <span>Year:</span>
                                    <span><?php echo $car->year; ?></span>
                                </div>
                                <div class="car-detail-item">
                                    <span>Transmission:</span>
                                    <span><?php echo $car->transmission; ?></span>
                                </div>
                                <div class="car-detail-item">
                                    <span>Fuel Type:</span>
                                    <span><?php echo $car->fuel_type; ?></span>
                                </div>
                            </div>
                            
                            <!-- Booking Dates -->
                            <div class="booking-dates">
                                <div class="date-item">
                                    <span>Pickup Date:</span>
                                    <span><?php echo date('M d, Y', strtotime($pickup_date)); ?></span>
                                </div>
                                <div class="date-item">
                                    <span>Return Date:</span>
                                    <span><?php echo date('M d, Y', strtotime($return_date)); ?></span>
                                </div>
                                <div class="date-item">
                                    <span>Duration:</span>
                                    <span><?php echo $days; ?> day<?php echo $days > 1 ? 's' : ''; ?></span>
                                </div>
                            </div>
                            
                            <!-- Price Summary -->
                            <div class="price-summary">
                                <div class="price-item">
                                    <span>Daily Rate:</span>
                                    <span>$<?php echo $car->price_per_day; ?></span>
                                </div>
                                <div class="price-item">
                                    <span>Number of Days:</span>
                                    <span><?php echo $days; ?></span>
                                </div>
                                <div class="price-total">
                                    <span>Total:</span>
                                    <span>$<?php echo $total_price; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Booking Summary -->
                </div>
            </div>
        </section>
        <!-- /Booking Form Section -->

        <!-- Terms Modal -->
        <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6>1. Rental Agreement</h6>
                        <p>By booking a vehicle, you agree to the terms and conditions of the rental agreement.</p>
                        
                        <h6>2. Driver Requirements</h6>
                        <p>All drivers must be at least 21 years of age and possess a valid driver's license.</p>
                        
                        <h6>3. Payment</h6>
                        <p>Full payment is required at the time of pickup. We accept major credit cards.</p>
                        
                        <h6>4. Cancellation Policy</h6>
                        <p>Cancellations made 48 hours or more before the pickup time will receive a full refund. Cancellations made less than 48 hours before the pickup time will be charged a one-day rental fee.</p>
                        
                        <h6>5. Insurance</h6>
                        <p>Basic insurance is included in the rental price. Additional coverage options are available at pickup.</p>
                        
                        <h6>6. Fuel Policy</h6>
                        <p>Vehicles are provided with a full tank of fuel and should be returned with a full tank. If the vehicle is not returned with a full tank, a refueling fee will be charged.</p>
                        
                        <h6>7. Late Returns</h6>
                        <p>Late returns will be charged at an hourly rate of 1/5 of the daily rate for up to 5 hours, after which a full day's rental will be charged.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Terms Modal -->

        <!-- Footer -->
        <?php include('assets/includes/footer.php') ?>
        <!-- /Footer -->
    </div>

    <?php include('assets/includes/footer_link.php') ?>
</body>
</html>
