<?php
/*
 * Group      : Group #9
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
    <title>User Dashboard - Aurelia</title>
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
            <a href="booking.php">book</a>
            <a href="transactions.php">transactions</a>
            <a href="../logout.php">logout</a>
        </div>
    </nav>

    <div class="app-wrapper">
        
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="margin-bottom: 5px; color: #16124c;">Welcome, <?= htmlspecialchars($_SESSION['fullname']) ?>!</h2>
            <p style="color: #666; margin-top: 0;">Role: Regular User</p>
        </div>

        <div class="action-grid">
            <a class="btn" href="booking.php">Book a Travel Package</a>
            <a class="btn" href="transactions.php">View My Transactions</a>
        </div>
    </div>
</body>
</html>
