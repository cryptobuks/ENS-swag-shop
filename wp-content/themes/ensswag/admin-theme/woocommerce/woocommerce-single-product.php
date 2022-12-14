<?php

add_action( 'woocommerce_single_product_summary', 'swag_action_after_single_product_title', 6 );

function swag_action_after_single_product_title() {
    global $product;

    $tagTitle = '';
    $tags = $product->tag_ids;
    foreach ($tags as $tag) {
        $tagTitle = get_term($tag)->name;
    }

    if ($tagTitle) {
        echo '
            <span class="product-tag">' . $tagTitle . '</span>
        ';
    }
}

function cmp($a, $b)
{
    $sizes = array(
        "2XL" => 0,
        "XL" => 1,
        "L" => 2,
        "M" => 3,
        "S" => 4,
        "XS" => 5,
        "XXS" => 6,
        "2XS" => 7,
    );

    $asize = $sizes[$a->name];
    $bsize = $sizes[$b->name];

    if ($asize == $bsize) {
        return 0;
    }

    return ($asize > $bsize) ? 1 : -1;
}

function wc_dropdown_variation_attribute_options($args = array())
{
    $args = wp_parse_args(
        apply_filters('woocommerce_dropdown_variation_attribute_options_args', $args),
        array(
            'options' => false,
            'attribute' => false,
            'product' => false,
            'selected' => false,
            'name' => '',
            'id' => '',
            'class' => '',
            'show_option_none' => __('Choose an option', 'woocommerce'),
        )
    );

    // Get selected value.
    if (false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product) {
        $selected_key = 'attribute_' . sanitize_title($args['attribute']);
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        $args['selected'] = isset($_REQUEST[$selected_key]) ? wc_clean(wp_unslash($_REQUEST[$selected_key])) : $args['product']->get_variation_default_attribute($args['attribute']);
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
    }

    $options = $args['options'];
    $product = $args['product'];
    $attribute = $args['attribute'];
    $name = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title($attribute);
    $id = $args['id'] ? $args['id'] : sanitize_title($attribute);
    $class = $args['class'];
    $show_option_none = (bool)$args['show_option_none'];
    $show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __('Choose an option', 'woocommerce'); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

    if (empty($options) && !empty($product) && !empty($attribute)) {
        $attributes = $product->get_variation_attributes();
        $options = $attributes[$attribute];
    }

    $html = '<select id="' . esc_attr($id) . '" class="' . esc_attr($class) . '" name="' . esc_attr($name) . '" data-attribute_name="attribute_' . esc_attr(sanitize_title($attribute)) . '" data-show_option_none="' . ($show_option_none ? 'yes' : 'no') . '" style="display: none;">';
    $html .= '<option value="">' . esc_html($show_option_none_text) . '</option>';

    $htmlButtons = '';

    if (!empty($options)) {
        if ($product && taxonomy_exists($attribute)) {
            // Get terms if this is a taxonomy - ordered. We need the names too.
            $terms = wc_get_product_terms(
                $product->get_id(),
                $attribute,
                array(
                    'fields' => 'all',
                )
            );

            // Sort XL - L - S ....
            if($name == 'attribute_pa_size'){
                usort($terms, "cmp");
            }

            foreach ($terms as $term) {
                if (in_array($term->slug, $options, true)) {
                    $html .= '<option value="' . esc_attr($term->slug) . '" ' . selected(sanitize_title($args['selected']), $term->slug, false) . '>' . esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute, $product)) . '</option>';

                    $selectedButton = str_replace("='selected'", '', selected(sanitize_title($args['selected']), $term->slug, false));
                    $htmlButtons .= '<button type="button" class="btn btn-size ' . $selectedButton . ' ' . esc_attr($id) . '" onclick="changeProductSize(this, \'' . esc_attr($id) . '\', \'' . esc_attr($term->slug) . '\');">' . esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute, $product)) . '</button>';
                }
            }
        } else {
            foreach ($options as $option) {
                // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
                $selected = sanitize_title($args['selected']) === $args['selected'] ? selected($args['selected'], sanitize_title($option), false) : selected($args['selected'], $option, false);
                $html .= '<option value="' . esc_attr($option) . '" ' . $selected . '>' . esc_html(apply_filters('woocommerce_variation_option_name', $option, null, $attribute, $product)) . '</option>';

                $selectedButton = str_replace("='selected'", '', $selected);
                $htmlButtons .= '<button type="button" class="btn btn-size ' . $selectedButton . ' ' . esc_attr($id) . '" onclick="changeProductSize(this, \'' . esc_attr($id) . '\', \'' . esc_attr($option) . '\');">' . esc_html(apply_filters('woocommerce_variation_option_name', $option, null, $attribute, $product)) . '</button>';
            }
        }
    }

    $html .= '</select>' . $htmlButtons;

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo apply_filters('woocommerce_dropdown_variation_attribute_options_html', $html, $args);
}

/**
 * Remove related products output
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );