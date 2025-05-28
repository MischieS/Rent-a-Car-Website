<?php
// Script to create necessary directories and default images

// Define directories to create
$directories = [
    'assets/img/cars',
    'uploads/cars',
    'uploads/profile_images'
];

// Create directories with proper permissions
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "Created directory: $dir<br>";
            chmod($dir, 0777);
        } else {
            echo "Failed to create directory: $dir<br>";
        }
    } else {
        echo "Directory already exists: $dir<br>";
        // Make sure it's writable
        chmod($dir, 0777);
    }
}

// Create a default car image if it doesn't exist
$default_car_path = 'assets/img/cars/default-car.png';
if (!file_exists($default_car_path)) {
    // Create a simple default car image (a colored rectangle with text)
    $image = imagecreatetruecolor(800, 600);
    $bg_color = imagecolorallocate($image, 200, 200, 200);
    $text_color = imagecolorallocate($image, 50, 50, 50);
    
    imagefill($image, 0, 0, $bg_color);
    imagestring($image, 5, 300, 280, 'Default Car Image', $text_color);
    
    // Save the image
    if (imagejpeg($image, $default_car_path, 90)) {
        echo "Created default car image at: $default_car_path<br>";
    } else {
        echo "Failed to create default car image<br>";
    }
    
    imagedestroy($image);
} else {
    echo "Default car image already exists<br>";
}

// Create a default profile image if it doesn't exist
$default_profile_path = 'assets/img/users/default-profile.jpg';
if (!file_exists('assets/img/users')) {
    mkdir('assets/img/users', 0777, true);
}

if (!file_exists($default_profile_path)) {
    // Create a simple default profile image (a colored circle)
    $image = imagecreatetruecolor(200, 200);
    $bg_color = imagecolorallocate($image, 240, 240, 240);
    $circle_color = imagecolorallocate($image, 100, 100, 200);
    
    imagefill($image, 0, 0, $bg_color);
    imagefilledellipse($image, 100, 100, 150, 150, $circle_color);
    
    // Save the image
    if (imagejpeg($image, $default_profile_path, 90)) {
        echo "Created default profile image at: $default_profile_path<br>";
    } else {
        echo "Failed to create default profile image<br>";
    }
    
    imagedestroy($image);
} else {
    echo "Default profile image already exists<br>";
}

echo "<p>All directories and default images have been created.</p>";
echo "<p>Current working directory: " . getcwd() . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

// Check if GD library is available
if (function_exists('gd_info')) {
    echo "<p>GD Library is available. Version info:<pre>";
    print_r(gd_info());
    echo "</pre></p>";
} else {
    echo "<p>GD Library is NOT available. This script requires GD to create default images.</p>";
}

// Check permissions
echo "<p>Directory permissions:</p>";
foreach ($directories as $dir) {
    echo "$dir: " . substr(sprintf('%o', fileperms($dir)), -4) . "<br>";
}
?>
