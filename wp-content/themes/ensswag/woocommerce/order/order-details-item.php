<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
    return;
}
?>
<div class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order)); ?>">

    <div class="woocommerce-table__product-name product-name">

        <div class="product-thumbnail">
            <?php

            global $wpdb;

            $domainName = $item->get_meta('ens_meta');

            // Check if we have mockup
            $haveCustomImage = '';
            if ($domainName && trim($domainName) != '') {

                $postID = $item['product_id'];

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
                    foreach ($query as $image_item) {
                        $haveCustomImage = '<img src="' . $image_item->image_big_url . '" />';
                    }
                }
            }

            if (trim($haveCustomImage != '')) {
                echo $haveCustomImage;
            } else {
                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $product->get_image());
                echo $thumbnail;
            }
            ?>
        </div>

        <div class="product-details">

            <div class="product-details-link">

                <?php
                $is_visible = $product && $product->is_visible();
                $product_permalink = apply_filters('woocommerce_order_item_permalink', $is_visible ? $product->get_permalink($item) : '', $item, $order);

                echo wp_kses_post(apply_filters('woocommerce_order_item_name', $product_permalink ? sprintf('<a href="%s">%s</a>', $product_permalink, $item->get_name()) : $item->get_name(), $item, $is_visible));

                $qty = $item->get_quantity();
                $refunded_qty = $order->get_qty_refunded_for_item($item_id);

                if ($refunded_qty) {
                    $qty_display = '<del>' . esc_html($qty) . '</del> <ins>' . esc_html($qty - ($refunded_qty * -1)) . '</ins>';
                } else {
                    $qty_display = esc_html($qty);
                }

                echo apply_filters('woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $qty_display) . '</strong>', $item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?>

            </div>

            <?php
            do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, false);

            wc_display_item_meta($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, false);
            ?>

        </div>

    </div>

    <div class="woocommerce-table__product-total product-total">
        <?php echo $order->get_formatted_line_subtotal($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </div>

</div>

<?php if ($show_purchase_note && $purchase_note) : ?>

    <div class="woocommerce-table__product-purchase-note product-purchase-note">

        <div colspan="2"><?php echo wpautop(do_shortcode(wp_kses_post($purchase_note))); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>

    </div>

<?php endif; ?>
