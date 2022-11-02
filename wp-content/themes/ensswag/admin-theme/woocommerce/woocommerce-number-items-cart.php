<?php

/**
 * Enqueue JS script code in ehad
 */
function swag_gm_header_scripts(){
    ?>
    <script>
        let cartContentsCount = <?php echo WC()->cart->cart_contents_count; ?>;
        jQuery( document ).ready(function() {

            const menuCartLink = jQuery('.menu .cart-main-link');

            if( menuCartLink.length > 0 ){
                menuCartLink.children('a').append('<span class="btn-cart-top-count">' + cartContentsCount + '</span>');
            }

        });
    </script>
    <?php
}

add_action( 'wp_head', 'swag_gm_header_scripts' );

/**
 * Update cart count on basket icon in
 * header of the website
 *
 * @param $fragments
 * @return mixed
 */
function swag_gm_header_add_to_cart_fragment( $fragments ) {

    ob_start();
    ?>
    <span class="btn-cart-top-count"><?php echo WC()->cart->cart_contents_count; ?></span>
    <?php

    $fragments['span.btn-cart-top-count'] = ob_get_clean();

    return $fragments;
}

add_filter( 'woocommerce_add_to_cart_fragments', 'swag_gm_header_add_to_cart_fragment' );
