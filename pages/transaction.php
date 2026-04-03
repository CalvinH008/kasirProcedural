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
    <title>Kasir</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            margin: 0;
        }

        .container {
            width: 90%;
            margin: 20px auto;
        }

        .grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .produk-card {
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .produk-card p {
            margin: 5px 0;
        }

        .cart {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        input[type="number"] {
            width: 60px;
            padding: 5px;
        }

        button {
            border: none;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
        }

        .add {
            background: #2ecc71;
            color: white;
        }

        .update {
            background: #3498db;
            color: white;
        }

        .remove {
            background: #e74c3c;
            color: white;
        }

        button:hover {
            opacity: 0.9;
        }

        .total {
            margin-top: 15px;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>

<body>

    <div class="container">
        <?php include(__DIR__ . '/../includes/header.php'); ?>

        <div class="grid">

            <div>
                <h2>Daftar Produk</h2>

                <?php while ($row = mysqli_fetch_assoc($produk)) { ?>
                    <div class="produk-card">
                        <p><b><?= $row['nama'] ?></b></p>
                        <p>Rp <?= number_format($row['harga']) ?></p>

                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="nama" value="<?= $row['nama'] ?>">
                            <input type="hidden" name="harga" value="<?= $row['harga'] ?>">

                            <input type="number" name="quantity" value="1" min="1">

                            <button type="submit" name="add" class="add">
                                Add
                            </button>
                        </form>
                    </div>
                <?php } ?>
            </div>

            <div class="cart">
                <h2>Keranjang</h2>

                <?php
                $total = 0;
                foreach ($cart as $produk_id => $item) {
                    $total += $item['subtotal'];
                ?>
                    <div class="cart-item">
                        <p><?= $item['nama'] ?></p>
                        <p>Rp <?= number_format($item['harga']) ?></p>

                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="produk_id" value="<?= $produk_id ?>">
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>">

                            <button name="update" class="update">Update</button>
                        </form>

                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="produk_id" value="<?= $produk_id ?>">

                            <button name="remove" class="remove">X</button>
                        </form>
                    </div>
                <?php } ?>

                <div class="total">
                    Total: Rp <?= number_format($total) ?>
                </div>

            </div>

        </div>
    </div>

</body>

</html>