<?php
require_once 'connection.php';

if (!isset($_SESSION['user_roles'])) {
    $user_id = $_SESSION['user_id'];
    
    // Fetch the role names for the current user
    $stmt = $conn->prepare("SELECT r.role_name 
                            FROM roles AS r 
                            JOIN user_roles AS ur ON r.id = ur.role_id 
                            WHERE ur.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $roles = [];
    
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row['role_name'];
    }
    
    // Store the roles in the session
    $_SESSION['user_roles'] = $roles;
}
?>