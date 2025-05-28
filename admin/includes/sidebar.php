<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <span class="brand-text">RENT A <span class="brand-text-highlight">CAR</span></span>
        </div>
    </div>
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['cars.php', 'car-add.php', 'car-edit.php'])) ? 'active' : ''; ?>" href="cars.php">
                    <i class="fas fa-car"></i>
                    <span>Cars</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'car-add.php') ? 'active' : ''; ?>" href="car-add.php">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add New Car</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['bookings.php', 'booking-view.php', 'booking-edit.php'])) ? 'active' : ''; ?>" href="bookings.php">
                    <i class="fas fa-calendar-check"></i>
                    <span>Bookings</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'booking-calendar.php') ? 'active' : ''; ?>" href="booking-calendar.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Calendar</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>" href="users.php">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </div>
</div>
