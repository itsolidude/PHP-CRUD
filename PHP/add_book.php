<?php
// Database connection script
include '../PHP/db_connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $title = $conn->real_escape_string($_POST['title']);
    $author_id = $conn->real_escape_string($_POST['author_id']);
    $genre_id = $conn->real_escape_string($_POST['genre_id']);
    $isbn = $conn->real_escape_string($_POST['isbn']);
    $summary = $conn->real_escape_string($_POST['summary']);
    $publication_year = $conn->real_escape_string($_POST['publication_year']);

    // SQL to insert the new book
    $sql = "INSERT INTO Books (title, author_id, genre_id, isbn, summary, publication_year) 
            VALUES ('$title', '$author_id', '$genre_id', '$isbn', '$summary', '$publication_year')";

    // Execute the query and check if it was successful
    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        // Return error message for the AJAX response
        echo "Error adding book: " . $conn->error;
    }

    $conn->close();
}
?>
