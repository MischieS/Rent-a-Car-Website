<?php
// This script will help find where your project is located

echo "<h1>Project Location Finder</h1>";

// Check common XAMPP locations
$possible_paths = [
    '/Applications/XAMPP/htdocs/',
    '/opt/lampp/htdocs/',
    'C:/xampp/htdocs/',
    $_SERVER['DOCUMENT_ROOT'] . '/',
];

echo "<h2>Checking Common XAMPP Locations:</h2>";

foreach ($possible_paths as $path) {
    if (is_dir($path)) {
        echo "<p>✅ Found: $path</p>";
        
        // Look for car rental projects
        $dirs = glob($path . '*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $dirname = basename($dir);
            if (stripos($dirname, 'car') !== false || stripos($dirname, 'rent') !== false) {
                echo "<p>&nbsp;&nbsp;&nbsp;📁 Possible project: <strong>$dirname</strong></p>";
                
                // Check if it has admin folder
                if (is_dir($dir . '/admin')) {
                    echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✅ Has admin folder</p>";
                }
                
                // Check if it has car-edit.php
                if (file_exists($dir . '/admin/car-edit.php')) {
                    echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;✅ Has car-edit.php</p>";
                    echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;🔗 Try: <a href='http://localhost/$dirname/admin/car-edit.php?id=1'>localhost/$dirname/admin/car-edit.php?id=1</a></p>";
                }
            }
        }
    } else {
        echo "<p>❌ Not found: $path</p>";
    }
}

// Show current location info
echo "<h2>Current Script Location:</h2>";
echo "<p>Full path: " . __FILE__ . "</p>";
echo "<p>Directory: " . dirname(__FILE__) . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

// Try to determine the correct URL
$script_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', __FILE__);
$script_path = str_replace('\\', '/', $script_path); // Windows compatibility
echo "<p>Script URL path: $script_path</p>";

$base_url = dirname($script_path);
if ($base_url === '/') $base_url = '';

echo "<h2>Suggested URLs to try:</h2>";
echo "<ul>";
echo "<li><a href='http://localhost$base_url/admin/simple-car-edit.php?id=1'>Simple Car Edit</a></li>";
echo "<li><a href='http://localhost$base_url/check-structure.php'>Structure Check</a></li>";
echo "<li><a href='http://localhost$base_url/admin/'>Admin Directory</a></li>";
echo "</ul>";

// List all directories in htdocs
if (is_dir($_SERVER['DOCUMENT_ROOT'])) {
    echo "<h2>All Projects in htdocs:</h2>";
    $dirs = glob($_SERVER['DOCUMENT_ROOT'] . '/*', GLOB_ONLYDIR);
    echo "<ul>";
    foreach ($dirs as $dir) {
        $dirname = basename($dir);
        echo "<li><a href='http://localhost/$dirname/'>$dirname</a>";
        if (is_dir($dir . '/admin')) {
            echo " (has admin folder)";
        }
        echo "</li>";
    }
    echo "</ul>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Find Project Location</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #333; }
        p { margin: 5px 0; }
        ul { margin: 10px 0; }
        li { margin: 2px 0; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <!-- Content generated above -->
</body>
</html>
