<?php
/*
 * Group      : Group #9
 * Date Created    : September 16, 2026
 * Problem Description:
 *   Entry point of the Travel Package Booking System. Sends guests to
 *   the login page, and sends already-logged-in users straight to the
 *   dashboard that matches their role (admin or regular user).
 */

require_once 'includes/session_check.php';

if (isset($_SESSION['username'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
} else {
    header("Location: login.php");
    exit();
}
