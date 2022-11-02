<!-- start:content -->
<div class="content content-single-main">

    <!-- start:container -->
    <div class="container">

        <!-- start:single-main-inside -->
        <div class="single-main-inside">

            <?php if (have_posts()) : ?>

                <?php while (have_posts()) : the_post(); ?>

                    <?php

                    //prepare data
                    $postID = get_the_ID();

                    ?>

                    <h2><?php the_title(); ?></h2>

                    <!-- start:text -->
                    <div class="text">
                        <?php the_content(); ?>

                    </div>
                    <!-- end:text -->

                <?php endwhile; ?>

            <?php endif; ?>

        </div>
        <!-- end:single-main-inside -->

    </div>
    <!-- end:container -->

</div>
<!-- end:content -->