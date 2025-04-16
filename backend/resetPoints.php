<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once 'connection.php'; // Include database connection
    include 'authCheck.php'; // Ensure the user is logged in and has a valid CSRF token

    // Get the 'day' value from POST data
    $dayMap = [
        'mon' => 'monPointValues',
        'tue' => 'tuePointValues',
        'wed' => 'wedPointValues',
        'thur' => 'thurPointValues'
    ];

    $day = $_POST['day'] ?? '';
    $message = "";

    if (isset($dayMap[$day])) {
        // Build the query dynamically using the mapped table name
        $query = "UPDATE {$dayMap[$day]} SET Kestrel_points = 0, Curlew_points = 0, Eagle_points = 0, Woodpecker_points = 0";
        $message = $conn->query($query) ? ucfirst($day) . "day points reset successfully!" : "Failed to reset " . ucfirst($day) . "day points.";
    } else {
        $message = "Invalid request.";
    }

    $conn->close(); // Close the connection

    echo $message; // Output plain-text response for the popup
    exit(); // Ensure nothing else is output
}
?>