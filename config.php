<?php
/* config.php
 * This file contains configuration settings for the application.
 */

// Set secure session cookie parameters
session_set_cookie_params([
    'lifetime' => 0,          // Session cookie lasts until the browser is closed
    'path'     => '/',
    'domain'   => '',         // Default to the current domain
    'secure'   => true,       // Ensure cookie is sent over HTTPS only (enable in production)
    'httponly' => true,       // Prevent JavaScript access to the session cookie
    'samesite' => 'Strict'    // Helps protect against CSRF
]);

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
