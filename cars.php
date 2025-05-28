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

// Initialize car object
$car = new Car($db);

// Get filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$class = isset($_GET['class']) ? $_GET['class'] : '';
$transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$fuel_type = isset($_GET['fuel_type']) ? $_GET['fuel_type'] : '';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : '';

// Get all cars from database
$stmt = $car->read();
$cars = [];

// Process cars
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cars[] = $row;
}

// Get unique brands, years, fuel types, and categories for filters
$brands = [];
$years = [];
$fuel_types = [];
$categories = $car->getAllCategories();
$transmissions = ['Automatic', 'Manual'];
$classes = [];

// Find min and max prices for the price range filter
$min_db_price = PHP_INT_MAX;
$max_db_price = 0;

// Extract unique values for filters
foreach ($cars as $car_item) {
    if (!in_array($car_item['brand'], $brands)) {
        $brands[] = $car_item['brand'];
    }
    if (!in_array($car_item['year'], $years)) {
        $years[] = $car_item['year'];
    }
    if (!in_array($car_item['fuel_type'], $fuel_types)) {
        $fuel_types[] = $car_item['fuel_type'];
    }
    
    // Track min and max prices
    $price = (int)$car_item['price_per_day'];
    if ($price < $min_db_price) {
        $min_db_price = $price;
    }
    if ($price > $max_db_price) {
        $max_db_price = $price;
    }
}

// Set default price range if not specified
if ($min_price === '') {
    $min_price = $min_db_price;
}
if ($max_price === '') {
    $max_price = $max_db_price;
}

// Get category names for each car
$category_names = [];
foreach ($categories as $category) {
    $category_names[$category['id']] = $category['name'];
    $classes[] = strtolower($category['name']);
}

// Sort filter options
sort($brands);
rsort($years);
sort($classes);
sort($fuel_types);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Car Listings - RENT A CAR</title>
    <?php include('assets/includes/header_link.php') ?>
    <link rel="stylesheet" href="assets/css/booking-calendar.css">
    <style>
        /* Compact Filter Styles */
        .filter-sidebar {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 15px;
            position: sticky;
            top: 20px;
            transition: all 0.3s ease;
        }
        
        .filter-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .filter-title i {
            margin-right: 8px;
            color: var(--primary-color);
        }
        
        .filter-widget {
            margin-bottom: 10px;
            padding-bottom: 10px;
        }
        
        .form-select, .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 8px 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
            height: auto;
        }
        
        .filter-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 10px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 15px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 15px;
            transition: all 0.3s ease;
        }
        
        /* Price range slider */
        .price-slider {
            margin-top: 10px;
        }
        
        .price-inputs {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }
        
        .price-input-group {
            flex: 1;
        }
        
        .price-input-group label {
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 3px;
            display: block;
        }
        
        .price-range-slider {
            height: 4px;
            background: #e5e7eb;
            border-radius: 4px;
            position: relative;
            margin: 15px 0 8px;
        }
        
        .price-range-values {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 0.8rem;
            color: #6b7280;
        }
        
        /* Filter tags */
        .filter-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 15px;
        }
        
        .filter-tag {
            background-color: #f0f7ff;
            color: var(--primary-color);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
            border: 1px solid #e0e7ff;
        }
        
        /* Calendar Styles */
        .calendar-modal .modal-dialog {
            max-width: 700px;
        }
        
        .calendar-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }
        
        .calendar-error {
            padding: 15px;
            display: none;
        }
        
        .calendar-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .calendar-nav-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #333;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.2s;
        }
        
        .calendar-nav-btn:hover {
            background-color: #f0f0f0;
        }
        
        .calendar-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 767px) {
            .calendar-container {
                flex-direction: column;
            }
        }
        
        .calendar {
            flex: 1;
            min-width: 0;
        }
        
        .calendar-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .calendar-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .calendar-table th {
            padding: 8px;
            text-align: center;
            font-weight: normal;
            color: #666;
        }
        
        .calendar-table td {
            padding: 8px;
            text-align: center;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.2s;
        }
        
        .calendar-table td.empty {
            cursor: default;
        }
        
        .calendar-table td.available:hover {
            background-color: #e6f7ff;
        }
        
        .calendar-table td.today {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .calendar-table td.selected {
            background-color: var(--primary-color);
            color: white;
            font-weight: bold;
        }
        
        .calendar-table td.in-range {
            background-color: #e6f7ff;
        }
        
        .calendar-table td.unavailable {
            color: #ccc;
            text-decoration: line-through;
            cursor: not-allowed;
            background-color: #f8f8f8;
        }
        
        .calendar-table td.disabled {
            color: #ccc;
            cursor: not-allowed;
        }
        
        .date-selection {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }
        
        .date-selection-item {
            flex: 1;
            padding: 15px;
            border-radius: 5px;
            background-color: #f8f8f8;
        }
        
        .date-selection-label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #666;
        }
        
        .date-selection-value {
            font-size: 1.1rem;
        }
        
        .continue-btn {
            width: 100%;
            padding: 12px;
        }
        
        /* Car Card Styles */
        .car-card {
            background-color: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }
        
        .car-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }
        
        /* Car Image Carousel */
        .car-image-container {
            position: relative;
            height: 220px;
            background-color: #f8f9fa;
            overflow: hidden;
        }
        
        .car-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .car-image-nav {
            position: absolute;
            bottom: 15px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 8px;
        }
        
        .car-image-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .car-image-dot.active {
            background-color: white;
            transform: scale(1.2);
        }
        
        .car-image-prev, .car-image-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 2;
        }
        
        .car-image-prev {
            left: 15px;
        }
        
        .car-image-next {
            right: 15px;
        }
        
        .car-image-container:hover .car-image-prev,
        .car-image-container:hover .car-image-next {
            opacity: 1;
        }
        
        .car-image-prev:hover, .car-image-next:hover {
            background-color: white;
            transform: translateY(-50%) scale(1.1);
        }
        
        .car-category {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: var(--primary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 1;
        }
        
        /* Car Card Body */
        .car-card-body {
            padding: 20px;
        }
        
        .car-name-price {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .car-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
            color: #333;
        }
        
        .car-price {
            text-align: right;
        }
        
        .car-price .amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .car-price .duration {
            font-size: 0.9rem;
            color: #6b7280;
        }
        
        .car-features {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 15px 0;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .feature-item {
            text-align: center;
        }
        
        .feature-item i {
            display: block;
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .feature-item span {
            font-size: 0.85rem;
            color: #6b7280;
        }
        
        .car-description {
            margin-bottom: 15px;
        }
        
        .car-description p {
            font-size: 0.95rem;
            color: #6b7280;
            margin: 0;
        }
        
        .book-now-btn {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .book-now-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.2);
        }
        
        /* No results message */
        .no-results {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            color: #6b7280;
        }
        
        .no-results i {
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .no-results h4 {
            color: #333;
            margin-bottom: 10px;
        }
        
        /* Car Grid */
        .car-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        /* Responsive Styles */
        @media (max-width: 991px) {
            .filter-sidebar {
                margin-bottom: 30px;
                position: relative;
                top: 0;
            }
            
            .car-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }
        
        @media (max-width: 767px) {
            .car-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
        
        @media (max-width: 575px) {
            .car-grid {
                grid-template-columns: 1fr;
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
                        <h1>Booking</h1>
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
                    <div class="col-12 mb-4">
                        <h2 class="text-center">Car Listings</h2>
                    </div>
                    <!-- Filter Sidebar -->
                    <div class="col-lg-3">
                        <div class="filter-sidebar">
                            <form id="filterForm" method="GET" action="cars.php">
                                <div class="filter-widget">
                                    <h4 class="filter-title"><i class="fas fa-search"></i> Search</h4>
                                    <div class="filter-body">
                                        <input type="text" class="form-control" placeholder="Search model or brand" id="searchInput" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                </div>

                                <!-- Class Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title"><i class="fas fa-car"></i> Class</h4>
                                    <div class="filter-body">
                                        <select class="form-select filter-select" id="classFilter" name="class">
                                            <option value="">Any Class</option>
                                            <?php foreach ($classes as $class_option): ?>
                                            <option value="<?php echo htmlspecialchars($class_option); ?>" <?php echo ($class == $class_option) ? 'selected' : ''; ?>>
                                                <?php echo ucfirst(htmlspecialchars($class_option)); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Transmission Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title"><i class="fas fa-cogs"></i> Transmission</h4>
                                    <div class="filter-body">
                                        <select class="form-select filter-select" id="transmissionFilter" name="transmission">
                                            <option value="">Any Transmission</option>
                                            <?php foreach ($transmissions as $trans_option): ?>
                                            <option value="<?php echo htmlspecialchars($trans_option); ?>" <?php echo ($transmission == $trans_option) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($trans_option); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Fuel Type Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title"><i class="fas fa-gas-pump"></i> Fuel Type</h4>
                                    <div class="filter-body">
                                        <select class="form-select filter-select" id="fuelTypeFilter" name="fuel_type">
                                            <option value="">Any Fuel Type</option>
                                            <?php foreach ($fuel_types as $fuel_option): ?>
                                            <option value="<?php echo htmlspecialchars($fuel_option); ?>" <?php echo ($fuel_type == $fuel_option) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($fuel_option); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Year Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title"><i class="fas fa-calendar-alt"></i> Year</h4>
                                    <div class="filter-body">
                                        <select class="form-select" id="yearFilter" name="year">
                                            <option value="">Any Year</option>
                                            <?php foreach ($years as $year_option): ?>
                                            <option value="<?php echo htmlspecialchars($year_option); ?>" <?php echo ($year == $year_option) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($year_option); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Price Range Filter -->
                                <div class="filter-widget">
                                    <h4 class="filter-title"><i class="fas fa-dollar-sign"></i> Price Range</h4>
                                    <div class="filter-body">
                                        <div class="price-slider">
                                            <div class="price-inputs">
                                                <div class="price-input-group">
                                                    <label for="minPrice">Min ($)</label>
                                                    <input type="number" class="form-control" id="minPrice" name="min_price" value="<?php echo $min_price; ?>" min="<?php echo $min_db_price; ?>" max="<?php echo $max_db_price; ?>">
                                                </div>
                                                <div class="price-input-group">
                                                    <label for="maxPrice">Max ($)</label>
                                                    <input type="number" class="form-control" id="maxPrice" name="max_price" value="<?php echo $max_price; ?>" min="<?php echo $min_db_price; ?>" max="<?php echo $max_db_price; ?>">
                                                </div>
                                            </div>
                                            <div class="price-range-slider">
                                                <div class="price-range-progress" style="left: <?php echo (($min_price - $min_db_price) / ($max_db_price - $min_db_price)) * 100; ?>%; right: <?php echo 100 - (($max_price - $min_db_price) / ($max_db_price - $min_db_price)) * 100; ?>%"></div>
                                            </div>
                                            <div class="price-range-values">
                                                <span>$<?php echo $min_db_price; ?></span>
                                                <span>$<?php echo $max_db_price; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-actions">
                                    <button type="submit" class="btn btn-primary" id="applyFilters">
                                        <i class="fas fa-filter me-2"></i> Apply Filters
                                    </button>
                                    <a href="cars.php" class="btn btn-outline-secondary" id="resetFilters">
                                        <i class="fas fa-redo me-2"></i> Reset Filters
                                    </a>
                                </div>
                                
                                <!-- Active Filters -->
                                <?php if (!empty($search) || !empty($class) || !empty($transmission) || !empty($year) || !empty($fuel_type) || $min_price > $min_db_price || $max_price < $max_db_price): ?>
                                <div class="filter-tags">
                                    <?php if (!empty($search)): ?>
                                    <div class="filter-tag">
                                        <span><?php echo htmlspecialchars($search); ?></span>
                                        <a href="<?php echo '?' . http_build_query(array_merge($_GET, ['search' => ''])); ?>" class="close">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($class)): ?>
                                    <div class="filter-tag">
                                        <span><?php echo ucfirst(htmlspecialchars($class)); ?></span>
                                        <a href="<?php echo '?' . http_build_query(array_merge($_GET, ['class' => ''])); ?>" class="close">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($transmission)): ?>
                                    <div class="filter-tag">
                                        <span><?php echo htmlspecialchars($transmission); ?></span>
                                        <a href="<?php echo '?' . http_build_query(array_merge($_GET, ['transmission' => ''])); ?>" class="close">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($fuel_type)): ?>
                                    <div class="filter-tag">
                                        <span><?php echo htmlspecialchars($fuel_type); ?></span>
                                        <a href="<?php echo '?' . http_build_query(array_merge($_GET, ['fuel_type' => ''])); ?>" class="close">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($year)): ?>
                                    <div class="filter-tag">
                                        <span><?php echo htmlspecialchars($year); ?></span>
                                        <a href="<?php echo '?' . http_build_query(array_merge($_GET, ['year' => ''])); ?>" class="close">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($min_price > $min_db_price || $max_price < $max_db_price): ?>
                                    <div class="filter-tag">
                                        <span>$<?php echo $min_price; ?> - $<?php echo $max_price; ?></span>
                                        <a href="<?php echo '?' . http_build_query(array_merge($_GET, ['min_price' => '', 'max_price' => ''])); ?>" class="close">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                    <!-- /Filter Sidebar -->

                    <!-- Car Listing -->
                    <div class="col-lg-9">
                        <?php if (empty($cars)): ?>
                        <div id="noResultsMessage" class="no-results">
                            <i class="fas fa-car-crash fa-3x mb-3"></i>
                            <h4>No cars available</h4>
                            <p>There are no cars available in the database.</p>
                        </div>
                        <?php else: ?>
                        <div id="noResultsMessage" class="no-results" style="display: none;">
                            <i class="fas fa-search fa-3x mb-3"></i>
                            <h4>No matches found</h4>
                            <p>No cars match your search criteria. Please try different filters.</p>
                        </div>

                        <div class="car-grid" id="carGrid">
                            <?php 
                            $filtered_cars = [];
                            foreach ($cars as $car_item): 
                                // Apply filters
                                $matches_filters = true;
                                
                                // Search filter (brand or model)
                                if (!empty($search)) {
                                    $search_term = strtolower($search);
                                    $brand = strtolower($car_item['brand']);
                                    $model = strtolower($car_item['model']);
                                    
                                    if (strpos($brand, $search_term) === false && strpos($model, $search_term) === false) {
                                        $matches_filters = false;
                                    }
                                }
                                
                                // Class filter
                                if (!empty($class)) {
                                    $car_category = isset($category_names[$car_item['category_id']]) ? 
                                        strtolower($category_names[$car_item['category_id']]) : '';
                                    if ($car_category != strtolower($class)) {
                                        $matches_filters = false;
                                    }
                                }
                                
                                // Transmission filter
                                if (!empty($transmission) && strtolower($car_item['transmission']) != strtolower($transmission)) {
                                    $matches_filters = false;
                                }
                                
                                // Fuel type filter
                                if (!empty($fuel_type) && strtolower($car_item['fuel_type']) != strtolower($fuel_type)) {
                                    $matches_filters = false;
                                }
                                
                                // Year filter
                                if (!empty($year) && $car_item['year'] != $year) {
                                    $matches_filters = false;
                                }
                                
                                // Price range filter
                                $car_price = (int)$car_item['price_per_day'];
                                if ($car_price < $min_price || $car_price > $max_price) {
                                    $matches_filters = false;
                                }
                                
                                if ($matches_filters) {
                                    $filtered_cars[] = $car_item;
                            ?>
                            <div class="car-card">
                                <!-- Car Image Container -->
                                <div class="car-image-container" id="carImages<?php echo $car_item['id']; ?>">
                                    <?php 
                                    // Get car images
                                    $main_image = !empty($car_item['image']) ? $car_item['image'] : 'assets/img/cars/default.png';
                                    $additional_images = [];
                                    
                                    // Parse additional images if available
                                    if (!empty($car_item['images'])) {
                                        $images_json = $car_item['images'];
                                        if (is_string($images_json) && !empty($images_json)) {
                                            try {
                                                $decoded_images = json_decode($images_json, true);
                                                if (is_array($decoded_images) && !empty($decoded_images)) {
                                                    $additional_images = $decoded_images;
                                                }
                                            } catch (Exception $e) {
                                                // Failed to parse JSON, ignore
                                            }
                                        }
                                    }
                                    
                                    // Combine main image with additional images
                                    $all_images = array_merge([$main_image], $additional_images);
                                    $all_images = array_filter($all_images); // Remove empty values
                                    $all_images = array_unique($all_images); // Remove duplicates
                                    $all_images = array_slice($all_images, 0, 5); // Limit to 5 images
                                    
                                    // If no images, use default
                                    if (empty($all_images)) {
                                        $all_images = ['assets/img/cars/default.png'];
                                    }
                                    
                                    // Display the first image
                                    echo '<img src="' . htmlspecialchars($all_images[0]) . '" alt="' . htmlspecialchars($car_item['brand'] . ' ' . $car_item['model']) . '" class="car-image active" id="carImage' . $car_item['id'] . '-0">';
                                    
                                    // Only show navigation if there are multiple images
                                    if (count($all_images) > 1):
                                    ?>
                                    <div class="car-image-nav">
                                        <?php foreach ($all_images as $index => $img_url): ?>
                                        <div class="car-image-dot <?php echo ($index === 0) ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" onclick="showImage(<?php echo $car_item['id']; ?>, <?php echo $index; ?>)"></div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="car-image-prev" onclick="prevImage(<?php echo $car_item['id']; ?>)">
                                        <i class="fas fa-chevron-left"></i>
                                    </div>
                                    <div class="car-image-next" onclick="nextImage(<?php echo $car_item['id']; ?>)">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                    
                                    <!-- Hidden images (preloaded but not displayed) -->
                                    <?php for ($i = 1; $i < count($all_images); $i++): ?>
                                    <img src="<?php echo htmlspecialchars($all_images[$i]); ?>" alt="<?php echo htmlspecialchars($car_item['brand'] . ' ' . $car_item['model']); ?>" class="car-image" id="carImage<?php echo $car_item['id']; ?>-<?php echo $i; ?>" style="display: none;">
                                    <?php endfor; ?>
                                    
                                    <script>
                                        // Store images for this car
                                        if (!window.carImages) window.carImages = {};
                                        window.carImages[<?php echo $car_item['id']; ?>] = <?php echo json_encode($all_images); ?>;
                                    </script>
                                    <?php endif; ?>
                                    
                                    <span class="car-category">
                                        <?php echo isset($category_names[$car_item['category_id']]) ? 
                                            htmlspecialchars($category_names[$car_item['category_id']]) : 'Unknown'; ?>
                                    </span>
                                </div>
                                <div class="car-card-body">
                                    <div class="car-name-price">
                                        <h3 class="car-name"><?php echo htmlspecialchars($car_item['brand'] . ' ' . $car_item['model']); ?></h3>
                                        <div class="car-price">
                                            <span class="amount">$<?php echo htmlspecialchars($car_item['price_per_day']); ?></span>
                                            <span class="duration">/ day</span>
                                        </div>
                                    </div>
                                    <div class="car-features">
                                        <div class="feature-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span><?php echo htmlspecialchars($car_item['year']); ?></span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-cogs"></i>
                                            <span><?php echo htmlspecialchars($car_item['transmission']); ?></span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-gas-pump"></i>
                                            <span><?php echo htmlspecialchars($car_item['fuel_type']); ?></span>
                                        </div>
                                    </div>
                                    <?php if (isset($car_item['description']) && !empty($car_item['description'])): ?>
                                    <div class="car-description mt-2">
                                        <p><?php echo htmlspecialchars(substr($car_item['description'], 0, 100) . (strlen($car_item['description']) > 100 ? '...' : '')); ?></p>
                                    </div>
                                    <?php endif; ?>
                                    <a href="#" class="btn btn-primary book-now-btn" data-car-id="<?php echo $car_item['id']; ?>">
                                        <i class="fas fa-calendar-check me-2"></i> Book Now
                                    </a>
                                </div>
                            </div>
                            <?php 
                                }
                            endforeach; 
                            
                            // Show no results message if no cars match filters
                            if (empty($filtered_cars)):
                            ?>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    document.getElementById('noResultsMessage').style.display = 'block';
                                    document.getElementById('carGrid').style.display = 'none';
                                });
                            </script>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- /Car Listing -->
                </div>
            </div>
        </section>
        <!-- /Car Listing Section -->

        <!-- Footer -->
        <?php include('assets/includes/footer.php') ?>
        <!-- /Footer -->
    </div>

    <?php include('assets/includes/footer_link.php') ?>
    
    <!-- Calendar Modal -->
    <div class="modal fade calendar-modal" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="calendarModalLabel">Select Rental Dates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Loading Indicator -->
                    <div id="calendarLoading" class="calendar-loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Loading availability data...</p>
                    </div>
                    
                    <!-- Error Message -->
                    <div id="calendarError" class="calendar-error alert alert-danger"></div>
                    
                    <!-- Calendar Content -->
                    <div id="calendarContent" style="display: none;">
                        <!-- Calendar Navigation -->
                        <div class="calendar-navigation">
                            <button id="prevMonth" class="calendar-nav-btn">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <h6 class="mb-0">Select pickup and return dates</h6>
                            <button id="nextMonth" class="calendar-nav-btn">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        
                        <!-- Calendar Container -->
                        <div class="calendar-container">
                            <div id="calendar1" class="calendar"></div>
                            <div id="calendar2" class="calendar"></div>
                        </div>
                        
                        <!-- Date Selection Display -->
                        <div class="date-selection">
                            <div class="date-selection-item">
                                <div class="date-selection-label">Pickup Date</div>
                                <div id="pickupDateDisplay" class="date-selection-value">Select Date</div>
                            </div>
                            <div class="date-selection-item">
                                <div class="date-selection-label">Return Date</div>
                                <div id="returnDateDisplay" class="date-selection-value">Select Date</div>
                            </div>
                        </div>
                        
                        <!-- Continue Button -->
                        <button id="continueBooking" class="btn btn-primary continue-btn" disabled>Continue to Booking</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/booking-calendar.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS
        if (typeof AOS !== 'undefined') {
            AOS.init();
        }
        
        // Price range validation
        const minPriceInput = document.getElementById('minPrice');
        const maxPriceInput = document.getElementById('maxPrice');
        
        if (minPriceInput && maxPriceInput) {
            minPriceInput.addEventListener('change', function() {
                if (parseInt(minPriceInput.value) > parseInt(maxPriceInput.value)) {
                    maxPriceInput.value = minPriceInput.value;
                }
                updatePriceRangeProgress();
            });
            
            maxPriceInput.addEventListener('change', function() {
                if (parseInt(maxPriceInput.value) < parseInt(minPriceInput.value)) {
                    minPriceInput.value = maxPriceInput.value;
                }
                updatePriceRangeProgress();
            });
            
            function updatePriceRangeProgress() {
                const min = parseInt(minPriceInput.value);
                const max = parseInt(maxPriceInput.value);
                const minDb = <?php echo $min_db_price; ?>;
                const maxDb = <?php echo $max_db_price; ?>;
                
                const leftPercent = ((min - minDb) / (maxDb - minDb)) * 100;
                const rightPercent = 100 - ((max - minDb) / (maxDb - minDb)) * 100;
                
                const progressBar = document.querySelector('.price-range-progress');
                if (progressBar) {
                    progressBar.style.left = leftPercent + '%';
                    progressBar.style.right = rightPercent + '%';
                }
            }
        }
        
        // Book now button click handler
        const bookNowButtons = document.querySelectorAll('.book-now-btn');
        bookNowButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const carId = this.getAttribute('data-car-id');
                // Store the selected car ID in localStorage
                localStorage.setItem('selectedCarId', carId);
                // Open the calendar modal
                const calendarModal = new bootstrap.Modal(document.getElementById('calendarModal'));
                calendarModal.show();
                
                // Load availability data for this car
                loadCarAvailability(carId);
            });
        });
    });
    
    // Image carousel functions
    function showImage(carId, index) {
        // Hide all images
        const images = document.querySelectorAll(`#carImages${carId} .car-image`);
        images.forEach(img => img.style.display = 'none');
        
        // Show the selected image
        const selectedImage = document.getElementById(`carImage${carId}-${index}`);
        if (selectedImage) {
            selectedImage.style.display = 'block';
        }
        
        // Update dots
        const dots = document.querySelectorAll(`#carImages${carId} .car-image-dot`);
        dots.forEach(dot => dot.classList.remove('active'));
        dots[index].classList.add('active');
    }
    
    function nextImage(carId) {
        const dots = document.querySelectorAll(`#carImages${carId} .car-image-dot`);
        const activeDot = document.querySelector(`#carImages${carId} .car-image-dot.active`);
        const activeIndex = Array.from(dots).indexOf(activeDot);
        const nextIndex = (activeIndex + 1) % dots.length;
        showImage(carId, nextIndex);
    }
    
    function prevImage(carId) {
        const dots = document.querySelectorAll(`#carImages${carId} .car-image-dot`);
        const activeDot = document.querySelector(`#carImages${carId} .car-image-dot.active`);
        const activeIndex = Array.from(dots).indexOf(activeDot);
        const prevIndex = (activeIndex - 1 + dots.length) % dots.length;
        showImage(carId, prevIndex);
    }
    
    // Function to load car availability data
    function loadCarAvailability(carId) {
        const calendarLoading = document.getElementById('calendarLoading');
        const calendarContent = document.getElementById('calendarContent');
        const calendarError = document.getElementById('calendarError');
        
        calendarLoading.style.display = 'block';
        calendarContent.style.display = 'none';
        calendarError.style.display = 'none';
        
        // Fetch availability data from the server
        fetch('get-unavailable-dates.php?car_id=' + carId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Initialize or update calendar with unavailable dates
                    if (window.initializeCalendar) {
                        window.initializeCalendar(data.unavailable_dates || []);
                    }
                    calendarLoading.style.display = 'none';
                    calendarContent.style.display = 'block';
                } else {
                    calendarLoading.style.display = 'none';
                    calendarError.textContent = data.message || 'Error loading availability data.';
                    calendarError.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                calendarLoading.style.display = 'none';
                calendarError.textContent = 'Error loading availability data. Please try again.';
                calendarError.style.display = 'block';
            });
    }

    // Continue booking button click handler
    document.getElementById('continueBooking').addEventListener('click', function() {
        const carId = localStorage.getItem('selectedCarId');
        const pickupDate = document.getElementById('pickupDateDisplay').getAttribute('data-date');
        const returnDate = document.getElementById('returnDateDisplay').getAttribute('data-date');
        
        if (carId && pickupDate && returnDate) {
            window.location.href = 'booking-form.php?car_id=' + carId + '&pickup_date=' + pickupDate + '&return_date=' + returnDate;
        }
    });
    </script>
</body>
</html>
