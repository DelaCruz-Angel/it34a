
<?php

// START USER SESSION
function startUserSession($pdo)
{
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        INSERT INTO user_sessions (
            user_id,
            session_start
        )
        VALUES (
            :user_id,
            NOW()
        )
    ");

    $stmt->execute([
        'user_id' => $user_id
    ]);

    // Save database session ID
    $session_id = $pdo->lastInsertId();

    $_SESSION['session_id'] = $session_id;

    return $session_id;
}


// END USER SESSION
function endUserSession($pdo)
{
    if (!isset($_SESSION['session_id'])) {
        return false;
    }

    $session_id = $_SESSION['session_id'];

    $stmt = $pdo->prepare("
        UPDATE user_sessions
        SET
            session_end_time = NOW(),
            session_duration = TIMESTAMPDIFF(
                SECOND,
                session_start,
                NOW()
            )
        WHERE session_id = :session_id
          AND session_end_time IS NULL
    ");

    return $stmt->execute([
        'session_id' => $session_id
    ]);
}


// CHECK ACTIVE USER SESSION
function hasActiveUserSession($pdo, $user_id)
{
    $stmt = $pdo->prepare("
        SELECT session_id
        FROM user_sessions
        WHERE user_id = :user_id
          AND session_end_time IS NULL
        LIMIT 1
    ");

    $stmt->execute([
        'user_id' => $user_id
    ]);

    return (bool) $stmt->fetchColumn();
}


// SESSION TIMEOUT
function checkSessionTimeout()
{
    global $pdo;

    // 10 seconds for testing
    // 1800 seconds = 30 minutes
    $timeout = 10;

    // Check last activity
    if (isset($_SESSION['last_activity'])) {

        $inactive = time() - $_SESSION['last_activity'];

        // If inactive for 10 seconds
        if ($inactive >= $timeout) {

            // End database session
            endUserSession($pdo);

            // Destroy PHP session
            $_SESSION = [];

            session_destroy();

            // Redirect to login page
            header('Location: ' . BASE_URL . '/index.php?timeout=1');
            exit;
        }
    }

    // Update last activity
    $_SESSION['last_activity'] = time();
}

?>
