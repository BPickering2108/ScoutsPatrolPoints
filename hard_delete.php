<?php
include 'connection.php'; // Database connection

// Delete users marked for deletion for more than 14 days
$stmt = $conn->prepare("DELETE FROM users WHERE is_deleted = TRUE AND deleted_at <= NOW() - INTERVAL 14 DAY");
if ($stmt->execute()) {
    echo "Hard delete completed successfully.";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
?>