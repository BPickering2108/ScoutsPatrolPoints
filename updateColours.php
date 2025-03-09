<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include 'connection.php'; // Include database connection

    $response = ""; // Initialize response

    try {
        // Sanitize and fetch inputs
        $kestrel_mon = htmlspecialchars($_POST['kestrel_mon']);
        $curlew_mon = htmlspecialchars($_POST['curlew_mon']);
        $eagle_mon = htmlspecialchars($_POST['eagle_mon']);
        $woodpecker_mon = htmlspecialchars($_POST['woodpecker_mon']);
        $kestrel_wed = htmlspecialchars($_POST['kestrel_wed']);
        $curlew_wed = htmlspecialchars($_POST['curlew_wed']);
        $eagle_wed = htmlspecialchars($_POST['eagle_wed']);
        $woodpecker_wed = htmlspecialchars($_POST['woodpecker_wed']);

        // Validate hex colors
        $valid_color_regex = '/^#[0-9A-Fa-f]{6}$/';
        foreach ([$kestrel_mon, $curlew_mon, $eagle_mon, $woodpecker_mon, $kestrel_wed, $curlew_wed, $eagle_wed, $woodpecker_wed] as $color) {
            if (!preg_match($valid_color_regex, $color)) {
                echo "Invalid color: $color";
                exit();
            }
        }

        // Update Monday colours
        $query_mon = "UPDATE monPointValues SET Kestrel_colour = ?, Curlew_colour = ?, Eagle_colour = ?, Woodpecker_colour = ?";
        $stmt_mon = $conn->prepare($query_mon);
        $stmt_mon->bind_param("ssss", $kestrel_mon, $curlew_mon, $eagle_mon, $woodpecker_mon);
        $stmt_mon->execute();

        // Update Wednesday colours
        $query_wed = "UPDATE wedPointValues SET Kestrel_colour = ?, Curlew_colour = ?, Eagle_colour = ?, Woodpecker_colour = ?";
        $stmt_wed = $conn->prepare($query_wed);
        $stmt_wed->bind_param("ssss", $kestrel_wed, $curlew_wed, $eagle_wed, $woodpecker_wed);
        $stmt_wed->execute();

        // Success message
        $response = "Colours updated successfully!";
    } catch (Exception $e) {
        $response = "Error updating colours: " . $e->getMessage();
    }

    $conn->close(); // Close connection
    echo $response; // Return response to JavaScript
    exit();
}
?>