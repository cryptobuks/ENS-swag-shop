<?php

/**
 * Update cart quantity
 */
function update_cart_quantity()
{

    $return_data = array();
    $return_data['status'] = 0;
    $return_data['rate'] = 0;

    // Get post data
    $qty = filter_var($_POST['qty']);
    $itemKey = filter_var($_POST['itemKey']);

    if (
        trim($qty) != '' && trim($itemKey) != ''
    ) {
        global $woocommerce;

        $woocommerce->cart->set_quantity($itemKey, $qty);

        $return_data['total_cart'] = WC()->cart->cart_contents_count;
        $return_data['subtotal_cart'] = WC()->cart->get_cart_subtotal();
        $product_updated = number_format(WC()->cart->get_cart_item($itemKey)['line_total'], '2', '.', ',');
        $return_data['product_updated'] = '
            <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&#36;</span>' . $product_updated . '</bdi></span>
        ';

        WC()->cart->get_cart_item($itemKey);

        $return_data['status'] = 1;

    } else {
        $return_data['status'] = 2;
    }

    echo json_encode($return_data);

}

add_action('wp_ajax_nopriv_update_cart_quantity', 'update_cart_quantity');
add_action('wp_ajax_update_cart_quantity', 'update_cart_quantity');