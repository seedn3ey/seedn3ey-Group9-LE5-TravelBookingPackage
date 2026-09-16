<?php
/*
 * Programmer      : Sydney Allison Magdaluyo
 * Date Created    : September 16, 2026
 * Problem Description:
 *   Shows only the bookings that belong to the currently logged-in
 *   user (never other users' transactions), pulled from xml/bookings.xml,
 *   including calculated travel end dates.
 */

require_once '../includes/session_check.php';
require_once '../includes/role_check.php';

require_login('../login.php');
require_role(['user'], '../access_denied.php');

$bookings = simplexml_load_file(__DIR__ . '/../xml/bookings.xml');
$my_bookings = [];
foreach ($bookings->booking as $b) {
    if ((string) $b->username === $_SESSION['username']) {
        $my_bookings[] = $b;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Transactions</title>
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
        <h1>My Transactions</h1>

        <?php if (empty($my_bookings)): ?>
            <p>You have no bookings yet. <a href="booking.php">Book a travel package</a>.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Package</th>
                    <th>Travel Date</th>
                    <th>End Date</th>
                    <th>Travelers</th>
                    <th>Booked On</th>
                </tr>
                <?php foreach ($my_bookings as $b): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $b->package_name) ?></td>
                    <td><?= htmlspecialchars((string) $b->travel_date) ?></td>
                    <td><?= htmlspecialchars((string) ($b->end_date ?? 'N/A')) ?></td>
                    <td><?= htmlspecialchars((string) $b->travelers) ?></td>
                    <td><?= htmlspecialchars((string) $b->booked_on) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>