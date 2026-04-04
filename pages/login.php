<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once(__DIR__ . '/../config/database.php');

if (is_logged_in()) {
    redirect("admin/dashboard.php");
}

$errors = [];
if (isset($_POST) && is_post()) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    if (empty($username)) {
        $errors[] = 'username wajib diisi';
    }

    if (empty($password)) {
        $errors[] = 'password wajib diisi';
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ? AND is_active = 1");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                login($user);

                if ($user['role'] == 'admin') {
                    redirect('admin/dashboard.php');
                } elseif($user['role'] == 'kasir') {
                    redirect('pages/transaction.php');
                }
            } else {
                $errors[] = 'password anda salah';
            }
        } else {
            $errors[] = 'user tidak ditemukan';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <style>
        label {
            display: block;
        }

        button {
            display: block;
            margin-top: 4px;
        }
    </style>
</head>

<body>
    <h1>Sign In</h1>
    <form action="" method="POST">
        <?php
        foreach ($errors as $error) {
            echo $error;
        }
        ?>

        <div class="container">
            <label for="username">Username: </label>
            <input type="text" name="username" placeholder="CalvinHendri">

            <label for="password">Password: </label>
            <input type="password" name="password" placeholder="********">

            <button type="submit" name="signIn">Sign In</button>
        </div>
    </form>
</body>

</html>