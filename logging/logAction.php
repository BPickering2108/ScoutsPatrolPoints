<?php
function logAction($action, $target_username = null, $details = null) {
    // Get the username of the current user from the session
    if(session_status() === PHP_SESSION_NONE) {
        $current_username = "anonymous";
    } else{
        $current_username = $_SESSION["username"] ?? "Unknown";
    }

    // Get the IP address and country
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $host_country = getCountryByIp($ip_address); // Retrieve host country

    // Build the log entry
    $log_entry = date('Y-m-d H:i:s') . " | Performed By: $current_username | Action: $action";

    if ($target_username) {
        $log_entry .= " | Target: $target_username";
    }

    if ($details) {
        $log_entry .= " | Details: $details";
    }

    $log_entry .= " | IP: $ip_address | Host Country: $host_country" . PHP_EOL;

    // Path to your log file
    $log_dir = dirname(__DIR__) . '/secure_logs/';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true); // Create the directory if it doesn’t exist
    }
    $log_file = $log_dir . 'admin_logs.txt';
    


    // Write to the log file
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Helper function to retrieve country by IP address (as in previous implementation)
function getCountryByIp($ip_address) {
    $geo_api_url = "http://ip-api.com/json/$ip_address";
    $geo_data = file_get_contents($geo_api_url);
    if ($geo_data === false) {
        return 'Unknown'; // Return a default country if the API call fails
    }
    $geo_json = json_decode($geo_data, true);

    return $geo_json['country'] ?? 'Unknown';
}
?>