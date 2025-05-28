<?php
session_start();
require_once '../config/database.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Check if admin is logged in
if (!isset($_SESSION['admin_login'])) {
    header('location:index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $car_title = $_POST['car_title'];
        $car_brand = $_POST['car_brand'];
        $car_overview = $_POST['car_overview'];
        $price_per_day = $_POST['price_per_day'];
        $fuel_type = $_POST['fuel_type'];
        $model_year = $_POST['model_year'];
        $seating_capacity = $_POST['seating_capacity'];
        $vhl_number = $_POST['vhl_number'];
        $transmission = $_POST['transmission'];
        $availability = isset($_POST['status']) && $_POST['status'] == 'available' ? 1 : 0;
        
        // Amenities
        $air_conditioner = isset($_POST['air_conditioner']) ? 1 : 0;
        $power_door_locks = isset($_POST['power_door_locks']) ? 1 : 0;
        $anti_lock_braking_system = isset($_POST['anti_lock_braking_system']) ? 1 : 0;
        $brake_assist = isset($_POST['brake_assist']) ? 1 : 0;
        $power_steering = isset($_POST['power_steering']) ? 1 : 0;
        $driver_airbag = isset($_POST['driver_airbag']) ? 1 : 0;
        $passenger_airbag = isset($_POST['passenger_airbag']) ? 1 : 0;
        $power_windows = isset($_POST['power_windows']) ? 1 : 0;
        $cd_player = isset($_POST['cd_player']) ? 1 : 0;
        $central_locking = isset($_POST['central_locking']) ? 1 : 0;
        $crash_sensor = isset($_POST['crash_sensor']) ? 1 : 0;
        $leather_seats = isset($_POST['leather_seats']) ? 1 : 0;
        
        // Set up upload directory
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/rentacar/uploads/cars/';
        $web_path = '/rentacar/uploads/cars/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Handle primary image
        $primary_image = 'assets/img/cars/default-car.png'; // Default image
        if (isset($_FILES['primary_image']) && $_FILES['primary_image']['size'] > 0) {
            $file_name = time() . '_' . basename($_FILES['primary_image']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['primary_image']['tmp_name'], $target_file)) {
                $primary_image = $web_path . $file_name;
            } else {
                $_SESSION['warning'] = "Failed to upload image. Using default image.";
            }
        }
        
        // Handle additional images (up to 5)
        $additional_images = array();
        for ($i = 1; $i <= 5; $i++) {
            $image_field = 'image' . $i;
            if (isset($_FILES[$image_field]) && $_FILES[$image_field]['size'] > 0) {
                $file_extension = pathinfo($_FILES[$image_field]['name'], PATHINFO_EXTENSION);
                $file_name = time() . '_' . $i . '.' . $file_extension;
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES[$image_field]['tmp_name'], $target_file)) {
                    $additional_images[] = $web_path . $file_name;
                    $_SESSION['success'] .= "<br>Image " . $i . " uploaded successfully!";
                } else {
                    $_SESSION['error'] .= "<br>Failed to upload image " . $i . ". Check permissions.";
                }
            }
        }

        // Convert additional images array to a comma-separated string
        $additional_images_str = implode(',', $additional_images);

        // Insert into database
        $query = "INSERT INTO tblvehicles (VehiclesTitle, VehiclesBrand, VehiclesOverview, PricePerDay, FuelType, ModelYear, SeatingCapacity, VhlNumber, Vimage1, Vimage2, Vimage3, Vimage4, Vimage5, AirConditioner, PowerDoorLocks, AntiLockBrakingSystem, BrakeAssist, PowerSteering, DriverAirbag, PassengerAirbag, PowerWindows, CDPlayer, CentralLocking, CrashSensor, LeatherSeats, Transmission, Availability) 
                  VALUES (:car_title, :car_brand, :car_overview, :price_per_day, :fuel_type, :model_year, :seating_capacity, :vhl_number, :primary_image, :image2, :image3, :image4, :image5, :air_conditioner, :power_door_locks, :anti_lock_braking_system, :brake_assist, :power_steering, :driver_airbag, :passenger_airbag, :power_windows, :cd_player, :central_locking, :crash_sensor, :leather_seats, :transmission, :availability)";
                  
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            $car_title, 
            $car_brand, 
            $car_overview, 
            $price_per_day, 
            $fuel_type, 
            $model_year, 
            $seating_capacity, 
            $vhl_number, 
            $primary_image, 
            isset($additional_images[0]) ? $additional_images[0] : '', 
            isset($additional_images[1]) ? $additional_images[1] : '', 
            isset($additional_images[2]) ? $additional_images[2] : '', 
            isset($additional_images[3]) ? $additional_images[3] : '', 
            isset($additional_images[4]) ? $additional_images[4] : '', 
            $air_conditioner, 
            $power_door_locks, 
            $anti_lock_braking_system, 
            $brake_assist, 
            $power_steering, 
            $driver_airbag, 
            $passenger_airbag, 
            $power_windows, 
            $cd_player, 
            $central_locking, 
            $crash_sensor, 
            $leather_seats, 
            $transmission, 
            $availability
        ]);
        
        if ($result) {
            $_SESSION['success'] .= "<br>Car added successfully!";
            header('Location: car-manage.php');
            exit();
        } else {
            $_SESSION['error'] = "Failed to add car";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

// Include header
include('includes/header.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Car</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .mt-6 {
            margin-top: 4rem !important;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Add New Car</h2>
        <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php } ?>
        <?php if (isset($_SESSION['warning'])) { ?>
            <div class="alert alert-warning">
                <?php echo $_SESSION['warning'];
                unset($_SESSION['warning']); ?>
            </div>
        <?php } ?>
        <?php if (isset($_SESSION['success'])) { ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php } ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="car_title">Car Title</label>
                <input type="text" class="form-control" id="car_title" name="car_title" required>
            </div>
            <div class="form-group">
                <label for="car_brand">Car Brand</label>
                <input type="text" class="form-control" id="car_brand" name="car_brand" required>
            </div>
            <div class="form-group">
                <label for="car_overview">Car Overview</label>
                <textarea class="form-control" id="car_overview" name="car_overview" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="price_per_day">Price Per Day</label>
                <input type="number" class="form-control" id="price_per_day" name="price_per_day" required>
            </div>
            <div class="form-group">
                <label for="fuel_type">Fuel Type</label>
                <select class="form-control" id="fuel_type" name="fuel_type" required>
                    <option value="Petrol">Petrol</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Electric">Electric</option>
                    <option value="Hybrid">Hybrid</option>
                </select>
            </div>
            <div class="form-group">
                <label for="model_year">Model Year</label>
                <input type="number" class="form-control" id="model_year" name="model_year" required>
            </div>
            <div class="form-group">
                <label for="seating_capacity">Seating Capacity</label>
                <input type="number" class="form-control" id="seating_capacity" name="seating_capacity" required>
            </div>
            <div class="form-group">
                <label for="vhl_number">Vehicle Number</label>
                <input type="text" class="form-control" id="vhl_number" name="vhl_number" required>
            </div>
            <div class="form-group">
                <label for="transmission">Transmission</label>
                <select class="form-control" id="transmission" name="transmission" required>
                    <option value="automatic">Automatic</option>
                    <option value="manual">Manual</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>
            <div class="form-group">
                <label for="primary_image">Primary Image</label>
                <input type="file" class="form-control-file" id="primary_image" name="primary_image" accept="image/*">
                <small class="text-muted">If no image is selected, a default image will be used.</small>
            </div>
            <?php for ($i = 1; $i <= 5; $i++) { ?>
                <div class="form-group">
                    <label for="image<?php echo $i; ?>">Image <?php echo $i; ?></label>
                    <input type="file" class="form-control-file" id="image<?php echo $i; ?>" name="image<?php echo $i; ?>" accept="image/*">
                </div>
            <?php } ?>

            <div class="form-group">
                <label>Amenities</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="air_conditioner" name="air_conditioner" value="1">
                    <label class="form-check-label" for="air_conditioner">Air Conditioner</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="power_door_locks" name="power_door_locks" value="1">
                    <label class="form-check-label" for="power_door_locks">Power Door Locks</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="anti_lock_braking_system" name="anti_lock_braking_system" value="1">
                    <label class="form-check-label" for="anti_lock_braking_system">AntiLock Braking System</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="brake_assist" name="brake_assist" value="1">
                    <label class="form-check-label" for="brake_assist">Brake Assist</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="power_steering" name="power_steering" value="1">
                    <label class="form-check-label" for="power_steering">Power Steering</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="driver_airbag" name="driver_airbag" value="1">
                    <label class="form-check-label" for="driver_airbag">Driver Airbag</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="passenger_airbag" name="passenger_airbag" value="1">
                    <label class="form-check-label" for="passenger_airbag">Passenger Airbag</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="power_windows" name="power_windows" value="1">
                    <label class="form-check-label" for="power_windows">Power Windows</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="cd_player" name="cd_player" value="1">
                    <label class="form-check-label" for="cd_player">CD Player</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="central_locking" name="central_locking" value="1">
                    <label class="form-check-label" for="central_locking">Central Locking</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="crash_sensor" name="crash_sensor" value="1">
                    <label class="form-check-label" for="crash_sensor">Crash Sensor</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="leather_seats" name="leather_seats" value="1">
                    <label class="form-check-label" for="leather_seats">Leather Seats</label>
                </div>
            </div>

            <div class="text-end">
                <a href="car-manage.php" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" name="add" class="btn btn-primary">Add Car</button>
            </div>
        </form>
    </div>
    <?php include('includes/footer.php'); ?>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
