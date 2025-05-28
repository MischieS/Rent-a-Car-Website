<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Simple upload directory
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
    $primary_image = '';
    if ($_FILES['primary_image']['size'] > 0) {
        $file_name = time() . '_' . $_FILES['primary_image']['name'];
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['primary_image']['tmp_name'], $target_file)) {
            $primary_image = 'uploads/cars/' . $file_name;
        }
    }
    
    // Handle additional images
    $additional_images = [];
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
    
    // Insert into database
    $query = "INSERT INTO cars (category_id, brand, model, year, transmission, fuel_type, price_per_day, availability, image, images) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$category_id, $brand, $model, $year, $transmission, $fuel_type, $price_per_day, $availability, $primary_image, $images_json]);
    
    $_SESSION['success'] = "Car added successfully!";
    header('Location: cars.php');
    exit();
}

// Get categories for dropdown
$categories_query = "SELECT id, name FROM car_categories ORDER BY name";
$categories_stmt = $db->prepare($categories_query);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

include_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1>Add New Car</h1>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Brand</label>
                                <input type="text" class="form-control" name="brand" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Model</label>
                                <input type="text" class="form-control" name="model" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Year</label>
                                <input type="number" class="form-control" name="year" min="2000" max="<?= date('Y') + 1 ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Transmission</label>
                                <select class="form-select" name="transmission" required>
                                    <option value="automatic">Automatic</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fuel Type</label>
                                <select class="form-select" name="fuel_type" required>
                                    <option value="petrol">Petrol</option>
                                    <option value="diesel">Diesel</option>
                                    <option value="electric">Electric</option>
                                    <option value="hybrid">Hybrid</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price Per Day ($)</label>
                                <input type="number" class="form-control" name="price_per_day" step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Primary Image</label>
                            <input type="file" class="form-control" name="primary_image" accept="image/*" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Additional Images</label>
                            <input type="file" class="form-control" name="additional_images[]" accept="image/*" multiple>
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
