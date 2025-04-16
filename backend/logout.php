<?php
// Destroy the session
$_SESSION = []; // Clear session data
session_unset(); 
session_destroy();
setcookie(session_name(), '', time() - 3600, '/'); // Expire session cookie

// Redirect the user to the homepage
header("Location: ../index");
exit();
?>