<?php
// Include the database configuration
require_once '../config/db.php';

// Function to create tables
function create_tables($conn) {
    // SQL to create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Users table created successfully.<br>";
    } else {
        echo "Error creating users table: " . $conn->error . "<br>";
    }

    // SQL to create books table
    $sql = "CREATE TABLE IF NOT EXISTS books (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                author VARCHAR(255) NOT NULL,
                isbn VARCHAR(20) UNIQUE NOT NULL,
                publication_year INT NOT NULL,
                available_copies INT NOT NULL
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Books table created successfully.<br>";
    } else {
        echo "Error creating books table: " . $conn->error . "<br>";
    }

    // SQL to create borrowed_books table
    $sql = "CREATE TABLE IF NOT EXISTS borrowed_books (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                book_id INT,
                borrowed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                due_date DATE NOT NULL,
                returned_at DATETIME,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (book_id) REFERENCES books(id)
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Borrowed_books table created successfully.<br>";
    } else {
        echo "Error creating borrowed_books table: " . $conn->error . "<br>";
    }
}

// Call the function to create tables
create_tables($conn);
?>
