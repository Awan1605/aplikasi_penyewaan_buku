<?php
// Include database connection
require_once 'koneksi2.php';

// Start session
session_start();

// Check if login form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $username = trim(htmlspecialchars($_POST['username']));
    $password = htmlspecialchars($_POST['password']);

    // Validate input
    if (empty($username) || empty($password)) {
        header('Location: login.php?error=invalid_input');
        exit();
    }

    // Prepare statement to prevent SQL injection
    $query = "SELECT user_id, username, password FROM users WHERE username = ?";
    $stmt = $koneksi->prepare($query);

    if (!$stmt) {
        die("Query preparation failed: {$koneksi->error}");
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, create session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Redirect to dashboard
            header('Location: dashboard.php');
            exit();
        } else {
            // Incorrect password
            header('Location: login.php?error=wrong_password');
            exit();
        }
    } else {
        // User not found
        header('Location: login.php?error=user_not_found');
        exit();
    }
} else {
    // If accessed directly without POST
    header('Location: login.php');
    exit();
}
