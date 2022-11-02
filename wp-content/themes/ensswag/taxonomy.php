<?php get_header(); ?>

    <!-- start:content -->
    <div class="content mb-5">

        <!-- start:container -->
        <div class="container">

            <!-- start:row -->
            <div class="row">

                <!-- start:content-right -->
                <div class="col-12 content-right">

                    <!-- start:content-inner -->
                    <div class="content-inner">

                        <!-- start:content-inner-text -->
                        <div class="content-inner-text">

                            <?php if ( have_posts() ) : ?>

                                <?php while ( have_posts() ) : the_post(); ?>

                                    <?php

                                    //prepare data
                                    $postID     =   get_the_ID();

                                    ?>

                                    <?php the_title(); ?>

                                    <?php the_content(); ?>

                                <?php endwhile; ?>

                            <?php endif; ?>

                        </div>
                        <!-- end:content-inner-text -->

                    </div>
                    <!-- end:content-inner -->

                </div>
                <!-- end:col-12 -->

            </div>
            <!-- end:row -->

        </div>
        <!-- end:container -->

    </div>
    <!-- end:content -->

<?php get_footer(); ?>