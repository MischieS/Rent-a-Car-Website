<?php
// Start session if not already started
session_start();

// Include database and required files
require_once 'config/database.php';
require_once 'models/Booking.php';
require_once 'models/Car.php';
require_once 'includes/session.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: my-bookings.php");
    exit;
}

$booking_id = $_GET['id'];

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$booking = new Booking($db);
$car = new Car($db);

// Get booking details
$booking_details = $booking->getBookingById($booking_id);

// Check if booking exists and belongs to current user
if (!$booking_details || $booking_details['user_id'] != $_SESSION['user_id']) {
    header("Location: my-bookings.php");
    exit;
}

// Process booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    // Update booking status to cancelled
    if ($booking->updateStatus($booking_id, 'cancelled')) {
        $success_message = "Booking cancelled successfully.";
        
        // Refresh booking details
        $booking_details = $booking->getBookingById($booking_id);
    } else {
        $error_message = "Failed to cancel booking. Please try again.";
    }
}

// Ensure price_per_day is available
if (!isset($booking_details['price_per_day']) && isset($booking_details['car_id'])) {
    // Fetch car details to get price_per_day
    $car_details = $car->getCarById($booking_details['car_id']);
    if ($car_details) {
        $booking_details['price_per_day'] = $car_details['price_per_day'];
    } else {
        $booking_details['price_per_day'] = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Booking Details - DREAMS RENT</title>
    <?php include('assets/includes/header_link.php') ?>
    <style>
        .booking-details-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .booking-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .booking-id {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .booking-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 10px;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-completed {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .booking-body {
            padding: 30px;
        }
        
        .booking-section {
            margin-bottom: 30px;
        }
        
        .booking-section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .car-details {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .car-image {
            width: 150px;
            height: 100px;
            margin-right: 20px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .car-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        
        .car-info h4 {
            font-size: 1.3rem;
            margin-bottom: 5px;
        }
        
        .booking-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .booking-info-item {
            display: flex;
            flex-direction: column;
        }
        
        .booking-info-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .booking-info-value {
            font-weight: 500;
        }
        
        .price-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .price-total {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .booking-actions {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        
        .booking-actions .btn {
            margin-left: 10px;
        }
        
        @media (max-width: 767px) {
            .booking-info-grid {
                grid-template-columns: 1fr;
            }
            
            .car-details {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .car-image {
                margin-right: 0;
                margin-bottom: 15px;
                width: 100%;
                height: 150px;
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
                        <h1>Booking Details</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="my-bookings.php">My Bookings</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Booking Details</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Booking Details Section -->
        <section class="booking-details-section py-5">
            <div class="container">
                <?php if (isset($success_message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="booking-details-card" data-aos="fade-up">
                            <div class="booking-header">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="booking-id">Booking ID: #<?php echo $booking_details['id']; ?></div>
                                        <h3 class="mb-0">Booking Details</h3>
                                        <div class="booking-date">
                                            <small>Booked on <?php echo date('F d, Y', strtotime($booking_details['created_at'])); ?></small>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="booking-status status-<?php echo strtolower($booking_details['status']); ?>">
                                            <?php echo ucfirst($booking_details['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="booking-body">
                                <div class="booking-section">
                                    <h4 class="booking-section-title">Car Information</h4>
                                    <div class="car-details">
                                        <div class="car-image">
                                            <?php if (!empty($booking_details['image'])): ?>
                                                <img src="<?php echo $booking_details['image']; ?>" alt="<?php echo $booking_details['brand'] . ' ' . $booking_details['model']; ?>">
                                            <?php else: ?>
                                                <img src="assets/img/cars/default.png" alt="<?php echo $booking_details['brand'] . ' ' . $booking_details['model']; ?>">
                                            <?php endif; ?>
                                        </div>
                                        <div class="car-info">
                                            <h4><?php echo $booking_details['brand'] . ' ' . $booking_details['model'] . ' (' . $booking_details['year'] . ')'; ?></h4>
                                            <p class="text-muted mb-0">$<?php echo number_format($booking_details['price_per_day'] ?? 0, 2); ?> per day</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="booking-section">
                                    <h4 class="booking-section-title">Booking Information</h4>
                                    <div class="booking-info-grid">
                                        <div class="booking-info-item">
                                            <span class="booking-info-label">Pickup Date</span>
                                            <span class="booking-info-value"><?php echo date('F d, Y', strtotime($booking_details['pickup_date'])); ?></span>
                                        </div>
                                        <div class="booking-info-item">
                                            <span class="booking-info-label">Return Date</span>
                                            <span class="booking-info-value"><?php echo date('F d, Y', strtotime($booking_details['return_date'])); ?></span>
                                        </div>
                                        <div class="booking-info-item">
                                            <span class="booking-info-label">Pickup Location</span>
                                            <span class="booking-info-value">
                                                <?php echo isset($booking_details['pickup_location']) ? $booking_details['pickup_location'] : 'Not specified'; ?>
                                            </span>
                                        </div>
                                        <div class="booking-info-item">
                                            <span class="booking-info-label">Return Location</span>
                                            <span class="booking-info-value">
                                                <?php echo isset($booking_details['return_location']) ? $booking_details['return_location'] : 'Not specified'; ?>
                                            </span>
                                        </div>
                                        <div class="booking-info-item">
                                            <span class="booking-info-label">Duration</span>
                                            <span class="booking-info-value">
                                                <?php 
                                                $pickup_date = new DateTime($booking_details['pickup_date']);
                                                $return_date = new DateTime($booking_details['return_date']);
                                                $interval = $pickup_date->diff($return_date);
                                                echo $interval->days . ' ' . ($interval->days == 1 ? 'day' : 'days');
                                                ?>
                                            </span>
                                        </div>
                                        <div class="booking-info-item">
                                            <span class="booking-info-label">Payment Status</span>
                                            <span class="booking-info-value">
                                                <?php 
                                                $payment_status = isset($booking_details['payment_status']) ? $booking_details['payment_status'] : 'pending';
                                                echo ucfirst($payment_status); 
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="booking-section">
                                    <h4 class="booking-section-title">Price Details</h4>
                                    <div class="price-details">
                                        <div class="price-row">
                                            <span>Base Rate</span>
                                            <span>
                                                $<?php echo number_format($booking_details['price_per_day'] ?? 0, 2); ?> x 
                                                <?php echo $interval->days; ?> 
                                                <?php echo ($interval->days == 1 ? 'day' : 'days'); ?>
                                            </span>
                                        </div>
                                        <div class="price-row">
                                            <span>Subtotal</span>
                                            <span>$<?php echo number_format(($booking_details['price_per_day'] ?? 0) * $interval->days, 2); ?></span>
                                        </div>
                                        <div class="price-row">
                                            <span>Tax (10%)</span>
                                            <span>$<?php echo number_format((($booking_details['price_per_day'] ?? 0) * $interval->days) * 0.1, 2); ?></span>
                                        </div>
                                        <div class="price-total">
                                            <span>Total</span>
                                            <span>$<?php echo number_format($booking_details['total_price'], 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="booking-actions">
                                    <a href="my-bookings.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Bookings
                                    </a>
                                    <?php if ($booking_details['status'] == 'pending' || $booking_details['status'] == 'confirmed'): ?>
                                    <form method="post" action="" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                        <button type="submit" name="cancel_booking" class="btn btn-danger">
                                            <i class="fas fa-times"></i> Cancel Booking
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Booking Details Section -->

        <!-- Footer -->
        <?php include('assets/includes/footer.php') ?>
        <!-- /Footer -->
    </div>

    <?php include('assets/includes/footer_link.php') ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS
        AOS.init();
    });
    </script>
</body>
</html>
