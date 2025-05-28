<?php
class Database {
    private $host = "localhost";
    private $db_name = "rent_a_car";
    private $username = "root";
    private $password = "";
    private $conn;

    // Get database connection
    public function getConnection() {
        $this->conn = null;

        try {
            // First connect without specifying a database
            $this->conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check if database exists
            $stmt = $this->conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $this->db_name . "'");
            $dbExists = $stmt->rowCount() > 0;
            
            if (!$dbExists) {
                // Database doesn't exist, run setup
                $this->runSetup();
            } else {
                // Connect to the database
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                
                // Check if tables exist, if not run setup
                $stmt = $this->conn->query("SHOW TABLES LIKE 'users'");
                if ($stmt->rowCount() == 0) {
                    $this->runSetup();
                }
            }
            
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }

        return $this->conn;
    }
    
    // Run setup script
    private function runSetup() {
        try {
            // Create the database if it doesn't exist
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name);
            
            // Connect to the database
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            // Include and run setup script
            require_once __DIR__ . '/../setup-with-examples.php';
            setupDatabase($this->conn);
            
            // Redirect to index page after setup
            if (!headers_sent()) {
                header("Location: index.php");
                exit();
            }
        } catch(PDOException $e) {
            echo "Setup Error: " . $e->getMessage();
        }
    }
}
?>
