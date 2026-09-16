<?php
/*
 * Programmer      : Sydney Allison Magdaluyo
 * Date Created    : September 16, 2026
 * Problem Description:
 *   Login page for the Travel Package Booking System. Validates the
 *   submitted username/password against xml/users.xml (Security
 *   Feature 1), creates the PHP session on success (Security Feature 2),
 *   and optionally stores the username - never the password - in a
 *   cookie when "Remember my username" is checked (Security Feature 5).
 */

require_once 'includes/session_check.php';
require_once 'includes/security.php';

// Already logged in? Skip straight to the dashboard.
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$errors = [];
$username_value = isset($_COOKIE['remember_username']) ? $_COOKIE['remember_username'] : '';

// Friendly message when redirected here by session_check.php
$notice = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'timeout') {
        $notice = 'Your session has expired. Please login again.';
    } elseif ($_GET['msg'] === 'login_required') {
        $notice = 'Please login first.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? ''; // compared raw, not echoed back
    $remember = isset($_POST['remember']);
    $username_value = $username;

    // ---- Security Feature 1: Login Validation ----
    if (!is_required($username)) {
        $errors[] = "Username is required.";
    }
    if (!is_required($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $users = simplexml_load_file(__DIR__ . '/xml/users.xml');
        $matched_user = null;

        foreach ($users->user as $u) {
            if ((string) $u->username === $username && (string) $u->password === $password) {
                $matched_user = $u;
                break;
            }
        }

        if ($matched_user === null) {
            // Generic message only - never reveal which field was wrong
            $errors[] = "Invalid username or password.";
        } else {
            // ---- Security Feature 2: PHP Session ----
            session_regenerate_id(true); // guard against session fixation
            $_SESSION['username']      = (string) $matched_user->username;
            $_SESSION['role']          = (string) $matched_user->role;
            $_SESSION['fullname']      = (string) $matched_user->fullname;
            $_SESSION['last_activity'] = time();

            // ---- Security Feature 5: Cookie (username only) ----
            if ($remember) {
                setcookie('remember_username', $username, time() + (30 * 24 * 60 * 60), '/');
            } else {
                setcookie('remember_username', '', time() - 3600, '/');
            }

            header("Location: index.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Travel Package Booking System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Travel Package Booking System</h1>
        <h2>Login</h2>

        <?php if ($notice): ?>
            <div class="notice-box"><?= htmlspecialchars($notice) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul style="margin:0; padding-left:18px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Login form: username, password, remember-username checkbox -->
        <form method="POST" action="login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username_value) ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password">

            <div class="checkbox-row">
                <input type="checkbox" id="remember" name="remember" <?= $username_value !== '' ? 'checked' : '' ?>>
                <label for="remember" style="margin:0;">Remember my username</label>
            </div>

            <button type="submit">LOGIN</button>
        </form>

        <p style="margin-top:20px; font-size:13px; color:#666;">
            Demo accounts &mdash; Admin: admin01 / Admin@123 &nbsp;|&nbsp; User: user01 / User@123
        </p>
    </div>
</body>
</html>
