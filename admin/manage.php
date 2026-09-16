<?php
/*
 * Group      : Group #9
 * Date Created    : September 16, 2026
 * Problem Description:
 *   Admin-only page for managing the system's core data: adding or
 *   deleting travel packages, and viewing every booking submitted by
 *   regular users. Restricted the same way as dashboard.php.
 */

require_once '../includes/session_check.php';
require_once '../includes/role_check.php';
require_once '../includes/security.php';

require_login('../login.php');
require_role(['admin'], '../access_denied.php');

$packages_file = __DIR__ . '/../xml/packages.xml';
$errors = [];
$success = '';

// ---- Handle: delete a package ----
if (isset($_GET['delete'])) {
    $delete_id = clean_input($_GET['delete']);
    $packages = simplexml_load_file($packages_file);

    $dom = dom_import_simplexml($packages)->ownerDocument;
    foreach ($packages->package as $pkg) {
        if ((string) $pkg->id === $delete_id) {
            $node = dom_import_simplexml($pkg);
            $node->parentNode->removeChild($node);
            break;
        }
    }
    $dom->save($packages_file);
    $success = "Package removed.";
}

// ---- Handle: add a new package ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_package'])) {
    $name        = clean_input($_POST['name'] ?? '');
    $destination = clean_input($_POST['destination'] ?? '');
    $price       = clean_input($_POST['price'] ?? '');
    $duration    = clean_input($_POST['duration'] ?? '');

    // ---- Form validation ----
    if (!is_required($name))        $errors[] = "Package name is required.";
    if (!is_required($destination)) $errors[] = "Destination is required.";
    if (!is_required($duration))    $errors[] = "Duration is required.";
    if (!is_numeric($price) || (float) $price <= 0) {
        $errors[] = "Price must be a valid positive number.";
    }

    if (empty($errors)) {
        $packages = simplexml_load_file($packages_file);

        // Generate the next numeric id
        $max_id = 0;
        foreach ($packages->package as $pkg) {
            $max_id = max($max_id, (int) $pkg->id);
        }
        $new_id = $max_id + 1;

        $new_package = $packages->addChild('package');
        $new_package->addChild('id', $new_id);
        $new_package->addChild('name', htmlspecialchars($name));
        $new_package->addChild('destination', htmlspecialchars($destination));
        $new_package->addChild('price', htmlspecialchars($price));
        $new_package->addChild('duration', htmlspecialchars($duration));

        $packages->asXML($packages_file);
        $success = "Travel package added successfully.";
    }
}

$packages = simplexml_load_file($packages_file);
$bookings = simplexml_load_file(__DIR__ . '/../xml/bookings.xml');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Packages - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* CSS to handle the Peso sign inside the input box */
        .currency-wrap {
            position: relative;
            display: inline-block; /* Or block, depending on your style.css layout */
        }
        .currency-symbol {
            position: absolute;
            left: 10px;          
            top: 50%;
            transform: translateY(-50%);
            color: #555;
            pointer-events: none; 
        }
        #price {
            padding-left: 28px;  
        }
    </style>
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
        <h1>Manage Travel Packages</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul style="margin:0; padding-left:18px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-box"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <h2>Add New Package</h2>
        <form method="POST" action="manage.php">
            <label for="name">Package Name</label>
            <input type="text" id="name" name="name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">

            <label for="destination">Destination</label>
            <input type="text" id="destination" name="destination" value="<?= isset($_POST['destination']) ? htmlspecialchars($_POST['destination']) : '' ?>">

            <label for="price">Price (PHP)</label>
            <div class="currency-wrap">
                <span class="currency-symbol">₱</span>
                <input 
                    type="number" 
                    id="price" 
                    name="price" 
                    min="1" 
                    step="1" 
                    pattern="\d*"
                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                    value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '' ?>"
                >
            </div>

            <label for="duration">Duration</label>
            <input type="text" id="duration" name="duration" placeholder="e.g. 3 Days / 2 Nights" value="<?= isset($_POST['duration']) ? htmlspecialchars($_POST['duration']) : '' ?>">

            <button type="submit" name="add_package">Add Package</button>
        </form>

        <h2>Current Packages</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Destination</th>
                <th>Price</th>
                <th>Duration</th>
                <th></th>
            </tr>
            <?php foreach ($packages->package as $pkg): ?>
            <tr>
                <td><?= htmlspecialchars((string) $pkg->id) ?></td>
                <td><?= htmlspecialchars((string) $pkg->name) ?></td>
                <td><?= htmlspecialchars((string) $pkg->destination) ?></td>
                <td>PHP <?= htmlspecialchars((string) $pkg->price) ?></td>
                <td><?= htmlspecialchars((string) $pkg->duration) ?></td>
                <td><a href="manage.php?delete=<?= urlencode((string) $pkg->id) ?>" onclick="return confirm('Delete this package?');">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>All Bookings (System Records)</h2>
        <table>
            <tr>
                <th>Passenger</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Package</th>
                <th>Travel Date</th>
                <th>End Date</th> <!-- Newly Added Header -->
                <th>Travelers</th>
                <th>Booked By</th>
            </tr>
            <?php foreach ($bookings->booking as $b): ?>
            <tr>
                <td><?= htmlspecialchars((string) $b->passenger_name) ?></td>
                <td><?= htmlspecialchars((string) $b->email) ?></td>
                <td><?= htmlspecialchars((string) $b->contact_number) ?></td>
                <td><?= htmlspecialchars((string) $b->package_name) ?></td>
                <td><?= htmlspecialchars((string) $b->travel_date) ?></td>
                <!-- Added End Date Output matching your user-side logic -->
                <td><?= htmlspecialchars((string) ($b->end_date ?? 'N/A')) ?></td>
                <td><?= htmlspecialchars((string) $b->travelers) ?></td>
                <td><?= htmlspecialchars((string) $b->username) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>