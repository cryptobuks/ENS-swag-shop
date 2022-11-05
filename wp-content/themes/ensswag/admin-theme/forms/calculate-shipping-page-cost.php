<?php

/**
 * Calculate shipping cost
 */
function calculate_shipping_page_cost()
{

    $return_data = array();
    $return_data['status'] = 0;
    $return_data['rate'] = 0;

    // Get post data
    $address = filter_var($_POST['address']);
    $zip = filter_var($_POST['zip']);
    $product_variant = filter_var($_POST['product_variant']);
    $country = filter_var($_POST['country']);
    $cshsp_check = filter_var($_POST['cshsp-check']);

    $state = filter_var($_POST['state']);
    $state_us = filter_var($_POST['state_us']);
    $state_ca = filter_var($_POST['state_ca']);
    $state_au = filter_var($_POST['state_au']);

    $nonce = (wp_verify_nonce($_POST['calculate-shipping-nonce'], "calculate_shipping_cost")) ? true : false;

    if (
        trim($address) != '' && trim($zip) != '' &&
        trim($product_variant) != '' && trim($country) != '' &&
        $cshsp_check == 1 && $nonce
    ) {

        if($country == 'US'){ // Check USA
            $state = $state_us;
        }
        elseif ($country == 'CA'){ // Check CANADA
            $state = $state_ca;
        }
        elseif ($country == 'AU'){ // Check AUSTRALIA
            $state = $state_au;
        }

        $return_data['rate'] = getShippingRatesForProduct($product_variant, $address, $country, $state, $zip);

        $return_data['status'] = 1;

    } else {
        $return_data['status'] = 2;
    }

    echo json_encode($return_data);

}

add_action('wp_ajax_nopriv_calculate_shipping_page_cost', 'calculate_shipping_page_cost');
add_action('wp_ajax_calculate_shipping_page_cost', 'calculate_shipping_page_cost');