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
            $harga = $_SESSION['cart'][$produk_id]['harga'];
            $_SESSION['cart'][$produk_id]['subtotal'] = $harga * $quantity;
        }
    }
}

function getCurrentUser($conn)
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $id = $_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "SELECT username FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt); 
    $row = mysqli_fetch_assoc($result);

    return $row['username'];
}

function checkout($conn, $bayar, $no)
{
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 'cart kosong';
    }

    $cart = $_SESSION['cart'];

    $total = 0;
    foreach ($cart as $item) {
        $total += $item['harga'] * $item['quantity'];
    }

    if ($bayar < $total) {
        return 'uang anda kurang';
    }

    $kembalian = $bayar - $total;

    $user_id = $_SESSION['user_id'];

    $kode = generate_kode_transaksi($no);

    mysqli_begin_transaction($conn);
    try {
        $stmt1 = mysqli_prepare($conn, "INSERT INTO transaksi(user_id, kode_transaksi, total, bayar, kembalian, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmt1, "isiii", $user_id, $kode, $total, $bayar, $kembalian);
        $result = mysqli_stmt_execute($stmt1);

        if (!$result) {
            throw new Exception("gagal insert transaksi");
        }

        $transaksi_id = mysqli_insert_id($conn);
        foreach ($cart as $produk_id => $item) {
            $quantity = $item['quantity'];
            $harga = $item['harga'];
            $subtotal = $harga * $quantity;
            $stmt2 = mysqli_prepare($conn, "INSERT INTO detail_transaksi(transaksi_id, produk_id, quantity, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, "iiiii", $transaksi_id, $produk_id, $quantity, $harga, $subtotal);
            $result = mysqli_stmt_execute($stmt2);
            if (!$result) {
                throw new Exception("gagal insert detail_transaksi");
            }

            $stmt3 = mysqli_prepare($conn, "UPDATE produk SET stok = stok - ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt3, "ii", $quantity, $produk_id);
            $result = mysqli_stmt_execute($stmt3);
            if (!$result) {
                throw new Exception("gagal update stok");
            }
        }

        mysqli_commit($conn);
        clear_cart();
        set_flash('kembalian', $kembalian);
        redirect("pages/transaction.php");
    } catch (\Exception $e) {
        mysqli_rollback($conn);
        echo "Transaksi gagal: " . $e->getMessage();
    }
}

function set_flash($key, $message){
    $_SESSION[$key] = $message;
}

function get_flash($key){
    $kembalian = null;
    if(isset($_SESSION[$key])){
        $kembalian = $_SESSION[$key];
        unset($_SESSION[$key]);
    }
    return $kembalian;
}