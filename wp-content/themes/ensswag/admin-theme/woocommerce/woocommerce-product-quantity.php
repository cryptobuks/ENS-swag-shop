<?php

function woocommerce_quantity_input($data = null)
{
    global $product;

    if (!$data) {
        $defaults = array(
            'input_name' => 'quantity',
            'input_value' => '1',
            'max_value' => apply_filters('woocommerce_quantity_input_max', '', $product),
            'min_value' => apply_filters('woocommerce_quantity_input_min', '', $product),
            'step' => apply_filters('woocommerce_quantity_input_step', '1', $product),
            'style' => apply_filters('woocommerce_quantity_style', 'float:left;', $product)
        );
    } else {
        $defaults = array(
            'input_name' => ( isset($data['input_name']) )? $data['input_name'] : 'quantity',
            'input_value' => $data['input_value'],
            'step' => apply_filters('cw_woocommerce_quantity_input_step', '1', $product),
            'max_value' => apply_filters('cw_woocommerce_quantity_input_max', '', $product),
            'min_value' => apply_filters('cw_woocommerce_quantity_input_min', '', $product),
            'style' => apply_filters('cw_woocommerce_quantity_style', 'float:left;', $product)
        );
    }

    if (!empty($defaults['min_value'])) {
        $min = $defaults['min_value'];
    } else {
        $min = 1;
    }

    if (!empty($defaults['max_value'])) {
        $max = $defaults['max_value'];
    } else {
        $max = 20;
    }

    if (!empty($defaults['step'])) {
        $step = $defaults['step'];
    } else {
        $step = 1;
    }

    $options = '';

    for ($count = $min; $count <= $max; $count = $count + $step) {

        $selected = $count == $defaults['input_value'] ? ' selected' : '';

        $options .= '<option value="' . $count . '"' . $selected . '>' . $count . '</option>';

    }

    echo '<div class="cw_quantity_select quantity-select" style="' . $defaults['style'] . '"><select name="' . esc_attr($defaults['input_name']) . '" title="' . _x('Qty', 'Product Description', 'woocommerce') . '" class="cw_qty" is="ms-dropdown">' . $options . '</select></div>';

}