<?php
class Car {

    // database connection and table name
    private $conn;
    private $table_name = "cars";

    // object properties
    public $id;
    public $category_id;
    public $brand;
    public $model;
    public $year;
    public $color; // Keep for backward compatibility
    public $transmission;
    public $fuel_type;
    public $seats; // Keep for backward compatibility
    public $price_per_day;
    public $availability;
    public $image;
    public $images;
    public $description; // Keep for backward compatibility
    public $created_at;
    public $updated_at;

    // constructor with $db as database connection
    public function __construct($db = null){
        if ($db !== null) {
            $this->conn = $db;
        }
    }

    // Set database connection
    public function setConnection($db) {
        $this->conn = $db;
    }

    // read all cars
    function read(){
        // select all query
        $query = "SELECT
                    id, category_id, brand, model, year, color, transmission, fuel_type, seats, price_per_day, availability, image, images, description, created_at, updated_at
                FROM
                    " . $this->table_name . "
                ORDER BY
                    created_at DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    // Super simple create method
    function create() {
        try {
            // Basic insert query with minimal fields
            $query = "INSERT INTO " . $this->table_name . " 
                    (brand, model, year, transmission, fuel_type, price_per_day, availability, image) 
                    VALUES 
                    (?, ?, ?, ?, ?, ?, ?, ?)";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            // Execute with parameters
            $result = $stmt->execute([
                $this->brand,
                $this->model,
                $this->year,
                $this->transmission,
                $this->fuel_type,
                $this->price_per_day,
                $this->availability,
                $this->image
            ]);
            
            if ($result) {
                return $this->conn->lastInsertId();
            }
            
            return false;
        } catch (Exception $e) {
            // Log the error
            error_log("Car create error: " . $e->getMessage());
            return false;
        }
    }

    // used when filling up the update car form
    function readOne(){
        // query to read single record
        $query = "SELECT
                    category_id, brand, model, year, color, transmission, fuel_type, seats, price_per_day, availability, image, images, description, created_at, updated_at
                FROM
                    " . $this->table_name . "
                WHERE
                    id = ?
                LIMIT
                    0,1";

        // prepare query statement
        $stmt = $this->conn->prepare( $query );

        // bind id of car to be updated
        $stmt->bindParam(1, $this->id);

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->category_id = $row['category_id'] ?? null;
        $this->brand = $row['brand'] ?? null;
        $this->model = $row['model'] ?? null;
        $this->year = $row['year'] ?? null;
        $this->color = $row['color'] ?? null;
        $this->transmission = $row['transmission'] ?? null;
        $this->fuel_type = $row['fuel_type'] ?? null;
        $this->seats = $row['seats'] ?? null;
        $this->price_per_day = $row['price_per_day'] ?? null;
        $this->availability = $row['availability'] ?? null;
        $this->image = $row['image'] ?? null;
        $this->images = $row['images'] ?? null;
        $this->description = $row['description'] ?? null;
        $this->created_at = $row['created_at'] ?? null;
        $this->updated_at = $row['updated_at'] ?? null;
    }

    // update the car
    function update() {
        try {
            // Query to update car
            $query = "UPDATE " . $this->table_name . " 
                    SET brand = :brand, 
                        model = :model, 
                        year = :year, 
                        transmission = :transmission, 
                        fuel_type = :fuel_type, 
                        price_per_day = :price_per_day, 
                        availability = :availability";
            
            // Add image to query if it exists
            if (!empty($this->image)) {
                $query .= ", image = :image";
            }
            
            // Add updated_at timestamp
            $query .= ", updated_at = NOW() 
                    WHERE id = :id";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':brand', $this->brand);
            $stmt->bindParam(':model', $this->model);
            $stmt->bindParam(':year', $this->year);
            $stmt->bindParam(':transmission', $this->transmission);
            $stmt->bindParam(':fuel_type', $this->fuel_type);
            $stmt->bindParam(':price_per_day', $this->price_per_day);
            $stmt->bindParam(':availability', $this->availability);
            $stmt->bindParam(':id', $this->id);
            
            // Bind image if it exists
            if (!empty($this->image)) {
                $stmt->bindParam(':image', $this->image);
            }
            
            // Execute query
            if ($stmt->execute()) {
                return true;
            }
            
            // Log the error if execution fails
            $errorInfo = $stmt->errorInfo();
            error_log("Car update error: " . print_r($errorInfo, true));
            return false;
        } catch (PDOException $e) {
            // Log the error
            error_log("Car update error: " . $e->getMessage());
            return false;
        }
    }

    // delete the car
    function deleteCar($id){
        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $id=htmlspecialchars(strip_tags($id));

        // bind id of record to delete
        $stmt->bindParam(':id', $id);

        // execute query
        if($stmt->execute()){
            return true;
        }

        // Log the error if execution fails
        $errorInfo = $stmt->errorInfo();
        error_log("Car delete error: " . print_r($errorInfo, true));
        return false;
    }

    // search cars
    function search($keywords){
        // select all query
        $query = "SELECT
                    id, category_id, brand, model, year, color, transmission, fuel_type, seats, price_per_day, availability, image, images, description, created_at, updated_at
                FROM
                    " . $this->table_name . "
                WHERE
                    brand LIKE ? OR model LIKE ? OR year LIKE ?
                ORDER BY
                    created_at DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $keywords=htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        // bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    // read cars with pagination
    public function readPaging($from_record_num, $records_per_page){
        // select query
        $query = "SELECT
                    id, category_id, brand, model, year, color, transmission, fuel_type, seats, price_per_day, availability, image, images, description, created_at, updated_at
                FROM
                    " . $this->table_name . "
                ORDER BY created_at DESC
                LIMIT ?, ?";

        // prepare query statement
        $stmt = $this->conn->prepare( $query );

        // bind variable values
        $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

        // execute query
        $stmt->execute();

        // return values from database
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // used for paging cars
    public function count(){
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . "";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total_rows'];
    }

    // Get all cars with pagination and filtering
    public function getAllCars($start = 0, $limit = 10, $search = '', $category_id = '', $status = '') {
        $query = "SELECT c.*, cc.name as category_name 
                FROM " . $this->table_name . " c
                LEFT JOIN car_categories cc ON c.category_id = cc.id
                WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (c.brand LIKE :search OR c.model LIKE :search OR c.year LIKE :search)";
        }
        
        if (!empty($category_id)) {
            $query .= " AND c.category_id = :category_id";
        }
        
        if (!empty($status)) {
            $query .= " AND c.availability = :status";
        }
        
        $query .= " ORDER BY c.created_at DESC LIMIT :start, :limit";
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) {
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam);
        }
        
        if (!empty($category_id)) {
            $stmt->bindParam(':category_id', $category_id);
        }
        
        if (!empty($status)) {
            $status_value = ($status == 'available') ? 1 : 0;
            $stmt->bindParam(':status', $status_value);
        }
        
        $stmt->bindParam(':start', $start, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Count total cars for pagination
    public function countAllCars($search = '', $category_id = '', $status = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " c WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (c.brand LIKE :search OR c.model LIKE :search OR c.year LIKE :search)";
        }
        
        if (!empty($category_id)) {
            $query .= " AND c.category_id = :category_id";
        }
        
        if (!empty($status)) {
            $query .= " AND c.availability = :status";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) {
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam);
        }
        
        if (!empty($category_id)) {
            $stmt->bindParam(':category_id', $category_id);
        }
        
        if (!empty($status)) {
            $status_value = ($status == 'available') ? 1 : 0;
            $stmt->bindParam(':status', $status_value);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }

    // Get car by ID - simplified
    public function getCarById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all categories
    public function getAllCategories() {
        $query = "SELECT DISTINCT id, name FROM car_categories ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get total number of cars
    public function getTotalCars() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Get cars by status
    public function getCarsByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE availability = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Get popular cars based on booking count
    public function getPopularCars($limit = 5) {
        $query = "SELECT c.*, COUNT(b.id) as bookings_count 
                FROM " . $this->table_name . " c
                LEFT JOIN bookings b ON c.id = b.car_id
                GROUP BY c.id
                ORDER BY bookings_count DESC
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
