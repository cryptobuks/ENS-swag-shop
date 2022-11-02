<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 *
 * @package ENS Merch Shop
 * @subpackage ENS Merch Shop
 * @since ENS Merch Shop 1.0
 */

get_header(); ?>

    <!-- start:single-item -->
    <div class="single-item mt-5">

        <!-- start:container -->
        <div class="container">

            <!-- start:row -->
            <div class="row justify-content-center">

                <div class="col-12 col-md-8">

					<?php if ( have_posts() ) : ?>

						<?php $queryCounter = 1; ?>

						<?php while ( have_posts() ) : the_post(); ?>

							<?php

							// Prepare data
							$postID     =   get_the_ID();
							$thumbID    =   get_post_thumbnail_id();

							$permalink  =   esc_url( get_the_permalink() );

							$image      =   wp_get_attachment_image_src( $thumbID, 'full' );
							$imageAlt   =   get_post_meta($thumbID, '_wp_attachment_image_alt', true);

							?>

                            <article>

                                <h1 class="mb-2"><a href="<?php echo $permalink; ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>

                                <time class="d-block mb-3"><?php echo get_the_date('j/n/Y'); ?></time>

								<?php if( isset($image[0]) ): ?>
                                    <img src="<?php echo $image[0]; ?>" alt="<?php echo $imageAlt; ?>" class="img-responsive mb-3">
								<?php endif; ?>

                                <!-- start:text -->
                                <div class="text">
									<?php the_content(); ?>
                                </div>
                                <!-- end:text -->

                            </article>

							<?php $queryCounter++; ?>

						<?php endwhile; ?>

					<?php endif; ?>

                </div>

                <aside class="col-12 col-md-4">
					<?php get_sidebar(); ?>
                </aside>

            </div>
            <!-- end:row -->

        </div>
        <!-- end:container -->

    </div>
    <!-- end:single-item -->


<?php get_footer(); ?>