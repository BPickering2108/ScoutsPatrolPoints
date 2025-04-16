<?php
    require_once 'backend/connection.php';
    include 'backend/authCheck.php';

    // Restrict access: only allow users with Administrator OR Section_Leadership access
    if (!in_array('Administrator', $_SESSION['user_roles']) && !in_array('Section_Leadership', $_SESSION['user_roles'])) {
        header("Location: unauthorised.php");
        exit();
    }

    // Determine which sections to display based on user roles.
    // Administrators see everything.
    $sections = [];
    if (in_array('Administrator', $_SESSION['user_roles'])) {
        $sections = ['Scouts', 'Explorers'];
    } else {
        // Only add sections if the user explicitly has that role.
        if (in_array('Scouts', $_SESSION['user_roles'])) {
            $sections[] = 'Scouts';
        }
        if (in_array('Explorers', $_SESSION['user_roles'])) {
            $sections[] = 'Explorers';
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Settings</title>
        <link rel="stylesheet" href="./styles.css">
        <link rel="icon" type="image/x-icon" href="favicon.ico">
    </head>
    <body>
        <?php include 'header.php'; ?>
        <?php
            // Reuse code: loop over each section and include the shared settings files.
            foreach ($sections as $section) {
                echo "<h2>" . htmlspecialchars($section) . "</h2>";
                
                // This variable can be used in the included files to decide which database table to use.
                $currentSection = strtolower($section); // e.g., 'scouts' or 'explorers'
                
                include 'settingsColours.php';
                include 'settingsPoints.php';
            }
        ?>
        <?php include 'footer.php'; ?>
    </body>
</html>