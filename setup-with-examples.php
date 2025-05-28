<?php
function setupDatabase($conn) {
    try {
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
                remember_token VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
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
        
        // Insert sample car categories
        $categories = [
            ['name' => 'Economy', 'description' => 'Fuel-efficient cars ideal for city driving'],
            ['name' => 'Compact', 'description' => 'Small cars with good handling and parking ease'],
            ['name' => 'Sedan', 'description' => 'Mid-size cars with comfortable seating and trunk space'],
            ['name' => 'SUV', 'description' => 'Spacious vehicles with higher ground clearance'],
            ['name' => 'Luxury', 'description' => 'Premium vehicles with advanced features and superior comfort'],
            ['name' => 'Convertible', 'description' => 'Cars with a removable or retractable roof']
        ];
        
        $categoryIds = [];
        foreach ($categories as $category) {
            $stmt = $conn->prepare("INSERT INTO car_categories (name, description) VALUES (:name, :description)");
            $stmt->bindParam(':name', $category['name']);
            $stmt->bindParam(':description', $category['description']);
            $stmt->execute();
            $categoryIds[] = $conn->lastInsertId();
        }
        
        // Insert sample locations
        $locations = [
            ['name' => 'Airport Terminal', 'address' => '123 Airport Road', 'city' => 'New York', 'state' => 'NY', 'zip_code' => '10001', 'phone' => '123-456-7890', 'email' => 'airport@dreamsrent.com', 'opening_hours' => 'Open 24/7'],
            ['name' => 'Downtown Office', 'address' => '456 Main Street', 'city' => 'New York', 'state' => 'NY', 'zip_code' => '10002', 'phone' => '123-456-7891', 'email' => 'downtown@dreamsrent.com', 'opening_hours' => 'Mon-Fri: 8:00 AM - 7:00 PM, Sat: 9:00 AM - 5:00 PM, Sun: 10:00 AM - 4:00 PM'],
            ['name' => 'Uptown Office', 'address' => '789 Park Avenue', 'city' => 'New York', 'state' => 'NY', 'zip_code' => '10003', 'phone' => '123-456-7892', 'email' => 'uptown@dreamsrent.com', 'opening_hours' => 'Mon-Fri: 9:00 AM - 6:00 PM, Sat: 10:00 AM - 4:00 PM, Sun: Closed']
        ];
        
        $locationIds = [];
        foreach ($locations as $location) {
            $stmt = $conn->prepare("INSERT INTO locations (name, address, city, state, zip_code, phone, email, opening_hours) VALUES (:name, :address, :city, :state, :zip_code, :phone, :email, :opening_hours)");
            $stmt->bindParam(':name', $location['name']);
            $stmt->bindParam(':address', $location['address']);
            $stmt->bindParam(':city', $location['city']);
            $stmt->bindParam(':state', $location['state']);
            $stmt->bindParam(':zip_code', $location['zip_code']);
            $stmt->bindParam(':phone', $location['phone']);
            $stmt->bindParam(':email', $location['email']);
            $stmt->bindParam(':opening_hours', $location['opening_hours']);
            $stmt->execute();
            $locationIds[] = $conn->lastInsertId();
        }
        
        // Insert sample users
        $users = [
            ['first_name' => 'Test', 'last_name' => 'User', 'email' => 'test@gmail.com', 'phone' => '123-456-7890', 'password' => password_hash('test1234', PASSWORD_DEFAULT), 'role' => 'user'],
            ['first_name' => 'Admin', 'last_name' => 'User', 'email' => 'admin@gmail.com', 'phone' => '123-456-7891', 'password' => password_hash('admin1234', PASSWORD_DEFAULT), 'role' => 'admin'],
            ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john@example.com', 'phone' => '123-456-7892', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'role' => 'user']
        ];
        
        $userIds = [];
        foreach ($users as $user) {
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password, role) VALUES (:first_name, :last_name, :email, :phone, :password, :role)");
            $stmt->bindParam(':first_name', $user['first_name']);
            $stmt->bindParam(':last_name', $user['last_name']);
            $stmt->bindParam(':email', $user['email']);
            $stmt->bindParam(':phone', $user['phone']);
            $stmt->bindParam(':password', $user['password']);
            $stmt->bindParam(':role', $user['role']);
            $stmt->execute();
            $userIds[] = $conn->lastInsertId();
        }
        
        // Insert sample cars
        $cars = [
            ['category_id' => $categoryIds[0], 'brand' => 'Toyota', 'model' => 'Corolla', 'year' => 2022, 'color' => 'White', 'transmission' => 'automatic', 'fuel_type' => 'petrol', 'seats' => 5, 'price_per_day' => 45.00, 'availability' => 1, 'description' => 'Fuel-efficient and reliable compact car, perfect for city driving.'],
            ['category_id' => $categoryIds[1], 'brand' => 'Honda', 'model' => 'Civic', 'year' => 2021, 'color' => 'Blue', 'transmission' => 'automatic', 'fuel_type' => 'petrol', 'seats' => 5, 'price_per_day' => 50.00, 'availability' => 1, 'description' => 'Sporty compact car with excellent handling and fuel economy.'],
            ['category_id' => $categoryIds[2], 'brand' => 'Ford', 'model' => 'Fusion', 'year' => 2020, 'color' => 'Silver', 'transmission' => 'automatic', 'fuel_type' => 'hybrid', 'seats' => 5, 'price_per_day' => 55.00, 'availability' => 1, 'description' => 'Mid-size sedan with hybrid technology for excellent fuel efficiency.'],
            ['category_id' => $categoryIds[3], 'brand' => 'Jeep', 'model' => 'Cherokee', 'year' => 2021, 'color' => 'Black', 'transmission' => 'automatic', 'fuel_type' => 'diesel', 'seats' => 5, 'price_per_day' => 70.00, 'availability' => 1, 'description' => 'Versatile SUV with off-road capabilities and spacious interior.'],
            ['category_id' => $categoryIds[4], 'brand' => 'BMW', 'model' => '5 Series', 'year' => 2022, 'color' => 'Black', 'transmission' => 'automatic', 'fuel_type' => 'petrol', 'seats' => 5, 'price_per_day' => 120.00, 'availability' => 1, 'description' => 'Luxury sedan with premium features and powerful performance.'],
            ['category_id' => $categoryIds[5], 'brand' => 'Mazda', 'model' => 'MX-5 Miata', 'year' => 2021, 'color' => 'Red', 'transmission' => 'manual', 'fuel_type' => 'petrol', 'seats' => 2, 'price_per_day' => 85.00, 'availability' => 1, 'description' => 'Fun-to-drive convertible with excellent handling and sporty design.']
        ];
        
        $carIds = [];
        foreach ($cars as $car) {
            $stmt = $conn->prepare("INSERT INTO cars (category_id, brand, model, year, color, transmission, fuel_type, seats, price_per_day, availability, description) VALUES (:category_id, :brand, :model, :year, :color, :transmission, :fuel_type, :seats, :price_per_day, :availability, :description)");
            $stmt->bindParam(':category_id', $car['category_id']);
            $stmt->bindParam(':brand', $car['brand']);
            $stmt->bindParam(':model', $car['model']);
            $stmt->bindParam(':year', $car['year']);
            $stmt->bindParam(':color', $car['color']);
            $stmt->bindParam(':transmission', $car['transmission']);
            $stmt->bindParam(':fuel_type', $car['fuel_type']);
            $stmt->bindParam(':seats', $car['seats']);
            $stmt->bindParam(':price_per_day', $car['price_per_day']);
            $stmt->bindParam(':availability', $car['availability']);
            $stmt->bindParam(':description', $car['description']);
            $stmt->execute();
            $carIds[] = $conn->lastInsertId();
        }
        
        // Insert sample bookings
        $bookings = [
            ['user_id' => $userIds[0], 'car_id' => $carIds[0], 'pickup_location_id' => $locationIds[0], 'return_location_id' => $locationIds[0], 'pickup_date' => date('Y-m-d H:i:s', strtotime('+1 day')), 'return_date' => date('Y-m-d H:i:s', strtotime('+4 days')), 'total_price' => 135.00, 'status' => 'confirmed', 'payment_status' => 'paid'],
            ['user_id' => $userIds[0], 'car_id' => $carIds[1], 'pickup_location_id' => $locationIds[1], 'return_location_id' => $locationIds[2], 'pickup_date' => date('Y-m-d H:i:s', strtotime('+7 days')), 'return_date' => date('Y-m-d H:i:s', strtotime('+10 days')), 'total_price' => 150.00, 'status' => 'pending', 'payment_status' => 'pending'],
            ['user_id' => $userIds[2], 'car_id' => $carIds[4], 'pickup_location_id' => $locationIds[2], 'return_location_id' => $locationIds[0], 'pickup_date' => date('Y-m-d H:i:s', strtotime('+2 days')), 'return_date' => date('Y-m-d H:i:s', strtotime('+5 days')), 'total_price' => 360.00, 'status' => 'confirmed', 'payment_status' => 'paid']
        ];
        
        foreach ($bookings as $booking) {
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, car_id, pickup_location_id, return_location_id, pickup_date, return_date, total_price, status, payment_status) VALUES (:user_id, :car_id, :pickup_location_id, :return_location_id, :pickup_date, :return_date, :total_price, :status, :payment_status)");
            $stmt->bindParam(':user_id', $booking['user_id']);
            $stmt->bindParam(':car_id', $booking['car_id']);
            $stmt->bindParam(':pickup_location_id', $booking['pickup_location_id']);
            $stmt->bindParam(':return_location_id', $booking['return_location_id']);
            $stmt->bindParam(':pickup_date', $booking['pickup_date']);
            $stmt->bindParam(':return_date', $booking['return_date']);
            $stmt->bindParam(':total_price', $booking['total_price']);
            $stmt->bindParam(':status', $booking['status']);
            $stmt->bindParam(':payment_status', $booking['payment_status']);
            $stmt->execute();
        }
        
        return true;
    } catch(PDOException $e) {
        echo "Setup Error: " . $e->getMessage();
        return false;
    }
}
?>
