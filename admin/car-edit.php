<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$car_id = $_GET['id'] ?? 0;
$message = '';

// Get car details
$query = "SELECT * FROM cars WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$car_id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    header('Location: cars.php');
    exit();
}

if ($_POST) {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $price_per_day = $_POST['price_per_day'];
    $availability = isset($_POST['status']) && $_POST['status'] == 'available' ? 1 : 0;
    
    // Keep existing image
    $image_path = $car['image'];
    
    // Simple upload - only if new file is selected
    if (isset($_FILES['car_image']) && $_FILES['car_image']['size'] > 0) {
        $upload_dir = '../uploads/cars/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Simple filename
        $filename = time() . '.jpg';
        $target = $upload_dir . $filename;
        
        // Try to upload
        if (move_uploaded_file($_FILES['car_image']['tmp_name'], $target)) {
            $image_path = 'uploads/cars/' . $filename;
            $message = '<div class="alert alert-success">New image uploaded successfully!</div>';
        } else {
            $message = '<div class="alert alert-warning">Image upload failed, keeping existing image.</div>';
        }
    }
    
    // Update database
    try {
        $query = "UPDATE cars SET brand=?, model=?, year=?, transmission=?, fuel_type=?, price_per_day=?, availability=?, image=? WHERE id=?";
        $stmt = $db->prepare($query);
        $stmt->execute([$brand, $model, $year, $transmission, $fuel_type, $price_per_day, $availability, $image_path, $car_id]);
        
        $message .= '<div class="alert alert-success">Car updated successfully!</div>';
        
        // Refresh car data
        $stmt = $db->prepare("SELECT * FROM cars WHERE id = ?");
        $stmt->execute([$car_id]);
        $car = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
}

include_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1>Edit Car</h1>
    
    <?= $message ?>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Brand</label>
                            <input type="text" class="form-control" name="brand" value="<?= htmlspecialchars($car['brand']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Model</label>
                            <input type="text" class="form-control" name="model" value="<?= htmlspecialchars($car['model']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <input type="number" class="form-control" name="year" value="<?= htmlspecialchars($car['year']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Transmission</label>
                            <select class="form-select" name="transmission" required>
                                <option value="automatic" <?= $car['transmission'] == 'automatic' ? 'selected' : '' ?>>Automatic</option>
                                <option value="manual" <?= $car['transmission'] == 'manual' ? 'selected' : '' ?>>Manual</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Fuel Type</label>
                            <select class="form-select" name="fuel_type" required>
                                <option value="petrol" <?= $car['fuel_type'] == 'petrol' ? 'selected' : '' ?>>Petrol</option>
                                <option value="diesel" <?= $car['fuel_type'] == 'diesel' ? 'selected' : '' ?>>Diesel</option>
                                <option value="electric" <?= $car['fuel_type'] == 'electric' ? 'selected' : '' ?>>Electric</option>
                                <option value="hybrid" <?= $car['fuel_type'] == 'hybrid' ? 'selected' : '' ?>>Hybrid</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Price Per Day ($)</label>
                            <input type="number" class="form-control" name="price_per_day" value="<?= htmlspecialchars($car['price_per_day']) ?>" step="0.01" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="available" <?= $car['availability'] == 1 ? 'selected' : '' ?>>Available</option>
                                <option value="unavailable" <?= $car['availability'] == 0 ? 'selected' : '' ?>>Unavailable</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <?php if (!empty($car['image']) && file_exists('../' . $car['image'])): ?>
                                <div class="mb-2">
                                    <img src="../<?= htmlspecialchars($car['image']) ?>" style="max-height: 150px; border-radius: 5px;" alt="Current car image">
                                </div>
                            <?php else: ?>
                                <div class="mb-2">
                                    <div style="width: 150px; height: 100px; background-color: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                                        <span class="text-muted">No Image</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Upload New Image</label>
                            <input type="file" class="form-control" name="car_image" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image</small>
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
