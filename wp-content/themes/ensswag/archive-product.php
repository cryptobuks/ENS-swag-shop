<?php

/**
 * Redirect user if try to reach this link
 */
if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/shop/'){
    wp_redirect('/cart');
    exit;
}

get_header();
?>

    <?php include "templates/webshop/webshop-list.php"; ?>

<?php get_footer(); ?>