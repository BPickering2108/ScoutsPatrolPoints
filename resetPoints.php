<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include 'connection.php'; // Include database connection

    // Get the 'day' value from POST data
    $day = $_POST['day'] ?? ''; // Use a default empty value in case 'day' is not set
    $message = "";

    if ($day === 'mon') {
        // Reset Monday points
        $query = "UPDATE monPointValues SET Kestrel_points = 0, Curlew_points = 0, Eagle_points = 0, Woodpecker_points = 0";
        $message = $conn->query($query) ? "Monday points reset successfully!" : "Failed to reset Monday points.";
    } elseif ($day === 'wed') {
        // Reset Wednesday points
        $query = "UPDATE wedPointValues SET Kestrel_points = 0, Curlew_points = 0, Eagle_points = 0, Woodpecker_points = 0";
        $message = $conn->query($query) ? "Wednesday points reset successfully!" : "Failed to reset Wednesday points.";
    } else {
        $message = "Invalid request.";
    }

    $conn->close(); // Close the connection

    echo $message; // Output plain-text response for the popup
    exit(); // Ensure nothing else is output
}
?>