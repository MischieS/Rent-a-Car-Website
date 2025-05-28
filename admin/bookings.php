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
    <h1>Bookings</h1>
    <p class="text-muted">Manage all car bookings</p>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="d-flex gap-2">
            <div class="input-group">
                <input type="text" class="form-control" id="tableSearch" placeholder="Search bookings..." onkeyup="searchTable()">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <select class="form-select" id="statusFilter" style="max-width: 150px;" onchange="filterByStatus()">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>
    <div class="col-md-4 text-end">
        <a href="booking-calendar.php" class="btn btn-outline-primary">
            <i class="fas fa-calendar-alt me-1"></i> Calendar View
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
            <table class="table table-hover" id="bookingsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Car</th>
                        <th>Pickup Date</th>
                        <th>Return Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch bookings from database
                    try {
                        $query = "SELECT b.*, u.first_name, u.last_name, u.email, 
                                c.brand, c.model, c.price_per_day 
                                FROM bookings b 
                                LEFT JOIN users u ON b.user_id = u.id 
                                LEFT JOIN cars c ON b.car_id = c.id 
                                ORDER BY b.created_at DESC";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        
                        if ($stmt->rowCount() > 0) {
                            while ($booking = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $pickup_date = new DateTime($booking['pickup_date']);
                                $return_date = new DateTime($booking['return_date']);
                                $days = $pickup_date->diff($return_date)->days;
                                $total = $days * ($booking['price_per_day'] ?? 0);
                                
                                $status = $booking['status'];
                                $statusClass = 'secondary';
                                if ($status == 'confirmed') $statusClass = 'success';
                                else if ($status == 'pending') $statusClass = 'warning';
                                else if ($status == 'cancelled') $statusClass = 'danger';
                                else if ($status == 'completed') $statusClass = 'info';
                    ?>
                    <tr data-status="<?php echo $status; ?>">
                        <td><?php echo $booking['id']; ?></td>
                        <td>
                            <div><?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?></div>
                            <small class="text-muted"><?php echo $booking['email']; ?></small>
                        </td>
                        <td><?php echo $booking['brand'] . ' ' . $booking['model']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($booking['return_date'])); ?></td>
                        <td>$<?php echo number_format($total, 2); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $statusClass; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="booking-view.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="booking-edit.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>No bookings found</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='8' class='text-danger'>Error fetching bookings: " . $e->getMessage() . "</td></tr>";
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
    
    // Get status filter value
    var statusFilter = document.getElementById("statusFilter").value.toLowerCase();
    
    // Get table and rows
    var table = document.getElementById("bookingsTable");
    var rows = table.getElementsByTagName("tr");
    
    // Loop through all table rows, starting from index 1 to skip the header
    for (var i = 1; i < rows.length; i++) {
        var showRow = false;
        var rowStatus = rows[i].getAttribute("data-status");
        
        // Check if row matches status filter
        var statusMatch = !statusFilter || (rowStatus && rowStatus.toLowerCase() === statusFilter);
        
        if (!statusMatch) {
            rows[i].style.display = "none";
            continue;
        }
        
        // Get all cells in the row
        var cells = rows[i].getElementsByTagName("td");
        
        // If search input is empty, show the row (if it matches status filter)
        if (filter === "") {
            showRow = true;
        } else {
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
        }
        
        // Show or hide the row
        rows[i].style.display = showRow ? "" : "none";
    }
}

function filterByStatus() {
    searchTable(); // Reuse the search function to apply both filters
}
</script>
