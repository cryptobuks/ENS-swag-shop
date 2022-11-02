<?php
/**
 * Template Name: Contact
 */
?>
<?php get_header(); ?>

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

                            <form id="contact-form" role="form" action="" class="form">

                                <input type="hidden" id="contact-nonce" name="contact-nonce" value="<?php echo wp_create_nonce('contact_form_check'); ?>" autocomplete="off">

                                <!-- start:row -->
                                <div class="row">

                                    <div class="col-12 col-md-12 mb-2">
                                        <div class="form-group">
                                            <input type="text" id="contact-name" name="contact-name" class="form-control" placeholder="<?php _e('Your name', 'template'); ?>">
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-12 mb-2">
                                        <div class="form-group">
                                            <input type="email" id="contact-email" name="contact-email" class="form-control" placeholder="<?php _e('Your email address', 'template'); ?>">
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-12 mb-2">
                                        <div class="form-group">
                                            <textarea type="text" id="contact-message" name="contact-message" class="form-control form-date" placeholder="<?php _e('Message', 'template'); ?>"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                                        <div class="alert alert-success contact-alert-success">
                                            <strong><?php _e('Your message has been sent.', 'template'); ?></strong>
                                        </div>

                                        <div class="alert alert-warning contact-alert-warning">
                                            <strong><?php _e('All fields are mandatory.', 'template'); ?></strong>
                                        </div>

                                    </div>

                                    <div class="col-12 text-center">
                                        <div class="form-group submit-over">
                                            <button type="submit" class="btn btn-submit text-uppercase" onclick="return sendContactMessage();"><?php _e('Send a message', 'template'); ?></button>
                                        </div>
                                    </div>

                                </div>
                                <!-- end:row -->

                            </form>

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

<?php get_footer(); ?>