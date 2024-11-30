<?php
session_start();

function is_logged_in() {
    return isset($_SESSION['username']);
}

function redirect_if_not_logged_in($redirect_url = "login.php") {
    if (!is_logged_in()) {
        header("Location: $redirect_url");
        exit;
    }
}
?>
