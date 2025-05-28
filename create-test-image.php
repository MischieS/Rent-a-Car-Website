<?php
// Script to create a test image

// Check if GD is available
if (!function_exists('imagecreatetruecolor')) {
    die("GD library is not available. Cannot create test image.");
}

// Create directories if they don't exist
$directories = [
    'assets/img/cars',
    'uploads/cars',
    'uploads/test'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Create a test image
$width = 800;
$height = 600;
$image = imagecreatetruecolor($width, $height);

// Define colors
$bg_color = imagecolorallocate($image, 200, 200, 200);
$text_color = imagecolorallocate($image, 50, 50, 50);
$car_color = imagecolorallocate($image, 100, 100, 200);

// Fill background
imagefill($image, 0, 0, $bg_color);

// Draw a simple car shape
// Car body
imagefilledrectangle($image, 200, 300, 600, 400, $car_color);
// Car top
imagefilledrectangle($image, 300, 200, 500, 300, $car_color);
// Wheels
imagefilledellipse($image, 250, 400, 80, 80, $text_color);
imagefilledellipse($image, 550, 400, 80, 80, $text_color);

// Add text
imagestring($image, 5, 300, 150, 'Test Car Image', $text_color);
imagestring($image, 3, 300, 450, 'Created: ' . date('Y-m-d H:i:s'), $text_color);

// Save the image to multiple locations
$locations = [
    'assets/img/cars/default-car.png',
    'uploads/test/test-car.jpg'
];

$success = true;
foreach ($locations as $location) {
    if (!imagejpeg($image, $location, 90)) {
        $success = false;
        echo "Failed to save image to: $location<br>";
    }
}

// Free memory
imagedestroy($image);

// Output result
if ($success) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Test Image Created</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .success { color: green; }
            img { max-width: 400px; border: 1px solid #ddd; margin: 10px 0; }
        </style>
    </head>
    <body>
        <h1 class='success'>Test images created successfully!</h1>
        <p>Images have been saved to the following locations:</p>
        <ul>";
    
    foreach ($locations as $location) {
        echo "<li>$location</li>";
    }
    
    echo "</ul>
        <h2>Preview:</h2>
        <img src='assets/img/cars/default-car.png' alt='Default Car Image'>
        <p><a href='test-upload.php'>Back to Upload Test</a></p>
    </body>
    </html>";
} else {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Test Image Creation Failed</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .error { color: red; }
        </style>
    </head>
    <body>
        <h1 class='error'>Failed to create test images!</h1>
        <p>There was an error saving the images. Please check the following:</p>
        <ul>
            <li>Directory permissions</li>
            <li>PHP GD library installation</li>
            <li>Available disk space</li>
        </ul>
        <p><a href='test-upload.php'>Back to Upload Test</a></p>
    </body>
    </html>";
}
?>
