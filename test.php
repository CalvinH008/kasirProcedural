<?php 
require_once 'config/database.php';
echo "done";
require_once 'includes/functions.php';
add_to_cart(1, 2, 'Aqua', 5000);
add_to_cart(1, 1, 'Aqua', 5000);
add_to_cart(2, 3, 'Indomie', 3000);
var_dump(get_cart());
echo get_cart_total();