<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    session_destroy(); // Destroy all session data
    echo "<script>alert('You have been successfully logged out.'); window.location = '../index.php';</script>";
} else {
    // If not logged in, display an alert and redirect
    echo "<script>alert('You are not logged in.'); window.location = '../index.php';</script>";
}
exit;
?>
