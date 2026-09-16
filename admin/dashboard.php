<?php
/*
 * Group      : Group #9
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

$packages = simplexml_load_file(__DIR__ . '/../xml/packages.xml');
$bookings = simplexml_load_file(__DIR__ . '/../xml/bookings.xml');
$package_count = count($packages->package);
$booking_count = count($bookings->booking);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Aurelia</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="custom-navbar">
        <div class="nav-brand">
            <img src="../css/LOGO.png" alt="Aurelia Logo" class="nav-logo">
            <span class="nav-title">AURELIA</span>
        </div>
        <div class="nav-links">
            <a href="dashboard.php">dashboard</a>
            <a href="manage.php">manage packages</a>
            <a href="../logout.php">logout</a>
        </div>
    </nav>

    <div class="app-wrapper">

        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="margin-bottom: 5px; color: #16124c;">Welcome, <?= htmlspecialchars($_SESSION['fullname']) ?>!</h2>
            <p style="color: #666; margin-top: 0;">Role: Administrator</p>
        </div>

        <div style="text-align: center; margin-bottom: 30px;">
            <p>Total travel packages: <strong><?= $package_count ?></strong></p>
            <p>Total bookings received: <strong><?= $booking_count ?></strong></p>
        </div>

        <div class="action-grid">
            <a class="btn" href="manage.php">Manage Travel Packages</a>
        </div>
    </div>
</body>
</html>