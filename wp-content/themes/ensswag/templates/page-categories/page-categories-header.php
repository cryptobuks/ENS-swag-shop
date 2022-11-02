<?php
$postID     =   get_the_ID();
$thumbID    =   get_post_thumbnail_id($postID);
$image      =   wp_get_attachment_image_src($thumbID, 'theme-thumb-1');
$imageAlt   =   get_post_meta($thumbID, '_wp_attachment_image_alt', true);
?>

<?php if( isset($image[0]) ): ?>

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

            <?php if( isset($image[0]) ): ?>
                <img src="<?php echo $image[0]; ?>" alt="<?php echo $imageAlt; ?>" class="d-block w-100">
            <?php endif; ?>

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