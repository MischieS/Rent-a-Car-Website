<?php
// Include session and database connection
include_once '../includes/session.php';
include_once '../config/database.php';
include_once '../models/Booking.php';
include_once '../models/Car.php';
include_once '../models/User.php';

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
    header('Location: bookings.php');
    exit();
}

// Initialize car and user objects
$car = new Car($db);
$car->id = $booking_details['car_id'];
$car->readOne();

$user = new User($db);
$user->id = $booking_details['user_id'];
$user->readOne();

// Calculate rental duration
$pickup_date = new DateTime($booking_details['pickup_date']);
$return_date = new DateTime($booking_details['return_date']);
$interval = $pickup_date->diff($return_date);
$days = $interval->days;
if ($days == 0) $days = 1; // Minimum 1 day
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking #<?php echo $booking_id; ?> - DREAMS RENT</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .booking-header {
            background-color: #343a40;
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
        }
        .booking-body {
            background-color: white;
            padding: 20px;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }
        .booking-info {
            margin-bottom: 20px;
        }
        .booking-info h5 {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .booking-info p {
            margin-bottom: 8px;
        }
        .booking-summary {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
        }
        .booking-total {
            font-size: 18px;
            font-weight: 600;
        }
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .print-logo {
            font-weight: 700;
            font-size: 24px;
        }
        .print-logo span {
            color: #f5a742;
        }
        .print-actions {
            margin-bottom: 20px;
        }
        @media print {
            .print-actions {
                display: none;
            }
            body {
                background-color: white;
            }
            .container {
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="print-actions text-end">
            <button onclick="window.print();" class="btn btn-primary">
                <i class="fas fa-print"></i> Print
            </button>
            <button onclick="window.close();" class="btn btn-secondary">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
        
        <div class="print-header">
            <div class="print-logo">
                DREAMS <span>RENT</span>
            </div>
            <div class="text-end">
                <h5>Booking #<?php echo $booking_id; ?></h5>
                <p class="mb-0">Date: <?php echo date('F d, Y', strtotime($booking_details['created_at'])); ?></p>
            </div>
        </div>
        
        <div class="booking-header">
            <div class="row">
                <div class="col-md-6">
                    <h4>Booking Details</h4>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-<?php 
                        echo ($booking_details['status'] == 'confirmed') ? 'success' : 
                            (($booking_details['status'] == 'pending') ? 'warning' : 
                            (($booking_details['status'] == 'cancelled') ? 'danger' : 'info')); 
                    ?> p-2">
                        <?php echo ucfirst($booking_details['status']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="booking-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="booking-info">
                        <h5>Customer Information</h5>
                        <p><strong>Name:</strong> <?php echo $user->first_name . ' ' . $user->last_name; ?></p>
                        <p><strong>Email:</strong> <?php echo $user->email; ?></p>
                        <p><strong>Phone:</strong> <?php echo $user->phone ?? 'N/A'; ?></p>
                        <p><strong>Address:</strong> <?php echo $user->address ?? 'N/A'; ?></p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="booking-info">
                        <h5>Booking Information</h5>
                        <p><strong>Pickup Date:</strong> <?php echo date('F d, Y', strtotime($booking_details['pickup_date'])); ?></p>
                        <p><strong>Return Date:</strong> <?php echo date('F d, Y', strtotime($booking_details['return_date'])); ?></p>
                        <p><strong>Pickup Location:</strong> <?php echo $booking_details['pickup_location']; ?></p>
                        <p><strong>Return Location:</strong> <?php echo $booking_details['return_location']; ?></p>
                        <p><strong>Duration:</strong> <?php echo $days; ?> day<?php echo ($days > 1) ? 's' : ''; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="booking-info">
                <h5>Vehicle Information</h5>
                <div class="row">
                    <div class="col-md-3">
                        <?php if (!empty($car->image)): ?>
                            <img src="../<?php echo $car->image; ?>" alt="<?php echo $car->brand . ' ' . $car->model; ?>" class="img-fluid rounded">
                        <?php else: ?>
                            <div class="bg-light rounded text-center py-5">
                                <i class="fas fa-car fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-9">
                        <h5><?php echo $car->brand . ' ' . $car->model . ' (' . $car->year . ')'; ?></h5>
                        <p class="mb-1"><strong>License Plate:</strong> <?php echo $car->license_plate ?? 'N/A'; ?></p>
                        <p class="mb-1"><strong>Color:</strong> <?php echo $car->color ?? 'N/A'; ?></p>
                        <p class="mb-1"><strong>Daily Rate:</strong> $<?php echo number_format($car->price_per_day, 2); ?></p>
                        <p class="mb-0"><strong>Category:</strong> <?php echo $car->category ?? 'N/A'; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="booking-info">
                <h5>Payment Summary</h5>
                <div class="booking-summary">
                    <div class="row mb-2">
                        <div class="col-8">
                            <p class="mb-0">Daily Rate:</p>
                        </div>
                        <div class="col-4 text-end">
                            <p class="mb-0">$<?php echo number_format($car->price_per_day, 2); ?></p>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-8">
                            <p class="mb-0">Rental Duration:</p>
                        </div>
                        <div class="col-4 text-end">
                            <p class="mb-0"><?php echo $days; ?> day<?php echo ($days > 1) ? 's' : ''; ?></p>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-8">
                            <p class="mb-0">Subtotal:</p>
                        </div>
                        <div class="col-4 text-end">
                            <p class="mb-0">$<?php echo number_format($car->price_per_day * $days, 2); ?></p>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-8">
                            <p class="mb-0">Tax (10%):</p>
                        </div>
                        <div class="col-4 text-end">
                            <p class="mb-0">$<?php echo number_format(($car->price_per_day * $days) * 0.1, 2); ?></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-8">
                            <p class="booking-total mb-0">Total:</p>
                        </div>
                        <div class="col-4 text-end">
                            <p class="booking-total mb-0">$<?php echo number_format($booking_details['total_price'], 2); ?></p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-8">
                            <p class="mb-0">Payment Status:</p>
                        </div>
                        <div class="col-4 text-end">
                            <span class="badge bg-<?php 
                                echo ($booking_details['payment_status'] == 'paid') ? 'success' : 
                                    (($booking_details['payment_status'] == 'pending') ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo ucfirst($booking_details['payment_status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="booking-info mb-0">
                <h5>Terms & Conditions</h5>
                <p class="small mb-1">1. The vehicle must be returned in the same condition as it was received.</p>
                <p class="small mb-1">2. Any damage to the vehicle will be charged to the customer.</p>
                <p class="small mb-1">3. Late returns will incur additional charges.</p>
                <p class="small mb-1">4. Cancellations must be made at least 24 hours before the pickup time.</p>
                <p class="small mb-0">5. A valid driver's license and credit card are required at pickup.</p>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <p class="small text-muted mb-0">Thank you for choosing DREAMS RENT!</p>
            <p class="small text-muted mb-0">For any questions, please contact us at support@dreamsrent.com</p>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
