<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$car_id = $_GET['id'] ?? 0;

// Get car details
$query = "SELECT * FROM cars WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$car_id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    header('Location: cars.php');
    exit();
}

$upload_dir = '../uploads/cars/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_POST) {
    $category_id = $_POST['category_id'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $price_per_day = $_POST['price_per_day'];
    $availability = isset($_POST['status']) && $_POST['status'] == 'available' ? 1 : 0;
    
    // Handle primary image
    $primary_image = $car['image']; // Keep existing image
    if ($_FILES['primary_image']['size'] > 0) {
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
    $query = "UPDATE cars SET category_id=?, brand=?, model=?, year=?, transmission=?, fuel_type=?, price_per_day=?, availability=?, image=?, images=? WHERE id=?";
    $stmt = $db->prepare($query);
    $stmt->execute([$category_id, $brand, $model, $year, $transmission, $fuel_type, $price_per_day, $availability, $primary_image, $images_json, $car_id]);
    
    $_SESSION['success'] = "Car updated successfully!";
    header('Location: cars.php');
    exit();
}

// Get categories
$categories_query = "SELECT id, name FROM car_categories ORDER BY name";
$categories_stmt = $db->prepare($categories_query);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

include_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1>Edit Car</h1>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category_id" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= $car['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                            <?= $category['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Brand</label>
                                <input type="text" class="form-control" name="brand" value="<?= $car['brand'] ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Model</label>
                                <input type="text" class="form-control" name="model" value="<?= $car['model'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Year</label>
                                <input type="number" class="form-control" name="year" value="<?= $car['year'] ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Transmission</label>
                                <select class="form-select" name="transmission" required>
                                    <option value="automatic" <?= $car['transmission'] == 'automatic' ? 'selected' : '' ?>>Automatic</option>
                                    <option value="manual" <?= $car['transmission'] == 'manual' ? 'selected' : '' ?>>Manual</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fuel Type</label>
                                <select class="form-select" name="fuel_type" required>
                                    <option value="petrol" <?= $car['fuel_type'] == 'petrol' ? 'selected' : '' ?>>Petrol</option>
                                    <option value="diesel" <?= $car['fuel_type'] == 'diesel' ? 'selected' : '' ?>>Diesel</option>
                                    <option value="electric" <?= $car['fuel_type'] == 'electric' ? 'selected' : '' ?>>Electric</option>
                                    <option value="hybrid" <?= $car['fuel_type'] == 'hybrid' ? 'selected' : '' ?>>Hybrid</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price Per Day ($)</label>
                                <input type="number" class="form-control" name="price_per_day" value="<?= $car['price_per_day'] ?>" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="available" <?= $car['availability'] == 1 ? 'selected' : '' ?>>Available</option>
                                <option value="unavailable" <?= $car['availability'] == 0 ? 'selected' : '' ?>>Unavailable</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Primary Image</label>
                            <?php if ($car['image']): ?>
                                <div class="mb-2">
                                    <img src="../<?= $car['image'] ?>" style="max-height: 100px;">
                                    <p class="text-muted small">Current image</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="primary_image" accept="image/*">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Additional Images</label>
                            <?php 
                            $images = json_decode($car['images'], true);
                            if ($images): ?>
                                <div class="mb-2">
                                    <?php foreach ($images as $image): ?>
                                        <img src="../<?= $image ?>" style="max-height: 80px; margin-right: 5px;">
                                    <?php endforeach; ?>
                                    <p class="text-muted small">Current additional images</p>
                                </div>
                            <?php endif; ?>
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
