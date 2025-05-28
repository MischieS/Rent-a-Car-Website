<?php
// Include session and check if user is logged in and is admin
require_once '../includes/session.php';
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Car.php';
require_once '../models/Booking.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Create user object
$user = new User($db);
$user->id = $_SESSION['user_id'];
$user_data = $user->getUser();

// Check if user is admin
if ($user_data['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Get statistics
$car = new Car($db);
$total_cars = $car->getTotalCars();

$booking = new Booking($db);
$total_bookings = $booking->getTotalBookings();
$pending_bookings = $booking->getBookingCountByStatus('pending');
$confirmed_bookings = $booking->getBookingCountByStatus('confirmed');
$completed_bookings = $booking->getBookingCountByStatus('completed');
$cancelled_bookings = $booking->getBookingCountByStatus('cancelled');

$total_users = $user->getTotalUsers();

// Get recent bookings
$recent_bookings = $booking->getRecentBookings(5);

// Get popular cars
$popular_cars = $car->getPopularCars(5);

// Include header
include_once 'includes/header.php';
?>

<div class="admin-page-title">
    <h1>Dashboard</h1>
    <p>Welcome back, <?php echo $user_data['first_name']; ?>!</p>
</div>

<!-- Stats Row -->
<div class="row">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Total Users</h6>
                        <h3><?php echo $total_users; ?></h3>
                    </div>
                    <div class="stat-icon text-warning">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Total Cars</h6>
                        <h3><?php echo $total_cars; ?></h3>
                    </div>
                    <div class="stat-icon text-success">
                        <i class="fas fa-car fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Total Bookings</h6>
                        <h3><?php echo $total_bookings; ?></h3>
                    </div>
                    <div class="stat-icon text-info">
                        <i class="fas fa-calendar-check fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Pending Bookings</h6>
                        <h3><?php echo $pending_bookings; ?></h3>
                    </div>
                    <div class="stat-icon text-warning">
                        <i class="fas fa-clock fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Stats Row -->
<div class="row">
    <div class="col-lg-8 col-md-12 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Bookings</h5>
                <a href="bookings.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Car</th>
                                <th>Dates</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_bookings)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No recent bookings</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td>#<?php echo $booking['id']; ?></td>
                                        <td><?php echo isset($booking['customer_name']) ? $booking['customer_name'] : $booking['user_id']; ?></td>
                                        <td><?php echo $booking['car_name']; ?></td>
                                        <td>
                                            <?php echo date('M d', strtotime($booking['pickup_date'])); ?> - 
                                            <?php echo date('M d, Y', strtotime($booking['return_date'])); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $booking['status'] == 'confirmed' ? 'success' : 
                                                    ($booking['status'] == 'pending' ? 'warning' : 
                                                    ($booking['status'] == 'cancelled' ? 'danger' : 'info')); 
                                            ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td>$<?php echo number_format(isset($booking['total_amount']) ? $booking['total_amount'] : $booking['total_price'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-12 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Popular Cars</h5>
                <a href="cars.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($popular_cars)): ?>
                        <li class="list-group-item text-center">No cars available</li>
                    <?php else: ?>
                        <?php foreach ($popular_cars as $car): ?>
                            <li class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo !empty($car['image']) ? '../' . $car['image'] : '../assets/img/car-placeholder.png'; ?>" 
                                         alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>" 
                                         class="me-3" 
                                         style="width: 60px; height: 40px; object-fit: cover; border-radius: 5px;">
                                    <div>
                                        <h6 class="mb-0"><?php echo $car['brand'] . ' ' . $car['model']; ?></h6>
                                        <small class="text-muted">
                                            <?php echo $car['year']; ?> • 
                                            <?php echo isset($car['bookings_count']) ? $car['bookings_count'] : 0; ?> bookings
                                        </small>
                                    </div>
                                    <div class="ms-auto">
                                        <span class="badge bg-primary">$<?php echo number_format($car['price_per_day'], 2); ?>/day</span>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Booking Status Row -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Booking Status Overview</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                        <div class="p-3 bg-light rounded text-center">
                            <h5 class="text-warning mb-1">Pending</h5>
                            <h3 class="mb-0"><?php echo $pending_bookings; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                        <div class="p-3 bg-light rounded text-center">
                            <h5 class="text-success mb-1">Confirmed</h5>
                            <h3 class="mb-0"><?php echo $confirmed_bookings; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                        <div class="p-3 bg-light rounded text-center">
                            <h5 class="text-info mb-1">Completed</h5>
                            <h3 class="mb-0"><?php echo $completed_bookings; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="p-3 bg-light rounded text-center">
                            <h5 class="text-danger mb-1">Cancelled</h5>
                            <h3 class="mb-0"><?php echo $cancelled_bookings; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
