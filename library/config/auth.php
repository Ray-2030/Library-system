<?php
/**
 * Start a session if it hasn't already been started.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Checks if the user is authenticated by verifying if the 'user_id' session variable is set.
 *
 * @return bool Returns true if the 'user_id' session variable is set, false otherwise.
 */
function isAuthenticated(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Redirects the user to a specified location if they are not authenticated.
 *
 * @param string $location The URL to redirect to if the user is not authenticated.
 */
function redirectIfNotAuthenticated(string $location): void {
    if (!isAuthenticated()) {
        header("Location: $location");
        exit(); // Ensure the script stops execution after redirection.
    }
}
