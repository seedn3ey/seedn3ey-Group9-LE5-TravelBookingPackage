<?php
/*
 * Programmer      : Sydney Allison Magdaluyo
 * Date Created    : September 16, 2026
 * Problem Description:
 *   This file centralizes all PHP session handling for the Travel Package
 *   Booking System. It starts the session (if not already started),
 *   provides require_login() to block guests from protected pages, and
 *   enforces a 15-minute inactivity timeout so sessions do not stay
 *   active forever on shared computers.
 */

// Start the session once, no matter which page includes this file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Feature 8: Session Timeout (15 minutes = 900 seconds)
define('SESSION_TIMEOUT_SECONDS', 900);

/**
 * Blocks guests (not logged in) from viewing the current page.
 * Also destroys the session and redirects if it has timed out.
 *
 * @param string $login_page Relative path to login.php from the calling file
 */
function require_login($login_page) {
    // Not logged in at all -> send to login page
    if (!isset($_SESSION['username'])) {
        header("Location: " . $login_page . "?msg=login_required");
        exit();
    }

    // Logged in, but check inactivity timeout
    if (isset($_SESSION['last_activity']) &&
        (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT_SECONDS) {

        $_SESSION = array();
        session_unset();
        session_destroy();

        header("Location: " . $login_page . "?msg=timeout");
        exit();
    }

    // Still active -> refresh the activity timestamp
    $_SESSION['last_activity'] = time();
}
