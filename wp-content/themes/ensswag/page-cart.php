<?php
/**
 * Template Name: Cart
 */
?>
<?php get_header(); ?>

    <!-- start:content -->
    <div class="content content-cart pt-4">

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