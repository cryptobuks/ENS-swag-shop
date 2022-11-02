<?php

/**
 * PRODUCT REMOVE CART
 */
function product_remove_cart()
{

    $return_data = array();
    $return_data['status'] = 0;
    $return_data['total_cart'] = 0;
    $return_data['cart_already_html'] = '';

    //get post data
    $productID = filter_var($_POST['productID']);
    $cartProductKey = filter_var($_POST['cartProductKey']);

    if (filter_var($productID) > 0 && trim($cartProductKey) != '') {

        WC()->cart->remove_cart_item( $cartProductKey );

        $return_data['cart_already_html'] = getProductAlreadyItemsInCartHtml($productID);
        $return_data['subtotal_cart'] = WC()->cart->get_cart_subtotal();

        $return_data['total_cart'] = WC()->cart->cart_contents_count;

        $return_data['status'] = 1;

    } else {
        $return_data['status'] = 2;
    }

    echo json_encode($return_data);

}

add_action('wp_ajax_nopriv_product_remove_cart', 'product_remove_cart');
add_action('wp_ajax_product_remove_cart', 'product_remove_cart');