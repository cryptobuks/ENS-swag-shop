<?php get_header(); ?>

<?php if (have_posts()) : ?>

    <?php $queryCounter = 0; ?>

    <?php while (have_posts()) : the_post(); ?>

        <?php

        // Prepare data

        $postID = get_the_ID();
        $permalink = esc_url(get_permalink());

        $product = wc_get_product($postID);

        $singleProduct = true;

        ?>

        <!-- start:home-products -->
        <div class="home-products pt-4">

            <!-- start:container -->
            <div class="container">

                <!-- start:product-list -->
                <div id="home-products-list" class="product-list">

                    <form id="addProductForm<?php echo $postID; ?>" class="add-mockup-form" role="form"
                          action="" class="form">

                        <input type="hidden" id="add-mockup-nonce-<?php echo $postID; ?>"
                               name="add-mockup-nonce"
                               value="<?php echo wp_create_nonce('add_mockup_form_check_' . $postID); ?>"
                               autocomplete="off">
                        <input type="hidden" name="product_id" id="product_id-<?php echo $postID; ?>"
                               value="<?php echo $postID; ?>" autocomplete="off">

                        <!-- start:row -->
                        <div class="row justify-content-between g-2">

                            <div class="col-auto home-products-gallery">
                                <?php include 'templates/page-home/page-home-product-image.php'; ?>
                            </div>

                            <div class="col-auto home-products-desc">
                                <?php include 'templates/page-home/page-home-product-desc.php'; ?>
                            </div>

                        </div>
                        <!-- end:row -->

                    </form>


                </div>
                <!-- end:product-list -->

            </div>
            <!-- end:container -->

        </div>
        <!-- end:home-products -->

        <?php $queryCounter++; ?>

    <?php endwhile; ?>

<?php endif; ?>

<?php include 'templates/page-home/page-home-newsletter.php'; ?>

<?php get_footer(); ?>