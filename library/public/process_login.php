<?php
// Include database and authentication configuration files
require_once '../config/db.php';
require_once '../config/auth.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form data
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Check if required inputs are not empty
    if (empty($email) || empty($password)) {
        session_start();
        $_SESSION['message'] = "Email and password are required!";
        header("Location: login.php");
        exit();
    }

    // Ensure the database connection is established
    if (!isset($conn) || $conn->connect_error) {
        session_start();
        $_SESSION['message'] = "Database connection error!";
        header("Location: login.php");
        exit();
    }

    // Prepare SQL statement to select user by email
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if user exists
        if ($stmt->num_rows > 0) {
            // Bind result variables
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                session_start();
                $_SESSION['user_id'] = $id;
                $_SESSION['message'] = "Login successful!";
                header("Location: add_book.php");
                exit();
            } else {
                // Invalid password
                session_start();
                $_SESSION['message'] = "Invalid password!";
                header("Location: login.php");
                exit();
            }
        } else {
            // User not found
            session_start();
            $_SESSION['message'] = "No user found with that email address!";
            header("Location: login.php");
            exit();
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // SQL preparation error
        session_start();
        $_SESSION['message'] = "Failed to prepare the SQL statement!";
        header("Location: login.php");
        exit();
    }

    // Close the database connection
    $conn->close();
} else {
    // Invalid request method
    session_start();
    $_SESSION['message'] = "Invalid request method!";
    header("Location: login.php");
    exit();
}
