<?php
session_start();

// Destroy session data
session_unset();
session_destroy();

// Optional: Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login page (adjust path if needed)
header("Location: ../index.php");
exit;
?>
