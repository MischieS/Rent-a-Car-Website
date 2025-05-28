<?php
// Include database connection
require_once 'config/database.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Function to check if a table exists
function tableExists($db, $table) {
    try {
        $result = $db->query("SELECT 1 FROM $table LIMIT 1");
    } catch (Exception $e) {
        return false;
    }
    return $result !== false;
}

// Function to check if a column exists in a table
function columnExists($db, $table, $column) {
    try {
        $result = $db->query("SELECT $column FROM $table LIMIT 1");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Check if my-bookings.php exists
if (!file_exists('my-bookings.php')) {
    echo "Error: my-bookings.php file not found.<br>";
    exit;
}

// Read the file content
$file_content = file_get_contents('my-bookings.php');

// Check for the problematic lines and replace them
$replacements = [
    // Replace pickup_location with pickup_location_name
    '/\$booking\[\'pickup_location\'\]/' => '$booking[\'pickup_location_name\']',
    
    // Replace return_location with return_location_name
    '/\$booking\[\'return_location\'\]/' => '$booking[\'return_location_name\']',
    
    // Replace total_amount with total_price
    '/\$booking\[\'total_amount\'\]/' => '$booking[\'total_price\']'
];

$updated_content = $file_content;
$changes_made = false;

foreach ($replacements as $pattern => $replacement) {
    $new_content = preg_replace($pattern, $replacement, $updated_content);
    if ($new_content !== $updated_content) {
        $updated_content = $new_content;
        $changes_made = true;
    }
}

// Write the updated content back to the file
if ($changes_made) {
    file_put_contents('my-bookings.php', $updated_content);
    echo "Successfully updated my-bookings.php file.<br>";
} else {
    echo "No changes needed in my-bookings.php file.<br>";
}

// Check database structure
echo "<h2>Database Structure Check</h2>";

// Check if locations table exists
if (!tableExists($db, 'locations')) {
    echo "Warning: locations table does not exist. Please run admin/update-db-locations.php to create it.<br>";
} else {
    echo "Locations table exists.<br>";
}

// Check if bookings table has the required columns
if (tableExists($db, 'bookings')) {
    $required_columns = [
        'pickup_location_id',
        'return_location_id',
        'total_price',
        'payment_status'
    ];
    
    $missing_columns = [];
    foreach ($required_columns as $column) {
        if (!columnExists($db, 'bookings', $column)) {
            $missing_columns[] = $column;
        }
    }
    
    if (empty($missing_columns)) {
        echo "Bookings table has all required columns.<br>";
    } else {
        echo "Warning: Bookings table is missing the following columns: " . implode(', ', $missing_columns) . ". Please run admin/update-db-locations.php to add them.<br>";
    }
} else {
    echo "Warning: bookings table does not exist.<br>";
}

echo "<p>Fix script completed.</p>";
echo "<p><a href='my-bookings.php'>Go to My Bookings</a></p>";
echo "<p><a href='admin/update-db-locations.php'>Run Database Update Script</a></p>";
?>
