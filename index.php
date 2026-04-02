<?php
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if(!is_logged_in()){
    redirect('pages/login.php');
}

if(is_admin()){
    redirect('admin/dashboard.php');
}else{
    redirect('kasir/dashboard.php');
}

echo "masukj";