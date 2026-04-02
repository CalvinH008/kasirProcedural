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

function getCurrentUser($conn){
    if(!isset($_SESSION['user_id'])){
        return null;
    }

    $id = $_SESSION['user_id'];
    $query = "SELECT username FROM users WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    return $row['username'];
}

function checkout($conn, $bayar){
    if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])){
        return 'cart kosong';
    }

    $cart = $_SESSION['cart'];

    $total = 0;
    foreach($cart as $item){
        $total += $item['harga'] * $item['quantity'];
    }

    if($bayar < 0){
        return "uang kurang";
    }

    $kembalian = $bayar - $total;

    $user_id = $_SESSION['user_id'];

    $kode = "TRX" . time();

    mysqli_query($conn, "INSERT INTO transaksi (user_id, kode_transaksi, total, bayar, kembalian, created_at) VALUES ($user_id, $kode, $total, $bayar, $kembalian, NOW())");

    $transaksi_id = mysqli_insert_id($conn);

    foreach($cart as $item){
        $produk_id = $item['id'];
        $quantity = $item['quantity'];
        $harga = $item['harga'];
        $subtotal = $quantity * $harga;
    }

    mysqli_query($conn, "INSERT INTO detail_transaksi(transaksi_id, produk_id, quantity, harga_satuan, subtotal) VALUES ($transaksi_id, $produk_id, $quantity, $harga, $subtotal)");

    unset($_SESSION['cart']);
    return "Checkout berhasil | Kembalian: " . $kembalian;
}
