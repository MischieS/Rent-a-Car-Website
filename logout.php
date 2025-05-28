<?php
// Include session handler
require_once 'includes/session.php';

// Clear user session
clearUserSession();

// Redirect to login page
header("Location: login.php");
exit();
?>
