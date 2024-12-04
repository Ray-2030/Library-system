<?php
// Start session
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Clear the session cookie if it exists
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to login page
header("Location: login.php");
exit();
?>
