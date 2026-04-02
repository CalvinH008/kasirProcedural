<?php 
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../includes/functions.php');
require_login();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="container">
        <?php include (__DIR__ . '/../includes/header.php'); ?>
        <form action="" method="POST">
            <h2>DAFTAR PRODUK</h2>
            <input type="search" placeholder="cari produk">

            <a href="">Add to cart</a>
        </form>
    </div>
    
</body>
</html>