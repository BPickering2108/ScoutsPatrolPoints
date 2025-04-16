<?php
require_once 'connection.php'; // Include your database connection

function getTextColor($hexColor) {
    $hexColor = ltrim($hexColor, '#');
    $r = hexdec(substr($hexColor, 0, 2));
    $g = hexdec(substr($hexColor, 2, 2));
    $b = hexdec(substr($hexColor, 4, 2));

    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    return ($luminance > 0.5) ? '#000000' : '#FFFFFF';
}

// Determine table name dynamically
$day = $_GET['day'] ?? ($_POST['day'] ?? 'monScouts'); // Check both GET and POST, then fallback
$tableName = match ($day) {
    'monScouts' => 'monPointValues',
    'tueExplorers' => 'tuePointValues',
    'wedScouts' => 'wedPointValues',
    'thurExplorers' => 'thurPointValues',
    default => 'monPointValues' // Fallback
};

$query = "SELECT Kestrel_points, Kestrel_colour, 
                 Curlew_points, Curlew_colour, 
                 Eagle_points, Eagle_colour, 
                 Woodpecker_points, Woodpecker_colour 
          FROM $tableName LIMIT 1";

$result = $conn->query($query);

$data = ($result->num_rows > 0) ? $result->fetch_assoc() : die("No data found.");
$conn->close();
?>