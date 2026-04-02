<?php
require_once(__DIR__ . '/../includes/functions.php');
require_once(__DIR__ . '/../includes/auth.php');

require_login();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    logout();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dashboard</title>
</head>
<body>
    <form action="" method="POST">
        <button type="submit" name="logout">logout</button>
    </form>
</body>
</html>