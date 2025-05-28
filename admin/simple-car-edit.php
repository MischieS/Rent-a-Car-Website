<?php
// Simple version of car edit to test basic functionality
session_start();

// Simple database connection (adjust these values for your setup)
$host = 'localhost';
$dbname = 'rent_a_car'; // or whatever your database name is
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
} catch(PDOException $e) {
    echo "<div style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</div>";
    echo "<p>Please check your database settings in this file.</p>";
    exit();
}

// Get car ID
$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($car_id <= 0) {
    echo "<div style='color: red;'>❌ Invalid car ID</div>";
    exit();
}

// Try to get car data
try {
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->execute([$car_id]);
    $car = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$car) {
        echo "<div style='color: red;'>❌ Car not found with ID: $car_id</div>";
        
        // Show available cars
        $stmt = $pdo->query("SELECT id, brand, model FROM cars LIMIT 10");
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($cars) {
            echo "<h3>Available cars:</h3><ul>";
            foreach ($cars as $c) {
                echo "<li><a href='?id={$c['id']}'>{$c['brand']} {$c['model']} (ID: {$c['id']})</a></li>";
            }
            echo "</ul>";
        }
        exit();
    }
    
    echo "<div style='color: green;'>✅ Car found: {$car['brand']} {$car['model']}</div>";
    
} catch(PDOException $e) {
    echo "<div style='color: red;'>❌ Database error: " . $e->getMessage() . "</div>";
    
    // Try to show table structure
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<h3>Available tables:</h3><ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    } catch(PDOException $e2) {
        echo "<p>Could not show tables: " . $e2->getMessage() . "</p>";
    }
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $year = $_POST['year'];
        $price_per_day = $_POST['price_per_day'];
        
        $stmt = $pdo->prepare("UPDATE cars SET brand = ?, model = ?, year = ?, price_per_day = ? WHERE id = ?");
        $result = $stmt->execute([$brand, $model, $year, $price_per_day, $car_id]);
        
        if ($result) {
            echo "<div style='color: green;'>✅ Car updated successfully!</div>";
            // Refresh car data
            $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
            $stmt->execute([$car_id]);
            $car = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "<div style='color: red;'>❌ Failed to update car</div>";
        }
    } catch(PDOException $e) {
        echo "<div style='color: red;'>❌ Update error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Car Edit</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 300px; padding: 5px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Simple Car Edit - ID: <?= $car_id ?></h1>
    
    <form method="POST">
        <div class="form-group">
            <label>Brand:</label>
            <input type="text" name="brand" value="<?= htmlspecialchars($car['brand'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Model:</label>
            <input type="text" name="model" value="<?= htmlspecialchars($car['model'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Year:</label>
            <input type="number" name="year" value="<?= htmlspecialchars($car['year'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Price Per Day:</label>
            <input type="number" name="price_per_day" value="<?= htmlspecialchars($car['price_per_day'] ?? '') ?>" step="0.01" required>
        </div>
        
        <div class="form-group">
            <button type="submit">Update Car</button>
        </div>
    </form>
    
    <h2>Current Car Data:</h2>
    <pre><?= print_r($car, true) ?></pre>
    
    <p><a href="../check-structure.php">← Back to Structure Check</a></p>
</body>
</html>
