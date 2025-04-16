<?php
require_once 'connection.php'; // Include the database connection
include 'authCheck.php'; // Ensure the user is logged in and has a valid CSRF token

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Ensure the required POST parameters are set
    if (!isset($_POST['patrol'], $_POST['action'], $_POST['page'])) {
        http_response_code(400);
        echo "Missing parameters.";
        exit();
    }

    // Get patrol, action, and page from AJAX request
    $patrol = $_POST['patrol'];
    $action = $_POST['action'];
    $page = $_POST['page']; // This determines which table to update
    $count  = isset($_POST['count']) ? intval($_POST['count']) : 1; // Default to 1 if not provided

    // Map $page values to human-readable names
    $pageNames = [
        "monScouts" => "Monday Scouts",
        "tueExplorers" => "Tuesday Explorers",
        "wedScouts" => "Wednesday Scouts",
        "thurExplorers" => "Thursday Explorers"
    ];

    // Validate the page parameter
    $tables = [
        "monScouts" => "monPointValues",
        "tueExplorers" => "tuePointValues",
        "wedScouts" => "wedPointValues",
        "thurExplorers" => "thurPointValues"
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

    // Validate that the action is either 'increment' or 'decrement'
    if ($action !== "increment" && $action !== "decrement") {
        http_response_code(400);
        echo "Invalid action.";
        exit();
    }

    // Build the SQL query using the whitelisted table and column names.
    if ($action === "increment") {
        $query = "UPDATE $table SET $column = $column + ? LIMIT 1";
    } else { // decrement action
        $query = "UPDATE $table SET $column = $column - ? LIMIT 1";
    }

    // Prepare the statement
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        http_response_code(500);
        echo "Database error: " . $conn->error;
        exit();
    }

    // Bind the integer parameter using "i"
    $stmt->bind_param("i", $count);

    // Execute the statement
    if ($stmt->execute()) {
        $friendlyPageName = isset($pageNames[$page]) ? $pageNames[$page] : "Unknown Page";
        echo "Points updated successfully for " . htmlspecialchars($patrol, ENT_QUOTES, 'UTF-8') .
             " in " . htmlspecialchars($friendlyPageName, ENT_QUOTES, 'UTF-8') . ".";
    } else {
        http_response_code(500);
        echo "Error updating points: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>