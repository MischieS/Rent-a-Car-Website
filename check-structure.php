<?php
// This file will help us understand your project structure
echo "<h1>Project Structure Check</h1>";

// Get current directory
echo "<h2>Current Directory Information</h2>";
echo "<p><strong>Current working directory:</strong> " . getcwd() . "</p>";
echo "<p><strong>Script filename:</strong> " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<p><strong>Document root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";

// Check if we're in the right place
echo "<h2>File Structure Check</h2>";
$files_to_check = [
    'index.php',
    'admin/index.php',
    'admin/car-edit.php',
    'admin/car-add.php',
    'config/database.php',
    'assets/css/style.css'
];

foreach ($files_to_check as $file) {
    echo "<p>$file: " . (file_exists($file) ? "✅ EXISTS" : "❌ NOT FOUND") . "</p>";
}

// Check directories
echo "<h2>Directory Structure</h2>";
$dirs_to_check = [
    'admin',
    'assets',
    'assets/img',
    'assets/img/cars',
    'uploads',
    'uploads/cars',
    'config'
];

foreach ($dirs_to_check as $dir) {
    echo "<p>$dir/: " . (is_dir($dir) ? "✅ EXISTS" : "❌ NOT FOUND") . "</p>";
}

// Show all files in current directory
echo "<h2>Files in Current Directory</h2>";
$files = scandir('.');
echo "<ul>";
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "<li>$file " . (is_dir($file) ? "(directory)" : "(file)") . "</li>";
    }
}
echo "</ul>";

// Show admin directory contents if it exists
if (is_dir('admin')) {
    echo "<h2>Files in Admin Directory</h2>";
    $admin_files = scandir('admin');
    echo "<ul>";
    foreach ($admin_files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>admin/$file " . (is_dir("admin/$file") ? "(directory)" : "(file)") . "</li>";
        }
    }
    echo "</ul>";
}

// Check what URL patterns work
echo "<h2>URL Testing</h2>";
echo "<p>Try these URLs to find the correct path:</p>";
echo "<ul>";
echo "<li><a href='/car/check-structure.php'>localhost/car/check-structure.php</a></li>";
echo "<li><a href='/rentacar/check-structure.php'>localhost/rentacar/check-structure.php</a></li>";
echo "<li><a href='/check-structure.php'>localhost/check-structure.php</a></li>";
echo "</ul>";

// PHP Info (commented out for security, uncomment if needed)
// echo "<h2>PHP Info</h2>";
// phpinfo();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Project Structure Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #333; }
        p { margin: 5px 0; }
        ul { margin: 10px 0; }
        li { margin: 2px 0; }
    </style>
</head>
<body>
    <!-- Content is generated above -->
</body>
</html>
