<?php
// Include database connection
require_once 'config/database.php';
require_once 'models/Car.php';
require_once 'models/Booking.php';
require_once 'models/Location.php';
include('includes/session.php');

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize car and booking objects
$car = new Car($db);
$booking = new Booking($db);

// Initialize location object
$location = new Location($db);
$locations = $location->getAllLocations();

// Get filter parameters
$pickup_date = isset($_GET['pickup_date']) ? $_GET['pickup_date'] : date('Y-m-d');
$return_date = isset($_GET['return_date']) ? $_GET['return_date'] : date('Y-m-d', strtotime('+3 days'));
$pickup_location = isset($_GET['pickup_location']) ? $_GET['pickup_location'] : '';
$return_location = isset($_GET['return_location']) ? $_GET['return_location'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
$fuel_type = isset($_GET['fuel_type']) ? $_GET['fuel_type'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// Get all cars
$stmt = $car->read();
$cars = [];
$available_cars = [];

// Get all bookings for the selected date range
try {
    $existing_bookings = $booking->getBookingsForDateRange($pickup_date, $return_date);
    $booked_car_ids = [];

    // Extract car IDs that are already booked for the selected date range
    foreach ($existing_bookings as $booking_item) {
        if (isset($booking_item['car_id'])) {
            $booked_car_ids[] = $booking_item['car_id'];
        }
    }

    // Process cars and check availability
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cars[] = $row;
        
        // Check if car is available (not in booked_car_ids)
        if (!in_array($row['id'], $booked_car_ids)) {
            // Apply filters
            $matches_filters = true;
            
            if (!empty($category) && $row['category_id'] != $category) {
                $matches_filters = false;
            }
            
            if (!empty($transmission) && $row['transmission'] != $transmission) {
                $matches_filters = false;
            }
            
            if (!empty($fuel_type) && $row['fuel_type'] != $fuel_type) {
                $matches_filters = false;
            }
            
            if (!empty($min_price) && $row['price_per_day'] < $min_price) {
                $matches_filters = false;
            }
            
            if (!empty($max_price) && $row['price_per_day'] > $max_price) {
                $matches_filters = false;
            }
            
            if (!empty($brand) && stripos($row['brand'], $brand) === false) {
                $matches_filters = false;
            }
            
            if (!empty($year) && $row['year'] != $year) {
                $matches_filters = false;
            }
            
            if ($matches_filters) {
                $available_cars[] = $row;
            }
        }
    }
} catch (Exception $e) {
    // Handle error
    error_log("Error in booking.php: " . $e->getMessage());
    $available_cars = [];
}

// Get unique brands, years, and categories for filters
$brands = [];
$years = [];
$categories = $car->getAllCategories();
$fuel_types = ['Petrol', 'Diesel', 'Hybrid', 'Electric'];
$transmissions = ['Automatic', 'Manual'];

foreach ($cars as $car_item) {
    if (!in_array($car_item['brand'], $brands)) {
        $brands[] = $car_item['brand'];
    }
    if (!in_array($car_item['year'], $years)) {
        $years[] = $car_item['year'];
    }
}

// Sort filter options
sort($brands);
rsort($years);

// Calculate price range
$price_min = 0;
$price_max = 0;

if (!empty($cars)) {
    $price_min = min(array_column($cars, 'price_per_day'));
    $price_max = max(array_column($cars, 'price_per_day'));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Car Booking - DREAMS RENT</title>
    <?php include('assets/includes/header_link.php') ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="assets/css/car-listing.css">
    <style>
        /* Additional Booking Page Styles */
        .filter-sidebar {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .filter-header {
            margin-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
        }
        
        .filter-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        
        .filter-widget {
            margin-bottom: 20px;
        }
        
        .filter-actions {
            margin-top: 30px;
        }
        
        .car-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .car-card {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #fff;
        }
        
        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        
        .car-card-img {
            position: relative;
            height: 180px;
            overflow: hidden;
        }
        
        .car-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .car-category {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0,0,0,0.6);
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .car-card-body {
            padding: 15px;
        }
        
        .car-name-price {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .car-name {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }
        
        .car-price {
            text-align: right;
        }
        
        .car-price .amount {
            font-size: 18px;
            font-weight: 700;
            color: #ff6b6b;
        }
        
        .car-price .duration {
            font-size: 12px;
            color: #6c757d;
        }
        
        .car-features {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 14px;
        }
        
        .feature-item i {
            margin-bottom: 5px;
            color: #6c757d;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            font-size: 14px;
        }
        
        .detail-item i {
            margin-right: 8px;
            color: #6c757d;
        }
        
        .car-description {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .booking-summary {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }
        
        .summary-value {
            color: #ff6b6b;
        }
        
        .booking-info-bar {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .no-results {
            grid-column: 1 / -1;
        }
        
        /* Calendar Modal Styles */
        .calendar-modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        
        .calendar-modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }
        
        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .calendar-container {
            margin-top: 20px;
        }
        
        .calendar-footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        
        /* Flatpickr customization */
        .flatpickr-day.flatpickr-disabled, 
        .flatpickr-day.flatpickr-disabled:hover {
            background-color: #ffcccc !important;
            text-decoration: line-through;
            color: #999 !important;
        }
        
        .date-range-selected {
            background-color: #e6f7ff !important;
        }
        
        @media (max-width: 768px) {
            .car-grid {
                grid-template-columns: 1fr;
            }
            
            .calendar-modal-content {
                width: 95%;
                margin: 5% auto;
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
                        <h1>Available Cars</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Booking</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Car Listing Section -->
        <section class="car-listing-section">
            <div class="container">
                <div class="row">
                    <!-- Filter Sidebar -->
                    <div class="col-lg-3">
                        <div class="filter-sidebar">
                            <div class="filter-header">
                                <h4>Find Your Perfect Car</h4>
                            </div>
                            
                            <form id="filterForm" method="GET" action="booking.php">
                                <!-- Date Range Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title">Booking Dates</h4>
                                    <div class="filter-body">
                                        <div class="mb-3">
                                            <label for="pickup_date" class="form-label">Pickup Date</label>
                                            <input type="text" class="form-control date-picker" id="pickup_date" name="pickup_date" value="<?php echo $pickup_date; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="return_date" class="form-label">Return Date</label>
                                            <input type="text" class="form-control date-picker" id="return_date" name="return_date" value="<?php echo $return_date; ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title">Locations</h4>
                                    <div class="filter-body">
                                        <div class="mb-3">
                                            <label for="pickup_location" class="form-label">Pickup Location</label>
                                            <select class="form-select" id="pickup_location" name="pickup_location">
                                                <option value="">Any Location</option>
                                                <?php foreach ($locations as $loc): ?>
                                                <option value="<?php echo $loc['id']; ?>" <?php echo ($pickup_location == $loc['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $loc['name']; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="return_location" class="form-label">Return Location</label>
                                            <select class="form-select" id="return_location" name="return_location">
                                                <option value="">Any Location</option>
                                                <?php foreach ($locations as $loc): ?>
                                                <option value="<?php echo $loc['id']; ?>" <?php echo ($return_location == $loc['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $loc['name']; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Category Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title">Car Category</h4>
                                    <div class="filter-body">
                                        <select class="form-select" id="category" name="category">
                                            <option value="">Any Category</option>
                                            <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo ($category == $cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo $cat['name']; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Brand Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title">Brand</h4>
                                    <div class="filter-body">
                                        <select class="form-select" id="brand" name="brand">
                                            <option value="">Any Brand</option>
                                            <?php foreach ($brands as $b): ?>
                                            <option value="<?php echo $b; ?>" <?php echo ($brand == $b) ? 'selected' : ''; ?>>
                                                <?php echo $b; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Transmission Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title">Transmission</h4>
                                    <div class="filter-body">
                                        <select class="form-select" id="transmission" name="transmission">
                                            <option value="">Any Transmission</option>
                                            <?php foreach ($transmissions as $t): ?>
                                            <option value="<?php echo $t; ?>" <?php echo ($transmission == $t) ? 'selected' : ''; ?>>
                                                <?php echo $t; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Fuel Type Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title">Fuel Type</h4>
                                    <div class="filter-body">
                                        <select class="form-select" id="fuel_type" name="fuel_type">
                                            <option value="">Any Fuel Type</option>
                                            <?php foreach ($fuel_types as $ft): ?>
                                            <option value="<?php echo $ft; ?>" <?php echo ($fuel_type == $ft) ? 'selected' : ''; ?>>
                                                <?php echo $ft; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Year Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title">Year</h4>
                                    <div class="filter-body">
                                        <select class="form-select" id="year" name="year">
                                            <option value="">Any Year</option>
                                            <?php foreach ($years as $y): ?>
                                            <option value="<?php echo $y; ?>" <?php echo ($year == $y) ? 'selected' : ''; ?>>
                                                <?php echo $y; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Price Range Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title">Price Range (per day)</h4>
                                    <div class="filter-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="min_price" class="form-label">Min ($)</label>
                                                <input type="number" class="form-control" id="min_price" name="min_price" min="<?php echo $price_min; ?>" max="<?php echo $price_max; ?>" value="<?php echo $min_price ? $min_price : $price_min; ?>">
                                            </div>
                                            <div class="col-6">
                                                <label for="max_price" class="form-label">Max ($)</label>
                                                <input type="number" class="form-control" id="max_price" name="max_price" min="<?php echo $price_min; ?>" max="<?php echo $price_max; ?>" value="<?php echo $max_price ? $max_price : $price_max; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-actions">
                                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                                    <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="resetFilters">Reset Filters</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /Filter Sidebar -->

                    <!-- Car Listing -->
                    <div class="col-lg-9">
                        <div class="booking-info-bar mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h4 class="mb-0">Available Cars: <span class="text-primary"><?php echo count($available_cars); ?></span></h4>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p class="mb-0">Booking Period: <span class="fw-bold"><?php echo date('M d, Y', strtotime($pickup_date)); ?></span> to <span class="fw-bold"><?php echo date('M d, Y', strtotime($return_date)); ?></span></p>
                                </div>
                            </div>
                        </div>

                        <?php if (empty($available_cars)): ?>
                        <div class="no-results">
                            <div class="alert alert-info">
                                <h5 class="alert-heading">No cars available!</h5>
                                <p>No cars match your criteria or are available for the selected dates. Please try different dates or adjust your filters.</p>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="car-grid" id="carGrid">
                            <?php foreach ($available_cars as $car_item): 
                                // Calculate rental days
                                $pickup = new DateTime($pickup_date);
                                $return = new DateTime($return_date);
                                $interval = $pickup->diff($return);
                                $days = $interval->days > 0 ? $interval->days : 1;
                                
                                // Calculate total price
                                $total_price = $car_item['price_per_day'] * $days;
                                
                                // Get category name
                                $category_name = '';
                                foreach ($categories as $cat) {
                                    if ($cat['id'] == $car_item['category_id']) {
                                        $category_name = $cat['name'];
                                        break;
                                    }
                                }
                                
                                // Handle image
                                $image_url = !empty($car_item['image']) ? $car_item['image'] : 'assets/img/cars/default.png';
                            ?>
                            <div class="car-card">
                                <div class="car-card-img">
                                    <img src="<?php echo $image_url; ?>" alt="<?php echo $car_item['brand'] . ' ' . $car_item['model']; ?>" class="img-fluid">
                                    <span class="car-category"><?php echo $category_name; ?></span>
                                </div>
                                <div class="car-card-body">
                                    <div class="car-name-price">
                                        <h3 class="car-name"><?php echo $car_item['brand'] . ' ' . $car_item['model']; ?></h3>
                                        <div class="car-price">
                                            <span class="amount">$<?php echo $car_item['price_per_day']; ?></span>
                                            <span class="duration">/ day</span>
                                        </div>
                                    </div>
                                    <div class="car-features">
                                        <div class="feature-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span><?php echo $car_item['year']; ?></span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-cogs"></i>
                                            <span><?php echo $car_item['transmission']; ?></span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-gas-pump"></i>
                                            <span><?php echo $car_item['fuel_type']; ?></span>
                                        </div>
                                    </div>
                                    <div class="car-details mt-3">
                                        <div class="row">
                                            <?php if (isset($car_item['seats']) && !empty($car_item['seats'])): ?>
                                            <div class="col-6 mb-2">
                                                <div class="detail-item">
                                                    <i class="fas fa-users"></i>
                                                    <span><?php echo $car_item['seats']; ?> Seats</span>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (isset($car_item['color']) && !empty($car_item['color'])): ?>
                                            <div class="col-6 mb-2">
                                                <div class="detail-item">
                                                    <i class="fas fa-palette"></i>
                                                    <span><?php echo $car_item['color']; ?></span>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (isset($car_item['description']) && !empty($car_item['description'])): ?>
                                    <div class="car-description mt-2">
                                        <p><?php echo substr($car_item['description'], 0, 100) . (strlen($car_item['description']) > 100 ? '...' : ''); ?></p>
                                    </div>
                                    <?php endif; ?>
                                    <div class="booking-summary mt-3">
                                        <div class="summary-item">
                                            <span class="summary-label">Total for <?php echo $days; ?> days:</span>
                                            <span class="summary-value fw-bold">$<?php echo $total_price; ?></span>
                                        </div>
                                    </div>
                                    <a href="car-details.php?id=<?php echo $car_item['id']; ?>&pickup_date=<?php echo $pickup_date; ?>&return_date=<?php echo $return_date; ?>&pickup_location=<?php echo $pickup_location; ?>&return_location=<?php echo $return_location; ?>" class="btn btn-outline-primary w-100 mt-2">View Details</a>
                                    <button type="button" class="btn btn-primary w-100 mt-2 book-now-btn" 
                                        data-car-id="<?php echo $car_item['id']; ?>" 
                                        data-car-name="<?php echo $car_item['brand'] . ' ' . $car_item['model']; ?>"
                                        data-car-price="<?php echo $car_item['price_per_day']; ?>">
                                        Book Now
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- /Car Listing -->
                </div>
            </div>
        </section>
        <!-- /Car Listing Section -->

        <!-- Calendar Modal -->
        <div id="calendarModal" class="calendar-modal">
            <div class="calendar-modal-content">
                <span class="close-modal">&times;</span>
                <h4 id="modalCarTitle">Select Booking Dates</h4>
                <p class="text-muted">Please select your pickup and return dates. Unavailable dates are marked in red.</p>
                
                <div class="calendar-container">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="modal_pickup_date" class="form-label">Pickup Date</label>
                            <input type="text" class="form-control" id="modal_pickup_date" placeholder="Select date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="modal_return_date" class="form-label">Return Date</label>
                            <input type="text" class="form-control" id="modal_return_date" placeholder="Select date">
                        </div>
                    </div>
                </div>
                
                <div class="calendar-footer">
                    <button type="button" class="btn btn-outline-secondary" id="cancelBooking">Cancel</button>
                    <button type="button" class="btn btn-primary" id="continueBooking" disabled>Continue to Booking</button>
                </div>
            </div>
        </div>
        <!-- /Calendar Modal -->

        <!-- Footer -->
        <?php include('assets/includes/footer.php') ?>
        <!-- /Footer -->
    </div>

    <?php include('assets/includes/footer_link.php') ?>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS
        AOS.init();
        
        // Initialize date pickers for filter
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        const pickupDatePicker = flatpickr("#pickup_date", {
            minDate: "today",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                // Update return date min date
                returnDatePicker.set("minDate", dateStr);
                
                // If return date is before pickup date, update it
                const returnDate = new Date(returnDatePicker.selectedDates[0]);
                const pickupDate = new Date(selectedDates[0]);
                
                if (returnDate < pickupDate) {
                    const newReturnDate = new Date(pickupDate);
                    newReturnDate.setDate(newReturnDate.getDate() + 1);
                    returnDatePicker.setDate(newReturnDate);
                }
            }
        });
        
        const returnDatePicker = flatpickr("#return_date", {
            minDate: tomorrow,
            dateFormat: "Y-m-d"
        });
        
        // Reset filters button
        document.getElementById('resetFilters').addEventListener('click', function() {
            window.location.href = 'booking.php';
        });

        // Calendar Modal
        const modal = document.getElementById("calendarModal");
        const closeBtn = document.querySelector(".close-modal");
        const cancelBtn = document.getElementById("cancelBooking");
        const continueBtn = document.getElementById("continueBooking");
        const bookNowBtns = document.querySelectorAll(".book-now-btn");
        
        let currentCarId = null;
        let currentCarPrice = 0;
        let modalPickupDate = null;
        let modalReturnDate = null;
        
        // Initialize modal date pickers
        const modalPickupDatePicker = flatpickr("#modal_pickup_date", {
            minDate: "today",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                modalPickupDate = dateStr;
                modalReturnDatePicker.set("minDate", dateStr);
                
                // If return date is before pickup date, update it
                if (modalReturnDate) {
                    const returnDate = new Date(modalReturnDate);
                    const pickupDate = new Date(dateStr);
                    
                    if (returnDate < pickupDate) {
                        const newReturnDate = new Date(pickupDate);
                        newReturnDate.setDate(newReturnDate.getDate() + 1);
                        modalReturnDatePicker.setDate(newReturnDate);
                    }
                }
                
                updateContinueButton();
            }
        });
        
        const modalReturnDatePicker = flatpickr("#modal_return_date", {
            minDate: tomorrow,
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                modalReturnDate = dateStr;
                updateContinueButton();
            }
        });
        
        // Open modal when Book Now is clicked
        bookNowBtns.forEach(btn => {
            btn.addEventListener("click", function() {
                currentCarId = this.getAttribute("data-car-id");
                currentCarPrice = this.getAttribute("data-car-price");
                const carName = this.getAttribute("data-car-name");
                
                document.getElementById("modalCarTitle").textContent = "Book " + carName;
                
                // Reset modal values
                modalPickupDate = null;
                modalReturnDate = null;
                modalPickupDatePicker.clear();
                modalReturnDatePicker.clear();
                continueBtn.disabled = true;
                
                // Get unavailable dates for this car
                fetchUnavailableDates(currentCarId);
                
                modal.style.display = "block";
            });
        });
        
        // Close modal
        closeBtn.addEventListener("click", function() {
            modal.style.display = "none";
        });
        
        cancelBtn.addEventListener("click", function() {
            modal.style.display = "none";
        });
        
        // Close modal when clicking outside
        window.addEventListener("click", function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });
        
        // Continue to booking form
        continueBtn.addEventListener("click", function() {
            if (modalPickupDate && modalReturnDate) {
                window.location.href = `booking-form.php?car_id=${currentCarId}&pickup_date=${modalPickupDate}&return_date=${modalReturnDate}`;
            }
        });
        
        // Update continue button state
        function updateContinueButton() {
            continueBtn.disabled = !(modalPickupDate && modalReturnDate);
        }
        
        // Fetch unavailable dates for a car
        function fetchUnavailableDates(carId) {
            // Show loading state
            modalPickupDatePicker.set("disable", []);
            modalReturnDatePicker.set("disable", []);
            
            // Fetch unavailable dates from server
            fetch(`get-unavailable-dates.php?car_id=${carId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Disable unavailable dates in the date pickers
                        const unavailableDates = data.unavailable_dates;
                        modalPickupDatePicker.set("disable", unavailableDates);
                        modalReturnDatePicker.set("disable", unavailableDates);
                    } else {
                        console.error("Error fetching unavailable dates:", data.message);
                    }
                })
                .catch(error => {
                    console.error("Error fetching unavailable dates:", error);
                });
        }
    });
    </script>
</body>
</html>
