<?php

$prefix = "product";

// Add meta box
function add_box_metabox_blog_information()
{

    global $meta_box_blog_information;

    add_meta_box(
        $meta_box_blog_information['id'],
        $meta_box_blog_information['title'],
        'blog_show_box',
        $meta_box_blog_information['page'],
        $meta_box_blog_information['context'],
        $meta_box_blog_information['priority']
    );
}

// Callback function to show fields in meta box
function blog_show_box()
{

    global $meta_box_blog_information, $post;

    // Use nonce for verification
    echo '<input type="hidden" name="bees_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    echo '<table class="form-table">';

    foreach ($meta_box_blog_information['fields'] as $field) {
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);
        echo '<tr>',
        '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
        '<td>';
        switch ($field['type']) {
            case 'text':
                echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
                break;
            case 'textarea':
                echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
                break;
            case 'textarea-rte':
                wp_editor($meta ? $meta : $field['std'], $field['id'], $settings = array(
                    'textarea_name' => $field['id'],
                    'wpautop' => false
                ));
                break;
            case 'select':
                echo '<select name="', $field['id'], '" id="', $field['id'], '">';
                foreach ($field['options'] as $option) {
                    echo '<option ', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                }
                echo '</select>';
                echo '<br />';
                echo $field['desc'];
                break;
            case 'radio':
                foreach ($field['options'] as $option) {
                    echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'], '<br />';
                }
                break;
            case 'checkbox':
                echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
                break;
        }
        echo '</td><td>',
        '</td></tr>';
    }
    echo '</table>';
}

add_action('save_post', 'blog_save_data');

// Save data from meta box
function blog_save_data($post_id)
{

    global $meta_box_blog_information;

    // verify nonce
    if (isset($_POST['bees_meta_box_nonce']) && !wp_verify_nonce($_POST['bees_meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // check permissions
    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    if(isset($meta_box_blog_information['fields'])){
        foreach ($meta_box_blog_information['fields'] as $field) {
            $old = get_post_meta($post_id, $field['id'], true);
            $new = (isset($_POST[$field['id']])) ? $_POST[$field['id']] : false;
            if ($new && $new != $old) {
                update_post_meta($post_id, $field['id'], $new);
            } elseif ('' == $new && $old) {
                delete_post_meta($post_id, $field['id'], $old);
            }
        }
    }
    else{
        return $post_id;
    }
}


/**
 * Register meta box(es).
 */
function wpdocs_register_meta_boxes()
{
    add_meta_box('meta-box-id', __('Printful Product Data', 'textdomain'), 'wpdocs_my_display_callback', 'product');
}

add_action('add_meta_boxes', 'wpdocs_register_meta_boxes');

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function wpdocs_my_display_callback($post)
{
    // Display code/markup goes here. Don't forget to include nonces!

//    echo '<pre>';
//    print_r($post);
//    echo '</pre>';
    // $post->ID

    global $wpdb;

    $metaData = $wpdb->get_row(
        "
                    SELECT * FROM wenp_ens_product_meta
                    WHERE post_id='{$post->ID}' LIMIT 1
        ");

    // Use nonce for verification
    echo '<input type="hidden" name="product_wpdocs_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

    echo '<table class="form-table">';

    $product_id_value = ($metaData && isset($metaData->product_id)) ? $metaData->product_id : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="product_id"><strong>Product ID</strong></label></td>
            <td style="width: 10%;"><input type="text" name="product_id" style="width: 400px;" value="' . $product_id_value . '"></td>
            <td>e.g. 282338808</td>
        </tr>
    ';

    $catalog_id_value = ($metaData && isset($metaData->catalog_id)) ? $metaData->catalog_id : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="catalog_id"><strong>Catalog ID</strong></label></td>
            <td><input type="text" name="catalog_id" style="width: 400px;" value="' . $catalog_id_value . '"></td>
            <td>e.g. 206</td>
        </tr>
    ';

    $variant_id_value = ($metaData && isset($metaData->variant_id)) ? $metaData->variant_id : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="variant_id"><strong>Variant ID</strong></label></td>
            <td><input type="text" name="variant_id" style="width: 400px;" value="' . $variant_id_value . '"></td>
            <td>e.g. 7854</td>
        </tr>
    ';

    // First file

    $first_file_placement_value = ($metaData && isset($metaData->first_file_placement)) ? $metaData->first_file_placement : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="first_file_placement"><strong>First File Placement</strong></label></td>
            <td><input type="text" name="first_file_placement" style="width: 400px;" value="' . $first_file_placement_value . '"></td>
            <td>e.g. embroidery_front, see more on link: <a href="https://developers.printful.com/docs/#section/Placements" target="_blank">https://developers.printful.com/docs/#section/Placements</a></td>
        </tr>
    ';

    $first_file_image_url_value = ($metaData && isset($metaData->first_file_image_url)) ? $metaData->first_file_image_url : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="first_file_image_url"><strong>First File URL or Text</strong></label></td>
            <td><input type="text" name="first_file_image_url" style="width: 400px;" value="' . $first_file_image_url_value . '"></td>
            <td>put file URL if it is text write default value</a></td>
        </tr>
    ';

    $first_file_area_width_value = ($metaData && isset($metaData->first_file_area_width)) ? $metaData->first_file_area_width : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="first_file_area_width"><strong>First File Area Width</strong></label></td>
            <td><input type="text" name="first_file_area_width" style="width: 400px;" value="' . $first_file_area_width_value . '"></td>
            <td>e.g. 1200</td>
        </tr>
    ';

    $first_file_area_height_value = ($metaData && isset($metaData->first_file_area_height)) ? $metaData->first_file_area_height : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="first_file_area_height"><strong>First File Area Height</strong></label></td>
            <td><input type="text" name="first_file_area_height" style="width: 400px;" value="' . $first_file_area_height_value . '"></td>
            <td>e.g. 525</td>
        </tr>
    ';

    $first_file_width_value = ($metaData && isset($metaData->first_file_width)) ? $metaData->first_file_width : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="first_file_width"><strong>First File Width</strong></label></td>
            <td><input type="text" name="first_file_width" style="width: 400px;" value="' . $first_file_width_value . '"></td>
            <td>e.g. 1100</td>
        </tr>
    ';

    $first_file_height_value = ($metaData && isset($metaData->first_file_height)) ? $metaData->first_file_height : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="first_file_height"><strong>First File Height</strong></label></td>
            <td><input type="text" name="first_file_height" style="width: 400px;" value="' . $first_file_height_value . '"></td>
            <td>e.g. 300</td>
        </tr>
    ';

    $first_file_top_value = ($metaData && isset($metaData->first_file_top)) ? $metaData->first_file_top : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="first_file_top"><strong>First File Top Position</strong></label></td>
            <td><input type="text" name="first_file_top" style="width: 400px;" value="' . $first_file_top_value . '"></td>
            <td>e.g. 50</td>
        </tr>
    ';

    $first_file_left_value = ($metaData && isset($metaData->first_file_left)) ? $metaData->first_file_left : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="first_file_left"><strong>First File Left Position</strong></label></td>
            <td><input type="text" name="first_file_left" style="width: 400px;" value="' . $first_file_left_value . '"></td>
            <td>e.g. 0</td>
        </tr>
    ';

    $first_file_thread_position_value = ($metaData && isset($metaData->first_file_thread_position)) ? $metaData->first_file_thread_position : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="first_file_thread_position"><strong>First File Thread Position</strong></label></td>
            <td><input type="text" name="first_file_thread_position" style="width: 400px;" value="' . $first_file_thread_position_value . '"></td>
            <td>e.g. thread_colors_right</td>
        </tr>
    ';

    $first_file_thread_position_colors_value = ($metaData && isset($metaData->first_file_thread_position_colors)) ? $metaData->first_file_thread_position_colors : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="first_file_thread_position_colors"><strong>First File Thread Position Colors</strong></label></td>
            <td><input type="text" name="first_file_thread_position_colors" style="width: 400px;" value="' . $first_file_thread_position_colors_value . '"></td>
            <td>e.g. #3399FF, #FFFFFF</td>
        </tr>
    ';


    // Second file

    $second_file_placement_value = ($metaData && isset($metaData->second_file_placement)) ? $metaData->second_file_placement : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="second_file_placement"><strong>Second File Placement</strong></label></td>
            <td><input type="text" name="second_file_placement" style="width: 400px;" value="' . $second_file_placement_value . '"></td>
            <td>e.g. embroidery_front, see more on link: <a href="https://developers.printful.com/docs/#section/Placements" target="_blank">https://developers.printful.com/docs/#section/Placements</a></td>
        </tr>
    ';

    $second_file_image_url_value = ($metaData && isset($metaData->second_file_image_url)) ? $metaData->second_file_image_url : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="second_file_image_url"><strong>Second File URL or Text</strong></label></td>
            <td><input type="text" name="second_file_image_url" style="width: 400px;" value="' . $second_file_image_url_value . '"></td>
            <td>put file URL if it is text write default value</a></td>
        </tr>
    ';

    $second_file_area_width_value = ($metaData && isset($metaData->second_file_area_width)) ? $metaData->second_file_area_width : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="second_file_area_width"><strong>Second File Area Width</strong></label></td>
            <td><input type="text" name="second_file_area_width" style="width: 400px;" value="' . $second_file_area_width_value . '"></td>
            <td>e.g. 1200</td>
        </tr>
    ';

    $second_file_area_height_value = ($metaData && isset($metaData->second_file_area_height)) ? $metaData->second_file_area_height : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="second_file_area_height"><strong>Second File Area Height</strong></label></td>
            <td><input type="text" name="second_file_area_height" style="width: 400px;" value="' . $second_file_area_height_value . '"></td>
            <td>e.g. 525</td>
        </tr>
    ';

    $second_file_width_value = ($metaData && isset($metaData->second_file_width)) ? $metaData->second_file_width : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="second_file_width"><strong>Second File Width</strong></label></td>
            <td><input type="text" name="second_file_width" style="width: 400px;" value="' . $second_file_width_value . '"></td>
            <td>e.g. 1100</td>
        </tr>
    ';

    $second_file_height_value = ($metaData && isset($metaData->second_file_height)) ? $metaData->second_file_height : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="second_file_height"><strong>Second File Height</strong></label></td>
            <td><input type="text" name="second_file_height" style="width: 400px;" value="' . $second_file_height_value . '"></td>
            <td>e.g. 300</td>
        </tr>
    ';

    $second_file_top_value = ($metaData && isset($metaData->second_file_top)) ? $metaData->second_file_top : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="second_file_top"><strong>Second File Top Position</strong></label></td>
            <td><input type="text" name="second_file_top" style="width: 400px;" value="' . $second_file_top_value . '"></td>
            <td>e.g. 50</td>
        </tr>
    ';

    $second_file_left_value = ($metaData && isset($metaData->second_file_left)) ? $metaData->second_file_left : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="second_file_left"><strong>Second File Left Position</strong></label></td>
            <td><input type="text" name="second_file_left" style="width: 400px;" value="' . $second_file_left_value . '"></td>
            <td>e.g. 0</td>
        </tr>
    ';

    $second_file_thread_position_value = ($metaData && isset($metaData->second_file_thread_position)) ? $metaData->second_file_thread_position : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="second_file_thread_position"><strong>Second File Thread Position</strong></label></td>
            <td><input type="text" name="second_file_thread_position" style="width: 400px;" value="' . $second_file_thread_position_value . '"></td>
            <td>e.g. thread_colors_right</td>
        </tr>
    ';

    $second_file_thread_position_colors_value = ($metaData && isset($metaData->second_file_thread_position_colors)) ? $metaData->second_file_thread_position_colors : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="second_file_thread_position_colors"><strong>Second File Thread Position Colors</strong></label></td>
            <td><input type="text" name="second_file_thread_position_colors" style="width: 400px;" value="' . $second_file_thread_position_colors_value . '"></td>
            <td>e.g. #3399FF, #FFFFFF</td>
        </tr>
    ';

    // Third file

    $third_file_placement_value = ($metaData && isset($metaData->third_file_placement)) ? $metaData->third_file_placement : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="third_file_placement"><strong>Third File Placement</strong></label></td>
            <td><input type="text" name="third_file_placement" style="width: 400px;" value="' . $third_file_placement_value . '"></td>
            <td>e.g. embroidery_front, see more on link: <a href="https://developers.printful.com/docs/#section/Placements" target="_blank">https://developers.printful.com/docs/#section/Placements</a></td>
        </tr>
    ';

    $third_file_image_url_value = ($metaData && isset($metaData->third_file_image_url)) ? $metaData->third_file_image_url : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="third_file_image_url"><strong>Third File URL or Text</strong></label></td>
            <td><input type="text" name="third_file_image_url" style="width: 400px;" value="' . $third_file_image_url_value . '"></td>
            <td>put file URL if it is text write default value</a></td>
        </tr>
    ';

    $third_file_area_width_value = ($metaData && isset($metaData->third_file_area_width)) ? $metaData->third_file_area_width : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="third_file_area_width"><strong>Third File Area Width</strong></label></td>
            <td><input type="text" name="third_file_area_width" style="width: 400px;" value="' . $third_file_area_width_value . '"></td>
            <td>e.g. 1200</td>
        </tr>
    ';

    $third_file_area_height_value = ($metaData && isset($metaData->third_file_area_height)) ? $metaData->third_file_area_height : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="third_file_area_height"><strong>Third File Area Height</strong></label></td>
            <td><input type="text" name="third_file_area_height" style="width: 400px;" value="' . $third_file_area_height_value . '"></td>
            <td>e.g. 525</td>
        </tr>
    ';

    $third_file_width_value = ($metaData && isset($metaData->third_file_width)) ? $metaData->third_file_width : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="third_file_width"><strong>Third File Width</strong></label></td>
            <td><input type="text" name="third_file_width" style="width: 400px;" value="' . $third_file_width_value . '"></td>
            <td>e.g. 1100</td>
        </tr>
    ';

    $third_file_height_value = ($metaData && isset($metaData->third_file_height)) ? $metaData->third_file_height : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="third_file_height"><strong>Third File Height</strong></label></td>
            <td><input type="text" name="third_file_height" style="width: 400px;" value="' . $third_file_height_value . '"></td>
            <td>e.g. 300</td>
        </tr>
    ';

    $third_file_top_value = ($metaData && isset($metaData->third_file_top)) ? $metaData->third_file_top : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="third_file_top"><strong>Third File Top Position</strong></label></td>
            <td><input type="text" name="third_file_top" style="width: 400px;" value="' . $third_file_top_value . '"></td>
            <td>e.g. 50</td>
        </tr>
    ';

    $third_file_left_value = ($metaData && isset($metaData->third_file_left)) ? $metaData->third_file_left : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="third_file_left"><strong>Third File Left Position</strong></label></td>
            <td><input type="text" name="third_file_left" style="width: 400px;" value="' . $third_file_left_value . '"></td>
            <td>e.g. 0</td>
        </tr>
    ';

    $third_file_thread_position_value = ($metaData && isset($metaData->third_file_thread_position)) ? $metaData->third_file_thread_position : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="third_file_thread_position"><strong>Third File Thread Position</strong></label></td>
            <td><input type="text" name="third_file_thread_position" style="width: 400px;" value="' . $third_file_thread_position_value . '"></td>
            <td>e.g. thread_colors_right</td>
        </tr>
    ';

    $third_file_thread_position_colors_value = ($metaData && isset($metaData->third_file_thread_position_colors)) ? $metaData->third_file_thread_position_colors : '';

    echo '
        <tr>
            <td style="width: 7%;"><label for="third_file_thread_position_colors"><strong>Third File Thread Position Colors</strong></label></td>
            <td><input type="text" name="third_file_thread_position_colors" style="width: 400px;" value="' . $third_file_thread_position_colors_value . '"></td>
            <td>e.g. #3399FF, #FFFFFF</td>
        </tr>
    ';

    echo '</table>';

}

/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function wpdocs_save_meta_box($post_id)
{
    global $wpdb;

    $postData = get_post($post_id);

    // verify nonce
    if (isset($_POST['product_wpdocs_meta_box_nonce']) && !wp_verify_nonce($_POST['product_wpdocs_meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    } elseif (!$postData) {
        return $post_id;
    } elseif ($postData->post_type != 'product') {
        return $post_id;
    }

    if (isset($_POST) && isset($_POST['product_id'])) {

        // Check if domain exist
        $query = $wpdb->get_row(
            "
                    SELECT * FROM wenp_ens_product_meta
                    WHERE post_id='{$post_id}' LIMIT 1
        ");

        $data = [
            'post_id' => $post_id,
            'product_id' => filter_var($_POST['product_id']),
            'catalog_id' => filter_var($_POST['catalog_id']),
            'variant_id' => filter_var($_POST['variant_id']),

            'first_file_placement' => filter_var($_POST['first_file_placement']),
            'first_file_image_url' => filter_var($_POST['first_file_image_url']),
            'first_file_area_width' => filter_var($_POST['first_file_area_width']),
            'first_file_area_height' => filter_var($_POST['first_file_area_height']),
            'first_file_width' => filter_var($_POST['first_file_width']),
            'first_file_height' => filter_var($_POST['first_file_height']),
            'first_file_top' => filter_var($_POST['first_file_top']),
            'first_file_left' => filter_var($_POST['first_file_left']),
            'first_file_thread_position' => filter_var($_POST['first_file_thread_position']),
            'first_file_thread_position_colors' => filter_var($_POST['first_file_thread_position_colors']),

            'second_file_placement' => filter_var($_POST['second_file_placement']),
            'second_file_image_url' => filter_var($_POST['second_file_image_url']),
            'second_file_area_width' => filter_var($_POST['second_file_area_width']),
            'second_file_area_height' => filter_var($_POST['second_file_area_height']),
            'second_file_width' => filter_var($_POST['second_file_width']),
            'second_file_height' => filter_var($_POST['second_file_height']),
            'second_file_top' => filter_var($_POST['second_file_top']),
            'second_file_left' => filter_var($_POST['second_file_left']),
            'second_file_thread_position' => filter_var($_POST['second_file_thread_position']),
            'second_file_thread_position_colors' => filter_var($_POST['second_file_thread_position_colors']),

            'third_file_placement' => filter_var($_POST['third_file_placement']),
            'third_file_image_url' => filter_var($_POST['third_file_image_url']),
            'third_file_area_width' => filter_var($_POST['third_file_area_width']),
            'third_file_area_height' => filter_var($_POST['third_file_area_height']),
            'third_file_width' => filter_var($_POST['third_file_width']),
            'third_file_height' => filter_var($_POST['third_file_height']),
            'third_file_top' => filter_var($_POST['third_file_top']),
            'third_file_left' => filter_var($_POST['third_file_left']),
            'third_file_thread_position' => filter_var($_POST['third_file_thread_position']),
            'third_file_thread_position_colors' => filter_var($_POST['third_file_thread_position_colors']),
        ];

        $format = [
            '%d',
            '%s',
            '%s',
            '%s',

            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',

            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',

            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
        ];

        // Update meta data
        if ($query->id > 0) {

            $data_where = ['id' => $query->id];
            $where_format = ['%d'];

            $result = $wpdb->update('wenp_ens_product_meta', $data, $data_where, $format, $where_format);

        } // Insert meta data
        else {
            $result = $wpdb->insert('wenp_ens_product_meta', $data, $format);
        }

    }

}

add_action('save_post', 'wpdocs_save_meta_box');