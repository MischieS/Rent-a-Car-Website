<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Include models
require_once '../models/User.php';

// Initialize user object
$user = new User($db);

// Set page title
$page_title = "Users Management";

// Include header
include_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Users Management</h1>
            <p class="text-muted">Manage system users</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus me-2"></i> Add New User
            </button>
        </div>
    </div>

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

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex gap-2">
                <div class="input-group">
                    <input type="text" class="form-control" id="tableSearch" placeholder="Search users..." onkeyup="searchTable()">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <select class="form-select" id="roleFilter" style="max-width: 150px;" onchange="filterByRole()">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all users from database
                        try {
                            $query = "SELECT * FROM users ORDER BY id DESC";
                            $stmt = $db->prepare($query);
                            $stmt->execute();
                            
                            if ($stmt->rowCount() > 0) {
                                while ($user_data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $status = $user_data['status'] ?? 'active';
                                    $statusClass = ($status == 'active') ? 'success' : 'danger';
                        ?>
                        <tr data-role="<?php echo $user_data['role']; ?>">
                            <td><?php echo $user_data['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($user_data['profile_image'])): ?>
                                        <img src="../<?php echo $user_data['profile_image']; ?>" alt="Profile" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <?php echo substr($user_data['first_name'], 0, 1); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($user_data['email']); ?></td>
                            <td><?php echo htmlspecialchars($user_data['phone'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo ($user_data['role'] == 'admin') ? 'primary' : 'secondary'; ?>">
                                    <?php echo ucfirst($user_data['role']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $statusClass; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user_data['created_at'])); ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="user-edit.php?id=<?php echo $user_data['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="user-delete.php?id=<?php echo $user_data['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No users found</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='8' class='text-danger'>Error fetching users: " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm" action="user-add.php" method="POST">
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addUserForm" class="btn btn-primary">Add User</button>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>

<script>
function searchTable() {
    // Get input value and convert to lowercase
    var input = document.getElementById("tableSearch");
    var filter = input.value.toLowerCase();
    
    // Get role filter value
    var roleFilter = document.getElementById("roleFilter").value.toLowerCase();
    
    // Get table and rows
    var table = document.getElementById("usersTable");
    var rows = table.getElementsByTagName("tr");
    
    // Loop through all table rows, starting from index 1 to skip the header
    for (var i = 1; i < rows.length; i++) {
        var showRow = false;
        var rowRole = rows[i].getAttribute("data-role");
        
        // Check if row matches role filter
        var roleMatch = !roleFilter || (rowRole && rowRole.toLowerCase() === roleFilter);
        
        if (!roleMatch) {
            rows[i].style.display = "none";
            continue;
        }
        
        // Get all cells in the row
        var cells = rows[i].getElementsByTagName("td");
        
        // If search input is empty, show the row (if it matches role filter)
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

function filterByRole() {
    searchTable(); // Reuse the search function to apply both filters
}
</script>
