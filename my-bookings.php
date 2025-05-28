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

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$booking = new Booking($db);
$car = new Car($db);

// Get user's bookings
$stmt = $booking->getBookingsByUser($_SESSION['user_id']);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    // Get booking details
    $booking_details = $booking->getBookingById($booking_id);
    
    // Check if booking belongs to current user
    if ($booking_details && $booking_details['user_id'] == $_SESSION['user_id']) {
        // Update booking status to cancelled
        if ($booking->updateStatus($booking_id, 'cancelled')) {
            $success_message = "Booking cancelled successfully.";
            
            // Refresh bookings list
            $stmt = $booking->getBookingsByUser($_SESSION['user_id']);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error_message = "Failed to cancel booking. Please try again.";
        }
    } else {
        $error_message = "Invalid booking.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>My Bookings - DREAMS RENT</title>
    <?php include('assets/includes/header_link.php') ?>
    <style>
        .booking-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        
        .booking-card:hover {
            transform: translateY(-5px);
        }
        
        .booking-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .booking-id {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .booking-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
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
            padding: 20px;
        }
        
        .booking-car {
            display: flex;
            margin-bottom: 20px;
        }
        
        .booking-car-img {
            width: 120px;
            height: 80px;
            margin-right: 15px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .booking-car-img img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        
        .booking-car-info h5 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        
        .booking-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .booking-detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .booking-detail-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 3px;
        }
        
        .booking-detail-value {
            font-weight: 500;
        }
        
        .booking-footer {
            padding: 15px 20px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .booking-price {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .booking-actions .btn {
            margin-left: 10px;
        }
        
        @media (max-width: 767px) {
            .booking-details {
                grid-template-columns: 1fr;
            }
        }
        
        .no-bookings {
            text-align: center;
            padding: 50px 0;
        }
        
        .no-bookings i {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        .no-bookings h4 {
            margin-bottom: 15px;
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
                        <h1>My Bookings</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">My Bookings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Bookings Section -->
        <section class="bookings-section py-5">
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
                    <div class="col-12">
                        <div class="section-title mb-4">
                            <h2>Your Bookings</h2>
                            <p>View and manage your car rental bookings</p>
                        </div>
                    </div>
                </div>
                
                <?php if (empty($bookings)): ?>
                <div class="no-bookings">
                    <i class="fas fa-calendar-times"></i>
                    <h4>No Bookings Found</h4>
                    <p>You haven't made any bookings yet.</p>
                    <a href="cars.php" class="btn btn-primary">Browse Cars</a>
                </div>
                <?php else: ?>
                <div class="row">
                    <?php foreach ($bookings as $booking): ?>
                    <div class="col-lg-6">
                        <div class="booking-card" data-aos="fade-up">
                            <div class="booking-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="booking-id">
                                        Booking ID: #<?php echo $booking['id']; ?>
                                    </div>
                                    <div class="booking-date">
                                        <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div>
                                        <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="booking-body">
                                <div class="booking-car">
                                    <div class="booking-car-img">
                                        <?php if (!empty($booking['car_image'])): ?>
                                            <img src="<?php echo $booking['car_image']; ?>" alt="<?php echo $booking['brand'] . ' ' . $booking['model']; ?>">
                                        <?php else: ?>
                                            <img src="assets/img/cars/default.png" alt="<?php echo $booking['brand'] . ' ' . $booking['model']; ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="booking-car-info">
                                        <h5><?php echo $booking['brand'] . ' ' . $booking['model']; ?></h5>
                                        <p class="text-muted mb-0">
                                            <?php 
                                            $pickup_date = new DateTime($booking['pickup_date']);
                                            $return_date = new DateTime($booking['return_date']);
                                            $interval = $pickup_date->diff($return_date);
                                            echo $interval->days . ' ' . ($interval->days == 1 ? 'day' : 'days');
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="booking-details">
                                    <div class="booking-detail-item">
                                        <span class="booking-detail-label">Pickup Date</span>
                                        <span class="booking-detail-value">
                                            <?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?>
                                        </span>
                                    </div>
                                    <div class="booking-detail-item">
                                        <span class="booking-detail-label">Return Date</span>
                                        <span class="booking-detail-value">
                                            <?php echo date('M d, Y', strtotime($booking['return_date'])); ?>
                                        </span>
                                    </div>
                                    <div class="booking-detail-item">
                                        <span class="booking-detail-label">Pickup Location</span>
                                        <span class="booking-detail-value">
                                            <?php echo isset($booking['pickup_location_name']) ? $booking['pickup_location_name'] : 'Not specified'; ?>
                                        </span>
                                    </div>
                                    <div class="booking-detail-item">
                                        <span class="booking-detail-label">Return Location</span>
                                        <span class="booking-detail-value">
                                            <?php echo isset($booking['return_location_name']) ? $booking['return_location_name'] : 'Not specified'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="booking-footer">
                                <div class="booking-price">
                                    $<?php echo number_format($booking['total_price'], 2); ?>
                                </div>
                                <div class="booking-actions">
                                    <a href="booking-details.php?id=<?php echo $booking['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <?php if ($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
                                    <form method="post" action="" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" name="cancel_booking" class="btn btn-danger btn-sm">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <!-- /Bookings Section -->

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
