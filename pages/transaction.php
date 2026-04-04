<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../includes/functions.php');
require_login();

if (isset($_POST['checkout'])) {
    $bayar = $_POST['bayar'];
    $result = mysqli_query($conn, "SELECT * FROM transaksi");
    $no = mysqli_num_rows($result);
    checkout($conn, $bayar, $no);
}


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
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f3f6;
            margin: 0;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 20px auto;
        }

        .grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }

        /* Produk */
        .produk-card {
            background: #fff;
            padding: 16px;
            margin-bottom: 12px;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .produk-info {
            display: flex;
            flex-direction: column;
        }

        .produk-info b {
            font-size: 16px;
        }

        .produk-info span {
            color: #555;
            font-size: 14px;
        }

        /* Cart */
        .cart {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .cart h2 {
            margin-top: 0;
        }

        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        /* Input */
        input[type="number"] {
            width: 60px;
            padding: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        /* Button */
        button {
            border: none;
            padding: 7px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
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

        .checkout {
            background: #2c3e50;
            color: white;
            width: 100%;
            margin-top: 10px;
            padding: 10px;
        }

        button:hover {
            opacity: 0.9;
        }

        /* Total */
        .total {
            margin-top: 15px;
            font-weight: bold;
            font-size: 18px;
        }

        /* Kembalian */
        .kembalian {
            background: #dff9fb;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <?php include(__DIR__ . '/../includes/header.php'); ?>

        <div class="grid">

            <div>
                <?php
                $kembalian = get_flash('kembalian');
                if ($kembalian) {
                    echo "<div class='kembalian'>Kembalian: " . format_rupiah($kembalian) . "</div>";
                }
                ?>
                <h2>Daftar Produk</h2>

                <?php while ($row = mysqli_fetch_assoc($produk)) { ?>
                    <div class="produk-card">
                        <div class="produk-info">
                            <b><?= $row['nama'] ?></b>
                            <span>Rp <?= number_format($row['harga']) ?></span>
                        </div>

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

                <form action="" method="POST">
                    <input type="number" name="bayar">
                    <button type="submit" name="checkout" class="checkout">Checkout</button>
                </form>
            </div>

        </div>
    </div>

</body>

</html>