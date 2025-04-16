<?php
require_once 'backend/connection.php';
include 'logging/logAction.php';

session_start(); // Start the session

// Generate CSRF token for authentication
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expiry'] = time() + 3600; // Token valid for 1 hour
}
$csrf_token = $_SESSION['csrf_token'];

// Define an error message variable
$error_message = '';

// Check for the `error` query parameter
if (isset($_GET['error'])) {
    $error_message = "Your login session has expired. Please log in again.";
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user input
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Basic validation to ensure fields are not empty
    if (empty($username) || empty($password)) {
        echo "Username and password cannot be empty.";
        exit();
    }

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("SELECT id, username, password_hash, is_deleted FROM users WHERE LOWER(username) = LOWER(?) AND is_deleted = FALSE");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the username exists
    if ($result->num_rows === 0) {
        echo "Account is disabled or does not exist.";
        exit();
    } else {
        $user = $result->fetch_assoc();
    }

    // Verify the password
    if (password_verify($password, $user["password_hash"])) {
        // Store login status and user details in session
        $_SESSION["logged_in"] = true;
        $_SESSION["username"] = $user["username"];
        $_SESSION["user_id"] = $user["id"]; // Store the user ID in the session
        logAction('Login Successful', $username); // Log login attempt

        // Define backend scripts
        $backend_scripts = ['updatePoints.php', 'resetPoints.php', 'updateColours.php'];

        // Determine where to redirect
        if (isset($_SESSION['redirect_after_login'])) {
            $redirect_page = $_SESSION['redirect_after_login'];

            // Check if the saved redirect is a backend script
            $script_name = basename(parse_url($redirect_page, PHP_URL_PATH));
            if (in_array($script_name, $backend_scripts)) {
                // Redirect to the home page
                $redirect_page = 'index.php';
            }

            // Clear the redirect_after_login variable
            unset($_SESSION['redirect_after_login']);
        } else {
            // Default to the home page
            $redirect_page = 'index.php';
        }

        // Redirect to the determined page
        header("Location: $redirect_page");

        exit();
    } else {
        logAction('Login Failed', $username); // Log login attempt
        echo "Invalid password.";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="./styles.css">
        <link rel="icon" type="image/x-icon" href="favicon.ico">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <!-- Display error message if it's set -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error_message) ?>
                <button class="close-btn" onclick="closeErrorMessage()">×</button>
            </div>
        <?php endif; ?>

        <h1>Login</h1>
        <main>
            <div class="form-container">
                <form action="login.php" method="POST">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    <br><br>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <br><br>
                    <button type="submit">Login</button>
                </form>
            </div>
        </main>
        <?php include 'footer.php'; ?>
    </body>
</html>

<script>
    function closeErrorMessage() {
        const errorMessage = document.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.style.display = 'none'; // Hides the error message
        }
    }
</script>