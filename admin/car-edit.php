<?php

// Include necessary files
require_once '../config/config.php';
require_once '../includes/auth_validate.php';

// Get Input data from query string
$edit = false;
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $car_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $edit = true;
}

// Serve POST method, After successful insert
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mass Insert Data.  Escape the string to prevent mysql injection
    $data_to_db = array_filter($_POST);
    foreach ($_POST as $key => $value) {
        $data_to_db[$key] = filter_var($value, FILTER_SANITIZE_STRING);
    }

    // Get the absolute path for uploads
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/rentacar/uploads/cars/';
    $web_path = '/rentacar/uploads/cars/';

    // Create upload directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
        chmod($upload_dir, 0755);
    }

    // Handle primary image
    $primary_image = '';
    if (isset($_FILES['primary_image']) && $_FILES['primary_image']['size'] > 0) {
        $file_extension = pathinfo($_FILES['primary_image']['name'], PATHINFO_EXTENSION);
        $file_name = time() . '_primary.' . $file_extension;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['primary_image']['tmp_name'], $target_file)) {
            $primary_image = $web_path . $file_name;
            $data_to_db['image'] = $primary_image;
        }
    }

    $db = getDbInstance();
    if ($edit) {
        $car_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $db->where('id', $car_id);
        $stat = $db->update('cars', $data_to_db);

        if ($stat) {
            $_SESSION['success'] = "Car updated successfully!";
            header('location: cars.php');
            exit;
        } else {
            $_SESSION['failure'] = "Failed to update car: " . $db->getLastError();
            header('location: cars.php');
            exit;
        }
    } else {
        $last_id = $db->insert('cars', $data_to_db);

        if ($last_id) {
            $_SESSION['success'] = "Car added successfully!";
            header('location: cars.php');
            exit;
        } else {
            $_SESSION['failure'] = "Failed to add car: " . $db->getLastError();
            header('location: cars.php');
            exit;
        }
    }
}

// If edit variable is set, we are performing the update operation.
if ($edit) {
    $db = getDbInstance();
    $db->where('id', $car_id);
    $car = $db->getOne('cars');
} else {
    $car = array(
        'make' => '',
        'model' => '',
        'year' => '',
        'color' => '',
        'registration_number' => '',
        'rental_price' => '',
        'availability' => '',
        'image' => ''
    );
}

// We are using same form for adding and updating.
// Check for edit variable.
?>
<?php include_once '../includes/header.php'; ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header"><?php echo $edit ? "Update Car" : "Add Car"; ?></h2>
        </div>
    </div>
    <!-- Flash messages -->
    <?php include('../includes/flash_messages.php') ?>
    <form class="form" action="" method="post" enctype="multipart/form-data" id="car_form">
        <fieldset>
            <div class="form-group">
                <label for="make">Make *</label>
                <input type="text" name="make" value="<?php echo htmlspecialchars($car['make']); ?>" placeholder="Make" class="form-control" required="required" id="make">
            </div>

            <div class="form-group">
                <label for="model">Model *</label>
                <input type="text" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" placeholder="Model" class="form-control" required="required" id="model">
            </div>

            <div class="form-group">
                <label for="year">Year *</label>
                <input type="number" name="year" value="<?php echo htmlspecialchars($car['year']); ?>" placeholder="Year" class="form-control" required="required" id="year">
            </div>

            <div class="form-group">
                <label for="color">Color *</label>
                <input type="text" name="color" value="<?php echo htmlspecialchars($car['color']); ?>" placeholder="Color" class="form-control" required="required" id="color">
            </div>

            <div class="form-group">
                <label for="registration_number">Registration Number *</label>
                <input type="text" name="registration_number" value="<?php echo htmlspecialchars($car['registration_number']); ?>" placeholder="Registration Number" class="form-control" required="required" id="registration_number">
            </div>

            <div class="form-group">
                <label for="rental_price">Rental Price *</label>
                <input type="number" name="rental_price" value="<?php echo htmlspecialchars($car['rental_price']); ?>" placeholder="Rental Price" class="form-control" required="required" id="rental_price">
            </div>

            <div class="form-group">
                <label for="availability">Availability *</label>
                <select name="availability" class="form-control" required="required" id="availability">
                    <option value="available" <?php echo ($car['availability'] == 'available') ? "selected" : ""; ?>>Available</option>
                    <option value="unavailable" <?php echo ($car['availability'] == 'unavailable') ? "selected" : ""; ?>>Unavailable</option>
                </select>
            </div>

            <div class="form-group">
                <label for="primary_image">Primary Image</label>
                <input type="file" name="primary_image" id="primary_image">
                <?php if ($edit && $car['image']): ?>
                    <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="Current Image" style="max-width: 200px; max-height: 200px;">
                <?php endif; ?>
            </div>

            <div class="form-group text-center">
                <label></label>
                <button type="submit" class="btn btn-warning">Save <span class="glyphicon glyphicon-send"></span></button>
            </div>
        </fieldset>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#car_form').validate({
            rules: {
                make: {
                    required: true,
                    minlength: 3
                },
                model: {
                    required: true,
                    minlength: 3
                },
                year: {
                    required: true,
                    digits: true,
                    minlength: 4,
                    maxlength: 4
                },
                color: {
                    required: true,
                    minlength: 3
                },
                registration_number: {
                    required: true,
                    minlength: 3
                },
                rental_price: {
                    required: true,
                    number: true
                },
                availability: {
                    required: true
                }
            }
        });
    });
</script>

<?php include_once '../includes/footer.php'; ?>
