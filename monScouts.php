<?php
include 'connection.php'; // Include your database connection

// Function to calculate text contrast based on the patrol colour
function getTextColor($hexColor) {
    // Strip the '#' if it exists
    $hexColor = ltrim($hexColor, '#');

    // Convert hex to RGB
    $r = hexdec(substr($hexColor, 0, 2));
    $g = hexdec(substr($hexColor, 2, 2));
    $b = hexdec(substr($hexColor, 4, 2));

    // Calculate relative luminance
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

    // Return white text for dark backgrounds and black text for light ones
    return ($luminance > 0.5) ? '#000000' : '#FFFFFF';
}

// Fetch patrol data
$query = "SELECT Kestrel_points, Kestrel_colour, 
                 Curlew_points, Curlew_colour, 
                 Eagle_points, Eagle_colour, 
                 Woodpecker_points, Woodpecker_colour 
          FROM monPointValues LIMIT 1";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    die("No data found in the database.");
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WSSG Patrol Points</title>
        <link rel="stylesheet" href="./styles.css">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <h1>Monday Scouts</h1>
        <div class="progress-container">
            <!-- Kestrel -->
            <div class="progress-wrapper" onclick="showButtons('Kestrel')">
                <div class="progress-bar">
                    <div class="progress-fill" 
                        style="height: <?= $data['Kestrel_points'] ?>; background-color: <?= $data['Kestrel_colour'] ?>; color: <?= getTextColor($data['Kestrel_colour']) ?>;">
                        <?= intval($data['Kestrel_points'])?>
                    </div>
                </div>
                <div class="progress-label">Kestrel</div>
                <div class="buttons" id="Kestrel-buttons">
                    <button onclick="updatePoints('Kestrel', 'increment')">+</button>
                    <button onclick="updatePoints('Kestrel', 'decrement')">-</button>
                </div>
            </div>

            <!-- Curlew -->
            <div class="progress-wrapper" onclick="showButtons('Curlew')">
                <div class="progress-bar">
                    <div class="progress-fill" 
                        style="height: <?= $data['Curlew_points'] ?>; background-color: <?= $data['Curlew_colour'] ?>; color: <?= getTextColor($data['Curlew_colour']) ?>;">
                        <?= intval($data['Curlew_points'])?>
                    </div>
                </div>
                <div class="progress-label">Curlew</div>
                <div class="buttons" id="Curlew-buttons">
                    <button onclick="updatePoints('Curlew', 'increment')">+</button>
                    <button onclick="updatePoints('Curlew', 'decrement')">-</button>
                </div>
            </div>

            <!-- Eagle -->
            <div class="progress-wrapper" onclick="showButtons('Eagle')">
                <div class="progress-bar">
                    <div class="progress-fill" 
                        style="height: <?= $data['Eagle_points'] ?>; background-color: <?= $data['Eagle_colour'] ?>; color: <?= getTextColor($data['Eagle_colour']) ?>;">
                        <?= intval($data['Eagle_points'])?>
                    </div>
                </div>
                <div class="progress-label">Eagle</div>
                <div class="buttons" id="Eagle-buttons">
                    <button onclick="updatePoints('Eagle', 'increment')">+</button>
                    <button onclick="updatePoints('Eagle', 'decrement')">-</button>
                </div>
            </div>

            <!-- Woodpecker -->
            <div class="progress-wrapper" onclick="showButtons('Woodpecker')">
                <div class="progress-bar">
                    <div class="progress-fill" 
                        style="height: <?= $data['Woodpecker_points'] ?>; background-color: <?= $data['Woodpecker_colour'] ?>; color: <?= getTextColor($data['Woodpecker_colour']) ?>;">
                        <?= intval($data['Woodpecker_points'])?>
                    </div>
                </div>
                <div class="progress-label">Woodpecker</div>
                <div class="buttons" id="Woodpecker-buttons">
                    <button onclick="updatePoints('Woodpecker', 'increment')">+</button>
                    <button onclick="updatePoints('Woodpecker', 'decrement')">-</button>
                </div>
            </div>
        </div>
        <main>
        </main>
        <script>
            // Function to update points
            function showButtons(patrol) {
                const buttonsDiv = document.getElementById(patrol + '-buttons');
                
                // Hide all other buttons
                document.querySelectorAll('.buttons').forEach(btn => {
                    if (btn !== buttonsDiv) {
                        btn.style.display = 'none';
                    }
                });

                // Toggle the display of the clicked patrol's buttons
                buttonsDiv.style.display = (buttonsDiv.style.display === 'block') ? 'none' : 'block';
            }


            function updatePoints(patrol, action) {
                const page = "monScouts";

                const xhr = new XMLHttpRequest();
                xhr.open("POST", "updatePoints.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            alert(xhr.responseText); // Success notification
                            location.reload(); // Reload to reflect updates
                        } else if (xhr.status === 401) {
                            alert("Your session has expired. Redirecting to login...");
                            window.location.href = "login"; // Redirect the user to login page
                        } else {
                            alert("An error occurred. Please try again.");
                        }
                    }
                };
                xhr.send("patrol=" + patrol + "&action=" + action + "&page=" + page);
            }
        </script>
    </body>
    <?php include 'footer.php'; ?>
</html>