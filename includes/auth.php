<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function login($user_id) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['last_activity'] = time();
}

function logout() {
    session_unset();
    session_destroy();
}
?>