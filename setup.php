<?php
// Display all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$host = "localhost";
$username = "root";
$password = "";
$db_name = "rent_a_car";

echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;'>";
echo "<h1>DreamsRent Database Setup</h1>";

try {
    echo "<div style='background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 15px;'>";
    echo "<h2>Step 1: Connecting to MySQL Server</h2>";
    
    // Connect to MySQL without a database
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✓ Connected to MySQL server successfully</p>";
    echo "</div>";
    
    echo "<div style='background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 15px;'>";
    echo "<h2>Step 2: Creating Database</h2>";
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
    $conn->exec($sql);
    
    echo "<p style='color: green;'>✓ Database '$db_name' created successfully or already exists</p>";
    echo "</div>";
    
    echo "<div style='background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 15px;'>";
    echo "<h2>Step 3: Creating Tables</h2>";
    
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create users table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            phone VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL,
            address VARCHAR(255) DEFAULT NULL,
            city VARCHAR(50) DEFAULT NULL,
            state VARCHAR(50) DEFAULT NULL,
            zip_code VARCHAR(20) DEFAULT NULL,
            license_number VARCHAR(50) DEFAULT NULL,
            license_expiry DATE DEFAULT NULL,
            role ENUM('user', 'admin') DEFAULT 'user',
            status ENUM('active', 'inactive') DEFAULT 'active',
            profile_image VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p style='color: green;'>✓ Table 'users' created successfully</p>";
    
    // Create car_categories table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS car_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p style='color: green;'>✓ Table 'car_categories' created successfully</p>";
    
    // Create cars table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS cars (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category_id INT NOT NULL,
            brand VARCHAR(50) NOT NULL,
            model VARCHAR(50) NOT NULL,
            year INT NOT NULL,
            color VARCHAR(30) DEFAULT NULL,
            transmission ENUM('automatic', 'manual') NOT NULL,
            fuel_type ENUM('petrol', 'diesel', 'electric', 'hybrid') NOT NULL,
            seats INT DEFAULT NULL,
            price_per_day DECIMAL(10, 2) NOT NULL,
            availability BOOLEAN DEFAULT TRUE,
            image VARCHAR(255) DEFAULT NULL,
            images TEXT DEFAULT NULL,
            description TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES car_categories(id) ON DELETE CASCADE
        )
    ");
    echo "<p style='color: green;'>✓ Table 'cars' created successfully</p>";
    
    // Create locations table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS locations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            address VARCHAR(255) NOT NULL,
            city VARCHAR(50) NOT NULL,
            state VARCHAR(50) NOT NULL,
            zip_code VARCHAR(20) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            email VARCHAR(100) NOT NULL,
            opening_hours VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p style='color: green;'>✓ Table 'locations' created successfully</p>";
    
    // Create bookings table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            car_id INT NOT NULL,
            pickup_location_id INT NOT NULL,
            return_location_id INT NOT NULL,
            pickup_date DATETIME NOT NULL,
            return_date DATETIME NOT NULL,
            total_price DECIMAL(10, 2) NOT NULL,
            status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
            payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
            FOREIGN KEY (pickup_location_id) REFERENCES locations(id) ON DELETE CASCADE,
            FOREIGN KEY (return_location_id) REFERENCES locations(id) ON DELETE CASCADE
        )
    ");
    echo "<p style='color: green;'>✓ Table 'bookings' created successfully</p>";
    
    // Create payments table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            booking_id INT NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            payment_method ENUM('credit_card', 'debit_card', 'paypal', 'cash') NOT NULL,
            transaction_id VARCHAR(100) DEFAULT NULL,
            payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
            FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
        )
    ");
    echo "<p style='color: green;'>✓ Table 'payments' created successfully</p>";
    
    // Create reviews table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            car_id INT NOT NULL,
            booking_id INT NOT NULL,
            rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
            FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
        )
    ");
    echo "<p style='color: green;'>✓ Table 'reviews' created successfully</p>";
    
    echo "<div style='background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 15px;'>";
    echo "<h2>Step 4: Inserting Sample Data</h2>";
    
    // Insert sample car categories
    $stmt = $conn->query("SELECT COUNT(*) FROM car_categories");
    $categoryCount = $stmt->fetchColumn();
    
    if ($categoryCount == 0) {
        $conn->exec("
            INSERT INTO car_categories (name, description) VALUES
            ('Economy', 'Fuel-efficient cars ideal for city driving'),
            ('Compact', 'Small cars with good handling and parking ease'),
            ('Sedan', 'Mid-size cars with comfortable seating and trunk space'),
            ('SUV', 'Spacious vehicles with higher ground clearance'),
            ('Luxury', 'Premium vehicles with advanced features and superior comfort'),
            ('Convertible', 'Cars with a removable or retractable roof')
        ");
        echo "<p style='color: green;'>✓ Sample car categories inserted</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Sample car categories already exist</p>";
    }
    
    // Insert sample locations
    $stmt = $conn->query("SELECT COUNT(*) FROM locations");
    $locationCount = $stmt->fetchColumn();
    
    if ($locationCount == 0) {
        $conn->exec("
            INSERT INTO locations (name, address, city, state, zip_code, phone, email, opening_hours) VALUES
            ('Airport Terminal', '123 Airport Road', 'New York', 'NY', '10001', '123-456-7890', 'airport@dreamsrent.com', 'Open 24/7'),
            ('Downtown Office', '456 Main Street', 'New York', 'NY', '10002', '123-456-7891', 'downtown@dreamsrent.com', 'Mon-Fri: 8:00 AM - 7:00 PM, Sat: 9:00 AM - 5:00 PM, Sun: 10:00 AM - 4:00 PM'),
            ('Uptown Office', '789 Park Avenue', 'New York', 'NY', '10003', '123-456-7892', 'uptown@dreamsrent.com', 'Mon-Fri: 9:00 AM - 6:00 PM, Sat: 10:00 AM - 4:00 PM, Sun: Closed')
        ");
        echo "<p style='color: green;'>✓ Sample locations inserted</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Sample locations already exist</p>";
    }
    
    // Check if admin user exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $adminEmail = 'admin@dreamsrent.com';
    $stmt->bindParam(':email', $adminEmail);
    $stmt->execute();
    $adminExists = $stmt->fetchColumn() > 0;
    
    if (!$adminExists) {
        // Insert admin user (password: admin123)
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            INSERT INTO users (first_name, last_name, email, phone, password, role, status) VALUES
            (:first_name, :last_name, :email, :phone, :password, :role, :status)
        ");
        
        $firstName = 'Admin';
        $lastName = 'User';
        $phone = '123-456-7890';
        $role = 'admin';
        $status = 'active';
        
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $adminEmail);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $adminPassword);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':status', $status);
        
        $stmt->execute();
        echo "<p style='color: green;'>✓ Admin user inserted</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Admin user already exists</p>";
    }
    
    // Count rows in users table to check if data was inserted
    $stmt = $conn->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    
    echo "<p>Users in database: $userCount</p>";
    echo "</div>";
    
    echo "<div style='background-color: #e8f5e9; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h2 style='color: #2e7d32;'>Setup Completed Successfully!</h2>";
    echo "<p>Your database has been set up and is ready to use.</p>";
    echo "<p><strong>Admin User:</strong> admin@dreamsrent.com</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><a href='index.php' style='display: inline-block; background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Go to Homepage</a></p>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div style='background-color: #ffebee; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h2 style='color: #c62828;'>Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "</div>";
?>
