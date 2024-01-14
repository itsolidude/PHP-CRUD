<?php
include 'db_connect.php';

if (isset($_GET['book_id'])) {
    $book_id = $conn->real_escape_string($_GET['book_id']);

    // SQL to delete the book
    $sql = "DELETE FROM Books WHERE book_id = '$book_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
} else {
    echo "No book ID provided";
}
?>
