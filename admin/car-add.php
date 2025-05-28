<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';

if ($_POST) {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $price_per_day = $_POST['price_per_day'];
    $availability = isset($_POST['status']) && $_POST['status'] == 'available' ? 1 : 0;
    
    // Simple upload - just check if file exists
    $image_path = '';
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
            $message = '<div class="alert alert-success">Image uploaded successfully!</div>';
        } else {
            $message = '<div class="alert alert-warning">Image upload failed, but car will be added without image.</div>';
        }
    }
    
    // Insert into database
    try {
        $query = "INSERT INTO cars (brand, model, year, transmission, fuel_type, price_per_day, availability, image) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$brand, $model, $year, $transmission, $fuel_type, $price_per_day, $availability, $image_path]);
        
        $message .= '<div class="alert alert-success">Car added successfully!</div>';
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
}

include_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1>Add New Car</h1>
    
    <?= $message ?>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Brand</label>
                            <input type="text" class="form-control" name="brand" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Model</label>
                            <input type="text" class="form-control" name="model" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <input type="number" class="form-control" name="year" min="2000" max="2030" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Transmission</label>
                            <select class="form-select" name="transmission" required>
                                <option value="automatic">Automatic</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Fuel Type</label>
                            <select class="form-select" name="fuel_type" required>
                                <option value="petrol">Petrol</option>
                                <option value="diesel">Diesel</option>
                                <option value="electric">Electric</option>
                                <option value="hybrid">Hybrid</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Price Per Day ($)</label>
                            <input type="number" class="form-control" name="price_per_day" step="0.01" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Car Image</label>
                            <input type="file" class="form-control" name="car_image" accept="image/*">
                            <small class="text-muted">Optional - JPG, PNG, GIF files only</small>
                        </div>
                        
                        <div class="text-end">
                            <a href="cars.php" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Car</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
