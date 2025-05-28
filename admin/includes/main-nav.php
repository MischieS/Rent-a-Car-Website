<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top admin-navbar">
  <div class="container-fluid">
      <!-- Brand Logo -->
      <a class="navbar-brand" href="index.php">
          <span class="brand-text">DREAMS <span class="brand-text-highlight">RENT</span></span>
      </a>
      
      <!-- Mobile Toggle Button -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
      </button>
      
      <!-- Navigation Items -->
      <div class="collapse navbar-collapse" id="adminNavbar">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                  <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">
                      <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'cars.php' || basename($_SERVER['PHP_SELF']) == 'car-add.php' || basename($_SERVER['PHP_SELF']) == 'car-edit.php') ? 'active' : ''; ?>" href="cars.php">
                      <i class="fas fa-car me-1"></i> Cars
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'bookings.php' || basename($_SERVER['PHP_SELF']) == 'booking-view.php' || basename($_SERVER['PHP_SELF']) == 'booking-edit.php' || basename($_SERVER['PHP_SELF']) == 'booking-calendar.php') ? 'active' : ''; ?>" href="bookings.php">
                      <i class="fas fa-calendar-check me-1"></i> Bookings
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>" href="users.php">
                      <i class="fas fa-users me-1"></i> Users
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>" href="settings.php">
                      <i class="fas fa-cog me-1"></i> Settings
                  </a>
              </li>
          </ul>
          
          <!-- Right Side Menu -->
          <ul class="navbar-nav">
              <li class="nav-item">
                  <a class="nav-link" href="../index.php" target="_blank">
                      <i class="fas fa-external-link-alt me-1"></i> View Site
                  </a>
              </li>
              <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-user-circle me-1"></i> 
                      <?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'Admin'; ?>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                      <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> My Profile</a></li>
                      <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i> Settings</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a></li>
                  </ul>
              </li>
          </ul>
      </div>
  </div>
</nav>
