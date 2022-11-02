<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined('ABSPATH') || exit;

global $wpdb;

do_action('woocommerce_before_cart'); ?>

<!-- start:cart-inside -->
<div class="cart-inside">

    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <?php do_action('woocommerce_before_cart_table'); ?>

        <div class="shop_table shop_table_responsive cart woocommerce-cart-form__contents">

            <?php do_action('woocommerce_before_cart_contents'); ?>
            
            <!-- start:cart-items -->
            <div class="cart-items">
                
                <?php
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                        ?>
                        <div id="cart-item-<?php echo $product_id; ?>" class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                            <!-- start:cart-item-left -->
                            <div class="cart-item-left">
                                
                                <div class="product-thumbnail">
                                    <?php

                                    /* Check if we have custom image */
                                    $postID = $cart_item['product_id'];

                                    $haveCustomImage = '';
                                    if (isset($cart_item['ensName'])) {
                                        $domainName = $cart_item['ensName'];

                                        // Get domain id
                                        $ens_domain_id = 0;
                                        $query = $wpdb->get_row(
                                                                            "
                                                SELECT id FROM wenp_ens_domains
                                                WHERE name='{$domainName}' LIMIT 1
                                        ");

                                        if (isset($query->id) && $query->id > 0) {
                                            $ens_domain_id = $query->id;
                                        }

                                        // Check if we have images
                                        $query = $wpdb->get_results(
                                            "
                                                SELECT * FROM wenp_ens_mockups
                                                WHERE ens_domain_id='{$ens_domain_id}' AND post_id='{$postID}' ORDER BY image_order ASC LIMIT 1
                                        ");

                                        // Return existing images
                                        if (sizeof($query) > 0) {
                                            foreach ($query AS $image_item){
                                                $haveCustomImage = '<img src="'.$image_item->image_big_url.'" />';
                                            }
                                        }
                                    }

                                    if(trim($haveCustomImage != '')){
                                        echo $haveCustomImage;
                                    }else{
                                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                                        if (!$product_permalink) {
                                            echo $thumbnail; // PHPCS: XSS ok.
                                        } else {
                                            printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
                                        }
                                    }

                                    ?>
                                </div>
                                
                            </div>
                            <!-- end:cart-item-left -->
                            
                            <!-- start:cart-item-right -->
                            <div class="cart-item-right">
                                
                                <!-- start:item-right-left -->
                                <div class="item-right-left">

                                    <div class="cart-title-holder">

                                        <div class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                                            <?php

                                            if (!$product_permalink) {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key) . '&nbsp;');
                                            } else {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_title()), $cart_item, $cart_item_key));
                                            }

                                            do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                                            // Meta data.
                                            echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

                                            // Backorder notification.
                                            if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                                            }

                                            ?>

                                        </div>

                                        <div class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                                            <?php
                                            if ($_product->is_sold_individually()) {
                                                $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                            } else {
                                                $product_quantity = woocommerce_quantity_input(
                                                    array(
                                                        'input_name' => "cart[{$cart_item_key}][qty]",
                                                        'input_value' => $cart_item['quantity'],
                                                        'max_value' => $_product->get_max_purchase_quantity(),
                                                        'min_value' => '0',
                                                        'product_name' => $_product->get_name(),
                                                    ),
                                                    $_product,
                                                    false
                                                );
                                            }

                                            echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
                                            ?>
                                        </div>

                                        <div class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
                                            <?php
                                            echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                            ?>
                                        </div>

                                    </div>

                                    <!-- start:product-bellow-title -->
                                    <div class="product-bellow-title">

                                        <?php
                                        if ($cart_item['product_id'] == $postID && isset($cart_item['ensName'])) {

                                            $domainName = (isset($cart_item['ensName']) && $cart_item['ensName'] != '0') ? $cart_item['ensName'] : 'nick.eth';

                                            echo '
                                                <div id="al-' . $cart_item_key . '" class="already-in-cart my-2">
                                                    <div class="cart-domain-holder">
                                                        <img src="' . TEMPLATEDIR . '/images/default-avatar.svg" alt="Default avatar">
                                                        <div class="cart-domain-name">' . $domainName . '</div>
                                                    </div>
                                                </div>
                                            ';
                                        }
                                        ?>

                                        <?php
                                        $variation = ( method_exists($_product, 'get_variation_attributes') )? $_product->get_variation_attributes() : [];

                                        $variationLabel = ( isset($variation['attribute_pa_size']) )? $variation['attribute_pa_size'] : '';
                                        ?>
                                        <?php if( $variationLabel ): ?>
                                            <div class="variation-label">
                                                <?php echo $variationLabel; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php
                                        $variationColorLabel = ( isset($variation['attribute_pa_color']) )? $variation['attribute_pa_color'] : '';
                                        ?>
                                        <?php if( $variationColorLabel ): ?>
                                            <div class="variation-label">
                                                <?php echo $variationColorLabel; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="product-remove">
                                            <button onclick="removeProductFromCart('<?php echo $cart_item_key; ?>', <?php echo $product_id; ?>);" class="remove-item">×</button>
                                            <?php
//                                            echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
//                                                'woocommerce_cart_item_remove_link',
//                                                sprintf(
//                                                    '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
//                                                    esc_url(wc_get_cart_remove_url($cart_item_key)),
//                                                    esc_html__('Remove this item', 'woocommerce'),
//                                                    esc_attr($product_id),
//                                                    esc_attr($_product->get_sku())
//                                                ),
//                                                $cart_item_key
//                                            );
                                            ?>
                                        </div>

                                    </div>
                                    <!-- end:product-bellow-title -->

                                </div>
                                <!-- end:item-right-left -->

                                <!-- start:item-right-right -->
                                <div class="item-right-right">

                                    <div class="product-price" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                                        <?php
                                        echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                        ?>
                                    </div>

                                    <div class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
                                        <?php
                                        echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                        ?>
                                    </div>

                                </div>
                                <!-- end:item-right-right -->

                            </div>
                            <!-- end:cart-item-right -->

                        </div>
                        <?php
                    }
                }
                ?>

            </div>
            <!-- end:cart-items -->

            <?php do_action('woocommerce_cart_contents'); ?>

            <div>
                <div class="actions">

                    <?php if (wc_coupons_enabled()) { ?>
                        <div class="coupon">
                            <label for="coupon_code"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label> <input
                                    type="text" name="coupon_code" class="input-text" id="coupon_code" value=""
                                    placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>"/>
                            <button type="submit" class="button" name="apply_coupon"
                                    value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_attr_e('Apply coupon', 'woocommerce'); ?></button>
                            <?php do_action('woocommerce_cart_coupon'); ?>
                        </div>
                    <?php } ?>

                    <button type="submit" class="button" name="update_cart"
                            value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>

                    <?php do_action('woocommerce_cart_actions'); ?>

                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                </div>
            </div>

            <?php do_action('woocommerce_after_cart_contents'); ?>

        </div>
        <?php do_action('woocommerce_after_cart_table'); ?>
    </form>

    <?php do_action('woocommerce_before_cart_collaterals'); ?>

    <div class="cart-collaterals">
        <?php
        /**
         * Cart collaterals hook.
         *
         * @hooked woocommerce_cross_sell_display
         * @hooked woocommerce_cart_totals - 10
         */
        do_action('woocommerce_cart_collaterals');
        ?>
    </div>

    <?php do_action('woocommerce_after_cart'); ?>

</div>
<!-- end:cart-inside -->