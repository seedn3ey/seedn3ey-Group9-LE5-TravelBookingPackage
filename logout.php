<?php
/*
 * Programmer      : Sydney Allison Magdaluyo
 * Date Created    : September 16, 2026
 * Problem Description:
 *   Logs the current user out. Clears all session data, expires the
 *   session cookie, and destroys the session so that pressing "Back"
 *   after logout can no longer reach protected pages.
 */

require_once 'includes/session_check.php';

// Clear session data
$_SESSION = array();

// Expire the session cookie itself
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

header("Location: login.php");
exit();
