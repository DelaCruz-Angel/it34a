<?php
require_once(__DIR__ . '/../../config/config.php');

// Log the logout and end the database session
if (isset($_SESSION['user_id'])) {

    logActivity(
        $pdo,
        $_SESSION['user_id'],
        $_SESSION['user_email'] ?? null,
        'logout',
        'success'
    );

    // IMPORTANT: End the database session before destroying PHP session
    endUserSession($pdo);
}

// Clear all session variables
$_SESSION = [];

// Destroy the PHP session
session_destroy();

// Redirect to login page
header('Location: ' . BASE_URL . '/index.php');
exit;