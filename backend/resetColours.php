<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once 'connection.php'; // Include the database connection
    include 'authCheck.php'; // Ensure the user is logged in and has a valid CSRF token

    try {
        // Define default colours
        $defaultColours = [
            'Kestrel' => '#76c7c0',
            'Curlew' => '#ff6f61',
            'Eagle' => '#f7b32b',
            'Woodpecker' => '#4a90e2'
        ];

        // Reset Monday patrol colours
        $query_mon = "UPDATE monPointValues SET Kestrel_colour = ?, Curlew_colour = ?, Eagle_colour = ?, Woodpecker_colour = ?";
        $stmt_mon = $conn->prepare($query_mon);
        $stmt_mon->bind_param("ssss", $defaultColours['Kestrel'], $defaultColours['Curlew'], $defaultColours['Eagle'], $defaultColours['Woodpecker']);
        $stmt_mon->execute();

        // Reset Tuesday patrol colours
        $query_tue = "UPDATE tuePointValues SET Kestrel_colour = ?, Curlew_colour = ?, Eagle_colour = ?, Woodpecker_colour = ?";
        $stmt_tue = $conn->prepare($query_mon);
        $stmt_tue->bind_param("ssss", $defaultColours['Kestrel'], $defaultColours['Curlew'], $defaultColours['Eagle'], $defaultColours['Woodpecker']);
        $stmt_tue->execute();

        // Reset Wednesday patrol colours
        $query_wed = "UPDATE wedPointValues SET Kestrel_colour = ?, Curlew_colour = ?, Eagle_colour = ?, Woodpecker_colour = ?";
        $stmt_wed = $conn->prepare($query_wed);
        $stmt_wed->bind_param("ssss", $defaultColours['Kestrel'], $defaultColours['Curlew'], $defaultColours['Eagle'], $defaultColours['Woodpecker']);
        $stmt_wed->execute();

        // Reset Thursday patrol colours
        $query_thur = "UPDATE thurPointValues SET Kestrel_colour = ?, Curlew_colour = ?, Eagle_colour = ?, Woodpecker_colour = ?";
        $stmt_thur = $conn->prepare($query_mon);
        $stmt_thur->bind_param("ssss", $defaultColours['Kestrel'], $defaultColours['Curlew'], $defaultColours['Eagle'], $defaultColours['Woodpecker']);
        $stmt_thur->execute();

        // Confirm success
        echo "All patrol colours have been reset to default values successfully!";
    } catch (Exception $e) {
        // Handle errors and output a response
        error_log($e->getMessage(), 0);
        echo "An unexpected error occurred while resetting the patrol colours. Please try again later.";
    }

    $conn->close(); // Close the connection
    exit(); // Ensure no further execution
}
?>