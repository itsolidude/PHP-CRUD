<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle adding a book to the user's list
$bookAlreadyExists = false; // Flag for checking if the book is already in the user's list

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book_id'])) {
    $add_book_id = $_POST['add_book_id'];

    // Check if the book is already in the user's list
    $checkStmt = $conn->prepare("SELECT * FROM UserBooks WHERE user_id = ? AND book_id = ?");
    $checkStmt->bind_param("ii", $user_id, $add_book_id);
    $checkStmt->execute();
    $resultCheck = $checkStmt->get_result();

    if ($resultCheck->num_rows > 0) {
        // Book already exists in user's list
        $bookAlreadyExists = true;
    } else {
        // Insert the book into user's list
        $stmt = $conn->prepare("INSERT INTO UserBooks (user_id, book_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $add_book_id);
        $stmt->execute();
        $stmt->close();
    }
    $checkStmt->close();
}


// Initialize filter variables and a flag to track if books are found
$filter_title = isset($_POST['filter_title']) ? $_POST['filter_title'] : '';
$filter_author = isset($_POST['filter_author']) ? $_POST['filter_author'] : '';
$filter_genre = isset($_POST['filter_genre']) ? $_POST['filter_genre'] : '';
$filter_isbn = isset($_POST['filter_isbn']) ? $_POST['filter_isbn'] : '';
$booksFound = false;

// SQL query to fetch all books with optional filters
$sql_all_books = "SELECT Books.book_id, Books.title, Books.isbn, Authors.name AS author_name, Genres.name AS genre_name 
                  FROM Books 
                  JOIN Authors ON Books.author_id = Authors.author_id 
                  JOIN Genres ON Books.genre_id = Genres.genre_id 
                  WHERE Books.title LIKE CONCAT('%', ?, '%') 
                  AND Authors.name LIKE CONCAT('%', ?, '%')
                  AND Genres.name LIKE CONCAT('%', ?, '%')
                  AND Books.isbn LIKE CONCAT('%', ?, '%')";
$stmt_all_books = $conn->prepare($sql_all_books);
$stmt_all_books->bind_param("ssss", $filter_title, $filter_author, $filter_genre, $filter_isbn);
$stmt_all_books->execute();
$result_all_books = $stmt_all_books->get_result();

if ($result_all_books->num_rows > 0) {
    $booksFound = true;
}

// SQL query to fetch books added by the logged-in user
$sql_user_books = "SELECT Books.book_id, Books.title, Books.isbn, Authors.name AS author_name, Genres.name AS genre_name 
                   FROM Books 
                   INNER JOIN UserBooks ON Books.book_id = UserBooks.book_id
                   INNER JOIN Authors ON Books.author_id = Authors.author_id 
                   INNER JOIN Genres ON Books.genre_id = Genres.genre_id 
                   WHERE UserBooks.user_id = ?";
$stmt_user_books = $conn->prepare($sql_user_books);
$stmt_user_books->bind_param("i", $user_id);
$stmt_user_books->execute();
$result_user_books = $stmt_user_books->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage My Book List</title>
</head>

<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <main>
        <!-- Filtering Form -->
        <form method="post">
            <label for="filter_title">Filter by Title:</label>
            <input type="text" id="filter_title" name="filter_title" value="<?php echo htmlspecialchars($filter_title); ?>">

            <label for="filter_author">Filter by Author:</label>
            <input type="text" id="filter_author" name="filter_author" value="<?php echo htmlspecialchars($filter_author); ?>">

            <label for="filter_genre">Filter by Genre:</label>
            <input type="text" id="filter_genre" name="filter_genre" value="<?php echo htmlspecialchars($filter_genre); ?>">

            <label for="filter_isbn">Filter by ISBN:</label>
            <input type="text" id="filter_isbn" name="filter_isbn" value="<?php echo htmlspecialchars($filter_isbn); ?>">

            <button type="submit">Apply Filter</button>
        </form>

        <h2>All Available Books</h2>
        <section>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>ISBN</th>
                    <th>Action</th>
                </tr>
                <?php
                if ($result_all_books->num_rows > 0) {
                    while ($row = $result_all_books->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['author_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['genre_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['isbn']) . "</td>";
                        echo "<td>";
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='add_book_id' value='" . $row['book_id'] . "'>";
                        echo "<button type='submit'>Add to My List</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No books found.</td></tr>";
                }
                $stmt_all_books->close();
                ?>
            </table>
        </section>

        <h2>My Book List</h2>
        <section>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>ISBN</th>
                    <th>Action</th>
                </tr>
                <?php
                if ($result_user_books->num_rows > 0) {
                    while ($row = $result_user_books->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['author_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['genre_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['isbn']) . "</td>";
                        echo "<td><a href='delete_from_my_list.php?book_id=" . $row['book_id'] . "'>Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Your book list is empty.</td></tr>";
                }
                $stmt_user_books->close();
                ?>
            </table>
        </section>
    </main>

    <footer>
        <?php include 'footer.php'; ?>
    </footer>

    <!-- Javascript-->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!$booksFound) : ?>
            alert('No books found with the specified criteria.');
        <?php endif; ?>

        <?php if ($bookAlreadyExists) : ?>
            alert('This book is already in your list.');
        <?php endif; ?>
    });
</script>

</body>

</html>