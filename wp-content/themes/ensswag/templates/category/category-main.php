<?php include __DIR__ . "/../../templates/elements/intro-title.php"; /* Load header title */ ?>

<!-- start:content -->
<div class="content category-list mt-5 mb-5">

    <!-- start:content-inside -->
    <div class="content-inside">

        <!-- start:container -->
        <div class="container">

            <!-- start:category-list-inside -->
            <div class="category-list-inside">

                <!-- start:row -->
                <div class="row justify-content-center">

                    <div class="col-12 col-lg-9">

                        <?php if ( have_posts() ) : ?>

                        <?php $queryCounter = 1; ?>

                        <?php while ( have_posts() ) : the_post(); ?>

                            <?php

                            //prepare data
                            $postID     =   get_the_ID();
                            $thumbID    =   get_post_thumbnail_id();

                            $permalink  =   esc_url( get_the_permalink() );

                            $image      =   wp_get_attachment_image_src( $thumbID, 'medium' );
                            $imageAlt   =   get_post_meta($thumbID, '_wp_attachment_image_alt', true);

                            ?>

                            <div class="category-item mb-4">

                                <div class="row">

                                    <div class="col-12 col-md-4">

                                        <?php if( isset($image[0]) ): ?>

                                            <a href="<?php echo $permalink; ?>" class="image-holder">
                                                <img src="<?php echo $image[0]; ?>" alt="<?php echo $imageAlt; ?>" class="img-responsive">
                                            </a>

                                        <?php endif; ?>

                                    </div>

                                    <div class="col-12 col-md-8">
                                        <div class="category-item-right">
                                            <h2><a href="<?php echo $permalink; ?>"><?php the_title(); ?></a></h2>
                                            <div class="text">
                                                <?php the_excerpt(); ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <?php $queryCounter++; ?>

                        <?php endwhile; ?>

                    <?php endif; ?>

                    </div>

                    <div class="col-12 col-lg-9">

                        <!-- start:post-pagination -->
                        <div class="post-pagination text-center pt-4">

                            <?php

                            the_posts_pagination(
                                array(
                                    'mid_size'  => 2,
                                    'prev_text' => __( '<', 'template' ),
                                    'next_text' => __( '>', 'template' ),
                                )
                            );

                            ?>

                        </div>
                        <!-- end:post-pagination -->

                    </div>

                </div>
                <!-- end:row -->

            </div>
            <!-- end:category-list-inside -->

        </div>
        <!-- end:container -->

    </div>
    <!-- end:content-inside -->

</div>
<!-- end:content -->