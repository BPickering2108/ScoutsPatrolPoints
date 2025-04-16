<?php
// Start session only if none is active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session
}

// Define backend scripts that shouldn't be saved for redirection
$backend_scripts = ['updatePoints.php', 'resetPoints.php', 'updateColours.php'];

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    // Check if the current script is a backend script
    $current_script = basename($_SERVER['PHP_SELF']);
    if (!in_array($current_script, $backend_scripts)) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; // Save current page URL
    }

    // AJAX requests: Respond with 401
    if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
        header("HTTP/1.1 401 Unauthorized");
        echo "You need to log in to perform this action.";
        exit(); // Ensure no further execution
    }

    // Non-AJAX requests: Redirect to login page
    header("Location: login");
    exit();

    // Validate CSRF token for POST requests
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            // Expired or invalid token: Destroy the session
            include 'logout.php';
            exit();
        }
    }  
}

include 'permissions.php'
?>