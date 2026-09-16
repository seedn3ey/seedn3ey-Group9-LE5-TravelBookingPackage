<?php
/*
 * Programmer      : Sydney Allison Magdaluyo
 * Date Created    : September 16, 2026
 * Problem Description:
 *   Main system form of the Travel Package Booking System. A logged-in
 *   regular user selects a travel package and submits their booking
 *   details. Demonstrates required-field, email, number-range, length,
 *   and date validation, then stores the booking in xml/bookings.xml
 *   and shows a summary confirmation.
 */

require_once '../includes/session_check.php';
require_once '../includes/role_check.php';
require_once '../includes/security.php';

require_login('../login.php');
require_role(['user'], '../access_denied.php');

$packages_file = __DIR__ . '/../xml/packages.xml';
$bookings_file = __DIR__ . '/../xml/bookings.xml';
$packages = simplexml_load_file($packages_file);

$errors = [];
$booking_summary = null; // holds data to display after a successful submission

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passenger_LastName = clean_input($_POST['passenger_LastName'] ?? '');
    $passenger_FirstName = clean_input($_POST['passenger_FirstName'] ?? '');
    $passenger_MiddleName = clean_input($_POST['passenger_MiddleName'] ?? '');

    $email          = clean_input($_POST['email'] ?? '');
    $contact_number = clean_input($_POST['contact_number'] ?? '');
    $package_id     = clean_input($_POST['package_id'] ?? '');
    $travel_date    = clean_input($_POST['travel_date'] ?? '');
    $travelers      = clean_input($_POST['travelers'] ?? '');

    // ---- Required fields ----
    if (!is_required($passenger_LastName)) {
        $errors[] = "Passenger Last name is required.";
    } elseif (!is_min_length($passenger_LastName, 5)) {
        // ---- Length validation ----
        $errors[] = "Passenger Last name must contain at least 5 characters.";
    }

    if (!is_required($passenger_FirstName)) {
        $errors[] = "Passenger First name is required.";
    } elseif (!is_min_length($passenger_FirstName, 5)) {
        // ---- Length validation ----
        $errors[] = "Passenger First name must contain at least 5 characters.";
    }

    if (!is_required($passenger_MiddleName)) {
        $errors[] = "Passenger Middle name is required.";
    } elseif (!is_min_length($passenger_MiddleName, 5)) {
        // ---- Length validation ----
        $errors[] = "Passenger Middle name must contain at least 5 characters.";
    }

    // ---- Email validation ----
    if (!is_required($email)) {
        $errors[] = "Email is required.";
    } elseif (!is_valid_email($email)) {
        $errors[] = "Invalid email address.";
    }

    // ---- Contact number ----
    if (!is_required($contact_number)) {
        $errors[] = "Contact number is required.";
    } elseif (!is_valid_contact_number($contact_number)) {
        $errors[] = "Contact number must contain 7 to 15 digits only.";
    }

    // ---- Package selection ----
    $selected_package = null;
    if (!is_required($package_id)) {
        $errors[] = "Please select a travel package.";
    } else {
        foreach ($packages->package as $pkg) {
            if ((string) $pkg->id === $package_id) {
                $selected_package = $pkg;
                break;
            }
        }
        if ($selected_package === null) {
            $errors[] = "Selected travel package is not valid.";
        }
    }

    // ---- Date validation ----
    if (!is_required($travel_date)) {
        $errors[] = "Travel date is required.";
    } elseif (!is_not_past_date($travel_date)) {
        $errors[] = "Travel date cannot be in the past.";
    }

    // ---- Number validation ----
    if (!is_required($travelers)) {
        $errors[] = "Number of travelers is required.";
    } elseif (!is_number_in_range($travelers, 1, 10)) {
        $errors[] = "Number of travelers must be between 1 and 10.";
    }

    if (empty($errors) && $selected_package !== null) {
        // Save booking to xml/bookings.xml
        $bookings = simplexml_load_file($bookings_file);
        $new_booking = $bookings->addChild('booking');
        $new_booking->addChild('username', htmlspecialchars($_SESSION['username']));

        $new_booking->addChild('passenger_LastName', htmlspecialchars($passenger_LastName));
        $new_booking->addChild('passenger_FirstName', htmlspecialchars($passenger_FirstName));
        $new_booking->addChild('passenger_MiddleName', htmlspecialchars($passenger_MiddleName));

        $new_booking->addChild('email', htmlspecialchars($email));
        $new_booking->addChild('contact_number', htmlspecialchars($contact_number));
        $new_booking->addChild('package_name', htmlspecialchars((string) $selected_package->name));
        $new_booking->addChild('travel_date', htmlspecialchars($travel_date));
        $new_booking->addChild('travelers', htmlspecialchars($travelers));
        $new_booking->addChild('booked_on', date('Y-m-d H:i:s'));
        $bookings->asXML($bookings_file);

        // Build summary to display below the form
        $booking_summary = [
            'passenger_FirstName' => $passenger_FirstName,
            'passenger_MiddleName' => $passenger_MiddleName,
            'passenger_LastName' => $passenger_LastName,
            'email'          => $email,
            'contact_number' => $contact_number,
            'package_name'   => (string) $selected_package->name,
            'destination'    => (string) $selected_package->destination,
            'price'          => (string) $selected_package->price,
            'travel_date'    => $travel_date,
            'travelers'      => $travelers,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book a Travel Package</title>
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
        <h1>Book a Travel Package</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul style="margin:0; padding-left:18px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($booking_summary): ?>
            <!-- Summary Details Page (shown after a successful booking) -->
            <div class="success-box">Booking confirmed! Here is your summary:</div>
            <table>
                <tr><th>Passenger Last Name</th><td><?= htmlspecialchars($booking_summary['passenger_LastName']) ?></td></tr>
                <tr><th>Passenger First Name</th><td><?= htmlspecialchars($booking_summary['passenger_FirstName']) ?></td></tr>
                <tr><th>Passenger Middle Name</th><td><?= htmlspecialchars($booking_summary['passenger_MiddleName']) ?></td></tr>

                <tr><th>Email</th><td><?= htmlspecialchars($booking_summary['email']) ?></td></tr>
                <tr><th>Contact Number</th><td><?= htmlspecialchars($booking_summary['contact_number']) ?></td></tr>
                <tr><th>Package</th><td><?= htmlspecialchars($booking_summary['package_name']) ?></td></tr>
                <tr><th>Destination</th><td><?= htmlspecialchars($booking_summary['destination']) ?></td></tr>
                <tr><th>Price</th><td>PHP <?= htmlspecialchars($booking_summary['price']) ?></td></tr>
                <tr><th>Travel Date</th><td><?= htmlspecialchars($booking_summary['travel_date']) ?></td></tr>
                <tr><th>Number of Travelers</th><td><?= htmlspecialchars($booking_summary['travelers']) ?></td></tr>
            </table>
            <a class="btn" href="booking.php">Book Another Package</a>
            <a class="btn" href="transactions.php">View My Transactions</a>
        <?php else: ?>
            <!-- Booking form -->
            <form method="POST" action="booking.php">
                <label for="passenger_LastName">Passenger Last Name</label>
                <input type="text" id="passenger_LastName" name="passenger_LastName" value="<?= isset($_POST['passenger_LastName']) ? htmlspecialchars($_POST['passenger_LastName']) : '' ?>">

                <label for="passenger_MiddleName">Passenger Middle Name</label>
                <input type="text" id="passenger_MiddleName" name="passenger_MiddleName" value="<?= isset($_POST['passenger_MiddleName']) ? htmlspecialchars($_POST['passenger_MiddleName']) : '' ?>">

                <label for="passenger_FirstName">Passenger First Name</label>
                <input type="text" id="passenger_FirstName" name="passenger_FirstName" value="<?= isset($_POST['passenger_FirstName']) ? htmlspecialchars($_POST['passenger_FirstName']) : '' ?>">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">

                <label for="contact_number">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" placeholder="e.g. 09171234567" value="<?= isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : '' ?>">

                <label for="package_id">Travel Package</label>
                <select id="package_id" name="package_id">
                    <option value="">-- Select a package --</option>
                    <?php foreach ($packages->package as $pkg): ?>
                        <option value="<?= htmlspecialchars((string) $pkg->id) ?>"
                            <?= (isset($_POST['package_id']) && $_POST['package_id'] === (string) $pkg->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $pkg->name) ?> - <?= htmlspecialchars((string) $pkg->destination) ?> (PHP <?= htmlspecialchars((string) $pkg->price) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="travel_date">Travel Date</label>
                <input type="date" id="travel_date" name="travel_date" value="<?= isset($_POST['travel_date']) ? htmlspecialchars($_POST['travel_date']) : '' ?>">

                <label for="travelers">Number of Travelers</label>
                <input type="number" id="travelers" name="travelers" min="1" max="10" value="<?= isset($_POST['travelers']) ? htmlspecialchars($_POST['travelers']) : '' ?>">

                <button type="submit">Submit Booking</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
