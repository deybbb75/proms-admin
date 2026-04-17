<?php
include 'includes/init.php';

// Unset all of the session variables.
$_SESSION = [];

// Delete the session cookie with the same params.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']);
}
// Finally, destroy the session.
session_destroy();

safe_redirect('login.php');
exit;
?>