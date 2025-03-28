<?php
include 'connection.php'; // Include the database connection
include 'auth_check.php'; // Ensure the user is logged in and has a valid CSRF token

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get patrol, action, and page from AJAX request
    $patrol = $_POST['patrol'];
    $action = $_POST['action'];
    $page = $_POST['page']; // This determines which table to update

    // Map $page values to human-readable names
    $pageNames = [
        "monScouts" => "Monday Scouts",
        "wedScouts" => "Wednesday Scouts"
    ];

    // Validate the page parameter
    $tables = [
        "monScouts" => "monPointValues",
        "wedScouts" => "wedPointValues"
    ];

    if (!array_key_exists($page, $tables)) {
        http_response_code(400); // Bad request
        echo "Invalid page.";
        exit();
    }

    $table = $tables[$page]; // Select the correct table based on the page

    // Map patrol to corresponding column in the database
    $columns = [
        "Kestrel" => "Kestrel_points",
        "Curlew" => "Curlew_points",
        "Eagle" => "Eagle_points",
        "Woodpecker" => "Woodpecker_points"
    ];

    // Ensure the patrol exists in our mapping
    if (!array_key_exists($patrol, $columns)) {
        http_response_code(400); // Bad request
        echo "Invalid patrol.";
        exit();
    }

    $column = $columns[$patrol];

    // Determine increment or decrement
    $operation = ($action === "increment") ? "$column = $column + 1" : "$column = $column - 1";

    // Update the patrol points in the correct table
    $query = "UPDATE $table SET $operation LIMIT 1";
    if ($conn->query($query)) {
        $friendlyPageName = isset($pageNames[$page]) ? $pageNames[$page] : "Unknown Page";
        echo "Points updated successfully for " . htmlspecialchars($patrol, ENT_QUOTES, 'UTF-8') . " in " . htmlspecialchars($friendlyPageName, ENT_QUOTES, 'UTF-8') . ".";
    } else {
        echo "Error updating points: " . $conn->error;
    }

    $conn->close();
}
?>