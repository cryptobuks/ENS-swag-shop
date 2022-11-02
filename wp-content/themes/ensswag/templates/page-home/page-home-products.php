<?php

$queryArgs = array(
    'post_type' => 'product',
    'posts_per_page' => 6,
    'post_status' => 'publish',
    'tax_query' => array(
        array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => 22,
        )
    ),
);

$query = new WP_Query($queryArgs);
?>

<?php if ($query->have_posts()): ?>

    <!-- start:home-products -->
    <div class="home-products">

        <!-- start:container -->
        <div class="container">

            <!-- start:product-list -->
            <div id="home-products-list" class="product-list">


                <?php $queryCounter = 0; ?>

                <?php while ($query->have_posts()): ?>

                    <?php
                    // Prepare data
                    $query->the_post();

                    $postID = get_the_ID();
                    $permalink = esc_url(get_permalink());

                    $product = wc_get_product($postID);
                    ?>

                    <form id="addProductForm<?php echo $postID; ?>" class="add-mockup-form form" role="form" action="">
                        <input type="hidden" name="product_id" id="product_id-<?php echo $postID; ?>" value="<?php echo $postID; ?>" autocomplete="off">
                        <!-- start:row -->
                        <div class="row justify-content-between g-2">

                            <div class="col-auto home-products-gallery">
                                <?php include 'page-home-product-image.php'; ?>
                            </div>

                            <div class="col-auto home-products-desc">
                                <?php include 'page-home-product-desc.php'; ?>
                            </div>

                        </div>
                        <!-- end:row -->

                    </form>

                    <?php $queryCounter++; ?>

                <?php endwhile; ?>

            </div>
            <!-- end:product-list -->

        </div>
        <!-- end:container -->

    </div>
    <!-- end:home-products -->

<?php endif; ?>

<?php wp_reset_postdata(); ?>