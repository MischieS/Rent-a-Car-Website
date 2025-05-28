<?php
// Start session at the beginning of the file
session_start();
require_once 'includes/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>RENT A CAR - Premium Car Rental Service</title>
    <?php include('assets/includes/header_link.php') ?>
    <!-- Add booking calendar CSS -->
    <link rel="stylesheet" href="assets/css/booking-calendar.css">
    <style>
        /* Calendar Modal Styles */
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
        
        /* Hero section background */
        .hero-banner {
            background-image: url('assets/img/hero-bg.png');
            background-size: cover;
            background-position: center;
            position: relative;
            z-index: 1;
        }
        
        .hero-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.7) 50%, rgba(255,255,255,0.5) 100%);
            z-index: -1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-image-container {
            position: relative;
            z-index: 2;
        }
        
        /* Car Image Carousel */
        .car-card-img {
            position: relative;
            padding: 20px;
            background-color: #f8f9fa;
            overflow: hidden;
        }
        
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
        
        .car-card-img:hover .car-image-prev,
        .car-card-img:hover .car-image-next {
            opacity: 1;
        }
        
        .car-image-prev:hover, .car-image-next:hover {
            background-color: white;
            transform: translateY(-50%) scale(1.1);
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <!-- Header -->
        <?php include('assets/includes/header.php') ?>
        <!-- /Header -->

        <!-- Hero Banner -->
        <section class="hero-banner">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 hero-content" data-aos="fade-right" data-aos-duration="1000">
                        <span class="hero-badge">👍 Whenever, wherever you need</span>
                        <h1 class="hero-title">Rent a <span class="text-accent">Car</span><br>in Seconds</h1>
                        <p class="hero-text">
                            Experience the ultimate in comfort, performance, and sophistication with our car rentals. 
                            From sleek sedans and stylish coupes to spacious SUVs and elegant convertibles, 
                            we offer a range of premium vehicles to suit your preferences and lifestyle.
                        </p>
                        <div class="hero-buttons">
                            <a href="cars.php" class="btn btn-primary btn-lg">View all Cars</a>
                        </div>
                    </div>
                    <div class="col-lg-6 d-none d-lg-block" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                        <div class="hero-image-container">
                            <img src="assets/img/hero-car.png" alt="Luxury Car Rental" class="img-fluid hero-image">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Hero Banner -->
        
        <!-- Search -->
        <div class="search-section" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
            <div class="container">
                <div class="search-card">
                    <form action="cars.php" method="GET" id="searchForm">
                        <div class="row g-4">
                            <div class="col-lg-3 col-md-6">
                                <div class="search-item">
                                    <label>Pickup Location</label>
                                    <select class="form-select" name="pickup_location" required>
                                        <option value="">Select location</option>
                                        <?php
                                        // Include database and location model
                                        include_once 'config/database.php';
                                        include_once 'models/Location.php';
                                        
                                        // Create database connection
                                        $database = new Database();
                                        $db = $database->getConnection();
                                        
                                        // Initialize location object
                                        $location = new Location($db);
                                        
                                        // Get all locations
                                        $locations = $location->getAllLocations();
                                        
                                        // Generate options for each location
                                        foreach ($locations as $loc) {
                                            echo '<option value="' . $loc['id'] . '">' . htmlspecialchars($loc['name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6">
                                <div class="search-item">
                                    <label>Pickup Date</label>
                                    <div class="date-time-group">
                                        <input type="date" class="form-control" name="pickup_date" required>
                                        <input type="time" class="form-control" name="pickup_time" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6">
                                <div class="search-item">
                                    <label>Return Date</label>
                                    <div class="date-time-group">
                                        <input type="date" class="form-control" name="return_date" required>
                                        <input type="time" class="form-control" name="return_time" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6">
                                <button type="submit" class="btn btn-primary search-btn">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Search -->

        <!-- Featured Cars -->
        <section class="featured-section" id="featured-cars">
            <div class="container">
                <div class="section-header text-center" data-aos="fade-up">
                    <h6 class="section-subtitle">EXPLORE OUR FLEET</h6>
                    <h2 class="section-title">Featured Vehicles</h2>
                    <p class="section-description">Choose from our selection of premium vehicles for your next adventure</p>
                </div>
                
                <div class="row g-4 mt-4">
                    <?php
                    // Safely include database and car model
                    if(file_exists('config/database.php')) {
                        include_once 'config/database.php';
                        
                        if(file_exists('models/Car.php')) {
                            include_once 'models/Car.php';
                            
                            try {
                                // Instantiate database and car object
                                $database = new Database();
                                $db = $database->getConnection();
                                
                                if($db) {
                                    // Get 3 random cars from the database
                                    $query = "SELECT c.*, cc.name as category_name 
                                            FROM cars c
                                            LEFT JOIN car_categories cc ON c.category_id = cc.id
                                            WHERE c.availability = 1
                                            ORDER BY RAND()
                                            LIMIT 3";
                                    
                                    $stmt = $db->prepare($query);
                                    $stmt->execute();
                                    
                                    // Check if any cars found
                                    if($stmt->rowCount() > 0) {
                                        $delay = 100;
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            // Set default image if none exists
                                            $main_image = !empty($row['image']) ? $row['image'] : 'assets/img/cars/default.png';
                                            
                                            // Get category name or set default
                                            $category = !empty($row['category_name']) ? $row['category_name'] : 'Standard';
                                            
                                            // Format price
                                            $price = number_format($row['price_per_day'], 2);
                                            
                                            // Parse additional images if available
                                            $additional_images = [];
                                            if (!empty($row['images'])) {
                                                $images_json = $row['images'];
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
                                    ?>
                                    <!-- Car Card -->
                                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                                        <div class="car-card">
                                            <div class="car-card-img" id="featuredCarImages<?php echo $row['id']; ?>">
                                                <!-- Display the first image -->
                                                <img src="<?php echo htmlspecialchars($all_images[0]); ?>" alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>" class="img-fluid" id="featuredCarImage<?php echo $row['id']; ?>-0">
                                                
                                                <?php if (count($all_images) > 1): ?>
                                                <!-- Navigation arrows -->
                                                <div class="car-image-prev" onclick="prevFeaturedImage(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-chevron-left"></i>
                                                </div>
                                                <div class="car-image-next" onclick="nextFeaturedImage(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-chevron-right"></i>
                                                </div>
                                                
                                                <!-- Image dots -->
                                                <div class="car-image-nav">
                                                    <?php foreach ($all_images as $index => $img_url): ?>
                                                    <div class="car-image-dot <?php echo ($index === 0) ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" onclick="showFeaturedImage(<?php echo $row['id']; ?>, <?php echo $index; ?>)"></div>
                                                    <?php endforeach; ?>
                                                </div>
                                                
                                                <!-- Hidden images (preloaded but not displayed) -->
                                                <?php for ($i = 1; $i < count($all_images); $i++): ?>
                                                <img src="<?php echo htmlspecialchars($all_images[$i]); ?>" alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>" class="img-fluid" id="featuredCarImage<?php echo $row['id']; ?>-<?php echo $i; ?>" style="display: none;">
                                                <?php endfor; ?>
                                                
                                                <script>
                                                    // Store images for this car
                                                    if (!window.featuredCarImages) window.featuredCarImages = {};
                                                    window.featuredCarImages[<?php echo $row['id']; ?>] = <?php echo json_encode($all_images); ?>;
                                                </script>
                                                <?php endif; ?>
                                                
                                                <span class="car-category"><?php echo htmlspecialchars($category); ?></span>
                                            </div>
                                            <div class="car-card-body">
                                                <div class="car-name-price">
                                                    <h3 class="car-name"><?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?></h3>
                                                    <div class="car-price">
                                                        <span class="amount">$<?php echo $price; ?></span>
                                                        <span class="duration">/ day</span>
                                                    </div>
                                                </div>
                                                <div class="car-features">
                                                    <div class="feature-item">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <span><?php echo htmlspecialchars($row['year']); ?></span>
                                                    </div>
                                                    <div class="feature-item">
                                                        <i class="fas fa-cogs"></i>
                                                        <span><?php echo htmlspecialchars($row['transmission']); ?></span>
                                                    </div>
                                                    <div class="feature-item">
                                                        <i class="fas fa-gas-pump"></i>
                                                        <span><?php echo htmlspecialchars($row['fuel_type']); ?></span>
                                                    </div>
                                                </div>
                                                <a href="#" class="btn btn-primary w-100 mt-3 book-now-btn" data-car-id="<?php echo $row['id']; ?>" style="background-color: var(--primary-color); border-color: var(--primary-color); font-weight: 500; transition: var(--transition);">Book Now</a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                            $delay += 100;
                                        }
                                    } else {
                                        // Fallback to static content if no cars found
                                    ?>
                                    <!-- Car Card 1 -->
                                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                                        <div class="car-card">
                                            <div class="car-card-img">
                                                <img src="assets/img/cars/sedan.png" alt="Mercedes C-Class" class="img-fluid">
                                                <span class="car-category">Luxury</span>
                                            </div>
                                            <div class="car-card-body">
                                                <div class="car-name-price">
                                                    <h3 class="car-name">Mercedes C-Class</h3>
                                                    <div class="car-price">
                                                        <span class="amount">$89</span>
                                                        <span class="duration">/ day</span>
                                                    </div>
                                                </div>
                                                <div class="car-features">
                                                    <div class="feature-item">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <span>2023</span>
                                                    </div>
                                                    <div class="feature-item">
                                                        <i class="fas fa-cogs"></i>
                                                        <span>Automatic</span>
                                                    </div>
                                                    <div class="feature-item">
                                                        <i class="fas fa-gas-pump"></i>
                                                        <span>Petrol</span>
                                                    </div>
                                                </div>
                                                <a href="#" class="btn btn-primary w-100 mt-3 book-now-btn" data-car-id="1" style="background-color: var(--primary-color); border-color: var(--primary-color); font-weight: 500; transition: var(--transition);">Book Now</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Car Card 2 -->
                                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                                        <div class="car-card">
                                            <div class="car-card-img">
                                                <img src="assets/img/cars/suv.png" alt="BMW X5" class="img-fluid">
                                                <span class="car-category">SUV</span>
                                            </div>
                                            <div class="car-card-body">
                                                <div class="car-name-price">
                                                    <h3 class="car-name">BMW X5</h3>
                                                    <div class="car-price">
                                                        <span class="amount">$129</span>
                                                        <span class="duration">/ day</span>
                                                    </div>
                                                </div>
                                                <div class="car-features">
                                                    <div class="feature-item">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <span>2023</span>
                                                    </div>
                                                    <div class="feature-item">
                                                        <i class="fas fa-cogs"></i>
                                                        <span>Automatic</span>
                                                    </div>
                                                    <div class="feature-item">
                                                        <i class="fas fa-gas-pump"></i>
                                                        <span>Diesel</span>
                                                    </div>
                                                </div>
                                                <a href="#" class="btn btn-primary w-100 mt-3 book-now-btn" data-car-id="2" style="background-color: var(--primary-color); border-color: var(--primary-color); font-weight: 500; transition: var(--transition);">Book Now</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Car Card 3 -->
                                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                                        <div class="car-card">
                                            <div class="car-card-img">
                                                <img src="assets/img/cars/convertible.png" alt="Audi A5 Cabriolet" class="img-fluid">
                                                <span class="car-category">Convertible</span>
                                            </div>
                                            <div class="car-card-body">
                                                <div class="car-name-price">
                                                    <h3 class="car-name">Audi A5 Cabriolet</h3>
                                                    <div class="car-price">
                                                        <span class="amount">$149</span>
                                                        <span class="duration">/ day</span>
                                                    </div>
                                                </div>
                                                <div class="car-features">
                                                    <div class="feature-item">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <span>2023</span>
                                                    </div>
                                                    <div class="feature-item">
                                                        <i class="fas fa-cogs"></i>
                                                        <span>Automatic</span>
                                                    </div>
                                                    <div class="feature-item">
                                                        <i class="fas fa-gas-pump"></i>
                                                        <span>Petrol</span>
                                                    </div>
                                                </div>
                                                <a href="#" class="btn btn-primary w-100 mt-3 book-now-btn" data-car-id="3" style="background-color: var(--primary-color); border-color: var(--primary-color); font-weight: 500; transition: var(--transition);">Book Now</a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    }
                                } else {
                                    // Fallback to static content if database connection fails
                                    // (Static content would be the same as above)
                                }
                            } catch (Exception $e) {
                                // Log error and display static content
                                error_log("Error in index.php: " . $e->getMessage());
                                // (Static content would be the same as above)
                            }
                        } else {
                            // Fallback if Car.php doesn't exist
                            // (Static content would be the same as above)
                        }
                    } else {
                        // Fallback if database.php doesn't exist
                        // (Static content would be the same as above)
                    }
                    ?>
                </div>
                
                <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="400">
                    <a href="cars.php" class="btn btn-primary btn-lg">View All Vehicles</a>
                </div>
            </div>
        </section>
        <!-- /Featured Cars -->

        <!-- How It Works -->
        <section class="how-works-section" id="how-it-works">
            <div class="container">
                <div class="section-header text-center" data-aos="fade-up">
                    <h6 class="section-subtitle">SIMPLE & EASY</h6>
                    <h2 class="section-title">How It Works</h2>
                    <p class="section-description">Rent your dream car in just a few simple steps</p>
                </div>
                
                <div class="row g-4 mt-4">
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="step-card">
                            <div class="step-icon">
                                <i class="fas fa-car-side"></i>
                                <span class="step-number">01</span>
                            </div>
                            <h3 class="step-title">Choose a Car</h3>
                            <p class="step-text">Browse our extensive fleet and select the perfect vehicle for your needs.</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="step-card">
                            <div class="step-icon">
                                <i class="fas fa-calendar-check"></i>
                                <span class="step-number">02</span>
                            </div>
                            <h3 class="step-title">Pick Date & Location</h3>
                            <p class="step-text">Select your preferred pickup and return dates and locations.</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="step-card">
                            <div class="step-icon">
                                <i class="fas fa-credit-card"></i>
                                <span class="step-number">03</span>
                            </div>
                            <h3 class="step-title">Book & Pay</h3>
                            <p class="step-text">Complete your booking with our secure payment system.</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="step-card">
                            <div class="step-icon">
                                <i class="fas fa-key"></i>
                                <span class="step-number">04</span>
                            </div>
                            <h3 class="step-title">Enjoy Your Ride</h3>
                            <p class="step-text">Pick up your car and enjoy your journey with our premium service.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /How It Works -->

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
        <!-- /Calendar Modal -->

        <!-- Footer -->
        <?php include('assets/includes/footer.php') ?>
        <!-- /Footer -->
    </div>

    <?php include('assets/includes/footer_link.php') ?>
    
    <!-- Add booking calendar JS -->
    <script src="assets/js/booking-calendar.js"></script>
    
    <script>
    // Set default dates (today and tomorrow)
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        // Format dates for input fields
        const formatDate = date => {
            return date.toISOString().split('T')[0];
        };
        
        // Set default values
        document.querySelector('input[name="pickup_date"]').value = formatDate(today);
        document.querySelector('input[name="return_date"]').value = formatDate(tomorrow);
        document.querySelector('input[name="pickup_time"]').value = '10:00';
        document.querySelector('input[name="return_time"]').value = '10:00';

        // Initialize AOS animations
        AOS.init();
        
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

    // Form validation
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        const pickupDate = new Date(document.querySelector('input[name="pickup_date"]').value);
        const returnDate = new Date(document.querySelector('input[name="return_date"]').value);
        const pickupTime = document.querySelector('input[name="pickup_time"]').value;
        const returnTime = document.querySelector('input[name="return_time"]').value;
        
        if (returnDate < pickupDate) {
            e.preventDefault();
            alert('Return date cannot be before pickup date');
            return false;
        }
        
        if (returnDate.getTime() === pickupDate.getTime() && returnTime < pickupTime) {
            e.preventDefault();
            alert('Return time must be after pickup time on the same day');
            return false;
        }
        
        return true;
    });
    
    // Featured car image carousel functions
    function showFeaturedImage(carId, index) {
        // Hide all images
        for (let i = 0; i < window.featuredCarImages[carId].length; i++) {
            const img = document.getElementById(`featuredCarImage${carId}-${i}`);
            if (img) {
                img.style.display = 'none';
            }
        }
        
        // Show the selected image
        const selectedImage = document.getElementById(`featuredCarImage${carId}-${index}`);
        if (selectedImage) {
            selectedImage.style.display = 'block';
        }
        
        // Update dots
        const dots = document.querySelectorAll(`#featuredCarImages${carId} .car-image-dot`);
        dots.forEach(dot => dot.classList.remove('active'));
        dots[index].classList.add('active');
    }
    
    function nextFeaturedImage(carId) {
        const dots = document.querySelectorAll(`#featuredCarImages${carId} .car-image-dot`);
        const activeDot = document.querySelector(`#featuredCarImages${carId} .car-image-dot.active`);
        const activeIndex = Array.from(dots).indexOf(activeDot);
        const nextIndex = (activeIndex + 1) % dots.length;
        showFeaturedImage(carId, nextIndex);
    }
    
    function prevFeaturedImage(carId) {
        const dots = document.querySelectorAll(`#featuredCarImages${carId} .car-image-dot`);
        const activeDot = document.querySelector(`#featuredCarImages${carId} .car-image-dot.active`);
        const activeIndex = Array.from(dots).indexOf(activeDot);
        const prevIndex = (activeIndex - 1 + dots.length) % dots.length;
        showFeaturedImage(carId, prevIndex);
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
    </script>
</body>
</html>
