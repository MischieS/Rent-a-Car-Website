<?php
// Include database configuration
require_once '../config/database.php';
require_once '../includes/session.php';

// Check if user is logged in and is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

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

// Function to log messages
function logMessage($message, $type = 'info') {
    echo '<div class="log-entry log-' . $type . '">' . $message . '</div>';
    ob_flush();
    flush();
}

// Start HTML output
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Update - Locations</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { padding: 20px; }
        .log-container { 
            background-color: #f8f9fa; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px;
            max-height: 400px;
            overflow-y: auto;
        }
        .log-entry { margin-bottom: 5px; }
        .log-success { color: #28a745; }
        .log-error { color: #dc3545; }
        .log-info { color: #17a2b8; }
        .log-warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Database Update - Locations</h1>
        <div class="log-container" id="log-container">';

// Create locations table if it doesn't exist
if (!tableExists($db, 'locations')) {
    logMessage("Creating locations table...", "info");
    
    try {
        $query = "CREATE TABLE locations (
            id INT(11) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            address VARCHAR(255) NOT NULL,
            city VARCHAR(100) NOT NULL,
            state VARCHAR(100) NOT NULL,
            zip_code VARCHAR(20) NOT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $db->exec($query);
        logMessage("Locations table created successfully.", "success");
    } catch (PDOException $e) {
        logMessage("Error creating locations table: " . $e->getMessage(), "error");
    }
    
    // Insert default locations
    logMessage("Adding default locations...", "info");
    
    $locations = [
        ['Airport Terminal 1', '123 Airport Blvd', 'New York', 'NY', '10001'],
        ['Downtown Office', '456 Main St', 'New York', 'NY', '10002'],
        ['Midtown Location', '789 Broadway', 'New York', 'NY', '10003'],
        ['Brooklyn Branch', '101 Atlantic Ave', 'Brooklyn', 'NY', '11201'],
        ['Queens Office', '202 Queens Blvd', 'Queens', 'NY', '11101']
    ];
    
    $query = "INSERT INTO locations (name, address, city, state, zip_code) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    
    foreach ($locations as $location) {
        $stmt->execute($location);
    }
    
    logMessage("Default locations added successfully.", "success");
} else {
    logMessage("Locations table already exists.", "info");
}

// Check if bookings table exists
if (tableExists($db, 'bookings')) {
    logMessage("Checking bookings table structure...", "info");
    
    // Check if pickup_location_id and return_location_id columns exist
    $pickup_location_id_exists = columnExists($db, 'bookings', 'pickup_location_id');
    $return_location_id_exists = columnExists($db, 'bookings', 'return_location_id');
    $pickup_location_exists = columnExists($db, 'bookings', 'pickup_location');
    $return_location_exists = columnExists($db, 'bookings', 'return_location');
    $total_price_exists = columnExists($db, 'bookings', 'total_price');
    $total_amount_exists = columnExists($db, 'bookings', 'total_amount');
    
    // Add pickup_location_id and return_location_id columns if they don't exist
    if (!$pickup_location_id_exists) {
        try {
            $db->exec("ALTER TABLE bookings ADD COLUMN pickup_location_id INT(11) DEFAULT 1 AFTER return_date");
            logMessage("Added pickup_location_id column to bookings table.", "success");
        } catch (PDOException $e) {
            logMessage("Error adding pickup_location_id column: " . $e->getMessage(), "error");
        }
    }
    
    if (!$return_location_id_exists) {
        try {
            $db->exec("ALTER TABLE bookings ADD COLUMN return_location_id INT(11) DEFAULT 1 AFTER pickup_location_id");
            logMessage("Added return_location_id column to bookings table.", "success");
        } catch (PDOException $e) {
            logMessage("Error adding return_location_id column: " . $e->getMessage(), "error");
        }
    }
    
    // Add total_price column if it doesn't exist
    if (!$total_price_exists && $total_amount_exists) {
        try {
            $db->exec("ALTER TABLE bookings CHANGE COLUMN total_amount total_price DECIMAL(10,2) NOT NULL");
            logMessage("Renamed total_amount column to total_price in bookings table.", "success");
        } catch (PDOException $e) {
            logMessage("Error renaming total_amount column: " . $e->getMessage(), "error");
        }
    } else if (!$total_price_exists && !$total_amount_exists) {
        try {
            $db->exec("ALTER TABLE bookings ADD COLUMN total_price DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER return_location_id");
            logMessage("Added total_price column to bookings table.", "success");
        } catch (PDOException $e) {
            logMessage("Error adding total_price column: " . $e->getMessage(), "error");
        }
    }
    
    // Add payment_status column if it doesn't exist
    if (!columnExists($db, 'bookings', 'payment_status')) {
        try {
            $db->exec("ALTER TABLE bookings ADD COLUMN payment_status ENUM('pending', 'paid', 'refunded', 'failed') NOT NULL DEFAULT 'pending' AFTER status");
            logMessage("Added payment_status column to bookings table.", "success");
        } catch (PDOException $e) {
            logMessage("Error adding payment_status column: " . $e->getMessage(), "error");
        }
    }
    
    // Migrate data from pickup_location and return_location to pickup_location_id and return_location_id
    if ($pickup_location_exists && $pickup_location_id_exists) {
        try {
            $stmt = $db->query("SELECT id, pickup_location FROM bookings WHERE pickup_location IS NOT NULL AND pickup_location != ''");
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($bookings as $booking) {
                // Find or create location
                $location_stmt = $db->prepare("SELECT id FROM locations WHERE name LIKE ? LIMIT 1");
                $search_term = '%' . $booking['pickup_location'] . '%';
                $location_stmt->execute([$search_term]);
                $location = $location_stmt->fetch(PDO::FETCH_ASSOC);
                
                $location_id = 1; // Default to first location
                if ($location) {
                    $location_id = $location['id'];
                } else {
                    // Create new location
                    $insert_stmt = $db->prepare("INSERT INTO locations (name, address, city, state, zip_code) VALUES (?, 'Address Unknown', 'City Unknown', 'State Unknown', 'Zip Unknown')");
                    $insert_stmt->execute([$booking['pickup_location']]);
                    $location_id = $db->lastInsertId();
                }
                
                // Update booking
                $update_stmt = $db->prepare("UPDATE bookings SET pickup_location_id = ? WHERE id = ?");
                $update_stmt->execute([$location_id, $booking['id']]);
            }
            
            logMessage("Migrated pickup_location data to pickup_location_id.", "success");
        } catch (PDOException $e) {
            logMessage("Error migrating pickup_location data: " . $e->getMessage(), "error");
        }
    }
    
    if ($return_location_exists && $return_location_id_exists) {
        try {
            $stmt = $db->query("SELECT id, return_location FROM bookings WHERE return_location IS NOT NULL AND return_location != ''");
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($bookings as $booking) {
                // Find or create location
                $location_stmt = $db->prepare("SELECT id FROM locations WHERE name LIKE ? LIMIT 1");
                $search_term = '%' . $booking['return_location'] . '%';
                $location_stmt->execute([$search_term]);
                $location = $location_stmt->fetch(PDO::FETCH_ASSOC);
                
                $location_id = 1; // Default to first location
                if ($location) {
                    $location_id = $location['id'];
                } else {
                    // Create new location
                    $insert_stmt = $db->prepare("INSERT INTO locations (name, address, city, state, zip_code) VALUES (?, 'Address Unknown', 'City Unknown', 'State Unknown', 'Zip Unknown')");
                    $insert_stmt->execute([$booking['return_location']]);
                    $location_id = $db->lastInsertId();
                }
                
                // Update booking
                $update_stmt = $db->prepare("UPDATE bookings SET return_location_id = ? WHERE id = ?");
                $update_stmt->execute([$location_id, $booking['id']]);
            }
            
            logMessage("Migrated return_location data to return_location_id.", "success");
        } catch (PDOException $e) {
            logMessage("Error migrating return_location data: " . $e->getMessage(), "error");
        }
    }
    
    // Drop old columns if migration is complete
    if ($pickup_location_exists && $pickup_location_id_exists) {
        try {
            $db->exec("ALTER TABLE bookings DROP COLUMN pickup_location");
            logMessage("Dropped pickup_location column from bookings table.", "success");
        } catch (PDOException $e) {
            logMessage("Error dropping pickup_location column: " . $e->getMessage(), "error");
        }
    }
    
    if ($return_location_exists && $return_location_id_exists) {
        try {
            $db->exec("ALTER TABLE bookings DROP COLUMN return_location");
            logMessage("Dropped return_location column from bookings table.", "success");
        } catch (PDOException $e) {
            logMessage("Error dropping return_location column: " . $e->getMessage(), "error");
        }
    }
} else {
    logMessage("Bookings table does not exist. No updates needed.", "warning");
}

// Complete HTML output
echo '</div>
        <div class="alert alert-success">
            <h4>Update Complete</h4>
            <p>The database has been updated successfully. You can now use the booking calendar.</p>
            <a href="booking-calendar.php" class="btn btn-primary">Go to Booking Calendar</a>
            <a href="index.php" class="btn btn-secondary">Return to Dashboard</a>
        </div>
    </div>
</body>
</html>';
?>
