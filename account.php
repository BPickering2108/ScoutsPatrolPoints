<?php
require_once 'backend/connection.php'; // Include database connection
include 'backend/authCheck.php'; // Ensure the user is logged in

// Get the logged-in user's username
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';

// Handle password reset form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    if ($new_password === $confirm_password && !empty($new_password)) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Update the password in the database
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $success_message = "Password updated successfully.";
        } else {
            $error_message = "Failed to update password. Please try again.";
        }
        $stmt->close();
    } else {
        $error_message = "Passwords do not match or are empty.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Account</title>
        <link rel="stylesheet" href="./styles.css">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <main>
            <!-- Personalized Welcome Message -->
            <h1>Welcome, <?= htmlspecialchars($username) ?>!</h1>

            <!-- Display success or error messages -->
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <!-- Password Reset Form -->
            <form method="POST" action="account.php">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
                <br><br>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <br><br>
                <button type="submit">Reset Password</button>
            </form>
        </main>
        <?php include 'footer.php'; ?>
    </body>
</html>