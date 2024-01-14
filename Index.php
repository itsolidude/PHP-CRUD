<?php
// Start PHP session
session_start();

// Database connection script
include 'PHP/db_connect.php';

// Fetch recent books
$recentBooksQuery = "SELECT title, author_id FROM Books ORDER BY book_id DESC LIMIT 5";
$recentBooksResult = $conn->query($recentBooksQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head Section -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Cataloging System</title>
    <link rel="stylesheet" href="CSS/main.css">
</head>
<body>
    <header>
        <?php include 'PHP/navbar.php';?>
    </header>
    <main>
        <section>
            <h2>Welcome to Your Personal Book Library</h2>
            <p>Discover, manage, and track your book collection with ease.</p>

            <?php
            // User Greeting
            if (isset($_SESSION['username'])) {
                echo "<p>Welcome back, " . htmlspecialchars($_SESSION['username']) . "!</p>";
            }

            // Display recent books
            if ($recentBooksResult->num_rows > 0) {
                echo "<h3>Most Recent 10 Books</h3><ul>";
                while ($row = $recentBooksResult->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['title']) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No recent books added.</p>";
            }
            ?>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Book Cataloging System. All rights reserved.</p>
    </footer>
</body>
</html>
