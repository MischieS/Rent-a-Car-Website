<?php
// Include database connection
require_once 'config/database.php';
require_once 'models/Booking.php';
include('includes/session.php');

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$booking_id = $_GET['id'];

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize booking object
$booking = new Booking($db);

// Get booking details
$booking_details = $booking->getBookingById($booking_id);

// Check if booking exists and belongs to the current user
if (!$booking_details || $booking_details['user_id'] != $_SESSION['user_id']) {
    header("Location: my-bookings.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Booking Confirmation - DREAMS RENT</title>
    <?php include('assets/includes/header_link.php') ?>
    <style>
        .confirmation-section {
            padding: 80px 0;
            background-color: #f8f9fa;
        }
        
        .confirmation-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            padding: 40px;
            text-align: center;
        }
        
        .confirmation-icon {
            width: 80px;
            height: 80px;
            background-color: #d4edda;
            color: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 2rem;
        }
        
        .confirmation-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #28a745;
        }
        
        .confirmation-message {
            font-size: 1.1rem;
            margin-bottom: 30px;
            color: #666;
        }
        
        .booking-details {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .booking-detail-item {
            margin-bottom: 15px;
        }
        
        .booking-detail-label {
            font-weight: 600;
            color: #333;
        }
        
        .booking-detail-value {
            color: #666;
        }
        
        .booking-id {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        @media (max-width: 767px) {
            .action-buttons {
                flex-direction: column;
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
                        <h1>Booking Confirmation</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Booking Confirmation</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Confirmation Section -->
        <section class="confirmation-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="confirmation-container">
                            <div class="confirmation-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <h2 class="confirmation-title">Booking Confirmed!</h2>
                            <p class="confirmation-message">Thank you for your booking. Your reservation has been successfully confirmed.</p>
                            
                            <div class="booking-id">
                                Booking ID: #<?php echo str_pad($booking_details['id'], 6, '0', STR_PAD_LEFT); ?>
                            </div>
                            
                            <div class="booking-details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="booking-detail-item">
                                            <div class="booking-detail-label">Car</div>
                                            <div class="booking-detail-value"><?php echo $booking_details['car_name']; ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="booking-detail-item">
                                            <div class="booking-detail-label">Status</div>
                                            <div class="booking-detail-value">
                                                <span class="badge bg-warning"><?php echo ucfirst($booking_details['status']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="booking-detail-item">
                                            <div class="booking-detail-label">Pickup Date</div>
                                            <div class="booking-detail-value"><?php echo date('M d, Y', strtotime($booking_details['pickup_date'])); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="booking-detail-item">
                                            <div class="booking-detail-label">Return Date</div>
                                            <div class="booking-detail-value"><?php echo date('M d, Y', strtotime($booking_details['return_date'])); ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="booking-detail-item">
                                            <div class="booking-detail-label">Pickup Location</div>
                                            <div class="booking-detail-value"><?php echo $booking_details['pickup_location']; ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="booking-detail-item">
                                            <div class="booking-detail-label">Return Location</div>
                                            <div class="booking-detail-value"><?php echo $booking_details['return_location']; ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="booking-detail-item">
                                            <div class="booking-detail-label">Total Price</div>
                                            <div class="booking-detail-value">$<?php echo $booking_details['total_price']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <p class="mb-4">A confirmation email has been sent to your email address. You can also view your booking details in your account dashboard.</p>
                            
                            <div class="action-buttons">
                                <a href="my-bookings.php" class="btn btn-primary">View My Bookings</a>
                                <a href="index.php" class="btn btn-outline-secondary">Return to Home</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Confirmation Section -->

        <!-- Footer -->
        <?php include('assets/includes/footer.php') ?>
        <!-- /Footer -->
    </div>

    <?php include('assets/includes/footer_link.php') ?>
</body>
</html>
