<?php
// Include database configuration
require_once 'config/database.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

try {
    // Start transaction
    $db->beginTransaction();
    
    // Clear existing locations
    $db->exec("TRUNCATE TABLE locations");
    
    // Insert new locations for Turkey
    $locations = [
        ['İstanbul Havalimanı', 'İstanbul Havalimanı Terminal 1', 'İstanbul', 'Marmara', '34000', '+90 212 555 1234', 'istanbul.airport@rentacar.com', 'Open 24/7'],
        ['İstanbul Şehir Merkezi', 'Taksim Meydanı', 'İstanbul', 'Marmara', '34010', '+90 212 555 5678', 'istanbul.city@rentacar.com', 'Mon-Sun: 8:00 AM - 8:00 PM'],
        ['Ankara Havalimanı', 'Esenboğa Havalimanı Terminal', 'Ankara', 'İç Anadolu', '06000', '+90 312 555 1234', 'ankara.airport@rentacar.com', 'Open 24/7'],
        ['Ankara Şehir Merkezi', 'Kızılay Meydanı', 'Ankara', 'İç Anadolu', '06010', '+90 312 555 5678', 'ankara.city@rentacar.com', 'Mon-Sun: 8:00 AM - 8:00 PM'],
        ['İzmir Havalimanı', 'Adnan Menderes Havalimanı Terminal', 'İzmir', 'Ege', '35000', '+90 232 555 1234', 'izmir.airport@rentacar.com', 'Open 24/7'],
        ['İzmir Şehir Merkezi', 'Konak Meydanı', 'İzmir', 'Ege', '35010', '+90 232 555 5678', 'izmir.city@rentacar.com', 'Mon-Sun: 8:00 AM - 8:00 PM']
    ];
    
    $stmt = $db->prepare("INSERT INTO locations (name, address, city, state, zip_code, phone, email, opening_hours) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($locations as $location) {
        $stmt->execute($location);
    }
    
    // Commit transaction
    $db->commit();
    
    echo '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">';
    echo '<h2 style="color: #4CAF50;">Success!</h2>';
    echo '<p>Locations have been updated to İstanbul, Ankara, and İzmir.</p>';
    echo '<p>The following locations were added:</p>';
    echo '<ul>';
    foreach ($locations as $location) {
        echo '<li><strong>' . $location[0] . '</strong> - ' . $location[1] . ', ' . $location[2] . '</li>';
    }
    echo '</ul>';
    echo '<p><a href="index.php" style="display: inline-block; background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;">Return to Home Page</a></p>';
    echo '</div>';
    
} catch (PDOException $e) {
    // Rollback transaction on error
    $db->rollBack();
    
    echo '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #fff0f0;">';
    echo '<h2 style="color: #F44336;">Error</h2>';
    echo '<p>An error occurred while updating locations:</p>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '<p><a href="index.php" style="display: inline-block; background-color: #F44336; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;">Return to Home Page</a></p>';
    echo '</div>';
}
?>
