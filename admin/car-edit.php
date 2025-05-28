<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$car_id = $_GET['id'] ?? 0;

if ($car_id <= 0) {
    header('Location: cars.php');
    exit();
}

// Get car details
$query = "SELECT * FROM cars WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$car_id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    $_SESSION['error'] = "Car not found";
    header('Location: cars.php');
    exit();
}

// Handle form submission
if ($_POST) {
    $category_id = $_POST['category_id'] ?? null;
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
        $file_name = time() . '_' . $_FILES['primary_image']['name'];
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['primary_image']['tmp_name'], $target_file)) {
            $primary_image = 'uploads/cars/' . $file_name;
        }
    }
    
    // Handle additional images
    $existing_images = json_decode($car['images'], true) ?: [];
    $additional_images = $existing_images;
    
    if (isset($_FILES['additional_images'])) {
        foreach ($_FILES['additional_images']['name'] as $key => $name) {
            if ($_FILES['additional_images']['size'][$key] > 0) {
                $file_name = time() . '_' . $key . '_' . $name;
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$key], $target_file)) {
                    $additional_images[] = 'uploads/cars/' . $file_name;
                }
            }
        }
    }
    
    $images_json = json_encode($additional_images);
    
    // Update database
    try {
        $query = "UPDATE cars SET brand=?, model=?, year=?, transmission=?, fuel_type=?, price_per_day=?, availability=?, image=?, images=? WHERE id=?";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([$brand, $model, $year, $transmission, $fuel_type, $price_per_day, $availability, $primary_image, $images_json, $car_id]);
        
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

// Get categories for dropdown
try {
    $categories_query = "SELECT id, name FROM car_categories ORDER BY name";
    $categories_stmt = $db->prepare($categories_query);
    $categories_stmt->execute();
    $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
}

include_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1>Edit Car</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= ($car['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Brand</label>
                                <input type="text" class="form-control" name="brand" value="<?= htmlspecialchars($car['brand']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Model</label>
                                <input type="text" class="form-control" name="model" value="<?= htmlspecialchars($car['model']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Year</label>
                                <input type="number" class="form-control" name="year" value="<?= htmlspecialchars($car['year']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Transmission</label>
                                <select class="form-select" name="transmission" required>
                                    <option value="automatic" <?= ($car['transmission'] == 'automatic') ? 'selected' : '' ?>>Automatic</option>
                                    <option value="manual" <?= ($car['transmission'] == 'manual') ? 'selected' : '' ?>>Manual</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fuel Type</label>
                                <select class="form-select" name="fuel_type" required>
                                    <option value="petrol" <?= ($car['fuel_type'] == 'petrol') ? 'selected' : '' ?>>Petrol</option>
                                    <option value="diesel" <?= ($car['fuel_type'] == 'diesel') ? 'selected' : '' ?>>Diesel</option>
                                    <option value="electric" <?= ($car['fuel_type'] == 'electric') ? 'selected' : '' ?>>Electric</option>
                                    <option value="hybrid" <?= ($car['fuel_type'] == 'hybrid') ? 'selected' : '' ?>>Hybrid</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price Per Day ($)</label>
                                <input type="number" class="form-control" name="price_per_day" value="<?= htmlspecialchars($car['price_per_day']) ?>" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="available" <?= ($car['availability'] == 1) ? 'selected' : '' ?>>Available</option>
                                <option value="unavailable" <?= ($car['availability'] == 0) ? 'selected' : '' ?>>Unavailable</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Primary Image</label>
                            <?php if (!empty($car['image'])): ?>
                                <div class="mb-2">
                                    <img src="../<?= htmlspecialchars($car['image']) ?>" style="max-height: 100px;" alt="Current image">
                                    <p class="text-muted small">Current image</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="primary_image" accept="image/*">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Additional Images</label>
                            <?php 
                            if (!empty($car['images'])) {
                                $images = json_decode($car['images'], true);
                                if (is_array($images) && count($images) > 0): ?>
                                    <div class="mb-2">
                                        <?php foreach ($images as $image): ?>
                                            <img src="../<?= htmlspecialchars($image) ?>" style="max-height: 80px; margin-right: 5px;" alt="Additional image">
                                        <?php endforeach; ?>
                                        <p class="text-muted small">Current additional images</p>
                                    </div>
                                <?php endif;
                            } ?>
                            <input type="file" class="form-control" name="additional_images[]" accept="image/*" multiple>
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
