<?php

function logActivity($pdo, $user_id, $email, $action, $status = "success")
{
    try {
        // Get client IP address
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? 'UNKNOWN';

        // If there are multiple IPs, get the first one
        if (strpos($ip_address, ',') !== false) {
            $ip_address = trim(explode(',', $ip_address)[0]);
        }

        // Get user agent
        $user_agent = substr(
            $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN',
            0,
            255
        );

        // Prepare query
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (
                user_id,
                user_email,
                activity_log_action,
                activity_log_status,
                activity_log_ip_address,
                activity_log_user_agent
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");

        // Execute insert
        $success = $stmt->execute([
            $user_id,
            $email,
            $action,
            $status,
            $ip_address,
            $user_agent
        ]);

        return $success;

    } catch (PDOException $e) {
        error_log("Activity log error: " . $e->getMessage());
        return false;
    }
}
?>
