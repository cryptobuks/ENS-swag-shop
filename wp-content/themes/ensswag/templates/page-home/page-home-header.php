<?php
$queryArgs = array(
    'post_type' => 'post',
    'tax_query' => array(
        array(
            'taxonomy' => 'category',
            'field' => 'term_id',
            'terms' => 59,
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

    <!-- start:home-header -->
    <div class="home-header">

        <!-- start:container -->
        <div class="container">
            <div class="text">
                <div class="text-inside">
                    apparel for impact - apparel for impact -apparel for impact - apparel for impact -apparel for impact
                    - apparel for impact - apparel for impact - apparel for impact - apparel for impact - apparel for
                    impact - apparel for impact - apparel for impact - apparel for impact - apparel for impact - apparel
                    for impact
                </div>
            </div>

            <?php $queryCounter = 1; ?>

            <?php while ($query->have_posts()): ?>

                <?php
                // Prepare data
                $query->the_post();

                $postID     =   get_the_ID();
                $thumbID    =   get_post_thumbnail_id();
                $permalink  =   esc_url( get_permalink() );

                $image      =   wp_get_attachment_image_src($thumbID, 'theme-thumb-1');
                $imageAlt   =   get_post_meta($thumbID, '_wp_attachment_image_alt', true);
                ?>

                <?php if( isset($image[0]) ): ?>
                    <img src="<?php echo $image[0]; ?>" alt="<?php echo $imageAlt; ?>" class="d-block w-100">
                <?php endif; ?>

                <?php $queryCounter++; ?>

            <?php endwhile; ?>

            <div class="text">
                <div class="text-inside">
                    impact -apparel for impact - apparel for impact - apparel for impact - apparel for impact - apparel
                    for impact - apparel for impact - apparel for impact - apparel for impact - apparel for impact -
                    apparel for impact - apparel for impact - apparel for impact - apparel for impact - apparel for
                    impact - apparel for impact - apparel for impact
                </div>
            </div>
        </div>
        <!-- end:container -->

    </div>
    <!-- end:home-header -->

<?php endif; ?>

<?php wp_reset_postdata(); ?>