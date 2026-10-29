<?php
// start user session
function startUserSession($pdo)
{
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
    INSERT INTO user_sessions(
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

    return $pdo->lastInsertId();
}


// end user session


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

// function to check if user is already logged in
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

//Session Timeout 
function checkSessionTimeout()
{
    global $pdo;

    //Timeout Settings
    // 10s for testing
    // 1800s for 30 minutes session time

    $timeout = 10; // 30 minutes or 10

//check last activity
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];

        if ($inactive >= $timeout) {
            
                endUserSession($pdo);
            }
            //Destroy PHP Sessions
            session_unset();
            session_destroy();

            //Redirect
            header('Location: ' . BASE_URL . '/index.php?timeout=1');
            exit;
        }
    }

    $_SESSION['last_activity'] = time();
}


?>