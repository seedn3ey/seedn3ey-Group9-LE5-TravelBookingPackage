<?php
/*
 * Group      : Group #9
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

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$errors = [];
$username_value = isset($_COOKIE['remember_username']) ? $_COOKIE['remember_username'] : '';
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
    $password = $_POST['password'] ?? ''; 
    $remember = isset($_POST['remember']);
    $username_value = $username;

    if (!is_required($username)) $errors[] = "Username is required.";
    if (!is_required($password)) $errors[] = "Password is required.";

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
            $errors[] = "Invalid username or password.";
        } else {
            session_regenerate_id(true); 
            $_SESSION['username']      = (string) $matched_user->username;
            $_SESSION['role']          = (string) $matched_user->role;
            $_SESSION['fullname']      = (string) $matched_user->fullname;
            $_SESSION['last_activity'] = time();

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
    <title>Login - Aurelia Luxury Flight</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="logo-circle">
            <img src="css/LOGO.png" alt="Aurelia Flight">
        </div>
        
        <div class="brand-header">
            <h1>AURELIA</h1>
            <div class="subtitle">
                <span class="line"></span>
                <p>Where Luxury Takes Flight</p>
                <span class="line"></span>
            </div>
        </div>

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

        <form class="login-form" method="POST" action="login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username_value) ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password">
            
            <div class="checkbox-row">
                <input type="checkbox" id="remember" name="remember" <?= $username_value !== '' ? 'checked' : '' ?>>
                <label for="remember">Remember my username</label>
            </div>

            <div class="btn-container">
                <button type="submit" class="btn-gold">LOGIN</button>
            </div>
        </form>

        <p style="margin-top:30px; font-size:12px; color:#a99d79; text-align:center;">
            Demo accounts &mdash; Admin: admin01 / Admin@123 &nbsp;|&nbsp; User: user01 / User@123
        </p>
    </div>
</body>
</html>