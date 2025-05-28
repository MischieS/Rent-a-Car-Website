<?php
session_start();
require_once '../config/database.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Get car ID from URL
$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Redirect if no valid ID
if ($car_id <= 0) {
    $_SESSION['error'] = "Invalid car ID";
    header('Location: cars.php');
    exit();
}

// Get car details
try {
    $query = "SELECT * FROM cars WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$car_id]);
    $car = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$car) {
        $_SESSION['error'] = "Car not found";
        header('Location: cars.php');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header('Location: cars.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $year = $_POST['year'];
        $transmission = $_POST['transmission'];
        $fuel_type = $_POST['fuel_type'];
        $price_per_day = $_POST['price_per_day'];
        $availability = isset($_POST['status']) && $_POST['status'] == 'available' ? 1 : 0;
        
        // Set up upload directory
        $upload_dir = '../uploads/cars/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Handle primary image
        $primary_image = $car['image']; // Keep existing image
        if (isset($_FILES['primary_image']) && $_FILES['primary_image']['size'] > 0) {
            $file_name = time() . '_' . basename($_FILES['primary_image']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['primary_image']['tmp_name'], $target_file)) {
                $primary_image = 'uploads/cars/' . $file_name;
            } else {
                $_SESSION['warning'] = "Failed to upload primary image. Using existing image.";
            }
        }
        
        // Update database
        $query = "UPDATE cars SET 
                  brand = ?, 
                  model = ?, 
                  year = ?, 
                  transmission = ?, 
                  fuel_type = ?, 
                  price_per_day = ?, 
                  availability = ?, 
                  image = ? 
                  WHERE id = ?";
                  
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            $brand, 
            $model, 
            $year, 
            $transmission, 
            $fuel_type, 
            $price_per_day, 
            $availability, 
            $primary_image, 
            $car_id
        ]);
        
        if ($result) {
            $_SESSION['success'] = "Car updated successfully!";
            header('Location: cars.php');
            exit();
        } else {
            $_SESSION['error'] = "Failed to update car";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

// Include header
include_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1>Edit Car</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['warning'])): ?>
        <div class="alert alert-warning">
            <?= $_SESSION['warning']; unset($_SESSION['warning']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Brand</label>
                                <input type="text" class="form-control" name="brand" value="<?= htmlspecialchars($car['brand'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Model</label>
                                <input type="text" class="form-control" name="model" value="<?= htmlspecialchars($car['model'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Year</label>
                                <input type="number" class="form-control" name="year" value="<?= htmlspecialchars($car['year'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price Per Day ($)</label>
                                <input type="number" class="form-control" name="price_per_day" value="<?= htmlspecialchars($car['price_per_day'] ?? '') ?>" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Transmission</label>
                                <select class="form-select" name="transmission" required>
                                    <option value="automatic" <?= ($car['transmission'] ?? '') == 'automatic' ? 'selected' : '' ?>>Automatic</option>
                                    <option value="manual" <?= ($car['transmission'] ?? '') == 'manual' ? 'selected' : '' ?>>Manual</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fuel Type</label>
                                <select class="form-select" name="fuel_type" required>
                                    <option value="petrol" <?= ($car['fuel_type'] ?? '') == 'petrol' ? 'selected' : '' ?>>Petrol</option>
                                    <option value="diesel" <?= ($car['fuel_type'] ?? '') == 'diesel' ? 'selected' : '' ?>>Diesel</option>
                                    <option value="electric" <?= ($car['fuel_type'] ?? '') == 'electric' ? 'selected' : '' ?>>Electric</option>
                                    <option value="hybrid" <?= ($car['fuel_type'] ?? '') == 'hybrid' ? 'selected' : '' ?>>Hybrid</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="available" <?= ($car['availability'] ?? 0) == 1 ? 'selected' : '' ?>>Available</option>
                                <option value="unavailable" <?= ($car['availability'] ?? 0) == 0 ? 'selected' : '' ?>>Unavailable</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Primary Image</label>
                            <?php if (!empty($car['image'])): ?>
                                <div class="mb-2">
                                    <img src="../<?= htmlspecialchars($car['image']) ?>" style="max-height: 100px;" alt="Current image">
                                    <p class="text-muted small">Current image</p>
                                </div>
                            <?php else: ?>
                                <div class="mb-2">
                                    <img src="../assets/img/cars/default-car.png" style="max-height: 100px;" alt="Default car image">
                                    <p class="text-muted small">Default image</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="primary_image" accept="image/*">
                        </div>
                        
                        <div class="text-end">
                            <a href="cars.php" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Car</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
