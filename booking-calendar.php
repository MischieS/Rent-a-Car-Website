<?php
// Include necessary files
require_once 'config/database.php';
require_once 'models/Car.php';
require_once 'models/Booking.php';

// Check if car_id is provided
if (!isset($_GET['car_id']) || empty($_GET['car_id'])) {
    header('Location: cars.php');
    exit;
}

$car_id = $_GET['car_id'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create car object
$car = new Car($db);
$car->id = $car_id;
$car_details = $car->readOne();

// Create booking object
$booking = new Booking($db);

// Get unavailable dates
$stmt = $db->prepare("SELECT pickup_date, return_date FROM bookings WHERE car_id = ? AND status != 'cancelled'");
$stmt->execute([$car_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$unavailable_dates = [];
foreach ($bookings as $booking_item) {
    $pickup_date = new DateTime($booking_item['pickup_date']);
    $return_date = new DateTime($booking_item['return_date']);
    
    // Add all dates between pickup and return (inclusive) to unavailable dates
    $current_date = clone $pickup_date;
    while ($current_date <= $return_date) {
        $unavailable_dates[] = $current_date->format('Y-m-d');
        $current_date->modify('+1 day');
    }
}

// Remove duplicates
$unavailable_dates = array_unique($unavailable_dates);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];
    
    // Redirect to booking form
    header("Location: booking-form.php?car_id=$car_id&pickup_date=$pickup_date&return_date=$return_date");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Book a Car - DREAMS RENT</title>
    <?php include('assets/includes/header_link.php') ?>
    <style>
        .calendar-container {
            margin-bottom: 30px;
        }
        .calendar {
            width: 100%;
            border-collapse: collapse;
        }
        .calendar th, .calendar td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .calendar th {
            background-color: #f8f9fa;
        }
        .calendar .today {
            background-color: #e9ecef;
        }
        .calendar .selected {
            background-color: var(--primary-color);
            color: white;
        }
        .calendar .unavailable {
            background-color: #f8d7da;
            color: #721c24;
            text-decoration: line-through;
        }
        .calendar .disabled {
            background-color: #f8f9fa;
            color: #adb5bd;
            cursor: not-allowed;
        }
        .date-display {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .date-display.active {
            background-color: #e9ecef;
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
                        <h1>Book a Car</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="cars.php">Cars</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Book a Car</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Booking Calendar Section -->
        <section class="section">
            <div class="container">
                <div class="row">
                    <!-- Car Details -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Car Details</h3>
                                <div class="car-image mb-3">
                                    <img src="<?php echo !empty($car_details['image']) ? $car_details['image'] : 'assets/img/car-placeholder.jpg'; ?>" alt="<?php echo $car_details['brand'] . ' ' . $car_details['model']; ?>" class="img-fluid rounded">
                                </div>
                                <h4><?php echo $car_details['brand'] . ' ' . $car_details['model']; ?></h4>
                                <p class="text-primary fw-bold">$<?php echo $car_details['price_per_day']; ?> per day</p>
                                <p><?php echo $car_details['description']; ?></p>
                                <div class="car-features">
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <i class="fas fa-car"></i> <?php echo $car_details['year']; ?>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <i class="fas fa-gas-pump"></i> <?php echo $car_details['fuel_type']; ?>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <i class="fas fa-cog"></i> <?php echo $car_details['transmission']; ?>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <i class="fas fa-tag"></i> <?php echo $car_details['category']; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Car Details -->

                    <!-- Calendar -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Select Dates</h3>
                                <p>Please select your pickup and return dates:</p>

                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?car_id=" . $car_id); ?>" method="post">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="pickup_date" class="form-label">Pickup Date</label>
                                            <input type="date" class="form-control" id="pickup_date" name="pickup_date" required min="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="return_date" class="form-label">Return Date</label>
                                            <input type="date" class="form-control" id="return_date" name="return_date" required min="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>

                                    <div class="alert alert-info">
                                        <h5>Unavailable Dates:</h5>
                                        <p>The car is not available on the following dates:</p>
                                        <div style="max-height: 150px; overflow-y: auto;">
                                            <?php if (empty($unavailable_dates)): ?>
                                                <p>No unavailable dates. This car is available for all dates.</p>
                                            <?php else: ?>
                                                <ul>
                                                    <?php foreach ($unavailable_dates as $date): ?>
                                                        <li><?php echo date('F j, Y', strtotime($date)); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <a href="cars.php" class="btn btn-secondary me-2">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Continue to Booking</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /Calendar -->
                </div>
            </div>
        </section>
        <!-- /Booking Calendar Section -->

        <!-- Footer -->
        <?php include('assets/includes/footer.php') ?>
        <!-- /Footer -->
    </div>

    <?php include('assets/includes/footer_link.php') ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const pickupDateInput = document.getElementById('pickup_date');
        const returnDateInput = document.getElementById('return_date');
        const unavailableDates = <?php echo json_encode($unavailable_dates); ?>;
        
        // Set min date for both inputs to today
        const today = new Date().toISOString().split('T')[0];
        pickupDateInput.min = today;
        returnDateInput.min = today;
        
        // When pickup date changes, update return date min
        pickupDateInput.addEventListener('change', function() {
            returnDateInput.min = this.value;
            
            // If return date is before new pickup date, update it
            if (returnDateInput.value && returnDateInput.value < this.value) {
                returnDateInput.value = this.value;
            }
            
            validateDates();
        });
        
        // When return date changes, validate
        returnDateInput.addEventListener('change', function() {
            validateDates();
        });
        
        // Function to validate selected dates against unavailable dates
        function validateDates() {
            const pickupDate = pickupDateInput.value;
            const returnDate = returnDateInput.value;
            
            if (!pickupDate || !returnDate) return;
            
            // Check if any date in the range is unavailable
            let currentDate = new Date(pickupDate);
            const endDate = new Date(returnDate);
            let hasUnavailableDate = false;
            
            while (currentDate <= endDate) {
                const dateString = currentDate.toISOString().split('T')[0];
                if (unavailableDates.includes(dateString)) {
                    hasUnavailableDate = true;
                    break;
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            if (hasUnavailableDate) {
                alert('Your selected date range includes unavailable dates. Please select different dates.');
                pickupDateInput.value = '';
                returnDateInput.value = '';
            }
        }
    });
    </script>
</body>
</html>
