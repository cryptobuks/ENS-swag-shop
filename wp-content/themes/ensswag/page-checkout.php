<?php
/**
 * Template Name: Checkout
 */
?>
<?php get_header(); ?>

    <!-- start:content -->
    <div class="content content-cart content-checkout <?php echo((isset($_GET['key'])) ? 'content-thanks' : ''); ?>">

        <!-- start:container -->
        <div class="container">

            <!-- start:cart-inside -->
            <div class="cart-inside px-0 px-md-5">

                <h1><?php _e('Cart', 'template'); ?></h1>

                <?php if (have_posts()) : ?>

                    <?php while (have_posts()) : the_post(); ?>


                        <?php

                        //prepare data
                        $postID = get_the_ID();

                        ?>

                        <!-- start:content-inner -->
                        <div class="content-inner">

                            <?php include 'templates/page-checkout/page-checkout-products.php'; ?>

                            <div class="cart-subtotal">
                                <div class="cart-subtotal-title">
                                    <div class="subtotal-title">Subtotal: <div data-title="Subtotal"><?php wc_cart_totals_subtotal_html(); ?></div></div>
                                    <span class="items-number"><?php echo WC()->cart->cart_contents_count; ?> <?php _e('Item(s) in cart', 'template'); ?></span>
                                </div>
                            </div>

                            <?php the_content(); ?>
                        </div>
                        <!-- end:content-inner -->

                    <?php endwhile; ?>

                <?php endif; ?>

            </div>
            <!-- end:container -->

        </div>
        <!-- end:cart-inside -->

    </div>
    <!-- end:content -->

<?php get_footer(); ?>