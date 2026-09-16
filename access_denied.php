<?php
/*
 * Programmer      : Sydney Allison Magdaluyo
 * Date Created    : September 16, 2026
 * Problem Description:
 *   Security Feature 9. Shown whenever a logged-in user tries to open
 *   a page their role does not permit (e.g. a regular user typing the
 *   admin dashboard URL directly into the address bar).
 */

require_once 'includes/session_check.php';

// Decide where "Back to Dashboard" should point, based on role
$dashboard_link = 'login.php';
if (isset($_SESSION['role'])) {
    $dashboard_link = ($_SESSION['role'] === 'admin') ? 'admin/dashboard.php' : 'user/dashboard.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access Denied</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>ACCESS DENIED</h1>
        <div class="error-box">
            You do not have permission to access this page.
        </div>
        <a class="btn" href="<?= htmlspecialchars($dashboard_link) ?>">Back to Dashboard</a>
    </div>
</body>
</html>
