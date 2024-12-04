<?php
// Include authentication and database configuration files
require_once '../config/auth.php';
require_once '../config/db.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if not authenticated
redirectIfNotAuthenticated('login.php');

// Fetch user details
$user_id = $_SESSION['user_id'];
$user_stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_stmt->bind_result($name, $email);
$user_stmt->fetch();
$user_stmt->close();

// Fetch borrowed books for the authenticated user
$borrowed_books_stmt = $conn->prepare("
    SELECT bb.id, b.title, b.author, bb.borrowed_at, bb.due_date, bb.returned_at 
    FROM borrowed_books bb 
    JOIN books b ON bb.book_id = b.id 
    WHERE bb.user_id = ?
");
$borrowed_books_stmt->bind_param("i", $user_id);
$borrowed_books_stmt->execute();
$borrowed_books_stmt->bind_result($borrow_id, $book_title, $book_author, $borrowed_at, $due_date, $returned_at);

$borrowed_books = [];
while ($borrowed_books_stmt->fetch()) {
    $borrowed_books[] = [
        'id' => $borrow_id,
        'title' => $book_title,
        'author' => $book_author,
        'borrowed_at' => $borrowed_at,
        'due_date' => $due_date,
        'returned_at' => $returned_at
    ];
}
$borrowed_books_stmt->close();

// Fetch all books
$books_result = $conn->query("
    SELECT id, title, author, publication_year, available_copies 
    FROM books
");
$books = $books_result->fetch_all(MYSQLI_ASSOC);
$books_result->close();

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Library Management System</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body class="centered">
<div class="dashboard-container" style="width: 90%; max-width: 1200px;">
    <h2>User Account Details</h2>
    <p>Name: <?php echo htmlspecialchars($name ?? ''); ?></p>
    <p>Email: <?php echo htmlspecialchars($email ?? ''); ?></p>

    <h2>Borrowed Books</h2>
    <?php if (!empty($borrowed_books)): ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Borrowed At</th>
                    <th>Due Date</th>
                    <th>Returned At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($borrowed_books as $borrowed_book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($borrowed_book['title']); ?></td>
                        <td><?php echo htmlspecialchars($borrowed_book['author']); ?></td>
                        <td><?php echo htmlspecialchars($borrowed_book['borrowed_at']); ?></td>
                        <td><?php echo htmlspecialchars($borrowed_book['due_date']); ?></td>
                        <td><?php echo htmlspecialchars($borrowed_book['returned_at'] ?? ''); ?></td>
                        <td>
                            <?php if (is_null($borrowed_book['returned_at'])): ?>
                                <form method="POST" action="return_book.php">
                                    <input type="hidden" name="borrow_id" value="<?php echo htmlspecialchars($borrowed_book['id']); ?>">
                                    <button type="submit" class="button">Return</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No borrowed books.</p>
    <?php endif; ?>

    <h2>Book Details</h2>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Publication Year</th>
                <th>Available Copies</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo htmlspecialchars($book['publication_year']); ?></td>
                    <td><?php echo htmlspecialchars($book['available_copies']); ?></td>
                    <td>
                        <?php if ($book['available_copies'] > 0): ?>
                            <form method="POST" action="borrow_book.php">
                                <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['id']); ?>">
                                <button type="submit" class="button">Borrow</button>
                            </form>
                        <?php else: ?>
                            <span>Not Available</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button class="button" onclick="window.location.href='index.php'">Back to Home</button>
</div>
</body>
</html>
