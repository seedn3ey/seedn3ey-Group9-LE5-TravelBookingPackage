<?php
/*
 * Group      : Group #9
 * Date Created    : September 16, 2026
 * Problem Description:
 *   Reusable helper functions for input sanitization and validation,
 *   used across the login page and the travel booking form. Keeping
 *   these in one file avoids duplicating validation logic in every page.
 */

// Removes extra whitespace and encodes special characters to help
// prevent stored/reflected XSS when the value is later echoed back.
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Basic required-field check (after trimming)
function is_required($value) {
    return trim($value) !== '';
}

// Minimum length check, e.g. is_min_length($name, 5)
function is_min_length($value, $min) {
    return mb_strlen(trim($value)) >= $min;
}

// Standard email format check
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Philippine-style contact number: digits only, 7 to 15 characters
function is_valid_contact_number($number) {
    return (bool) preg_match('/^[0-9]{7,15}$/', trim($number));
}

// Whole number within an inclusive range, e.g. travelers between 1 and 10
function is_number_in_range($value, $min, $max) {
    if (!is_numeric($value)) {
        return false;
    }
    $intValue = (int) $value;
    return $intValue >= $min && $intValue <= $max;
}

// Date must not be earlier than today (no past travel dates)
function is_not_past_date($dateString) {
    $today = strtotime(date('Y-m-d'));
    $submitted = strtotime($dateString);
    if ($submitted === false) {
        return false;
    }
    return $submitted >= $today;
}
