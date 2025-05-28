<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Include header
include_once 'includes/header.php';
?>

<div class="admin-page-title">
    <h1>Car Management</h1>
    <p class="text-muted">Manage your car inventory</p>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="input-group">
            <input type="text" class="form-control" id="tableSearch" placeholder="Search cars..." onkeyup="searchTable()">
            <button class="btn btn-primary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div class="col-md-4 text-end">
        <a href="car-add.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add New Car
        </a>
    </div>
</div>

<!-- Display success/error messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php 
        echo $_SESSION['success']; 
        unset($_SESSION['success']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php 
        echo $_SESSION['error']; 
        unset($_SESSION['error']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="carsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Brand/Model</th>
                        <th>Year</th>
                        <th>Price/Day</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch cars from database
                    try {
                        $query = "SELECT * FROM cars ORDER BY id DESC";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        
                        if ($stmt->rowCount() > 0) {
                            while ($car = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $image = !empty($car['image']) ? $car['image'] : 'assets/img/cars/default-car.jpg';
                                $status = $car['availability'] == 1 ? 'Available' : 'Unavailable';
                                $statusClass = $car['availability'] == 1 ? 'success' : 'danger';
                    ?>
                    <tr>
                        <td><?php echo $car['id']; ?></td>
                        <td>
                            <img src="../<?php echo $image; ?>" alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>" 
                                 class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;">
                        </td>
                        <td><?php echo $car['brand'] . ' ' . $car['model']; ?></td>
                        <td><?php echo $car['year']; ?></td>
                        <td>$<?php echo number_format($car['price_per_day'] ?? 0, 2); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $statusClass; ?>">
                                <?php echo $status; ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="car-edit.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- Direct delete link instead of modal -->
                                <a href="car-delete.php?id=<?php echo $car['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this car?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No cars found</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='7' class='text-danger'>Error fetching cars: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>

<script>
function searchTable() {
    // Get input value and convert to lowercase
    var input = document.getElementById("tableSearch");
    var filter = input.value.toLowerCase();
    
    // Get table and rows
    var table = document.getElementById("carsTable");
    var rows = table.getElementsByTagName("tr");
    
    // Loop through all table rows, starting from index 1 to skip the header
    for (var i = 1; i < rows.length; i++) {
        var showRow = false;
        
        // Get all cells in the row
        var cells = rows[i].getElementsByTagName("td");
        
        // Loop through all cells in the row
        for (var j = 0; j < cells.length; j++) {
            // Skip the actions column
            if (j === cells.length - 1) continue;
            
            var cell = cells[j];
            
            // Check if cell contains the search text
            if (cell) {
                var textValue = cell.textContent || cell.innerText;
                
                if (textValue.toLowerCase().indexOf(filter) > -1) {
                    showRow = true;
                    break;
                }
            }
        }
        
        // Show or hide the row
        rows[i].style.display = showRow ? "" : "none";
    }
}
</script>
