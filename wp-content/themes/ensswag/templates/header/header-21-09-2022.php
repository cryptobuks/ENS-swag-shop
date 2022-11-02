<!-- start:header -->
<header class="header">

    <!-- start:container -->
    <div class="container">

        <!-- start:row -->
        <div class="row justify-content-between align-items-center">

            <div class="col-auto">
                <a href="/" class="logo" title="<?php bloginfo('name'); ?>">
                    <?php bloginfo('name'); ?>
                </a>
            </div>

            <div class="col-auto">

                <div class="header-right">
                    <div class="row align-items-center g-0">
                        <div class="col-auto search-column">
                            <div class="row align-items-center g-0">
                                <div class="col-auto">
                                    <?php echo get_search_form(); ?>
                                </div>
                                <div class="delimiter"></div>
                            </div>
                        </div>
                        <div class="col-auto cart-column">
                            <div class="row align-items-center g-0">
                                <a href="<?php echo wc_get_checkout_url(); ?>"
                                   class="col-auto cart-icon"><?php _e('Cart', 'template'); ?><span
                                            class="btn-cart-top-count"></span></a>
                                <div class="delimiter"></div>
                            </div>
                        </div>
                        <div class="col-auto column-shipping">
                            <div class="row align-items-center g-0">
                                <a href="<?php echo get_the_permalink(19); ?>"
                                   class="col-auto shipping"><?php _e('Shipping', 'template'); ?></a>
                                <div class="delimiter"></div>
                            </div>
                        </div>
                        <div class="col-auto connect-over">
                            <div id="connectHeader"></div>
                            <div class="delimiter"></div>
                        </div>
                        <div class="col-auto nav-button-over">
                            <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#navbarsDefault" aria-controls="navbarNavAltMarkup"
                                    aria-expanded="false" aria-label="Toggle navigation"></button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-12 nav-column">
                <nav class="navbar">

                    <div id="navbarsDefault" class="navbar-collapse collapse">

                        <ul class="navbar-nav mr-auto">
                            <li><a href="<?php echo wc_get_cart_url(); ?>"><?php _e('Cart', 'template'); ?></a></li>
                            <li><a href="<?php echo get_permalink(19); ?>"><?php _e('Shipping', 'template'); ?></a></li>
                            <li><a href="javascript:void(0);"
                                   onclick="jQuery('#connectHeader button').trigger('click');"
                                   class="nav-disconnect"><?php _e('Disconnect', 'template'); ?></a></li>
                        </ul>

                    </div>

                </nav>
            </div>

        </div>
        <!-- end:row -->

    </div>
    <!-- end:container -->

</header>
<!-- end:header -->