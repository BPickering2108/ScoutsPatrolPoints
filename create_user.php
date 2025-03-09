<?php
// Include the database connection file
include './connection.php';

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

    // Check for duplicate username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Username is already taken.");
    }

    $stmt->close();

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Set the role
    $role = 'Section_Leadership';

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "User created successfully with role: $role";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>