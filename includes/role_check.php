<?php
/*
 * Programmer      : Sydney Allison Magdaluyo
 * Date Created    : September 16, 2026
 * Problem Description:
 *   Implements role-based access control (RBAC). A page calls
 *   require_role() with the list of roles allowed to view it; anyone
 *   logged in with a different role is redirected to the Access Denied
 *   page. This protects the actual PHP page itself, not just the menu
 *   buttons/links shown in the UI.
 */

/**
 * Blocks any logged-in user whose role is not in $allowed_roles.
 *
 * @param array  $allowed_roles Roles permitted on this page, e.g. ['admin']
 * @param string $denied_page   Relative path to access_denied.php
 */
function require_role($allowed_roles, $denied_page) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles, true)) {
        header("Location: " . $denied_page);
        exit();
    }
}
