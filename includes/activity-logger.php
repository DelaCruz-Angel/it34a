<?php
require_once ("..config/config.php");
//require`_once ('includes/activity-logger.php');
function logActivity($pdo, $user_id,$email,$action, $status="success") {
    try{
        //get client ip address
        $ip_address = $_SERVER['HTTP_X_FORWARD_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

        //string to array
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);


        }

        //get user agent
        $user_agent = substr( $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN', 0,255);

        //query
        $stmt = $pdo->prepare("
        INSERT INTO activity_logs(
        user_id, 
        user_email,
        activity_log_action,
        activity_log_status,
        activity_log_ip_address,
        activity_log_user_agent
        ) VALUES (?, ?, ?, ?, ?, ?)
        ");

        //execute insert
        $success = $stmt->execute([
            $user_id, 
            $email, 
            $action, 
            $status, 
            $ip,
            $user_agent]);

            return $success;


    } catch (PDOException $e) {
        // Handle the exception (e.g., log it, display an error message, etc.)
        error_log("Activity log error: " . $e->getMessage());
        return false;
} 
}
?>