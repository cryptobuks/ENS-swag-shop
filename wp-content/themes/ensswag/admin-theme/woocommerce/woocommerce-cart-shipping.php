<?php

/**
 * Calculate cart shipping based on Printful data
 *
 * @param $rates
 *
 * @return mixed
 */
function calculate_cart_shipping($rates)
{

    foreach ($rates as $rate) {

        // Set the price
        $rate->cost = (string)getCartShippingCost();
    }

    return $rates;
}

add_filter('woocommerce_package_rates', 'calculate_cart_shipping', 100, 2);

/**
 * Remove some countries from list
 * because Printful service don't ship to them
 *
 * @param $country
 * @return mixed
 */
function woo_remove_specific_country( $country )
{
    unset($country["RU"]); // Russia
    unset($country["BY"]); // Belarus
    unset($country["EC"]); // Ecuador
    unset($country["CU"]); // Cuba
    unset($country["IR"]); // Iran
    unset($country["SY"]); // Syria
    unset($country["KP"]); // North Korea
    return $country;
}
add_filter( 'woocommerce_countries', 'woo_remove_specific_country', 10, 1 );