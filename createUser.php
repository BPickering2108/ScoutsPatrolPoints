<?php
session_start();
include 'connection.php'; // Include the database connection
include 'auth_check.php'; // Ensure the user is logged in and has a valid CSRF token
include 'log_action.php';

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

// Handle form submission for creating a new user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role = trim($_POST["role"]); // Get the selected role from the form

    if (empty($username) || empty($password) || empty($role)) {
        $error_message = "All fields are required.";
    } else {
        // Check for duplicate username
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Username is already taken.";
        } else {
            // Hash the password securely
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);

            if ($stmt->execute()) {
                log_action('User created: ')
                $success_message = "User created successfully with role: $role";
            } else {
                $error_message = "Error: " . $stmt->error;
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
        <title>Create User</title>
        <link rel="stylesheet" href="./styles.css">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <h1>Create User</h1>
        <main>
            <!-- Display error or success messages -->
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <!-- Create New User Form -->
            <div class="form-container">
                <form action="create_user.php" method="POST">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    <br><br>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" autocomplete="user-password" required>
                    <br><br>
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="">Select a role</option>
                        <option value="Section_Leadership">Section Leadership</option>
                        <option value="Section_Member">Section Member</option>
                    </select>
                    <br><br>
                    <button type="submit">Create User</button>
                </form>
            </div>
        </main>
        <?php include 'footer.php'; ?>
    </body>
</html>