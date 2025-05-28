<?php
class Location {
    // Database connection and table name
    private $conn;
    private $table_name = "locations";

    // Object properties
    public $id;
    public $name;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $country;
    public $created_at;
    public $updated_at;

    // Constructor
    public function __construct($db = null) {
        if ($db !== null) {
            $this->conn = $db;
        }
    }

    // Set database connection
    public function setConnection($db) {
        $this->conn = $db;
    }

    // Get all locations
    public function getAllLocations() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get location by ID
    public function getLocationById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create location
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET name = :name, 
                    address = :address, 
                    city = :city, 
                    state = :state, 
                    zip_code = :zip_code, 
                    country = :country, 
                    created_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':state', $this->state);
        $stmt->bindParam(':zip_code', $this->zip_code);
        $stmt->bindParam(':country', $this->country);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    // Update location
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET name = :name, 
                    address = :address, 
                    city = :city, 
                    state = :state, 
                    zip_code = :zip_code, 
                    country = :country, 
                    updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':state', $this->state);
        $stmt->bindParam(':zip_code', $this->zip_code);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    // Delete location
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>
