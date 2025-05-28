-- Create the database
CREATE DATABASE IF NOT EXISTS rent_a_car;
USE rent_a_car;

-- Users table
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Car categories table
CREATE TABLE IF NOT EXISTS car_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Cars table
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    color VARCHAR(30) NOT NULL,
    transmission ENUM('automatic', 'manual') NOT NULL,
    fuel_type ENUM('petrol', 'diesel', 'electric', 'hybrid') NOT NULL,
    seats INT NOT NULL,
    price_per_day DECIMAL(10, 2) NOT NULL,
    availability BOOLEAN DEFAULT TRUE,
    image VARCHAR(255) DEFAULT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES car_categories(id) ON DELETE CASCADE
);

-- Locations table
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
);

-- Bookings table
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
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('credit_card', 'debit_card', 'paypal', 'cash') NOT NULL,
    transaction_id VARCHAR(100) DEFAULT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Reviews table
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
);

-- Insert sample car categories
INSERT INTO car_categories (name, description) VALUES
('Economy', 'Fuel-efficient cars ideal for city driving'),
('Compact', 'Small cars with good handling and parking ease'),
('Sedan', 'Mid-size cars with comfortable seating and trunk space'),
('SUV', 'Spacious vehicles with higher ground clearance'),
('Luxury', 'Premium vehicles with advanced features and superior comfort'),
('Convertible', 'Cars with a removable or retractable roof');

-- Insert sample locations
INSERT INTO locations (name, address, city, state, zip_code, phone, email, opening_hours) VALUES
('Istanbul Airport', 'Istanbul Airport Terminal 1', 'Istanbul', 'Marmara', '34000', '+90 212 555 1234', 'istanbul.airport@rentacar.com', 'Open 24/7'),
('Istanbul City Center', 'Taksim Square', 'Istanbul', 'Marmara', '34010', '+90 212 555 5678', 'istanbul.city@rentacar.com', 'Mon-Sun: 8:00 AM - 8:00 PM'),
('Ankara Airport', 'Esenboga Airport Terminal', 'Ankara', 'Central Anatolia', '06000', '+90 312 555 1234', 'ankara.airport@rentacar.com', 'Open 24/7'),
('Ankara City Center', 'Kizilay Square', 'Ankara', 'Central Anatolia', '06010', '+90 312 555 5678', 'ankara.city@rentacar.com', 'Mon-Sun: 8:00 AM - 8:00 PM'),
('Izmir Airport', 'Adnan Menderes Airport Terminal', 'Izmir', 'Aegean', '35000', '+90 232 555 1234', 'izmir.airport@rentacar.com', 'Open 24/7'),
('Izmir City Center', 'Konak Square', 'Izmir', 'Aegean', '35010', '+90 232 555 5678', 'izmir.city@rentacar.com', 'Mon-Sun: 8:00 AM - 8:00 PM');

-- Insert admin user (password: admin123)
INSERT INTO users (first_name, last_name, email, phone, password, role) VALUES
('Admin', 'User', 'admin@rentacar.com', '+90 555 123 4567', '$2y$10$8SOl8DPYVQxF0P5eFEp3A.MKL.3.O9.TT8lyHt1WCHSzlXGJwnMry', 'admin');
