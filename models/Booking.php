<?php
class Booking {
    // Database connection and table name
    private $conn;
    private $table_name = "bookings";

    // Object properties
    public $id;
    public $user_id;
    public $car_id;
    public $pickup_date;
    public $return_date;
    public $pickup_location_id;
    public $return_location_id;
    public $total_price;
    public $status;
    public $payment_status;
    public $created_at;
    public $updated_at;

    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to create a new booking
    public function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_id=:user_id,
                    car_id=:car_id,
                    pickup_date=:pickup_date,
                    return_date=:return_date,
                    pickup_location_id=:pickup_location_id,
                    return_location_id=:return_location_id,
                    total_price=:total_price,
                    status=:status,
                    payment_status=:payment_status,
                    created_at=NOW(),
                    updated_at=NOW()";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->car_id = htmlspecialchars(strip_tags($this->car_id));
        $this->pickup_date = htmlspecialchars(strip_tags($this->pickup_date));
        $this->return_date = htmlspecialchars(strip_tags($this->return_date));
        $this->pickup_location_id = htmlspecialchars(strip_tags($this->pickup_location_id));
        $this->return_location_id = htmlspecialchars(strip_tags($this->return_location_id));
        $this->total_price = htmlspecialchars(strip_tags($this->total_price));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->payment_status = htmlspecialchars(strip_tags($this->payment_status));

        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":car_id", $this->car_id);
        $stmt->bindParam(":pickup_date", $this->pickup_date);
        $stmt->bindParam(":return_date", $this->return_date);
        $stmt->bindParam(":pickup_location_id", $this->pickup_location_id);
        $stmt->bindParam(":return_location_id", $this->return_location_id);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":payment_status", $this->payment_status);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Method to read all bookings
    public function read() {
        // Select all query
        $query = "SELECT
                    id, user_id, car_id, pickup_date, return_date, pickup_location_id, return_location_id, status, payment_status, total_price, created_at, updated_at
                FROM
                    " . $this->table_name . "
                ORDER BY
                    created_at DESC";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // Method to read a single booking
    public function readOne() {
        // Query to read single record
        $query = "SELECT
                    id, user_id, car_id, pickup_date, return_date, pickup_location_id, return_location_id, status, payment_status, total_price, created_at, updated_at
                FROM
                    " . $this->table_name . "
                WHERE
                    id = ?
                LIMIT
                    0,1";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind id of product to be updated
        $stmt->bindParam(1, $this->id);

        // Execute query
        $stmt->execute();

        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set values to object properties
        $this->user_id = $row['user_id'];
        $this->car_id = $row['car_id'];
        $this->pickup_date = $row['pickup_date'];
        $this->return_date = $row['return_date'];
        $this->pickup_location_id = $row['pickup_location_id'];
        $this->return_location_id = $row['return_location_id'];
        $this->status = $row['status'];
        $this->payment_status = $row['payment_status'];
        $this->total_price = $row['total_price'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }

    // Method to update a booking
    public function update() {
        // Query to update record
        $query = "UPDATE
                    " . $this->table_name . "
                SET
                    user_id=:user_id,
                    car_id=:car_id,
                    pickup_date=:pickup_date,
                    return_date=:return_date,
                    pickup_location_id=:pickup_location_id,
                    return_location_id=:return_location_id,
                    total_price=:total_price,
                    status=:status,
                    payment_status=:payment_status,
                    updated_at=NOW()
                WHERE
                    id = :id";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->car_id = htmlspecialchars(strip_tags($this->car_id));
        $this->pickup_date = htmlspecialchars(strip_tags($this->pickup_date));
        $this->return_date = htmlspecialchars(strip_tags($this->return_date));
        $this->pickup_location_id = htmlspecialchars(strip_tags($this->pickup_location_id));
        $this->return_location_id = htmlspecialchars(strip_tags($this->return_location_id));
        $this->total_price = htmlspecialchars(strip_tags($this->total_price));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->payment_status = htmlspecialchars(strip_tags($this->payment_status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":car_id", $this->car_id);
        $stmt->bindParam(":pickup_date", $this->pickup_date);
        $stmt->bindParam(":return_date", $this->return_date);
        $stmt->bindParam(":pickup_location_id", $this->pickup_location_id);
        $stmt->bindParam(":return_location_id", $this->return_location_id);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":payment_status", $this->payment_status);
        $stmt->bindParam(":id", $this->id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Method to delete a booking
    public function delete() {
        // Query to delete record
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind id of record to delete
        $stmt->bindParam(1, $this->id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Method to search bookings
    public function search($keywords) {
        // Select all query
        $query = "SELECT
                    id, user_id, car_id, pickup_date, return_date, pickup_location_id, return_location_id, status, payment_status, total_price, created_at, updated_at
                FROM
                    " . $this->table_name . "
                WHERE
                    user_id LIKE ? OR
                    car_id LIKE ? OR
                    pickup_location_id LIKE ? OR
                    return_location_id LIKE ?
                ORDER BY
                    created_at DESC";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        // Bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->bindParam(4, $keywords);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    // Method to read bookings with pagination
    public function readPaging($from_record_num, $records_per_page) {
        // Select query
        $query = "SELECT
                    id, user_id, car_id, pickup_date, return_date, pickup_location_id, return_location_id, status, payment_status, total_price, created_at, updated_at
                FROM
                    " . $this->table_name . "
                ORDER BY created_at DESC
                LIMIT ?, ?";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind variable values
        $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

        // Execute query
        $stmt->execute();

        // Return values from database
        return $stmt;
    }

    // Method to count all bookings
    public function count() {
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . "";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total_rows'];
    }

    // Method to get all locations
    public function getAllLocations() {
        // Check if locations table exists
        $query = "SHOW TABLES LIKE 'locations'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Locations table exists, get all locations
            $query = "SELECT * FROM locations ORDER BY name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Locations table doesn't exist, return default locations
            return [
                ['id' => 1, 'name' => 'Main Office - Downtown'],
                ['id' => 2, 'name' => 'Airport Terminal 1'],
                ['id' => 3, 'name' => 'Airport Terminal 2'],
                ['id' => 4, 'name' => 'North Branch'],
                ['id' => 5, 'name' => 'South Branch'],
                ['id' => 6, 'name' => 'East Branch'],
                ['id' => 7, 'name' => 'West Branch'],
                ['id' => 8, 'name' => 'Central Station'],
                ['id' => 9, 'name' => 'Shopping Mall']
            ];
        }
    }

    // Method to get bookings for calendar
    public function getBookingsForCalendar($car_id = null, $status = null) {
        // Create query
        $query = "SELECT b.id, 
                  b.pickup_date, 
                  b.return_date, 
                  b.status,
                  b.user_id,
                  b.car_id,
                  CONCAT(c.brand, ' ', c.model, ' (', c.year, ')') as car_name,
                  c.image as car_image,
                  CONCAT(u.first_name, ' ', u.last_name) as user_name,
                  u.email as user_email,
                  pl.name as pickup_location_name,
                  rl.name as return_location_name,
                  b.total_price
                  FROM " . $this->table_name . " b
                  LEFT JOIN cars c ON b.car_id = c.id
                  LEFT JOIN users u ON b.user_id = u.id
                  LEFT JOIN locations pl ON b.pickup_location_id = pl.id
                  LEFT JOIN locations rl ON b.return_location_id = rl.id
                  WHERE 1=1";
        
        // Add filters
        if ($car_id && !empty($car_id)) {
            $query .= " AND b.car_id = :car_id";
        }
        
        if ($status && !empty($status)) {
            $query .= " AND b.status = :status";
        }
        
        $query .= " ORDER BY b.pickup_date ASC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        if ($car_id && !empty($car_id)) {
            $stmt->bindParam(':car_id', $car_id);
        }
        
        if ($status && !empty($status)) {
            $stmt->bindParam(':status', $status);
        }
        
        // Execute query
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to get bookings for a specific date range
    public function getBookingsForDateRange($start_date, $end_date) {
        try {
            // For debugging
            // echo "Start Date: " . $start_date . "<br>";
            // echo "End Date: " . $end_date . "<br>";
            
            // Check if the bookings table exists
            $tableCheck = $this->conn->query("SHOW TABLES LIKE '" . $this->table_name . "'");
            if ($tableCheck->rowCount() == 0) {
                // Table doesn't exist, return empty array
                return [];
            }
            
            // Use a simpler query for now to avoid parameter binding issues
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $all_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filtered_bookings = [];
            
            // Filter bookings manually in PHP
            foreach ($all_bookings as $booking) {
                $pickup = $booking['pickup_date'];
                $return = $booking['return_date'];
                
                // Check if booking overlaps with the requested date range
                if (
                    ($pickup >= $start_date && $pickup <= $end_date) || // Pickup date is within range
                    ($return >= $start_date && $return <= $end_date) || // Return date is within range
                    ($pickup <= $start_date && $return >= $end_date)     // Booking spans the entire range
                ) {
                    if ($booking['status'] != 'cancelled') {
                        $filtered_bookings[] = $booking;
                    }
                }
            }
            
            return $filtered_bookings;
            
        } catch (Exception $e) {
            // Log the error
            error_log("Error in getBookingsForDateRange: " . $e->getMessage());
            // Return empty array on error
            return [];
        }
    }

    // Method to get total bookings
    public function getTotalBookings($filters = []) {
        // Create base query
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " b
                  LEFT JOIN cars c ON b.car_id = c.id
                  LEFT JOIN users u ON b.user_id = u.id
                  WHERE 1=1";
        
        // Add filters
        if (!empty($filters)) {
            if (isset($filters['status']) && !empty($filters['status'])) {
                $query .= " AND b.status = :status";
            }
            
            if (isset($filters['search']) && !empty($filters['search'])) {
                $query .= " AND (CONCAT(u.first_name, ' ', u.last_name) LIKE :search 
                          OR u.email LIKE :search 
                          OR CONCAT(c.brand, ' ', c.model) LIKE :search)";
            }
            
            if (isset($filters['date_from']) && !empty($filters['date_from'])) {
                $query .= " AND b.pickup_date >= :date_from";
            }
            
            if (isset($filters['date_to']) && !empty($filters['date_to'])) {
                $query .= " AND b.return_date <= :date_to";
            }
        }
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind filter parameters if they exist
        if (!empty($filters)) {
            if (isset($filters['status']) && !empty($filters['status'])) {
                $stmt->bindParam(':status', $filters['status']);
            }
            
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search = "%" . $filters['search'] . "%";
                $stmt->bindParam(':search', $search);
            }
            
            if (isset($filters['date_from']) && !empty($filters['date_from'])) {
                $stmt->bindParam(':date_from', $filters['date_from']);
            }
            
            if (isset($filters['date_to']) && !empty($filters['date_to'])) {
                $stmt->bindParam(':date_to', $filters['date_to']);
            }
        }
        
        // Execute query
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }

    // Method to get booking count by status
    public function getBookingCountByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Method to get recent bookings
    public function getRecentBookings($limit = 5) {
        // Create query
        $query = "SELECT b.*, c.brand, c.model, c.year, c.price_per_day as car_price, 
                  CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                  b.total_price as total_amount
                  FROM " . $this->table_name . " b
                  LEFT JOIN cars c ON b.car_id = c.id
                  LEFT JOIN users u ON b.user_id = u.id
                  ORDER BY b.created_at DESC
                  LIMIT :limit";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind limit as integer
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        
        // Execute query
        $stmt->execute();
        
        $bookings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Create car_name field for convenience
            $row['car_name'] = $row['brand'] . ' ' . $row['model'];
            $bookings[] = $row;
        }
        
        return $bookings;
    }

    // Method to get booking by ID
    public function getBookingById($id) {
        try {
            // Create query with detailed joins
            $query = "SELECT b.*, 
                    c.brand, c.model, c.year, c.price_per_day as car_price, c.image as car_image,
                    CONCAT(c.brand, ' ', c.model, ' (', c.year, ')') as car_name,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name,
                    u.email as user_email,
                    u.phone as user_phone,
                    pl.name as pickup_location,
                    rl.name as return_location
                    FROM " . $this->table_name . " b
                    LEFT JOIN cars c ON b.car_id = c.id
                    LEFT JOIN users u ON b.user_id = u.id
                    LEFT JOIN locations pl ON b.pickup_location_id = pl.id
                    LEFT JOIN locations rl ON b.return_location_id = rl.id
                    WHERE b.id = :id";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            // Bind data
            $stmt->bindParam(':id', $id);
            
            // Execute query
            $stmt->execute();
            
            // Fetch the booking data
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Log the query result for debugging
            error_log("Booking query result: " . print_r($row, true));
            
            // If no data found, try a simpler query
            if (!$row) {
                $simpleQuery = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
                $simpleStmt = $this->conn->prepare($simpleQuery);
                $simpleStmt->bindParam(':id', $id);
                $simpleStmt->execute();
                $row = $simpleStmt->fetch(PDO::FETCH_ASSOC);
                
                error_log("Simple booking query result: " . print_r($row, true));
                
                // If still no data, return default values
                if (!$row) {
                    return [
                        'id' => $id,
                        'user_id' => 0,
                        'car_id' => 0,
                        'pickup_date' => date('Y-m-d'),
                        'return_date' => date('Y-m-d', strtotime('+1 day')),
                        'pickup_location' => 'N/A',
                        'return_location' => 'N/A',
                        'pickup_location_id' => 1,
                        'return_location_id' => 1,
                        'total_price' => 0,
                        'status' => 'pending',
                        'payment_status' => 'pending',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'car_name' => 'N/A',
                        'car_price' => 0,
                        'car_image' => '',
                        'user_name' => 'N/A',
                        'user_email' => 'N/A',
                        'user_phone' => 'N/A'
                    ];
                }
                
                // If we have basic booking data but missing related data, fetch it separately
                
                // Get location names
                if (isset($row['pickup_location_id'])) {
                    $locationQuery = "SELECT name FROM locations WHERE id = :id";
                    $locationStmt = $this->conn->prepare($locationQuery);
                    
                    $locationStmt->bindParam(':id', $row['pickup_location_id']);
                    $locationStmt->execute();
                    $pickupLocation = $locationStmt->fetch(PDO::FETCH_ASSOC);
                    $row['pickup_location'] = $pickupLocation ? $pickupLocation['name'] : 'N/A';
                    
                    $locationStmt->bindParam(':id', $row['return_location_id']);
                    $locationStmt->execute();
                    $returnLocation = $locationStmt->fetch(PDO::FETCH_ASSOC);
                    $row['return_location'] = $returnLocation ? $returnLocation['name'] : 'N/A';
                }
                
                // Get car details
                if (isset($row['car_id'])) {
                    $carQuery = "SELECT brand, model, year, price_per_day, image FROM cars WHERE id = :id";
                    $carStmt = $this->conn->prepare($carQuery);
                    $carStmt->bindParam(':id', $row['car_id']);
                    $carStmt->execute();
                    $car = $carStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($car) {
                        $row['brand'] = $car['brand'];
                        $row['model'] = $car['model'];
                        $row['year'] = $car['year'];
                        $row['car_price'] = $car['price_per_day'];
                        $row['car_image'] = $car['image'];
                        $row['car_name'] = $car['brand'] . ' ' . $car['model'] . ' (' . $car['year'] . ')';
                    }
                }
                
                // Get user details
                if (isset($row['user_id'])) {
                    $userQuery = "SELECT first_name, last_name, email, phone FROM users WHERE id = :id";
                    $userStmt = $this->conn->prepare($userQuery);
                    $userStmt->bindParam(':id', $row['user_id']);
                    $userStmt->execute();
                    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user) {
                        $row['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                        $row['user_email'] = $user['email'];
                        $row['user_phone'] = $user['phone'];
                    }
                }
            }
            
            return $row;
            
        } catch (Exception $e) {
            // Log the error
            error_log("Error in getBookingById: " . $e->getMessage());
            
            // Return default values on error
            return [
                'id' => $id,
                'user_id' => 0,
                'car_id' => 0,
                'pickup_date' => date('Y-m-d'),
                'return_date' => date('Y-m-d', strtotime('+1 day')),
                'pickup_location' => 'Error retrieving location',
                'return_location' => 'Error retrieving location',
                'pickup_location_id' => 1,
                'return_location_id' => 1,
                'total_price' => 0,
                'status' => 'pending',
                'payment_status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'car_name' => 'Error retrieving car',
                'car_price' => 0,
                'car_image' => '',
                'user_name' => 'Error retrieving user',
                'user_email' => 'error@example.com',
                'user_phone' => 'N/A',
                'error' => $e->getMessage()
            ];
        }
    }

    // Method to get bookings by car ID
    public function getBookingsByCarId($car_id) {
        // Create query
        $query = "SELECT id, car_id, pickup_date, return_date, status 
              FROM " . $this->table_name . " 
              WHERE car_id = :car_id 
              AND status != 'cancelled'";
    
        // Prepare statement
        $stmt = $this->conn->prepare($query);
    
        // Bind data
        $stmt->bindParam(':car_id', $car_id);
    
        // Execute query
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to get bookings by user ID
    public function getBookingsByUser($user_id) {
        // Create query with joins to get car and location details
        $query = "SELECT b.*, 
              c.brand, c.model, c.year, c.image as car_image,
              pl.name as pickup_location_name,
              rl.name as return_location_name
              FROM " . $this->table_name . " b
              LEFT JOIN cars c ON b.car_id = c.id
              LEFT JOIN locations pl ON b.pickup_location_id = pl.id
              LEFT JOIN locations rl ON b.return_location_id = rl.id
              WHERE b.user_id = :user_id
              ORDER BY b.created_at DESC";
    
        // Prepare statement
        $stmt = $this->conn->prepare($query);
    
        // Bind data
        $stmt->bindParam(':user_id', $user_id);
    
        // Execute query
        $stmt->execute();
    
        return $stmt;
    }

    // Method to update booking status
    public function updateStatus($booking_id, $status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status, updated_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize data
        $status = htmlspecialchars(strip_tags($status));
        $booking_id = htmlspecialchars(strip_tags($booking_id));
        
        // Bind values
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $booking_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Method to update payment status
    public function updatePaymentStatus($booking_id, $payment_status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET payment_status = :payment_status, updated_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize data
        $payment_status = htmlspecialchars(strip_tags($payment_status));
        $booking_id = htmlspecialchars(strip_tags($booking_id));
        
        // Bind values
        $stmt->bindParam(':payment_status', $payment_status);
        $stmt->bindParam(':id', $booking_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>
