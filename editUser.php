<?php
session_start();
include 'connection.php'; // Include the database connection
include 'auth_check.php'; // Ensure the user is logged in and has a valid CSRF token
include 'api_helpers.php';

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

// Handle form submission for editing a user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $new_password = trim($_POST["new_password"]);
    $role = trim($_POST["role"]);
    $delete_user = isset($_POST["delete_user"]) && $_POST["delete_user"] === "true";

    if (empty($username)) {
        $error_message = "Username is required.";

    // Handle user deletion
    } elseif ($delete_user) {
        if ($username === 'Bradley_Pickering') {
            $error_message = "No user found with the specified username."
        } else{
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $error_message = "No user found with the specified username.";
            } else {
                $stmt = $conn->prepare("UPDATE users SET is_deleted = TRUE, deleted_at = NOW() WHERE username = ?");
                $stmt->bind_param("s", $username);

                if ($stmt->execute()) {
                    $success_message = "User '$username' has been successfully deleted.";

                    // Log soft delete action
                    logAction(
                        "Soft Delete Marked",
                        $username,
                        "User marked for soft deletion."
                    );

                    // Alert via Pushover
                    sendPushoverNotification('User marked for deletion');
                    
                } else {
                    $error_message = "Error deleting user: " . $stmt->error;
                }
            }
        }
        $stmt->close();

    } elseif (empty($new_password) && empty($role)) {
        $error_message = "At least one of new password or role is required.";
    } else {
        // Check if the username exists
        $stmt = $conn->prepare("SELECT id, password_reset_allowed FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error_message = "No user found with the specified username.";
        } else {
            $user = $result->fetch_assoc();

            if (!empty($new_password)) {
                // Ensure password reset is allowed
                if (!$user['password_reset_allowed']) {
                    $error_message = "Password reset is not allowed for this user.";
                } else {
                    // Hash and update the password
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
                    $stmt->bind_param("ss", $hashed_password, $username);

                    if ($stmt->execute()) {
                        $success_message = "Password updated successfully for user: $username";

                        logAction(
                            "Password Updated",
                            $username,
                            "Password was changed."
                        );
                    } else {
                        $error_message = "Error updating password: " . $stmt->error;
                    }
                }
            }

            if (!empty($role)) {
                // Update the role
                $stmt = $conn->prepare("UPDATE users SET role = ? WHERE username = ?");
                $stmt->bind_param("ss", $role, $username);

                if ($stmt->execute()) {
                    $success_message = "Role updated successfully for user: $username";

                    logAction(
                        "Role Updated",
                        $username,
                        "Role changed to $role."
                    );
                } else {
                    $error_message = "Error updating role: " . $stmt->error;
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
        <title>Edit Existing User</title>
        <link rel="stylesheet" href="./styles.css">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <h1>Edit Existing User</h1>
        <main>
            <!-- Display error or success messages -->
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <!-- Edit User Form -->
            <div class="form-container">
            <h2>Edit Existing User</h2>
            <form id="editUserForm" action="editUser.php" method="POST">
                <table class="edit-user-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>New Password</th>
                            <th>Role</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" id="username" name="username" placeholder="Enter username" required>
                            </td>
                            <td>
                                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" autocomplete="new-password">
                            </td>
                            <td>
                                <select id="role" name="role">
                                    <option value="">Select a role</option>
                                    <option value="Section_Leadership">Section Leadership</option>
                                    <option value="Section_Member">Section Member</option>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="delete_user" value="true">
                                    <img src="bin-icon.png" alt="Delete Icon" style="width: 16px; height: 16px;"> Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit" class="update-user-btn">Update User</button>
            </form>
            </div>
        </main>
        <?php include 'footer.php'; ?>
    </body>
</html>

<script>
    document.getElementById("deleteButton").addEventListener("click", function () {
        const username = document.getElementById("username").value.trim();

        if (!username) {
            alert("Please enter the username before attempting to delete.");
            return;
        }

        // Show a prompt for deletion confirmation
        const confirmation = prompt(`Type "delete ${username}" to confirm deletion:`);

        if (confirmation === `delete ${username}`) {
            // If the confirmation matches, create a hidden form and submit it
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "editUser.php";

            // Add CSRF token
            const csrfTokenInput = document.createElement("input");
            csrfTokenInput.type = "hidden";
            csrfTokenInput.name = "csrf_token";
            csrfTokenInput.value = "<?= htmlspecialchars($csrf_token) ?>";
            form.appendChild(csrfTokenInput);

            // Add delete_user field
            const deleteUserInput = document.createElement("input");
            deleteUserInput.type = "hidden";
            deleteUserInput.name = "delete_user";
            deleteUserInput.value = "true";
            form.appendChild(deleteUserInput);

            // Add username_to_delete field
            const usernameInput = document.createElement("input");
            usernameInput.type = "hidden";
            usernameInput.name = "username_to_delete";
            usernameInput.value = username;
            form.appendChild(usernameInput);

            document.body.appendChild(form);
            form.submit();
        } else {
            alert("Deletion cancelled or confirmation text did not match.");
        }
    });
</script>