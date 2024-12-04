<?php
// Include database configuration file
require_once '../config/db.php';

// Check if book ID is set in the GET request
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $book_id = $_GET['id'];

    // Prepare SQL statement to select book details by ID
    $stmt = $conn->prepare("SELECT title, author, publication_year, available_copies FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();

    // Check if the book exists
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        // Bind result variables
        $stmt->bind_result($title, $author, $publication_year, $available_copies);
        $stmt->fetch();
        // Close the statement
        $stmt->close();
    } else {
        // Redirect if no book was found
        $stmt->close();
        header("Location: index.php");
        exit();
    }
} else {
    // Redirect to home page if book ID is not set or invalid
    header("Location: index.php");
    exit();
}

// Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Details</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body class="centered">
    <div class="form-container">
        <h2>Book Details</h2>
        <!-- Display book details -->
        <p><strong>Title:</strong> <?php echo htmlspecialchars($title); ?></p>
        <p><strong>Author:</strong> <?php echo htmlspecialchars($author); ?></p>
        <p><strong>Publication Year:</strong> <?php echo htmlspecialchars($publication_year); ?></p>
        <p><strong>Available Copies:</strong> <?php echo htmlspecialchars($available_copies); ?></p>
        <!-- Link to go back to home page -->
        <a class="button" href="index.php">Back to Home</a>
    </div>
</body>
</html>
