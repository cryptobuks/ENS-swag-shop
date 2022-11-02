<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page' );
}

/*-----------------------------------------------------------------------------------*/
/* This theme supports WooCommerce, woo! */
/*-----------------------------------------------------------------------------------*/

add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

/*-----------------------------------------------------------------------------------*/
/* Custom files */
/*-----------------------------------------------------------------------------------*/

/* Manage product list of Woocommerce */
include 'woocommerce-product-list.php';

/* Manage single product of Woocommerce */
include 'woocommerce-single-product.php';

/* Manage product quantity of Woocommerce */
include 'woocommerce-product-quantity.php';

/* Manage checkout of Woocommerce */
include 'woocommerce-checkout.php';

/* Manage cart count of Woocommerce */
include 'woocommerce-number-items-cart.php';

/* Manage cart shipping of Woocommerce */
include 'woocommerce-cart-shipping.php';

/* Manage cart order of Woocommerce => Printufull */
include 'woocommerce-cart-order.php';