<?php
include 'connection.php'; // Include the database connection
include 'auth_check.php'; // Ensure the user is logged in

// Fetch the user's role from the database
$user_id = $_SESSION['user_id']; // Assuming user ID is stored in the session
$query = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] !== "Section_Leadership") {
    header("Location: unauthorised.php"); // Redirect to an unauthorized access page
    exit();
}

// Fetch Monday patrol colours
$query_mon = "SELECT Kestrel_colour, Curlew_colour, Eagle_colour, Woodpecker_colour FROM monPointValues LIMIT 1";
$result_mon = $conn->query($query_mon);
if ($result_mon && $result_mon->num_rows > 0) {
    $monColours = $result_mon->fetch_assoc();
} else {
    // Fallback to default colours
    $monColours = [
        'Kestrel_colour' => '#FF0000',
        'Curlew_colour' => '#00FF00',
        'Eagle_colour' => '#0000FF',
        'Woodpecker_colour' => '#FFFF00',
    ];
}

// Fetch Wednesday patrol colours
$query_wed = "SELECT Kestrel_colour, Curlew_colour, Eagle_colour, Woodpecker_colour FROM wedPointValues LIMIT 1";
$result_wed = $conn->query($query_wed);
if ($result_wed && $result_wed->num_rows > 0) {
    $wedColours = $result_wed->fetch_assoc();
} else {
    // Fallback to default colours
    $wedColours = [
        'Kestrel_colour' => '#FF0000',
        'Curlew_colour' => '#00FF00',
        'Eagle_colour' => '#0000FF',
        'Woodpecker_colour' => '#FFFF00',
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Settings</title>
        <link rel="stylesheet" href="./styles.css">
    </head>

    <body>
        <?php include 'header.php'; ?>
        <main>
            <h1>Settings</h1>

            <!-- Update Colours Section -->
            <h2>Change Patrol Colours</h2>
            <form id="updateColoursForm" onsubmit="event.preventDefault(); updateColours();">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">Patrol</th>
                        <th colspan="2">Colour</th>
                    </tr>
                    <tr>
                        <th>Monday</th>
                        <th>Wednesday</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Kestrel</td>
                        <td>
                            <input type="color" id="kestrel_mon" name="kestrel_mon" value="<?= $monColours['Kestrel_colour'] ?>">
                        </td>
                        <td>
                            <input type="color" id="kestrel_wed" name="kestrel_wed" value="<?= $wedColours['Kestrel_colour'] ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Curlew</td>
                        <td>
                            <input type="color" id="curlew_mon" name="curlew_mon" value="<?= $monColours['Curlew_colour'] ?>">
                        </td>
                        <td>
                            <input type="color" id="curlew_wed" name="curlew_wed" value="<?= $wedColours['Curlew_colour'] ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Eagle</td>
                        <td>
                            <input type="color" id="eagle_mon" name="eagle_mon" value="<?= $monColours['Eagle_colour'] ?>">
                        </td>
                        <td>
                            <input type="color" id="eagle_wed" name="eagle_wed" value="<?= $wedColours['Eagle_colour'] ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Woodpecker</td>
                        <td>
                            <input type="color" id="woodpecker_mon" name="woodpecker_mon" value="<?= $monColours['Woodpecker_colour'] ?>">
                        </td>
                        <td>
                            <input type="color" id="woodpecker_wed" name="woodpecker_wed" value="<?= $wedColours['Woodpecker_colour'] ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
                <button type="submit">Update Colours</button>
            </form>

            <!-- Reset colours -->
            <h2>Reset All Patrol Colours to Default</h2>
            <form id="resetColoursForm" onsubmit="event.preventDefault(); resetColours();">
                <button type="submit">Reset All Colours</button>
            </form>

            <!-- Reset Points Section -->
            <h2>Reset Patrol Points</h2>
            <form id="resetMonForm" onsubmit="event.preventDefault(); resetPoints('mon');">
                <button type="submit">Reset Monday Points</button>
            </form>
            <form id="resetWedForm" onsubmit="event.preventDefault(); resetPoints('wed');">
                <button type="submit">Reset Wednesday Points</button>
            </form>

            <!-- User Management -->
            <h2>User Management</h2>
            <form action="editUser.php" method="GET">
                <button type="submit">Edit Existing User</button>
            </form>
            <form action="createUser.php" method="GET">
                <button type="submit">Create New User</button>
            </form>

            <!-- Popup for Messages -->
            <div id="popup"></div>
        </main>

        <script>
            function showPopup(message, isError = false) {
                const popup = document.getElementById("popup");
                popup.textContent = message;
                popup.className = isError ? "error" : "";
                popup.style.display = "block";
                setTimeout(() => (popup.style.display = "none"), 3000);
            }

            function updateColours() {
                const form = document.getElementById("updateColoursForm");
                const formData = new FormData(form);

                const xhr = new XMLHttpRequest();
                xhr.open("POST", "updateColours.php", true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            showPopup(xhr.responseText);
                        } else {
                            showPopup("An error occurred while updating colours.", true);
                        }
                    }
                };
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send(new URLSearchParams(formData).toString());
            }

            function resetColours() {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "resetColours.php", true); // Send the request to resetColours.php
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        const popup = document.getElementById("popup");
                        if (xhr.status === 200) {
                            popup.textContent = xhr.responseText; // Show the server response
                            popup.style.backgroundColor = "#4CAF50"; // Green for success
                        } else {
                            popup.textContent = "An error occurred while resetting colours.";
                            popup.style.backgroundColor = "#f44336"; // Red for errors
                        }
                        popup.style.display = "block"; // Display the popup
                        setTimeout(() => (popup.style.display = "none"), 3000); // Auto-hide popup
                    }
                };
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send(); // No additional data needed for reset
            }

            function resetPoints(day) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "resetPoints.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            showPopup(xhr.responseText);
                        } else {
                            showPopup("An error occurred. Please try again.", true);
                        }
                    }
                };
                xhr.send("day=" + day);
            }

        </script>

        <?php include 'footer.php'; ?>
    </body>
</html>