<?php
// Ensure this script is not accessed directly
if (!defined('AUTH_FUNCTIONS_INCLUDED')) {
    define('AUTH_FUNCTIONS_INCLUDED', true); // Second argument is mandatory for define()

    /**
     * Logs a user in by verifying their credentials.
     * 
     * @param PgSql\Connection $con Database connection.
     * @param string $email User's email.
     * @param string $password User's plaintext password.
     * @return bool|array Returns the user's data on success or false on failure.
     */
    function loginUser($con, $email, $password)
    {
        // Escape email to prevent SQL injection
        $email = pg_escape_string($con, $email);

        // Query to find the user by email
        $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = pg_query($con, $query);

        if ($result && pg_num_rows($result) === 1) {
            $user = pg_fetch_assoc($result);

            // Verify the password
            if (password_verify($password, $user['password'])) {
                return $user; // Return user data on success
            }
        }
        return false; // Login failed
    }

    /**
     * Checks if a user is logged in by verifying session data.
     * 
     * @return bool True if the user is logged in, false otherwise.
     */
    function isLoggedIn()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(); // Start the session if not already started
        }
        return isset($_SESSION['user_id']);
    }

    /**
     * Logs a user out by clearing their session.
     */
    function logoutUser()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(); // Start the session if not already started
        }

        // Clear all session data
        $_SESSION = [];
        session_destroy(); // Destroy the session
    }

    /**
     * Redirects the user to the login page if not logged in.
     */
    function requireLogin()
    {
        if (!isLoggedIn()) {
            header('Location: login.php');
            exit(); // Prevent further script execution
        }
    }
}