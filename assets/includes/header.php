<?php
// Include session handler if not already included
if (!function_exists('isLoggedIn')) {
    // Start output buffering to prevent headers already sent error
    ob_start();
    require_once __DIR__ . '/../../includes/session.php';
    ob_end_clean();
}
?>

<header class="main-header">
    <div class="container">
        <nav class="navbar navbar-expand-lg py-3">
            <a class="navbar-brand" href="index.php">
                <span class="brand-dreams">RENT A</span> <span class="brand-rent">CAR</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'cars.php' || basename($_SERVER['PHP_SELF']) == 'booking.php') ? 'active' : ''; ?>" href="cars.php">Booking</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>" href="about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'faq.php') ? 'active' : ''; ?>" href="faq.php">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact</a>
                    </li>
                    <?php if (isAdmin()) : ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'admin') !== false) ? 'active' : ''; ?>" href="admin/index.php">Admin</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="header-buttons">
                    <?php if (isLoggedIn()) : ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-dark dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?php echo getCurrentUserName(); ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> My Profile</a></li>
                                <li><a class="dropdown-item" href="my-bookings.php"><i class="fas fa-calendar-check me-2"></i> My Bookings</a></li>
                                <?php if (isAdmin()) : ?>
                                <li><a class="dropdown-item" href="admin/index.php"><i class="fas fa-cog me-2"></i> Admin Dashboard</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a></li>
                            </ul>
                        </div>
                    <?php else : ?>
                        <a href="login.php" class="btn btn-outline-dark">Sign In</a>
                        <a href="register.php" class="btn btn-primary">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </div>
</header>
