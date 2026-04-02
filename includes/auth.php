<?php
session_start();
require_once(__DIR__ . '/../config/database.php');

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function require_login()
{
    if (!is_logged_in()) {
        redirect('pages/login.php');
    }
}

function get_logged_in_user()
{
    return [
       'user_id' => $_SESSION['user_id'] ?? null,
       'username' => $_SESSION['username'] ?? null,
       'role' => $_SESSION['role'] ?? null
    ];
}

function is_admin()
{
    $user = get_logged_in_user();
    return $user['role'] === 'admin'; 
}

function require_admin()
{
    if (!is_admin()) {
        redirect('index.php');
    }
}

function login($user)
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
}

function logout()
{
    $_SESSION = [];
    session_destroy();
    redirect("pages/login.php");
}
