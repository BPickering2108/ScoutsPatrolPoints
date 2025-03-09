<?php
// Include the database connection file
include 'connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user inputs
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Validate inputs
    if (empty($username) || empty($password)) {
        die("Username and password cannot be empty.");
    }

    // Query to find the user (case-insensitive for username)
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE LOWER(username) = LOWER(?)");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Fetch the user's data
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user["password_hash"])) {
            echo "Login successful! Welcome, " . htmlspecialchars($user["username"]) . ".";
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid username.";
    }

    $stmt->close();
    $conn->close();
}
?>