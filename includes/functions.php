<?php
require_once(__DIR__ . '/../config/database.php');

function format_rupiah($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}

function redirect($url)
{
    header("Location: /sistemKasir/" . $url);
    exit;
}

function sanitize($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES);
}

function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function generate_kode_transaksi($no)
{
    $tanggal = date('Ymd');
    $urutan = str_pad($no, 3, '0', STR_PAD_LEFT);
    return "TRX-$tanggal-$urutan";
}

function hash_password($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash)
{
    return password_verify($password, $hash);
}

function add_to_cart($produk_id, $quantity, $nama, $harga)
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$produk_id])) {
        $_SESSION['cart'][$produk_id]['quantity'] += $quantity;
        $_SESSION['cart'][$produk_id]['subtotal'] = $_SESSION['cart'][$produk_id]['quantity'] * $_SESSION['cart'][$produk_id]['harga'];
    } else {
        $_SESSION['cart'][$produk_id] = [
            'quantity' => $quantity,
            'nama' => $nama,
            'harga' => $harga
        ];
        $_SESSION['cart'][$produk_id]['subtotal'] = $_SESSION['cart'][$produk_id]['quantity'] * $_SESSION['cart'][$produk_id]['harga'];
    }
}

function remove_from_cart($produk_id)
{
    if (isset($_SESSION['cart'][$produk_id])) {
        unset($_SESSION['cart'][$produk_id]);
    }
}

function get_cart()
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    return $_SESSION['cart'];
}

function get_cart_total()
{
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['subtotal'];
        }
    }
    return $total;
}

function clear_cart()
{
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
}

function update_qty_cart($produk_id, $quantity)
{
    if (isset($_SESSION['cart'][$produk_id])) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$produk_id]);
        } else {
            $_SESSION['cart'][$produk_id]['quantity'] = $quantity;
        }
    }
}
