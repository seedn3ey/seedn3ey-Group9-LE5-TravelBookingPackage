<?php
/*
 * Programmer      : Sydney Allison Magdaluyo
 * Date Created    : September 16, 2026
 * Problem Description:
 *   Administrator dashboard. Security Feature 3 (restricted page): a
 *   guest is sent to login, and a logged-in non-admin is sent to
 *   Access Denied even if they type this URL directly.
 */

require_once '../includes/session_check.php';
require_once '../includes/role_check.php';

require_login('../login.php');
require_role(['admin'], '../access_denied.php');

// Quick stats for the dashboard
$packages = simplexml_load_file(__DIR__ . '/../xml/packages.xml');
$bookings = simplexml_load_file(__DIR__ . '/../xml/bookings.xml');
$package_count = count($packages->package);
$booking_count = count($bookings->booking);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="navbar">
        <span>Travel Package Booking System &mdash; Admin</span>
        <span>
            <a href="dashboard.php">Dashboard</a>
            <a href="manage.php">Manage Packages</a>
            <a href="../logout.php">Logout</a>
        </span>
    </div>

    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['fullname']) ?>!</p>
        <p>Role: Administrator</p>

        <div class="dashboard-links">
            <p>Total travel packages: <strong><?= $package_count ?></strong></p>
            <p>Total bookings received: <strong><?= $booking_count ?></strong></p>
            <a class="btn" href="manage.php">Manage Travel Packages</a>
        </div>
    </div>
</body>
</html>
