<?php
/* logout.php
 * This file logs the user out by 
 * destroying the session and redirecting to the login page.
 */
require_once 'config.php';

// Clear all session data
$_SESSION = [];

// Destroy session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
              $params["path"],
              $params["domain"],
              $params["secure"],
              $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: index.php");
exit;
?>
