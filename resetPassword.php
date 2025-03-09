<?php
session_start();
include 'connection.php'; // Include the database connection

// Ensure only logged-in users with the "Section_Leadership" role can access this page
$user_id = $_SESSION['user_id']; // Get the user ID from the session
$query = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] !== "Section_Leadership") {
    header("Location: unauthorized.php"); // Redirect to an unauthorized access page
    exit();
}

// Handle form submission for resetting a user's password
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $new_password = trim($_POST["new_password"]);

    if (empty($username) || empty($new_password)) {
        $error_message = "All fields are required.";
    } else {
        // Check if the username exists and if password resets are allowed
        $stmt = $conn->prepare("SELECT id, password_reset_allowed FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error_message = "No user found with the specified username.";
        } else {
            $user = $result->fetch_assoc();
            if (!$user['password_reset_allowed']) {
                $error_message = "Password reset is not allowed for this user.";
            } else {
                // Hash the new password securely
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Update the user's password in the database
                $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
                $stmt->bind_param("ss", $hashed_password, $username);

                if ($stmt->execute()) {
                    $success_message = "Password reset successfully for user: $username";
                } else {
                    $error_message = "Error: " . $stmt->error;
                }
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password</title>
        <link rel="stylesheet" href="./styles.css">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <h1>Reset User Password</h1>
        <main>
            <!-- Display error or success messages -->
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <!-- Reset Password Form -->
            <div class="form-container">
                <form action="resetPassword.php" method="POST">
                    <h2>Reset Password</h2>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" placeholder="Enter username" required>
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                    <button type="submit">Reset Password</button>
                </form>
            </div>
        </main>
        <?php include 'footer.php'; ?>
    </body>
</html>