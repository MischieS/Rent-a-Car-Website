<?php
class User {
    // database connection and table name
    private $conn;
    private $table_name = "users";

    // object properties
    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $password;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $license_number;
    public $license_expiry;
    public $role;
    public $status;
    public $profile_image;
    public $remember_token;
    public $created_at;
    public $updated_at;

    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Login user
    public function login() {
        try {
            // Check if email exists
            $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            
            // If email exists, verify password
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Check if account is active
                if(isset($row['status']) && $row['status'] !== 'active') {
                    return false;
                }
                
                // Verify password
                if(password_verify($this->password, $row['password'])) {
                    // Set user properties
                    $this->id = $row['id'];
                    $this->first_name = $row['first_name'];
                    $this->last_name = $row['last_name'];
                    $this->email = $row['email'];
                    $this->phone = $row['phone'];
                    $this->address = $row['address'];
                    $this->city = $row['city'];
                    $this->state = $row['state'];
                    $this->zip_code = $row['zip_code'];
                    $this->license_number = $row['license_number'];
                    $this->license_expiry = $row['license_expiry'];
                    $this->role = $row['role'];
                    $this->status = $row['status'];
                    $this->profile_image = $row['profile_image'];
                    $this->created_at = $row['created_at'];
                    $this->updated_at = $row['updated_at'];
                    
                    return true;
                }
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    // Store remember token
    public function storeRememberToken($token) {
        try {
            $query = "UPDATE " . $this->table_name . " SET remember_token = :token WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':id', $this->id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Store remember token error: " . $e->getMessage());
            return false;
        }
    }

    // Login with remember token
    public function loginWithToken($token) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE remember_token = :token";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Check if account is active
                if(isset($row['status']) && $row['status'] !== 'active') {
                    return false;
                }
                
                // Set user properties
                $this->id = $row['id'];
                $this->first_name = $row['first_name'];
                $this->last_name = $row['last_name'];
                $this->email = $row['email'];
                $this->phone = $row['phone'];
                $this->address = $row['address'];
                $this->city = $row['city'];
                $this->state = $row['state'];
                $this->zip_code = $row['zip_code'];
                $this->license_number = $row['license_number'];
                $this->license_expiry = $row['license_expiry'];
                $this->role = $row['role'];
                $this->status = $row['status'];
                $this->profile_image = $row['profile_image'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Login with token error: " . $e->getMessage());
            return false;
        }
    }

    // Clear remember token
    public function clearRememberToken() {
        try {
            $query = "UPDATE " . $this->table_name . " SET remember_token = NULL WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Clear remember token error: " . $e->getMessage());
            return false;
        }
    }

    // Get user by ID
    public function getUserById($id) {
        // Query to read single record
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Bind ID
        $stmt->bindParam(1, $id);
        
        // Execute query
        $stmt->execute();
        
        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set values to object properties
        if ($row) {
            $this->id = $row['id'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->email = $row['email'];
            $this->password = $row['password'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->city = $row['city'];
            $this->state = $row['state'];
            $this->zip_code = $row['zip_code'];
            $this->license_number = $row['license_number'];
            $this->license_expiry = $row['license_expiry'];
            $this->role = $row['role'];
            $this->profile_image = $row['profile_image'];
            $this->created_at = $row['created_at'];
            return true;
        }
        
        return false;
    }

    // Get user by email
    public function getUserByEmail($email) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get user by email error: " . $e->getMessage());
            return false;
        }
    }

    // Get total number of users
    public function getTotalUsers() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (Exception $e) {
            error_log("Get total users error: " . $e->getMessage());
            return 0;
        }
    }

    // Get all users with pagination and search
    public function getAllUsers($start = 0, $limit = 10, $search = '', $role = '') {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
            
            if (!empty($search)) {
                $query .= " AND (first_name LIKE :search 
                          OR last_name LIKE :search 
                          OR email LIKE :search 
                          OR phone LIKE :search)";
            }
            
            if (!empty($role)) {
                $query .= " AND role = :role";
            }
            
            $query .= " ORDER BY created_at DESC
                       LIMIT :start, :limit";
            
            $stmt = $this->conn->prepare($query);
            
            if (!empty($search)) {
                $searchParam = "%{$search}%";
                $stmt->bindParam(':search', $searchParam);
            }
            
            if (!empty($role)) {
                $stmt->bindParam(':role', $role);
            }
            
            $stmt->bindParam(':start', $start, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get all users error: " . $e->getMessage());
            return [];
        }
    }

    // Count total users for pagination
    public function countAllUsers($search = '', $role = '') {
        try {
            $query = "SELECT COUNT(*) as total
                      FROM " . $this->table_name . "
                      WHERE 1=1";
            
            if (!empty($search)) {
                $query .= " AND (first_name LIKE :search 
                          OR last_name LIKE :search 
                          OR email LIKE :search 
                          OR phone LIKE :search)";
            }
            
            if (!empty($role)) {
                $query .= " AND role = :role";
            }
            
            $stmt = $this->conn->prepare($query);
            
            if (!empty($search)) {
                $searchParam = "%{$search}%";
                $stmt->bindParam(':search', $searchParam);
            }
            
            if (!empty($role)) {
                $stmt->bindParam(':role', $role);
            }
            
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $row['total'];
        } catch (Exception $e) {
            error_log("Count all users error: " . $e->getMessage());
            return 0;
        }
    }

    // Public method to check if email exists
    public function checkEmailExists($email = null) {
        try {
            if($email !== null) {
                $this->email = $email;
            }
            return $this->emailExists();
        } catch (Exception $e) {
            error_log("Check email exists error: " . $e->getMessage());
            return false;
        }
    }

    // Create new user
    public function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    first_name=:first_name,
                    last_name=:last_name,
                    email=:email,
                    password=:password,
                    phone=:phone,
                    role=:role,
                    created_at=:created_at";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->role = htmlspecialchars(strip_tags($this->role));
        
        // Hash the password
        $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
        
        // Bind values
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":role", $this->role);
        
        // Get current timestamp
        $this->created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(":created_at", $this->created_at);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Check if email exists
    public function emailExists() {
        // Query to check if email exists
        $query = "SELECT id, first_name, last_name, password, role
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);
        
        // Bind email to parameter
        $stmt->bindParam(1, $this->email);
        
        // Execute query
        $stmt->execute();
        
        // Get number of rows
        $num = $stmt->rowCount();
        
        // If email exists, assign values to object properties for easy access
        if ($num > 0) {
            // Get record details
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Assign values to object properties
            $this->id = $row['id'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->password = $row['password'];
            $this->role = $row['role'];
            
            return true;
        }
        
        return false;
    }
    
    // Update user
    public function updateUser() {
        try {
            $query = "UPDATE " . $this->table_name . "
                      SET first_name = :first_name,
                          last_name = :last_name,
                          phone = :phone,
                          address = :address,
                          city = :city,
                          state = :state,
                          zip_code = :zip_code,
                          license_number = :license_number,
                          license_expiry = :license_expiry,
                          role = :role,
                          status = :status";
            
            // Only update profile image if provided
            if(!empty($this->profile_image)) {
                $query .= ", profile_image = :profile_image";
            }
            
            // Only update password if a new one is provided
            if(!empty($this->password)) {
                $query .= ", password = :password";
            }
            
            // Only update email if it's changed and doesn't already exist
            if(!empty($this->email) && $this->email != $this->getCurrentEmail()) {
                if($this->emailExists()) {
                    return false;
                }
                $query .= ", email = :email";
            }
            
            $query .= " WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Sanitize and bind parameters
            $this->id = htmlspecialchars(strip_tags($this->id));
            $this->first_name = htmlspecialchars(strip_tags($this->first_name));
            $this->last_name = htmlspecialchars(strip_tags($this->last_name));
            $this->phone = htmlspecialchars(strip_tags($this->phone));
            $this->address = htmlspecialchars(strip_tags($this->address));
            $this->city = htmlspecialchars(strip_tags($this->city));
            $this->state = htmlspecialchars(strip_tags($this->state));
            $this->zip_code = htmlspecialchars(strip_tags($this->zip_code));
            $this->license_number = htmlspecialchars(strip_tags($this->license_number));
            $this->license_expiry = htmlspecialchars(strip_tags($this->license_expiry));
            $this->role = htmlspecialchars(strip_tags($this->role));
            $this->status = htmlspecialchars(strip_tags($this->status));
            
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':first_name', $this->first_name);
            $stmt->bindParam(':last_name', $this->last_name);
            $stmt->bindParam(':phone', $this->phone);
            $stmt->bindParam(':address', $this->address);
            $stmt->bindParam(':city', $this->city);
            $stmt->bindParam(':state', $this->state);
            $stmt->bindParam(':zip_code', $this->zip_code);
            $stmt->bindParam(':license_number', $this->license_number);
            $stmt->bindParam(':license_expiry', $this->license_expiry);
            $stmt->bindParam(':role', $this->role);
            $stmt->bindParam(':status', $this->status);
            
            if(!empty($this->profile_image)) {
                $this->profile_image = htmlspecialchars(strip_tags($this->profile_image));
                $stmt->bindParam(':profile_image', $this->profile_image);
            }
            
            if(!empty($this->password)) {
                $this->password = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindParam(':password', $this->password);
            }
            
            if(!empty($this->email) && $this->email != $this->getCurrentEmail()) {
                $this->email = htmlspecialchars(strip_tags($this->email));
                $stmt->bindParam(':email', $this->email);
            }
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Update user error: " . $e->getMessage());
            return false;
        }
    }

    // Delete user
    public function delete() {
        // Query to delete record
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Bind id
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Get current email
    private function getCurrentEmail() {
        try {
            $query = "SELECT email FROM " . $this->table_name . " WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $row['email'];
        } catch (Exception $e) {
            error_log("Get current email error: " . $e->getMessage());
            return '';
        }
    }

    // Get user by ID and return as object
    public function getUser() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }

    // Register new user
    public function register() {
        try {
            // Check if email already exists
            if($this->emailExists()) {
                error_log("Registration failed: Email already exists - " . $this->email);
                return false;
            }
            
            // Set default values for optional fields
            if(empty($this->role)) {
                $this->role = 'user';
            }
            
            if(empty($this->status)) {
                $this->status = 'active';
            }
            
            // Check if the users table has the necessary columns
            $columns = $this->getTableColumns();
            
            // Build the query dynamically based on available columns
            $fields = [];
            $placeholders = [];
            $params = [];
            
            // Always include these basic fields
            $basicFields = [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => password_hash($this->password, PASSWORD_DEFAULT),
                'role' => $this->role,
                'status' => $this->status
            ];
            
            foreach ($basicFields as $field => $value) {
                if (in_array($field, $columns)) {
                    $fields[] = $field;
                    $placeholders[] = ":$field";
                    $params[$field] = $value;
                }
            }
            
            // Create query
            $query = "INSERT INTO " . $this->table_name . " (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            
            // Debug information
            error_log("Register query: " . $query);
            error_log("Register params: " . json_encode($params));
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            // Execute query
            if($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            
            error_log("Registration failed: Execute returned false");
            return false;
        } catch (Exception $e) {
            error_log("Register error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get table columns
    private function getTableColumns() {
        try {
            $query = "DESCRIBE " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $columns = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $columns[] = $row['Field'];
            }
            
            return $columns;
        } catch (Exception $e) {
            error_log("Get table columns error: " . $e->getMessage());
            return ['first_name', 'last_name', 'email', 'phone', 'password', 'role', 'status'];
        }
    }

    // Update profile
    public function updateProfile() {
        // Query to update record
        $query = "UPDATE " . $this->table_name . "
                SET
                    first_name = :first_name,
                    last_name = :last_name,
                    phone = :phone,
                    address = :address,
                    city = :city,
                    state = :state,
                    zip_code = :zip_code,
                    license_number = :license_number,
                    license_expiry = :license_expiry";
        
        // Add profile image to query if it exists
        if (!empty($this->profile_image)) {
            $query .= ", profile_image = :profile_image";
        }
        
        $query .= " WHERE id = :id";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->state = htmlspecialchars(strip_tags($this->state));
        $this->zip_code = htmlspecialchars(strip_tags($this->zip_code));
        $this->license_number = htmlspecialchars(strip_tags($this->license_number));
        
        // Bind values
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":state", $this->state);
        $stmt->bindParam(":zip_code", $this->zip_code);
        $stmt->bindParam(":license_number", $this->license_number);
        $stmt->bindParam(":license_expiry", $this->license_expiry);
        $stmt->bindParam(":id", $this->id);
        
        // Bind profile image if it exists
        if (!empty($this->profile_image)) {
            $stmt->bindParam(":profile_image", $this->profile_image);
        }
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Change password - SIMPLIFIED VERSION
    public function changePassword($new_password) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Query to update password
        $query = "UPDATE " . $this->table_name . " SET password = ? WHERE id = ?";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(1, $hashed_password);
        $stmt->bindParam(2, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get all users
    public function getAll($search = "", $role = "") {
        // Base query
        $query = "SELECT * FROM " . $this->table_name;
        
        // Add search condition if provided
        $conditions = array();
        $params = array();
        
        if (!empty($search)) {
            $conditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $search_term = "%{$search}%";
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        if (!empty($role)) {
            $conditions[] = "role = ?";
            $params[] = $role;
        }
        
        // Combine conditions if any
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        // Order by created date
        $query .= " ORDER BY created_at DESC";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters if any
        if (!empty($params)) {
            for ($i = 0; $i < count($params); $i++) {
                $stmt->bindParam($i + 1, $params[$i]);
            }
        }
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
}
?>
