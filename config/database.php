<?php

$serverName = 'localhost';
$username = 'root';
$password = '';
$dbName = 'kasir';

$conn = mysqli_connect($serverName, $username, $password, $dbName);

if (!$conn) {
    die('Connection Failed: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
