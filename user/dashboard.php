<?php
/*
 * Programmer      : Sydney Allison Magdaluyo
 * Date Created    : September 16, 2026
 * Problem Description:
 *   Regular user dashboard. Guests are sent to login; the page itself
 *   checks the session and role rather than relying on hidden links.
 */

require_once '../includes/session_check.php';
require_once '../includes/role_check.php';

require_login('../login.php');
require_role(['user', 'admin'], '../access_denied.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="navbar">
        <span>Travel Package Booking System</span>
        <span>
            <a href="dashboard.php">Dashboard</a>
            <a href="booking.php">Book a Package</a>
            <a href="transactions.php">My Transactions</a>
            <a href="../logout.php">Logout</a>
        </span>
    </div>

    <div class="container">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['fullname']) ?>!</h1>
        <p>Role: Regular User</p>

        <div class="dashboard-links">
            <a class="btn" href="booking.php">Book a Travel Package</a>
            <a class="btn" href="transactions.php">View My Transactions</a>
        </div>
    </div>
</body>
</html>
