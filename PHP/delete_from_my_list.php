<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['book_id'])) {
    $book_id = $conn->real_escape_string($_GET['book_id']);

    // Prepare the SQL statement to delete the book from the user's list
    $stmt = $conn->prepare("DELETE FROM UserBooks WHERE user_id = ? AND book_id = ?");
    $stmt->bind_param("ii", $user_id, $book_id);

    if ($stmt->execute()) {
        // Redirect back to the manage_my_list.php with a success message
        header("Location: manage_my_list.php?msg=Book removed successfully");
    } else {
        // Redirect back with an error message
        header("Location: manage_my_list.php?msg=Error removing book");
    }

    $stmt->close();
} else {
    // Redirect back if no book_id is provided
    header("Location: manage_my_list.php?msg=No book selected for removal");
}

$conn->close();
?>
