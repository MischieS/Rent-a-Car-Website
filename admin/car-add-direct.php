<?php
// Start session
session_start();

// Include database connection
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Get car categories from database
$categories = [];
try {
    $query = "SELECT id, name FROM car_categories ORDER BY name";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching categories: " . $e->getMessage());
}

// Get brands and models for dropdowns
$brands = ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen', 'Nissan', 'Hyundai', 'Kia', 'Mazda', 'Subaru', 'Lexus', 'Jeep', 'Tesla'];
$models = [
    'Toyota' => ['Corolla', 'Camry', 'RAV4', 'Highlander', 'Tacoma', 'Tundra', 'Prius'],
    'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot', 'Odyssey', 'Fit', 'HR-V'],
    'Ford' => ['F-150', 'Mustang', 'Explorer', 'Escape', 'Edge', 'Ranger', 'Bronco'],
    'Chevrolet' => ['Silverado', 'Equinox', 'Malibu', 'Traverse', 'Tahoe', 'Suburban', 'Camaro'],
    'BMW' => ['3 Series', '5 Series', '7 Series', 'X3', 'X5', 'X7', 'i4'],
    'Mercedes-Benz' => ['C-Class', 'E-Class', 'S-Class', 'GLC', 'GLE', 'GLS', 'EQS'],
    'Audi' => ['A3', 'A4', 'A6', 'Q3', 'Q5', 'Q7', 'e-tron'],
    'Volkswagen' => ['Golf', 'Jetta', 'Passat', 'Tiguan', 'Atlas', 'ID.4', 'Taos'],
    'Nissan' => ['Altima', 'Maxima', 'Rogue', 'Murano', 'Pathfinder', 'Frontier', 'Leaf'],
    'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Palisade', 'Kona', 'Ioniq'],
    'Kia' => ['Forte', 'K5', 'Sportage', 'Sorento', 'Telluride', 'Soul', 'Niro'],
    'Mazda' => ['Mazda3', 'Mazda6', 'CX-5', 'CX-9', 'MX-5 Miata', 'CX-30', 'CX-50'],
    'Subaru' => ['Impreza', 'Legacy', 'Forester', 'Outback', 'Crosstrek', 'Ascent', 'WRX'],
    'Lexus' => ['ES', 'IS', 'LS', 'RX', 'NX', 'GX', 'LX'],
    'Jeep' => ['Wrangler', 'Grand Cherokee', 'Cherokee', 'Compass', 'Renegade', 'Gladiator', 'Wagoneer'],
    'Tesla' => ['Model 3', 'Model Y', 'Model S', 'Model X', 'Cybertruck']
];

// Process form submission - direct database approach
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $category_id = $_POST['category_id'] ?? '';
        $brand = $_POST['brand'] ?? '';
        $model = $_POST['model'] ?? '';
        $year = $_POST['year'] ?? '';
        $transmission = $_POST['transmission'] ?? '';
        $fuel_type = $_POST['fuel_type'] ?? '';
        $seats = $_POST['seats'] ?? '';
        $price_per_day = $_POST['price_per_day'] ?? 0;
        $availability = isset($_POST['status']) && $_POST['status'] == 'available' ? 1 : 0;
        $description = $_POST['description'] ?? '';
        
        // Debug form data
        error_log("Form data: " . print_r($_POST, true));
        error_log("Files data: " . print_r($_FILES, true));
        
        // Create uploads directory if it doesn't exist
        $upload_dir = '../uploads/cars/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Handle primary image upload
        $primary_image = '';
        if (isset($_FILES['primary_image']) && $_FILES['primary_image']['error'] === 0) {
            $file_name = time() . '_primary_' . basename($_FILES['primary_image']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['primary_image']['tmp_name'], $target_file)) {
                $primary_image = 'uploads/cars/' . $file_name;
                error_log("Primary image uploaded: " . $primary_image);
            } else {
                error_log("Failed to upload primary image: " . $_FILES['primary_image']['error']);
            }
        }
        
        // Handle additional images upload - simplified approach
        $additional_images = [];
        
        // Check if files were uploaded
        if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
            $file_count = count($_FILES['additional_images']['name']);
            error_log("Additional images count: " . $file_count);
            
            for ($i = 0; $i < $file_count; $i++) {
                // Check if this file was uploaded successfully
                if ($_FILES['additional_images']['error'][$i] === 0) {
                    $file_name = time() . '_' . $i . '_' . basename($_FILES['additional_images']['name'][$i]);
                    $target_file = $upload_dir . $file_name;
                    
                    error_log("Trying to upload: " . $_FILES['additional_images']['name'][$i] . " to " . $target_file);
                    
                    if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$i], $target_file)) {
                        $additional_images[] = 'uploads/cars/' . $file_name;
                        error_log("Additional image uploaded: uploads/cars/" . $file_name);
                    } else {
                        error_log("Failed to upload additional image: " . $_FILES['additional_images']['error'][$i]);
                    }
                } else {
                    error_log("Error with additional image " . $i . ": " . $_FILES['additional_images']['error'][$i]);
                }
            }
        } else {
            error_log("No additional images uploaded");
        }
        
        // If no primary image but we have additional images, use the first one as primary
        if (empty($primary_image) && !empty($additional_images)) {
            $primary_image = $additional_images[0];
            error_log("Using first additional image as primary: " . $primary_image);
        }
        
        // Convert additional images to JSON
        $images_json = json_encode($additional_images);
        error_log("Images JSON: " . $images_json);
        
        // Direct SQL insert with all fields
        $query = "INSERT INTO cars (category_id, brand, model, year, transmission, fuel_type, seats, price_per_day, availability, image, images, description, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            $category_id,
            $brand,
            $model,
            $year,
            $transmission,
            $fuel_type,
            $seats,
            $price_per_day,
            $availability,
            $primary_image,
            $images_json,
            $description
        ]);
        
        if ($result) {
            $_SESSION['success'] = "Car added successfully";
            header('Location: cars.php');
            exit();
        } else {
            $error = $stmt->errorInfo();
            $_SESSION['error'] = "Failed to add car: " . $error[2];
            error_log("Database error: " . print_r($error, true));
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
        error_log("Exception: " . $e->getMessage());
    }
}

// Set page title
$page_title = "Add New Car (Direct)";

// Include header
include_once 'includes/header.php';
?>

<body>
    <!-- Main Navigation -->
    <?php include_once 'includes/main-nav.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1>Add New Car</h1>
                <p class="text-muted">Add a new car to the inventory</p>
            </div>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data" id="carForm">
                            <!-- Car Category -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="category_id" class="form-label">Car Category/Class</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Brand and Model -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="brand" class="form-label">Brand</label>
                                    <select class="form-select" id="brand" name="brand" required>
                                        <option value="">Select Brand</option>
                                        <?php foreach ($brands as $brand): ?>
                                            <option value="<?php echo $brand; ?>"><?php echo $brand; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="model" class="form-label">Model</label>
                                    <select class="form-select" id="model" name="model" required>
                                        <option value="">Select Model</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Year, Transmission, Fuel Type -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="year" class="form-label">Year</label>
                                    <input type="number" class="form-control" id="year" name="year" min="2000" max="<?php echo date('Y') + 1; ?>" value="<?php echo date('Y'); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="transmission" class="form-label">Transmission</label>
                                    <select class="form-select" id="transmission" name="transmission" required>
                                        <option value="Automatic">Automatic</option>
                                        <option value="Manual">Manual</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="fuel_type" class="form-label">Fuel Type</label>
                                    <select class="form-select" id="fuel_type" name="fuel_type" required>
                                        <option value="Petrol">Petrol</option>
                                        <option value="Diesel">Diesel</option>
                                        <option value="Electric">Electric</option>
                                        <option value="Hybrid">Hybrid</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Seats, Price, Status -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="seats" class="form-label">Number of Seats</label>
                                    <input type="number" class="form-control" id="seats" name="seats" min="1" max="15" value="5" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="price_per_day" class="form-label">Price Per Day</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="price_per_day" name="price_per_day" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="available" selected>Available</option>
                                        <option value="unavailable">Unavailable</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <!-- Primary Image Upload with Preview -->
                            <div class="mb-4">
                                <label class="form-label">Primary Image</label>
                                <div class="primary-image-container">
                                    <div class="primary-image-preview" id="primaryImagePreview">
                                        <div class="upload-placeholder">
                                            <i class="fas fa-image fa-3x mb-2"></i>
                                            <p>Click to select primary image</p>
                                        </div>
                                    </div>
                                    <input type="file" class="form-control d-none" id="primary_image" name="primary_image" accept="image/*">
                                </div>
                            </div>
                            
                            <!-- Additional Images Upload with Preview - SIMPLIFIED -->
                            <div class="mb-4">
                                <label class="form-label">Additional Images</label>
                                <div class="mb-3">
                                    <input type="file" class="form-control" id="additional_images" name="additional_images[]" accept="image/*" multiple>
                                    <div class="form-text">You can select multiple additional images at once.</div>
                                </div>
                                <div id="additionalImagesPreview" class="additional-images-grid">
                                    <!-- Preview images will be added here -->
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <a href="cars.php" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Add Car</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Help</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6><i class="fas fa-info-circle me-2 text-primary"></i> Adding a New Car</h6>
                            <p class="small text-muted">Fill in all the required information to add a new car to the inventory.</p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-image me-2 text-primary"></i> Car Images</h6>
                            <p class="small text-muted">Click on the primary image area to select the main image. Use the file input to select multiple additional images.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        /* Primary Image Styles */
        .primary-image-container {
            margin-bottom: 15px;
        }
        .primary-image-preview {
            width: 100%;
            height: 250px;
            border: 2px dashed #ccc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .primary-image-preview:hover {
            border-color: #6c757d;
            background-color: #e9ecef;
        }
        .primary-image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .upload-placeholder {
            text-align: center;
            color: #6c757d;
        }
        
        /* Additional Images Styles */
        .additional-images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .additional-image-item {
            width: 100%;
            height: 150px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }
        .additional-image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .remove-preview-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #dc3545;
            z-index: 10;
        }
        .remove-preview-btn:hover {
            background-color: rgba(255, 255, 255, 1);
            color: #b02a37;
        }
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const brandSelect = document.getElementById('brand');
        const modelSelect = document.getElementById('model');
        const primaryImageInput = document.getElementById('primary_image');
        const primaryImagePreview = document.getElementById('primaryImagePreview');
        const additionalImagesInput = document.getElementById('additional_images');
        const additionalImagesPreview = document.getElementById('additionalImagesPreview');
        
        // Models data
        const models = <?php echo json_encode($models); ?>;
        
        // Update models when brand changes
        brandSelect.addEventListener('change', function() {
            const brand = this.value;
            
            // Clear model options
            modelSelect.innerHTML = '<option value="">Select Model</option>';
            
            // Add new options based on selected brand
            if (brand && models[brand]) {
                models[brand].forEach(function(model) {
                    const option = document.createElement('option');
                    option.value = model;
                    option.textContent = model;
                    modelSelect.appendChild(option);
                });
            }
        });
        
        // Primary Image Preview
        primaryImagePreview.addEventListener('click', function() {
            primaryImageInput.click();
        });
        
        primaryImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    primaryImagePreview.innerHTML = `<img src="${e.target.result}" alt="Primary Image">`;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Additional Images Preview - SIMPLIFIED
        additionalImagesInput.addEventListener('change', function() {
            // Clear previous previews
            additionalImagesPreview.innerHTML = '';
            
            if (this.files) {
                for (let i = 0; i < this.files.length; i++) {
                    const file = this.files[i];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const imageItem = document.createElement('div');
                        imageItem.className = 'additional-image-item';
                        imageItem.innerHTML = `
                            <img src="${e.target.result}" alt="Additional Image ${i+1}">
                        `;
                        
                        additionalImagesPreview.appendChild(imageItem);
                    };
                    
                    reader.readAsDataURL(file);
                }
            }
        });
    });
    </script>
</body>
</html>
