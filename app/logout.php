<?php

require_once ('../includes/bootstrap.php');
$pageTitle = 'Logout';
// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
unset($_SESSION);
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
require_once ('../includes/header.php');
require_once ('../src/loginForm.php');
require_once ('../includes/footer.php');