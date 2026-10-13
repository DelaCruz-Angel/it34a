<?php
require_once __DIR__ . '/config/config.php';

if (isset($_SESSION['user_id'])) {
    header(
        'Location: ' . BASE_URL . '/app/' .
        $_SESSION['user_role'] . '/index.php'
    );
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        $error = 'Invalid login credentials';

        logActivity(
            $pdo,
            null,
            $login,
            'login',
            'failed'
        );
    } else {
        if (loginUser($pdo, $login, $password)) {
            logActivity(
                $pdo,
                $_SESSION['user_id'],
                $_SESSION['user_email'],
                'login',
                'success'
            );

            header(
                'Location: ' . BASE_URL . '/app/' .
                $_SESSION['user_role'] . '/index.php'
            );
            exit;
        } else {
            $error = 'Invalid login credentials';

            logActivity(
                $pdo,
                null,
                $login,
                'login',
                'failed'
            );
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

    <?php if ($error !== ''): ?>
        <p role="alert">
            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>
    <?php endif; ?>

    <h1>User Login</h1>

    <form method="post">
        <label for="login">Username or email</label>
        <input
            type="text"
            id="login"
            name="login"
            autocomplete="username"
            required
        >

        <br>

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            required
        >

        <br>

        <button type="submit">Sign In</button>
    </form>

</body>
</html>