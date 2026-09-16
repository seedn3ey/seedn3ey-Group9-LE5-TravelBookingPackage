System Name: Travel Package Booking System

Members:
- [Add group member names here]

Admin Account:
Username: admin01
Password: Admin@123

Regular User Account:
Username: user01
Password: User@123

Implemented Security Features:
1. Login validation (required fields, generic "Invalid username or password" message)
2. PHP sessions ($_SESSION["username"], $_SESSION["role"], $_SESSION["fullname"])
3. Restricted pages - admin/ and user/ pages check the session directly, not just hidden links
4. Role-based access control (admin vs. regular user vs. guest)
5. Cookie - "Remember my username" (username only, password never stored in a cookie)
6. Form validation - required fields, email format, contact number format, number range
   (travelers 1-10), minimum length (passenger name), date validation (no past travel dates)
7. Logout - destroys the session and expires the session cookie
8. Session timeout - 15 minutes of inactivity, then redirected to login with a message
9. Unauthorized access page (access_denied.php) for role violations

Notes:
- Passwords/data are dummy values for classroom testing only, not real credentials.
- Data source is XML (xml/users.xml, xml/packages.xml, xml/bookings.xml) as required
  by the lab (PHP's SimpleXML / DOM extensions, both built in - no extra setup needed).
- To run: place the GROUP_TravelBookingSystem folder inside your PHP server's web root
  (e.g. XAMPP's htdocs), start Apache, and open http://localhost/GROUP_TravelBookingSystem/
- The xml/ folder must remain writable by PHP so bookings and new packages can be saved.
