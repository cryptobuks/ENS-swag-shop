<?php
$queryArgs = array(
    'post_type' => 'post',
    'tax_query' => array(
        array(
            'taxonomy' => 'category',
            'field' => 'term_id',
            'terms' => 21,
        )
    ),
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 1,
    'post_status' => 'publish'
);

$query = new WP_Query($queryArgs);
?>

<?php if ($query->have_posts()): ?>

    <!-- start:home-intro-header -->
    <div class="home-intro-header">

        <div class="image-holder position-relative">

            <img src="<?php echo TEMPLATEDIR; ?>/images/body-bg-over.webp" alt="" class="img-responsive d-none d-md-block" loading="lazy">

            <img src="<?php echo TEMPLATEDIR; ?>/images/body-bg-over-mobile.webp" alt="" class="img-responsive d-block d-md-none">

            <!-- start:over -->
            <div class="over position-absolute">

                <!-- start:container -->
                <div class="container">

                    <?php $queryCounter = 1; ?>

                    <?php while ($query->have_posts()): ?>

                        <?php
                        // Prepare data
                        $query->the_post();

                        $postID = get_the_ID();
                        ?>

                        <!-- start:text -->
                        <div class="text">
                            <h2><?php the_title(); ?></h2>
                            <div><?php the_content(); ?></div>
                        </div>
                        <!-- end:text -->

                        <?php $queryCounter++; ?>

                    <?php endwhile; ?>


                </div>
                <!-- end:container -->

            </div>
            <!-- end:over -->

        </div>

    </div>
    <!-- end:home-intro-header -->

<?php endif; ?>

<?php wp_reset_postdata(); ?>