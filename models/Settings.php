<?php
class Settings {
    private $conn;
    private $table_name = "settings";
    
    // Settings properties
    public $id;
    public $site_title;
    public $site_description;
    public $contact_email;
    public $contact_phone;
    public $contact_address;
    public $currency_symbol;
    public $min_rental_period;
    public $max_rental_period;
    public $terms_conditions;
    public $privacy_policy;
    public $facebook_url;
    public $twitter_url;
    public $instagram_url;
    public $enable_reviews;
    public $maintenance_mode;
    public $updated_at;
    
    // Constructor
    public function __construct($db) {
        $this->conn = $db;
        $this->checkSettingsTable();
    }
    
    // Check if settings table exists, create if not
    private function checkSettingsTable() {
        // Use a different approach to check if table exists
        try {
            $query = "SELECT 1 FROM " . $this->table_name . " LIMIT 1";
            $stmt = $this->conn->query($query);
            
            // If we get here, table exists but might be empty
            $count = $stmt->rowCount();
            if ($count == 0) {
                // Table exists but is empty, insert default settings
                $this->insertDefaultSettings();
            }
        } catch (PDOException $e) {
            // Table doesn't exist, create it
            $this->createSettingsTable();
            $this->insertDefaultSettings();
        }
    }
    
    // Create settings table
    private function createSettingsTable() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            site_title VARCHAR(255) NOT NULL DEFAULT 'DREAMS RENT',
            site_description TEXT DEFAULT 'Your premium car rental service',
            contact_email VARCHAR(255) DEFAULT 'contact@dreamsrent.com',
            contact_phone VARCHAR(50) DEFAULT '+1 234 567 8900',
            contact_address TEXT DEFAULT '123 Main Street, City, Country',
            currency_symbol VARCHAR(10) DEFAULT '$',
            min_rental_period INT DEFAULT 1,
            max_rental_period INT DEFAULT 30,
            terms_conditions TEXT,
            privacy_policy TEXT,
            facebook_url VARCHAR(255),
            twitter_url VARCHAR(255),
            instagram_url VARCHAR(255),
            enable_reviews BOOLEAN DEFAULT 1,
            maintenance_mode BOOLEAN DEFAULT 0,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $this->conn->exec($query);
    }
    
    // Insert default settings
    private function insertDefaultSettings() {
        $query = "INSERT INTO " . $this->table_name . " 
                (site_title, site_description, terms_conditions, privacy_policy) VALUES
                ('DREAMS RENT', 'Your premium car rental service', 
                'Default Terms and Conditions. Please update this.', 
                'Default Privacy Policy. Please update this.')";
        
        $this->conn->exec($query);
    }
    
    // Get all settings
    public function getSettings() {
        $query = "SELECT * FROM " . $this->table_name . " LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->site_title = $row['site_title'];
            $this->site_description = $row['site_description'];
            $this->contact_email = $row['contact_email'];
            $this->contact_phone = $row['contact_phone'];
            $this->contact_address = $row['contact_address'];
            $this->currency_symbol = $row['currency_symbol'];
            $this->min_rental_period = $row['min_rental_period'];
            $this->max_rental_period = $row['max_rental_period'];
            $this->terms_conditions = $row['terms_conditions'];
            $this->privacy_policy = $row['privacy_policy'];
            $this->facebook_url = $row['facebook_url'];
            $this->twitter_url = $row['twitter_url'];
            $this->instagram_url = $row['instagram_url'];
            $this->enable_reviews = $row['enable_reviews'];
            $this->maintenance_mode = $row['maintenance_mode'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Update settings
    public function updateSettings() {
        $query = "UPDATE " . $this->table_name . " SET
                site_title = :site_title,
                site_description = :site_description,
                contact_email = :contact_email,
                contact_phone = :contact_phone,
                contact_address = :contact_address,
                currency_symbol = :currency_symbol,
                min_rental_period = :min_rental_period,
                max_rental_period = :max_rental_period,
                terms_conditions = :terms_conditions,
                privacy_policy = :privacy_policy,
                facebook_url = :facebook_url,
                twitter_url = :twitter_url,
                instagram_url = :instagram_url,
                enable_reviews = :enable_reviews,
                maintenance_mode = :maintenance_mode
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize and bind values
        $this->site_title = htmlspecialchars(strip_tags($this->site_title));
        $this->site_description = htmlspecialchars(strip_tags($this->site_description));
        $this->contact_email = htmlspecialchars(strip_tags($this->contact_email));
        $this->contact_phone = htmlspecialchars(strip_tags($this->contact_phone));
        $this->contact_address = htmlspecialchars(strip_tags($this->contact_address));
        $this->currency_symbol = htmlspecialchars(strip_tags($this->currency_symbol));
        $this->min_rental_period = (int)$this->min_rental_period;
        $this->max_rental_period = (int)$this->max_rental_period;
        $this->terms_conditions = $this->terms_conditions; // Allow HTML in terms
        $this->privacy_policy = $this->privacy_policy; // Allow HTML in privacy policy
        $this->facebook_url = htmlspecialchars(strip_tags($this->facebook_url));
        $this->twitter_url = htmlspecialchars(strip_tags($this->twitter_url));
        $this->instagram_url = htmlspecialchars(strip_tags($this->instagram_url));
        $this->enable_reviews = (int)$this->enable_reviews;
        $this->maintenance_mode = (int)$this->maintenance_mode;
        $this->id = (int)$this->id;
        
        $stmt->bindParam(':site_title', $this->site_title);
        $stmt->bindParam(':site_description', $this->site_description);
        $stmt->bindParam(':contact_email', $this->contact_email);
        $stmt->bindParam(':contact_phone', $this->contact_phone);
        $stmt->bindParam(':contact_address', $this->contact_address);
        $stmt->bindParam(':currency_symbol', $this->currency_symbol);
        $stmt->bindParam(':min_rental_period', $this->min_rental_period);
        $stmt->bindParam(':max_rental_period', $this->max_rental_period);
        $stmt->bindParam(':terms_conditions', $this->terms_conditions);
        $stmt->bindParam(':privacy_policy', $this->privacy_policy);
        $stmt->bindParam(':facebook_url', $this->facebook_url);
        $stmt->bindParam(':twitter_url', $this->twitter_url);
        $stmt->bindParam(':instagram_url', $this->instagram_url);
        $stmt->bindParam(':enable_reviews', $this->enable_reviews);
        $stmt->bindParam(':maintenance_mode', $this->maintenance_mode);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>
