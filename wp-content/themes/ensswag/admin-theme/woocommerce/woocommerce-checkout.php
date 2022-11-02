<?php


add_filter('woocommerce_checkout_fields', 'gm_fields_reorder', 10000);

/**
 * Set up fields additional data like placeholder, position, etc...
 *
 * @param $checkout_fields
 * @return mixed
 */
function gm_fields_reorder($checkout_fields)
{
    // Set up placeholder
    $checkout_fields['billing']['billing_first_name']['placeholder'] = __('First Name', 'template');
    $checkout_fields['billing']['billing_last_name']['placeholder'] = __('Last Name', 'template');
    $checkout_fields['billing']['billing_country']['placeholder'] = __('Country / Region', 'template');
    $checkout_fields['billing']['billing_address_2']['placeholder'] = __('Apartment/Landmark', 'template');
    $checkout_fields['billing']['billing_city']['placeholder'] = __('City', 'template');
    $checkout_fields['billing']['billing_state']['placeholder'] = __('State', 'template');
    $checkout_fields['billing']['billing_postcode']['placeholder'] = __('ZIP Code', 'template');
    $checkout_fields['billing']['billing_phone']['placeholder'] = __('Phone Number', 'template');
    $checkout_fields['billing']['billing_email']['placeholder'] = __('Email', 'template');

    // Set up position
    $checkout_fields['billing']['billing_first_name']['priority'] = 2;
    $checkout_fields['billing']['billing_last_name']['priority'] = 3;
    $checkout_fields['billing']['billing_email']['priority'] = 4;
    $checkout_fields['billing']['billing_country']['priority'] = 5;

    return $checkout_fields;
}

add_filter('woocommerce_default_address_fields', 'wc_override_address_fields');

/**
 * Change address placeholder
 *
 * @param $fields
 * @return array
 */
function wc_override_address_fields( $fields ) {
    $fields['address_1']['placeholder'] = 'Address';
    return $fields;
}

add_filter( 'woocommerce_cart_shipping_method_full_label', 'change_cart_shipping_method_full_label', 10, 2 );

/**
 * Adjust shipping label title, remove cost at end
 *
 * @param $label
 * @param $method
 * @return mixed
 */
function change_cart_shipping_method_full_label( $label, $method ) {

    $label  = $method->get_label();

    return $label;
}

// REMOVED: 26-29-2022 Global redirect to check out when hitting cart page
//add_action( 'template_redirect', 'redirect_to_checkout_if_cart' );
function redirect_to_checkout_if_cart() {

    if ( !is_cart() ) return;

    global $woocommerce;

    if ( $woocommerce->cart->is_empty() ) {
        // If empty cart redirect to home
        wp_redirect( get_home_url(), 302 );
    } else {
        // Else redirect to check out url
        wp_redirect( $woocommerce->cart->get_checkout_url(), 302 );
    }

    exit;
}


/**
 * Add the field to the checkout
 */
add_action('woocommerce_after_order_notes', 'signature_checkout_field');

function signature_checkout_field($checkout)
{
    woocommerce_form_field('wa_signature', array(
        'type' => 'text',
        'class' => array(''),
        'label' => '',
        'placeholder' => '',
        'autocomplete' => 'off',
        'default' => '',
    ),
        $checkout->get_value('wa_signature')
    );

    woocommerce_form_field('wa_hash', array(
        'type' => 'text',
        'class' => array(''),
        'label' => '',
        'placeholder' => '',
        'autocomplete' => 'off',
        'default' => '',
    ),
        $checkout->get_value('wa_hash')
    );

}

/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'signature_checkout_field_update_order_meta' );

function signature_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['wa_signature'] ) ) {
        update_post_meta( $order_id, 'Signature Address Data', sanitize_text_field( $_POST['wa_signature'] ) );
    }

    if ( ! empty( $_POST['wa_hash'] ) ) {
        update_post_meta($order_id, 'Wallet Message Data', sanitize_text_field($_SESSION['wa_hash']));
    }

    if ( isset($_SESSION['user_wallet_address']) ) {
        update_post_meta($order_id, 'Wallet Address Data', sanitize_text_field($_SESSION['user_wallet_address']));
    }
}

/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'signature_checkout_field_display_admin_order_meta', 10, 1 );

function signature_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Signature Address Data').':</strong> ' . get_post_meta( $order->id, 'Signature Address Data', true ) . '</p>';
    echo '<p><strong>'.__('Wallet Address Data').':</strong> ' . get_post_meta( $order->id, 'Wallet Address Data', true ) . '</p>';
    echo '<p><strong>'.__('Wallet Message Data').':</strong> ' . get_post_meta( $order->id, 'Wallet Message Data', true ) . '</p>';
}

/**
 * Process the checkout
 */
add_action('woocommerce_checkout_process', 'signature_checkout_field_checkout_field_process');

function signature_checkout_field_checkout_field_process() {
    if ( ! $_POST['wa_signature'] ) {
        wc_add_notice(__('You are missing signature.'), 'error');
    }
}