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

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: bookings.php');
    exit();
}

$booking_id = $_GET['id'];

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize booking object
$booking = new Booking($db);
$booking_details = $booking->getBookingById($booking_id);

// If booking not found, redirect to bookings page
if (!$booking_details) {
    header('Location: bookings.php');
    exit();
}

// Initialize car and user objects
$car = new Car($db);
$car->id = $booking_details['car_id'];
$car_details = $car->readOne();

$user = new User($db);
$user->id = $booking_details['user_id'];
$user_details = $user->getUserById($user->id);

// Generate PDF using TCPDF library
// If TCPDF is not available, we'll create a simple HTML export
// that can be printed to PDF using the browser's print function

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="booking-' . $booking_id . '.pdf"');

// If TCPDF is available, use it to generate PDF
if (class_exists('TCPDF')) {
    // TCPDF code would go here
    // For now, we'll just output HTML that can be printed to PDF
    echo "TCPDF library not found. Please install TCPDF to generate PDF exports.";
    exit();
}

// If TCPDF is not available, output HTML that can be printed to PDF
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking #<?php echo $booking_id; ?> - PDF Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .booking-info {
            margin-bottom: 30px;
        }
        .booking-info h2 {
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .car-info {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-completed {
            background-color: #cce5ff;
            color: #004085;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.8em;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Booking Confirmation</h1>
            <p>Booking #<?php echo $booking_id; ?></p>
        </div>
        
        <div class="booking-info">
            <h2>Booking Details</h2>
            <div class="info-grid">
                <div>
                    <p><strong>Booking Date:</strong> <?php echo date('F d, Y', strtotime($booking_details['created_at'])); ?></p>
                    <p><strong>Pickup Date:</strong> <?php echo date('F d, Y', strtotime($booking_details['pickup_date'])); ?></p>
                    <p><strong>Return Date:</strong> <?php echo date('F d, Y', strtotime($booking_details['return_date'])); ?></p>
                    <p><strong>Pickup Location:</strong> <?php echo $booking_details['pickup_location']; ?></p>
                    <p><strong>Return Location:</strong> <?php echo $booking_details['return_location']; ?></p>
                </div>
                <div>
                    <p><strong>Status:</strong> 
                        <span class="status status-<?php echo strtolower($booking_details['status']); ?>">
                            <?php echo ucfirst($booking_details['status']); ?>
                        </span>
                    </p>
                    <p><strong>Payment Status:</strong> 
                        <span class="status status-<?php echo ($booking_details['payment_status'] == 'paid') ? 'confirmed' : (($booking_details['payment_status'] == 'pending') ? 'pending' : 'cancelled'); ?>">
                            <?php echo ucfirst($booking_details['payment_status']); ?>
                        </span>
                    </p>
                    <p><strong>Total Price:</strong> $<?php echo number_format($booking_details['total_price'], 2); ?></p>
                </div>
            </div>
        </div>
        
        <div class="customer-info">
            <h2>Customer Information</h2>
            <p><strong>Name:</strong> <?php echo $user_details['first_name'] . ' ' . $user_details['last_name']; ?></p>
            <p><strong>Email:</strong> <?php echo $user_details['email']; ?></p>
            <p><strong>Phone:</strong> <?php echo $user_details['phone'] ?? 'N/A'; ?></p>
            <p><strong>Address:</strong> <?php echo $user_details['address'] ?? 'N/A'; ?></p>
        </div>
        
        <div class="car-info">
            <h2>Vehicle Information</h2>
            <table>
                <tr>
                    <th>Vehicle</th>
                    <th>License Plate</th>
                    <th>Color</th>
                    <th>Daily Rate</th>
                </tr>
                <tr>
                    <td><?php echo $car_details['brand'] . ' ' . $car_details['model'] . ' (' . $car_details['year'] . ')'; ?></td>
                    <td><?php echo $car_details['license_plate'] ?? 'N/A'; ?></td>
                    <td><?php echo $car_details['color'] ?? 'N/A'; ?></td>
                    <td>$<?php echo number_format($car_details['price_per_day'], 2); ?></td>
                </tr>
            </table>
        </div>
        
        <div class="terms">
            <h2>Terms and Conditions</h2>
            <p>1. The vehicle must be returned in the same condition as it was received.</p>
            <p>2. Any damage to the vehicle will be charged to the customer.</p>
            <p>3. Late returns will incur additional charges.</p>
            <p>4. Cancellations must be made at least 24 hours before the pickup time.</p>
        </div>
        
        <div class="footer">
            <p>This is an automatically generated document. Thank you for choosing our service.</p>
            <p>Generated on: <?php echo date('F d, Y h:i A'); ?></p>
        </div>
    </div>
    
    <script>
        // Auto-print when the page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
