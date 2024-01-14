<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="CSS/main.css">
    <script>
        // Function to handle book deletion via AJAX
        function deleteBook(bookId, element) {
            if (confirm('Are you sure you want to delete this book?')) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'delete_book.php?book_id=' + bookId, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var row = element.parentNode.parentNode;
                        row.parentNode.removeChild(row);
                    }
                }
                xhr.send();
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            // AJAX submission for the Add Book form
            document.getElementById("addBookForm").addEventListener("submit", function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "add_book.php", true);
                xhr.onload = function() {
                    if (xhr.status == 200) {
                        var response = xhr.responseText;
                        if (response === "Success") {
                            alert("Book added successfully.");
                            location.reload(); // Reloads the page to show the updated list
                        } else {
                            alert("Error adding book: " + response);
                        }
                    }
                };
                xhr.send(formData);
            });

        });
    </script>

</head>

<body>
    <header>
        <?php include 'navbar.php';?>
    </header>

    <main>
        <?php
        include 'db_connect.php';

        $authorsQuery = "SELECT author_id, name FROM Authors";
        $authorsResult = $conn->query($authorsQuery);

        $genresQuery = "SELECT genre_id, name FROM Genres";
        $genresResult = $conn->query($genresQuery);

        // Add Book Form
        echo "<form id='addBookForm' method='post' action='add_book.php'>";
        echo "<div class='form-group'>";
        echo "<label for='title'>Title:</label>";
        echo "<input type='text' id='title' name='title' required>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label for='author_id'>Author:</label>";
        echo "<select id='author_id' name='author_id'>";
        while ($author = $authorsResult->fetch_assoc()) {
            echo "<option value='" . $author['author_id'] . "'>" . htmlspecialchars($author['name']) . "</option>";
        }
        echo "</select>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label for='genre_id'>Genre:</label>";
        echo "<select id='genre_id' name='genre_id'>";
        while ($genre = $genresResult->fetch_assoc()) {
            echo "<option value='" . $genre['genre_id'] . "'>" . htmlspecialchars($genre['name']) . "</option>";
        }
        echo "</select>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label for='isbn'>ISBN:</label>";
        echo "<input type='text' id='isbn' name='isbn'>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label for='summary'>Summary:</label>";
        echo "<textarea id='summary' name='summary' required></textarea>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label for='publication_year'>Publication Year:</label>";
        echo "<input type='number' id='publication_year' name='publication_year'>";
        echo "</div>";

        echo "<button type='submit' class='submit-button'>Add Book</button>";
        echo "</form>";

        // Filter Books Form
        echo "<form id='filterBooksForm' method='post'>";
        echo "<div class='form-group'>";
        echo "<label for='filter_title'>Title:</label>";
        echo "<input type='text' id='filter_title' name='filter_title'>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label for='filter_author_id'>Author:</label>";
        echo "<select id='filter_author_id' name='filter_author_id'>";
        echo "<option value=''>All Authors</option>";
        $authorsResult->data_seek(0);
        while ($author = $authorsResult->fetch_assoc()) {
            echo "<option value='" . $author['author_id'] . "'>" . htmlspecialchars($author['name']) . "</option>";
        }
        echo "</select>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label for='filter_genre_id'>Genre:</label>";
        echo "<select id='filter_genre_id' name='filter_genre_id'>";
        echo "<option value=''>All Genres</option>";
        $genresResult->data_seek(0);
        while ($genre = $genresResult->fetch_assoc()) {
            echo "<option value='" . $genre['genre_id'] . "'>" . htmlspecialchars($genre['name']) . "</option>";
        }
        echo "</select>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label for='filter_isbn'>ISBN:</label>";
        echo "<input type='text' id='filter_isbn' name='filter_isbn'>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label for='filter_publication_year'>Publication Year:</label>";
        echo "<input type='number' id='filter_publication_year' name='filter_publication_year'>";
        echo "</div>";

        echo "<button type='submit' class='submit-button'>Filter Books / Display All</button>";
        echo "</form>";

        // Display Books Table
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $filterTitle = $_POST['filter_title'] ?? '';
            $filterAuthorId = $_POST['filter_author_id'] ?? '';
            $filterGenreId = $_POST['filter_genre_id'] ?? '';
            $filterIsbn = $_POST['filter_isbn'] ?? '';
            $filterPublicationYear = $_POST['filter_publication_year'] ?? '';

            $sql = "SELECT Books.book_id, Books.title, Authors.name AS author_name, Genres.name AS genre_name, Books.isbn, Books.summary, Books.publication_year FROM Books
                    JOIN Authors ON Books.author_id = Authors.author_id
                    JOIN Genres ON Books.genre_id = Genres.genre_id WHERE 1=1";

            if (!empty($filterTitle)) {
                $sql .= " AND Books.title LIKE '%" . $conn->real_escape_string($filterTitle) . "%'";
            }
            if (!empty($filterAuthorId)) {
                $sql .= " AND Books.author_id = '" . $conn->real_escape_string($filterAuthorId) . "'";
            }
            if (!empty($filterGenreId)) {
                $sql .= " AND Books.genre_id = '" . $conn->real_escape_string($filterGenreId) . "'";
            }
            if (!empty($filterIsbn)) {
                $sql .= " AND Books.isbn LIKE '%" . $conn->real_escape_string($filterIsbn) . "%'";
            }
            if (!empty($filterPublicationYear)) {
                $sql .= " AND Books.publication_year = '" . $conn->real_escape_string($filterPublicationYear) . "'";
            }
        } else {
            $sql = "SELECT Books.book_id, Books.title, Authors.name AS author_name, Genres.name AS genre_name, Books.isbn, Books.summary, Books.publication_year FROM Books
                    JOIN Authors ON Books.author_id = Authors.author_id
                    JOIN Genres ON Books.genre_id = Genres.genre_id";
        }

        $result = $conn->query($sql);

        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Title</th><th>Author</th><th>Genre</th><th>ISBN</th><th>Summary</th><th>Year</th><th>Action</th></tr>";

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["book_id"] . "</td>";
                echo "<td>" . htmlspecialchars($row["title"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["author_name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["genre_name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["isbn"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["summary"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["publication_year"]) . "</td>";
                echo "<td><button onclick='deleteBook(" . $row["book_id"] . ", this)'>Delete</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No books found</td></tr>";
        }
        echo "</table>";
        ?>
    </main>

    <footer>
        <p>&copy; 2023 Book Cataloging System. All rights reserved.</p>
    </footer>
</body>

</html>