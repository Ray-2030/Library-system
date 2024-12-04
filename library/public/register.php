<?php
// Start session
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Library Management System</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body class="centered">
<div class="form-container">
    <h2>Register</h2>
    <!-- Registration form -->
    <form method="POST" action="register.php">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Register</button>
    </form>
    <div>
        <!-- Display registration message if set in session -->
        <?php
        if (isset($_SESSION['message'])) {
            echo '<p class="message">' . htmlspecialchars($_SESSION['message']) . '</p>';
            unset($_SESSION['message']);
        }
        ?>
    </div>
</div>
</body>
</html>

<?php
// Include database configuration file
require_once '../config/db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check for empty inputs
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['message'] = "All fields are required!";
        header("Location: register.php");
        exit();
    }

    // Validate password strength
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $_SESSION['message'] = "Password must be at least 8 characters long and contain both letters and numbers.";
        header("Location: register.php");
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        // Execute statement and handle success or failure
        if ($stmt->execute()) {
            // Store the new user ID in session
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['message'] = "Registration successful!";
            header("Location: add_book.php");
            exit();
        } else {
            if ($conn->errno === 1062) { // Duplicate entry error
                $_SESSION['message'] = "Email address already registered!";
            } else {
                $_SESSION['message'] = "Registration failed: " . $stmt->error;
            }
            header("Location: register.php");
            exit();
        }

        $stmt->close();
    } else {
        // SQL preparation error
        $_SESSION['message'] = "Failed to prepare the registration statement.";
        header("Location: register.php");
        exit();
    }

    $conn->close();
}
?>
