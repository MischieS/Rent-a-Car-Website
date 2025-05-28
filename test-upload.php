<?php
// Test file for debugging upload issues

// Display PHP configuration
echo "<h2>PHP Configuration</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>upload_max_filesize: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>post_max_size: " . ini_get('post_max_size') . "</p>";
echo "<p>max_file_uploads: " . ini_get('max_file_uploads') . "</p>";
echo "<p>memory_limit: " . ini_get('memory_limit') . "</p>";

// Check directory structure
echo "<h2>Directory Structure</h2>";
$directories = [
    'uploads',
    'uploads/cars',
    'uploads/profile_images',
    'assets/img/cars'
];

foreach ($directories as $dir) {
    echo "<p>$dir: ";
    if (is_dir($dir)) {
        echo "Exists";
        echo " (Permissions: " . substr(sprintf('%o', fileperms($dir)), -4) . ")";
        echo " (Writable: " . (is_writable($dir) ? "Yes" : "No") . ")";
    } else {
        echo "Does not exist";
    }
    echo "</p>";
}

// Test file upload
echo "<h2>Test File Upload</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h3>Upload Results:</h3>";
    echo "<pre>";
    print_r($_FILES['test_file']);
    echo "</pre>";
    
    if ($_FILES['test_file']['error'] === 0) {
        $upload_dir = 'uploads/test/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $target_file = $upload_dir . basename($_FILES['test_file']['name']);
        
        if (move_uploaded_file($_FILES['test_file']['tmp_name'], $target_file)) {
            echo "<p class='text-success'>File uploaded successfully to: $target_file</p>";
        } else {
            echo "<p class='text-danger'>Failed to move uploaded file!</p>";
        }
    } else {
        echo "<p class='text-danger'>Upload error: " . $_FILES['test_file']['error'] . "</p>";
        
        // Provide more detailed error message
        $upload_errors = [
            1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
            2 => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
            3 => "The uploaded file was only partially uploaded",
            4 => "No file was uploaded",
            6 => "Missing a temporary folder",
            7 => "Failed to write file to disk",
            8 => "A PHP extension stopped the file upload"
        ];
        
        if (isset($upload_errors[$_FILES['test_file']['error']])) {
            echo "<p class='text-danger'>" . $upload_errors[$_FILES['test_file']['error']] . "</p>";
        }
    }
}

// Display server information
echo "<h2>Server Information</h2>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current Script: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Current Working Directory: " . getcwd() . "</p>";

// Check if GD is available for image processing
echo "<h2>GD Library</h2>";
if (function_exists('gd_info')) {
    echo "<p>GD is available. Version info:</p>";
    echo "<pre>";
    print_r(gd_info());
    echo "</pre>";
} else {
    echo "<p>GD is NOT available.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .text-success { color: green; }
        .text-danger { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>File Upload Test</h1>
        
        <form method="post" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="test_file" class="form-label">Select a file to upload:</label>
                <input type="file" class="form-control" id="test_file" name="test_file">
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
        
        <hr>
        
        <h2>Create Test Image</h2>
        <p>Click the button below to create a test image:</p>
        
        <form method="post" action="create-test-image.php">
            <button type="submit" class="btn btn-success">Create Test Image</button>
        </form>
    </div>
</body>
</html>
