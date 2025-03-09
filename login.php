<?php
session_start(); // Start the session

// Include the database connection file
include 'connection.php';

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
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE LOWER(username) = LOWER(?)");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the username exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user["password_hash"])) {
            // Store login status and user details in session
            $_SESSION["logged_in"] = true;
            $_SESSION["username"] = $user["username"];
            $_SESSION["user_id"] = $user["id"]; // Store the user ID in the session

            // Define backend scripts
            $backend_scripts = ['updatePoints.php', 'resetPoints.php', 'updateColours.php'];

            // Determine where to redirect
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect_page = $_SESSION['redirect_after_login'];

                // Check if the saved redirect is a backend script
                $script_name = basename(parse_url($redirect_page, PHP_URL_PATH));
                if (in_array($script_name, $backend_scripts)) {
                    // Redirect to the home page
                    $redirect_page = 'https://scoutpatrolpoints.pickering.cloud/';
                }

                // Clear the redirect_after_login variable
                unset($_SESSION['redirect_after_login']);
            } else {
                // Default to the home page
                $redirect_page = 'https://scoutspatrolpoints.pickering.cloud/';
            }

            // Redirect to the determined page
            header("Location: $redirect_page");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid username.";
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
    </head>
    <body>
        <?php include 'header.php'; ?>
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