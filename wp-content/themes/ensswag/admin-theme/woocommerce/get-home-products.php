<?php


/**
 * Get home products
 *
 * Send mail to client - AJAX function - contact form
 */
function get_home_products()
{

    $return_data = array();
    $return_data['status'] = 0;

    // Get post data
    $page = filter_var($_POST['str']);

    if ($page > 0) {

        $queryArgs = array(
            'post_type' => 'product',
            'posts_per_page' => 6,
            'paged' => $page,
            'post_status' => 'publish',
        );

        $query = new WP_Query($queryArgs);

        if ($query->have_posts()) {

            $return_data['content'] = '';

            while ($query->have_posts()) {
                // Prepare data
                $query->the_post();

                ob_start();

                echo '<ul class="col-12 col-sm-6 col-lg-4">';
                wc_get_template('content-product.php');
                echo '</ul>';

                $return_data['content'] .= ob_get_contents();
                ob_end_clean();

            }

            global $wp;

            $numberOfPages = $query->max_num_pages;

            $end_size = 1;
            $mid_size = 3;
            $queryString = '';
            $currentUrl = home_url($wp->request);

            $return_data['pagination'] = getPagination($currentUrl, $queryString, $numberOfPages, $page, $mid_size, $end_size, false, false);

            $return_data['status'] = 1;
        }else{
            $return_data['status'] = 2;
        }

    } else {
        $return_data['status'] = 2;
    }

    echo json_encode($return_data);

}

add_action('wp_ajax_nopriv_get_home_products', 'get_home_products');
add_action('wp_ajax_get_home_products', 'get_home_products');