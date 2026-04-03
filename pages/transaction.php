<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../includes/functions.php');
require_login();

$cart = get_cart();

$produk = mysqli_query($conn, "SELECT * FROM produk WHERE is_active = 1");

if (isset($_POST['add'])) {
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];
    $name = $_POST['nama'];
    $harga = $_POST['harga'];
    add_to_cart($id, $quantity, $name, $harga);
    redirect('pages/transaction.php');
}

if (isset($_POST['update'])) {
    $id = $_POST['produk_id'];
    $quantity = $_POST['quantity'];
    update_qty_cart($id, $quantity);
    redirect('pages/transaction.php');
}

if (isset($_POST['remove'])) {
    $id = $_POST['produk_id'];
    remove_from_cart($id);
    redirect('pages/transaction.php');
}

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
        <?php include(__DIR__ . '/../includes/header.php'); ?>
        <h2>DAFTAR PRODUK</h2>
        <?php while ($row = mysqli_fetch_assoc($produk)) {
        ?>
            <div style="border:1px solid #ccc; padding:10px; margin:10px;">
                <p><?= $row['nama'] ?></p>
                <p><?= $row['harga'] ?></p>
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="nama" value="<?= $row['nama'] ?>">
                    <input type="hidden" name="harga" value="<?= $row['harga'] ?>">
                    <button type="submit" name="add">Add to cart</button>
                </form>
            </div>
        <?php
        }
        ?>

        <input type="search" placeholder="cari produk">

        <?php foreach ($cart as $produk_id => $item) {
            echo $item['nama'];
            echo $item['quantity'];
            echo $item['subtotal'];
        ?>
            <form action="" method="POST">
                <input type="hidden" name="produk_id" value="<?= $produk_id ?>">
                <input type="hidden" name="quantity" value="<?= $item['quantity'] ?>">
                <button type="submit" name="update">Update Qty</button>
            </form>

            <form action="" method="POST">
                <input type="hidden" name="produk_id" value="<?= $produk_id ?>">
                <button type="submit" name="remove">Remove Item</button>
            </form>
        <?php
        }
        ?>


    </div>

</body>

</html>