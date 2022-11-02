<?php

$queryArgs = array(
    'post_type' => 'product',
    'posts_per_page' => 3,
    'post_status' => 'publish',
    'orderby' => 'rand',
    'order' => 'ASC'
);

if( isset($categoryProduct) && $categoryProduct > 0 ){

    $queryArgs['tax_query'] = [
        [
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $categoryProduct,
        ]
    ];
}

if( isset($postID) && $postID > 0 ){
    $queryArgs['post__not_in'] = [$postID];
}

$query = new WP_Query($queryArgs);
?>

<?php if ($query->have_posts()): ?>

    <!-- start:also-like-->
    <div class="home-product">

        <!-- start:container -->
        <div class="container">

            <!-- start:section-title -->
            <div class="section-title">
                <h2><?php _e('You May also Like', 'template'); ?></h2>
            </div>
            <!-- end:section-title -->


            <!-- start:product-list -->
            <div id="home-product-list" class="product-list">

                <div class="over"></div>

                <!-- start:row -->
                <div class="row g-1">

                    <?php $queryCounter = 1; ?>

                    <?php while ($query->have_posts()): ?>

                        <?php
                        // Prepare data
                        $query->the_post();

                        $postID = get_the_ID();
                        $thumbID = get_post_thumbnail_id();
                        $permalink = esc_url(get_permalink());

                        $image = wp_get_attachment_image_src($thumbID, 'theme-thumb-1');
                        $imageAlt = get_post_meta($thumbID, '_wp_attachment_image_alt', true);
                        ?>

                        <ul class="col-12 col-sm-6 col-lg-4">
                            <?php wc_get_template('content-product.php'); ?>
                        </ul>

                        <?php $queryCounter++; ?>

                    <?php endwhile; ?>

                </div>
                <!-- end:row -->

            </div>
            <!-- end:product-list -->

        </div>
        <!-- end:container -->

    </div>
    <!-- end:also-like-->

<?php endif; ?>

<?php wp_reset_postdata(); ?>