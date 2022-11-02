<!-- start:home-newsletter -->
<div class="home-newsletter">

    <!-- start:container -->
    <div class="container">

        <!-- start:home-newsletter-inside -->
        <div class="home-newsletter-inside">

            <!-- start:row -->
            <div class="row justify-content-center align-items-center">

                <div class="col-12 col-md-6">
                    <h2>
                        be the first<br>
                        to have the<br>
                        <span>best merch</span>
                    </h2>
                    <div class="text">
                        Find out before others about our future<br>releases and limited edition products
                    </div>
                    <div class="form-holder">
                        <form id="newsletter-form" action="">
                            <input type="hidden" id="newsletter-form-nonce" name="newsletter-form-nonce" value="<?php echo wp_create_nonce('newsletter_form_nonce'); ?>" autocomplete="off">
                            <input type="hidden" name="newfrch-check" id="newfrch-check" value="0" autocomplete="off">
                            <div class="row">
                                <div class="col-12 col-xl-auto">
                                    <div class="form-input-over">
                                        <input type="email" name="email" id="email" class="form-input" placeholder="<?php _e('Email address', 'template'); ?>">
                                    </div>
                                </div>
                                <div class="col-12 col-xl-auto text-center text-md-start">
                                    <button type="submit" onclick="return newslettelSubscribe();"><?php _e('Get Notified', 'template'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <img src="<?php echo TEMPLATEDIR; ?>/images/newsletter-image.webp" alt="" class="img-responsive" loading="lazy">
                </div>

            </div>
            <!-- end:row -->

        </div>
        <!-- end:home-newsletter-inside -->
        
    </div>
    <!-- end:container -->
    
</div>
<!-- end:home-newsletter -->